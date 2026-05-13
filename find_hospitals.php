<?php
session_start();
$loggedIn = isset($_SESSION['user']);
$username = $loggedIn ? $_SESSION['user'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Find Hospitals Near You — Blood Donation</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css"/>
<style>
:root {
    --surface:   #ffffff;
    --card:      rgba(255,255,255,0.85);
    --border:    rgba(0,0,0,.08);
    --border-s:  rgba(0,0,0,.15);
    --red:       #DC2626;
    --red-d:     #B91C1C;
    --red-muted: rgba(220,38,38,.08);
    --red-b:     rgba(220,38,38,.22);
    --green:     #16a34a;
    --green-m:   rgba(22,163,74,.08);
    --green-b:   rgba(22,163,74,.22);
    --blue:      #2563EB;
    --blue-m:    rgba(37,99,235,.08);
    --blue-b:    rgba(37,99,235,.22);
    --text:      #1a1a2e;
    --text-2:    rgba(0,0,0,.6);
    --text-3:    rgba(0,0,0,.38);
    --serif:     'Cormorant Garamond', Georgia, serif;
    --sans:      'Outfit', sans-serif;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: var(--sans);
    background: linear-gradient(135deg, #ffffff 0%, #ffe4ec 50%, #ffc0cb 100%);
    background-size: 300% 300%;
    animation: gradMove 12s ease infinite;
    color: var(--text);
    min-height: 100vh;
}
@keyframes gradMove {
    0%   { background-position: 0% 50%; }
    50%  { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
.bg-grid {
    position: fixed; inset: 0; pointer-events: none; z-index: 0;
    background-image:
        linear-gradient(rgba(0,0,0,.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,0,0,.03) 1px, transparent 1px);
    background-size: 72px 72px;
}
.glow-1 {
    position: fixed; top: -300px; right: -200px; z-index: 0;
    width: 800px; height: 800px; border-radius: 50%;
    background: radial-gradient(circle, rgba(255,77,109,.22), transparent 60%);
    pointer-events: none;
}

/* ── NAVBAR ── */
nav {
    position: sticky; top: 0; left: 0; right: 0; z-index: 200;
    height: 68px;
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 48px;
    background: rgba(255,255,255,.82);
    border-bottom: 1px solid var(--border);
    backdrop-filter: blur(16px);
}
.nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
.nav-logo-icon {
    width: 34px; height: 34px; background: var(--red); border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 3px 12px rgba(220,38,38,.32); font-size: 18px;
}
.nav-logo-text { font-family: var(--serif); font-size: 20px; font-weight: 600; color: var(--text); }
.nav-links { display: flex; align-items: center; gap: 6px; }
.nav-link {
    color: var(--text-2); text-decoration: none;
    padding: 7px 16px; border-radius: 8px; font-size: 14px;
    transition: all .18s;
}
.nav-link:hover, .nav-link.active { color: var(--red); background: var(--red-muted); }
.nav-cta {
    background: var(--red); color: #fff;
    padding: 8px 20px; border-radius: 8px; font-size: 14px; font-weight: 500;
    text-decoration: none; transition: all .2s;
    box-shadow: 0 3px 14px rgba(220,38,38,.28);
}
.nav-cta:hover { background: var(--red-d); transform: translateY(-1px); }
.nav-user {
    display: flex; align-items: center; gap: 8px;
    background: var(--red-muted); border: 1px solid var(--red-b);
    color: var(--red); font-size: 13px; padding: 7px 14px; border-radius: 100px;
}
.nav-avatar {
    width: 22px; height: 22px; background: var(--red); border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; color: #fff;
}

/* ── PAGE WRAPPER ── */
.page-wrap {
    position: relative; z-index: 1;
    max-width: 1200px; margin: 0 auto;
    padding: 48px 36px 80px;
}

/* ── PAGE HEADER ── */
.page-header {
    margin-bottom: 36px;
}
.page-header-top {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 20px; flex-wrap: wrap;
}
.page-title { font-family: var(--serif); font-size: clamp(36px, 4.5vw, 58px); font-weight: 600; color: var(--text); line-height: 1.05; }
.page-title em { color: var(--red); font-style: italic; }
.page-subtitle { font-size: 15px; color: var(--text-2); line-height: 1.7; margin-top: 10px; max-width: 520px; }
.sec-divider { width: 48px; height: 3px; background: var(--red); border-radius: 2px; margin: 14px 0 0; }

/* ── LOCATE BUTTON ── */
.locate-btn {
    display: inline-flex; align-items: center; gap: 9px;
    background: var(--red); color: #fff;
    padding: 13px 26px; border-radius: 10px;
    font-size: 14px; font-weight: 600; font-family: var(--sans);
    border: none; cursor: pointer; transition: all .2s;
    box-shadow: 0 4px 16px rgba(220,38,38,.32);
    white-space: nowrap; flex-shrink: 0;
}
.locate-btn:hover { background: var(--red-d); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(220,38,38,.38); }
.locate-btn:disabled { background: #ccc; box-shadow: none; cursor: not-allowed; transform: none; }
.locate-btn svg { flex-shrink: 0; }

/* ── RADIUS CONTROL ── */
.controls-bar {
    display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
    margin-bottom: 28px;
}
.radius-group {
    display: flex; align-items: center; gap: 10px;
    background: var(--card); border: 1px solid var(--border);
    padding: 8px 16px; border-radius: 10px;
    backdrop-filter: blur(10px);
    font-size: 13px; color: var(--text-2);
}
.radius-group label { font-weight: 500; white-space: nowrap; }
.radius-select {
    background: transparent; border: none; outline: none;
    font-family: var(--sans); font-size: 13px; color: var(--red); font-weight: 600;
    cursor: pointer;
}
.status-pill {
    display: inline-flex; align-items: center; gap: 7px;
    background: var(--card); border: 1px solid var(--border);
    padding: 8px 16px; border-radius: 10px;
    font-size: 13px; color: var(--text-3);
    backdrop-filter: blur(10px);
    transition: all .3s;
}
.status-pill.locating { border-color: rgba(234,179,8,.4); background: rgba(234,179,8,.06); color: #92400e; }
.status-pill.success  { border-color: var(--green-b); background: var(--green-m); color: var(--green); }
.status-pill.error    { border-color: var(--red-b); background: var(--red-muted); color: var(--red); }
.pulse { width: 8px; height: 8px; border-radius: 50%; background: currentColor; animation: pulse 1.2s infinite; }
@keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.6)} }

/* ── MAIN LAYOUT ── */
.main-grid {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 20px;
    align-items: start;
}
@media(max-width: 900px) { .main-grid { grid-template-columns: 1fr; } }

/* ── RESULTS LIST ── */
.results-panel {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 18px; overflow: hidden;
    backdrop-filter: blur(16px);
    box-shadow: 0 4px 24px rgba(200,50,80,.07);
}
.results-head {
    padding: 18px 22px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
}
.results-head h2 { font-family: var(--serif); font-size: 20px; color: var(--text); }
.results-count {
    background: var(--red-muted); border: 1px solid var(--red-b);
    color: var(--red); font-size: 11px; font-weight: 700;
    padding: 3px 10px; border-radius: 6px;
}
.results-body { max-height: 560px; overflow-y: auto; }
.results-body::-webkit-scrollbar { width: 4px; }
.results-body::-webkit-scrollbar-thumb { background: var(--border-s); border-radius: 4px; }

/* ── HOSPITAL CARD ── */
.hosp-card {
    padding: 18px 22px; border-bottom: 1px solid rgba(0,0,0,.05);
    cursor: pointer; transition: background .18s;
    display: flex; align-items: flex-start; gap: 14px;
}
.hosp-card:last-child { border-bottom: none; }
.hosp-card:hover, .hosp-card.active { background: var(--red-muted); }
.hosp-rank {
    width: 30px; height: 30px; flex-shrink: 0;
    background: var(--red); color: #fff;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; margin-top: 2px;
}
.hosp-info { flex: 1; min-width: 0; }
.hosp-name { font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 4px; line-height: 1.35; }
.hosp-meta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 8px; }
.hosp-dist {
    display: inline-flex; align-items: center; gap: 4px;
    background: var(--green-m); border: 1px solid var(--green-b);
    color: var(--green); font-size: 11px; font-weight: 700;
    padding: 2px 8px; border-radius: 6px;
}
.hosp-type {
    font-size: 11px; color: var(--text-3);
    background: rgba(0,0,0,.04); padding: 2px 8px; border-radius: 6px;
}
.hosp-addr { font-size: 12px; color: var(--text-3); line-height: 1.5; margin-bottom: 10px; }
.hosp-actions { display: flex; gap: 8px; }
.btn-dir {
    display: inline-flex; align-items: center; gap: 5px;
    background: var(--red); color: #fff;
    padding: 6px 14px; border-radius: 7px;
    font-size: 12px; font-weight: 600; font-family: var(--sans);
    text-decoration: none; transition: all .18s;
    box-shadow: 0 2px 8px rgba(220,38,38,.2);
}
.btn-dir:hover { background: var(--red-d); transform: translateY(-1px); }
.btn-focus {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(255,255,255,.8); border: 1px solid var(--border);
    color: var(--text-2); padding: 6px 14px; border-radius: 7px;
    font-size: 12px; font-weight: 500; font-family: var(--sans);
    cursor: pointer; transition: all .18s;
}
.btn-focus:hover { border-color: var(--red-b); color: var(--red); background: var(--red-muted); }

/* ── EMPTY / LOADING STATE ── */
.state-box {
    padding: 56px 24px; text-align: center;
}
.state-icon { font-size: 48px; margin-bottom: 16px; }
.state-title { font-family: var(--serif); font-size: 22px; color: var(--text); margin-bottom: 8px; }
.state-desc { font-size: 14px; color: var(--text-3); line-height: 1.7; max-width: 260px; margin: 0 auto; }

/* Loading spinner */
.spinner {
    width: 40px; height: 40px; margin: 0 auto 16px;
    border: 3px solid var(--red-muted);
    border-top-color: var(--red);
    border-radius: 50%;
    animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── MAP ── */
.map-panel {
    border-radius: 18px; overflow: hidden;
    border: 1px solid var(--border);
    box-shadow: 0 4px 24px rgba(200,50,80,.07);
    position: sticky; top: 88px;
}
#map {
    height: 620px; width: 100%;
    background: #f5f0eb;
}

/* ── FOOTER ── */
footer {
    text-align: center; padding: 28px 24px;
    font-size: 13px; color: var(--text-3);
    border-top: 1px solid var(--border);
    background: rgba(255,255,255,.5);
    position: relative; z-index: 1;
}

/* ── RESPONSIVE ── */
@media(max-width: 768px) {
    nav { padding: 0 20px; }
    .page-wrap { padding: 32px 20px 60px; }
    .page-title { font-size: 34px; }
    #map { height: 340px; }
    .results-body { max-height: 380px; }
}
</style>
</head>
<body>
<div class="bg-grid"></div>
<div class="glow-1"></div>

<!-- ── NAVBAR ── -->
<nav>
  <a href="index.php" class="nav-logo">
    <div class="nav-logo-icon">🩸</div>
    <span class="nav-logo-text">Blood Donation</span>
  </a>
  <div class="nav-links">
    <a href="index.php" class="nav-link">Home</a>
    <a href="donate_form.php" class="nav-link">Donate</a>
    <a href="request_blood_form.php" class="nav-link">Request Blood</a>
    <a href="find_hospitals.php" class="nav-link active"> Find Hospitals</a>
    <?php if($loggedIn): ?>
      <span class="nav-user">
        <div class="nav-avatar"><?= strtoupper(substr($username, 0, 1)) ?></div>
        <?= htmlspecialchars($username) ?>
      </span>
      <a href="<?= $loggedIn ? 'user_dashboard.php' : '' ?>" class="nav-link">Dashboard</a>
      <a href="user_logout.php" class="nav-link">Log Out</a>
    <?php else: ?>
      <a href="user_login.php" class="nav-link">Log In</a>
      <a href="register1.php" class="nav-cta">Register</a>
    <?php endif; ?>
  </div>
</nav>

<!-- ── PAGE ── -->
<div class="page-wrap">

  <!-- Header -->
  <div class="page-header">
    <div class="page-header-top">
      <div>
        <h1 class="page-title">Hospitals <em>Near You</em></h1>
        <p class="page-subtitle">Find blood banks and donation centers closest to your current location. Click the button and allow location access to get started.</p>
        <div class="sec-divider"></div>
      </div>
      <button class="locate-btn" id="locateBtn" onclick="locateMe()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/>
          <path d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2z" stroke-opacity=".3"/>
        </svg>
        Use My Location
      </button>
    </div>
  </div>

  <!-- Controls -->
  <div class="controls-bar">
    <div class="radius-group">
      <label for="radiusSelect">Search radius:</label>
      <select class="radius-select" id="radiusSelect" onchange="if(userLat) searchHospitals(userLat, userLng)">
        <option value="2000">2 km</option>
        <option value="5000" selected>5 km</option>
        <option value="10000">10 km</option>
        <option value="20000">20 km</option>
      </select>
    </div>
    <div class="status-pill" id="statusPill">
      <span> Click "Use My Location" to begin</span>
    </div>
  </div>

  <!-- Main Grid -->
  <div class="main-grid">

    <!-- Results Panel -->
    <div class="results-panel">
      <div class="results-head">
        <h2>Nearby Hospitals</h2>
        <span class="results-count" id="resultsCount">0 found</span>
      </div>
      <div class="results-body" id="resultsList">
        <div class="state-box">
          <div class="state-icon"></div>
          <div class="state-title">Ready to Search</div>
          <div class="state-desc">Allow location access to find hospitals and blood centers near you.</div>
        </div>
      </div>
    </div>

    <!-- Map Panel -->
    <div class="map-panel">
      <div id="map"></div>
    </div>

  </div>
</div>

<!-- Footer -->
<footer>
  <span>Blood Donation</span> · · &copy; <?= date('Y') ?>
</footer>

<!-- Leaflet JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
// ── STATE ──
let map, userMarker, userCircle;
let hospitalMarkers = [];
let userLat = null, userLng = null;

// ── INIT MAP (Philippines default center) ──
map = L.map('map', { zoomControl: true }).setView([12.8797, 121.7740], 6);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
  maxZoom: 19
}).addTo(map);

// Custom red marker icon
function makeIcon(color = '#DC2626', label = '') {
  return L.divIcon({
    className: '',
    html: `<div style="
      width:34px;height:34px;background:${color};border-radius:50% 50% 50% 0;
      transform:rotate(-45deg);border:3px solid #fff;
      box-shadow:0 3px 12px rgba(0,0,0,.25);
      display:flex;align-items:center;justify-content:center;
    ">
      <span style="transform:rotate(45deg);font-size:12px;font-weight:700;color:#fff">${label}</span>
    </div>`,
    iconSize: [34, 34],
    iconAnchor: [17, 34],
    popupAnchor: [0, -36]
  });
}

function userIcon() {
  return L.divIcon({
    className: '',
    html: `<div style="
      width:18px;height:18px;background:#2563EB;border-radius:50%;
      border:3px solid #fff;box-shadow:0 0 0 4px rgba(37,99,235,.25);
    "></div>`,
    iconSize: [18, 18],
    iconAnchor: [9, 9]
  });
}

// ── SET STATUS ──
function setStatus(msg, type = '') {
  const el = document.getElementById('statusPill');
  el.className = 'status-pill' + (type ? ' ' + type : '');
  el.innerHTML = type === 'locating'
    ? `<span class="pulse"></span>${msg}`
    : msg;
}

// ── LOCATE ME ──
function locateMe() {
  if (!navigator.geolocation) {
    setStatus('⚠️ Geolocation not supported by your browser.', 'error');
    return;
  }
  const btn = document.getElementById('locateBtn');
  btn.disabled = true;
  btn.innerHTML = `
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" style="animation:spin .7s linear infinite">
      <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" stroke-opacity=".3"/>
      <path d="M21 12a9 9 0 0 0-9-9"/>
    </svg>
    Locating…`;
  setStatus('Getting your location…', 'locating');

  navigator.geolocation.getCurrentPosition(
    pos => {
      userLat = pos.coords.latitude;
      userLng = pos.coords.longitude;
      btn.disabled = false;
      btn.innerHTML = `
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/>
        </svg>
        Refresh Location`;
      setStatus(' Location found! Searching for hospitals…', 'success');
      placeUserMarker(userLat, userLng);
      searchHospitals(userLat, userLng);
    },
    err => {
      btn.disabled = false;
      btn.innerHTML = `
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/>
        </svg>
        Try Again`;
      const msgs = {
        1: '🔒 Location access denied. Please allow location in your browser.',
        2: '📡 Location unavailable. Check your device settings.',
        3: '⏱ Location request timed out. Please try again.'
      };
      setStatus(msgs[err.code] || '⚠️ Could not get location.', 'error');
      showErrorState();
    },
    { enableHighAccuracy: true, timeout: 12000, maximumAge: 60000 }
  );
}

// ── PLACE USER PIN ──
function placeUserMarker(lat, lng) {
  if (userMarker) map.removeLayer(userMarker);
  if (userCircle) map.removeLayer(userCircle);
  const radius = parseInt(document.getElementById('radiusSelect').value);
  userMarker = L.marker([lat, lng], { icon: userIcon(), zIndexOffset: 9999 })
    .bindPopup('<strong>📍 Your Location</strong>')
    .addTo(map);
  userCircle = L.circle([lat, lng], {
    radius,
    color: '#2563EB', fillColor: '#2563EB',
    fillOpacity: 0.06, weight: 1.5, dashArray: '6 4'
  }).addTo(map);
}

// ── SEARCH VIA OVERPASS API ──
function searchHospitals(lat, lng) {
  const radius = parseInt(document.getElementById('radiusSelect').value);

  // Update circle radius
  if (userCircle) userCircle.setRadius(radius);

  showLoadingState();

  // Clear old markers
  hospitalMarkers.forEach(m => map.removeLayer(m));
  hospitalMarkers = [];

  // Overpass query: hospitals + blood banks within radius
  const query = `
    [out:json][timeout:20];
    (
      node["amenity"="hospital"](around:${radius},${lat},${lng});
      way["amenity"="hospital"](around:${radius},${lat},${lng});
      node["amenity"="blood_bank"](around:${radius},${lat},${lng});
      node["healthcare"="hospital"](around:${radius},${lat},${lng});
      node["healthcare"="blood_bank"](around:${radius},${lat},${lng});
    );
    out center;
  `.trim();

  fetch('https://overpass-api.de/api/interpreter', {
    method: 'POST',
    body: 'data=' + encodeURIComponent(query)
  })
  .then(r => r.json())
  .then(data => {
    const elements = data.elements || [];
    if (elements.length === 0) {
      showNoResultsState(radius);
      document.getElementById('resultsCount').textContent = '0 found';
      // Zoom to user location anyway
      map.setView([lat, lng], 14);
      return;
    }

    // Compute distances and sort
    const hospitals = elements.map(el => {
      const elLat = el.lat ?? el.center?.lat;
      const elLng = el.lon ?? el.center?.lon;
      const dist  = haversine(lat, lng, elLat, elLng);
      return { ...el, elLat, elLng, dist };
    }).sort((a, b) => a.dist - b.dist);

    document.getElementById('resultsCount').textContent = hospitals.length + ' found';
    setStatus(` Found ${hospitals.length} hospital${hospitals.length !== 1 ? 's' : ''} within ${radius/1000} km`, 'success');

    renderResults(hospitals, lat, lng);
    renderMarkers(hospitals);

    // Fit map to bounds
    const allPoints = [[lat, lng], ...hospitals.map(h => [h.elLat, h.elLng])];
    map.fitBounds(L.latLngBounds(allPoints), { padding: [40, 40] });
  })
  .catch(() => {
    setStatus('⚠️ Could not reach map service. Check your connection.', 'error');
    showErrorState();
  });
}

// ── RENDER RESULT LIST ──
function renderResults(hospitals, userLat, userLng) {
  const list = document.getElementById('resultsList');
  if (hospitals.length === 0) { showNoResultsState(); return; }

  list.innerHTML = hospitals.map((h, i) => {
    const name    = h.tags?.name || h.tags?.['name:en'] || 'Unnamed Hospital';
    const addr    = buildAddress(h.tags);
    const distStr = h.dist < 1 ? (h.dist * 1000).toFixed(0) + ' m' : h.dist.toFixed(1) + ' km';
    const type    = h.tags?.amenity === 'blood_bank' || h.tags?.healthcare === 'blood_bank'
                    ? '🩸 Blood Bank' : ' Hospital';
    const mapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${h.elLat},${h.elLng}`;

    return `
      <div class="hosp-card" id="card-${i}" onclick="focusHospital(${i}, ${h.elLat}, ${h.elLng})">
        <div class="hosp-rank">${i + 1}</div>
        <div class="hosp-info">
          <div class="hosp-name">${escHtml(name)}</div>
          <div class="hosp-meta">
            <span class="hosp-dist">📍 ${distStr}</span>
            <span class="hosp-type">${type}</span>
          </div>
          ${addr ? `<div class="hosp-addr">${escHtml(addr)}</div>` : ''}
          <div class="hosp-actions">
            <a class="btn-dir" href="${mapsUrl}" target="_blank" rel="noopener" onclick="event.stopPropagation()">
              ↗ Get Directions
            </a>
            <button class="btn-focus" onclick="event.stopPropagation(); focusHospital(${i}, ${h.elLat}, ${h.elLng})">
               View on Map
            </button>
          </div>
        </div>
      </div>
    `;
  }).join('');
}

// ── RENDER MAP MARKERS ──
function renderMarkers(hospitals) {
  hospitals.forEach((h, i) => {
    const name    = h.tags?.name || 'Unnamed Hospital';
    const distStr = h.dist < 1 ? (h.dist * 1000).toFixed(0) + ' m' : h.dist.toFixed(1) + ' km';
    const isBlood = h.tags?.amenity === 'blood_bank' || h.tags?.healthcare === 'blood_bank';
    const color   = isBlood ? '#7C3AED' : '#DC2626';
    const mapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${h.elLat},${h.elLng}`;

    const marker = L.marker([h.elLat, h.elLng], { icon: makeIcon(color, i + 1) })
      .bindPopup(`
        <div style="font-family:'Outfit',sans-serif;min-width:200px">
          <strong style="font-size:14px;color:#1a1a2e">${escHtml(name)}</strong><br>
          <span style="font-size:12px;color:#6b7280;margin-top:4px;display:block">${isBlood ? '🩸 Blood Bank' : ' Hospital'} · ${distStr} away</span>
          <a href="${mapsUrl}" target="_blank" rel="noopener"
             style="display:inline-block;margin-top:10px;background:#DC2626;color:#fff;
                    padding:5px 14px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none">
            ↗ Get Directions
          </a>
        </div>
      `)
      .addTo(map);

    marker.on('click', () => highlightCard(i));
    hospitalMarkers.push(marker);
  });
}

// ── FOCUS ON HOSPITAL ──
function focusHospital(i, lat, lng) {
  map.setView([lat, lng], 16);
  hospitalMarkers[i]?.openPopup();
  highlightCard(i);
}

function highlightCard(i) {
  document.querySelectorAll('.hosp-card').forEach(c => c.classList.remove('active'));
  const card = document.getElementById('card-' + i);
  if (card) {
    card.classList.add('active');
    card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }
}

// ── STATES ──
function showLoadingState() {
  document.getElementById('resultsList').innerHTML = `
    <div class="state-box">
      <div class="spinner"></div>
      <div class="state-title">Searching…</div>
      <div class="state-desc">Looking for hospitals and blood centers near you.</div>
    </div>`;
}
function showNoResultsState(radius) {
  const km = radius ? radius / 1000 : '?';
  document.getElementById('resultsList').innerHTML = `
    <div class="state-box">
      <div class="state-icon">🔍</div>
      <div class="state-title">None Found Nearby</div>
      <div class="state-desc">No hospitals found within ${km} km. Try increasing the search radius.</div>
    </div>`;
}
function showErrorState() {
  document.getElementById('resultsList').innerHTML = `
    <div class="state-box">
      <div class="state-icon">⚠️</div>
      <div class="state-title">Something Went Wrong</div>
      <div class="state-desc">Could not load hospitals. Please try again.</div>
    </div>`;
}

// ── HELPERS ──
function haversine(lat1, lng1, lat2, lng2) {
  const R = 6371;
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLng = (lng2 - lng1) * Math.PI / 180;
  const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLng/2)**2;
  return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}
function buildAddress(tags) {
  if (!tags) return '';
  const parts = [tags['addr:housenumber'], tags['addr:street'], tags['addr:city'] || tags['addr:municipality']].filter(Boolean);
  return parts.join(', ') || tags['addr:full'] || '';
}
function escHtml(str) {
  const d = document.createElement('div');
  d.textContent = str;
  return d.innerHTML;
}
</script>
</body>
</html>