import re
from typing import List, Dict, Optional

class QuestionExtractor:
    def __init__(self):
        """
        Initialize question extractor with patterns
        """
        # Answer patterns for multiple choice (now just need A, B, C, D - at least 1)
        self.answer_patterns = [
            r'([A-D])[.\)]\s*(.+?)(?=[A-D][.\)]|$)',  # A. answer
            r'([A-D])\s*-\s*(.+?)(?=[A-D]\s*-|$)',     # A - answer
            r'([A-D])\s+(.+?)(?=[A-D]\s+|$)',          # A answer
        ]
        
        # Correct answer patterns
        self.correct_answer_patterns = [
            r'(?:Đáp án|Answer|Correct|ĐA|DA)[:：\s]+([A-D])',
            r'(?:Đ[aá]p\s*[aá]n\s*đ[uú]ng)[:：\s]*([A-D])',
        ]
    
    def extract_questions(self, structured_data: Dict) -> List[Dict]:
        """
        Main method: Extract questions and answers from structured text
        Now supports detecting correct answer from highlighted text
        """
        questions = []
        
        potential_questions = structured_data.get("potential_questions", [])
        highlighted_texts = structured_data.get("highlighted_texts", [])
        
        for pq in potential_questions:
            question = self._parse_question_block(pq["raw_text"], highlighted_texts)
            
            if question and self._validate_question(question):
                question["original_number"] = pq.get("question_number")
                questions.append(question)
        
        return questions
    
    def _parse_question_block(self, text: str, highlighted_texts: List[str] = None) -> Optional[Dict]:
        """
        Parse a single question block into structured format
        Detect correct answer from highlighted text or explicit markers
        """
        try:
            # Extract question stem (before answer options)
            question_stem = self._extract_question_stem(text)
            
            # Extract answer options
            answers = self._extract_answers(text)
            
            # Extract correct answer from text
            correct_answer = self._extract_correct_answer(text)
            
            # If no explicit correct answer, try to detect from highlighted text
            if not correct_answer and highlighted_texts:
                correct_answer = self._detect_correct_from_highlighted(answers, highlighted_texts)
            
            # If still no correct answer, try to detect from formatting
            if not correct_answer:
                correct_answer = self._detect_correct_from_formatting(text, answers)
            
            if not question_stem or len(answers) < 1:  # Changed from 2 to 1
                return None
            
            # Mark correct answer in answers list
            for answer in answers:
                if correct_answer and answer['letter'] == correct_answer:
                    answer['is_correct'] = True
            
            return {
                "content": question_stem,
                "answers": answers,
                "correct_answer": correct_answer,
                "difficulty": self._estimate_difficulty(question_stem),
                "raw_text": text
            }
            
        except Exception as e:
            print(f"Error parsing question: {e}")
            return None
    
    def _extract_question_stem(self, text: str) -> str:
        """
        Extract the main question text
        """
        # Remove answer options to get question stem
        stem = re.split(r'[A-D][.\)]\s*', text)[0]
        stem = stem.strip()
        
        # Remove common prefixes
        stem = re.sub(r'^(?:Câu|Question)\s+\d+[:\.\s]+', '', stem)
        stem = re.sub(r'^(?:Câu hỏi|CH)\s+\d+[:\.\s]+', '', stem, flags=re.IGNORECASE)
        
        return stem.strip()
    
    def _extract_answers(self, text: str) -> List[Dict]:
        """
        Extract answer options (A, B, C, D) - at least 1 required
        """
        answers = []
        
        # Try different patterns
        for pattern in self.answer_patterns:
            matches = re.finditer(pattern, text, re.DOTALL)
            temp_answers = []
            
            for match in matches:
                letter = match.group(1)
                content = match.group(2).strip()
                
                # Clean answer content
                content = re.sub(r'\s+', ' ', content)
                
                # Split by newline and take meaningful lines
                lines = content.split('\n')
                content = ' '.join([line.strip() for line in lines if line.strip()])
                
                # Take only first reasonable part
                content = content.split('Đáp án')[0].strip()
                content = content.split('Answer')[0].strip()
                
                if content and len(content) > 1 and len(content) < 500:
                    temp_answers.append({
                        "letter": letter,
                        "content": content,
                        "is_correct": False  # Will be updated later
                    })
            
            # If found at least 1 valid answer, use this pattern
            if len(temp_answers) >= 1:  # Changed from 2 to 1
                answers = temp_answers
                break
        
        return answers
    
    def _extract_correct_answer(self, text: str) -> Optional[str]:
        """
        Extract correct answer letter from explicit markers
        """
        for pattern in self.correct_answer_patterns:
            match = re.search(pattern, text, re.IGNORECASE)
            if match:
                return match.group(1).upper()
        
        return None
    
    def _detect_correct_from_highlighted(self, answers: List[Dict], highlighted_texts: List[str]) -> Optional[str]:
        """
        Detect correct answer from highlighted/colored text
        """
        try:
            # Convert highlighted texts to lowercase for comparison
            highlighted_lower = [h.lower().strip() for h in highlighted_texts]
            
            # Check if any answer content is in highlighted texts
            for answer in answers:
                answer_text = answer['content'].lower().strip()
                
                # Direct match
                if answer_text in highlighted_lower:
                    return answer['letter']
                
                # Partial match (at least 80% of answer is highlighted)
                for highlighted in highlighted_lower:
                    if len(highlighted) > 10:  # Meaningful highlight
                        # Check if highlighted contains significant part of answer
                        if highlighted in answer_text or answer_text in highlighted:
                            return answer['letter']
                        
                        # Check similarity
                        common_words = set(answer_text.split()) & set(highlighted.split())
                        if len(common_words) > len(answer_text.split()) * 0.6:
                            return answer['letter']
            
        except Exception as e:
            print(f"Error detecting correct answer from highlight: {e}")
        
        return None
    
    def _detect_correct_from_formatting(self, text: str, answers: List[Dict]) -> Optional[str]:
        """
        Try to detect correct answer from formatting clues
        (bold, underline, etc. in text)
        """
        # Look for patterns like: A. **answer** or A. _answer_
        for answer in answers:
            letter = answer['letter']
            
            # Check for bold markers near this answer
            bold_pattern = rf'{letter}[.\)]\s*\*\*(.+?)\*\*'
            if re.search(bold_pattern, text):
                return letter
            
            # Check for underline markers
            underline_pattern = rf'{letter}[.\)]\s*_(.+?)_'
            if re.search(underline_pattern, text):
                return letter
        
        return None
    
    def _estimate_difficulty(self, question_text: str) -> str:
        """
        Estimate question difficulty based on length and complexity
        """
        length = len(question_text)
        
        # Count complex indicators
        complex_indicators = [
            'phân tích', 'so sánh', 'giải thích', 'tại sao', 'như thế nào',
            'analyze', 'compare', 'explain', 'why', 'how'
        ]
        
        complexity = sum(1 for indicator in complex_indicators if indicator in question_text.lower())
        
        if length < 50 and complexity == 0:
            return "easy"
        elif length < 150 and complexity < 2:
            return "medium"
        else:
            return "hard"
    
    def _validate_question(self, question: Dict) -> bool:
        """
        Validate if extracted question is valid
        """
        # Must have content
        if not question.get("content"):
            return False
        
        # Must have at least 1 answer (changed from 2)
        if len(question.get("answers", [])) < 1:
            return False
        
        # Content must be meaningful (not too short)
        if len(question["content"]) < 10:
            return False
        
        # Answer content should not be too short
        for answer in question["answers"]:
            if len(answer["content"]) < 1:
                return False
        
        return True