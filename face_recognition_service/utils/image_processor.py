"""
Image Processor Utility
Handles image encoding/decoding, preprocessing, and augmentation
"""

import base64
import cv2
import numpy as np
from PIL import Image
from io import BytesIO
from typing import Optional
from loguru import logger


class ImageProcessor:
    """Utility class for image processing operations"""
    
    def __init__(self):
        """Initialize image processor"""
        logger.info("✅ Image Processor initialized")
    
    def decode_base64(self, base64_string: str) -> np.ndarray:
        """
        Decode base64 string to OpenCV image
        
        Args:
            base64_string: Base64 encoded image (with or without data URI prefix)
        
        Returns:
            OpenCV image (BGR format)
        """
        try:
            # Remove data URI prefix if present
            if ',' in base64_string:
                base64_string = base64_string.split(',')[1]
            
            # Decode base64
            image_bytes = base64.b64decode(base64_string)
            
            # Convert to numpy array
            nparr = np.frombuffer(image_bytes, np.uint8)
            
            # Decode image
            image = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
            
            if image is None:
                raise ValueError("Failed to decode image")
            
            return image
            
        except Exception as e:
            logger.error(f"Failed to decode base64 image: {str(e)}")
            raise ValueError(f"Invalid base64 image: {str(e)}")
    
    def encode_base64(self, image: np.ndarray, format: str = 'jpg') -> str:
        """
        Encode OpenCV image to base64 string
        
        Args:
            image: OpenCV image (BGR format)
            format: Output format ('jpg' or 'png')
        
        Returns:
            Base64 encoded string
        """
        # Encode image
        _, buffer = cv2.imencode(f'.{format}', image)
        
        # Convert to base64
        base64_string = base64.b64encode(buffer).decode('utf-8')
        
        return base64_string
    
    def resize_image(
        self, 
        image: np.ndarray, 
        max_width: int = 1280, 
        max_height: int = 720
    ) -> np.ndarray:
        """
        Resize image while maintaining aspect ratio
        
        Args:
            image: Input image
            max_width: Maximum width
            max_height: Maximum height
        
        Returns:
            Resized image
        """
        height, width = image.shape[:2]
        
        # Check if resize is needed
        if width <= max_width and height <= max_height:
            return image
        
        # Calculate scaling factor
        scale = min(max_width / width, max_height / height)
        
        # Calculate new dimensions
        new_width = int(width * scale)
        new_height = int(height * scale)
        
        # Resize
        resized = cv2.resize(image, (new_width, new_height), interpolation=cv2.INTER_AREA)
        
        logger.debug(f"Resized image from {width}x{height} to {new_width}x{new_height}")
        
        return resized
    
    def normalize_lighting(self, image: np.ndarray) -> np.ndarray:
        """
        Normalize lighting using histogram equalization
        
        Args:
            image: Input image
        
        Returns:
            Normalized image
        """
        # Convert to LAB color space
        lab = cv2.cvtColor(image, cv2.COLOR_BGR2LAB)
        
        # Split channels
        l, a, b = cv2.split(lab)
        
        # Apply CLAHE to L channel
        clahe = cv2.createCLAHE(clipLimit=2.0, tileGridSize=(8, 8))
        l = clahe.apply(l)
        
        # Merge channels
        lab = cv2.merge([l, a, b])
        
        # Convert back to BGR
        normalized = cv2.cvtColor(lab, cv2.COLOR_LAB2BGR)
        
        return normalized
    
    def enhance_face(self, image: np.ndarray) -> np.ndarray:
        """
        Enhance face image quality
        
        Args:
            image: Face crop image
        
        Returns:
            Enhanced image
        """
        # Normalize lighting
        enhanced = self.normalize_lighting(image)
        
        # Denoise
        enhanced = cv2.fastNlMeansDenoisingColored(enhanced, None, 10, 10, 7, 21)
        
        # Sharpen
        kernel = np.array([[-1, -1, -1],
                          [-1,  9, -1],
                          [-1, -1, -1]])
        enhanced = cv2.filter2D(enhanced, -1, kernel)
        
        return enhanced
    
    def crop_center(
        self, 
        image: np.ndarray, 
        crop_width: int, 
        crop_height: int
    ) -> np.ndarray:
        """
        Crop center of image
        
        Args:
            image: Input image
            crop_width: Width of crop
            crop_height: Height of crop
        
        Returns:
            Cropped image
        """
        height, width = image.shape[:2]
        
        # Calculate center
        center_x = width // 2
        center_y = height // 2
        
        # Calculate crop coordinates
        x1 = max(0, center_x - crop_width // 2)
        y1 = max(0, center_y - crop_height // 2)
        x2 = min(width, x1 + crop_width)
        y2 = min(height, y1 + crop_height)
        
        return image[y1:y2, x1:x2]
    
    def rotate_image(self, image: np.ndarray, angle: float) -> np.ndarray:
        """
        Rotate image by angle
        
        Args:
            image: Input image
            angle: Rotation angle in degrees
        
        Returns:
            Rotated image
        """
        height, width = image.shape[:2]
        center = (width // 2, height // 2)
        
        # Get rotation matrix
        matrix = cv2.getRotationMatrix2D(center, angle, 1.0)
        
        # Rotate
        rotated = cv2.warpAffine(image, matrix, (width, height))
        
        return rotated
    
    def add_padding(
        self, 
        image: np.ndarray, 
        padding: int, 
        color: tuple = (0, 0, 0)
    ) -> np.ndarray:
        """
        Add padding around image
        
        Args:
            image: Input image
            padding: Padding size in pixels
            color: Padding color (BGR)
        
        Returns:
            Padded image
        """
        return cv2.copyMakeBorder(
            image, 
            padding, padding, padding, padding,
            cv2.BORDER_CONSTANT,
            value=color
        )
    
    def validate_image(self, image: np.ndarray) -> bool:
        """
        Validate if image is valid
        
        Args:
            image: Input image
        
        Returns:
            True if valid, False otherwise
        """
        if image is None:
            return False
        
        if image.size == 0:
            return False
        
        if len(image.shape) != 3:
            return False
        
        if image.shape[2] != 3:
            return False
        
        return True
    
    def save_image(self, image: np.ndarray, filepath: str) -> bool:
        """
        Save image to file
        
        Args:
            image: Image to save
            filepath: Output file path
        
        Returns:
            True if successful, False otherwise
        """
        try:
            cv2.imwrite(filepath, image)
            logger.info(f"Image saved to {filepath}")
            return True
        except Exception as e:
            logger.error(f"Failed to save image: {str(e)}")
            return False
    
    def load_image(self, filepath: str) -> Optional[np.ndarray]:
        """
        Load image from file
        
        Args:
            filepath: Input file path
        
        Returns:
            Loaded image or None if failed
        """
        try:
            image = cv2.imread(filepath)
            if image is None:
                raise ValueError("Failed to load image")
            return image
        except Exception as e:
            logger.error(f"Failed to load image: {str(e)}")
            return None
