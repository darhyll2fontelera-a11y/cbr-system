<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: user_login.php");
    exit();
}

$username = $_SESSION['user'];
$user_id  = $_SESSION['user_id'];

// Fetch donations by user_id (secure — only THIS user's records)
$donations = [];
$stmt = $conn->prepare("SELECT * FROM donations WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $donations[] = $row;
}

$donation_count  = count($donations);
// Count by type
$donate_count    = count(array_filter($donations, fn($d) => ($d['type'] ?? 'donation') === 'donation'));
$request_count   = count(array_filter($donations, fn($d) => ($d['type'] ?? 'donation') === 'request'));
$lives_saved     = $donate_count * 3;

// Next eligible donation date (56 days after most recent donation)
$next_eligible = null;
$can_donate    = true;
$days_left     = 0;
// Find most recent actual donation (not request)
$donations_only = array_values(array_filter($donations, fn($d) => ($d['type'] ?? 'donation') === 'donation'));
if (count($donations_only) > 0 && !empty($donations_only[0]['created_at'])) {
    $today  = new DateTime();
    $nextDt = new DateTime($donations_only[0]['created_at']);
    $nextDt->modify('+56 days');
    $next_eligible = $nextDt->format('M j, Y');
    $can_donate    = $today >= $nextDt;
    $days_left     = $can_donate ? 0 : (int)$today->diff($nextDt)->days;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Dashboard — Blood Donation</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --card:rgba(255,255,255,0.85);
  --card2:rgba(255,255,255,0.92);
  --border:rgba(0,0,0,.09);
  --red:#DC2626;--red-d:#B91C1C;
  --red-m:rgba(220,38,38,.08);
  --red-b:rgba(220,38,38,.22);
  --blue:#2563EB;--blue-m:rgba(37,99,235,.08);--blue-b:rgba(37,99,235,.22);
  --t:#1a1a2e;
  --t2:rgba(0,0,0,.62);
  --t3:rgba(0,0,0,.4);
  --t4:rgba(0,0,0,.25);
  --serif:'Cormorant Garamond',Georgia,serif;
  --sans:'Outfit',sans-serif
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

/* TOPBAR */
.topbar{
  position:sticky;top:0;z-index:100;height:66px;
  display:flex;align-items:center;justify-content:space-between;padding:0 36px;
  background:rgba(255,255,255,.82);border-bottom:1px solid var(--border);backdrop-filter:blur(20px)
}
.tb-logo{display:flex;align-items:center;gap:11px;text-decoration:none}
.tb-icon{width:32px;height:32px;background:var(--red);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;box-shadow:0 3px 12px rgba(220,38,38,.32)}
.tb-text{font-family:var(--serif);font-size:19px;color:var(--t)}
.tb-right{display:flex;align-items:center;gap:10px}
.tb-user{display:flex;align-items:center;gap:8px;background:var(--red-m);border:1px solid var(--red-b);color:var(--red);font-size:13px;padding:7px 14px;border-radius:100px}
.tb-avatar{width:22px;height:22px;background:var(--red);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;color:#fff}
.logout-btn{font-size:13px;color:var(--t3);text-decoration:none;padding:7px 14px;border-radius:8px;transition:all .18s;border:1px solid var(--border)}
.logout-btn:hover{color:var(--red);border-color:var(--red-b);background:var(--red-m)}

/* CONTENT */
.content{position:relative;z-index:1;max-width:1100px;margin:0 auto;padding:40px 32px 60px}

.welcome-banner{
  background:var(--card);border:1px solid var(--border);border-radius:18px;
  padding:32px 36px;margin-bottom:28px;
  display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px;
  backdrop-filter:blur(16px);box-shadow:0 4px 20px rgba(200,50,80,.08)
}
.wb-left h1{font-family:var(--serif);font-size:32px;font-weight:400;color:var(--t);margin-bottom:6px}
.wb-left h1 em{font-style:italic;color:var(--red)}
.wb-left p{font-size:14px;color:var(--t3)}
.wb-right{display:flex;gap:10px;flex-wrap:wrap}
.wb-btn{display:inline-flex;align-items:center;gap:7px;text-decoration:none;padding:9px 18px;border-radius:9px;font-size:13px;font-weight:500;transition:all .2s}
.wb-btn.primary{background:var(--red);color:#fff;box-shadow:0 3px 12px rgba(220,38,38,.25)}
.wb-btn.primary:hover{background:var(--red-d);transform:translateY(-1px)}
.wb-btn.secondary{background:var(--blue-m);border:1px solid var(--blue-b);color:var(--blue)}
.wb-btn.secondary:hover{background:rgba(37,99,235,.14);transform:translateY(-1px)}
.wb-btn.outline{background:rgba(255,255,255,.7);border:1px solid var(--border);color:var(--t2)}
.wb-btn.outline:hover{border-color:var(--red-b);color:var(--red);background:var(--red-m)}

/* STATS */
.stats-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-bottom:28px}
.scard{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:22px 20px;backdrop-filter:blur(10px);box-shadow:0 2px 12px rgba(0,0,0,.05);transition:all .2s}
.scard:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(200,50,80,.1)}
.scard.feat{border-color:var(--red-b);background:linear-gradient(135deg,rgba(220,38,38,.07),rgba(255,200,210,.12))}
.scard.blue{border-color:var(--blue-b);background:linear-gradient(135deg,rgba(37,99,235,.06),rgba(200,210,255,.1))}
.scard h3{font-size:11px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--t4);margin-bottom:8px}
.scard h2{font-family:var(--serif);font-size:34px;font-weight:400;color:var(--t);line-height:1}
.scard.feat h2{color:var(--red)}
.scard.blue h2{color:var(--blue)}
.scard.green h2{color:#16a34a}
.scard-sub{font-size:12px;color:var(--t4);margin-top:4px}
.badge-eligible{display:inline-flex;align-items:center;gap:5px;background:rgba(22,163,74,.1);border:1px solid rgba(22,163,74,.25);color:#15803d;font-size:11px;font-weight:600;padding:3px 10px;border-radius:6px;margin-top:6px}
.badge-soon{background:rgba(234,179,8,.1);border-color:rgba(234,179,8,.3);color:#854d0e}

/* TABLE */
.tbox{background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden;backdrop-filter:blur(10px);box-shadow:0 4px 20px rgba(0,0,0,.06)}
.tbox-head{padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px}
.tbox-head h2{font-family:var(--serif);font-size:20px;color:var(--t)}
.tbox-meta{font-size:12px;color:var(--t4);display:flex;align-items:center;gap:6px}
.meta-dot{width:5px;height:5px;background:#16a34a;border-radius:50%;box-shadow:0 0 6px #16a34a}
.tbox-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.history-link{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.7);border:1px solid var(--border);color:var(--t2);text-decoration:none;padding:7px 14px;border-radius:8px;font-size:13px;font-weight:500;transition:all .2s}
.history-link:hover{border-color:var(--red-b);color:var(--red);background:var(--red-m)}
.donate-again{display:inline-flex;align-items:center;gap:6px;background:var(--red);color:#fff;text-decoration:none;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:500;box-shadow:0 3px 12px rgba(220,38,38,.25);transition:all .2s}
.donate-again:hover{background:var(--red-d);transform:translateY(-1px)}
.twrap{overflow-x:auto}
table{width:100%;border-collapse:collapse}
thead tr{border-bottom:1px solid var(--border)}
th{padding:12px 18px;text-align:left;font-size:10px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--t4);white-space:nowrap}
td{padding:14px 18px;font-size:14px;color:var(--t2);border-bottom:1px solid rgba(0,0,0,.04);white-space:nowrap}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover td{background:rgba(220,38,38,.03);color:var(--t)}
.bt{display:inline-flex;align-items:center;background:var(--red-m);border:1px solid var(--red-b);color:var(--red);font-size:11px;font-weight:700;padding:3px 10px;border-radius:6px}
.type-badge{display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:6px}
.type-badge.donation{background:var(--red-m);border:1px solid var(--red-b);color:var(--red)}
.type-badge.request{background:var(--blue-m);border:1px solid var(--blue-b);color:var(--blue)}
.td-ticket{font-family:monospace;font-size:12px;color:var(--t3)}
.td-date{font-size:12px;color:var(--t4)}
.td-num{font-size:12px;color:var(--t4);font-weight:600}
.td-name{font-weight:500;color:var(--t)}
.print-btn{display:inline-flex;align-items:center;gap:5px;font-size:11px;color:var(--t3);border:1px solid var(--border);padding:4px 10px;border-radius:6px;cursor:pointer;background:none;font-family:var(--sans);transition:all .18s}
.print-btn:hover{color:var(--red);border-color:var(--red-b);background:var(--red-m)}
.empty-state{padding:56px 24px;text-align:center}
.empty-state .empty-icon{font-size:40px;margin-bottom:12px}
.empty-state h3{font-family:var(--serif);font-size:22px;color:var(--t);margin-bottom:8px}
.empty-state p{font-size:14px;color:var(--t3);margin-bottom:20px}
.empty-btns{display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
.empty-btns a{display:inline-flex;align-items:center;gap:7px;text-decoration:none;padding:10px 22px;border-radius:9px;font-size:14px;font-weight:500;transition:all .2s}
.empty-btns a.r{background:var(--red);color:#fff;box-shadow:0 4px 14px rgba(220,38,38,.28)}
.empty-btns a.r:hover{background:var(--red-d)}
.empty-btns a.b{background:var(--blue-m);border:1px solid var(--blue-b);color:var(--blue)}
.empty-btns a.b:hover{background:rgba(37,99,235,.14)}
@media(max-width:700px){.content{padding:24px 16px}.welcome-banner{padding:24px 20px}.stats-row{grid-template-columns:1fr 1fr}}
</style>
</head>
<body>
<div class="bg-grid"></div><div class="g1"></div>

<div class="topbar">
  <a class="tb-logo" href="#">
    <div class="tb-icon">🩸</div>
    <span class="tb-text">Blood Donation</span>
  </a>
  <div class="tb-right">
    <div class="tb-user">
      <div class="tb-avatar">👤</div>
      <?=htmlspecialchars($username)?>
    </div>
    <a href="user_logout.php" class="logout-btn">Sign Out</a>
  </div>
</div>

<div class="content">

  <!-- WELCOME BANNER -->
  <div class="welcome-banner">
    <div class="wb-left">
      <h1>Hello, <em><?=htmlspecialchars($username)?></em></h1>
      <p>Welcome to your donor dashboard · <?=date('F j, Y')?></p>
    </div>
    <div class="wb-right">
      <a href="donate_form.php?type=donation" class="wb-btn primary"> Donate Blood</a>
      <a href="request_blood_form.php"  class="wb-btn secondary"> Request Blood</a>
      <a href="my_history.php"                class="wb-btn outline"> Full History</a>
    </div>
  </div>

  <!-- STATS -->
  <div class="stats-row">
    <div class="scard feat">
      <h3>Total Donations</h3>
      <h2><?=$donate_count?></h2>
      <div class="scard-sub">Blood donated</div>
    </div>
    <div class="scard blue">
      <h3>Blood Requests</h3>
      <h2><?=$request_count?></h2>
      <div class="scard-sub">Requests submitted</div>
    </div>
   
    <div class="scard">
      <h3>Next Eligible</h3>
      <?php if($next_eligible): ?>
        <h2 style="font-size:16px;margin-top:6px"><?=$next_eligible?></h2>
        <?php if($can_donate): ?>
          <span class="badge-eligible">✓ Eligible now</span>
        <?php else: ?>
          <span class="badge-eligible badge-soon"> <?=$days_left?> days left</span>
        <?php endif; ?>
      <?php else: ?>
        <h2 style="font-size:16px;margin-top:6px;color:#16a34a">Now</h2>
        <span class="badge-eligible">✓ Ready to donate</span>
      <?php endif; ?>
    </div>
    <div class="scard">
      <h3>Status</h3>
      <h2 style="font-size:22px;color:#16a34a;margin-top:4px">Active</h2>
      <div class="scard-sub">Account verified</div>
    </div>
  </div>

  <!-- HISTORY TABLE (recent 5) -->
  <div class="tbox">
    <div class="tbox-head">
      <h2>My Donation &amp; Request History</h2>
      <div class="tbox-actions">
        <div class="tbox-meta"><div class="meta-dot"></div><?=$donation_count?> record<?=$donation_count!==1?'s':''?></div>
        <?php if($donation_count > 0): ?>
          <a href="my_history.php" class="history-link"> View All</a>
        <?php endif; ?>
        <a href="donate_form.php" class="donate-again"> New Submission</a>
      </div>
    </div>
    <div class="twrap">
    <?php
    // Show only the 5 most recent on dashboard
    $recent = array_slice($donations, 0, 5);
    if($donation_count === 0): ?>
      <div class="empty-state">
        <div class="empty-icon">🩸</div>
        <h3>No records yet</h3>
        <p>You haven't submitted a donation or blood request yet.</p>
        <div class="empty-btns">
          <a href="donate_form.php?type=donation" class="r"> Donate Blood →</a>
          <a href="request_blood_form.php"  class="b"> Request Blood →</a>
        </div>
      </div>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Type</th>
          <th>Ticket ID</th>
          <th>Full Name</th>
          <th>Blood Type</th>
          <th>Hospital</th>
          <th>Date</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($recent as $i => $d):
        $t = $d['type'] ?? 'donation';
      ?>
        <tr>
          <td class="td-num"><?=($i+1)?></td>
          <td><span class="type-badge <?=$t?>"><?=$t==='request'?'🆘 Request':'🩸 Donation'?></span></td>
          <td class="td-ticket"><?=htmlspecialchars($d['ticket_id'])?></td>
          <td class="td-name"><?=htmlspecialchars($d['fullname'] ?? '—')?></td>
          <td><span class="bt"><?=htmlspecialchars($d['blood_type'])?></span></td>
          <td><?=htmlspecialchars($d['hospital'])?></td>
          <td class="td-date"><?=date('M j, Y', strtotime($d['created_at']))?></td>
          <td>
            <button class="print-btn" onclick="printTicket(<?=htmlspecialchars(json_encode($d))?>)">
              🖨 Ticket
            </button>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php if($donation_count > 5): ?>
    <div style="padding:14px 24px;border-top:1px solid var(--border);text-align:center">
      <a href="my_history.php" style="font-size:13px;color:var(--t3);text-decoration:none;transition:color .18s" onmouseover="this.style.color='#DC2626'" onmouseout="this.style.color=''">
        View all <?=$donation_count?> records in full history →
      </a>
    </div>
    <?php endif; ?>
    <?php endif; ?>
    </div>
  </div>

</div>

<script>
function printTicket(d) {
  const isRequest = d.type === 'request';
  const w = window.open('', '_blank', 'width=420,height=560');
  w.document.write(`
    <html><head><title>${isRequest ? 'Blood Request' : 'Donation'} Ticket</title>
    <style>
      body{font-family:'Segoe UI',sans-serif;margin:0;padding:32px;color:#1a1a2e;background:#fff}
      .t-header{text-align:center;margin-bottom:24px}
      .t-header h2{font-size:22px;margin-bottom:4px}
      .t-header p{font-size:13px;color:#888}
      .t-ticket{font-family:monospace;font-size:18px;font-weight:700;text-align:center;
        background:${isRequest ? '#eff6ff' : '#fff0f3'};
        border:2px dashed ${isRequest ? '#2563EB' : '#DC2626'};
        padding:12px;border-radius:8px;
        color:${isRequest ? '#2563EB' : '#DC2626'};margin-bottom:24px;letter-spacing:2px}
      .type-label{display:inline-block;text-align:center;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:700;margin-bottom:16px;
        background:${isRequest ? '#eff6ff' : '#fff0f3'};
        border:1px solid ${isRequest ? 'rgba(37,99,235,.3)' : 'rgba(220,38,38,.3)'};
        color:${isRequest ? '#2563EB' : '#DC2626'}}
      .t-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:14px}
      .t-row .lbl{color:#888;font-size:12px;text-transform:uppercase;letter-spacing:.5px}
      .bt{background:#fff0f3;border:1px solid rgba(220,38,38,.3);color:#DC2626;padding:2px 10px;border-radius:5px;font-weight:700;font-size:13px}
      .footer{text-align:center;font-size:12px;color:#aaa;margin-top:24px}
    </style></head><body>
    <div class="t-header">
      <h2>${isRequest ? '🆘 Blood Request' : '🩸 Blood Donation'}</h2>
      <p>${isRequest ? 'Blood Request Confirmation' : 'Donation Confirmation Ticket'}</p>
    </div>
    <div style="text-align:center"><span class="type-label">${isRequest ? '🆘 Blood Request' : '🩸 Blood Donation'}</span></div>
    <div class="t-ticket">${d.ticket_id}</div>
    <div class="t-row"><span class="lbl">Full Name</span><span>${d.fullname ?? '—'}</span></div>
    <div class="t-row"><span class="lbl">Blood Type</span><span class="bt">${d.blood_type}</span></div>
    <div class="t-row"><span class="lbl">Hospital</span><span>${d.hospital}</span></div>
    <div class="t-row"><span class="lbl">Date</span><span>${new Date(d.created_at).toLocaleDateString('en-US',{year:'numeric',month:'long',day:'numeric'})}</span></div>
    <div class="footer">Thank you · </div>
    <script>window.onload=()=>window.print()<\/script>
    </body></html>
  `);
  w.document.close();
}
</script>
</body>
</html>