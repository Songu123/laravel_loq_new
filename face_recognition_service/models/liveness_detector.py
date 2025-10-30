"""
Liveness Detector - Anti-Spoofing
Detects if the face is from a live person or a photo/video
"""

import cv2
import numpy as np
from typing import Dict
from loguru import logger
import os


class LivenessDetector:
    """
    Detect if face is from live person using multiple techniques:
    1. 3D face mesh depth analysis
    2. Texture analysis
    3. Eye blink detection
    4. Motion detection (for video streams)
    """
    
    def __init__(self):
        """Initialize liveness detector"""
        self.threshold = float(os.getenv("LIVENESS_THRESHOLD", 0.5))
        
        # For texture analysis
        self.lbp_radius = 1
        self.lbp_points = 8
        
        logger.info("✅ Liveness Detector initialized")
    
    def check_liveness(self, image: np.ndarray, face: Dict) -> bool:
        """
        Check if face is from a live person
        
        Args:
            image: Input image
            face: Face dict with bbox and landmarks
        
        Returns:
            True if live, False if spoofed
        """
        scores = []
        
        # 1. Check 3D depth variation
        depth_score = self._check_depth_variation(face)
        scores.append(depth_score)
        logger.debug(f"Depth score: {depth_score:.3f}")
        
        # 2. Texture analysis
        texture_score = self._analyze_texture(image, face)
        scores.append(texture_score)
        logger.debug(f"Texture score: {texture_score:.3f}")
        
        # 3. Face size reasonableness
        size_score = self._check_face_size(face)
        scores.append(size_score)
        logger.debug(f"Size score: {size_score:.3f}")
        
        # Calculate final score
        final_score = np.mean(scores)
        is_live = final_score > self.threshold
        
        logger.info(f"Liveness check: {'✅ LIVE' if is_live else '❌ SPOOFED'} (score: {final_score:.3f})")
        
        return is_live
    
    def _check_depth_variation(self, face: Dict) -> float:
        """
        Check 3D depth variation in landmarks
        Real faces have significant depth variation, photos are flat
        """
        if 'landmarks' not in face:
            return 0.5  # Neutral score
        
        landmarks = face['landmarks']
        
        # Extract z-coordinates (depth)
        z_coords = [lm['z'] for lm in landmarks]
        
        # Calculate variation
        z_std = np.std(z_coords)
        z_range = max(z_coords) - min(z_coords)
        
        # Real faces typically have z_range > 0.05 and z_std > 0.01
        # Photos have very small values close to 0
        
        if z_range > 0.05 and z_std > 0.01:
            score = min(1.0, z_range * 10)  # Scale to 0-1
        else:
            score = z_range * 5  # Lower score for flat faces
        
        return score
    
    def _analyze_texture(self, image: np.ndarray, face: Dict) -> float:
        """
        Analyze texture using Local Binary Patterns (LBP)
        Real faces have richer texture than printed photos
        """
        bbox = face['bbox']
        
        # Crop face
        face_crop = image[bbox['y1']:bbox['y2'], bbox['x1']:bbox['x2']]
        
        if face_crop.size == 0:
            return 0.5
        
        # Convert to grayscale
        gray = cv2.cvtColor(face_crop, cv2.COLOR_BGR2GRAY)
        
        # Calculate LBP
        lbp = self._calculate_lbp(gray)
        
        # Calculate histogram
        hist, _ = np.histogram(lbp.ravel(), bins=256, range=(0, 256))
        hist = hist.astype("float")
        hist /= (hist.sum() + 1e-7)
        
        # Calculate entropy (measure of texture richness)
        entropy = -np.sum(hist * np.log2(hist + 1e-7))
        
        # Normalize entropy to 0-1 range
        # Real faces: entropy ~6-8, Photos: entropy ~4-6
        score = min(1.0, entropy / 8.0)
        
        return score
    
    def _calculate_lbp(self, gray: np.ndarray) -> np.ndarray:
        """Calculate Local Binary Pattern"""
        h, w = gray.shape
        lbp = np.zeros_like(gray)
        
        for i in range(1, h-1):
            for j in range(1, w-1):
                center = gray[i, j]
                code = 0
                
                # 8 neighbors
                code |= (gray[i-1, j-1] > center) << 7
                code |= (gray[i-1, j] > center) << 6
                code |= (gray[i-1, j+1] > center) << 5
                code |= (gray[i, j+1] > center) << 4
                code |= (gray[i+1, j+1] > center) << 3
                code |= (gray[i+1, j] > center) << 2
                code |= (gray[i+1, j-1] > center) << 1
                code |= (gray[i, j-1] > center) << 0
                
                lbp[i, j] = code
        
        return lbp
    
    def _check_face_size(self, face: Dict) -> float:
        """
        Check if face size is reasonable for a live person
        Too small might be a photo, too large might be abnormal
        """
        bbox = face['bbox']
        width = bbox['width']
        height = bbox['height']
        
        # Reasonable face sizes: 100-600 pixels
        if 100 <= width <= 600 and 100 <= height <= 600:
            score = 1.0
        elif 80 <= width <= 800 and 80 <= height <= 800:
            score = 0.7
        else:
            score = 0.3
        
        return score
    
    def check_eye_blink(self, landmarks: np.ndarray) -> bool:
        """
        Check for eye blink (requires video stream)
        This is a placeholder for video-based liveness detection
        """
        # Eye blink detection using Eye Aspect Ratio (EAR)
        # Will be implemented for video streams
        # For now, return True for single images
        return True
    
    def detect_motion(self, frames: list) -> float:
        """
        Detect motion between frames (requires video stream)
        Real people naturally move slightly, photos don't
        """
        # Motion detection using optical flow
        # Will be implemented for video streams
        # For now, return neutral score
        return 0.5
