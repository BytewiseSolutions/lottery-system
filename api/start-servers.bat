@echo off
echo Starting Lottery System...
echo.

echo Starting API Server on port 3002...
start "API Server" cmd /k "npm start"

timeout /t 3 /nobreak > nul

echo Starting Web Server on port 4200...
start "Web Server" cmd /k "cd ..\web && npm start"

echo.
echo Both servers are starting...
echo API Server: http://localhost:3002
echo Web Server: http://localhost:4200
echo.
echo Press any key to exit...
pause > nul