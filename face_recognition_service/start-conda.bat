@echo off
REM Start Face Recognition Service với Conda

echo Starting Face Recognition Service...
echo.

REM Activate conda environment
call conda activate face_recognition

REM Check Python version
python --version

REM Copy .env if not exists
IF NOT EXIST ".env" (
    echo Creating .env file...
    copy .env.example .env
    echo.
    echo ⚠️  Please edit .env file with your database credentials!
    echo.
    pause
)

REM Create logs directory
IF NOT EXIST "logs\" (
    mkdir logs
)

REM Start service
echo.
echo Starting Face Recognition Service on port 8001...
echo Press Ctrl+C to stop
echo.
python app.py

pause
