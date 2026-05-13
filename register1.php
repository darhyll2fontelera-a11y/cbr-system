<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account — Blood Donation</title>
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
.g1{position:fixed;top:-300px;left:-200px;z-index:0;width:800px;height:800px;border-radius:50%;background:radial-gradient(circle,rgba(255,100,130,.18) 0%,transparent 60%);pointer-events:none}
.g2{position:fixed;bottom:-200px;right:-150px;z-index:0;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(255,160,180,.15) 0%,transparent 60%);pointer-events:none}
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
.sl p{font-size:15px;font-weight:300;color:var(--t2);line-height:1.8;margin-bottom:28px}
.perks{list-style:none;display:flex;flex-direction:column;gap:10px}
.perks li{display:flex;align-items:center;gap:12px;font-size:14px;color:var(--t2)}
.perk-dot{width:8px;height:8px;background:var(--red);border-radius:50%;flex-shrink:0}
.card{background:var(--card);border:1px solid var(--border);border-radius:18px;padding:38px;box-shadow:0 20px 60px rgba(200,50,80,.1),0 2px 12px rgba(0,0,0,.06);animation:cin .45s ease both;backdrop-filter:blur(20px)}
@keyframes cin{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
.ctitle{font-family:var(--serif);font-size:26px;color:var(--t);margin-bottom:4px}
.csub{font-size:13px;font-weight:300;color:var(--t3);margin-bottom:24px}

/* ── Stepper ── */
.stepper{display:flex;align-items:center;margin-bottom:28px;gap:0}
.step{display:flex;align-items:center;gap:7px;flex-shrink:0}
.step-num{width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;transition:all .3s;border:2px solid var(--border-s);color:var(--t3);background:transparent}
.step-label{font-size:11px;font-weight:600;letter-spacing:.6px;text-transform:uppercase;color:var(--t3);transition:color .3s}
.step.active .step-num{background:var(--red);border-color:var(--red);color:#fff;box-shadow:0 2px 10px rgba(220,38,38,.35)}
.step.active .step-label{color:var(--red)}
.step.done .step-num{background:var(--red-m);border-color:var(--red-b);color:var(--red)}
.step.done .step-label{color:var(--t2)}
.step-line{flex:1;height:1px;background:var(--border-s);margin:0 8px;transition:background .3s}
.step-line.done{background:rgba(220,38,38,.35)}

/* ── Step panels ── */
.step-panel{display:none;animation:sfade .3s ease both}
.step-panel.active{display:block}
@keyframes sfade{from{opacity:0;transform:translateX(14px)}to{opacity:1;transform:translateX(0)}}

.field{margin-bottom:16px}
.field label{display:block;font-size:11px;font-weight:600;color:var(--t3);letter-spacing:.8px;text-transform:uppercase;margin-bottom:7px}
.field input{width:100%;padding:12px 15px;background:rgba(255,255,255,.7);border:1px solid var(--border-s);border-radius:9px;color:var(--t);font-family:var(--sans);font-size:14px;outline:none;transition:all .2s}
.field input:focus{border-color:rgba(220,38,38,.5);background:#fff;box-shadow:0 0 0 3px rgba(220,38,38,.08)}
.field input::placeholder{color:rgba(0,0,0,.28)}
.field .hint{font-size:11px;color:var(--t3);margin-top:5px}
.field .err{font-size:11px;color:var(--red);margin-top:5px;display:none}
.field.has-error input{border-color:rgba(220,38,38,.5);box-shadow:0 0 0 3px rgba(220,38,38,.08)}
.field.has-error .err{display:block}

.sbtn{width:100%;padding:13px;background:var(--red);color:#fff;border:none;border-radius:9px;font-family:var(--sans);font-size:15px;font-weight:500;cursor:pointer;transition:all .2s;margin-top:4px;box-shadow:0 4px 18px rgba(220,38,38,.28)}
.sbtn:hover{background:var(--red-d);transform:translateY(-1px);box-shadow:0 7px 24px rgba(220,38,38,.38)}
.sbtn:disabled{opacity:.6;cursor:not-allowed;transform:none}
.back-btn{width:100%;padding:13px;background:transparent;color:var(--t3);border:1px solid var(--border-s);border-radius:9px;font-family:var(--sans);font-size:14px;cursor:pointer;transition:all .2s;margin-top:8px}
.back-btn:hover{color:var(--t);border-color:var(--border-s);background:rgba(0,0,0,.03)}

hr.div{border:none;border-top:1px solid var(--border);margin:22px 0}
.foot{margin-top:20px;display:flex;flex-direction:column;gap:8px;align-items:center}
.foot a{font-size:13px;color:var(--t3);text-decoration:none;transition:color .18s}
.foot a:hover{color:var(--red)}

/* ── Done step ── */
.done-icon{width:64px;height:64px;background:var(--red-m);border:2px solid var(--red-b);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 18px;animation:popIn .4s cubic-bezier(.34,1.56,.64,1) both}
@keyframes popIn{from{opacity:0;transform:scale(.5)}to{opacity:1;transform:scale(1)}}
.done-title{font-family:var(--serif);font-size:22px;text-align:center;margin-bottom:8px}
.done-sub{font-size:13px;font-weight:300;color:var(--t3);text-align:center;margin-bottom:24px;line-height:1.7}
.summary{background:rgba(220,38,38,.04);border:1px solid var(--red-b);border-radius:10px;padding:14px 16px;margin-bottom:20px}
.summary-row{display:flex;justify-content:space-between;align-items:center;padding:5px 0;font-size:13px}
.summary-row:not(:last-child){border-bottom:1px solid rgba(220,38,38,.1)}
.summary-row span:first-child{color:var(--t3);font-weight:500}
.summary-row span:last-child{color:var(--t);font-weight:500}

/* ── Password strength ── */
.pw-strength{margin-top:8px}
.pw-bars{display:flex;gap:4px;margin-bottom:4px}
.pw-bar{flex:1;height:3px;border-radius:2px;background:var(--border-s);transition:background .3s}
.pw-bar.weak{background:#ef4444}
.pw-bar.fair{background:#f97316}
.pw-bar.good{background:#eab308}
.pw-bar.strong{background:#22c55e}
.pw-label{font-size:11px;color:var(--t3)}

@media(max-width:760px){.split{flex-direction:column;gap:28px}.sl{display:none}.sr{width:100%;max-width:440px}nav{padding:0 20px}}

/* ── Error / Success banners ── */
.banner{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border-radius:10px;font-size:13px;font-weight:400;line-height:1.5;margin-bottom:18px;animation:cin .3s ease both}
.banner-err{background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.25);color:#b91c1c}
.banner-ok{background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.3);color:#15803d}
.banner-icon{font-size:15px;flex-shrink:0;margin-top:1px}
</style>
</head>
<body>
<div class="bg-grid"></div><div class="g1"></div><div class="g2"></div>
<nav>
    <a class="nlogo" href="index.php"><div class="nicon">🩸</div><span class="ntext">Blood Donation</span></a>
    <a href="user_login.php" class="nlink">← Login</a>
</nav>
<div class="pw">
<div class="split">
<div class="sl">
    <div class="pill"><span class="pdot"></span>New Account</div>
    <h1>Join &amp; <em>Start</em> Saving</h1>
    <p>Create your administrator account to access the donation management portal and make a difference.</p>
    <ul class="perks">
        <li><span class="perk-dot"></span>View all donation records in real-time</li>
        <li><span class="perk-dot"></span>Manage blood type inventory at a glance</li>
        <li><span class="perk-dot"></span>Delete and audit donor entries securely</li>
        <li><span class="perk-dot"></span>Full account control — edit or delete anytime</li>
    </ul>
</div>
<div class="sr">
<div class="card">
    <?php
    $errorMessages = [
        'username_taken' => 'That username is already taken. Please choose a different one.',
        'email_taken'    => 'An account with that email already exists. Try logging in instead.',
        'server_error'   => 'Something went wrong on our end. Please try again.',
    ];
    if (!empty($_GET['error']) && isset($errorMessages[$_GET['error']])) {
        echo '<div class="banner banner-err"><span class="banner-icon"></span><span>' . $errorMessages[$_GET['error']] . '</span></div>';
    }
    ?>
    <div class="ctitle">Create account</div>
    <div class="csub">Fill in your details to register</div>

    <!-- Stepper -->
    <div class="stepper" id="stepper">
        <div class="step active" id="s1">
            <div class="step-num">1</div>
            <div class="step-label">Account</div>
        </div>
        <div class="step-line" id="l1"></div>
        <div class="step" id="s2">
            <div class="step-num">2</div>
            <div class="step-label">Security</div>
        </div>
        <div class="step-line" id="l2"></div>
        <div class="step" id="s3">
            <div class="step-num">3</div>
            <div class="step-label">Done</div>
        </div>
    </div>

    <!-- Real form that will be submitted on step 3 -->
    <form action="process_register.php" method="POST" id="regForm">
        <input type="hidden" name="username" id="h_username">
        <input type="hidden" name="email"    id="h_email">
        <input type="hidden" name="password" id="h_password">

        <!-- Step 1 — Account -->
        <div class="step-panel active" id="panel1">
            <div class="field" id="f_username">
                <label>Username</label>
                <input type="text" id="username" placeholder="Choose a username" autocomplete="username">
                <div class="err" id="e_username">Please enter a username (min 3 characters).</div>
            </div>
            <div class="field" id="f_email">
                <label>Email Address</label>
                <input type="email" id="email" placeholder="Your email address" autocomplete="email">
                <div class="err" id="e_email">Please enter a valid email address.</div>
            </div>
            <button type="button" class="sbtn" onclick="goStep2()">Continue →</button>
        </div>

        <!-- Step 2 — Security -->
        <div class="step-panel" id="panel2">
            <div class="field" id="f_password">
                <label>Password</label>
                <input type="password" id="password" placeholder="Create a strong password" autocomplete="new-password" oninput="checkStrength(this.value)">
                <div class="pw-strength">
                    <div class="pw-bars">
                        <div class="pw-bar" id="b1"></div>
                        <div class="pw-bar" id="b2"></div>
                        <div class="pw-bar" id="b3"></div>
                        <div class="pw-bar" id="b4"></div>
                    </div>
                    <div class="pw-label" id="pw-label">Enter a password</div>
                </div>
                <div class="err" id="e_password">Password must be at least 8 characters.</div>
            </div>
            <div class="field" id="f_confirm">
                <label>Confirm Password</label>
                <input type="password" id="confirm" placeholder="Re-enter your password" autocomplete="new-password">
                <div class="err" id="e_confirm">Passwords do not match.</div>
            </div>
            <button type="button" class="sbtn" onclick="goStep3()">Continue →</button>
            <button type="button" class="back-btn" onclick="goStep1()">← Back</button>
        </div>

        <!-- Step 3 — Done / Review -->
        <div class="step-panel" id="panel3">
            <div class="done-icon">🩸</div>
            <div class="done-title">Almost there!</div>
            <div class="done-sub">Review your details before creating your account.</div>
            <div class="summary">
                <div class="summary-row">
                    <span>Username</span>
                    <span id="rev_username">—</span>
                </div>
                <div class="summary-row">
                    <span>Email</span>
                    <span id="rev_email">—</span>
                </div>
                <div class="summary-row">
                    <span>Password</span>
                    <span id="rev_password">—</span>
                </div>
            </div>
            <button type="button" class="sbtn" onclick="submitForm()">Create Account →</button>
            <button type="button" class="back-btn" onclick="goStep2()">← Back</button>
        </div>
    </form>

    <hr class="div">
    <div class="foot"><a href="user_login.php">Already have an account? Sign in</a><a href="index.php">← Back to Home</a></div>
</div>
</div>
</div>
</div>

<script>
/* ── Stepper helpers ── */
function setStep(n){
    [1,2,3].forEach(i=>{
        document.getElementById('panel'+i).classList.toggle('active', i===n);
        const s=document.getElementById('s'+i);
        s.classList.remove('active','done');
        if(i===n) s.classList.add('active');
        else if(i<n) s.classList.add('done');
    });
    if(document.getElementById('l1')) document.getElementById('l1').classList.toggle('done', n>1);
    if(document.getElementById('l2')) document.getElementById('l2').classList.toggle('done', n>2);
}

/* ── Field validation helpers ── */
function setErr(fieldId, errId, show){
    document.getElementById(fieldId).classList.toggle('has-error', show);
    document.getElementById(errId).style.display = show ? 'block' : 'none';
}
function clearErr(fieldId, errId){setErr(fieldId,errId,false);}

/* ── Step 1 → 2 ── */
function goStep2(){
    const u=document.getElementById('username').value.trim();
    const e=document.getElementById('email').value.trim();
    let ok=true;
    if(u.length<3){setErr('f_username','e_username',true);ok=false;}else{clearErr('f_username','e_username');}
    if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e)){setErr('f_email','e_email',true);ok=false;}else{clearErr('f_email','e_email');}
    if(ok) setStep(2);
}

/* ── Step 2 → 3 ── */
function goStep3(){
    const p=document.getElementById('password').value;
    const c=document.getElementById('confirm').value;
    let ok=true;
    if(p.length<8){setErr('f_password','e_password',true);ok=false;}else{clearErr('f_password','e_password');}
    if(p!==c||c===''){setErr('f_confirm','e_confirm',true);ok=false;}else{clearErr('f_confirm','e_confirm');}
    if(ok){
        const u=document.getElementById('username').value.trim();
        const e=document.getElementById('email').value.trim();
        document.getElementById('rev_username').textContent=u;
        document.getElementById('rev_email').textContent=e;
        document.getElementById('rev_password').textContent='•'.repeat(Math.min(p.length,10));
        setStep(3);
    }
}

/* ── Back helpers ── */
function goStep1(){setStep(1);}
function goStep2back(){setStep(2);}

/* ── Submit ── */
function submitForm(){
    document.getElementById('h_username').value=document.getElementById('username').value.trim();
    document.getElementById('h_email').value=document.getElementById('email').value.trim();
    document.getElementById('h_password').value=document.getElementById('password').value;
    document.getElementById('regForm').submit();
}

/* ── Password strength ── */
function checkStrength(p){
    let score=0;
    if(p.length>=8) score++;
    if(/[A-Z]/.test(p)) score++;
    if(/[0-9]/.test(p)) score++;
    if(/[^A-Za-z0-9]/.test(p)) score++;
    const bars=[document.getElementById('b1'),document.getElementById('b2'),document.getElementById('b3'),document.getElementById('b4')];
    const labels=['','Weak','Fair','Good','Strong'];
    const cls=['','weak','fair','good','strong'];
    bars.forEach((b,i)=>{b.className='pw-bar'+(i<score?' '+cls[score]:'');});
    document.getElementById('pw-label').textContent=p.length===0?'Enter a password':labels[score]||'Weak';
}
</script>
</body>
</html>