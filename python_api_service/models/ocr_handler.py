import pytesseract
from PIL import Image
import fitz  # PyMuPDF
from pdf2image import convert_from_path
import re
from typing import List, Dict

class OCRHandler:
    def __init__(self):
        """
        Initialize OCR handler with Tesseract
        """
        # Cấu hình Tesseract path (Windows)
        # pytesseract.pytesseract.tesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract.exe'
        pass
    
    def extract_text_from_pdf(self, pdf_path: str, language: str = "vie") -> Dict:
        """
        Extract text from PDF using two methods:
        1. Direct text extraction (if PDF has selectable text)
        2. OCR (if PDF is scanned image)
        Also detect highlighted/colored text for correct answers
        """
        # Try method 1: Direct text extraction with color detection
        text_data = self._extract_direct_text_with_colors(pdf_path)
        
        # If no text found, use OCR
        if len(text_data['text'].strip()) < 100:
            print("Direct extraction failed, using OCR...")
            text_data = self._extract_ocr_text_with_colors(pdf_path, language)
        
        return text_data
    
    def _extract_direct_text_with_colors(self, pdf_path: str) -> Dict:
        """
        Extract text directly from PDF and detect highlighted/colored text
        """
        try:
            doc = fitz.open(pdf_path)
            all_text = ""
            highlighted_texts = []
            
            for page_num, page in enumerate(doc):
                # Get text with color information
                text_instances = page.get_text("dict")
                
                for block in text_instances.get("blocks", []):
                    if block.get("type") == 0:  # Text block
                        for line in block.get("lines", []):
                            for span in line.get("spans", []):
                                text = span.get("text", "")
                                color = span.get("color", 0)
                                
                                # Add to all text
                                all_text += text + " "
                                
                                # Detect highlighted text (non-black color)
                                # Black color is 0, other colors have different values
                                if color != 0:
                                    highlighted_texts.append({
                                        "text": text.strip(),
                                        "color": color,
                                        "page": page_num + 1
                                    })
                
                # Also check for highlight annotations
                for annot in page.annots():
                    if annot.type[0] == 8:  # Highlight annotation
                        # Get text under highlight
                        rect = annot.rect
                        highlight_text = page.get_textbox(rect)
                        if highlight_text:
                            highlighted_texts.append({
                                "text": highlight_text.strip(),
                                "type": "highlight",
                                "page": page_num + 1
                            })
                
                all_text += "\n"
            
            doc.close()
            
            return {
                "text": all_text,
                "highlighted": highlighted_texts
            }
        except Exception as e:
            print(f"Direct extraction error: {e}")
            return {"text": "", "highlighted": []}
    
    def _extract_ocr_text_with_colors(self, pdf_path: str, language: str) -> Dict:
        """
        Extract text using OCR and detect colored/highlighted text from images
        """
        try:
            # Convert PDF to images
            images = convert_from_path(pdf_path, dpi=300)
            
            # OCR config
            lang_map = {
                'vie': 'vie+eng',  # Vietnamese + English
                'eng': 'eng'
            }
            ocr_lang = lang_map.get(language, 'eng')
            
            # Extract text from each page
            full_text = ""
            highlighted_texts = []
            
            for i, image in enumerate(images):
                print(f"Processing page {i+1}/{len(images)}...")
                
                # Detect highlighted regions by color
                highlighted_regions = self._detect_highlighted_regions(image)
                
                # OCR with custom config
                custom_config = r'--oem 3 --psm 6'
                text = pytesseract.image_to_string(
                    image, 
                    lang=ocr_lang, 
                    config=custom_config
                )
                
                full_text += f"\n--- PAGE {i+1} ---\n{text}"
                
                # OCR on highlighted regions
                for region in highlighted_regions:
                    try:
                        region_img = image.crop(region['bbox'])
                        region_text = pytesseract.image_to_string(region_img, lang=ocr_lang)
                        if region_text.strip():
                            highlighted_texts.append({
                                "text": region_text.strip(),
                                "page": i + 1,
                                "color": "detected"
                            })
                    except:
                        pass
            
            return {
                "text": full_text,
                "highlighted": highlighted_texts
            }
            
        except Exception as e:
            print(f"OCR error: {e}")
            return {"text": "", "highlighted": []}
    
    def _detect_highlighted_regions(self, image) -> List[Dict]:
        """
        Detect highlighted/colored regions in image
        """
        import numpy as np
        
        try:
            # Convert PIL Image to numpy array
            img_array = np.array(image)
            
            # Detect yellow/bright colored regions (common for highlights)
            # This is a simple implementation - can be improved
            highlighted_regions = []
            
            # TODO: Implement color-based region detection
            # For now, return empty list
            
            return highlighted_regions
            
        except Exception as e:
            print(f"Highlight detection error: {e}")
            return []
    
    def clean_ocr_text(self, text: str) -> str:
        """
        Clean OCR output (remove noise, fix spacing)
        """
        # Remove multiple spaces
        text = re.sub(r'\s+', ' ', text)
        
        # Remove special characters but keep Vietnamese
        text = re.sub(r'[^\w\s\u00C0-\u1EF9\.\,\?\!\:\;\-\(\)]', '', text)
        
        return text.strip()