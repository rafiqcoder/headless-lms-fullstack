#!/bin/bash

echo "🧪 Testing WPGraphQL LMS Setup..."

# GraphQL endpoint
GRAPHQL_URL="http://localhost/lms/graphql"

echo ""
echo "1. Testing GraphQL endpoint availability..."
curl -s -o /dev/null -w "Status: %{http_code}\n" "$GRAPHQL_URL"

echo ""
echo "2. Testing introspection query..."
curl -X POST \
  -H "Content-Type: application/json" \
  -d '{"query":"query IntrospectionQuery { __schema { queryType { name } } }"}' \
  "$GRAPHQL_URL" | head -c 200

echo ""
echo ""
echo "3. Testing courses query..."
curl -X POST \
  -H "Content-Type: application/json" \
  -d '{"query":"query GetCourses { courses { nodes { id title } } }"}' \
  "$GRAPHQL_URL" | head -c 200

echo ""
echo ""
echo "4. Testing CORS headers..."
curl -s -I -H "Origin: http://localhost:3000" "$GRAPHQL_URL" | grep -i "access-control"

echo ""
echo ""
echo "✅ GraphQL API test completed!"
echo ""
echo "🔍 To test full query in browser:"
echo "   POST $GRAPHQL_URL"
echo "   Body: {\"query\":\"{ courses { nodes { id title } } }\"}"
echo ""
echo "📖 GraphiQL IDE (if enabled):"
echo "   $GRAPHQL_URL"
