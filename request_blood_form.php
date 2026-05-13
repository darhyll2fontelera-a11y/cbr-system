<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

$user_id    = $_SESSION['user_id'];
$showTicket = false;
$error      = "";

if (isset($_POST['donate'])) {
    $name      = $_POST['fullname'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone'];
    $address   = $_POST['address'];
    $birthdate = $_POST['birthdate'];
    $blood     = $_POST['blood'];
    $type      = 'request';
    $ticket_id = 'BR-' . rand(10000, 99999);
    $hospital  = trim($_POST['hospital'] ?? 'Quiapo General Hospital');
    if (empty($hospital)) $hospital = 'Quiapo General Hospital';

    $stmt = $conn->prepare(
        "INSERT INTO donations (user_id, fullname, email, phone, address, birthdate, blood_type, ticket_id, hospital, type)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    if ($stmt) {
        $stmt->bind_param("isssssssss", $user_id, $name, $email, $phone, $address, $birthdate, $blood, $ticket_id, $hospital, $type);
        if ($stmt->execute()) {
            $showTicket = true;
        } else {
            $error = "Insert failed. Try again.";
        }
    } else {
        $error = "Preparation failed.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Request Blood — Blood Donation</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#fff;
  --card:rgba(255,255,255,0.88);
  --border:rgba(0,0,0,.09);
  --border-s:rgba(0,0,0,.14);
  --blue:#2563EB;--blue-d:#1D4ED8;
  --blue-m:rgba(37,99,235,.08);
  --blue-b:rgba(37,99,235,.22);
  --t:#1a1a2e;
  --t2:rgba(0,0,0,.62);
  --t3:rgba(0,0,0,.4);
  --serif:'Cormorant Garamond',Georgia,serif;
  --sans:'Outfit',sans-serif
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{
  font-family:var(--sans);
  background:linear-gradient(135deg,#ffffff 0%,#dbeafe 50%,#bfdbfe 100%);
  background-size:300% 300%;
  animation:gradMove 12s ease infinite;
  color:var(--t);min-height:100vh;display:flex;flex-direction:column
}
@keyframes gradMove{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
.bg-grid{position:fixed;inset:0;z-index:0;pointer-events:none;background-image:linear-gradient(rgba(0,0,0,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(0,0,0,.03) 1px,transparent 1px);background-size:72px 72px}
.g1{position:fixed;top:-200px;right:-100px;z-index:0;width:700px;height:700px;border-radius:50%;background:radial-gradient(circle,rgba(37,99,235,.15) 0%,transparent 60%);pointer-events:none}
.g2{position:fixed;bottom:-200px;left:-100px;z-index:0;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(96,165,250,.12) 0%,transparent 60%);pointer-events:none}
nav{position:relative;z-index:10;height:66px;display:flex;align-items:center;justify-content:space-between;padding:0 40px;border-bottom:1px solid var(--border);background:rgba(255,255,255,.75);backdrop-filter:blur(16px)}
.nlogo{display:flex;align-items:center;gap:11px;text-decoration:none}
.nicon{width:32px;height:32px;background:var(--blue);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;box-shadow:0 3px 12px rgba(37,99,235,.35)}
.ntext{font-family:var(--serif);font-size:19px;color:var(--t)}
.nlink{font-size:14px;color:var(--t2);text-decoration:none;padding:6px 14px;border-radius:7px;transition:all .18s}
.nlink:hover{color:var(--blue);background:rgba(37,99,235,.07)}
.nnav{display:flex;align-items:center;gap:4px}
.pw{position:relative;z-index:1;flex:1;display:flex;align-items:flex-start;justify-content:center;padding:48px 24px}
.split{display:flex;gap:60px;align-items:flex-start;max-width:1060px;width:100%}
.sl{flex:1;padding-top:16px;position:sticky;top:32px}
.sr{flex-shrink:0;width:440px}
.pill{display:inline-flex;align-items:center;gap:7px;background:var(--blue-m);border:1px solid var(--blue-b);color:var(--blue);font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;padding:5px 13px;border-radius:100px;margin-bottom:22px}
.pdot{width:5px;height:5px;background:var(--blue);border-radius:50%;animation:pdot 2.5s ease-in-out infinite}
@keyframes pdot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.3;transform:scale(.6)}}
.sl h1{font-family:var(--serif);font-size:clamp(34px,4vw,58px);font-weight:400;line-height:1.05;color:var(--t);margin-bottom:16px}
.sl h1 em{font-style:italic;color:var(--blue);display:block}
.sl p{font-size:15px;font-weight:300;color:var(--t2);line-height:1.8;margin-bottom:28px}
.info-cards{display:flex;flex-direction:column;gap:10px}
.ic{display:flex;align-items:center;gap:14px;background:rgba(255,255,255,.6);border:1px solid var(--border);border-radius:12px;padding:14px 16px;backdrop-filter:blur(8px)}
.ic-icon{width:36px;height:36px;background:var(--blue-m);border:1px solid var(--blue-b);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0}
.ic-text strong{font-size:13px;font-weight:500;color:var(--t);display:block}
.ic-text span{font-size:12px;color:var(--t3)}
.card{background:var(--card);border:1px solid var(--border);border-radius:18px;overflow:hidden;box-shadow:0 20px 60px rgba(37,99,235,.08),0 2px 12px rgba(0,0,0,.06);animation:cin .45s ease both;backdrop-filter:blur(20px)}
@keyframes cin{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
.card-head{padding:26px 30px;border-bottom:1px solid var(--border)}
.ctitle{font-family:var(--serif);font-size:24px;color:var(--t);margin-bottom:3px}
.csub{font-size:13px;font-weight:300;color:var(--t3)}
.card-body{padding:28px 30px}
.sect-label{font-size:10px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:var(--t3);margin-bottom:14px;margin-top:20px}
.sect-label:first-child{margin-top:0}
.fgrid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.field{margin-bottom:0}
.field.full{grid-column:span 2}
.field label{display:block;font-size:11px;font-weight:600;color:var(--t3);letter-spacing:.8px;text-transform:uppercase;margin-bottom:6px}
.field input,.field select,.field textarea{width:100%;padding:11px 14px;background:rgba(255,255,255,.7);border:1px solid var(--border-s);border-radius:9px;color:var(--t);font-family:var(--sans);font-size:14px;outline:none;transition:all .2s;resize:vertical}
.field input:focus,.field select:focus,.field textarea:focus{border-color:rgba(37,99,235,.5);background:#fff;box-shadow:0 0 0 3px rgba(37,99,235,.08)}
.field input::placeholder,.field textarea::placeholder{color:rgba(0,0,0,.28)}
.field select option{background:#fff;color:var(--t)}
.aerr{padding:11px 15px;border-radius:9px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px;background:rgba(37,99,235,.07);border:1px solid rgba(37,99,235,.2);color:var(--blue)}
.sbtn{width:100%;padding:13px;background:var(--blue);color:#fff;border:none;border-radius:9px;font-family:var(--sans);font-size:15px;font-weight:500;cursor:pointer;transition:all .2s;margin-top:6px;box-shadow:0 4px 18px rgba(37,99,235,.28);letter-spacing:.2px}
.sbtn:hover{background:var(--blue-d);transform:translateY(-1px);box-shadow:0 7px 24px rgba(37,99,235,.38)}
.back{display:block;text-align:center;margin-top:16px;font-size:13px;color:var(--t3);text-decoration:none;transition:color .18s}
.back:hover{color:var(--blue)}
.ticket{padding:28px 30px}
.tick-top{text-align:center;padding-bottom:20px;border-bottom:1px dashed rgba(0,0,0,.12)}
.tick-icon{font-size:40px;display:block;margin-bottom:10px}
.tick-title{font-family:var(--serif);font-size:24px;color:var(--t)}
.tick-sub{font-size:13px;color:var(--t3);margin-top:4px}
.type-badge{display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;letter-spacing:.5px;margin-top:10px;background:var(--blue-m);border:1px solid var(--blue-b);color:var(--blue)}
.tick-rows{padding:16px 0}
.tr{display:flex;justify-content:space-between;align-items:flex-start;padding:8px 0;border-bottom:1px solid rgba(0,0,0,.05);font-size:14px}
.tr:last-child{border-bottom:none}
.tlbl{color:var(--t3);font-size:12px;flex-shrink:0;margin-right:16px}
.tval{color:var(--t);font-weight:500;text-align:right}
.tid-val{font-family:monospace;font-size:16px;color:var(--blue);font-weight:700;letter-spacing:.5px}
.tick-hosp{text-align:center;padding:16px 0 0;border-top:1px dashed rgba(0,0,0,.12)}
.th-label{font-size:11px;color:var(--t3);text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px}
.th-name{font-family:var(--serif);font-size:20px;color:var(--blue)}
.th-note{font-size:12px;color:var(--t3);margin-top:4px}
.tdone{display:block;text-align:center;padding:12px;background:var(--blue);color:#fff;border-radius:9px;text-decoration:none;font-size:14px;font-weight:500;margin-top:20px;transition:all .2s}
.tdone:hover{background:var(--blue-d)}
/* HOSPITAL PICKER */
.hosp-picker { margin-top: 4px; }
.hosp-locate-btn {
  width: 100%; padding: 11px 14px; margin-bottom: 10px;
  background: var(--blue-m); border: 1.5px dashed rgba(37,99,235,.4);
  border-radius: 9px; color: var(--blue); font-family: var(--sans);
  font-size: 13px; font-weight: 600; cursor: pointer; transition: all .2s;
  display: flex; align-items: center; justify-content: center; gap: 8px;
}
.hosp-locate-btn:hover { background: rgba(37,99,235,.14); border-style: solid; }
.hosp-locate-btn:disabled { opacity: .5; cursor: not-allowed; }
.hosp-status {
  font-size: 12px; padding: 7px 12px; border-radius: 7px; margin-bottom: 8px; display: none;
}
.hosp-status.loading { display:block; background:rgba(217,119,6,.08); border:1px solid rgba(217,119,6,.25); color:#92400e; }
.hosp-status.error   { display:block; background:rgba(37,99,235,.07); border:1px solid rgba(37,99,235,.2); color:var(--blue); }
.hosp-list {
  border: 1px solid var(--border-s); border-radius: 9px; overflow: hidden;
  margin-bottom: 10px; max-height: 220px; overflow-y: auto;
}
.hosp-list::-webkit-scrollbar { width: 4px; }
.hosp-list::-webkit-scrollbar-thumb { background: var(--border-s); border-radius: 4px; }
.hosp-option {
  padding: 11px 14px; cursor: pointer; border-bottom: 1px solid rgba(0,0,0,.05);
  transition: background .15s; font-size: 13px;
}
.hosp-option:last-child { border-bottom: none; }
.hosp-option:hover { background: var(--blue-m); }
.hosp-option.selected { background: var(--blue-m); border-left: 3px solid var(--blue); }
.hosp-option-name { font-weight: 500; color: var(--t); margin-bottom: 2px; }
.hosp-option-meta { font-size: 11px; color: var(--t3); display: flex; gap: 8px; }
.hosp-dist-badge {
  display: inline-block; background: rgba(22,163,74,.1); border: 1px solid rgba(22,163,74,.25);
  color: #16a34a; font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 5px;
}
.hosp-selected-box {
  display: none; align-items: center; gap: 10px; padding: 11px 14px;
  background: rgba(22,163,74,.06); border: 1px solid rgba(22,163,74,.3);
  border-radius: 9px; margin-bottom: 8px;
}
.hosp-selected-box.show { display: flex; }
.hosp-selected-name { font-size: 13px; font-weight: 600; color: #15803d; flex: 1; }
.hosp-clear { background: none; border: none; color: var(--t3); cursor: pointer; font-size: 16px; padding: 0 4px; transition: color .15s; }
.hosp-clear:hover { color: var(--blue); }
.hosp-divider { display: flex; align-items: center; gap: 10px; margin: 10px 0 8px; }
.hosp-divider span { font-size: 11px; color: var(--t3); white-space: nowrap; }
.hosp-divider::before,.hosp-divider::after { content:''; flex:1; height:1px; background:var(--border); }
.hosp-manual input {
  width:100%; padding:11px 14px; background:rgba(255,255,255,.7);
  border:1px solid var(--border-s); border-radius:9px; color:var(--t);
  font-family:var(--sans); font-size:14px; outline:none; transition:all .2s;
}
.hosp-manual input:focus { border-color:rgba(37,99,235,.5); background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.08); }
.hosp-manual input::placeholder { color:rgba(0,0,0,.28); }
@media(max-width:860px){.split{flex-direction:column}.sl{position:static;display:none}.sr{width:100%;max-width:480px}.fgrid{grid-template-columns:1fr}.field.full{grid-column:span 1}nav{padding:0 20px}}
</style>
</head>
<body>
<div class="bg-grid"></div><div class="g1"></div><div class="g2"></div>
<nav>
    <a class="nlogo" href="user_dashboard.php"><div class="nicon">🆘</div><span class="ntext">Blood Donation</span></a>
    <div class="nnav">
        <a href="donate_form.php" class="nlink">🩸 Donate Blood</a>
        <a href="my_history.php" class="nlink">📋 My History</a>
        <a href="user_dashboard.php" class="nlink">← Dashboard</a>
    </div>
</nav>
<div class="pw">
<div class="split">
<div class="sl">
    <div class="pill"><span class="pdot"></span>Blood Request</div>
    <h1>Request<br><em>Blood</em></h1>
    <p>Complete the form to submit a blood request. A ticket will be generated for you to present at the hospital.</p>
    <div class="info-cards">
        <div class="ic"><div class="ic-icon">🏥</div><div class="ic-text"><strong>Quiapo General Hospital</strong><span>Your blood request center</span></div></div>
        <div class="ic"><div class="ic-icon">🎫</div><div class="ic-text"><strong>Instant Ticket</strong><span>Generated immediately after submission</span></div></div>
        <div class="ic"><div class="ic-icon">🆘</div><div class="ic-text"><strong>Urgent Requests Welcome</strong><span>We process requests as quickly as possible</span></div></div>
    </div>
</div>
<div class="sr">
<div class="card">

<?php if(!$showTicket):?>
<div class="card-head">
  <div class="ctitle">🆘 Request Details</div>
  <div class="csub">All fields are required</div>
</div>
<div class="card-body">
    <?php if($error):?><div class="aerr">⚠ <?=htmlspecialchars($error)?></div><?php endif;?>
    <form method="POST">
        <div class="sect-label">Personal Information</div>
        <div class="fgrid">
            <div class="field full"><label>Full Name</label><input type="text" name="fullname" placeholder="Your full name" required></div>
            <div class="field"><label>Email</label><input type="email" name="email" placeholder="Email address" required></div>
            <div class="field"><label>Phone</label><input type="text" name="phone" placeholder="Phone number" required></div>
            <div class="field full"><label>Date of Birth</label><input type="date" name="birthdate" required></div>
            <div class="field full"><label>Permanent Address</label><textarea name="address" placeholder="Your full address" rows="2" required></textarea></div>
        </div>
        <div class="sect-label">Blood Details</div>
        <div class="fgrid">
            <div class="field full"><label>Blood Type Needed</label>
                <select name="blood" required>
                    <option value="">Select blood type</option>
                    <option>O+</option><option>O-</option><option>A+</option><option>A-</option>
                    <option>B+</option><option>B-</option><option>AB+</option><option>AB-</option>
                </select>
            </div>
        </div>
        <div class="sect-label">Hospital Location</div>
        <input type="hidden" name="hospital" id="hospitalInput" value="">
        <div class="hosp-picker">
          <div class="hosp-selected-box" id="hospSelectedBox">
            <span>🏥</span>
            <span class="hosp-selected-name" id="hospSelectedName"></span>
            <button type="button" class="hosp-clear" onclick="clearHospital()" title="Clear">✕</button>
          </div>
          <button type="button" class="hosp-locate-btn" id="hospLocateBtn" onclick="findHospitals()">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg>
            Find Hospitals Near Me
          </button>
          <div class="hosp-status" id="hospStatus"></div>
          <div class="hosp-list" id="hospList" style="display:none"></div>
          <div class="hosp-divider"><span>or type manually</span></div>
          <div class="hosp-manual">
            <input type="text" id="manualHospital" placeholder="e.g. Quiapo General Hospital" oninput="setManualHospital(this.value)">
          </div>
        </div>

        <button class="sbtn" name="donate" style="margin-top:20px" onclick="return validateHospital()">Submit Request →</button>
    </form>
    <a href="user_dashboard.php" class="back">← Back to Dashboard</a>
</div>

<?php else:?>
<div class="ticket">
    <div class="tick-top">
        <span class="tick-icon">🆘</span>
        <div class="tick-title">Blood Request Submitted</div>
        <div class="tick-sub">Keep this ticket — present it upon arrival</div>
        <span class="type-badge">🆘 Blood Request</span>
    </div>
    <div class="tick-rows">
        <div class="tr"><span class="tlbl">Name</span><span class="tval"><?=htmlspecialchars($name)?></span></div>
        <div class="tr"><span class="tlbl">Email</span><span class="tval"><?=htmlspecialchars($email)?></span></div>
        <div class="tr"><span class="tlbl">Phone</span><span class="tval"><?=htmlspecialchars($phone)?></span></div>
        <div class="tr"><span class="tlbl">Date of Birth</span><span class="tval"><?=htmlspecialchars($birthdate)?></span></div>
        <div class="tr"><span class="tlbl">Address</span><span class="tval" style="max-width:220px"><?=htmlspecialchars($address)?></span></div>
        <div class="tr"><span class="tlbl">Blood Type Needed</span><span class="tval"><?=htmlspecialchars($blood)?></span></div>
        <div class="tr"><span class="tlbl">Ticket ID</span><span class="tid-val"><?=$ticket_id?></span></div>
    </div>
    <div class="tick-hosp">
        <div class="th-label">Report to</div>
        <div class="th-name"><?=$hospital?></div>
        <div class="th-note">Show this screen or print upon arrival</div>
        <a href="user_dashboard.php" class="tdone">Done — Back to Dashboard</a>
    </div>
</div>
<?php endif;?>

</div>
</div>
</div>
</div>

<script>
function findHospitals() {
  if (!navigator.geolocation) { showHospStatus('⚠️ Geolocation not supported.', 'error'); return; }
  const btn = document.getElementById('hospLocateBtn');
  btn.disabled = true; btn.textContent = '⏳ Getting location…';
  showHospStatus('📡 Detecting your location…', 'loading');

  navigator.geolocation.getCurrentPosition(pos => {
    showHospStatus('🔍 Searching for nearby hospitals…', 'loading');
    const lat = pos.coords.latitude, lng = pos.coords.longitude;
    const query = `[out:json][timeout:20];(node["amenity"="hospital"](around:8000,${lat},${lng});way["amenity"="hospital"](around:8000,${lat},${lng});node["amenity"="blood_bank"](around:8000,${lat},${lng});node["healthcare"="hospital"](around:8000,${lat},${lng}););out center;`;
    fetch('https://overpass-api.de/api/interpreter', { method:'POST', body:'data='+encodeURIComponent(query) })
      .then(r => r.json())
      .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg> Find Hospitals Near Me';
        const els = (data.elements||[]).map(el => {
          const elLat=el.lat??el.center?.lat, elLng=el.lon??el.center?.lon;
          return { ...el, dist: haversineKm(lat,lng,elLat,elLng) };
        }).sort((a,b)=>a.dist-b.dist).slice(0,12);
        if (!els.length) { showHospStatus('No hospitals found within 8 km. Try typing manually.','error'); return; }
        renderHospList(els);
        document.getElementById('hospStatus').style.display = 'none';
      })
      .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg> Find Hospitals Near Me';
        showHospStatus('⚠️ Could not reach map service. Type hospital name manually.','error');
      });
  }, err => {
    btn.disabled = false;
    btn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg> Find Hospitals Near Me';
    const msgs={1:'🔒 Location denied. Please allow or type manually.',2:'📡 Unavailable.',3:'⏱ Timed out.'};
    showHospStatus(msgs[err.code]||'⚠️ Could not get location.','error');
  }, { enableHighAccuracy:true, timeout:12000, maximumAge:60000 });
}

function renderHospList(hospitals) {
  const list = document.getElementById('hospList');
  list.innerHTML = hospitals.map(h => {
    const name = h.tags?.name || h.tags?.['name:en'] || 'Unnamed Hospital';
    const dist = h.dist < 1 ? (h.dist*1000).toFixed(0)+' m' : h.dist.toFixed(1)+' km';
    const type = (h.tags?.amenity==='blood_bank'||h.tags?.healthcare==='blood_bank') ? '🩸 Blood Bank' : '🏥 Hospital';
    return `<div class="hosp-option" onclick="selectHospital(this,'${name.replace(/'/g,"\\'")}')">
      <div class="hosp-option-name">${escH(name)}</div>
      <div class="hosp-option-meta"><span class="hosp-dist-badge">📍 ${dist}</span><span>${type}</span></div>
    </div>`;
  }).join('');
  list.style.display = 'block';
}

function selectHospital(el, name) {
  document.querySelectorAll('.hosp-option').forEach(o => o.classList.remove('selected'));
  el.classList.add('selected');
  document.getElementById('hospitalInput').value = name;
  document.getElementById('hospSelectedName').textContent = name;
  document.getElementById('hospSelectedBox').classList.add('show');
  document.getElementById('manualHospital').value = name;
  document.getElementById('hospList').style.display = 'none';
  document.getElementById('hospStatus').style.display = 'none';
}

function clearHospital() {
  document.getElementById('hospitalInput').value = '';
  document.getElementById('hospSelectedBox').classList.remove('show');
  document.getElementById('manualHospital').value = '';
}

function setManualHospital(val) {
  document.getElementById('hospitalInput').value = val;
  if (val.trim()) {
    document.getElementById('hospSelectedName').textContent = val;
    document.getElementById('hospSelectedBox').classList.add('show');
  } else {
    document.getElementById('hospSelectedBox').classList.remove('show');
  }
}

function validateHospital() {
  const val = document.getElementById('hospitalInput').value.trim();
  if (!val) {
    showHospStatus('⚠️ Please select or type a hospital before submitting.', 'error');
    document.getElementById('hospLocateBtn').scrollIntoView({ behavior:'smooth', block:'center' });
    return false;
  }
  return true;
}

function showHospStatus(msg, type) {
  const el = document.getElementById('hospStatus');
  el.className = 'hosp-status ' + type; el.textContent = msg;
}

function haversineKm(lat1,lng1,lat2,lng2) {
  const R=6371,dLat=(lat2-lat1)*Math.PI/180,dLng=(lng2-lng1)*Math.PI/180;
  const a=Math.sin(dLat/2)**2+Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLng/2)**2;
  return R*2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));
}
function escH(s){const d=document.createElement('div');d.textContent=s;return d.innerHTML;}
</script>
</body>
</html>