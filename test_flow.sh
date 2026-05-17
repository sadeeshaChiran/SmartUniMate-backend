#!/bin/bash
echo "Registering user..."
REG_RESP=$(curl -s -X POST http://127.0.0.1:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test1@susl.lk","student_id":"12345","password":"password","password_confirmation":"password"}')
echo $REG_RESP
TOKEN=$(echo $REG_RESP | grep -o '"token":"[^"]*' | cut -d'"' -f4)
echo "Token: $TOKEN"

echo "Updating Profile..."
curl -s -X PUT http://127.0.0.1:8000/api/v1/profile \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name":"Updated Name","phone":"1234567890","faculty":"Science"}'

echo ""
echo "Creating Post..."
curl -s -X POST http://127.0.0.1:8000/api/v1/communities \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"post_content":"This is a test post","description":"General"}'

echo ""
echo "Fetching Posts..."
curl -s -X GET http://127.0.0.1:8000/api/v1/communities \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"

