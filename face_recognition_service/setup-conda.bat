@echo off
REM Setup Face Recognition với Conda Environment (Python 3.11)

echo ========================================
echo    Face Recognition Setup (Python 3.11)
echo ========================================
echo.

REM Activate conda environment
echo Activating face_recognition environment...
call conda activate face_recognition

REM Check Python version
echo.
echo Checking Python version...
python --version

REM Install dependencies
echo.
echo Installing dependencies...
pip install -r requirements-minimal.txt

echo.
echo ========================================
echo Setup complete!
echo ========================================
echo.
echo To start the service:
echo   1. conda activate face_recognition
echo   2. python app.py
echo.

pause
