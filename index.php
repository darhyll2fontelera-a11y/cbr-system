<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blood Donation — Save a Life</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root {
    --surface:   #ffffff;
    --card:      rgba(255,255,255,0.82);
    --border:    rgba(0,0,0,.08);
    --border-s:  rgba(0,0,0,.15);
    --red:       #DC2626;
    --red-d:     #B91C1C;
    --red-muted: rgba(220,38,38,.1);
    --red-b:     rgba(220,38,38,.22);
    --rose:      #e05a5a;
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
}
@keyframes gradMove {
    0%   { background-position: 0% 50%; }
    50%  { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
.bg-grid {
    position: fixed; inset: 0; pointer-events: none;
    background-image:
        linear-gradient(rgba(0,0,0,.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,0,0,.03) 1px, transparent 1px);
    background-size: 72px 72px;
}
.glow-1 {
    position: fixed; top: -300px; right: -200px;
    width: 800px; height: 800px; border-radius: 50%;
    background: radial-gradient(circle, rgba(255,77,109,.22), transparent 60%);
    pointer-events: none;
}
.glow-2 {
    position: fixed; bottom: -200px; left: -150px;
    width: 600px; height: 600px; border-radius: 50%;
    background: radial-gradient(circle, rgba(255,143,163,.18), transparent 60%);
    pointer-events: none;
}

/* ── NAVBAR ── */
nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    height: 68px;
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 48px;
    background: rgba(255,255,255,.78);
    border-bottom: 1px solid var(--border);
    backdrop-filter: blur(16px);
}
.nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
.nav-logo-icon {
    width: 34px; height: 34px; background: var(--red); border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 3px 12px rgba(220,38,38,.32);
}
.nav-logo-text { font-family: var(--serif); font-size: 20px; font-weight: 600; color: var(--text); }

/* Hamburger — hidden on desktop */
.nav-hamburger {
    display: none;
    background: none;
    border: 1px solid var(--border);
    padding: 7px 10px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 20px;
    color: var(--text);
    line-height: 1;
    transition: background .18s;
}
.nav-hamburger:hover { background: var(--red-muted); }

.nav-links { display: flex; align-items: center; gap: 6px; }
.nav-link {
    color: var(--text-2); text-decoration: none;
    padding: 7px 16px; border-radius: 8px; font-size: 14px;
    transition: all .18s;
}
.nav-link:hover { color: var(--red); background: var(--red-muted); }
.nav-cta {
    background: var(--red); color: #fff;
    padding: 8px 20px; border-radius: 8px; font-size: 14px; font-weight: 500;
    text-decoration: none; transition: all .2s;
    box-shadow: 0 3px 14px rgba(220,38,38,.28);
}
.nav-cta:hover { background: var(--red-d); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(220,38,38,.36); }

/* HERO */
.hero { min-height: 100vh; display: flex; flex-direction: column; padding-top: 68px; }
.hero-main {
    flex: 1; display: flex; align-items: center;
    padding: 80px 48px; gap: 80px;
}
.hero-left { flex: 1; animation: fadeUp .7s ease both; }
.hero-headline { font-family: var(--serif); font-size: clamp(52px, 6vw, 76px); line-height: 1.05; color: var(--text); margin-bottom: 20px; }
.hero-headline em { color: var(--red); font-style: italic; }
.hero-desc { font-size: 16px; font-weight: 300; color: var(--text-2); line-height: 1.8; margin-bottom: 32px; max-width: 400px; }
.hero-btns { display: flex; gap: 12px; flex-wrap: wrap; }
.btn { padding: 12px 24px; border-radius: 9px; text-decoration: none; font-size: 14px; font-weight: 500; transition: all .2s; }
.btn-red { background: var(--red); color: white; box-shadow: 0 4px 16px rgba(220,38,38,.28); }
.btn-red:hover { background: var(--red-d); transform: translateY(-1px); box-shadow: 0 7px 22px rgba(220,38,38,.36); }
.btn-outline { border: 1px solid var(--border-s); color: var(--text-2); background: rgba(255,255,255,.5); }
.btn-outline:hover { border-color: var(--red-b); color: var(--red); background: var(--red-muted); }

/* STRIP */
.hero-strip {
    display: flex; justify-content: space-around;
    padding: 20px 48px; border-top: 1px solid var(--border);
    background: rgba(255,255,255,.5); backdrop-filter: blur(10px);
}
.strip-item { display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--text-2); }
.strip-icon { font-size: 18px; }

@keyframes fadeUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }

/* ── CONTENT SECTIONS ── */
.sections-wrap { position: relative; z-index: 1; background: #fff; }
.section-block { max-width: 920px; margin: 0 auto; padding: 80px 40px; }
.section-block + .section-block { border-top: 1px solid var(--border); }
.sec-title { font-family: var(--serif); font-size: clamp(36px, 4vw, 56px); font-weight: 600; color: var(--red); line-height: 1.05; margin-bottom: 12px; }
.sec-divider { width: 48px; height: 3px; background: var(--red); border-radius: 2px; margin-bottom: 36px; }

/* ACCORDION */
.accordion { display: flex; flex-direction: column; gap: 8px; }
.acc-item { background: #F2F2F2; border: 1px solid transparent; border-radius: 8px; overflow: hidden; transition: border-color .2s, box-shadow .2s; }
.acc-item.open { border-color: var(--red-b); box-shadow: 0 2px 12px rgba(220,38,38,.08); }
.acc-trigger { width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 18px 20px; background: none; border: none; cursor: pointer; font-family: var(--sans); text-align: left; gap: 16px; transition: background .18s; }
.acc-trigger:hover { background: rgba(220,38,38,.04); }
.acc-label { font-size: 13px; font-weight: 600; letter-spacing: .8px; text-transform: uppercase; color: #5a5a5a; line-height: 1.4; }
.acc-item.open .acc-label { color: var(--red-d); }
.acc-btn { width: 28px; height: 28px; flex-shrink: 0; background: var(--red); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px; font-weight: 300; line-height: 1; transition: transform .28s ease, background .2s; user-select: none; }
.acc-item.open .acc-btn { transform: rotate(45deg); background: var(--red-d); }
.acc-body { max-height: 0; overflow: hidden; transition: max-height .35s ease, padding .3s ease; padding: 0 20px; }
.acc-item.open .acc-body { max-height: 600px; padding: 0 20px 18px; }
.acc-content { font-size: 14px; color: var(--text-2); line-height: 1.8; border-top: 1px solid var(--border); padding-top: 14px; }
.acc-content p + p { margin-top: 8px; }

/* REGION LABEL */
.region-label { font-size: 13px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--red); margin: 28px 0 12px; }
.region-label:first-child { margin-top: 0; }

/* CTA BANNER */
.home-cta { background: var(--red); position: relative; z-index: 1; padding: 72px 40px; text-align: center; }
.home-cta h2 { font-family: var(--serif); font-size: clamp(32px, 4vw, 52px); font-weight: 400; color: #fff; margin-bottom: 12px; }
.home-cta p { font-size: 15px; color: rgba(255,255,255,.8); margin-bottom: 32px; max-width: 480px; margin-left: auto; margin-right: auto; line-height: 1.7; }
.cta-btn { display: inline-flex; align-items: center; gap: 8px; padding: 14px 36px; background: #fff; color: var(--red); border-radius: 10px; font-family: var(--sans); font-size: 15px; font-weight: 600; text-decoration: none; transition: all .2s; box-shadow: 0 4px 20px rgba(0,0,0,.15); }
.cta-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,.2); }

/* FOOTER */
footer { background: var(--text); color: rgba(255,255,255,.45); text-align: center; padding: 22px; font-size: 13px; position: relative; z-index: 1; }
footer span { color: rgba(255,255,255,.75); }

/* ══════════════════════════════
   MOBILE RESPONSIVE
══════════════════════════════ */
@media(max-width:860px) {
    /* Nav */
    nav { padding: 0 20px; }
    .nav-hamburger { display: flex; }
    .nav-links {
        display: none;
        position: fixed;
        top: 68px; left: 0; right: 0;
        background: rgba(255,255,255,.97);
        border-bottom: 1px solid var(--border);
        backdrop-filter: blur(20px);
        flex-direction: column;
        padding: 12px 16px 20px;
        gap: 4px;
        z-index: 99;
        box-shadow: 0 8px 24px rgba(0,0,0,.08);
    }
    .nav-links.open { display: flex; }
    .nav-link { padding: 11px 16px; border-radius: 8px; font-size: 15px; }
    .nav-cta { padding: 12px 20px; text-align: center; margin-top: 4px; border-radius: 9px; }

    /* Hero */
    .hero-main { flex-direction: column; padding: 40px 20px 32px; gap: 32px; }
    .hero-headline { font-size: clamp(40px, 10vw, 60px); }
    .hero-desc { font-size: 15px; max-width: 100%; }
    .hero-btns { gap: 10px; }
    .btn { padding: 12px 20px; font-size: 14px; }

    /* Strip */
    .hero-strip { padding: 16px 20px; gap: 12px; flex-wrap: wrap; justify-content: flex-start; }
    .strip-item { font-size: 13px; }

    /* Sections */
    .section-block { padding: 48px 20px; }
    .acc-label { font-size: 12px; }

    /* CTA */
    .home-cta { padding: 56px 24px; }
}

@media(max-width:480px) {
    .hero-btns { flex-direction: column; }
    .btn { text-align: center; justify-content: center; }
}
</style>
</head>
<body>

<div class="bg-grid"></div>
<div class="glow-1"></div>
<div class="glow-2"></div>

<nav>
    <a class="nav-logo" href="#">
        <div class="nav-logo-icon">🩸</div>
        <span class="nav-logo-text">Blood Donation</span>
    </a>
    <!-- Hamburger button (mobile only) -->
    <button class="nav-hamburger" onclick="toggleNav()" aria-label="Toggle menu">☰</button>
    <div class="nav-links" id="navLinks">
        <a href="#how-to-donate" class="nav-link" onclick="closeNav()">How to Donate</a>
        <a href="#blood-banks" class="nav-link" onclick="closeNav()">Blood Banks</a>
        <a href="donate_form.php" class="nav-link" onclick="closeNav()">Donate</a>
        <a href="user_login.php" class="nav-link" onclick="closeNav()">Sign in</a>
        <a href="login.php" class="nav-link" onclick="closeNav()">Admin</a>
        <a href="find_hospitals.php" class="nav-link" onclick="closeNav()">Find Hospitals</a>
        <a href="donate_form.php" class="nav-cta" onclick="closeNav()">Donate Now</a>
    </div>
</nav>

<div class="hero">
    <div class="hero-main">
        <div class="hero-left">
            <h1 class="hero-headline">Give <em>Blood,</em><br>Save Lives</h1>
            <p class="hero-desc">Join our voluntary blood donation program and help save lives. Every donation counts — one contribution can help up to three people.</p>
        </div>
    </div>

    <div class="hero-strip">
        <div class="strip-item"><span class="strip-icon"></span> Collection Center</div>
        <div class="strip-item"><span class="strip-icon">🩸</span> All Blood Types</div>
        <div class="strip-item"><span class="strip-icon"></span> Instant Ticket</div>
    </div>
</div>

<!-- HOW TO DONATE -->
<div class="sections-wrap">
<div class="section-block" id="how-to-donate">
  <h2 class="sec-title">How to Donate</h2>
  <div class="sec-divider"></div>
  <div class="accordion">
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">How often can a person donate?</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p>Whole blood donors can donate every <strong>56 days (8 weeks)</strong>. Platelet donors can give every 7 days, up to 24 times per year. Plasma donors may donate up to twice per week. Double red cell donors must wait at least 112 days between donations.</p>
        <p>Your body replenishes the donated blood volume within 24 hours, and red blood cells are fully replaced within 4–6 weeks.</p>
      </div></div>
    </div>
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">Will donating blood make a person weak?</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p>Most healthy donors feel fine after giving blood. Some may feel slightly lightheaded or tired immediately after donation, which is why we recommend resting for 10–15 minutes afterward.</p>
        <p>Drink plenty of fluids and avoid strenuous physical activity for the rest of the day. Most donors return to normal activities within a few hours.</p>
      </div></div>
    </div>
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">Can a person who has a tattoo or body piercing still donate blood?</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p>Yes, but there is a <strong>waiting period of 12 months</strong> after getting a tattoo or body piercing before you can donate blood. This is a precautionary measure to reduce the risk of bloodborne infections such as hepatitis B and C.</p>
        <p>If your tattoo was applied in a licensed and regulated facility using sterile needles, requirements may differ — please check with your local blood center.</p>
      </div></div>
    </div>
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">How long will it take to donate blood?</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p>The actual blood donation itself takes approximately <strong>8–10 minutes</strong>. However, the entire process — including registration, health screening, donation, and recovery — typically takes about <strong>45 minutes to 1 hour</strong>.</p>
        <p>Please plan accordingly and avoid rushing after your donation.</p>
      </div></div>
    </div>
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">Will I contract a disease through blood donation?</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p><strong>No.</strong> All equipment used — needles, tubing, and collection bags — is sterile, single-use, and discarded after each donor. There is absolutely no risk of contracting any disease from donating blood.</p>
        <p>Every donation is handled under strict medical protocols to ensure the safety of both the donor and the recipient.</p>
      </div></div>
    </div>
  </div>
</div>

<!-- BLOOD BANK LOCATOR -->
<div class="section-block" id="blood-banks">
  <h2 class="sec-title">Blood Bank Locator</h2>
  <div class="sec-divider"></div>
  <div class="accordion">
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">National Blood Center (PRC Tower)</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p><strong>Address:</strong> PRC Tower, P. Paredes St., Sampaloc, Manila</p>
        <p><strong>Phone:</strong> (02) 8711-9502</p>
        <p><strong>Hours:</strong> Monday–Sunday, 7:00 AM – 7:00 PM</p>
      </div></div>
    </div>
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">National Blood Center (Manila)</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p><strong>Address:</strong> Quirino Ave., Paco, Manila</p>
        <p><strong>Phone:</strong> (02) 8525-5581</p>
        <p><strong>Hours:</strong> Monday–Sunday, 7:00 AM – 7:00 PM</p>
      </div></div>
    </div>
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">Eastern Visayas Regional Blood Center</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p><strong>Address:</strong> Magsaysay Blvd., Tacloban City, Leyte</p>
        <p><strong>Phone:</strong> (053) 832-4018</p>
        <p><strong>Hours:</strong> Monday–Friday, 8:00 AM – 5:00 PM</p>
      </div></div>
    </div>
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">Western Visayas Regional Blood Center</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p><strong>Address:</strong> Molo, Iloilo City, Iloilo</p>
        <p><strong>Phone:</strong> (033) 337-7741</p>
        <p><strong>Hours:</strong> Monday–Friday, 8:00 AM – 5:00 PM</p>
      </div></div>
    </div>
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">Mindanao Regional Blood Center</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p><strong>Address:</strong> Davao City, Davao del Sur</p>
        <p><strong>Phone:</strong> (082) 226-3391</p>
        <p><strong>Hours:</strong> Monday–Friday, 8:00 AM – 5:00 PM</p>
      </div></div>
    </div>
  </div>

  <div class="region-label">Northern and Central Luzon</div>
  <div class="accordion">
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">Alaminos City – Western Pangasinan</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p><strong>Address:</strong> Alaminos City, Pangasinan</p>
        <p><strong>Phone:</strong> (075) 551-2345</p>
        <p><strong>Hours:</strong> Monday–Friday, 8:00 AM – 5:00 PM</p>
      </div></div>
    </div>
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">Baguio City</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p><strong>Address:</strong> Baguio General Hospital, Gov. Pack Road, Baguio City</p>
        <p><strong>Phone:</strong> (074) 442-3180</p>
        <p><strong>Hours:</strong> Monday–Friday, 8:00 AM – 5:00 PM</p>
      </div></div>
    </div>
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">San Fernando, Pampanga</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p><strong>Address:</strong> Jose B. Lingad Memorial Regional Hospital, San Fernando, Pampanga</p>
        <p><strong>Phone:</strong> (045) 961-2536</p>
        <p><strong>Hours:</strong> Monday–Friday, 8:00 AM – 5:00 PM</p>
      </div></div>
    </div>
  </div>

  <div class="region-label">National Capital Region (NCR)</div>
  <div class="accordion">
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">Philippine General Hospital – Manila</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p><strong>Address:</strong> Taft Avenue, Ermita, Manila</p>
        <p><strong>Phone:</strong> (02) 8554-8400</p>
        <p><strong>Hours:</strong> 24 hours, 7 days a week</p>
      </div></div>
    </div>
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">East Avenue Medical Center – Quezon City</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p><strong>Address:</strong> East Avenue, Diliman, Quezon City</p>
        <p><strong>Phone:</strong> (02) 8928-0611</p>
        <p><strong>Hours:</strong> Monday–Sunday, 7:00 AM – 7:00 PM</p>
      </div></div>
    </div>
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">Makati Medical Center</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p><strong>Address:</strong> 2 Amorsolo St., Legaspi Village, Makati City</p>
        <p><strong>Phone:</strong> (02) 8888-8999</p>
        <p><strong>Hours:</strong> 24 hours, 7 days a week</p>
      </div></div>
    </div>
  </div>

  <div class="region-label">Southern Luzon</div>
  <div class="accordion">
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">Batangas Regional Hospital</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p><strong>Address:</strong> Kumintang Ibaba, Batangas City</p>
        <p><strong>Phone:</strong> (043) 723-2302</p>
        <p><strong>Hours:</strong> Monday–Friday, 8:00 AM – 5:00 PM</p>
      </div></div>
    </div>
    <div class="acc-item">
      <button class="acc-trigger" onclick="toggleAcc(this)">
        <span class="acc-label">Bicol Regional Training and Teaching Hospital</span>
        <div class="acc-btn">+</div>
      </button>
      <div class="acc-body"><div class="acc-content">
        <p><strong>Address:</strong> Legaspi City, Albay</p>
        <p><strong>Phone:</strong> (052) 480-0351</p>
        <p><strong>Hours:</strong> Monday–Friday, 8:00 AM – 5:00 PM</p>
      </div></div>
    </div>
  </div>
</div>
</div>

<!-- CTA BANNER -->
<div class="home-cta">
  <h2>Ready to Save a Life?</h2>
  <p>It only takes a few minutes. Your blood donation can make all the difference for someone in need.</p>
  <a href="donate_form.php" class="cta-btn">🩸&nbsp; Donate Now</a>
</div>

<!-- FOOTER -->
<footer>
  <span>Blood Donation</span> · Quiapo General Hospital · &copy; <?= date('Y') ?>
</footer>

<script>
/* Accordion */
function toggleAcc(trigger) {
  const item  = trigger.closest('.acc-item');
  const group = item.closest('.accordion');
  const isOpen = item.classList.contains('open');
  group.querySelectorAll('.acc-item.open').forEach(el => el.classList.remove('open'));
  if (!isOpen) item.classList.add('open');
}

/* Smooth scroll for anchor links */
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const target = document.querySelector(a.getAttribute('href'));
    if (target) {
      e.preventDefault();
      closeNav();
      setTimeout(() => target.scrollIntoView({ behavior:'smooth', block:'start' }), 50);
    }
  });
});

/* Mobile nav toggle */
function toggleNav() {
  const links = document.getElementById('navLinks');
  const open  = links.classList.toggle('open');
  document.querySelector('.nav-hamburger').textContent = open ? '✕' : '☰';
}
function closeNav() {
  document.getElementById('navLinks').classList.remove('open');
  document.querySelector('.nav-hamburger').textContent = '☰';
}

/* Close nav when clicking outside */
document.addEventListener('click', e => {
  const nav = document.querySelector('nav');
  if (!nav.contains(e.target)) closeNav();
});
</script>
</body>
</html>