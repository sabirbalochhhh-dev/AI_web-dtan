@echo off
setlocal
set MYSQL_EXE="C:\xampp\mysql\bin\mysql.exe"
set SQL_FILE="%~dp0healthcare_pharmacy.sql"

"%MYSQL_EXE%" -u root < "%SQL_FILE%"
if errorlevel 1 (
    echo Import failed.
    exit /b 1
)

echo Database imported successfully.
