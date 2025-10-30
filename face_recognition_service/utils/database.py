"""
Database Manager
Handles MySQL database operations for face recognition
"""

import pymysql
import json
import os
from typing import List, Dict, Optional
from loguru import logger
import uuid
from datetime import datetime


class DatabaseManager:
    """Manage database operations"""
    
    def __init__(self):
        """Initialize database connection"""
        self.connection = None
        self.connect()
    
    def connect(self):
        """Connect to MySQL database"""
        try:
            self.connection = pymysql.connect(
                host=os.getenv("DB_HOST", "127.0.0.1"),
                port=int(os.getenv("DB_PORT", 3306)),
                user=os.getenv("DB_USERNAME", "root"),
                password=os.getenv("DB_PASSWORD", ""),
                database=os.getenv("DB_DATABASE", "laravel_loq_quizz"),
                charset='utf8mb4',
                cursorclass=pymysql.cursors.DictCursor,
                autocommit=False
            )
            logger.success("✅ Connected to MySQL database")
        except Exception as e:
            logger.error(f"❌ Failed to connect to database: {str(e)}")
            raise
    
    def is_connected(self) -> bool:
        """Check if database is connected"""
        if self.connection is None:
            return False
        try:
            self.connection.ping(reconnect=True)
            return True
        except:
            return False
    
    def save_face_embeddings(self, user_id: int, embeddings: List) -> str:
        """
        Save face embeddings for a user
        
        Args:
            user_id: User ID
            embeddings: List of face embeddings
        
        Returns:
            Face ID (UUID)
        """
        try:
            face_id = str(uuid.uuid4())
            
            with self.connection.cursor() as cursor:
                # Convert embeddings to JSON
                embeddings_json = json.dumps(embeddings)
                
                # Update user table
                sql = """
                    UPDATE users 
                    SET face_embeddings = %s,
                        face_enrolled_at = NOW()
                    WHERE id = %s
                """
                cursor.execute(sql, (embeddings_json, user_id))
                
            self.connection.commit()
            logger.success(f"✅ Saved {len(embeddings)} embeddings for user_id={user_id}")
            
            return face_id
            
        except Exception as e:
            self.connection.rollback()
            logger.error(f"❌ Failed to save embeddings: {str(e)}")
            raise
    
    def get_face_embeddings(self, user_id: int) -> Optional[List]:
        """
        Get face embeddings for a user
        
        Args:
            user_id: User ID
        
        Returns:
            List of embeddings or None
        """
        try:
            with self.connection.cursor() as cursor:
                sql = "SELECT face_embeddings FROM users WHERE id = %s"
                cursor.execute(sql, (user_id,))
                result = cursor.fetchone()
                
                if result and result['face_embeddings']:
                    embeddings = json.loads(result['face_embeddings'])
                    return embeddings
                
                return None
                
        except Exception as e:
            logger.error(f"❌ Failed to get embeddings: {str(e)}")
            return None
    
    def get_all_enrolled_admins(self) -> List[Dict]:
        """
        Get all admin users with enrolled faces
        
        Returns:
            List of user dicts with id, name, embeddings
        """
        try:
            with self.connection.cursor() as cursor:
                sql = """
                    SELECT id, name, email, face_embeddings 
                    FROM users 
                    WHERE role = 'admin' 
                    AND face_embeddings IS NOT NULL
                    AND is_active = 1
                """
                cursor.execute(sql)
                results = cursor.fetchall()
                
                users = []
                for row in results:
                    users.append({
                        'id': row['id'],
                        'name': row['name'],
                        'email': row['email'],
                        'embeddings': json.loads(row['face_embeddings'])
                    })
                
                logger.info(f"Found {len(users)} enrolled admin users")
                return users
                
        except Exception as e:
            logger.error(f"❌ Failed to get enrolled admins: {str(e)}")
            return []
    
    def log_verification_attempt(
        self,
        user_id: Optional[int],
        success: bool,
        confidence: float,
        is_live: bool,
        ip_address: str = "0.0.0.0",
        user_agent: str = "Python API",
        error_message: Optional[str] = None
    ):
        """
        Log a face verification attempt
        
        Args:
            user_id: User ID (None if unknown)
            success: Whether verification succeeded
            confidence: Confidence score
            is_live: Whether liveness check passed
            ip_address: Client IP
            user_agent: Client user agent
            error_message: Error message if failed
        """
        try:
            with self.connection.cursor() as cursor:
                sql = """
                    INSERT INTO face_login_attempts 
                    (user_id, ip_address, user_agent, success, confidence, is_live, error_message, created_at)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, NOW())
                """
                cursor.execute(sql, (
                    user_id,
                    ip_address,
                    user_agent,
                    success,
                    confidence,
                    is_live,
                    error_message
                ))
            
            self.connection.commit()
            logger.debug(f"Logged verification attempt for user_id={user_id}")
            
        except Exception as e:
            logger.error(f"Failed to log verification attempt: {str(e)}")
            # Don't raise - logging failures shouldn't break the flow
    
    def delete_face_embeddings(self, user_id: int) -> bool:
        """
        Delete face embeddings for a user
        
        Args:
            user_id: User ID
        
        Returns:
            True if successful
        """
        try:
            with self.connection.cursor() as cursor:
                sql = """
                    UPDATE users 
                    SET face_embeddings = NULL,
                        face_enrolled_at = NULL
                    WHERE id = %s
                """
                cursor.execute(sql, (user_id,))
            
            self.connection.commit()
            logger.success(f"✅ Deleted embeddings for user_id={user_id}")
            return True
            
        except Exception as e:
            self.connection.rollback()
            logger.error(f"❌ Failed to delete embeddings: {str(e)}")
            return False
    
    def get_user_info(self, user_id: int) -> Optional[Dict]:
        """
        Get user information
        
        Args:
            user_id: User ID
        
        Returns:
            User dict or None
        """
        try:
            with self.connection.cursor() as cursor:
                sql = """
                    SELECT id, name, email, role, face_enrolled_at 
                    FROM users 
                    WHERE id = %s
                """
                cursor.execute(sql, (user_id,))
                result = cursor.fetchone()
                
                return result
                
        except Exception as e:
            logger.error(f"❌ Failed to get user info: {str(e)}")
            return None
    
    def close(self):
        """Close database connection"""
        if self.connection:
            self.connection.close()
            logger.info("Database connection closed")
