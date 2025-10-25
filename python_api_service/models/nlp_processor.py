from transformers import AutoTokenizer, AutoModelForTokenClassification
from transformers import pipeline
import re
from typing import List, Dict

class NLPProcessor:
    def __init__(self):
        """
        Initialize NLP model
        Sử dụng PhoBERT cho tiếng Việt hoặc BERT cho tiếng Anh
        """
        try:
            # PhoBERT for Vietnamese
            self.tokenizer = AutoTokenizer.from_pretrained("vinai/phobert-base")
            # Hoặc dùng model đơn giản hơn cho demo
            # self.model = AutoModelForTokenClassification.from_pretrained("vinai/phobert-base")
            
            print("NLP Model loaded successfully")
        except Exception as e:
            print(f"Warning: Could not load NLP model: {e}")
            self.tokenizer = None
    
    def process_text(self, text_data: Dict) -> Dict:
        """
        Process and structure extracted text with highlighted text info
        """
        text = text_data.get("text", "")
        highlighted = text_data.get("highlighted", [])
        
        # Clean text
        cleaned_text = self._clean_text(text)
        
        # Split into sections
        sections = self._split_into_sections(cleaned_text)
        
        # Identify question patterns
        potential_questions = self._find_question_patterns(cleaned_text)
        
        # Extract highlighted text content
        highlighted_texts = [h.get("text", "") for h in highlighted if h.get("text")]
        
        structured = {
            "raw_text": text,
            "cleaned_text": cleaned_text,
            "sections": sections,
            "potential_questions": potential_questions,
            "highlighted_texts": highlighted_texts  # Pass highlighted texts to question extractor
        }
        
        return structured
    
    def _clean_text(self, text: str) -> str:
        """
        Clean and normalize text
        """
        # Remove page markers
        text = re.sub(r'--- PAGE \d+ ---', '', text)
        
        # Normalize whitespace
        text = re.sub(r'\s+', ' ', text)
        
        # Fix common OCR errors
        text = text.replace('|', 'I')
        # Don't replace O with 0 - keep original
        
        return text.strip()
    
    def _split_into_sections(self, text: str) -> List[str]:
        """
        Split text into logical sections
        """
        # Split by common section markers
        sections = re.split(r'\n\n+|(?:Câu|Question)\s+\d+', text)
        return [s.strip() for s in sections if s.strip()]
    
    def _find_question_patterns(self, text: str) -> List[Dict]:
        """
        Find potential question blocks using regex patterns
        Improved to handle more formats
        """
        questions = []
        
        # Pattern 1: Câu 1: ... A. ... B. ... C. ... D. ...
        # Pattern 2: Question 1: ... A. ... B. ... C. ... D. ...
        # Pattern 3: Câu hỏi 1: ... A. ... B. ...
        
        patterns = [
            r'(?:Câu|Question|Câu hỏi|CH)\s+(\d+)[:\.\s]+(.+?)(?=(?:Câu|Question|Câu hỏi|CH)\s+\d+|$)',
            r'(\d+)[.\)]\s+(.+?)(?=\d+[.\)]|$)',  # Simple numbered format
        ]
        
        for pattern in patterns:
            matches = re.finditer(pattern, text, re.DOTALL | re.IGNORECASE)
            
            for match in matches:
                question_num = match.group(1)
                question_text = match.group(2).strip()
                
                # Validate this looks like a question (has at least one answer option)
                if re.search(r'[A-D][.\)]\s+', question_text):
                    questions.append({
                        "question_number": question_num,
                        "raw_text": question_text,
                        "type": "detected"
                    })
            
            # If found questions with this pattern, don't try other patterns
            if questions:
                break
        
        return questions