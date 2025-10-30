@echo off
echo ================================================
echo  Installing Python API Service Dependencies
echo ================================================
echo.

echo Installing core packages...
pip install fastapi
echo.

echo Installing web server...
pip install uvicorn
echo.

echo Installing file upload support...
pip install python-multipart
echo.

echo Installing PDF processing...
pip install PyMuPDF
echo.

echo Installing image tools...
pip install Pillow pdf2image
echo.

echo Installing OCR...
pip install pytesseract
echo.

echo Installing utilities...
pip install python-dotenv
echo.

echo ================================================
echo  Installation Complete!
echo ================================================
echo.
echo Now you can run: python app.py
echo.
pause
