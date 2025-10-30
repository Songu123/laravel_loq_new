"""
Face Embedder using FaceNet
Generates 512-dimensional face embeddings
"""

import numpy as np
import torch
from facenet_pytorch import InceptionResnetV1
from torchvision import transforms
from PIL import Image
import cv2
from typing import List
from loguru import logger
import os
from sklearn.metrics.pairwise import cosine_similarity


class FaceEmbedder:
    """Generate face embeddings using FaceNet"""
    
    def __init__(self):
        """Initialize FaceNet model"""
        self.device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')
        logger.info(f"Using device: {self.device}")
        
        # Load pretrained FaceNet model
        self.model = InceptionResnetV1(pretrained='vggface2').eval().to(self.device)
        
        # Image preprocessing pipeline
        self.transform = transforms.Compose([
            transforms.Resize((160, 160)),  # FaceNet input size
            transforms.ToTensor(),
            transforms.Normalize(mean=[0.5, 0.5, 0.5], std=[0.5, 0.5, 0.5])
        ])
        
        self.embedding_size = int(os.getenv("FACE_EMBEDDING_SIZE", 512))
        
        logger.success("✅ FaceNet model loaded successfully")
    
    def get_embedding(self, image: np.ndarray, face: dict) -> np.ndarray:
        """
        Generate face embedding from image
        
        Args:
            image: Full image (BGR format)
            face: Face dict with bbox information
        
        Returns:
            512-dimensional embedding vector
        """
        # Crop face
        bbox = face['bbox']
        face_crop = image[bbox['y1']:bbox['y2'], bbox['x1']:bbox['x2']]
        
        # Convert BGR to RGB
        face_rgb = cv2.cvtColor(face_crop, cv2.COLOR_BGR2RGB)
        
        # Convert to PIL Image
        pil_image = Image.fromarray(face_rgb)
        
        # Preprocess
        face_tensor = self.transform(pil_image).unsqueeze(0).to(self.device)
        
        # Generate embedding
        with torch.no_grad():
            embedding = self.model(face_tensor)
        
        # Convert to numpy
        embedding = embedding.cpu().numpy().flatten()
        
        # Normalize
        embedding = embedding / np.linalg.norm(embedding)
        
        logger.debug(f"Generated embedding with shape: {embedding.shape}")
        return embedding
    
    def compare_embeddings(
        self, 
        embedding1: np.ndarray, 
        embeddings2: List[List[float]]
    ) -> float:
        """
        Compare one embedding against multiple enrolled embeddings
        
        Args:
            embedding1: Single embedding (512,)
            embeddings2: List of enrolled embeddings
        
        Returns:
            Maximum cosine similarity score (0-1)
        """
        # Convert to numpy array
        if isinstance(embeddings2, list):
            embeddings2 = np.array(embeddings2)
        
        # Reshape if needed
        if embedding1.ndim == 1:
            embedding1 = embedding1.reshape(1, -1)
        
        if embeddings2.ndim == 1:
            embeddings2 = embeddings2.reshape(1, -1)
        
        # Calculate cosine similarity
        similarities = cosine_similarity(embedding1, embeddings2)
        
        # Get maximum similarity
        max_similarity = float(np.max(similarities))
        
        # Convert to confidence (0-1 range)
        # Cosine similarity ranges from -1 to 1, but for faces it's usually 0.3 to 1
        # We normalize to make it more intuitive
        confidence = (max_similarity + 1) / 2  # Map [-1, 1] to [0, 1]
        
        return confidence
    
    def calculate_distance(self, embedding1: np.ndarray, embedding2: np.ndarray) -> float:
        """Calculate Euclidean distance between two embeddings"""
        return float(np.linalg.norm(embedding1 - embedding2))
    
    def average_embeddings(self, embeddings: List[np.ndarray]) -> np.ndarray:
        """Calculate average of multiple embeddings"""
        embeddings_array = np.array(embeddings)
        avg_embedding = np.mean(embeddings_array, axis=0)
        
        # Normalize
        avg_embedding = avg_embedding / np.linalg.norm(avg_embedding)
        
        return avg_embedding
