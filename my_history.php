<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

// ── Fetch this user's donation/request history ──
$stmt = $conn->prepare("SELECT * FROM donations WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$history         = [];
$total_donations = 0;
$total_requests  = 0;

while ($row = $result->fetch_assoc()) {
    $history[] = $row;
    if (strtolower($row['type']) === 'donation') $total_donations++;
    else $total_requests++;
}
$total = count($history);
$stmt->close();

// ── Fetch screening answers for each donation record ──
$screenings = [];
foreach ($history as $row) {
    if (!empty($row['id'])) {
        $sid = intval($row['id']);
        $sc_res = $conn->query(
            "SELECT * FROM donor_screening WHERE donation_id = $sid LIMIT 1"
        );
        if ($sc_res && $sc_res->num_rows > 0) {
            $screenings[$row['id']] = $sc_res->fetch_assoc();
        }
    }
}

$uname  = $_SESSION['name']  ?? $_SESSION['email'] ?? 'User';
$uemail = $_SESSION['email'] ?? '';

// ── Helper: format yes/no answer as readable label ──
function yn(string $val, bool $invertColor = false): string {
    $isYes = strtolower($val) === 'yes';
    // For "good" questions (feeling_well) yes=green; for "bad" questions yes=red
    if ($invertColor) {
        $cls   = $isYes ? 'sc-pass' : 'sc-fail';
        $label = $isYes ? '✓ Yes'   : '✗ No';
    } else {
        $cls   = $isYes ? 'sc-fail' : 'sc-pass';
        $label = $isYes ? '✗ Yes'   : '✓ No';
    }
    return "<span class=\"sc-pill $cls\">$label</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My History — Blood Donation</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#fff;
  --surface:rgba(255,255,255,0.85);
  --card:rgba(255,255,255,0.82);
  --card2:rgba(255,255,255,0.92);
  --border:rgba(0,0,0,.09);
  --border-s:rgba(0,0,0,.14);
  --red:#DC2626;--red-d:#B91C1C;
  --red-m:rgba(220,38,38,.08);
  --red-b:rgba(220,38,38,.22);
  --rose:#e05a5a;
  --blue-m:rgba(37,99,235,.08);
  --blue-b:rgba(37,99,235,.22);
  --blue:#2563EB;
  --green-m:rgba(22,163,74,.08);
  --green-b:rgba(22,163,74,.22);
  --green:#16A34A;
  --t:#1a1a2e;
  --t2:rgba(0,0,0,.62);
  --t3:rgba(0,0,0,.4);
  --t4:rgba(0,0,0,.25);
  --serif:'Cormorant Garamond',Georgia,serif;
  --sans:'Outfit',sans-serif;
  --sidebar:240px
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{
  font-family:var(--sans);
  background:linear-gradient(135deg,#ffffff 0%,#ffe4ec 50%,#ffc0cb 100%);
  background-size:300% 300%;
  animation:gradMove 12s ease infinite;
  color:var(--t);min-height:100vh
}
@keyframes gradMove{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
.bg-grid{position:fixed;inset:0;z-index:0;pointer-events:none;background-image:linear-gradient(rgba(0,0,0,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(0,0,0,.03) 1px,transparent 1px);background-size:72px 72px}
.g1{position:fixed;top:-300px;right:-200px;z-index:0;width:700px;height:700px;border-radius:50%;background:radial-gradient(circle,rgba(255,100,130,.2) 0%,transparent 60%);pointer-events:none}

/* ── SIDEBAR ── */
.sidebar{position:fixed;top:0;left:0;width:var(--sidebar);height:100%;background:rgba(255,255,255,.9);border-right:1px solid var(--border);z-index:200;display:flex;flex-direction:column;transform:translateX(-100%);transition:transform .3s ease;backdrop-filter:blur(20px)}
.sidebar.open{transform:translateX(0)}
.sb-head{padding:24px 20px;border-bottom:1px solid var(--border)}
.sb-logo{display:flex;align-items:center;gap:10px;text-decoration:none}
.sb-icon{width:30px;height:30px;background:var(--red);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px;box-shadow:0 3px 10px rgba(220,38,38,.35)}
.sb-text{font-family:var(--serif);font-size:18px;color:var(--t)}
.sb-user{margin:16px 12px 0;background:var(--red-m);border:1px solid var(--red-b);border-radius:10px;padding:12px 14px;display:flex;align-items:center;gap:10px}
.sb-avatar{width:32px;height:32px;background:var(--red);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
.sb-uname{font-size:13px;font-weight:500;color:var(--t);display:block}
.sb-urole{font-size:11px;color:var(--rose);margin-top:1px}
.sb-nav{flex:1;padding:16px 12px;overflow-y:auto}
.sb-section{font-size:10px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:var(--t4);padding:0 8px;margin:16px 0 8px}
.sb-section:first-child{margin-top:0}
.sb-link{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:9px;font-size:13px;font-weight:400;color:var(--t2);text-decoration:none;transition:all .18s;margin-bottom:2px}
.sb-link:hover{background:rgba(220,38,38,.06);color:var(--t)}
.sb-link.active{background:var(--red-m);border:1px solid var(--red-b);color:var(--red)}
.sb-link.danger{color:rgba(180,30,30,.5)}
.sb-link.danger:hover{background:rgba(220,38,38,.08);color:var(--red)}
.sb-link-icon{width:18px;text-align:center;font-size:15px}
.sb-foot{padding:16px 12px;border-top:1px solid var(--border)}
.sb-logout{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:9px;font-size:13px;color:var(--t3);text-decoration:none;transition:all .18s}
.sb-logout:hover{background:rgba(0,0,0,.04);color:var(--t)}

/* ── TOPBAR ── */
.topbar{position:sticky;top:0;z-index:100;height:64px;display:flex;align-items:center;justify-content:space-between;padding:0 28px;background:rgba(255,255,255,.82);border-bottom:1px solid var(--border);backdrop-filter:blur(20px)}
.tb-left{display:flex;align-items:center;gap:14px}
.menu-btn{background:rgba(0,0,0,.04);border:1px solid var(--border);color:var(--t);width:36px;height:36px;border-radius:9px;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .18s;flex-shrink:0}
.menu-btn:hover{background:rgba(0,0,0,.08)}
.tb-title{font-family:var(--serif);font-size:20px;color:var(--t)}
.tb-right{display:flex;align-items:center;gap:10px}
.tb-badge{display:flex;align-items:center;gap:7px;background:var(--red-m);border:1px solid var(--red-b);color:var(--red);font-size:13px;padding:7px 14px;border-radius:100px}
.tb-dot{width:6px;height:6px;background:var(--red);border-radius:50%;animation:pdot 2.5s ease-in-out infinite}
@keyframes pdot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.3;transform:scale(.6)}}

/* ── CONTENT ── */
.content{position:relative;z-index:1;padding:32px 32px 48px;max-width:1400px}
.page-hd{margin-bottom:28px;display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:12px}
.page-hd-l h1{font-family:var(--serif);font-size:32px;font-weight:400;color:var(--t);margin-bottom:4px}
.page-hd-l p{font-size:14px;font-weight:300;color:var(--t3)}
.page-hd-r{font-size:13px;color:var(--t3)}

/* ── STATS ── */
.stats-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin-bottom:28px}
.scard{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px 16px;text-align:center;transition:all .2s;cursor:default;backdrop-filter:blur(10px);box-shadow:0 2px 12px rgba(0,0,0,.05)}
.scard:hover{border-color:var(--red-b);background:var(--card2);transform:translateY(-1px);box-shadow:0 6px 20px rgba(200,50,80,.1)}
.scard.feat{border-color:var(--red-b);background:linear-gradient(135deg,rgba(220,38,38,.08),rgba(255,200,210,.15))}
.scard.blue{border-color:var(--blue-b);background:linear-gradient(135deg,rgba(37,99,235,.06),rgba(200,215,255,.15))}
.scard.green{border-color:var(--green-b);background:linear-gradient(135deg,rgba(22,163,74,.06),rgba(200,255,220,.15))}
.scard h3{font-size:10px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--t4);margin-bottom:10px}
.scard h2{font-family:var(--serif);font-size:36px;font-weight:400;color:var(--t);line-height:1}
.scard.feat h2{color:var(--red)}
.scard.blue h2{color:var(--blue)}
.scard.green h2{color:var(--green)}
.scard-sub{font-size:11px;color:var(--t4);margin-top:4px}

/* ── FILTER TABS ── */
.filter-row{display:flex;align-items:center;gap:8px;margin-bottom:20px;flex-wrap:wrap}
.filter-btn{padding:7px 16px;border-radius:100px;font-size:13px;font-weight:500;border:1px solid var(--border);background:var(--card);color:var(--t2);cursor:pointer;transition:all .18s;font-family:var(--sans)}
.filter-btn:hover{border-color:var(--red-b);color:var(--red)}
.filter-btn.active{background:var(--red-m);border-color:var(--red-b);color:var(--red)}

/* ── TABLE BOX ── */
.tbox{background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden;backdrop-filter:blur(10px);box-shadow:0 4px 20px rgba(0,0,0,.06)}
.tbox-head{padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px}
.tbox-head h2{font-family:var(--serif);font-size:20px;color:var(--t)}
.tbox-meta{font-size:12px;color:var(--t4);display:flex;align-items:center;gap:6px}
.meta-dot{width:5px;height:5px;background:#16a34a;border-radius:50%;box-shadow:0 0 6px #16a34a}
.twrap{overflow-x:auto}
table{width:100%;border-collapse:collapse}
thead tr{border-bottom:1px solid var(--border)}
th{padding:12px 18px;text-align:left;font-size:10px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--t4);white-space:nowrap}
td{padding:14px 18px;font-size:14px;color:var(--t2);border-bottom:1px solid rgba(0,0,0,.04);white-space:nowrap}
tbody tr:last-child td{border-bottom:none}
tbody tr{transition:background .15s}
tbody tr:hover td{background:rgba(220,38,38,.03);color:var(--t)}
.td-id{color:var(--t4);font-size:12px;font-family:monospace}
.td-name{font-weight:500;color:var(--t)!important}

/* ── BADGES ── */
.badge{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:4px 11px;border-radius:6px;letter-spacing:.3px}
.badge-donation{background:var(--red-m);border:1px solid var(--red-b);color:var(--red)}
.badge-request{background:var(--blue-m);border:1px solid var(--blue-b);color:var(--blue)}
.badge-blood{background:var(--red-m);border:1px solid var(--red-b);color:var(--red)}
.td-ticket{font-family:monospace;font-size:12px;color:var(--t3)}
.td-date{font-size:12px;color:var(--t4)}

/* ── STATUS ── */
.status{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;padding:4px 11px;border-radius:100px}
.status-pending{background:rgba(202,138,4,.1);border:1px solid rgba(202,138,4,.3);color:#B45309}
.status-approved{background:var(--green-m);border:1px solid var(--green-b);color:var(--green)}
.status-completed{background:rgba(107,114,128,.1);border:1px solid rgba(107,114,128,.3);color:#4B5563}

/* ── SCREENING VIEW BUTTON ── */
.sc-btn{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:7px;font-size:11px;font-weight:600;border:1px solid var(--border-s);background:rgba(255,255,255,.6);color:var(--t3);cursor:pointer;transition:all .18s;font-family:var(--sans)}
.sc-btn:hover{border-color:var(--red-b);color:var(--red);background:var(--red-m)}
.sc-none{font-size:11px;color:var(--t4);font-style:italic}

/* ── EMPTY STATE ── */
.empty-state{padding:72px 24px;text-align:center}
.empty-icon{font-size:48px;margin-bottom:16px;opacity:.4}
.empty-state h3{font-family:var(--serif);font-size:22px;color:var(--t2);margin-bottom:8px}
.empty-state p{font-size:14px;color:var(--t4);max-width:320px;margin:0 auto 20px}
.empty-cta{display:inline-flex;align-items:center;gap:8px;background:var(--red);color:#fff;padding:10px 22px;border-radius:10px;text-decoration:none;font-size:13px;font-weight:500;transition:background .18s}
.empty-cta:hover{background:var(--red-d)}

/* ── OVERLAY ── */
.sb-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.25);z-index:199;backdrop-filter:blur(2px)}
.sb-overlay.open{display:block}

/* ── SCREENING MODAL ── */
.modal-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:900;backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:24px}
.modal-backdrop.open{display:flex}
.modal{background:#fff;border-radius:20px;width:100%;max-width:560px;max-height:88vh;overflow-y:auto;box-shadow:0 32px 80px rgba(0,0,0,.22);position:relative}
.modal::-webkit-scrollbar{width:4px}
.modal::-webkit-scrollbar-thumb{background:var(--border-s);border-radius:4px}
.modal-head{padding:24px 28px 18px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;justify-content:space-between;gap:12px;position:sticky;top:0;background:#fff;z-index:1}
.modal-head h2{font-family:var(--serif);font-size:22px;color:var(--t);margin-bottom:2px}
.modal-head p{font-size:12px;color:var(--t3);font-weight:300}
.modal-close{background:rgba(0,0,0,.06);border:none;width:30px;height:30px;border-radius:8px;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .18s;flex-shrink:0;color:var(--t2)}
.modal-close:hover{background:rgba(220,38,38,.1);color:var(--red)}
.modal-body{padding:20px 28px 28px}

/* Screening sections */
.sc-section{margin-bottom:22px}
.sc-section-title{font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--t3);padding-bottom:8px;border-bottom:1px solid var(--border);margin-bottom:12px}
.sc-row{display:flex;align-items:center;justify-content:space-between;padding:7px 0;border-bottom:1px dashed rgba(0,0,0,.05);gap:12px}
.sc-row:last-child{border-bottom:none}
.sc-label{font-size:13px;color:var(--t2);flex:1;line-height:1.4}
.sc-val{font-size:13px;font-weight:600;color:var(--t);white-space:nowrap}
.sc-pill{display:inline-flex;align-items:center;padding:3px 10px;border-radius:100px;font-size:11px;font-weight:700;letter-spacing:.3px}
.sc-pass{background:var(--green-m);border:1px solid var(--green-b);color:var(--green)}
.sc-fail{background:var(--red-m);border:1px solid var(--red-b);color:var(--red)}
.sc-eligible-bar{display:flex;align-items:center;justify-content:center;gap:10px;padding:14px;border-radius:12px;margin-bottom:20px;font-size:14px;font-weight:600}
.sc-eligible-bar.pass{background:var(--green-m);border:1px solid var(--green-b);color:var(--green)}
.sc-eligible-bar.fail{background:var(--red-m);border:1px solid var(--red-b);color:var(--red)}

@media(max-width:768px){
    .content{padding:20px 16px}
    .stats-row{grid-template-columns:repeat(3,1fr)}
    .topbar{padding:0 16px}
    td,th{padding:10px 12px}
    .modal{border-radius:14px}
    .modal-head,.modal-body{padding-left:18px;padding-right:18px}
}
</style>
</head>
<body>
<div class="bg-grid"></div>
<div class="g1"></div>

<div id="sbOverlay" class="sb-overlay" onclick="closeSB()"></div>

<!-- ── SIDEBAR ── -->
<div id="sidebar" class="sidebar">
    <div class="sb-head">
        <a class="sb-logo" href="#">
            <div class="sb-icon">🩸</div>
            <span class="sb-text">Blood Donation</span>
        </a>
        <div class="sb-user" style="margin-top:14px">
            <div class="sb-avatar">👤</div>
            <div>
                <span class="sb-uname"><?= htmlspecialchars($uname) ?></span>
                <div class="sb-urole">Member</div>
            </div>
        </div>
    </div>
    <div class="sb-nav">
        <div class="sb-section">Navigation</div>
        <a href="user_dashboard.php" class="sb-link"><span class="sb-link-icon">🏠</span>Home</a>
        <a href="donate_form.php" class="sb-link"><span class="sb-link-icon">🩸</span>Donate Blood</a>
        <a href="request_blood_form.php" class="sb-link"><span class="sb-link-icon">📋</span>Request Blood</a>
        <a href="my_history.php" class="sb-link active"><span class="sb-link-icon">📂</span>My History</a>
        <div class="sb-section">Account</div>
        <a href="edit_account.php" class="sb-link"><span class="sb-link-icon">✏️</span>Edit Account</a>
        <a href="delete_account.php" class="sb-link danger" onclick="return confirm('Are you sure you want to permanently delete your account?')"><span class="sb-link-icon">🗑</span>Delete Account</a>
    </div>
    <div class="sb-foot">
        <a href="logout.php" class="sb-logout"><span class="sb-link-icon">🚪</span>Sign Out</a>
    </div>
</div>

<!-- ── TOPBAR ── -->
<div class="topbar">
    <div class="tb-left">
        <button class="menu-btn" onclick="toggleSB()">☰</button>
        <span class="tb-title">My History</span>
    </div>
    <div class="tb-right">
        <div class="tb-badge"><div class="tb-dot"></div><?= htmlspecialchars($uname) ?></div>
    </div>
</div>

<!-- ── CONTENT ── -->
<div class="content">
    <div class="page-hd">
        <div class="page-hd-l">
            <h1>My Donation & Request History</h1>
            <p>Only your personal records are shown here — private to you</p>
        </div>
        <div class="page-hd-r"><?= date('F j, Y') ?></div>
    </div>

    <!-- STATS -->
    <div class="stats-row">
        <div class="scard feat">
            <h3>Total Records</h3>
            <h2><?= $total ?></h2>
            <div class="scard-sub">All time</div>
        </div>
        <div class="scard green">
            <h3>Donations</h3>
            <h2><?= $total_donations ?></h2>
            <div class="scard-sub">Blood donated</div>
        </div>
        <div class="scard blue">
            <h3>Requests</h3>
            <h2><?= $total_requests ?></h2>
            <div class="scard-sub">Blood requested</div>
        </div>
    </div>

    <!-- FILTER TABS -->
    <div class="filter-row">
        <button class="filter-btn active" onclick="filterTable('all', this)">All Records</button>
        <button class="filter-btn" onclick="filterTable('donation', this)">🩸 Donations</button>
        <button class="filter-btn" onclick="filterTable('request', this)">📋 Requests</button>
    </div>

    <!-- TABLE -->
    <div class="tbox">
        <div class="tbox-head">
            <h2>History</h2>
            <div class="tbox-meta">
                <div class="meta-dot"></div>
                <span id="recordCount"><?= $total ?></span> records · your data only
            </div>
        </div>
        <div class="twrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Full Name</th>
                        <th>Blood Type</th>
                        <th>Ticket ID</th>
                        <th>Hospital</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Screening</th>
                    </tr>
                </thead>
                <tbody id="historyBody">
                <?php if ($total === 0): ?>
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <div class="empty-icon">🩸</div>
                                <h3>No records yet</h3>
                                <p>You haven't donated or requested blood yet. Your history will appear here once you do.</p>
                                <a href="donate_form.php" class="empty-cta">🩸 Make a Donation</a>
                            </div>
                        </td>
                    </tr>
                <?php else: foreach ($history as $i => $row):
                    $type        = strtolower($row['type'] ?? 'donation');
                    $status      = strtolower($row['status'] ?? 'pending');
                    $badgeClass  = $type === 'donation' ? 'badge-donation' : 'badge-request';
                    $typeLabel   = $type === 'donation'  ? '🩸 Donation' : '📋 Request';
                    $statusClass = match($status) {
                        'approved'  => 'status-approved',
                        'completed' => 'status-completed',
                        default     => 'status-pending',
                    };
                    $statusLabel = match($status) {
                        'approved'  => '✓ Approved',
                        'completed' => '✔ Completed',
                        default     => '⏳ Pending',
                    };
                    $hasScreening = isset($screenings[$row['id']]);
                ?>
                    <tr data-type="<?= htmlspecialchars($type) ?>">
                        <td class="td-id"><?= $i + 1 ?></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= $typeLabel ?></span></td>
                        <td class="td-name"><?= htmlspecialchars($row['fullname'] ?? '') ?></td>
                        <td><span class="badge badge-blood"><?= htmlspecialchars($row['blood_type'] ?? '—') ?></span></td>
                        <td class="td-ticket"><?= htmlspecialchars($row['ticket_id'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['hospital'] ?? '—') ?></td>
                        <td class="td-date"><?= isset($row['created_at']) ? date('M j, Y', strtotime($row['created_at'])) : '—' ?></td>
                        <td><span class="status <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                        <td>
                          <?php if ($hasScreening): ?>
                            <button class="sc-btn" onclick="openScreeningModal(<?= $row['id'] ?>)">
                              🩺 View
                            </button>
                          <?php else: ?>
                            <span class="sc-none">N/A</span>
                          <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div><!-- /content -->

<!-- ══════════════════════════════════════════════
     SCREENING MODALS (one per donation with screening data)
     ══════════════════════════════════════════════ -->
<?php foreach ($screenings as $did => $sc): ?>
<div class="modal-backdrop" id="scModal<?= $did ?>" onclick="closeModalOutside(event, <?= $did ?>)">
  <div class="modal" role="dialog" aria-label="Screening Details">
    <div class="modal-head">
      <div>
        <h2>🩺 Screening Details</h2>
        <p>Eligibility quiz answers recorded at the time of this donation</p>
      </div>
      <button class="modal-close" onclick="closeModal(<?= $did ?>)">✕</button>
    </div>
    <div class="modal-body">

      <!-- Eligibility result banner -->
      <div class="sc-eligible-bar <?= strtolower($sc['eligible'] ?? 'yes') === 'yes' ? 'pass' : 'fail' ?>">
        <?= strtolower($sc['eligible'] ?? 'yes') === 'yes'
            ? '✓ Passed All 15 Eligibility Checks'
            : '✗ One or More Checks Not Met' ?>
      </div>

      <!-- Section A: Vitals -->
      <div class="sc-section">
        <div class="sc-section-title">Section A — Vitals</div>
        <div class="sc-row">
          <span class="sc-label">Age at time of donation</span>
          <span class="sc-val">
            <?php if (!empty($sc['sc_age'])): ?>
              <span class="sc-pill <?= ($sc['sc_age'] >= 17 && $sc['sc_age'] <= 65) ? 'sc-pass' : 'sc-fail' ?>">
                <?= intval($sc['sc_age']) ?> yrs
              </span>
            <?php else: ?><span class="sc-pill">—</span><?php endif; ?>
          </span>
        </div>
        <div class="sc-row">
          <span class="sc-label">Body weight</span>
          <span class="sc-val">
            <?php if (!empty($sc['sc_weight'])): ?>
              <span class="sc-pill <?= ($sc['sc_weight'] >= 50) ? 'sc-pass' : 'sc-fail' ?>">
                <?= number_format($sc['sc_weight'], 1) ?> kg
              </span>
            <?php else: ?><span class="sc-pill">—</span><?php endif; ?>
          </span>
        </div>
      </div>

      <!-- Section B: Donation History -->
      <div class="sc-section">
        <div class="sc-section-title">Section B — Donation History</div>
        <div class="sc-row">
          <span class="sc-label">Donated blood within last 56 days?</span>
          <span class="sc-val"><?= yn($sc['sc_last_donated'] ?? '') ?></span>
        </div>
      </div>

      <!-- Section C: Current Health -->
      <div class="sc-section">
        <div class="sc-section-title">Section C — Current Health</div>
        <div class="sc-row">
          <span class="sc-label">Feeling well on donation day (no fever, cold, or illness)?</span>
          <span class="sc-val"><?= yn($sc['sc_feeling_well'] ?? '', true) ?></span>
        </div>
      </div>

      <!-- Section D: Medical History -->
      <div class="sc-section">
        <div class="sc-section-title">Section D — Medical History</div>
        <div class="sc-row">
          <span class="sc-label">History of heart disease, high blood pressure, or stroke?</span>
          <span class="sc-val"><?= yn($sc['sc_heart_condition'] ?? '') ?></span>
        </div>
        <div class="sc-row">
          <span class="sc-label">Insulin-dependent or uncontrolled diabetes?</span>
          <span class="sc-val"><?= yn($sc['sc_diabetes'] ?? '') ?></span>
        </div>
        <div class="sc-row">
          <span class="sc-label">Tested positive for Hepatitis B/C, HIV/AIDS, or syphilis?</span>
          <span class="sc-val"><?= yn($sc['sc_hepatitis_hiv'] ?? '') ?></span>
        </div>
        <div class="sc-row">
          <span class="sc-label">Active cancer or blood disorder (e.g., leukemia, sickle cell)?</span>
          <span class="sc-val"><?= yn($sc['sc_active_cancer'] ?? '') ?></span>
        </div>
      </div>

      <!-- Section E: Travel -->
      <div class="sc-section">
        <div class="sc-section-title">Section E — Travel History</div>
        <div class="sc-row">
          <span class="sc-label">Traveled to malaria, dengue, or Zika-endemic area in last 12 months?</span>
          <span class="sc-val"><?= yn($sc['sc_travel_endemic'] ?? '') ?></span>
        </div>
      </div>

      <!-- Section F: Lifestyle -->
      <div class="sc-section">
        <div class="sc-section-title">Section F — Lifestyle & Risk Behaviors</div>
        <div class="sc-row">
          <span class="sc-label">Tattoo, body piercing, or acupuncture in last 12 months?</span>
          <span class="sc-val"><?= yn($sc['sc_tattoo_piercing'] ?? '') ?></span>
        </div>
        <div class="sc-row">
          <span class="sc-label">Non-prescribed intravenous (IV) drug use?</span>
          <span class="sc-val"><?= yn($sc['sc_iv_drugs'] ?? '') ?></span>
        </div>
      </div>

      <!-- Section G: Recent Procedures -->
      <div class="sc-section">
        <div class="sc-section-title">Section G — Recent Procedures</div>
        <div class="sc-row">
          <span class="sc-label">Surgery, major dental work, or blood transfusion in last 12 months?</span>
          <span class="sc-val"><?= yn($sc['sc_recent_procedure'] ?? '') ?></span>
        </div>
      </div>

      <!-- Section H: Pregnancy -->
      <div class="sc-section">
        <div class="sc-section-title">Section H — Pregnancy & Maternal Health</div>
        <div class="sc-row">
          <span class="sc-label">Pregnant, gave birth within 6 months, or breastfeeding?</span>
          <span class="sc-val"><?= yn($sc['sc_pregnant'] ?? '') ?></span>
        </div>
      </div>

      <!-- Section I: Medications -->
      <div class="sc-section">
        <div class="sc-section-title">Section I — Medications</div>
        <div class="sc-row">
          <span class="sc-label">Currently on antibiotics, blood thinners, or isotretinoin (Accutane)?</span>
          <span class="sc-val"><?= yn($sc['sc_medications'] ?? '') ?></span>
        </div>
        <div class="sc-row">
          <span class="sc-label">Received any vaccine in the last 4 weeks?</span>
          <span class="sc-val"><?= yn($sc['sc_recent_vaccine'] ?? '') ?></span>
        </div>
      </div>

      <div style="font-size:11px;color:var(--t4);text-align:center;margin-top:4px">
        Recorded: <?= isset($sc['created_at']) ? date('F j, Y · g:i A', strtotime($sc['created_at'])) : '—' ?>
      </div>

    </div><!-- /modal-body -->
  </div><!-- /modal -->
</div><!-- /modal-backdrop -->
<?php endforeach; ?>

<script>
function toggleSB() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sbOverlay').classList.toggle('open');
}
function closeSB() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sbOverlay').classList.remove('open');
}

function filterTable(type, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const rows = document.querySelectorAll('#historyBody tr[data-type]');
    let visible = 0;
    rows.forEach(row => {
        if (type === 'all' || row.dataset.type === type) {
            row.style.display = ''; visible++;
        } else {
            row.style.display = 'none';
        }
    });
    document.getElementById('recordCount').textContent = visible;
}

function openScreeningModal(id) {
    const modal = document.getElementById('scModal' + id);
    if (modal) { modal.classList.add('open'); document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
    const modal = document.getElementById('scModal' + id);
    if (modal) { modal.classList.remove('open'); document.body.style.overflow = ''; }
}
function closeModalOutside(event, id) {
    if (event.target === event.currentTarget) closeModal(id);
}
// Close any open modal on Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-backdrop.open').forEach(m => {
            m.classList.remove('open');
            document.body.style.overflow = '';
        });
    }
});
</script>
</body>
</html>
