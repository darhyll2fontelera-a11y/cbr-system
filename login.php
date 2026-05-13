<?php
session_start();
include 'db.php';
$error = "";
if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $u);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $admin = $res->fetch_assoc();
        if ($p === $admin['password']) { $_SESSION['admin'] = $u; header("Location: dashboard.php"); exit(); }
        else { $error = "Invalid username or password."; }
    } else { $error = "Invalid username or password."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — Blood Donation</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#fff;
  --card:rgba(255,255,255,0.88);
  --border:rgba(0,0,0,.09);
  --border-s:rgba(0,0,0,.14);
  --red:#DC2626;--red-d:#B91C1C;
  --red-m:rgba(220,38,38,.08);
  --red-b:rgba(220,38,38,.22);
  --rose:#e05a5a;
  --t:#1a1a2e;
  --t2:rgba(0,0,0,.62);
  --t3:rgba(0,0,0,.4);
  --serif:'Cormorant Garamond',Georgia,serif;
  --sans:'Outfit',sans-serif
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}
body{
  font-family:var(--sans);
  background:linear-gradient(135deg,#ffffff 0%,#ffe4ec 50%,#ffc0cb 100%);
  background-size:300% 300%;
  animation:gradMove 12s ease infinite;
  color:var(--t);
  display:flex;flex-direction:column;min-height:100vh
}
@keyframes gradMove{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
.bg-grid{position:fixed;inset:0;z-index:0;pointer-events:none;background-image:linear-gradient(rgba(0,0,0,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(0,0,0,.03) 1px,transparent 1px);background-size:72px 72px}
.g1{position:fixed;top:-300px;right:-200px;z-index:0;width:800px;height:800px;border-radius:50%;background:radial-gradient(circle,rgba(255,100,130,.18) 0%,transparent 60%);pointer-events:none}
.g2{position:fixed;bottom:-200px;left:-150px;z-index:0;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(255,160,180,.15) 0%,transparent 60%);pointer-events:none}
nav{position:relative;z-index:10;height:66px;display:flex;align-items:center;justify-content:space-between;padding:0 40px;border-bottom:1px solid var(--border);background:rgba(255,255,255,.75);backdrop-filter:blur(16px)}
.nlogo{display:flex;align-items:center;gap:11px;text-decoration:none}
.nicon{width:32px;height:32px;background:var(--red);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;box-shadow:0 3px 12px rgba(220,38,38,.35)}
.ntext{font-family:var(--serif);font-size:19px;color:var(--t)}
.nlink{font-size:14px;color:var(--t2);text-decoration:none;padding:6px 14px;border-radius:7px;transition:all .18s}
.nlink:hover{color:var(--red);background:rgba(220,38,38,.07)}
.pw{position:relative;z-index:1;flex:1;display:flex;align-items:center;justify-content:center;padding:48px 24px}
.split{display:flex;gap:72px;align-items:center;max-width:960px;width:100%}
.sl{flex:1}
.sr{flex-shrink:0;width:380px}
.pill{display:inline-flex;align-items:center;gap:7px;background:var(--red-m);border:1px solid var(--red-b);color:var(--red);font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;padding:5px 13px;border-radius:100px;margin-bottom:22px}
.pdot{width:5px;height:5px;background:var(--red);border-radius:50%;animation:pdot 2.5s ease-in-out infinite}
@keyframes pdot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.3;transform:scale(.6)}}
.sl h1{font-family:var(--serif);font-size:clamp(38px,5vw,66px);font-weight:400;line-height:1.03;color:var(--t);margin-bottom:16px}
.sl h1 em{font-style:italic;color:var(--red);display:block}
.sl p{font-size:15px;font-weight:300;color:var(--t2);line-height:1.8;margin-bottom:32px}
.badge{display:flex;align-items:flex-start;gap:12px;background:rgba(255,255,255,.6);border:1px solid var(--border);border-radius:12px;padding:16px 18px;backdrop-filter:blur(8px)}
.badge-icon{font-size:22px;margin-top:1px}
.badge-text{font-size:13px;color:var(--t3);line-height:1.6}
.badge-text strong{color:var(--t2);font-weight:500;display:block;margin-bottom:2px}
.card{background:var(--card);border:1px solid var(--border);border-radius:18px;padding:38px;box-shadow:0 20px 60px rgba(200,50,80,.1),0 2px 12px rgba(0,0,0,.06);animation:cin .45s ease both;backdrop-filter:blur(20px)}
@keyframes cin{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
.ctitle{font-family:var(--serif);font-size:26px;color:var(--t);margin-bottom:4px}
.csub{font-size:13px;font-weight:300;color:var(--t3);margin-bottom:28px}
.field{margin-bottom:16px}
.field label{display:block;font-size:11px;font-weight:600;color:var(--t3);letter-spacing:.8px;text-transform:uppercase;margin-bottom:7px}
.field input{width:100%;padding:12px 15px;background:rgba(255,255,255,.7);border:1px solid var(--border-s);border-radius:9px;color:var(--t);font-family:var(--sans);font-size:14px;outline:none;transition:all .2s}
.field input:focus{border-color:rgba(220,38,38,.5);background:#fff;box-shadow:0 0 0 3px rgba(220,38,38,.08)}
.field input::placeholder{color:rgba(0,0,0,.28)}
.aerr{padding:11px 15px;border-radius:9px;font-size:13px;margin-bottom:18px;display:flex;align-items:center;gap:8px;background:rgba(220,38,38,.07);border:1px solid rgba(220,38,38,.2);color:var(--red)}
.sbtn{width:100%;padding:13px;background:var(--red);color:#fff;border:none;border-radius:9px;font-family:var(--sans);font-size:15px;font-weight:500;cursor:pointer;transition:all .2s;letter-spacing:.2px;margin-top:4px;box-shadow:0 4px 18px rgba(220,38,38,.28)}
.sbtn:hover{background:var(--red-d);transform:translateY(-1px);box-shadow:0 7px 24px rgba(220,38,38,.38)}
hr.div{border:none;border-top:1px solid var(--border);margin:22px 0}
.foot{margin-top:20px;display:flex;flex-direction:column;gap:8px;align-items:center}
.foot a{font-size:13px;color:var(--t3);text-decoration:none;transition:color .18s}
.foot a:hover{color:var(--red)}
@media(max-width:760px){.split{flex-direction:column;gap:28px}.sl{display:none}.sr{width:100%;max-width:440px}nav{padding:0 20px}}
</style>
</head>
<body>
<div class="bg-grid"></div><div class="g1"></div><div class="g2"></div>
<nav>
    <a class="nlogo" href="index.php"><div class="nicon">🩸</div><span class="ntext">Blood Donation</span></a>
    <a href="index.php" class="nlink">← Home</a>
</nav>
<div class="pw">
<div class="split">
<div class="sl">
    <div class="pill"><span class="pdot"></span>Admin Portal</div>
    <h1>Secure<br><em>Admin</em>Access</h1>
    <p>Manage donation records, monitor blood type inventory, and oversee all donor data from a central dashboard.</p>
    <div class="badge">
        <span class="badge-icon"></span>
        <div class="badge-text"><strong>Restricted Access</strong>Authorized administrators only. Credentials required to proceed.</div>
    </div>
</div>
<div class="sr">
<div class="card">
    <div class="ctitle">Welcome back</div>
    <div class="csub">Sign in to your admin account</div>
    <?php if($error):?><div class="aerr">⚠ <?=htmlspecialchars($error)?></div><?php endif;?>
    <form method="POST">
        <div class="field"><label>Username</label><input type="text" name="username" placeholder="Enter your username" required autocomplete="username"></div>
        <div class="field"><label>Password</label><input type="password" name="password" placeholder="Enter your password" required autocomplete="current-password"></div>
        <button class="sbtn" name="login">Sign In →</button>
    </form>
    <hr class="div">
    <div class="foot"><a href="register1.php">Create a new admin account</a><a href="index.php">← Back to Home</a></div>
</div>
</div>
</div>
</div>
</body>
</html>
