"""
Face Recognition 3D Service - Main Application
FastAPI server for face enrollment, verification, and authentication
"""

from fastapi import FastAPI, UploadFile, File, HTTPException, Request
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from pydantic import BaseModel
from typing import List, Optional
import uvicorn
import os
from dotenv import load_dotenv
from loguru import logger
import sys

# Import custom modules
from models.face_detector import FaceDetector
from models.face_embedder import FaceEmbedder
from models.liveness_detector import LivenessDetector
from utils.image_processor import ImageProcessor
from utils.database import DatabaseManager
from utils.cache import CacheManager

# Load environment variables
load_dotenv()

# Configure logging
logger.remove()
logger.add(
    sys.stderr,
    level=os.getenv("LOG_LEVEL", "INFO"),
    format="<green>{time:YYYY-MM-DD HH:mm:ss}</green> | <level>{level: <8}</level> | <cyan>{name}</cyan>:<cyan>{function}</cyan> - <level>{message}</level>"
)
logger.add(
    "logs/face_recognition_{time:YYYY-MM-DD}.log",
    rotation="00:00",
    retention="30 days",
    level="INFO"
)

# Initialize FastAPI
app = FastAPI(
    title="Face Recognition 3D Service",
    description="API for 3D face detection, enrollment, verification, and authentication",
    version="1.0.0"
)

# CORS Configuration
allowed_origins = os.getenv("ALLOWED_ORIGINS", "http://localhost:8000").split(",")
app.add_middleware(
    CORSMiddleware,
    allow_origins=allowed_origins,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Initialize components
face_detector = None
face_embedder = None
liveness_detector = None
image_processor = None
db_manager = None
cache_manager = None

# Pydantic Models
class EnrollRequest(BaseModel):
    user_id: int
    images: List[str]  # Base64 encoded images

class VerifyRequest(BaseModel):
    user_id: int
    image: str  # Base64 encoded image

class AuthenticateRequest(BaseModel):
    image: str  # Base64 encoded image

class EnrollResponse(BaseModel):
    success: bool
    face_id: Optional[str] = None
    embeddings_count: int = 0
    message: str = ""

class VerifyResponse(BaseModel):
    success: bool
    matched: bool = False
    confidence: float = 0.0
    is_live: bool = False
    user_id: Optional[int] = None
    message: str = ""

class AuthenticateResponse(BaseModel):
    success: bool
    user_id: Optional[int] = None
    name: Optional[str] = None
    confidence: float = 0.0
    is_live: bool = False
    message: str = ""


@app.on_event("startup")
async def startup_event():
    """Initialize all components on startup"""
    global face_detector, face_embedder, liveness_detector, image_processor, db_manager, cache_manager
    
    logger.info("🚀 Starting Face Recognition 3D Service...")
    
    try:
        # Initialize components
        logger.info("Loading Face Detector (MediaPipe)...")
        face_detector = FaceDetector()
        
        logger.info("Loading Face Embedder (FaceNet)...")
        face_embedder = FaceEmbedder()
        
        logger.info("Loading Liveness Detector...")
        liveness_detector = LivenessDetector()
        
        logger.info("Initializing Image Processor...")
        image_processor = ImageProcessor()
        
        logger.info("Connecting to Database...")
        db_manager = DatabaseManager()
        
        logger.info("Connecting to Redis Cache...")
        cache_manager = CacheManager()
        
        logger.success("✅ All components loaded successfully!")
        
    except Exception as e:
        logger.error(f"❌ Failed to initialize components: {str(e)}")
        raise


@app.on_event("shutdown")
async def shutdown_event():
    """Cleanup on shutdown"""
    logger.info("🛑 Shutting down Face Recognition Service...")
    
    if db_manager:
        db_manager.close()
    if cache_manager:
        cache_manager.close()
    
    logger.info("👋 Goodbye!")


@app.get("/")
async def root():
    """Root endpoint"""
    return {
        "service": "Face Recognition 3D Service",
        "version": "1.0.0",
        "status": "running",
        "endpoints": [
            "/health",
            "/api/face/enroll",
            "/api/face/verify",
            "/api/face/authenticate"
        ]
    }


@app.get("/health")
async def health_check():
    """Health check endpoint"""
    return {
        "status": "healthy",
        "components": {
            "face_detector": face_detector is not None,
            "face_embedder": face_embedder is not None,
            "liveness_detector": liveness_detector is not None,
            "database": db_manager is not None and db_manager.is_connected(),
            "cache": cache_manager is not None and cache_manager.is_connected()
        }
    }


@app.post("/api/face/enroll", response_model=EnrollResponse)
async def enroll_face(request: EnrollRequest):
    """
    Enroll a user's face for future authentication
    
    Args:
        user_id: User ID from Laravel
        images: List of base64 encoded images (3-10 images recommended)
    
    Returns:
        success: Whether enrollment was successful
        face_id: UUID of the enrolled face
        embeddings_count: Number of embeddings created
    """
    try:
        logger.info(f"📸 Enrollment request for user_id={request.user_id}")
        
        # Validate number of images
        min_images = int(os.getenv("MIN_ENROLLMENT_IMAGES", 3))
        max_images = int(os.getenv("MAX_ENROLLMENT_IMAGES", 10))
        
        if len(request.images) < min_images:
            raise HTTPException(
                status_code=400,
                detail=f"Please provide at least {min_images} images"
            )
        
        if len(request.images) > max_images:
            raise HTTPException(
                status_code=400,
                detail=f"Maximum {max_images} images allowed"
            )
        
        # Process each image
        embeddings = []
        valid_images = 0
        
        for idx, base64_image in enumerate(request.images):
            try:
                # Decode image
                image = image_processor.decode_base64(base64_image)
                
                # Detect face
                faces = face_detector.detect(image)
                
                if len(faces) == 0:
                    logger.warning(f"No face detected in image {idx+1}")
                    continue
                
                if len(faces) > 1:
                    logger.warning(f"Multiple faces detected in image {idx+1}, using first face")
                
                face = faces[0]
                
                # Check liveness (optional during enrollment)
                if os.getenv("ENABLE_ANTI_SPOOFING", "True") == "True":
                    is_live = liveness_detector.check_liveness(image, face)
                    if not is_live:
                        logger.warning(f"Liveness check failed for image {idx+1}")
                        continue
                
                # Extract face embedding
                embedding = face_embedder.get_embedding(image, face)
                embeddings.append(embedding.tolist())
                valid_images += 1
                
                logger.info(f"✅ Processed image {idx+1}/{len(request.images)}")
                
            except Exception as e:
                logger.error(f"Error processing image {idx+1}: {str(e)}")
                continue
        
        # Check if we have enough valid embeddings
        if valid_images < min_images:
            raise HTTPException(
                status_code=400,
                detail=f"Only {valid_images} valid images processed. Need at least {min_images}."
            )
        
        # Save to database
        face_id = db_manager.save_face_embeddings(request.user_id, embeddings)
        
        # Cache the embeddings
        cache_manager.cache_embeddings(request.user_id, embeddings)
        
        logger.success(f"✅ Enrollment successful for user_id={request.user_id}, face_id={face_id}")
        
        return EnrollResponse(
            success=True,
            face_id=face_id,
            embeddings_count=len(embeddings),
            message=f"Successfully enrolled {len(embeddings)} face embeddings"
        )
        
    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"❌ Enrollment failed: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/api/face/verify", response_model=VerifyResponse)
async def verify_face(request: VerifyRequest):
    """
    Verify if the provided face matches the enrolled user
    
    Args:
        user_id: User ID to verify against
        image: Base64 encoded image
    
    Returns:
        success: Whether verification was successful
        matched: Whether the face matched
        confidence: Confidence score (0-1)
        is_live: Whether liveness check passed
    """
    try:
        logger.info(f"🔍 Verification request for user_id={request.user_id}")
        
        # Decode image
        image = image_processor.decode_base64(request.image)
        
        # Detect face
        faces = face_detector.detect(image)
        
        if len(faces) == 0:
            return VerifyResponse(
                success=True,
                matched=False,
                message="No face detected in image"
            )
        
        face = faces[0]
        
        # Check liveness
        is_live = True
        if os.getenv("ENABLE_ANTI_SPOOFING", "True") == "True":
            is_live = liveness_detector.check_liveness(image, face)
            if not is_live:
                logger.warning("Liveness check failed")
                return VerifyResponse(
                    success=True,
                    matched=False,
                    is_live=False,
                    message="Liveness check failed. Please use a live camera."
                )
        
        # Extract embedding
        embedding = face_embedder.get_embedding(image, face)
        
        # Get enrolled embeddings from cache or database
        enrolled_embeddings = cache_manager.get_embeddings(request.user_id)
        
        if not enrolled_embeddings:
            enrolled_embeddings = db_manager.get_face_embeddings(request.user_id)
            
            if not enrolled_embeddings:
                return VerifyResponse(
                    success=True,
                    matched=False,
                    message="User has not enrolled face data"
                )
            
            # Cache for next time
            cache_manager.cache_embeddings(request.user_id, enrolled_embeddings)
        
        # Compare embeddings
        confidence = face_embedder.compare_embeddings(embedding, enrolled_embeddings)
        
        threshold = float(os.getenv("CONFIDENCE_THRESHOLD", 0.6))
        matched = confidence >= threshold
        
        # Log attempt
        db_manager.log_verification_attempt(
            user_id=request.user_id,
            success=matched,
            confidence=confidence,
            is_live=is_live
        )
        
        logger.info(f"{'✅' if matched else '❌'} Verification result: matched={matched}, confidence={confidence:.3f}")
        
        return VerifyResponse(
            success=True,
            matched=matched,
            confidence=round(confidence, 3),
            is_live=is_live,
            user_id=request.user_id if matched else None,
            message="Face matched" if matched else "Face did not match"
        )
        
    except Exception as e:
        logger.error(f"❌ Verification failed: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/api/face/authenticate", response_model=AuthenticateResponse)
async def authenticate_face(request: AuthenticateRequest):
    """
    Authenticate a user by comparing against all enrolled faces
    
    Args:
        image: Base64 encoded image
    
    Returns:
        success: Whether authentication was successful
        user_id: ID of matched user (if any)
        name: Name of matched user
        confidence: Confidence score
    """
    try:
        logger.info("🔐 Authentication request")
        
        # Decode image
        image = image_processor.decode_base64(request.image)
        
        # Detect face
        faces = face_detector.detect(image)
        
        if len(faces) == 0:
            return AuthenticateResponse(
                success=True,
                message="No face detected in image"
            )
        
        face = faces[0]
        
        # Check liveness
        is_live = True
        if os.getenv("ENABLE_ANTI_SPOOFING", "True") == "True":
            is_live = liveness_detector.check_liveness(image, face)
            if not is_live:
                logger.warning("Liveness check failed")
                return AuthenticateResponse(
                    success=True,
                    is_live=False,
                    message="Liveness check failed"
                )
        
        # Extract embedding
        embedding = face_embedder.get_embedding(image, face)
        
        # Get all enrolled users (admins only)
        enrolled_users = db_manager.get_all_enrolled_admins()
        
        if not enrolled_users:
            return AuthenticateResponse(
                success=True,
                message="No enrolled users in system"
            )
        
        # Find best match
        best_match = None
        best_confidence = 0.0
        threshold = float(os.getenv("CONFIDENCE_THRESHOLD", 0.6))
        
        for user in enrolled_users:
            user_id = user['id']
            user_embeddings = user['embeddings']
            
            confidence = face_embedder.compare_embeddings(embedding, user_embeddings)
            
            if confidence > best_confidence and confidence >= threshold:
                best_confidence = confidence
                best_match = user
        
        if best_match:
            # Log successful authentication
            db_manager.log_verification_attempt(
                user_id=best_match['id'],
                success=True,
                confidence=best_confidence,
                is_live=is_live
            )
            
            logger.success(f"✅ Authentication successful: user_id={best_match['id']}, confidence={best_confidence:.3f}")
            
            return AuthenticateResponse(
                success=True,
                user_id=best_match['id'],
                name=best_match['name'],
                confidence=round(best_confidence, 3),
                is_live=is_live,
                message="Authentication successful"
            )
        else:
            logger.warning("❌ No matching face found")
            
            return AuthenticateResponse(
                success=True,
                is_live=is_live,
                message="No matching face found"
            )
        
    except Exception as e:
        logger.error(f"❌ Authentication failed: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))


@app.exception_handler(Exception)
async def global_exception_handler(request: Request, exc: Exception):
    """Global exception handler"""
    logger.error(f"Unhandled exception: {str(exc)}")
    return JSONResponse(
        status_code=500,
        content={"detail": "Internal server error", "error": str(exc)}
    )


if __name__ == "__main__":
    port = int(os.getenv("PORT", 8001))
    debug = os.getenv("DEBUG", "False") == "True"
    
    logger.info(f"🚀 Starting server on port {port}")
    
    uvicorn.run(
        "app:app",
        host="0.0.0.0",
        port=port,
        reload=debug,
        log_level="info"
    )
