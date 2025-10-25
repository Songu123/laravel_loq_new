from fastapi import FastAPI, File, UploadFile, HTTPException
from fastapi.responses import JSONResponse
import uvicorn
from typing import List, Dict
import os
import tempfile

from models.ocr_handler import OCRHandler
from models.nlp_processor import NLPProcessor
from models.question_extractor import QuestionExtractor

app = FastAPI(title="LOQ AI Service", version="1.0.0")

# Initialize AI components
ocr = OCRHandler()
nlp = NLPProcessor()
extractor = QuestionExtractor()

@app.get("/")
async def root():
    return {"message": "LOQ AI Service is running"}

@app.post("/api/extract-questions")
async def extract_questions_from_pdf(
    file: UploadFile = File(...),
    language: str = "vie"  # vie hoặc eng
):
    """
    Endpoint chính: Upload PDF và trả về câu hỏi trắc nghiệm
    Hỗ trợ:
    - Tự động nhận diện câu hỏi (Câu 1, Câu 2...)
    - Tối thiểu 1 đáp án (A, B, C, D)
    - Tự động phát hiện đáp án đúng từ:
      + Text bôi màu/highlight
      + Đánh dấu rõ ràng (Đáp án: A)
      + Format đặc biệt (bold, underline)
    """
    try:
        # Validate file type
        if not file.filename.endswith('.pdf'):
            raise HTTPException(status_code=400, detail="Only PDF files are allowed")
        
        # Validate file size (max 10MB)
        content = await file.read()
        if len(content) > 10 * 1024 * 1024:  # 10MB
            raise HTTPException(status_code=400, detail="File size exceeds 10MB limit")
        
        # Save temporary file
        with tempfile.NamedTemporaryFile(delete=False, suffix='.pdf') as tmp:
            tmp.write(content)
            tmp_path = tmp.name
        
        # Step 1: OCR - Extract text from PDF with color/highlight detection
        print("Step 1: Extracting text and detecting highlights from PDF...")
        text_data = ocr.extract_text_from_pdf(tmp_path, language)
        
        # Step 2: NLP - Clean and structure text
        print("Step 2: Processing text with NLP...")
        structured_text = nlp.process_text(text_data)
        
        # Step 3: Extract questions and answers
        print("Step 3: Extracting questions and answers...")
        questions = extractor.extract_questions(structured_text)
        
        # Cleanup
        os.unlink(tmp_path)
        
        # Log extraction results
        print(f"Successfully extracted {len(questions)} questions")
        if text_data.get('highlighted'):
            print(f"Detected {len(text_data['highlighted'])} highlighted text regions")
        
        return JSONResponse(content={
            "success": True,
            "total_questions": len(questions),
            "questions": questions,
            "metadata": {
                "filename": file.filename,
                "language": language,
                "raw_text_length": len(text_data.get('text', '')),
                "highlighted_regions": len(text_data.get('highlighted', []))
            }
        })
        
    except HTTPException as he:
        raise he
    except Exception as e:
        import traceback
        print(f"Error: {str(e)}")
        print(traceback.format_exc())
        return JSONResponse(
            status_code=500,
            content={
                "success": False, 
                "error": str(e),
                "detail": "Không thể xử lý file PDF. Vui lòng kiểm tra định dạng file."
            }
        )

@app.post("/api/analyze-text")
async def analyze_text(text: str):
    """
    Endpoint phụ: Phân tích text đã có (không cần PDF)
    """
    try:
        structured = nlp.process_text(text)
        questions = extractor.extract_questions(structured)
        
        return {
            "success": True,
            "questions": questions
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=5001)