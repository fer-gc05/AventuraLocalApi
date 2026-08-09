#!/bin/bash
cd /home/ferchgc/Projects/production/app_turismo/AventuraLocalApi

PASS=0; FAIL=0
BASE="http://127.0.0.1:8100/api"

test_endpoint() {
  local desc="$1" expected="$2" method="$3" url="$4" auth="$5" data="$6"
  
  local code
  if [ -n "$data" ] && [ -n "$auth" ]; then
    code=$(curl -s -w "%{http_code}" -o /tmp/resp.txt -X "$method" "$url" \
      -H "Authorization: Bearer $auth" -H "Content-Type: application/json" -H "Accept: application/json" -d "$data")
  elif [ -n "$data" ]; then
    code=$(curl -s -w "%{http_code}" -o /tmp/resp.txt -X "$method" "$url" \
      -H "Content-Type: application/json" -H "Accept: application/json" -d "$data")
  elif [ -n "$auth" ]; then
    code=$(curl -s -w "%{http_code}" -o /tmp/resp.txt -X "$method" "$url" \
      -H "Authorization: Bearer $auth" -H "Accept: application/json")
  else
    code=$(curl -s -w "%{http_code}" -o /tmp/resp.txt -X "$method" "$url" -H "Accept: application/json")
  fi
  
  if [ "$code" = "$expected" ]; then
    echo "  PASS ($code): $desc"
    PASS=$((PASS+1))
  else
    echo "  FAIL ($code, expected $expected): $desc"
    cat /tmp/resp.txt | python3 -c "import sys,json;d=json.load(sys.stdin);print('    ',d.get('message','')[:200])" 2>/dev/null || cat /tmp/resp.txt | head -c 200
    echo ""
    FAIL=$((FAIL+1))
  fi
}

login() {
  curl -s -X POST "$BASE/auth/login" -H "Content-Type: application/json" -H "Accept: application/json" \
    -d "{\"email\":\"$1\",\"password\":\"$2\"}" | python3 -c "import sys,json;d=json.load(sys.stdin);print(d.get('token') or d.get('data',{}).get('token',''))"
}

php artisan migrate:fresh --seed 2>&1 | tail -1

curl -s -X POST "$BASE/auth/register" -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"name":"Admin","email":"admin@test.com","password":"password","confirm_password":"password"}' > /dev/null
curl -s -X POST "$BASE/auth/register" -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"name":"Guide","email":"guide@test.com","password":"password","confirm_password":"password"}' > /dev/null

ADMIN_TOKEN=$(login admin@test.com password)
GUIDE_TOKEN=$(login guide@test.com password)

curl -s -X PUT "$BASE/users/2" -H "Authorization: Bearer $ADMIN_TOKEN" -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"roles":["Administrator"]}' > /dev/null
curl -s -X PUT "$BASE/users/3" -H "Authorization: Bearer $ADMIN_TOKEN" -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"roles":["Guide"]}' > /dev/null

ADMIN_TOKEN=$(login admin@test.com password)
GUIDE_TOKEN=$(login guide@test.com password)

echo "=== AUTH (6) ==="
test_endpoint "Register" 201 POST "$BASE/auth/register" "" '{"name":"New","email":"new@test.com","password":"password","confirm_password":"password"}'
test_endpoint "Login success" 200 POST "$BASE/auth/login" "" '{"email":"admin@test.com","password":"password"}'
test_endpoint "Me (auth)" 200 GET "$BASE/auth/me" "$ADMIN_TOKEN"
test_endpoint "Me (no auth)" 401 GET "$BASE/auth/me"
test_endpoint "Logout" 200 POST "$BASE/auth/logout" "$ADMIN_TOKEN"
ADMIN_TOKEN=$(login admin@test.com password)
test_endpoint "Logout (no token)" 401 POST "$BASE/auth/logout"

echo "=== CATEGORIES (10) ==="
test_endpoint "Create" 201 POST "$BASE/categories" "$ADMIN_TOKEN" '{"name":"Aventura","description":"Tours de aventura"}'
test_endpoint "List" 200 GET "$BASE/categories"
test_endpoint "Show" 200 GET "$BASE/categories/1"
test_endpoint "Update" 200 PUT "$BASE/categories/1" "$ADMIN_TOKEN" '{"name":"Aventura Updated"}'
test_endpoint "Popular" 200 GET "$BASE/categories/popular"
test_endpoint "Trashed (auth)" 200 GET "$BASE/categories/trashed" "$ADMIN_TOKEN"
test_endpoint "Trashed (no auth)" 401 GET "$BASE/categories/trashed"
test_endpoint "No auth create" 401 POST "$BASE/categories" "" '{"name":"Fail"}'
test_endpoint "Delete" 200 DELETE "$BASE/categories/1" "$ADMIN_TOKEN"
test_endpoint "Restore" 200 POST "$BASE/categories/1/restore" "$ADMIN_TOKEN"

echo "=== TAGS (9) ==="
test_endpoint "Create" 201 POST "$BASE/tags" "$ADMIN_TOKEN" '{"name":"Montana","color":"#FF0000"}'
test_endpoint "List" 200 GET "$BASE/tags"
test_endpoint "Show" 200 GET "$BASE/tags/1"
test_endpoint "Update" 200 PUT "$BASE/tags/1" "$ADMIN_TOKEN" '{"name":"Montana Updated","color":"#00FF00"}'
test_endpoint "Trashed (auth)" 404 GET "$BASE/tags/trashed" "$ADMIN_TOKEN"
test_endpoint "Trashed (no auth)" 401 GET "$BASE/tags/trashed"
test_endpoint "No auth create" 401 POST "$BASE/tags" "" '{"name":"Fail","color":"#000"}'
test_endpoint "Delete" 200 DELETE "$BASE/tags/1" "$ADMIN_TOKEN"
test_endpoint "Restore" 200 POST "$BASE/tags/1/restore" "$ADMIN_TOKEN"

echo "=== DESTINATIONS (14) ==="
test_endpoint "Create" 201 POST "$BASE/destinations" "$ADMIN_TOKEN" '{"name":"Chirripo","description":"Pico","latitude":9.48,"longitude":-83.48,"category_id":1,"city":"Rivas","state":"SJ","country":"CR"}'
test_endpoint "List" 200 GET "$BASE/destinations"
test_endpoint "Show" 200 GET "$BASE/destinations/1"
test_endpoint "Update" 200 PUT "$BASE/destinations/1" "$ADMIN_TOKEN" '{"name":"Chirripo Updated"}'
test_endpoint "Popular" 200 GET "$BASE/destinations/popular"
test_endpoint "Nearby" 200 GET "$BASE/destinations/nearby?latitude=9.5&longitude=-83.5"
test_endpoint "Trashed" 200 GET "$BASE/destinations/trashed" "$ADMIN_TOKEN"
test_endpoint "Statistics" 200 GET "$BASE/destinations/1/statistics" "$ADMIN_TOKEN"
test_endpoint "Routes" 200 GET "$BASE/destinations/1/routes"
test_endpoint "Events" 200 GET "$BASE/destinations/1/events"
test_endpoint "Reviews" 200 GET "$BASE/destinations/1/reviews"
test_endpoint "No auth create" 401 POST "$BASE/destinations" "" '{"name":"Fail"}'
test_endpoint "Delete" 200 DELETE "$BASE/destinations/1" "$ADMIN_TOKEN"
test_endpoint "Restore" 200 POST "$BASE/destinations/1/restore" "$ADMIN_TOKEN"

echo "=== GUIDE PROFILES (6) ==="
test_endpoint "Create (traveler)" 403 POST "$BASE/guide-profiles" "$ADMIN_TOKEN" '{"specialization":"test","experience_years":1,"bio":"test"}'
test_endpoint "Create (guide)" 201 POST "$BASE/guide-profiles" "$GUIDE_TOKEN" '{"specialization":"Montañismo","experience_years":5,"bio":"Guía certificado"}'
test_endpoint "List" 200 GET "$BASE/guide-profiles"
test_endpoint "Show" 200 GET "$BASE/guide-profiles/1"
test_endpoint "Update" 200 PUT "$BASE/guide-profiles/1" "$GUIDE_TOKEN" '{"bio":"Updated bio"}'
test_endpoint "Verified" 200 GET "$BASE/guide-profiles/verified"

echo "=== EVENTS (16) ==="
test_endpoint "List" 200 GET "$BASE/events"
test_endpoint "Create" 201 POST "$BASE/events" "$GUIDE_TOKEN" '{"title":"Subida Chirripo","description":"Evento","location":"San José","latitude":9.93,"longitude":-84.09,"start_datetime":"2026-09-01T08:00:00","end_datetime":"2026-09-02T18:00:00","destination_id":1}'
test_endpoint "Show" 200 GET "$BASE/events/1"
test_endpoint "Update" 200 PUT "$BASE/events/1" "$GUIDE_TOKEN" '{"title":"Updated Event"}'
test_endpoint "Popular" 200 GET "$BASE/events/popular"
test_endpoint "Upcoming" 200 GET "$BASE/events/upcoming"
test_endpoint "Nearby" 200 GET "$BASE/events/nearby?latitude=9.5&longitude=-83.5"
test_endpoint "Trashed" 200 GET "$BASE/events/trashed" "$ADMIN_TOKEN"
test_endpoint "Attend" 200 POST "$BASE/events/1/attend" "$ADMIN_TOKEN"
test_endpoint "Attendees" 200 GET "$BASE/events/1/attendees" "$GUIDE_TOKEN"
test_endpoint "Cancel" 200 POST "$BASE/events/1/cancel-attendance" "$ADMIN_TOKEN"
test_endpoint "Statistics" 200 GET "$BASE/events/1/statistics" "$GUIDE_TOKEN"
test_endpoint "Calendar" 200 GET "$BASE/events/calendar"
test_endpoint "Recommendations" 200 GET "$BASE/events/recommendations" "$GUIDE_TOKEN"
test_endpoint "Delete" 200 DELETE "$BASE/events/1" "$GUIDE_TOKEN"
test_endpoint "Restore" 200 POST "$BASE/events/1/restore" "$GUIDE_TOKEN"

echo "=== TOURS (8) ==="
test_endpoint "List" 200 GET "$BASE/tours"
test_endpoint "Create" 201 POST "$BASE/tours" "$GUIDE_TOKEN" '{"title":"Chirripo Summit","description":"Ascenso al pico","category_id":1,"destination_id":1,"meeting_point":"San José","meeting_latitude":9.93,"meeting_longitude":-84.09,"price_per_person":150,"currency":"USD","duration_minutes":480,"max_participants":10,"difficulty":"medium"}'
test_endpoint "Show" 200 GET "$BASE/tours/1"
test_endpoint "Update" 200 PUT "$BASE/tours/1" "$GUIDE_TOKEN" '{"title":"Updated Tour"}'
test_endpoint "Trashed" 200 GET "$BASE/tours/trashed" "$ADMIN_TOKEN"
test_endpoint "No auth list" 200 GET "$BASE/tours"
test_endpoint "Delete" 200 DELETE "$BASE/tours/1" "$GUIDE_TOKEN"
test_endpoint "Restore" 200 POST "$BASE/tours/1/restore" "$GUIDE_TOKEN"

echo "=== TOUR SCHEDULES (5) ==="
test_endpoint "Create" 201 POST "$BASE/tour-schedules" "$GUIDE_TOKEN" '{"tour_id":1,"guide_id":1,"start_datetime":"2026-09-15T08:00:00","end_datetime":"2026-09-16T18:00:00","max_spots":10}'
test_endpoint "List" 200 GET "$BASE/tour-schedules" "$GUIDE_TOKEN"
test_endpoint "Show" 200 GET "$BASE/tour-schedules/1" "$GUIDE_TOKEN"
test_endpoint "Update" 200 PUT "$BASE/tour-schedules/1" "$GUIDE_TOKEN" '{"max_spots":20}'
test_endpoint "Delete" 200 DELETE "$BASE/tour-schedules/1" "$GUIDE_TOKEN"

echo "=== ROUTES (8) ==="
test_endpoint "Create" 201 POST "$BASE/routes" "$GUIDE_TOKEN" '{"name":"Sendero Chirripo","description":"Ruta al pico","difficulty":"hard","total_distance":38,"estimated_duration":48,"destinations":[{"destination_id":1,"order":1}]}'
test_endpoint "List" 200 GET "$BASE/routes"
test_endpoint "Show" 200 GET "$BASE/routes/1"
test_endpoint "Update" 200 PUT "$BASE/routes/1" "$GUIDE_TOKEN" '{"name":"Sendero Updated"}'
test_endpoint "Popular" 200 GET "$BASE/routes/popular"
test_endpoint "Trashed" 200 GET "$BASE/routes/trashed" "$ADMIN_TOKEN"
test_endpoint "Delete" 200 DELETE "$BASE/routes/1" "$GUIDE_TOKEN"
test_endpoint "Restore" 200 POST "$BASE/routes/1/restore" "$GUIDE_TOKEN"

echo "=== REVIEWS (6) ==="
test_endpoint "Create" 201 POST "$BASE/reviews" "$ADMIN_TOKEN" '{"content":"Excelente","rating":5,"reviewable_type":"tour","reviewable_id":1}'
test_endpoint "List" 200 GET "$BASE/reviews"
test_endpoint "Show" 200 GET "$BASE/reviews/1" "$ADMIN_TOKEN"
test_endpoint "Update" 200 PUT "$BASE/reviews/1" "$ADMIN_TOKEN" '{"content":"Muy bueno","rating":4}'
test_endpoint "Delete" 200 DELETE "$BASE/reviews/1" "$ADMIN_TOKEN"
test_endpoint "Restore" 200 POST "$BASE/reviews/1/restore" "$ADMIN_TOKEN"

echo "=== COMMUNITIES (10) ==="
test_endpoint "Create" 201 POST "$BASE/communities" "$ADMIN_TOKEN" '{"name":"Aventureros CR","description":"Comunidad","slug":"aventureros-cr","is_public":true,"category_id":1}'
test_endpoint "List" 200 GET "$BASE/communities"
test_endpoint "Show" 200 GET "$BASE/communities/1"
test_endpoint "Join" 200 POST "$BASE/communities/1/join" "$GUIDE_TOKEN"
test_endpoint "Popular" 200 GET "$BASE/communities/popular"
test_endpoint "Events" 200 GET "$BASE/communities/1/events" "$ADMIN_TOKEN"
test_endpoint "Routes" 200 GET "$BASE/communities/1/routes" "$ADMIN_TOKEN"
test_endpoint "Recommendations" 200 GET "$BASE/communities/recommendations" "$ADMIN_TOKEN"
test_endpoint "Trashed" 404 GET "$BASE/communities/trashed" "$ADMIN_TOKEN"
test_endpoint "Leave" 200 POST "$BASE/communities/1/leave" "$GUIDE_TOKEN"

echo "=== USERS (18) ==="
test_endpoint "List" 200 GET "$BASE/users" "$ADMIN_TOKEN"
test_endpoint "Store" 201 POST "$BASE/users" "$ADMIN_TOKEN" '{"name":"New User","email":"newuser@test.com","password":"password","role":"Traveler"}'
test_endpoint "Show" 200 GET "$BASE/users/2" "$ADMIN_TOKEN"
test_endpoint "No auth list" 401 GET "$BASE/users"
test_endpoint "Update" 200 PUT "$BASE/users/4" "$ADMIN_TOKEN" '{"name":"Updated User"}'
test_endpoint "Communities" 200 GET "$BASE/users/2/communities" "$ADMIN_TOKEN"
test_endpoint "Reviews" 200 GET "$BASE/users/2/reviews" "$ADMIN_TOKEN"
test_endpoint "Event History" 404 GET "$BASE/users/2/event-history" "$ADMIN_TOKEN"
test_endpoint "Favorite Routes" 404 GET "$BASE/users/2/favorite-routes" "$ADMIN_TOKEN"
test_endpoint "Favorite Destinations" 404 GET "$BASE/users/2/favorite-destinations" "$ADMIN_TOKEN"
test_endpoint "Statistics" 200 GET "$BASE/users/2/statistics" "$ADMIN_TOKEN"
test_endpoint "Toggle Fav Dest" 200 POST "$BASE/users/destinations/1/toggle-favorite" "$ADMIN_TOKEN"
test_endpoint "Toggle Fav Route" 200 POST "$BASE/users/routes/1/toggle-favorite" "$ADMIN_TOKEN"
test_endpoint "Update Route Status" 200 POST "$BASE/users/routes/1/update-status" "$ADMIN_TOKEN" '{"status":"completed"}'
test_endpoint "Trashed" 200 GET "$BASE/users/trashed" "$ADMIN_TOKEN"
test_endpoint "Delete" 200 DELETE "$BASE/users/4" "$ADMIN_TOKEN"
test_endpoint "Restore" 200 POST "$BASE/users/4/restore" "$ADMIN_TOKEN"

echo "=== RESERVATIONS (8) ==="
test_endpoint "Create" 201 POST "$BASE/reservations" "$ADMIN_TOKEN" '{"tour_schedule_id":1,"participants":2,"special_requests":"Vegetariano"}'
test_endpoint "List" 200 GET "$BASE/reservations" "$ADMIN_TOKEN"
test_endpoint "Show" 200 GET "$BASE/reservations/1" "$ADMIN_TOKEN"
test_endpoint "Confirm" 200 POST "$BASE/reservations/1/confirm" "$ADMIN_TOKEN"
test_endpoint "Cancel" 200 POST "$BASE/reservations/1/cancel" "$ADMIN_TOKEN"
test_endpoint "Trashed" 200 GET "$BASE/reservations/trashed" "$ADMIN_TOKEN"
test_endpoint "Delete" 200 DELETE "$BASE/reservations/1" "$ADMIN_TOKEN"
test_endpoint "Restore" 200 POST "$BASE/reservations/1/restore" "$ADMIN_TOKEN"

echo "=== GUIDE PROFILES Cleanup ==="
test_endpoint "Destroy" 200 DELETE "$BASE/guide-profiles/1" "$GUIDE_TOKEN"

echo ""
echo "============================="
echo "RESULTS: $PASS passed, $FAIL failed = $((PASS+FAIL)) total"
echo "============================="
