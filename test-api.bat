@echo off
echo 🧪 Testing Headless LMS REST API endpoints...

REM WordPress API base URL
set API_BASE=http://localhost/lms/wp-json

echo.
echo 1. Testing WordPress core API...
curl -s -o nul -w "Status: %%{http_code}" "%API_BASE%/wp/v2/"
echo.

echo.
echo 2. Testing LMS courses endpoint...
curl -s -o nul -w "Status: %%{http_code}" "%API_BASE%/headless-lms/v1/courses" 2>nul || echo Endpoint not available yet
echo.

echo.
echo 3. Testing LMS quizzes endpoint...
curl -s -o nul -w "Status: %%{http_code}" "%API_BASE%/headless-lms/v1/quizzes" 2>nul || echo Endpoint not available yet
echo.

echo.
echo 4. Testing CORS headers...
curl -s -I -H "Origin: http://localhost:3000" "%API_BASE%/wp/v2/" | findstr /i "access-control"

echo.
echo ✅ API test completed!
echo.
echo 🔍 To check detailed API response:
echo    curl %API_BASE%/wp/v2/
echo.
echo 📖 API Documentation:
echo    %API_BASE%/

pause
