<?php
session_start();
include 'db.php';
if(!isset($_SESSION['admin'])){ header("Location: login.php"); exit(); }

// Delete record
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM donations WHERE id=$id");
    header("Location: dashboard.php"); exit();
}

// ── Fetch all records from donations table (single source of truth) ──
$res            = $conn->query("SELECT d.*, u.email as user_email FROM donations d LEFT JOIN users u ON d.user_id = u.id ORDER BY d.id DESC");
$total          = $conn->query("SELECT COUNT(*) as t FROM donations")->fetch_assoc()['t'];
$totalDonations = $conn->query("SELECT COUNT(*) as t FROM donations WHERE type='donation'")->fetch_assoc()['t'];
$totalRequests  = $conn->query("SELECT COUNT(*) as t FROM donations WHERE type='request'")->fetch_assoc()['t'];
$bloodCounts    = [];
$r              = $conn->query("SELECT blood_type, COUNT(*) as total FROM donations GROUP BY blood_type");

while($row = $r->fetch_assoc()) $bloodCounts[$row['blood_type']] = $row['total'];
$allTypes = ['O+','O-','A+','A-','B+','B-','AB+','AB-'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Blood Donation</title>
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
.stats-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:12px;margin-bottom:28px}
.scard{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px 16px;text-align:center;transition:all .2s;cursor:default;backdrop-filter:blur(10px);box-shadow:0 2px 12px rgba(0,0,0,.05)}
.scard:hover{border-color:var(--red-b);background:var(--card2);transform:translateY(-1px);box-shadow:0 6px 20px rgba(200,50,80,.1)}
.scard.feat{border-color:var(--red-b);background:linear-gradient(135deg,rgba(220,38,38,.08),rgba(255,200,210,.15))}
.scard.blue{border-color:var(--blue-b);background:linear-gradient(135deg,rgba(37,99,235,.06),rgba(200,215,255,.15))}
.scard.green{border-color:var(--green-b);background:linear-gradient(135deg,rgba(22,163,74,.06),rgba(200,255,220,.15))}
.scard h3{font-size:10px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--t4);margin-bottom:10px}
.scard h2{font-family:var(--serif);font-size:32px;font-weight:400;color:var(--t);line-height:1}
.scard.feat h2{font-size:38px;color:var(--red)}
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

/* Badges */
.badge{display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:6px;letter-spacing:.3px}
.badge-blood{background:var(--red-m);border:1px solid var(--red-b);color:var(--red)}
.badge-donation{background:var(--green-m);border:1px solid var(--green-b);color:var(--green)}
.badge-request{background:var(--blue-m);border:1px solid var(--blue-b);color:var(--blue)}

.td-ticket{font-family:monospace;font-size:12px;color:var(--t3)}
.td-date{font-size:12px;color:var(--t4)}
.td-email{font-size:12px;color:var(--t3)}

.del-btn{display:inline-flex;align-items:center;gap:5px;background:rgba(220,38,38,.06);color:rgba(180,30,30,.7);border:1px solid rgba(220,38,38,.18);padding:6px 13px;border-radius:7px;font-size:12px;text-decoration:none;transition:all .18s;font-family:var(--sans)}
.del-btn:hover{background:rgba(220,38,38,.12);color:var(--red);border-color:var(--red-b)}
.empty-state{padding:60px 24px;text-align:center}
.empty-state p{font-size:14px;color:var(--t4)}

/* ── OVERLAY ── */
.sb-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.25);z-index:199;backdrop-filter:blur(2px)}
.sb-overlay.open{display:block}

/* ── TICKET SEARCH BAR ── */
.search-box{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:20px 24px;margin-bottom:24px;backdrop-filter:blur(10px);box-shadow:0 4px 20px rgba(0,0,0,.05)}
.search-box-title{font-family:var(--serif);font-size:18px;color:var(--t);margin-bottom:4px}
.search-box-sub{font-size:12px;color:var(--t3);margin-bottom:16px}
.search-row{display:flex;gap:10px;align-items:center}
.search-input-wrap{position:relative;flex:1}
.search-input-wrap .s-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:15px;pointer-events:none}
.search-input{width:100%;padding:11px 14px 11px 40px;background:rgba(255,255,255,.8);border:1.5px solid var(--border-s);border-radius:10px;color:var(--t);font-family:var(--sans);font-size:14px;outline:none;transition:all .2s;letter-spacing:.4px}
.search-input:focus{border-color:rgba(220,38,38,.45);background:#fff;box-shadow:0 0 0 3px rgba(220,38,38,.08)}
.search-input::placeholder{color:rgba(0,0,0,.28);letter-spacing:0}
.search-btn{padding:11px 22px;background:var(--red);color:#fff;border:none;border-radius:10px;font-family:var(--sans);font-size:14px;font-weight:500;cursor:pointer;transition:all .2s;white-space:nowrap;box-shadow:0 3px 12px rgba(220,38,38,.28)}
.search-btn:hover{background:var(--red-d);transform:translateY(-1px);box-shadow:0 6px 18px rgba(220,38,38,.35)}
.search-clear{padding:11px 16px;background:rgba(0,0,0,.04);color:var(--t3);border:1px solid var(--border);border-radius:10px;font-family:var(--sans);font-size:13px;cursor:pointer;transition:all .2s;display:none}
.search-clear:hover{background:rgba(0,0,0,.08);color:var(--t)}
.search-msg{margin-top:12px;font-size:13px;display:none}
.search-msg.found{color:var(--green);display:flex;align-items:center;gap:6px}
.search-msg.notfound{color:var(--red);display:flex;align-items:center;gap:6px}

/* ── CLIENT MODAL ── */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:500;backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:20px}
.modal-overlay.open{display:flex;animation:mfadein .2s ease}
@keyframes mfadein{from{opacity:0}to{opacity:1}}
.modal{background:#fff;border-radius:20px;width:100%;max-width:520px;box-shadow:0 30px 80px rgba(0,0,0,.2);overflow:hidden;animation:mslide .25s ease}
@keyframes mslide{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.modal-head{padding:22px 28px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
.modal-head-l{}
.modal-htitle{font-family:var(--serif);font-size:22px;color:var(--t);margin-bottom:2px}
.modal-hsub{font-size:12px;color:var(--t3)}
.modal-close{width:32px;height:32px;background:rgba(0,0,0,.05);border:none;border-radius:8px;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .18s;color:var(--t2)}
.modal-close:hover{background:rgba(220,38,38,.1);color:var(--red)}
.modal-body{padding:24px 28px}
.modal-ticket-id{display:inline-flex;align-items:center;gap:8px;background:var(--red-m);border:1px solid var(--red-b);padding:6px 14px;border-radius:100px;margin-bottom:20px}
.modal-ticket-id span{font-size:11px;font-weight:600;color:var(--t3);text-transform:uppercase;letter-spacing:.8px}
.modal-ticket-id strong{font-family:monospace;font-size:15px;color:var(--red);font-weight:700;letter-spacing:.5px}
.modal-type-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 11px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:.5px;margin-left:8px}
.modal-type-badge.donation{background:rgba(22,163,74,.1);border:1px solid rgba(22,163,74,.25);color:var(--green)}
.modal-type-badge.request{background:var(--red-m);border:1px solid var(--red-b);color:var(--red)}
.modal-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.mfield{background:rgba(0,0,0,.025);border:1px solid var(--border);border-radius:10px;padding:12px 14px}
.mfield.full{grid-column:span 2}
.mfield-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.9px;color:var(--t4);margin-bottom:5px}
.mfield-val{font-size:14px;font-weight:500;color:var(--t);word-break:break-word}
.mfield-val.blood{font-size:18px;font-family:var(--serif);color:var(--red)}
.modal-foot{padding:16px 28px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px}
.mfoot-close{padding:9px 22px;background:rgba(0,0,0,.05);border:1px solid var(--border);border-radius:9px;font-family:var(--sans);font-size:13px;font-weight:500;color:var(--t2);cursor:pointer;transition:all .18s}
.mfoot-close:hover{background:rgba(0,0,0,.09)}
.mfoot-del{padding:9px 22px;background:rgba(220,38,38,.07);border:1px solid var(--red-b);border-radius:9px;font-family:var(--sans);font-size:13px;font-weight:500;color:var(--red);cursor:pointer;transition:all .18s;text-decoration:none;display:inline-flex;align-items:center;gap:5px}
.mfoot-del:hover{background:var(--red);color:#fff}

@media(max-width:768px){.content{padding:20px 16px}.stats-row{grid-template-columns:repeat(3,1fr)}.topbar{padding:0 16px}.search-row{flex-wrap:wrap}.modal-grid{grid-template-columns:1fr}.mfield.full{grid-column:span 1}}
</style>
</head>
<body>
<div class="bg-grid"></div><div class="g1"></div>

<div id="sbOverlay" class="sb-overlay" onclick="closeSB()"></div>

<!-- SIDEBAR -->
<div id="sidebar" class="sidebar">
    <div class="sb-head">
        <a class="sb-logo" href="#">
            <div class="sb-icon">🩸</div>
            <span class="sb-text">Blood Donation</span>
        </a>
        <div class="sb-user" style="margin-top:14px">
            <div class="sb-avatar">👤</div>
            <div><span class="sb-uname"><?=htmlspecialchars($_SESSION['admin'])?></span><div class="sb-urole">Administrator</div></div>
        </div>
    </div>
    <div class="sb-nav">
        <div class="sb-section">Navigation</div>
        <a href="dashboard.php" class="sb-link active"><span class="sb-link-icon"></span>Dashboard</a>
        <div class="sb-section">Account</div>
        <a href="edit_account.php" class="sb-link"><span class="sb-link-icon">️</span>Edit Account</a>
        <a href="delete_account.php" class="sb-link danger" onclick="return confirm('Are you sure you want to permanently delete your account?')"><span class="sb-link-icon"></span>Delete Account</a>
    </div>
    <div class="sb-foot">
        <a href="logout.php" class="sb-logout"><span class="sb-link-icon"></span>Sign Out</a>
    </div>
</div>

<!-- TOPBAR -->
<div class="topbar">
    <div class="tb-left">
        <button class="menu-btn" onclick="toggleSB()">☰</button>
        <span class="tb-title">Dashboard</span>
    </div>
    <div class="tb-right">
        <div class="tb-badge"><div class="tb-dot"></div><?=htmlspecialchars($_SESSION['admin'])?></div>
    </div>
</div>

<!-- CONTENT -->
<div class="content">
    <div class="page-hd">
        <div class="page-hd-l">
            <h1>All Donation & Request Records</h1>
            <p>Admin view — every user's history across the entire system</p>
        </div>
        <div class="page-hd-r"><?=date('F j, Y')?></div>
    </div>

    <!-- STATS -->
    <div class="stats-row">
        <div class="scard feat">
            <h3>Total Records</h3>
            <h2><?=$total?></h2>
            <div class="scard-sub">All users</div>
        </div>
        <div class="scard green">
            <h3>Donations</h3>
            <h2><?=$totalDonations?></h2>
            <div class="scard-sub">Blood donated</div>
        </div>
        <div class="scard blue">
            <h3>Requests</h3>
            <h2><?=$totalRequests?></h2>
            <div class="scard-sub">Blood requested</div>
        </div>
        <?php foreach($allTypes as $type): $c=$bloodCounts[$type]??0;?>
        <div class="scard">
            <h3><?=$type?></h3>
            <h2><?=$c?></h2>
        </div>
        <?php endforeach;?>
    </div>

    <!-- TICKET SEARCH -->
    <div class="search-box">
        <div class="search-box-title"> Search by Ticket ID</div>
        <div class="search-box-sub">Enter a ticket ID (e.g. BD-12345 or BR-12345) to pull up the client's full record</div>
        <div class="search-row">
            <div class="search-input-wrap">
                <span class="s-icon"></span>
                <input type="text" id="ticketSearchInput" class="search-input" placeholder="e.g. BD-12345 or BR-67890" oninput="onSearchInput()" onkeydown="if(event.key==='Enter') doSearch()">
            </div>
            <button class="search-btn" onclick="doSearch()">Search</button>
            <button class="search-clear" id="searchClearBtn" onclick="clearSearch()">✕ Clear</button>
        </div>
        <div class="search-msg" id="searchMsg"></div>
    </div>

    <!-- CLIENT INFO MODAL -->
    <div class="modal-overlay" id="clientModal" onclick="closeModalOutside(event)">
        <div class="modal" id="modalBox">
            <div class="modal-head">
                <div class="modal-head-l">
                    <div class="modal-htitle">Client Record</div>
                    <div class="modal-hsub">Full information for the searched ticket</div>
                </div>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body">
                <div style="display:flex;align-items:center;flex-wrap:wrap;gap:6px;margin-bottom:20px">
                    <div class="modal-ticket-id"><span>Ticket ID</span><strong id="mTicketId">—</strong></div>
                    <span class="modal-type-badge" id="mTypeBadge"></span>
                </div>
                <div class="modal-grid">
                    <div class="mfield full"><div class="mfield-label">Full Name</div><div class="mfield-val" id="mName">—</div></div>
                    <div class="mfield"><div class="mfield-label">Email</div><div class="mfield-val" id="mEmail">—</div></div>
                    <div class="mfield"><div class="mfield-label">Phone</div><div class="mfield-val" id="mPhone">—</div></div>
                    <div class="mfield"><div class="mfield-label">Date of Birth</div><div class="mfield-val" id="mBirthdate">—</div></div>
                    <div class="mfield"><div class="mfield-label">Blood Type</div><div class="mfield-val blood" id="mBlood">—</div></div>
                    <div class="mfield full"><div class="mfield-label">Address</div><div class="mfield-val" id="mAddress">—</div></div>
                    <div class="mfield"><div class="mfield-label">Hospital</div><div class="mfield-val" id="mHospital">—</div></div>
                    <div class="mfield"><div class="mfield-label">Date Submitted</div><div class="mfield-val" id="mDate">—</div></div>
                </div>
            </div>
            <div class="modal-foot">
                <button class="mfoot-close" onclick="closeModal()">Close</button>
                <a class="mfoot-del" id="mDelBtn" href="#">🗑 Delete Record</a>
            </div>
        </div>
    </div>

    <!-- FILTER TABS -->
    <div class="filter-row">
        <button class="filter-btn active" onclick="filterTable('all', this)">All Records</button>
        <button class="filter-btn" onclick="filterTable('donation', this)"> Donations</button>
        <button class="filter-btn" onclick="filterTable('request', this)"> Requests</button>
    </div>

    <!-- TABLE -->
    <div class="tbox">
        <div class="tbox-head">
            <h2>All Records</h2>
            <div class="tbox-meta"><div class="meta-dot"></div><span id="recordCount"><?=$total?></span> entries · live data</div>
        </div>
        <div class="twrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Blood</th>
                    <th>Ticket ID</th>
                    <th>Hospital</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="recordsBody">
            <?php if($total == 0):?>
            <tr><td colspan="9"><div class="empty-state"><p>No records found.</p></div></td></tr>
            <?php else: while($row = $res->fetch_assoc()):
                $type      = strtolower($row['type'] ?? 'donation');
                $typeLabel = $type === 'donation' ? ' Donation' : ' Request';
                $badgeClass = $type === 'donation' ? 'badge-donation' : 'badge-request';
                $rowDate   = isset($row['created_at']) ? date('M j, Y', strtotime($row['created_at'])) : '—';
            ?>
            <tr data-type="<?=htmlspecialchars($type)?>"
                data-id="<?=$row['id']?>"
                data-ticket="<?=htmlspecialchars($row['ticket_id'] ?? '')?>"
                data-name="<?=htmlspecialchars($row['fullname'] ?? '')?>"
                data-email="<?=htmlspecialchars($row['user_email'] ?? $row['email'] ?? '')?>"
                data-phone="<?=htmlspecialchars($row['phone'] ?? '')?>"
                data-birthdate="<?=htmlspecialchars($row['birthdate'] ?? '')?>"
                data-blood="<?=htmlspecialchars($row['blood_type'] ?? '')?>"
                data-address="<?=htmlspecialchars($row['address'] ?? '')?>"
                data-hospital="<?=htmlspecialchars($row['hospital'] ?? '')?>"
                data-date="<?=$rowDate?>">
                <td class="td-id">#<?=$row['id']?></td>
                <td><span class="badge <?=$badgeClass?>"><?=$typeLabel?></span></td>
                <td class="td-name"><?=htmlspecialchars($row['fullname'] ?? '')?></td>
                <td class="td-email"><?=htmlspecialchars($row['user_email'] ?? $row['email'] ?? '—')?></td>
                <td><span class="badge badge-blood"><?=htmlspecialchars($row['blood_type'] ?? '—')?></span></td>
                <td class="td-ticket"><?=htmlspecialchars($row['ticket_id'] ?? '—')?></td>
                <td><?=htmlspecialchars($row['hospital'] ?? '—')?></td>
                <td class="td-date"><?=isset($row['created_at']) ? date('M j, Y', strtotime($row['created_at'])) : '—'?></td>
                <td><a class="del-btn" href="?delete=<?=$row['id']?>" onclick="return confirm('Delete this record?')">🗑 Delete</a></td>
            </tr>
            <?php endwhile; endif;?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<script>
function toggleSB(){
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sbOverlay').classList.toggle('open');
}
function closeSB(){
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sbOverlay').classList.remove('open');
}
function filterTable(type, btn){
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const rows = document.querySelectorAll('#recordsBody tr[data-type]');
    let visible = 0;
    rows.forEach(row => {
        if(type === 'all' || row.dataset.type === type){
            row.style.display = '';
            visible++;
        } else {
            row.style.display = 'none';
        }
    });
    document.getElementById('recordCount').textContent = visible;
}

/* ── TICKET SEARCH ── */
function onSearchInput(){
    const val = document.getElementById('ticketSearchInput').value.trim();
    document.getElementById('searchClearBtn').style.display = val ? 'block' : 'none';
    // Hide previous message if user starts typing again
    const msg = document.getElementById('searchMsg');
    msg.className = 'search-msg';
    msg.innerHTML = '';
}

function doSearch(){
    const query = document.getElementById('ticketSearchInput').value.trim().toUpperCase();
    const msg   = document.getElementById('searchMsg');

    if(!query){
        msg.className = 'search-msg notfound';
        msg.innerHTML = '⚠ Please enter a ticket ID to search.';
        return;
    }

    const rows = document.querySelectorAll('#recordsBody tr[data-ticket]');
    let found = null;

    rows.forEach(row => {
        if(row.dataset.ticket.toUpperCase() === query) found = row;
    });

    if(!found){
        msg.className = 'search-msg notfound';
        msg.innerHTML = '✕ No record found for ticket <strong>' + query + '</strong>. Check the ID and try again.';
        return;
    }

    msg.className = 'search-msg found';
    msg.innerHTML = '✓ Record found — opening client details…';

    // Populate modal
    const d = found.dataset;
    document.getElementById('mTicketId').textContent   = d.ticket  || '—';
    document.getElementById('mName').textContent       = d.name    || '—';
    document.getElementById('mEmail').textContent      = d.email   || '—';
    document.getElementById('mPhone').textContent      = d.phone   || '—';
    document.getElementById('mBirthdate').textContent  = d.birthdate || '—';
    document.getElementById('mBlood').textContent      = d.blood   || '—';
    document.getElementById('mAddress').textContent    = d.address || '—';
    document.getElementById('mHospital').textContent   = d.hospital|| '—';
    document.getElementById('mDate').textContent       = d.date    || '—';

    // Type badge
    const badge = document.getElementById('mTypeBadge');
    const isDonation = d.type === 'donation';
    badge.textContent  = isDonation ? '🩸 Donation' : '🆘 Request';
    badge.className    = 'modal-type-badge ' + (isDonation ? 'donation' : 'request');

    // Delete link
    document.getElementById('mDelBtn').href = '?delete=' + d.id;
    document.getElementById('mDelBtn').onclick = function(){
        return confirm('Delete this record for ' + d.name + '?');
    };

    // Highlight the matched row briefly
    rows.forEach(r => r.style.background = '');
    found.style.background = 'rgba(220,38,38,.06)';
    found.scrollIntoView({behavior:'smooth', block:'center'});

    openModal();
}

function clearSearch(){
    document.getElementById('ticketSearchInput').value = '';
    document.getElementById('searchClearBtn').style.display = 'none';
    const msg = document.getElementById('searchMsg');
    msg.className = 'search-msg';
    msg.innerHTML = '';
    document.querySelectorAll('#recordsBody tr[data-ticket]').forEach(r => r.style.background = '');
}

function openModal(){
    document.getElementById('clientModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeModal(){
    document.getElementById('clientModal').classList.remove('open');
    document.body.style.overflow = '';
}
function closeModalOutside(e){
    if(e.target === document.getElementById('clientModal')) closeModal();
}
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeModal(); });
</script>
</body>
</html>