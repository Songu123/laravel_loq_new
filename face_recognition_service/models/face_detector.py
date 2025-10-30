"""
Face Detector using MediaPipe Face Mesh
Detects faces and extracts 468 3D landmarks
"""

import cv2
import mediapipe as mp
import numpy as np
from typing import List, Dict, Optional
from loguru import logger
import os


class FaceDetector:
    """Face detection using MediaPipe Face Mesh"""
    
    def __init__(self):
        """Initialize MediaPipe Face Mesh"""
        self.mp_face_mesh = mp.solutions.face_mesh
        self.face_mesh = self.mp_face_mesh.FaceMesh(
            static_image_mode=True,
            max_num_faces=int(os.getenv("MEDIAPIPE_MAX_FACES", 1)),
            refine_landmarks=True,
            min_detection_confidence=0.5,
            min_tracking_confidence=0.5
        )
        
        self.min_face_size = int(os.getenv("MIN_FACE_SIZE", 80))
        self.max_face_size = int(os.getenv("MAX_FACE_SIZE", 800))
        
        logger.info("✅ MediaPipe Face Mesh initialized")
    
    def detect(self, image: np.ndarray) -> List[Dict]:
        """
        Detect faces in image and return bounding boxes + landmarks
        
        Args:
            image: Input image (BGR format from OpenCV)
        
        Returns:
            List of face dictionaries with bbox, landmarks, confidence
        """
        # Convert BGR to RGB
        rgb_image = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
        
        # Process image
        results = self.face_mesh.process(rgb_image)
        
        faces = []
        
        if results.multi_face_landmarks:
            for face_landmarks in results.multi_face_landmarks:
                # Extract landmarks
                landmarks = []
                for landmark in face_landmarks.landmark:
                    landmarks.append({
                        'x': landmark.x,
                        'y': landmark.y,
                        'z': landmark.z  # 3D depth information
                    })
                
                # Calculate bounding box
                bbox = self._calculate_bbox(landmarks, image.shape)
                
                # Validate face size
                face_width = bbox['x2'] - bbox['x1']
                face_height = bbox['y2'] - bbox['y1']
                
                if face_width < self.min_face_size or face_height < self.min_face_size:
                    logger.warning(f"Face too small: {face_width}x{face_height}")
                    continue
                
                if face_width > self.max_face_size or face_height > self.max_face_size:
                    logger.warning(f"Face too large: {face_width}x{face_height}")
                    continue
                
                faces.append({
                    'bbox': bbox,
                    'landmarks': landmarks,
                    'num_landmarks': len(landmarks),
                    'confidence': 0.9  # MediaPipe doesn't provide confidence score
                })
        
        logger.info(f"Detected {len(faces)} face(s)")
        return faces
    
    def _calculate_bbox(self, landmarks: List[Dict], image_shape: tuple) -> Dict[str, int]:
        """Calculate bounding box from landmarks"""
        height, width = image_shape[:2]
        
        # Get x, y coordinates
        x_coords = [lm['x'] * width for lm in landmarks]
        y_coords = [lm['y'] * height for lm in landmarks]
        
        # Calculate bbox with padding
        padding = 20
        x1 = max(0, int(min(x_coords)) - padding)
        y1 = max(0, int(min(y_coords)) - padding)
        x2 = min(width, int(max(x_coords)) + padding)
        y2 = min(height, int(max(y_coords)) + padding)
        
        return {
            'x1': x1,
            'y1': y1,
            'x2': x2,
            'y2': y2,
            'width': x2 - x1,
            'height': y2 - y1
        }
    
    def crop_face(self, image: np.ndarray, bbox: Dict[str, int]) -> np.ndarray:
        """Crop face from image using bounding box"""
        return image[bbox['y1']:bbox['y2'], bbox['x1']:bbox['x2']]
    
    def get_face_landmarks_array(self, landmarks: List[Dict]) -> np.ndarray:
        """Convert landmarks to numpy array (468 x 3)"""
        return np.array([[lm['x'], lm['y'], lm['z']] for lm in landmarks])
    
    def __del__(self):
        """Cleanup"""
        if hasattr(self, 'face_mesh'):
            self.face_mesh.close()
