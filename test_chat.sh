#!/bin/bash
REG_RESP=$(curl -s -X POST http://127.0.0.1:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"maxi@gmail.com","password":"password"}')
TOKEN=$(echo $REG_RESP | grep -o '"token":"[^"]*' | cut -d'"' -f4)
echo "Login as maxi:"
echo $TOKEN

echo "Fetching Chat History..."
curl -s -X GET http://127.0.0.1:8000/api/v1/chat/history \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
