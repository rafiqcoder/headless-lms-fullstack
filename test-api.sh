#!/bin/bash

echo "🧪 Testing Headless LMS REST API endpoints..."

# WordPress API base URL
API_BASE="http://localhost/lms/wp-json"

echo ""
echo "1. Testing WordPress core API..."
curl -s -o /dev/null -w "Status: %{http_code}\n" "$API_BASE/wp/v2/"

echo ""
echo "2. Testing LMS courses endpoint..."
curl -s -o /dev/null -w "Status: %{http_code}\n" "$API_BASE/headless-lms/v1/courses" || echo "Endpoint not available yet"

echo ""
echo "3. Testing LMS quizzes endpoint..."
curl -s -o /dev/null -w "Status: %{http_code}\n" "$API_BASE/headless-lms/v1/quizzes" || echo "Endpoint not available yet"

echo ""
echo "4. Testing CORS headers..."
curl -s -I -H "Origin: http://localhost:3000" "$API_BASE/wp/v2/" | grep -i "access-control"

echo ""
echo "✅ API test completed!"
echo ""
echo "🔍 To check detailed API response:"
echo "   curl $API_BASE/wp/v2/"
echo ""
echo "📖 API Documentation:"
echo "   $API_BASE/"
