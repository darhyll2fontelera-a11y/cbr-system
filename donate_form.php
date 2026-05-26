<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

$user_id    = $_SESSION['user_id'];
$showTicket = false;
$error      = "";

if (isset($_POST['donate'])) {
    $name      = trim($_POST['fullname']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $address   = trim($_POST['address']);
    $birthdate = $_POST['birthdate'];
    $blood     = $_POST['blood'];
    $type      = 'donation';
    $ticket_id = 'BD-' . rand(10000, 99999);
    $hospital  = trim($_POST['hospital'] ?? 'Quiapo General Hospital');
    if (empty($hospital)) $hospital = 'Quiapo General Hospital';

    // ── 1. Insert donation record ──
    $stmt = $conn->prepare(
        "INSERT INTO donations (user_id, fullname, email, phone, address, birthdate, blood_type, ticket_id, hospital, type)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    if ($stmt) {
        $stmt->bind_param("isssssssss",
            $user_id, $name, $email, $phone, $address,
            $birthdate, $blood, $ticket_id, $hospital, $type
        );
        if ($stmt->execute()) {
            $donation_id = $stmt->insert_id;
            $stmt->close();

            // ── 2. Save screening answers ──
            $sc_age       = intval($_POST['sc_age']       ?? 0);
            $sc_weight    = floatval($_POST['sc_weight']  ?? 0);
            $sc_q3  = $_POST['sc_last_donated']    ?? '';
            $sc_q4  = $_POST['sc_feeling_well']    ?? '';
            $sc_q5  = $_POST['sc_heart_condition'] ?? '';
            $sc_q6  = $_POST['sc_diabetes']        ?? '';
            $sc_q7  = $_POST['sc_hepatitis_hiv']   ?? '';
            $sc_q8  = $_POST['sc_active_cancer']   ?? '';
            $sc_q9  = $_POST['sc_travel_endemic']  ?? '';
            $sc_q10 = $_POST['sc_tattoo_piercing'] ?? '';
            $sc_q11 = $_POST['sc_iv_drugs']        ?? '';
            $sc_q12 = $_POST['sc_recent_procedure']?? '';
            $sc_q13 = $_POST['sc_pregnant']        ?? '';
            $sc_q14 = $_POST['sc_medications']     ?? '';
            $sc_q15 = $_POST['sc_recent_vaccine']  ?? '';
            $eligible = 'yes';

            $sc = $conn->prepare(
                "INSERT INTO donor_screening
                 (donation_id, user_id, sc_age, sc_weight,
                  sc_last_donated, sc_feeling_well, sc_heart_condition, sc_diabetes,
                  sc_hepatitis_hiv, sc_active_cancer, sc_travel_endemic, sc_tattoo_piercing,
                  sc_iv_drugs, sc_recent_procedure, sc_pregnant, sc_medications,
                  sc_recent_vaccine, eligible)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
            );
            if ($sc) {
                $sc->bind_param(
                    "iiidssssssssssssss",
                    $donation_id, $user_id, $sc_age, $sc_weight,
                    $sc_q3, $sc_q4, $sc_q5, $sc_q6,
                    $sc_q7, $sc_q8, $sc_q9, $sc_q10,
                    $sc_q11, $sc_q12, $sc_q13, $sc_q14,
                    $sc_q15, $eligible
                );
                $sc->execute();
                $sc->close();
            }

            $showTicket = true;
        } else {
            $error = "Insert failed. Please try again.";
        }
    } else {
        $error = "Preparation failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Donate Blood — Eligibility Check</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root {
  --card: rgba(255,255,255,0.88);
  --border: rgba(0,0,0,.09);
  --border-s: rgba(0,0,0,.14);
  --red: #DC2626; --red-d: #B91C1C;
  --red-m: rgba(220,38,38,.08);
  --red-b: rgba(220,38,38,.22);
  --green: #16A34A; --green-d: #15803D;
  --green-m: rgba(22,163,74,.08);
  --green-b: rgba(22,163,74,.22);
  --amber: #D97706;
  --amber-m: rgba(217,119,6,.08);
  --amber-b: rgba(217,119,6,.22);
  --t: #1a1a2e; --t2: rgba(0,0,0,.62); --t3: rgba(0,0,0,.4);
  --serif: 'Cormorant Garamond', Georgia, serif;
  --sans: 'Outfit', sans-serif;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: var(--sans);
  background: linear-gradient(135deg,#ffffff 0%,#ffe4ec 50%,#ffc0cb 100%);
  background-size: 300% 300%;
  animation: gradMove 12s ease infinite;
  color: var(--t); min-height: 100vh; display: flex; flex-direction: column;
}
@keyframes gradMove { 0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%} }
.bg-grid { position:fixed;inset:0;z-index:0;pointer-events:none;background-image:linear-gradient(rgba(0,0,0,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(0,0,0,.03) 1px,transparent 1px);background-size:72px 72px }
.g1 { position:fixed;top:-200px;right:-100px;z-index:0;width:700px;height:700px;border-radius:50%;background:radial-gradient(circle,rgba(255,100,130,.2) 0%,transparent 60%);pointer-events:none }
.g2 { position:fixed;bottom:-200px;left:-100px;z-index:0;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(255,160,180,.15) 0%,transparent 60%);pointer-events:none }

nav { position:relative;z-index:10;height:66px;display:flex;align-items:center;justify-content:space-between;padding:0 40px;border-bottom:1px solid var(--border);background:rgba(255,255,255,.75);backdrop-filter:blur(16px) }
.nlogo { display:flex;align-items:center;gap:11px;text-decoration:none }
.nicon { width:32px;height:32px;background:var(--red);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;box-shadow:0 3px 12px rgba(220,38,38,.35) }
.ntext { font-family:var(--serif);font-size:19px;color:var(--t) }
.nlink { font-size:14px;color:var(--t2);text-decoration:none;padding:6px 14px;border-radius:7px;transition:all .18s }
.nlink:hover { color:var(--red);background:rgba(220,38,38,.07) }
.nnav { display:flex;align-items:center;gap:4px }

.pw { position:relative;z-index:1;flex:1;display:flex;align-items:flex-start;justify-content:center;padding:48px 24px }
.split { display:flex;gap:60px;align-items:flex-start;max-width:1060px;width:100% }
.sl { flex:1;padding-top:16px;position:sticky;top:32px }
.sr { flex-shrink:0;width:480px }

.pill { display:inline-flex;align-items:center;gap:7px;background:var(--red-m);border:1px solid var(--red-b);color:var(--red);font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;padding:5px 13px;border-radius:100px;margin-bottom:22px }
.pdot { width:5px;height:5px;background:var(--red);border-radius:50%;animation:pdot 2.5s ease-in-out infinite }
@keyframes pdot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.3;transform:scale(.6)} }
.sl h1 { font-family:var(--serif);font-size:clamp(34px,4vw,58px);font-weight:400;line-height:1.05;color:var(--t);margin-bottom:16px }
.sl h1 em { font-style:italic;color:var(--red);display:block }
.sl p { font-size:15px;font-weight:300;color:var(--t2);line-height:1.8;margin-bottom:28px }
.info-cards { display:flex;flex-direction:column;gap:10px }
.ic { display:flex;align-items:center;gap:14px;background:rgba(255,255,255,.6);border:1px solid var(--border);border-radius:12px;padding:14px 16px;backdrop-filter:blur(8px) }
.ic-icon { width:36px;height:36px;background:var(--red-m);border:1px solid var(--red-b);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0 }
.ic-text strong { font-size:13px;font-weight:500;color:var(--t);display:block }
.ic-text span { font-size:12px;color:var(--t3) }

/* CARD */
.card { background:var(--card);border:1px solid var(--border);border-radius:18px;overflow:hidden;box-shadow:0 20px 60px rgba(200,50,80,.1),0 2px 12px rgba(0,0,0,.06);backdrop-filter:blur(20px) }

/* STEP INDICATOR */
.steps { display:flex;align-items:center;padding:20px 30px;border-bottom:1px solid var(--border);background:rgba(255,255,255,.4) }
.step-item { display:flex;align-items:center;gap:8px }
.step-item:last-child { flex:0 }
.step-num { width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;flex-shrink:0;transition:all .3s }
.step-num.active { background:var(--red);color:#fff;box-shadow:0 2px 8px rgba(220,38,38,.4) }
.step-num.done { background:var(--green);color:#fff }
.step-num.inactive { background:rgba(0,0,0,.06);color:var(--t3) }
.step-label { font-size:12px;font-weight:500;color:var(--t3);transition:color .3s }
.step-label.active { color:var(--red) }
.step-label.done { color:var(--green) }
.step-line { flex:1;height:1px;background:var(--border);margin:0 14px;transition:background .3s }
.step-line.done { background:var(--green) }

/* QUIZ */
.card-body { padding:28px 30px }
.q-header { margin-bottom:20px }
.q-header h3 { font-family:var(--serif);font-size:20px;color:var(--t);margin-bottom:4px }
.q-header p { font-size:13px;color:var(--t3);font-weight:300 }
.q-section-tag { display:inline-block;font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:3px 10px;border-radius:100px;margin-bottom:10px;background:var(--red-m);border:1px solid var(--red-b);color:var(--red) }
.progress-bar { height:4px;background:rgba(0,0,0,.06);border-radius:2px;margin-bottom:24px;overflow:hidden }
.progress-fill { height:100%;background:var(--red);border-radius:2px;transition:width .4s ease }

.question-slide { display:none;animation:qin .35s ease both }
.question-slide.active { display:block }
@keyframes qin { from{opacity:0;transform:translateX(14px)} to{opacity:1;transform:translateX(0)} }

.q-text { font-size:15px;font-weight:500;color:var(--t);margin-bottom:6px;line-height:1.5 }
.q-hint { font-size:12px;color:var(--t3);margin-bottom:18px;font-weight:300;line-height:1.6 }
.q-options { display:flex;flex-direction:column;gap:10px }
.q-opt { display:flex;align-items:flex-start;gap:12px;padding:13px 16px;border:2px solid var(--border-s);border-radius:10px;cursor:pointer;transition:all .2s;background:rgba(255,255,255,.6);font-size:13.5px;color:var(--t2);user-select:none;line-height:1.4 }
.q-opt:hover { border-color:rgba(220,38,38,.3);background:var(--red-m);color:var(--t) }
.q-opt.selected-yes { border-color:var(--green);background:var(--green-m);color:var(--green) }
.q-opt.selected-no  { border-color:var(--red);background:var(--red-m);color:var(--red) }
.q-opt-icon { font-size:16px;flex-shrink:0;margin-top:1px }

.q-input-row { display:flex;gap:10px;align-items:flex-end }
.q-input-wrap { flex:1 }
.q-input-wrap label { display:block;font-size:11px;font-weight:600;color:var(--t3);letter-spacing:.8px;text-transform:uppercase;margin-bottom:6px }
.q-input-wrap input { width:100%;padding:11px 14px;background:rgba(255,255,255,.7);border:1.5px solid var(--border-s);border-radius:9px;color:var(--t);font-family:var(--sans);font-size:15px;outline:none;transition:all .2s }
.q-input-wrap input:focus { border-color:rgba(220,38,38,.5);background:#fff;box-shadow:0 0 0 3px rgba(220,38,38,.08) }
.q-input-wrap input::placeholder { color:rgba(0,0,0,.28) }
.q-check-btn { padding:11px 18px;background:var(--red);color:#fff;border:none;border-radius:9px;font-family:var(--sans);font-size:13px;font-weight:500;cursor:pointer;transition:all .2s;white-space:nowrap }
.q-check-btn:hover { background:var(--red-d) }

.q-nav { display:flex;justify-content:space-between;align-items:center;margin-top:22px }
.q-counter { font-size:12px;color:var(--t3) }
.q-next { padding:10px 22px;background:var(--red);color:#fff;border:none;border-radius:8px;font-family:var(--sans);font-size:13px;font-weight:500;cursor:pointer;transition:all .2s;opacity:.35;pointer-events:none }
.q-next.enabled { opacity:1;pointer-events:all }
.q-next.enabled:hover { background:var(--red-d);transform:translateY(-1px) }

/* RESULTS */
.result-panel { display:none;padding:30px }
.result-panel.show { display:block;animation:qin .4s ease both }
.result-top { text-align:center;padding-bottom:22px;margin-bottom:22px;border-bottom:1px dashed rgba(0,0,0,.1) }
.result-icon { font-size:46px;display:block;margin-bottom:10px }
.result-title { font-family:var(--serif);font-size:26px;color:var(--t) }
.result-sub { font-size:13px;color:var(--t3);margin-top:5px;font-weight:300 }
.badge-ok  { display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700;letter-spacing:.5px;margin-top:10px;background:var(--green-m);border:1px solid var(--green-b);color:var(--green) }
.badge-no  { display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700;letter-spacing:.5px;margin-top:10px;background:var(--amber-m);border:1px solid var(--amber-b);color:var(--amber) }

.info-block { border-radius:12px;padding:16px 18px;margin-bottom:16px }
.info-block.warn { background:rgba(217,119,6,.06);border:1px solid var(--amber-b) }
.info-block.good { background:rgba(22,163,74,.06);border:1px solid var(--green-b) }
.info-block-title { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;margin-bottom:10px }
.info-block.warn .info-block-title { color:var(--amber) }
.info-block.good .info-block-title { color:var(--green) }
.info-list { list-style:none;display:flex;flex-direction:column;gap:8px }
.info-list li { font-size:13px;color:var(--t2);display:flex;gap:8px;align-items:flex-start;line-height:1.5 }
.info-list.warn li::before { content:'⚠';font-size:13px;flex-shrink:0;margin-top:1px }
.info-list.good li::before { content:'✓';color:var(--green);font-weight:700;flex-shrink:0 }

.proc-btn { display:block;width:100%;padding:13px;background:var(--green);color:#fff;border:none;border-radius:9px;font-family:var(--sans);font-size:15px;font-weight:500;cursor:pointer;text-align:center;transition:all .2s;box-shadow:0 4px 18px rgba(22,163,74,.28);text-decoration:none;margin-top:4px }
.proc-btn:hover { background:var(--green-d);transform:translateY(-1px) }
.ghost-btn { display:block;width:100%;padding:12px;background:transparent;color:var(--t3);border:1.5px solid var(--border-s);border-radius:9px;font-family:var(--sans);font-size:14px;font-weight:500;cursor:pointer;text-align:center;transition:all .2s;text-decoration:none;margin-top:10px }
.ghost-btn:hover { border-color:var(--red);color:var(--red) }

/* DONATION FORM */
.form-panel { display:none;animation:qin .4s ease both }
.form-panel.show { display:block }
.form-head { padding:24px 30px;border-bottom:1px solid var(--border) }
.form-head .ctitle { font-family:var(--serif);font-size:22px;color:var(--t);margin-bottom:3px }
.form-head .csub { font-size:13px;font-weight:300;color:var(--t3) }
.sect-label { font-size:10px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:var(--t3);margin-bottom:14px;margin-top:20px }
.sect-label:first-child { margin-top:0 }
.fgrid { display:grid;grid-template-columns:1fr 1fr;gap:14px }
.field { margin-bottom:0 }
.field.full { grid-column:span 2 }
.field label { display:block;font-size:11px;font-weight:600;color:var(--t3);letter-spacing:.8px;text-transform:uppercase;margin-bottom:6px }
.field input,.field select,.field textarea { width:100%;padding:11px 14px;background:rgba(255,255,255,.7);border:1px solid var(--border-s);border-radius:9px;color:var(--t);font-family:var(--sans);font-size:14px;outline:none;transition:all .2s;resize:vertical }
.field input:focus,.field select:focus,.field textarea:focus { border-color:rgba(220,38,38,.5);background:#fff;box-shadow:0 0 0 3px rgba(220,38,38,.08) }
.field input::placeholder,.field textarea::placeholder { color:rgba(0,0,0,.28) }
.field select option { background:#fff;color:var(--t) }
.aerr { padding:11px 15px;border-radius:9px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px;background:rgba(220,38,38,.07);border:1px solid rgba(220,38,38,.2);color:var(--red) }
.sbtn { width:100%;padding:13px;background:var(--red);color:#fff;border:none;border-radius:9px;font-family:var(--sans);font-size:15px;font-weight:500;cursor:pointer;transition:all .2s;margin-top:6px;box-shadow:0 4px 18px rgba(220,38,38,.28);letter-spacing:.2px }
.sbtn:hover { background:var(--red-d);transform:translateY(-1px);box-shadow:0 7px 24px rgba(220,38,38,.38) }
.back-link { display:block;text-align:center;margin-top:16px;font-size:13px;color:var(--t3);text-decoration:none;cursor:pointer;transition:color .18s }
.back-link:hover { color:var(--red) }

/* TICKET */
.ticket { padding:28px 30px }
.tick-top { text-align:center;padding-bottom:20px;border-bottom:1px dashed rgba(0,0,0,.12) }
.tick-icon { font-size:40px;display:block;margin-bottom:10px }
.tick-title { font-family:var(--serif);font-size:24px;color:var(--t) }
.tick-sub { font-size:13px;color:var(--t3);margin-top:4px }
.type-badge { display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;letter-spacing:.5px;margin-top:10px;background:var(--red-m);border:1px solid var(--red-b);color:var(--red) }
.tick-rows { padding:16px 0 }
.tr { display:flex;justify-content:space-between;align-items:flex-start;padding:8px 0;border-bottom:1px solid rgba(0,0,0,.05);font-size:14px }
.tr:last-child { border-bottom:none }
.tlbl { color:var(--t3);font-size:12px;flex-shrink:0;margin-right:16px }
.tval { color:var(--t);font-weight:500;text-align:right }
.tid-val { font-family:monospace;font-size:16px;color:var(--red);font-weight:700;letter-spacing:.5px }
.tick-hosp { text-align:center;padding:16px 0 0;border-top:1px dashed rgba(0,0,0,.12) }
.th-label { font-size:11px;color:var(--t3);text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px }
.th-name { font-family:var(--serif);font-size:20px;color:var(--red) }
.th-note { font-size:12px;color:var(--t3);margin-top:4px }
.tdone { display:block;text-align:center;padding:12px;background:var(--red);color:#fff;border-radius:9px;text-decoration:none;font-size:14px;font-weight:500;margin-top:20px;transition:all .2s }
.tdone:hover { background:var(--red-d) }

/* HOSPITAL PICKER */
.hosp-picker { margin-top: 4px; }
.hosp-locate-btn {
  width: 100%; padding: 11px 14px; margin-bottom: 10px;
  background: var(--red-m); border: 1.5px dashed rgba(220,38,38,.4);
  border-radius: 9px; color: var(--red); font-family: var(--sans);
  font-size: 13px; font-weight: 600; cursor: pointer; transition: all .2s;
  display: flex; align-items: center; justify-content: center; gap: 8px;
}
.hosp-locate-btn:hover { background: rgba(220,38,38,.14); border-style: solid; }
.hosp-locate-btn:disabled { opacity: .5; cursor: not-allowed; }
.hosp-status { font-size: 12px; padding: 7px 12px; border-radius: 7px; margin-bottom: 8px; display: none; }
.hosp-status.loading { display:block; background:rgba(217,119,6,.08); border:1px solid rgba(217,119,6,.25); color:#92400e; }
.hosp-status.error   { display:block; background:rgba(220,38,38,.07); border:1px solid rgba(220,38,38,.2); color:var(--red); }
.hosp-list { border: 1px solid var(--border-s); border-radius: 9px; overflow: hidden; margin-bottom: 10px; max-height: 220px; overflow-y: auto; }
.hosp-list::-webkit-scrollbar { width: 4px; }
.hosp-list::-webkit-scrollbar-thumb { background: var(--border-s); border-radius: 4px; }
.hosp-option { padding: 11px 14px; cursor: pointer; border-bottom: 1px solid rgba(0,0,0,.05); transition: background .15s; font-size: 13px; }
.hosp-option:last-child { border-bottom: none; }
.hosp-option:hover { background: var(--red-m); }
.hosp-option.selected { background: var(--red-m); border-left: 3px solid var(--red); }
.hosp-option-name { font-weight: 500; color: var(--t); margin-bottom: 2px; }
.hosp-option-meta { font-size: 11px; color: var(--t3); display: flex; gap: 8px; }
.hosp-dist-badge { display: inline-block; background: rgba(22,163,74,.1); border: 1px solid rgba(22,163,74,.25); color: #16a34a; font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 5px; }
.hosp-selected-box { display: none; align-items: center; gap: 10px; padding: 11px 14px; background: rgba(22,163,74,.06); border: 1px solid rgba(22,163,74,.3); border-radius: 9px; margin-bottom: 8px; }
.hosp-selected-box.show { display: flex; }
.hosp-selected-name { font-size: 13px; font-weight: 600; color: #15803d; flex: 1; }
.hosp-clear { background: none; border: none; color: var(--t3); cursor: pointer; font-size: 16px; padding: 0 4px; transition: color .15s; }
.hosp-clear:hover { color: var(--red); }
.hosp-divider { display: flex; align-items: center; gap: 10px; margin: 10px 0 8px; }
.hosp-divider span { font-size: 11px; color: var(--t3); white-space: nowrap; }
.hosp-divider::before,.hosp-divider::after { content:''; flex:1; height:1px; background:var(--border); }
.hosp-manual input { width:100%; padding:11px 14px; background:rgba(255,255,255,.7); border:1px solid var(--border-s); border-radius:9px; color:var(--t); font-family:var(--sans); font-size:14px; outline:none; transition:all .2s; }
.hosp-manual input:focus { border-color:rgba(220,38,38,.5); background:#fff; box-shadow:0 0 0 3px rgba(220,38,38,.08); }
.hosp-manual input::placeholder { color:rgba(0,0,0,.28); }

@media(max-width:860px){
  .split{flex-direction:column}.sl{position:static;display:none}
  .sr{width:100%;max-width:480px}
  .fgrid{grid-template-columns:1fr}.field.full{grid-column:span 1}
  nav{padding:0 20px}
}
</style>
</head>
<body>
<div class="bg-grid"></div><div class="g1"></div><div class="g2"></div>

<nav>
  <a class="nlogo" href="user_dashboard.php"><div class="nicon">🩸</div><span class="ntext">Blood Donation</span></a>
  <div class="nnav">
    <a href="request_blood_form.php" class="nlink">Request Blood</a>
    <a href="my_history.php" class="nlink">My History</a>
    <a href="user_dashboard.php" class="nlink">← Dashboard</a>
  </div>
</nav>

<div class="pw">
<div class="split">

<!-- LEFT PANEL -->
<div class="sl">
  <div class="pill"><span class="pdot"></span>Registered Donor</div>
  <h1>Donate<br><em>Blood</em></h1>
  <p>We'll run a quick 15-question eligibility screening first to ensure a safe donation for you and the recipient. It only takes a few minutes.</p>
  <div class="info-cards">
    <div class="ic"><div class="ic-icon">🩺</div><div class="ic-text"><strong>15-Step Screening</strong><span>Covers health, travel, medications & more</span></div></div>
    <div class="ic"><div class="ic-icon">🔒</div><div class="ic-text"><strong>Private & Secure</strong><span>Your answers are saved to your profile only</span></div></div>
    <div class="ic"><div class="ic-icon">⏱</div><div class="ic-text"><strong>Takes ~3 Minutes</strong><span>Answer honestly — your safety depends on it</span></div></div>
  </div>
</div>

<!-- RIGHT PANEL / CARD -->
<div class="sr">
<div class="card">

<?php if(!$showTicket): ?>

<!-- STEP BAR -->
<div class="steps">
  <div class="step-item">
    <div class="step-num active" id="sn1">1</div>
    <div class="step-label active" id="sl1">Eligibility</div>
  </div>
  <div class="step-line" id="stepLine" style="flex:1"></div>
  <div class="step-item">
    <div class="step-num inactive" id="sn2">2</div>
    <div class="step-label inactive" id="sl2">Your Details</div>
  </div>
</div>

<!-- ══════════════════════════════════════════════
     QUIZ PANEL — 15 Questions
     ══════════════════════════════════════════════ -->
<div id="quizPanel" class="card-body">
  <div class="q-header">
    <h3>Eligibility Screening</h3>
    <p>Answer honestly — your safety and the recipient's depend on it.</p>
  </div>
  <div class="progress-bar"><div class="progress-fill" id="progressFill" style="width:0%"></div></div>

  <!-- ── SECTION A: VITALS ── -->

  <!-- Q1: Age -->
  <div class="question-slide active" id="q1">
    <span class="q-section-tag">Section A · Vitals</span>
    <div class="q-text">How old are you?</div>
    <div class="q-hint">Donors must be between 17 and 65 years old. Those under 17 or over 65 are deferred for safety.</div>
    <div class="q-input-row">
      <div class="q-input-wrap">
        <label>Age (years)</label>
        <input type="number" id="ageInput" min="1" max="120" placeholder="e.g. 25">
      </div>
      <button class="q-check-btn" onclick="checkAge()">Confirm</button>
    </div>
    <div class="q-nav">
      <span class="q-counter">Question 1 of 15</span>
      <button class="q-next" id="nextQ1" onclick="goToQ(2)">Next →</button>
    </div>
  </div>

  <!-- Q2: Weight -->
  <div class="question-slide" id="q2">
    <span class="q-section-tag">Section A · Vitals</span>
    <div class="q-text">What is your current body weight?</div>
    <div class="q-hint">A minimum weight of 50 kg (110 lbs) is required. This protects you from volume-related side effects after donation.</div>
    <div class="q-input-row">
      <div class="q-input-wrap">
        <label>Weight (kg)</label>
        <input type="number" id="weightInput" min="1" max="300" placeholder="e.g. 60">
      </div>
      <button class="q-check-btn" onclick="checkWeight()">Confirm</button>
    </div>
    <div class="q-nav">
      <span class="q-counter">Question 2 of 15</span>
      <button class="q-next" id="nextQ2" onclick="goToQ(3)">Next →</button>
    </div>
  </div>

  <!-- ── SECTION B: DONATION HISTORY ── -->

  <!-- Q3: Last donation -->
  <div class="question-slide" id="q3">
    <span class="q-section-tag">Section B · Donation History</span>
    <div class="q-text">Have you donated whole blood in the last 56 days (8 weeks)?</div>
    <div class="q-hint">Your body needs at least 8 weeks to fully replenish red blood cells after a whole blood donation.</div>
    <div class="q-options">
      <div class="q-opt" onclick="selectOpt(this,'q3','no')"><span class="q-opt-icon">✅</span> No — it has been more than 56 days, or I have never donated before</div>
      <div class="q-opt" onclick="selectOpt(this,'q3','yes')"><span class="q-opt-icon">⛔</span> Yes — I donated blood within the last 56 days</div>
    </div>
    <div class="q-nav">
      <span class="q-counter">Question 3 of 15</span>
      <button class="q-next" id="nextQ3" onclick="goToQ(4)">Next →</button>
    </div>
  </div>

  <!-- ── SECTION C: CURRENT HEALTH ── -->

  <!-- Q4: Feeling well -->
  <div class="question-slide" id="q4">
    <span class="q-section-tag">Section C · Current Health</span>
    <div class="q-text">Are you feeling well today — free from fever, cold, cough, or any active illness?</div>
    <div class="q-hint">You must be in good health on the day of donation. Any current illness — even mild — is grounds for a temporary deferral.</div>
    <div class="q-options">
      <div class="q-opt" onclick="selectOpt(this,'q4','yes')"><span class="q-opt-icon">✅</span> Yes — I feel healthy and well today</div>
      <div class="q-opt" onclick="selectOpt(this,'q4','no')"><span class="q-opt-icon">⛔</span> No — I have a fever, cold, cough, or active illness</div>
    </div>
    <div class="q-nav">
      <span class="q-counter">Question 4 of 15</span>
      <button class="q-next" id="nextQ4" onclick="goToQ(5)">Next →</button>
    </div>
  </div>

  <!-- ── SECTION D: MEDICAL HISTORY ── -->

  <!-- Q5: Heart / BP / Stroke -->
  <div class="question-slide" id="q5">
    <span class="q-section-tag">Section D · Medical History</span>
    <div class="q-text">Have you ever been diagnosed with heart disease, high blood pressure (hypertension), or had a stroke?</div>
    <div class="q-hint">Donors with cardiovascular conditions need a doctor's medical clearance before donating. This protects both you and the recipient.</div>
    <div class="q-options">
      <div class="q-opt" onclick="selectOpt(this,'q5','no')"><span class="q-opt-icon">✅</span> No — I have no history of heart disease, high blood pressure, or stroke</div>
      <div class="q-opt" onclick="selectOpt(this,'q5','yes')"><span class="q-opt-icon">⛔</span> Yes — I have been diagnosed with one or more of these conditions</div>
    </div>
    <div class="q-nav">
      <span class="q-counter">Question 5 of 15</span>
      <button class="q-next" id="nextQ5" onclick="goToQ(6)">Next →</button>
    </div>
  </div>

  <!-- Q6: Diabetes -->
  <div class="question-slide" id="q6">
    <span class="q-section-tag">Section D · Medical History</span>
    <div class="q-text">Do you have insulin-dependent (Type 1) or uncontrolled diabetes?</div>
    <div class="q-hint">Insulin-dependent diabetes or poorly controlled blood sugar can pose risks during donation. Well-controlled Type 2 diabetes may be acceptable — consult your doctor.</div>
    <div class="q-options">
      <div class="q-opt" onclick="selectOpt(this,'q6','no')"><span class="q-opt-icon">✅</span> No — I do not have insulin-dependent or uncontrolled diabetes</div>
      <div class="q-opt" onclick="selectOpt(this,'q6','yes')"><span class="q-opt-icon">⛔</span> Yes — I have insulin-dependent or uncontrolled diabetes</div>
    </div>
    <div class="q-nav">
      <span class="q-counter">Question 6 of 15</span>
      <button class="q-next" id="nextQ6" onclick="goToQ(7)">Next →</button>
    </div>
  </div>

  <!-- Q7: Hepatitis / HIV / Syphilis -->
  <div class="question-slide" id="q7">
    <span class="q-section-tag">Section D · Medical History</span>
    <div class="q-text">Have you ever tested positive for Hepatitis B, Hepatitis C, HIV/AIDS, or syphilis?</div>
    <div class="q-hint">These blood-borne infections can be transmitted to recipients and result in a permanent deferral from donating blood.</div>
    <div class="q-options">
      <div class="q-opt" onclick="selectOpt(this,'q7','no')"><span class="q-opt-icon">✅</span> No — I have never tested positive for any of these</div>
      <div class="q-opt" onclick="selectOpt(this,'q7','yes')"><span class="q-opt-icon">⛔</span> Yes — I have tested positive for one or more of these</div>
    </div>
    <div class="q-nav">
      <span class="q-counter">Question 7 of 15</span>
      <button class="q-next" id="nextQ7" onclick="goToQ(8)">Next →</button>
    </div>
  </div>

  <!-- Q8: Active Cancer / Blood Disorder -->
  <div class="question-slide" id="q8">
    <span class="q-section-tag">Section D · Medical History</span>
    <div class="q-text">Do you currently have or have you recently been treated for active cancer, leukemia, or any blood disorder (e.g., sickle cell disease, hemophilia)?</div>
    <div class="q-hint">Active cancer and certain blood disorders prevent safe donation. Cancer survivors who have fully recovered may be eligible — please consult your doctor.</div>
    <div class="q-options">
      <div class="q-opt" onclick="selectOpt(this,'q8','no')"><span class="q-opt-icon">✅</span> No — none of these currently apply to me</div>
      <div class="q-opt" onclick="selectOpt(this,'q8','yes')"><span class="q-opt-icon">⛔</span> Yes — I currently have or am being treated for one of these</div>
    </div>
    <div class="q-nav">
      <span class="q-counter">Question 8 of 15</span>
      <button class="q-next" id="nextQ8" onclick="goToQ(9)">Next →</button>
    </div>
  </div>

  <!-- ── SECTION E: TRAVEL HISTORY ── -->

  <!-- Q9: Travel to endemic areas -->
  <div class="question-slide" id="q9">
    <span class="q-section-tag">Section E · Travel History</span>
    <div class="q-text">Have you traveled to a malaria-endemic, dengue-endemic, or Zika-affected area within the last 12 months?</div>
    <div class="q-hint">Travel to these areas may expose you to parasites or viruses that can be unknowingly transmitted via blood transfusion. Examples include parts of Africa, Southeast Asia, South America, and some provinces of the Philippines.</div>
    <div class="q-options">
      <div class="q-opt" onclick="selectOpt(this,'q9','no')"><span class="q-opt-icon">✅</span> No — I have not traveled to any of these areas in the past 12 months</div>
      <div class="q-opt" onclick="selectOpt(this,'q9','yes')"><span class="q-opt-icon">⛔</span> Yes — I traveled to a malaria, dengue, or Zika area within the last year</div>
    </div>
    <div class="q-nav">
      <span class="q-counter">Question 9 of 15</span>
      <button class="q-next" id="nextQ9" onclick="goToQ(10)">Next →</button>
    </div>
  </div>

  <!-- ── SECTION F: LIFESTYLE & RISK BEHAVIORS ── -->

  <!-- Q10: Tattoo / Piercing -->
  <div class="question-slide" id="q10">
    <span class="q-section-tag">Section F · Lifestyle & Risk Behaviors</span>
    <div class="q-text">Have you gotten a tattoo, body piercing, or acupuncture in the last 12 months?</div>
    <div class="q-hint">These procedures carry a risk of blood-borne infection if not done under sterile conditions. A 12-month waiting period applies unless done at a licensed facility using sterile equipment.</div>
    <div class="q-options">
      <div class="q-opt" onclick="selectOpt(this,'q10','no')"><span class="q-opt-icon">✅</span> No — not within the past 12 months</div>
      <div class="q-opt" onclick="selectOpt(this,'q10','yes')"><span class="q-opt-icon">⛔</span> Yes — I got a tattoo, piercing, or acupuncture within the last year</div>
    </div>
    <div class="q-nav">
      <span class="q-counter">Question 10 of 15</span>
      <button class="q-next" id="nextQ10" onclick="goToQ(11)">Next →</button>
    </div>
  </div>

  <!-- Q11: IV Drug Use -->
  <div class="question-slide" id="q11">
    <span class="q-section-tag">Section F · Lifestyle & Risk Behaviors</span>
    <div class="q-text">Have you ever used intravenous (IV) drugs that were not prescribed by a doctor?</div>
    <div class="q-hint">Non-prescribed IV drug use is a permanent disqualification due to the high risk of blood-borne disease transmission. This policy exists to protect transfusion recipients.</div>
    <div class="q-options">
      <div class="q-opt" onclick="selectOpt(this,'q11','no')"><span class="q-opt-icon">✅</span> No — I have never used non-prescribed IV drugs</div>
      <div class="q-opt" onclick="selectOpt(this,'q11','yes')"><span class="q-opt-icon">⛔</span> Yes — I have used non-prescribed IV drugs</div>
    </div>
    <div class="q-nav">
      <span class="q-counter">Question 11 of 15</span>
      <button class="q-next" id="nextQ11" onclick="goToQ(12)">Next →</button>
    </div>
  </div>

  <!-- ── SECTION G: RECENT PROCEDURES ── -->

  <!-- Q12: Surgery / Dental / Transfusion -->
  <div class="question-slide" id="q12">
    <span class="q-section-tag">Section G · Recent Procedures</span>
    <div class="q-text">Have you had surgery, a major dental procedure, an endoscopy, or received a blood transfusion in the last 12 months?</div>
    <div class="q-hint">These procedures may expose you to infections or require time for your body to fully recover. A waiting period is needed before you can donate safely.</div>
    <div class="q-options">
      <div class="q-opt" onclick="selectOpt(this,'q12','no')"><span class="q-opt-icon">✅</span> No — I have not had any of these within the last 12 months</div>
      <div class="q-opt" onclick="selectOpt(this,'q12','yes')"><span class="q-opt-icon">⛔</span> Yes — I had surgery, a dental procedure, or a transfusion in the last year</div>
    </div>
    <div class="q-nav">
      <span class="q-counter">Question 12 of 15</span>
      <button class="q-next" id="nextQ12" onclick="goToQ(13)">Next →</button>
    </div>
  </div>

  <!-- ── SECTION H: FOR FEMALE DONORS ── -->

  <!-- Q13: Pregnancy / Breastfeeding -->
  <div class="question-slide" id="q13">
    <span class="q-section-tag">Section H · Pregnancy & Maternal Health</span>
    <div class="q-text">Are you currently pregnant, have you given birth within the last 6 months, or are you currently breastfeeding?</div>
    <div class="q-hint">Pregnancy and the postpartum period place additional demands on your body. Donation is deferred to protect both your health and your baby's. If this does not apply to you (e.g., you are male), select "No."</div>
    <div class="q-options">
      <div class="q-opt" onclick="selectOpt(this,'q13','no')"><span class="q-opt-icon">✅</span> No — none of these apply to me</div>
      <div class="q-opt" onclick="selectOpt(this,'q13','yes')"><span class="q-opt-icon">⛔</span> Yes — I am pregnant, gave birth within 6 months, or am breastfeeding</div>
    </div>
    <div class="q-nav">
      <span class="q-counter">Question 13 of 15</span>
      <button class="q-next" id="nextQ13" onclick="goToQ(14)">Next →</button>
    </div>
  </div>

  <!-- ── SECTION I: MEDICATIONS ── -->

  <!-- Q14: Medications -->
  <div class="question-slide" id="q14">
    <span class="q-section-tag">Section I · Medications</span>
    <div class="q-text">Are you currently taking antibiotics, blood thinners (e.g., warfarin, aspirin), or isotretinoin (Accutane)?</div>
    <div class="q-hint">
      These medications require a deferral period:<br>
      • Antibiotics — wait until the full course is complete<br>
      • Blood thinners (warfarin) — 7-day wait after last dose<br>
      • Isotretinoin / Accutane — 30-day wait after last dose
    </div>
    <div class="q-options">
      <div class="q-opt" onclick="selectOpt(this,'q14','no')"><span class="q-opt-icon">✅</span> No — I am not currently taking any of these medications</div>
      <div class="q-opt" onclick="selectOpt(this,'q14','yes')"><span class="q-opt-icon">⛔</span> Yes — I am currently on antibiotics, blood thinners, or isotretinoin</div>
    </div>
    <div class="q-nav">
      <span class="q-counter">Question 14 of 15</span>
      <button class="q-next" id="nextQ14" onclick="goToQ(15)">Next →</button>
    </div>
  </div>

  <!-- Q15: Recent Vaccine -->
  <div class="question-slide" id="q15">
    <span class="q-section-tag">Section I · Medications</span>
    <div class="q-text">Have you received any vaccine in the last 4 weeks?</div>
    <div class="q-hint">
      Most live vaccines (e.g., MMR, chickenpox, yellow fever) require a 4-week deferral.<br>
      Inactivated vaccines (e.g., flu shot, COVID-19, hepatitis B) typically require only 48 hours — if you feel well, you may still be eligible. Answer "Yes" if you received any vaccine in the last 4 weeks to be safe.
    </div>
    <div class="q-options">
      <div class="q-opt" onclick="selectOpt(this,'q15','no')"><span class="q-opt-icon">✅</span> No — I have not received any vaccine in the last 4 weeks</div>
      <div class="q-opt" onclick="selectOpt(this,'q15','yes')"><span class="q-opt-icon">⛔</span> Yes — I received a vaccine within the last 4 weeks</div>
    </div>
    <div class="q-nav">
      <span class="q-counter">Question 15 of 15</span>
      <button class="q-next" id="nextQ15" onclick="evaluateEligibility()">Check Eligibility →</button>
    </div>
  </div>

</div><!-- /quizPanel -->

<!-- ══════════════════════════════════════════════
     RESULT: ELIGIBLE
     ══════════════════════════════════════════════ -->
<div id="resultEligible" class="result-panel">
  <div class="result-top">
    <span class="result-icon">🎉</span>
    <div class="result-title">You're Eligible to Donate!</div>
    <div class="result-sub">You passed all 15 screening questions. You are cleared for a safe blood donation.</div>
    <span class="badge-ok">✓ Cleared for Donation</span>
  </div>
  <div class="info-block good">
    <div class="info-block-title">Before You Come In</div>
    <ul class="info-list good">
      <li>Drink plenty of water — stay well-hydrated for at least 2 hours before donating.</li>
      <li>Eat a healthy, iron-rich meal at least 2 hours before (avoid fatty foods).</li>
      <li>Get a full night's sleep the night before your donation.</li>
      <li>Wear comfortable clothing with sleeves that roll up easily.</li>
      <li>Bring a valid ID and this ticket when you arrive at the hospital.</li>
    </ul>
  </div>
  <button class="proc-btn" onclick="showDonationForm()">Proceed to Donation Form →</button>
  <a href="user_dashboard.php" class="ghost-btn">← Back to Dashboard</a>
</div>

<!-- ══════════════════════════════════════════════
     RESULT: NOT ELIGIBLE
     ══════════════════════════════════════════════ -->
<div id="resultIneligible" class="result-panel">
  <div class="result-top">
    <span class="result-icon">⚠️</span>
    <div class="result-title">Not Eligible at This Time</div>
    <div class="result-sub">Based on your answers, you are temporarily or permanently deferred from donating.</div>
    <span class="badge-no">⚠ Deferred from Donation</span>
  </div>
  <div class="info-block warn">
    <div class="info-block-title">Reason(s) for Deferral</div>
    <ul class="info-list warn" id="reasonList"></ul>
  </div>
  <div class="info-block good">
    <div class="info-block-title">What You Can Do</div>
    <ul class="info-list good">
      <li>Wait until your deferral period has passed, then retake the screening.</li>
      <li>Consult your doctor if a medical condition or medication is the reason.</li>
      <li>You can still help by encouraging family and friends to donate!</li>
      <li>Check back regularly — most deferrals are temporary.</li>
    </ul>
  </div>
  <button class="ghost-btn" onclick="restartQuiz()" style="margin-top:0;border-color:var(--red);color:var(--red)">↺ Retake Screening</button>
  <a href="user_dashboard.php" class="ghost-btn">← Back to Dashboard</a>
</div>

<!-- ══════════════════════════════════════════════
     STEP 2: DONATION FORM
     ══════════════════════════════════════════════ -->
<div id="donationForm" class="form-panel">
  <div class="form-head">
    <div class="ctitle">🩸 Donor Details</div>
    <div class="csub">Fill in your information — all fields are required</div>
  </div>
  <div class="card-body">
    <?php if($error):?><div class="aerr">⚠ <?=htmlspecialchars($error)?></div><?php endif;?>
    <form method="POST" id="donateForm">
      <!-- Hidden screening fields (populated by JS before submit) -->
      <input type="hidden" name="sc_age"            id="sc_age">
      <input type="hidden" name="sc_weight"         id="sc_weight">
      <input type="hidden" name="sc_last_donated"   id="sc_last_donated">
      <input type="hidden" name="sc_feeling_well"   id="sc_feeling_well">
      <input type="hidden" name="sc_heart_condition" id="sc_heart_condition">
      <input type="hidden" name="sc_diabetes"       id="sc_diabetes">
      <input type="hidden" name="sc_hepatitis_hiv"  id="sc_hepatitis_hiv">
      <input type="hidden" name="sc_active_cancer"  id="sc_active_cancer">
      <input type="hidden" name="sc_travel_endemic" id="sc_travel_endemic">
      <input type="hidden" name="sc_tattoo_piercing" id="sc_tattoo_piercing">
      <input type="hidden" name="sc_iv_drugs"       id="sc_iv_drugs">
      <input type="hidden" name="sc_recent_procedure" id="sc_recent_procedure">
      <input type="hidden" name="sc_pregnant"       id="sc_pregnant">
      <input type="hidden" name="sc_medications"    id="sc_medications">
      <input type="hidden" name="sc_recent_vaccine" id="sc_recent_vaccine">

      <div class="sect-label">Personal Information</div>
      <div class="fgrid">
        <div class="field full"><label>Full Name</label><input type="text" name="fullname" placeholder="Your full legal name" required></div>
        <div class="field"><label>Email Address</label><input type="email" name="email" placeholder="you@email.com" required></div>
        <div class="field"><label>Phone Number</label><input type="text" name="phone" placeholder="e.g. 09171234567" required></div>
        <div class="field full"><label>Date of Birth</label><input type="date" name="birthdate" required></div>
        <div class="field full"><label>Permanent Address</label><textarea name="address" placeholder="Street, Barangay, City, Province" rows="2" required></textarea></div>
      </div>

      <div class="sect-label">Blood Details</div>
      <div class="fgrid">
        <div class="field full"><label>Blood Type</label>
          <select name="blood" required>
            <option value="">Select your blood type</option>
            <option>O+</option><option>O-</option>
            <option>A+</option><option>A-</option>
            <option>B+</option><option>B-</option>
            <option>AB+</option><option>AB-</option>
          </select>
        </div>
      </div>

      <div class="sect-label">Donation Hospital</div>
      <input type="hidden" name="hospital" id="hospitalInput" value="">
      <div class="hosp-picker">
        <!-- Selected display -->
        <div class="hosp-selected-box" id="hospSelectedBox">
          <span>🏥</span>
          <span class="hosp-selected-name" id="hospSelectedName"></span>
          <button type="button" class="hosp-clear" onclick="clearHospital()" title="Clear">✕</button>
        </div>
        <!-- Locate button -->
        <button type="button" class="hosp-locate-btn" id="hospLocateBtn" onclick="findHospitals()">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg>
          Find Hospitals Near Me
        </button>
        <!-- Status -->
        <div class="hosp-status" id="hospStatus"></div>
        <!-- Results list -->
        <div class="hosp-list" id="hospList" style="display:none"></div>
        <!-- Manual fallback -->
        <div class="hosp-divider"><span>or type manually</span></div>
        <div class="hosp-manual">
          <input type="text" id="manualHospital" placeholder="e.g. Philippine General Hospital" oninput="setManualHospital(this.value)">
        </div>
      </div>

      <button type="submit" class="sbtn" name="donate" id="submitDonateBtn" style="margin-top:20px" onclick="populateScreeningFields(); return validateHospital();">
        Submit Donation →
      </button>
    </form>
    <a class="back-link" onclick="backToResult()">← Back to Eligibility Result</a>
  </div>
</div>

<?php else: ?>
<!-- ══════════════════════════════════════════════
     TICKET
     ══════════════════════════════════════════════ -->
<div class="ticket">
  <div class="tick-top">
    <span class="tick-icon">🎫</span>
    <div class="tick-title">Donation Confirmed</div>
    <div class="tick-sub">Keep this ticket — present it upon arrival at the hospital</div>
    <span class="type-badge">🩸 Blood Donation</span>
  </div>
  <div class="tick-rows">
    <div class="tr"><span class="tlbl">Name</span><span class="tval"><?=htmlspecialchars($name)?></span></div>
    <div class="tr"><span class="tlbl">Email</span><span class="tval"><?=htmlspecialchars($email)?></span></div>
    <div class="tr"><span class="tlbl">Phone</span><span class="tval"><?=htmlspecialchars($phone)?></span></div>
    <div class="tr"><span class="tlbl">Date of Birth</span><span class="tval"><?=htmlspecialchars($birthdate)?></span></div>
    <div class="tr"><span class="tlbl">Address</span><span class="tval" style="max-width:220px"><?=htmlspecialchars($address)?></span></div>
    <div class="tr"><span class="tlbl">Blood Type</span><span class="tval"><?=htmlspecialchars($blood)?></span></div>
    <div class="tr"><span class="tlbl">Ticket ID</span><span class="tid-val"><?=$ticket_id?></span></div>
  </div>
  <div class="tick-hosp">
    <div class="th-label">Report to</div>
    <div class="th-name"><?=$hospital?></div>
    <div class="th-note">Show this screen or print it upon arrival</div>
    <a href="user_dashboard.php" class="tdone">Done — Back to Dashboard</a>
  </div>
</div>
<?php endif; ?>

</div><!-- /card -->
</div><!-- /sr -->
</div><!-- /split -->
</div><!-- /pw -->

<script>
// ── Quiz State ──
const TOTAL_Q = 15;
const answers = {};

function updateProgress(current) {
  document.getElementById('progressFill').style.width = ((current - 1) / TOTAL_Q * 100) + '%';
}

function goToQ(n) {
  for (let i = 1; i <= TOTAL_Q; i++)
    document.getElementById('q' + i).classList.remove('active');
  document.getElementById('q' + n).classList.add('active');
  updateProgress(n);
}

function selectOpt(el, qid, val) {
  el.parentElement.querySelectorAll('.q-opt')
    .forEach(s => s.classList.remove('selected-yes','selected-no'));
  el.classList.add(val === 'yes' ? 'selected-yes' : 'selected-no');
  answers[qid] = val;
  const num = qid.replace('q', '');
  const btn = document.getElementById('nextQ' + num);
  if (btn) btn.classList.add('enabled');
}

function checkAge() {
  const val = parseInt(document.getElementById('ageInput').value);
  if (isNaN(val) || val < 1) { alert('Please enter a valid age.'); return; }
  answers['q1'] = val;
  document.getElementById('nextQ1').classList.add('enabled');
}

function checkWeight() {
  const val = parseFloat(document.getElementById('weightInput').value);
  if (isNaN(val) || val < 1) { alert('Please enter a valid weight.'); return; }
  answers['q2'] = val;
  document.getElementById('nextQ2').classList.add('enabled');
}

// ── Populate hidden inputs with quiz answers before form submit ──
function populateScreeningFields() {
  document.getElementById('sc_age').value            = answers['q1']  || '';
  document.getElementById('sc_weight').value         = answers['q2']  || '';
  document.getElementById('sc_last_donated').value   = answers['q3']  || '';
  document.getElementById('sc_feeling_well').value   = answers['q4']  || '';
  document.getElementById('sc_heart_condition').value= answers['q5']  || '';
  document.getElementById('sc_diabetes').value       = answers['q6']  || '';
  document.getElementById('sc_hepatitis_hiv').value  = answers['q7']  || '';
  document.getElementById('sc_active_cancer').value  = answers['q8']  || '';
  document.getElementById('sc_travel_endemic').value = answers['q9']  || '';
  document.getElementById('sc_tattoo_piercing').value= answers['q10'] || '';
  document.getElementById('sc_iv_drugs').value       = answers['q11'] || '';
  document.getElementById('sc_recent_procedure').value = answers['q12'] || '';
  document.getElementById('sc_pregnant').value       = answers['q13'] || '';
  document.getElementById('sc_medications').value    = answers['q14'] || '';
  document.getElementById('sc_recent_vaccine').value = answers['q15'] || '';
}

// ── Eligibility Evaluation (all 15 questions) ──
function evaluateEligibility() {
  const reasons = [];

  const age = answers['q1'];
  if (age === undefined || age < 17 || age > 65)
    reasons.push('Age must be between 17 and 65 years old. (Entered: ' + (age ?? 'not confirmed') + ')');

  const weight = answers['q2'];
  if (weight === undefined || weight < 50)
    reasons.push('Minimum weight of 50 kg required. (Entered: ' + (weight !== undefined ? weight + ' kg' : 'not confirmed') + ')');

  if (answers['q3'] === 'yes')
    reasons.push('You donated blood within the last 56 days. Please wait at least 8 weeks between whole blood donations.');

  if (answers['q4'] === 'no')
    reasons.push('You must be feeling completely well — no fever, cold, cough, or active illness — on the day of donation.');

  if (answers['q5'] === 'yes')
    reasons.push('A history of heart disease, high blood pressure, or stroke requires a medical clearance letter from your doctor before you can donate.');

  if (answers['q6'] === 'yes')
    reasons.push('Insulin-dependent or uncontrolled diabetes defers donation. Please consult your doctor for clearance.');

  if (answers['q7'] === 'yes')
    reasons.push('A positive test for Hepatitis B, Hepatitis C, HIV/AIDS, or syphilis results in a permanent deferral to protect transfusion recipients.');

  if (answers['q8'] === 'yes')
    reasons.push('Active cancer, leukemia, or blood disorders prevent safe donation at this time. Please consult your doctor.');

  if (answers['q9'] === 'yes')
    reasons.push('Travel to malaria, dengue, or Zika-endemic areas within the last 12 months requires a deferral period. Please wait until the period has passed.');

  if (answers['q10'] === 'yes')
    reasons.push('A tattoo, body piercing, or acupuncture within the last 12 months requires a deferral period due to the risk of blood-borne infection.');

  if (answers['q11'] === 'yes')
    reasons.push('Non-prescribed intravenous (IV) drug use is a permanent disqualification from donating blood due to serious infection risk.');

  if (answers['q12'] === 'yes')
    reasons.push('Surgery, major dental work, or a blood transfusion within the last 12 months requires a waiting period before you can donate.');

  if (answers['q13'] === 'yes')
    reasons.push('Pregnancy, having given birth within the last 6 months, or currently breastfeeding defers donation to protect both your health and your baby.');

  if (answers['q14'] === 'yes')
    reasons.push('Current use of antibiotics, blood thinners (e.g., warfarin), or isotretinoin (Accutane) requires a waiting period after your last dose before donating.');

  if (answers['q15'] === 'yes')
    reasons.push('Receiving a live vaccine within the last 4 weeks requires a deferral period. Please wait until the 4 weeks have passed and you are feeling well.');

  document.getElementById('quizPanel').style.display = 'none';
  setStep1Done();

  if (reasons.length === 0) {
    populateScreeningFields();
    document.getElementById('resultEligible').classList.add('show');
  } else {
    const ul = document.getElementById('reasonList');
    ul.innerHTML = '';
    reasons.forEach(r => {
      const li = document.createElement('li');
      li.textContent = r;
      ul.appendChild(li);
    });
    document.getElementById('resultIneligible').classList.add('show');
  }
}

function setStep1Done() {
  document.getElementById('sn1').className = 'step-num done';
  document.getElementById('sl1').className = 'step-label done';
}

function showDonationForm() {
  populateScreeningFields();
  document.getElementById('resultEligible').classList.remove('show');
  document.getElementById('donationForm').classList.add('show');
  document.getElementById('sn2').className = 'step-num active';
  document.getElementById('sl2').className = 'step-label active';
  document.getElementById('stepLine').classList.add('done');
}

function backToResult() {
  document.getElementById('donationForm').classList.remove('show');
  document.getElementById('resultEligible').classList.add('show');
  document.getElementById('sn2').className = 'step-num inactive';
  document.getElementById('sl2').className = 'step-label inactive';
  document.getElementById('stepLine').classList.remove('done');
}

function restartQuiz() {
  for (const k in answers) delete answers[k];
  document.getElementById('resultIneligible').classList.remove('show');
  document.getElementById('quizPanel').style.display = '';
  document.getElementById('sn1').className = 'step-num active';
  document.getElementById('sl1').className = 'step-label active';
  document.getElementById('sn2').className = 'step-num inactive';
  document.getElementById('sl2').className = 'step-label inactive';
  document.getElementById('stepLine').classList.remove('done');
  for (let i = 1; i <= TOTAL_Q; i++) {
    document.getElementById('q' + i).classList.remove('active');
    const btn = document.getElementById('nextQ' + i);
    if (btn) btn.classList.remove('enabled');
  }
  document.getElementById('q1').classList.add('active');
  document.getElementById('progressFill').style.width = '0%';
  document.getElementById('ageInput').value   = '';
  document.getElementById('weightInput').value = '';
  document.querySelectorAll('.q-opt').forEach(o => o.classList.remove('selected-yes','selected-no'));
}

// If PHP returned a form error, skip quiz and show donation form directly
<?php if($error): ?>
  document.getElementById('quizPanel').style.display = 'none';
  setStep1Done();
  showDonationForm();
<?php endif; ?>

// ── Hospital Picker ──
function findHospitals() {
  if (!navigator.geolocation) { showHospStatus('⚠️ Geolocation not supported by your browser.', 'error'); return; }
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
          const elLat = el.lat??el.center?.lat, elLng = el.lon??el.center?.lon;
          return { ...el, dist: haversineKm(lat,lng,elLat,elLng) };
        }).sort((a,b) => a.dist - b.dist).slice(0,12);
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
    const msgs = {1:'🔒 Location denied. Please allow location or type manually.',2:'📡 Location unavailable.',3:'⏱ Timed out.'};
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
  document.getElementById('hospitalInput').value    = name;
  document.getElementById('hospSelectedName').textContent = name;
  document.getElementById('hospSelectedBox').classList.add('show');
  document.getElementById('manualHospital').value   = name;
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
  const R=6371, dLat=(lat2-lat1)*Math.PI/180, dLng=(lng2-lng1)*Math.PI/180;
  const a=Math.sin(dLat/2)**2+Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLng/2)**2;
  return R*2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));
}

function escH(s) { const d=document.createElement('div'); d.textContent=s; return d.innerHTML; }
</script>
</body>
</html>
