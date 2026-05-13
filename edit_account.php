<?php
session_start();
include 'db.php';
if(!isset($_SESSION['admin'])){ header("Location: login.php"); exit(); }
$admin=$_SESSION['admin'];
$data=$conn->query("SELECT * FROM admins WHERE username='$admin'");
$row=$data->fetch_assoc();
$alert="";
if(isset($_POST['update'])){
    $username=trim($_POST['username']);
    $old_pass=$_POST['old_password'];
    $new_pass=$_POST['new_password'];
    $confirm_pass=$_POST['confirm_password'];
    if(!empty($username)&&$username!==$admin){
        $conn->query("UPDATE admins SET username='$username' WHERE username='$admin'");
        $_SESSION['admin']=$username; $admin=$username;
    }
    if(!empty($old_pass)||!empty($new_pass)||!empty($confirm_pass)){
        if($old_pass!==$row['password']) $alert="error_old";
        elseif($new_pass!==$confirm_pass) $alert="error_match";
        elseif(empty($new_pass)) $alert="error_empty";
        else{ $conn->query("UPDATE admins SET password='$new_pass' WHERE username='$admin'"); $alert="success"; }
    } else $alert="success_username";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Account — Blood Donation</title>
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
body{
  font-family:var(--sans);
  background:linear-gradient(135deg,#ffffff 0%,#ffe4ec 50%,#ffc0cb 100%);
  background-size:300% 300%;
  animation:gradMove 12s ease infinite;
  color:var(--t);min-height:100vh;display:flex;flex-direction:column
}
@keyframes gradMove{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
.bg-grid{position:fixed;inset:0;z-index:0;pointer-events:none;background-image:linear-gradient(rgba(0,0,0,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(0,0,0,.03) 1px,transparent 1px);background-size:72px 72px}
.g1{position:fixed;top:-200px;right:-100px;z-index:0;width:700px;height:700px;border-radius:50%;background:radial-gradient(circle,rgba(255,100,130,.18) 0%,transparent 60%);pointer-events:none}
nav{position:relative;z-index:10;height:66px;display:flex;align-items:center;justify-content:space-between;padding:0 40px;border-bottom:1px solid var(--border);background:rgba(255,255,255,.75);backdrop-filter:blur(16px)}
.nlogo{display:flex;align-items:center;gap:11px;text-decoration:none}
.nicon{width:32px;height:32px;background:var(--red);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;box-shadow:0 3px 12px rgba(220,38,38,.35)}
.ntext{font-family:var(--serif);font-size:19px;color:var(--t)}
.nlink{font-size:14px;color:var(--t2);text-decoration:none;padding:6px 14px;border-radius:7px;transition:all .18s}
.nlink:hover{color:var(--red);background:rgba(220,38,38,.07)}
.pw{position:relative;z-index:1;flex:1;display:flex;align-items:center;justify-content:center;padding:48px 24px}
.split{display:flex;gap:72px;align-items:flex-start;max-width:960px;width:100%}
.sl{flex:1;padding-top:12px}
.sr{flex-shrink:0;width:420px}
.pill{display:inline-flex;align-items:center;gap:7px;background:var(--red-m);border:1px solid var(--red-b);color:var(--red);font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;padding:5px 13px;border-radius:100px;margin-bottom:22px}
.pdot{width:5px;height:5px;background:var(--red);border-radius:50%;animation:pdot 2.5s ease-in-out infinite}
@keyframes pdot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.3;transform:scale(.6)}}
.sl h1{font-family:var(--serif);font-size:clamp(36px,4.5vw,60px);font-weight:400;line-height:1.05;color:var(--t);margin-bottom:16px}
.sl h1 em{font-style:italic;color:var(--red);display:block}
.sl p{font-size:15px;font-weight:300;color:var(--t2);line-height:1.8;margin-bottom:28px}
.adm-badge{background:rgba(255,255,255,.6);border:1px solid var(--border);border-radius:12px;padding:16px 18px;display:flex;align-items:center;gap:12px;backdrop-filter:blur(8px)}
.adm-av{width:40px;height:40px;background:var(--red-m);border:1px solid var(--red-b);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.adm-info strong{display:block;font-size:14px;font-weight:500;color:var(--t)}
.adm-info span{font-size:12px;color:var(--t3)}
.card{background:var(--card);border:1px solid var(--border);border-radius:18px;overflow:hidden;box-shadow:0 20px 60px rgba(200,50,80,.1),0 2px 12px rgba(0,0,0,.06);animation:cin .45s ease both;backdrop-filter:blur(20px)}
@keyframes cin{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
.card-head{padding:24px 28px;border-bottom:1px solid var(--border)}
.ctitle{font-family:var(--serif);font-size:24px;color:var(--t);margin-bottom:3px}
.csub{font-size:13px;font-weight:300;color:var(--t3)}
.card-body{padding:26px 28px}
.sect{margin-bottom:20px}
.sect-hd{display:flex;align-items:center;gap:10px;margin-bottom:14px}
.sect-title{font-size:12px;font-weight:600;color:var(--t2);letter-spacing:.6px;text-transform:uppercase}
.sect-line{flex:1;height:1px;background:var(--border)}
.field{margin-bottom:14px}
.field label{display:block;font-size:11px;font-weight:600;color:var(--t3);letter-spacing:.8px;text-transform:uppercase;margin-bottom:7px}
.field input{width:100%;padding:11px 14px;background:rgba(255,255,255,.7);border:1px solid var(--border-s);border-radius:9px;color:var(--t);font-family:var(--sans);font-size:14px;outline:none;transition:all .2s}
.field input:focus{border-color:rgba(220,38,38,.5);background:#fff;box-shadow:0 0 0 3px rgba(220,38,38,.08)}
.field input::placeholder{color:rgba(0,0,0,.28)}
.sbtn{width:100%;padding:12px;background:var(--red);color:#fff;border:none;border-radius:9px;font-family:var(--sans);font-size:15px;font-weight:500;cursor:pointer;transition:all .2s;box-shadow:0 4px 18px rgba(220,38,38,.28);letter-spacing:.2px}
.sbtn:hover{background:var(--red-d);transform:translateY(-1px);box-shadow:0 7px 24px rgba(220,38,38,.38)}
.back{display:block;text-align:center;margin-top:16px;font-size:13px;color:var(--t3);text-decoration:none;transition:color .18s}
.back:hover{color:var(--red)}
@media(max-width:760px){.split{flex-direction:column;gap:28px}.sl{display:none}.sr{width:100%;max-width:460px}nav{padding:0 20px}}
</style>
</head>
<body>
<div class="bg-grid"></div><div class="g1"></div>
<nav>
    <a class="nlogo" href="index.php"><div class="nicon">🩸</div><span class="ntext">Blood Donation</span></a>
    <a href="dashboard.php" class="nlink">← Dashboard</a>
</nav>
<div class="pw">
<div class="split">
<div class="sl">
    <div class="pill"><span class="pdot"></span>Account Settings</div>
    <h1>Edit Your<br><em>Admin</em>Account</h1>
    <p>Update your username or change your password. Changes take effect immediately after saving.</p>
    <div class="adm-badge">
        <div class="adm-av">👤</div>
        <div class="adm-info">
            <strong><?=htmlspecialchars($admin)?></strong>
            <span>Administrator · Active session</span>
        </div>
    </div>
</div>
<div class="sr">
<div class="card">
    <div class="card-head"><div class="ctitle">✏️ Edit Account</div><div class="csub">Update your credentials below</div></div>
    <div class="card-body">
    <form method="POST">
        <div class="sect">
            <div class="sect-hd"><span class="sect-title">Username</span><div class="sect-line"></div></div>
            <div class="field"><label>New Username</label><input type="text" name="username" value="<?=htmlspecialchars($admin)?>" placeholder="Keep current or change"></div>
        </div>
        <div class="sect">
            <div class="sect-hd"><span class="sect-title">Change Password</span><div class="sect-line"></div></div>
            <div class="field"><label>Current Password</label><input type="password" name="old_password" placeholder="Enter current password"></div>
            <div class="field"><label>New Password</label><input type="password" name="new_password" placeholder="Enter new password"></div>
            <div class="field"><label>Confirm New Password</label><input type="password" name="confirm_password" placeholder="Repeat new password"></div>
        </div>
        <button class="sbtn" name="update">Save Changes →</button>
    </form>
    <a class="back" href="dashboard.php">← Back to Dashboard</a>
    </div>
</div>
</div>
</div>
</div>
<script>
<?php if($alert==="error_old"):?>alert(" Current password is incorrect.");
<?php elseif($alert==="error_match"):?>alert(" New passwords do not match.");
<?php elseif($alert==="error_empty"):?>alert(" New password cannot be empty.");
<?php elseif($alert==="success"):?>alert(" Account updated successfully."); window.location="dashboard.php";
<?php elseif($alert==="success_username"):?>alert(" Username updated successfully."); window.location="dashboard.php";
<?php endif;?>
</script>
</body>
</html>
