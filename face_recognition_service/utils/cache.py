"""
Cache Manager
Handles Redis caching for face embeddings
"""

import redis
import json
import os
from typing import List, Optional
from loguru import logger


class CacheManager:
    """Manage Redis cache operations"""
    
    def __init__(self):
        """Initialize Redis connection"""
        self.redis_client = None
        self.connect()
        
        # Cache TTL (1 hour by default)
        self.ttl = int(os.getenv("CACHE_TTL", 3600))
    
    def connect(self):
        """Connect to Redis"""
        try:
            self.redis_client = redis.Redis(
                host=os.getenv("REDIS_HOST", "127.0.0.1"),
                port=int(os.getenv("REDIS_PORT", 6379)),
                db=int(os.getenv("REDIS_DB", 1)),
                password=os.getenv("REDIS_PASSWORD", None) or None,
                decode_responses=True,
                socket_connect_timeout=5,
                socket_timeout=5
            )
            
            # Test connection
            self.redis_client.ping()
            logger.success("✅ Connected to Redis cache")
            
        except redis.ConnectionError:
            logger.warning("⚠️  Redis not available - caching disabled")
            self.redis_client = None
        except Exception as e:
            logger.error(f"❌ Failed to connect to Redis: {str(e)}")
            self.redis_client = None
    
    def is_connected(self) -> bool:
        """Check if Redis is connected"""
        if self.redis_client is None:
            return False
        try:
            self.redis_client.ping()
            return True
        except:
            return False
    
    def cache_embeddings(self, user_id: int, embeddings: List):
        """
        Cache face embeddings for a user
        
        Args:
            user_id: User ID
            embeddings: List of embeddings
        """
        if not self.is_connected():
            return
        
        try:
            key = f"face_embeddings:user:{user_id}"
            value = json.dumps(embeddings)
            
            self.redis_client.setex(key, self.ttl, value)
            logger.debug(f"Cached embeddings for user_id={user_id}")
            
        except Exception as e:
            logger.error(f"Failed to cache embeddings: {str(e)}")
    
    def get_embeddings(self, user_id: int) -> Optional[List]:
        """
        Get cached face embeddings for a user
        
        Args:
            user_id: User ID
        
        Returns:
            List of embeddings or None if not cached
        """
        if not self.is_connected():
            return None
        
        try:
            key = f"face_embeddings:user:{user_id}"
            value = self.redis_client.get(key)
            
            if value:
                embeddings = json.loads(value)
                logger.debug(f"Cache HIT for user_id={user_id}")
                return embeddings
            else:
                logger.debug(f"Cache MISS for user_id={user_id}")
                return None
                
        except Exception as e:
            logger.error(f"Failed to get cached embeddings: {str(e)}")
            return None
    
    def delete_embeddings(self, user_id: int):
        """
        Delete cached embeddings for a user
        
        Args:
            user_id: User ID
        """
        if not self.is_connected():
            return
        
        try:
            key = f"face_embeddings:user:{user_id}"
            self.redis_client.delete(key)
            logger.debug(f"Deleted cached embeddings for user_id={user_id}")
            
        except Exception as e:
            logger.error(f"Failed to delete cached embeddings: {str(e)}")
    
    def clear_all(self):
        """Clear all face embeddings cache"""
        if not self.is_connected():
            return
        
        try:
            pattern = "face_embeddings:user:*"
            keys = self.redis_client.keys(pattern)
            
            if keys:
                self.redis_client.delete(*keys)
                logger.info(f"Cleared {len(keys)} cached embeddings")
            
        except Exception as e:
            logger.error(f"Failed to clear cache: {str(e)}")
    
    def get_stats(self) -> dict:
        """Get cache statistics"""
        if not self.is_connected():
            return {"status": "disconnected"}
        
        try:
            info = self.redis_client.info()
            pattern = "face_embeddings:user:*"
            keys = self.redis_client.keys(pattern)
            
            return {
                "status": "connected",
                "cached_users": len(keys),
                "memory_used": info.get("used_memory_human", "N/A"),
                "uptime_seconds": info.get("uptime_in_seconds", 0)
            }
            
        except Exception as e:
            logger.error(f"Failed to get cache stats: {str(e)}")
            return {"status": "error", "message": str(e)}
    
    def close(self):
        """Close Redis connection"""
        if self.redis_client:
            self.redis_client.close()
            logger.info("Redis connection closed")
