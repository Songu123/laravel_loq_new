@echo off
REM Start Face Recognition Service

echo ========================================
echo    Face Recognition 3D Service
echo ========================================
echo.

REM Check if virtual environment exists
IF NOT EXIST "venv\" (
    echo Creating virtual environment...
    python -m venv venv
)

REM Activate virtual environment
echo Activating virtual environment...
call venv\Scripts\activate.bat

REM Install dependencies
echo.
echo Installing dependencies...
pip install -r requirements.txt

REM Copy .env if not exists
IF NOT EXIST ".env" (
    echo Creating .env file...
    copy .env.example .env
    echo Please edit .env file with your configuration!
    pause
)

REM Create logs directory
IF NOT EXIST "logs\" (
    mkdir logs
)

REM Start service
echo.
echo Starting Face Recognition Service on port 8001...
echo.
python app.py

pause
