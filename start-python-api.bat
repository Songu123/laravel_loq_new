@echo off
echo ========================================
echo   LOQ AI Service Starter
echo ========================================
echo.

cd /d "%~dp0python_api_service"

echo Checking Python installation...
python --version
if %errorlevel% neq 0 (
    echo ERROR: Python is not installed or not in PATH
    echo Please install Python 3.8+ from https://www.python.org
    pause
    exit /b 1
)

echo.
echo Installing/Updating dependencies...
pip install -r requirements.txt
if %errorlevel% neq 0 (
    echo WARNING: Some dependencies failed to install
    echo You may need to install them manually
)

echo.
echo ========================================
echo   Starting Python AI Service
echo   URL: http://127.0.0.1:5001
echo   Press Ctrl+C to stop
echo ========================================
echo.

python app.py

pause
