<?php
require_once 'config/db.php';
// Get base URL for assets
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$base_url = $protocol . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

// Fetch all data
$teams = $pdo->query("SELECT * FROM teams ORDER BY id")->fetchAll();
$members = $pdo->query("SELECT m.*, t.name as team_name FROM members m LEFT JOIN teams t ON m.team_id = t.id ORDER BY t.id, m.id")->fetchAll();
$events = $pdo->query("SELECT * FROM events ORDER BY FIELD(type, 'Upcoming', 'Featured', 'AI/ML'), event_date ASC")->fetchAll();
$stats = $pdo->query("SELECT * FROM stats ORDER BY display_order")->fetchAll();
$resources = $pdo->query("SELECT * FROM resources")->fetchAll();
$contact = $pdo->query("SELECT * FROM contact LIMIT 1")->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <title>AWS Cloud Club TCOER | Next-Gen Cloud Community</title>
  <meta name="description" content="AWS Cloud Club - Trinity College of Engineering and Research, Pune. Empowering students with cutting-edge cloud computing skills, certifications, and industry networking." />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800;14..32,900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --bg-deep: #0a0c12;
      --bg-surface: #0f1117;
      --bg-card: rgba(18, 22, 32, 0.75);
      --border-glow: rgba(255, 153, 0, 0.25);
      --primary: #ff9900;
      --primary-dark: #ec7211;
      --accent: #232f3e;
      --accent-light: #2c3a4e;
      --text-primary: #f0f4fa;
      --text-secondary: #9aa4bf;
      --text-muted: #6c7a91;
      --radius-sm: 0.75rem;
      --radius-md: 1.25rem;
      --radius-lg: 2rem;
      --transition-fast: 0.2s cubic-bezier(0.2, 0.9, 0.4, 1.1);
      --transition-smooth: 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--bg-deep);
      color: var(--text-primary);
      line-height: 1.5;
      overflow-x: hidden;
    }

    ::-webkit-scrollbar {
      width: 6px;
    }
    ::-webkit-scrollbar-track {
      background: var(--bg-surface);
    }
    ::-webkit-scrollbar-thumb {
      background: var(--primary);
      border-radius: 10px;
    }

    .gradient-bg {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -2;
      background: radial-gradient(circle at 20% 30%, rgba(255, 153, 0, 0.08) 0%, transparent 50%),
                  radial-gradient(circle at 80% 70%, rgba(35, 47, 62, 0.1) 0%, transparent 50%),
                  linear-gradient(135deg, #0a0c12 0%, #11161f 100%);
    }

    .moving-orb {
      position: fixed;
      border-radius: 50%;
      filter: blur(70px);
      opacity: 0.4;
      z-index: -1;
      animation: floatOrb 18s infinite alternate ease-in-out;
    }
    .orb1 {
      width: 60vw;
      height: 60vw;
      background: radial-gradient(circle, rgba(255, 153, 0, 0.2), transparent);
      top: -20vh;
      left: -30vw;
      animation-duration: 25s;
    }
    .orb2 {
      width: 50vw;
      height: 50vw;
      background: radial-gradient(circle, rgba(35, 47, 62, 0.25), transparent);
      bottom: -20vh;
      right: -20vw;
      animation-duration: 20s;
      animation-delay: -5s;
    }

    @keyframes floatOrb {
      0% { transform: translate(0, 0) scale(1); }
      100% { transform: translate(5%, 8%) scale(1.1); }
    }

    .glass-card {
      background: rgba(18, 22, 32, 0.65);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 153, 0, 0.15);
      border-radius: var(--radius-md);
      transition: all var(--transition-smooth);
    }
    .glass-card:hover {
      border-color: rgba(255, 153, 0, 0.4);
      box-shadow: 0 20px 40px -12px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,153,0,0.2);
      transform: translateY(-4px);
    }

    .gradient-text {
      background: linear-gradient(135deg, #ff9900 0%, #ffb347 80%);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    .btn-primary {
      background: linear-gradient(105deg, #ff9900, #ec7211);
      border: none;
      padding: 0.85rem 2rem;
      border-radius: 2rem;
      font-weight: 600;
      color: #0a0c12;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(255, 153, 0, 0.25);
      cursor: pointer;
    }
    .btn-primary:hover {
      transform: scale(0.97);
      box-shadow: 0 8px 20px rgba(255, 153, 0, 0.4);
      background: linear-gradient(105deg, #ffaa33, #f47b20);
    }
    .btn-outline {
      background: transparent;
      border: 1.5px solid rgba(255, 153, 0, 0.5);
      padding: 0.85rem 2rem;
      border-radius: 2rem;
      font-weight: 600;
      color: #ff9900;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .btn-outline:hover {
      background: rgba(255, 153, 0, 0.1);
      border-color: #ff9900;
      transform: scale(0.97);
    }

    .section-tag {
      display: inline-block;
      font-size: 0.7rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      padding: 0.3rem 1rem;
      border-radius: 2rem;
      background: rgba(255, 153, 0, 0.1);
      border: 1px solid rgba(255, 153, 0, 0.25);
      color: var(--primary);
      margin-bottom: 1rem;
    }

    .cursor-glow {
      width: 300px;
      height: 300px;
      background: radial-gradient(circle, rgba(255, 153, 0, 0.08) 0%, transparent 70%);
      position: fixed;
      pointer-events: none;
      border-radius: 50%;
      z-index: 9999;
      transform: translate(-50%, -50%);
      transition: transform 0.05s linear;
      opacity: 0;
    }

    .navbar {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 100;
      padding: 1rem 0;
      transition: all 0.4s ease;
    }
    .navbar.scrolled {
      background: rgba(10, 12, 18, 0.85);
      backdrop-filter: blur(16px);
      border-bottom: 1px solid rgba(255, 153, 0, 0.2);
      padding: 0.6rem 0;
    }
    .nav-container {
      max-width: 1280px;
      margin: 0 auto;
      padding: 0 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .logo {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-weight: 800;
      font-size: 1.2rem;
    }
    .logo svg {
      width: 36px;
      height: 36px;
    }
    .nav-links {
      display: flex;
      gap: 2rem;
    }
    .nav-links a {
      color: var(--text-secondary);
      text-decoration: none;
      font-weight: 500;
      transition: color 0.2s;
      position: relative;
    }
    .nav-links a:hover {
      color: var(--primary);
    }
    .nav-links a::after {
      content: '';
      position: absolute;
      bottom: -6px;
      left: 0;
      width: 0;
      height: 2px;
      background: var(--primary);
      transition: width 0.3s;
    }
    .nav-links a:hover::after {
      width: 100%;
    }
    .hamburger {
      display: none;
      background: none;
      border: none;
      color: white;
      font-size: 1.8rem;
      cursor: pointer;
    }
    .mobile-menu {
      position: fixed;
      top: 0;
      right: -100%;
      width: 70%;
      max-width: 300px;
      height: 100%;
      background: rgba(10,12,18,0.95);
      backdrop-filter: blur(20px);
      z-index: 200;
      padding: 5rem 2rem;
      transition: right 0.4s ease;
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
      border-left: 1px solid rgba(255, 153, 0, 0.2);
    }
    .mobile-menu.active {
      right: 0;
    }
    .mobile-menu a {
      color: var(--text-primary);
      text-decoration: none;
      font-size: 1.2rem;
      font-weight: 500;
      padding: 0.5rem 0;
    }
    .close-menu {
      position: absolute;
      top: 1rem;
      right: 1.5rem;
      font-size: 2rem;
      background: none;
      border: none;
      color: white;
      cursor: pointer;
    }

    .hero {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 6rem 1.5rem;
      position: relative;
    }
    .hero-content {
      max-width: 900px;
      z-index: 2;
    }
    .hero h1 {
      font-size: clamp(2.5rem, 7vw, 5rem);
      font-weight: 800;
      line-height: 1.2;
      margin-bottom: 1.5rem;
    }
    .hero-sub {
      font-size: 1.1rem;
      color: var(--text-secondary);
      margin-bottom: 1rem;
    }
    .hero-desc {
      color: var(--text-muted);
      max-width: 600px;
      margin: 0 auto 2rem;
    }
    .btn-group {
      display: flex;
      gap: 1rem;
      justify-content: center;
      flex-wrap: wrap;
    }

    .about-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.8rem;
      margin: 3rem 0;
    }
    .about-card {
      padding: 2rem;
      text-align: center;
    }
    .icon-circle {
      width: 60px;
      height: 60px;
      background: rgba(255, 153, 0, 0.1);
      border-radius: 1.2rem;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.2rem;
      font-size: 1.8rem;
    }

    .vision-block {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
      align-items: center;
      padding: 2rem;
    }
    .vision-list {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    .vision-list li {
      display: flex;
      gap: 1rem;
      align-items: flex-start;
    }

    .events-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 2rem;
      margin-top: 2rem;
    }
    .event-card {
      overflow: hidden;
    }
    .event-banner {
      height: 180px;
      background: linear-gradient(135deg, #1a1f2a, #0a0f18);
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .event-tag {
      position: absolute;
      top: 1rem;
      left: 1rem;
      background: rgba(255, 153, 0, 0.2);
      backdrop-filter: blur(4px);
      padding: 0.2rem 0.8rem;
      border-radius: 2rem;
      font-size: 0.7rem;
      font-weight: 600;
      color: #ff9900;
    }
    .event-content {
      padding: 1.5rem;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1.5rem;
      margin: 3rem auto;
      max-width: 1100px;
    }
    .stat-item {
      text-align: center;
      padding: 1.5rem;
    }
    .stat-number {
      font-size: 2.5rem;
      font-weight: 800;
      background: linear-gradient(135deg, #ff9900, #ffb347);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    .resources-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 2rem;
      margin: 2rem 0;
    }
    .resource-card {
      padding: 2rem;
      text-align: center;
    }

    .contact-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
      margin-top: 2rem;
    }
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }
    input, textarea {
      background: rgba(20, 25, 35, 0.7);
      border: 1px solid rgba(255, 153, 0, 0.2);
      border-radius: 1rem;
      padding: 0.9rem 1.2rem;
      color: white;
      font-family: inherit;
      transition: all 0.2s;
      width: 100%;
    }
    input:focus, textarea:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.15);
    }
    .newsletter-box {
      margin-top: 1rem;
      display: flex;
      gap: 0.5rem;
    }

    .countdown {
      background: rgba(255, 153, 0, 0.05);
      border-radius: 2rem;
      padding: 1rem;
      margin-top: 1rem;
      font-size: 0.9rem;
      display: inline-flex;
      gap: 1rem;
      justify-content: center;
      flex-wrap: wrap;
    }

    .back-to-top {
      position: fixed;
      bottom: 2rem;
      right: 2rem;
      background: var(--primary);
      color: #0a0c12;
      width: 45px;
      height: 45px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      opacity: 0;
      transition: all 0.3s;
      z-index: 99;
      border: none;
      font-size: 1.2rem;
    }
    .back-to-top.visible {
      opacity: 1;
    }

    @media (max-width: 768px) {
      .nav-links { display: none; }
      .hamburger { display: block; }
      .vision-block { grid-template-columns: 1fr; text-align: center; }
      .stats-grid { grid-template-columns: 1fr 1fr; }
      .contact-grid { grid-template-columns: 1fr; }
      .form-row { grid-template-columns: 1fr; }
      .newsletter-box { flex-direction: column; }
    }

    .container {
      max-width: 1280px;
      margin: 0 auto;
      padding: 0 1.5rem;
    }
    section {
      padding: 5rem 0;
    }
    .footer {
      border-top: 1px solid rgba(255, 153, 0, 0.15);
      padding: 3rem 0;
      text-align: center;
    }
    .social-links {
      display: flex;
      justify-content: center;
      gap: 1.2rem;
      margin: 1.5rem 0;
    }
    .social-links a {
      width: 40px;
      height: 40px;
      border-radius: 1rem;
      background: rgba(255,255,255,0.05);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
      color: var(--text-secondary);
      text-decoration: none;
    }
    .social-links a:hover {
      background: var(--primary);
      color: #0a0c12;
      transform: translateY(-3px);
    }
    .event-banner {
    height: 200px;
    position: relative;
    background-size: cover !important;
    background-position: center center !important;
    background-repeat: no-repeat !important;
    border-radius: var(--radius-md) var(--radius-md) 0 0;
    overflow: hidden;
}

.event-banner .overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    z-index: 1;
}

.event-tag {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: rgba(255, 153, 0, 0.9);
    backdrop-filter: blur(4px);
    padding: 0.3rem 1rem;
    border-radius: 2rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #0a0c12;
    z-index: 2;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
  </style>
</head>
<body>

<div class="gradient-bg"></div>
<div class="moving-orb orb1"></div>
<div class="moving-orb orb2"></div>
<div class="cursor-glow" id="cursorGlow"></div>

<nav class="navbar" id="navbar">
  <div class="nav-container">
    <div class="logo">
      <img src="assets\l.png" height="38px" width="auto" alt="">
      Cloud Club <span style="color:#ff9900;">TCOER</span>
    </div>
    <div class="nav-links">
      <a href="#home">Home</a>
      <a href="#about">About</a>
      <a href="#events">Events</a>
      <a href="member.php">Team</a>
      <a href="#resources">Resources</a>
      <a href="#contact">Contact</a>
    </div>
    <button class="hamburger" id="menuToggle">☰</button>
  </div>
</nav>

<div class="mobile-menu" id="mobileMenu">
  <button class="close-menu" id="closeMenu">✕</button>
  <a href="#home">Home</a>
  <a href="#about">About</a>
  <a href="#events">Events</a>
  <a href="#resources">Resources</a>
  <a href="#contact">Contact</a>
</div>

<section id="home" class="hero">
  <div class="hero-content">
    <span class="section-tag">⚡ Official AWS Initiative</span>
    <h1>AWS Cloud Club <span class="gradient-text">TCOER</span></h1>
    <p class="hero-sub">Trinity College of Engineering and Research, Pune</p>
    <p class="hero-desc">Empowering students to build cloud-aligned tech, earn certifications, and lead the next wave of cloud innovation.</p>
    <div class="btn-group">
      <button class="btn-primary" onclick="document.getElementById('about').scrollIntoView({behavior:'smooth'})">Explore More</button>
      <button class="btn-outline" onclick="document.getElementById('contact').scrollIntoView({behavior:'smooth'})">Join Community</button>
    </div>
    <div class="countdown" id="countdownContainer">
      <span>🔥 Next Event:</span>
      <span id="countdownTimer">--d --h --m --s</span>
    </div>
  </div>
</section>

<section id="about">
  <div class="container">
    <div style="text-align:center;">
      <span class="section-tag">About Us</span>
      <h2 style="font-size:2.5rem; margin-bottom:1rem;">About <span class="gradient-text">AWS Cloud Club</span></h2>
    </div>
    <div class="about-grid">
      <div class="glass-card about-card">
        <div class="icon-circle">☁️</div>
        <h3 style="margin-bottom:0.75rem;">What is AWS Cloud Club?</h3>
        <p style="color:var(--text-secondary);">Student-led communities that help students learn cloud computing through hands-on projects, workshops, and AWS resources.</p>
      </div>
      <div class="glass-card about-card">
        <div class="icon-circle">🚀</div>
        <h3 style="margin-bottom:0.75rem;">Why AWS Cloud Club?</h3>
        <p style="color:var(--text-secondary);">Real-world learning, certifications, professional portfolios, and access to a global network of cloud professionals.</p>
      </div>
      <div class="glass-card about-card">
        <div class="icon-circle">📜</div>
        <h3 style="margin-bottom:0.75rem;">Certification Support</h3>
        <p style="color:var(--text-secondary);">Free exam prep sessions, practice tests, and AWS vouchers for Cloud Practitioner & Associates.</p>
      </div>
      <div class="glass-card about-card">
        <div class="icon-circle">🧪</div>
        <h3 style="margin-bottom:0.75rem;">AWS Cloud Labs</h3>
        <p style="color:var(--text-secondary);">Sandbox environments to practice EC2, S3, Lambda, and more without any cost.</p>
      </div>
    </div>

    <div class="glass-card vision-block">
      <div style="text-align:center;">
        <div style="width:180px;height:180px;margin:0 auto;border-radius:50%;background:radial-gradient(circle, rgba(255,153,0,0.15), transparent);display:flex;align-items:center;justify-content:center;">
          <svg width="100" height="100" viewBox="0 0 100 100" fill="none">
            <circle cx="50" cy="50" r="35" stroke="#ff9900" stroke-width="2" fill="none"/>
            <circle cx="50" cy="50" r="45" stroke="#ffb347" stroke-width="1" stroke-dasharray="3 3"/>
            <path d="M35 65c5-12 8-20 15-20s10 8 15 20" stroke="#ffb347" stroke-width="2" fill="none"/>
            <circle cx="50" cy="42" r="5" fill="#ff9900"/>
          </svg>
        </div>
      </div>
      <div>
        <h3 style="font-size:1.8rem; margin-bottom:1rem;">Our Vision & <span class="gradient-text">Objective</span></h3>
        <ul class="vision-list">
          <li>✦ Promote cloud literacy and AWS platforms, innovation in cloud technology.</li>
          <li>✦ Foster a collaborative community of cloud-tech enthusiasts and problem solvers.</li>
          <li>✦ Help students gain industry-relevant skills, certifications, and career opportunities.</li>
        </ul>
      </div>
    </div>
  </div>
</section>
<section id="events">
  <div class="container">
    <div style="text-align:center;">
      <span class="section-tag">Events</span>
      <h2 style="font-size:2rem;">Our <span class="gradient-text">Events</span></h2>
      <p style="color:var(--text-muted); margin-bottom:2rem;">Workshops, community days, AWS Meetups, AMA sessions, and more.</p>
    </div>
    <div class="events-grid">
      <?php
      $stmt = $pdo->prepare("SELECT * FROM events ORDER BY FIELD(type, 'Upcoming', 'Featured', 'AI/ML'), event_date ASC");
      $stmt->execute();
      $events = $stmt->fetchAll();
      
      $defaultImage = 'uploads/default-event.jpg';
      $defaultImageExists = file_exists(__DIR__ . '/' . $defaultImage);
      
      if (count($events) > 0):
        foreach ($events as $event):
          $bgStyle = '';
          if (!empty($event['image_url']) && file_exists(__DIR__ . '/' . $event['image_url'])) {
              $bgStyle = "background-image: url('" . htmlspecialchars($event['image_url']) . "'); background-size: cover; background-position: center;";
          } else {
              if ($defaultImageExists) {
                  $bgStyle = "background-image: url('$defaultImage'); background-size: cover; background-position: center;";
              } else {
                  $bgStyle = "background: linear-gradient(135deg, #232f3e 0%, #1a2530 100%);";
              }
          }
      ?>
      <div class="glass-card event-card">
        <div class="event-banner" style="<?= $bgStyle ?>">
          <div class="overlay"></div>
          <span class="event-tag"><?= htmlspecialchars($event['type']) ?></span>
        </div>
        <div class="event-content">
          <div style="display:flex; gap:1rem; color:var(--primary); font-size:0.8rem; margin-bottom:0.5rem;">
            <span>🗓️ <?= htmlspecialchars($event['event_date']) ?></span>
            <span>📍 <?= htmlspecialchars($event['location']) ?></span>
          </div>
          <h3><?= htmlspecialchars($event['title']) ?></h3>
          <p><?= htmlspecialchars($event['description']) ?></p>
          <div style="display:flex; gap:1rem; margin-top:1.5rem;">
            <button class="btn-primary" onclick="window.location.href='<?= htmlspecialchars($event['register_link']) ?>'">Details →</button>
            <button class="btn-outline" onclick="window.location.href='<?= htmlspecialchars($event['register_link']) ?>'">Register</button>
          </div>
        </div>
      </div>
      <?php endforeach; else: ?>
      <p>No events available.</p>
      <?php endif; ?>
    </div>
  </div>
</section>
<section>
  <div class="container">
    <div class="stats-grid">
      <?php
      $stats = $pdo->query("SELECT * FROM stats ORDER BY display_order ASC")->fetchAll();
      foreach ($stats as $stat):
      ?>
        <div class="glass-card stat-item">
          <div style="font-size:2rem;"><?= $stat['icon'] ?></div>
          <div class="stat-number" data-target="<?= (int)$stat['value'] ?>">0</div>
          <div><?= htmlspecialchars($stat['label']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section id="resources">
  <div class="container">
    <div style="text-align:center;">
      <span class="section-tag">Learning Hub</span>
      <h2 style="font-size:2rem;">Resources & <span class="gradient-text">Tools</span></h2>
      <p style="color:var(--text-muted); margin-bottom:2rem;">Curated materials to accelerate your cloud journey</p>
    </div>
    <div class="resources-grid">
      <div class="glass-card resource-card">
        <div style="font-size:2.5rem;">📘</div>
        <h3>AWS Skill Builder</h3>
        <p style="color:var(--text-secondary);">Free digital training, learning plans, and exam readiness.</p>
        <button class="btn-outline" style="margin-top:1rem; padding:0.5rem 1rem;">Explore →</button>
      </div>
      <div class="glass-card resource-card">
        <div style="font-size:2.5rem;">🎮</div>
        <h3>AWS Cloud Quest</h3>
        <p style="color:var(--text-secondary);">Role-playing game to learn cloud skills in a fun way.</p>
        <button class="btn-outline" style="margin-top:1rem; padding:0.5rem 1rem;">Play →</button>
      </div>
      <div class="glass-card resource-card">
        <div style="font-size:2.5rem;">🏆</div>
        <h3>Student Builder Program</h3>
        <p style="color:var(--text-secondary);">Earn AWS credits, swag, and mentorship opportunities.</p>
        <button class="btn-outline" style="margin-top:1rem; padding:0.5rem 1rem;">Apply →</button>
      </div>
    </div>
  </div>
</section>

<section id="contact">
  <div class="container">
    <div style="text-align:center;">
      <span class="section-tag">Get in Touch</span>
      <h2 style="font-size:2rem;">Contact <span class="gradient-text">Us</span></h2>
      <p style="color:var(--text-muted);">Fill out the form and we'll get back to you.</p>
    </div>
    <div class="contact-grid">
      <form class="glass-card" style="padding:2rem;" id="contactForm">
        <div class="form-row">
          <input type="text" placeholder="Your Name" required>
          <input type="email" placeholder="Email Address" required>
        </div>
        <input type="text" placeholder="Phone (optional)">
        <textarea rows="4" placeholder="Your message..."></textarea>
        <button type="submit" class="btn-primary" style="width:100%; margin-top:1rem;">Send Message →</button>
        <p id="formFeedback" style="margin-top:1rem; font-size:0.8rem; color:#ff9900; text-align:center;"></p>
        <div class="newsletter-box">
          <input type="email" placeholder="Subscribe to newsletter" style="flex:1;">
          <button class="btn-outline" style="padding:0.5rem 1rem;">Subscribe</button>
        </div>
      </form>
      <div class="glass-card" style="padding:2rem;">
        <h3>Be part of the <span class="gradient-text">Community</span></h3>
        <p style="color:var(--text-muted); margin:1rem 0 2rem;">Connect with us and start your cloud journey today.</p>
        <div style="display:flex; flex-direction:column; gap:1.2rem;">
          <div style="display:flex; align-items:center; gap:1rem;"><span style="font-size:1.4rem;">📧</span> cloudclubstcoer@tcoer.com</div>
          <div style="display:flex; align-items:center; gap:1rem;"><span style="font-size:1.4rem;">💬</span> Reach us through social media</div>
          <div style="display:flex; align-items:center; gap:1rem;"><span style="font-size:1.4rem;">📞</span> Join us to grow your career</div>
        </div>
      </div>
    </div>
  </div>
</section>

<footer class="footer">
  <div class="container">
    <div class="logo" style="justify-content:center; margin-bottom:1rem;">
      <svg width="32" height="32" viewBox="0 0 36 36" fill="none">
        <circle cx="18" cy="18" r="16" stroke="#ff9900" stroke-width="2"/>
        <path d="M10 22c2-6 4-10 8-10s6 4 8 10" stroke="#ffb347" stroke-width="2"/>
        <circle cx="18" cy="14" r="3" fill="#ff9900"/>
      </svg>
      AWS Cloud Club TCOER
    </div>
    <div class="social-links">
      <a href="#">𝕏</a>
      <a href="#">in</a>
      <a href="#">🐙</a>
      <a href="#">📷</a>
    </div>
    <p style="color:var(--text-muted); font-size:0.8rem;">© 2025 AWS CLUB TCOER — All rights reserved.</p>
  </div>
</footer>

<button class="back-to-top" id="backToTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">↑</button>

<script>
  window.addEventListener('scroll', () => {
    const navbar = document.getElementById('navbar');
    if (window.scrollY > 50) navbar.classList.add('scrolled');
    else navbar.classList.remove('scrolled');
    const backBtn = document.getElementById('backToTop');
    if (window.scrollY > 300) backBtn.classList.add('visible');
    else backBtn.classList.remove('visible');
  });

  const menuToggle = document.getElementById('menuToggle');
  const mobileMenu = document.getElementById('mobileMenu');
  const closeMenu = document.getElementById('closeMenu');
  function openMenu() { mobileMenu.classList.add('active'); }
  function closeMenuFn() { mobileMenu.classList.remove('active'); }
  menuToggle.addEventListener('click', openMenu);
  closeMenu.addEventListener('click', closeMenuFn);
  document.querySelectorAll('.mobile-menu a').forEach(link => link.addEventListener('click', closeMenuFn));

  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href === "#" || href === "") return;
      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        closeMenuFn();
      }
    });
  });

  const cursorGlow = document.getElementById('cursorGlow');
  document.addEventListener('mousemove', (e) => {
    cursorGlow.style.opacity = '1';
    cursorGlow.style.transform = `translate(${e.clientX}px, ${e.clientY}px) translate(-50%, -50%)`;
  });
  document.addEventListener('mouseleave', () => cursorGlow.style.opacity = '0');

  function updateCountdown() {
    const eventDate = new Date(2025, 0, 24, 10, 0, 0).getTime();
    const now = new Date().getTime();
    const diff = eventDate - now;
    if (diff <= 0) {
      document.getElementById('countdownTimer').innerHTML = "Event Live! 🎉";
      return;
    }
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (86400000)) / 3600000);
    const mins = Math.floor((diff % 3600000) / 60000);
    const secs = Math.floor((diff % 60000) / 1000);
    document.getElementById('countdownTimer').innerHTML = `${days}d ${hours}h ${mins}m ${secs}s`;
  }
  setInterval(updateCountdown, 1000);
  updateCountdown();

  function animateNumber(element, target) {
    let current = 0;
    const increment = target / 50;
    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        element.innerText = target;
        clearInterval(timer);
      } else {
        element.innerText = Math.floor(current);
      }
    }, 20);
  }
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const target = parseInt(el.getAttribute('data-target'));
        animateNumber(el, target);
        observer.unobserve(el);
      }
    });
  }, { threshold: 0.5 });
  document.querySelectorAll('.stat-number').forEach(el => observer.observe(el));

  const contactForm = document.getElementById('contactForm');
  const feedback = document.getElementById('formFeedback');
  contactForm.addEventListener('submit', (e) => {
    e.preventDefault();
    feedback.innerText = '✨ Message sent! Our team will reach out soon.';
    contactForm.reset();
    setTimeout(() => feedback.innerText = '', 3000);
  });

  const cards = document.querySelectorAll('.glass-card');
  cards.forEach(card => {
    card.addEventListener('mousemove', (e) => {
      const rect = card.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      const centerX = rect.width / 2;
      const centerY = rect.height / 2;
      const rotateX = (y - centerY) / 20;
      const rotateY = (centerX - x) / 20;
      card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-4px)`;
    });
    card.addEventListener('mouseleave', () => {
      card.style.transform = '';
    });
  });
</script>
</body>
</html>
