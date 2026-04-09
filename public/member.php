<?php
require_once __DIR__ . '/../config/db.php';

// Fetch all teams with their members
$teams = $pdo->query("SELECT * FROM teams ORDER BY id ASC")->fetchAll();

// For each team, fetch its members
$teamMembers = [];
foreach ($teams as $team) {
    $stmt = $pdo->prepare("SELECT * FROM members WHERE team_id = ? ORDER BY id ASC");
    $stmt->execute([$team['id']]);
    $teamMembers[$team['id']] = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <title>AWS Cloud Club TCOER | Our Members</title>
  <meta name="description" content="Meet the core team, technical leads, outreach volunteers, and faculty advisor of AWS Cloud Club TCOER – grouped by teams." />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    /* ========== ALL YOUR EXISTING CSS STYLES GO HERE ========== */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    :root {
      --bg-deep: #0a0c12; --bg-surface: #0f1117; --primary: #ff9900;
      --primary-dark: #ec7211; --accent: #232f3e; --text-primary: #f0f4fa;
      --text-secondary: #9aa4bf; --text-muted: #6c7a91;
      --radius-md: 1rem; --transition-smooth: 0.4s cubic-bezier(0.16,1,0.3,1);
    }
    body { font-family: 'Inter', sans-serif; background-color: var(--bg-deep); color: var(--text-primary); line-height: 1.5; overflow-x: hidden; }
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: var(--bg-surface); }
    ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }
    .gradient-bg {
      position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -2;
      background: radial-gradient(circle at 20% 30%, rgba(255,153,0,0.08) 0%, transparent 50%),
                  radial-gradient(circle at 80% 70%, rgba(35,47,62,0.1) 0%, transparent 50%),
                  linear-gradient(135deg, #0a0c12 0%, #11161f 100%);
    }
    .moving-orb {
      position: fixed; border-radius: 50%; filter: blur(70px); opacity: 0.4; z-index: -1;
      animation: floatOrb 18s infinite alternate ease-in-out;
    }
    .orb1 { width: 60vw; height: 60vw; background: radial-gradient(circle, rgba(255,153,0,0.2), transparent); top: -20vh; left: -30vw; animation-duration: 25s; }
    .orb2 { width: 50vw; height: 50vw; background: radial-gradient(circle, rgba(35,47,62,0.25), transparent); bottom: -20vh; right: -20vw; animation-duration: 20s; animation-delay: -5s; }
    @keyframes floatOrb { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(5%,8%) scale(1.1); } }
    .glass-card {
      background: rgba(18,22,32,0.65); backdrop-filter: blur(12px);
      border: 1px solid rgba(255,153,0,0.15); border-radius: var(--radius-md);
      transition: all var(--transition-smooth);
    }
    .glass-card:hover { border-color: rgba(255,153,0,0.4); box-shadow: 0 20px 40px -12px rgba(0,0,0,0.4); transform: translateY(-4px); }
    .gradient-text { background: linear-gradient(135deg, #ff9900 0%, #ffb347 80%); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .section-tag {
      display: inline-block; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;
      padding: 0.3rem 1rem; border-radius: 2rem; background: rgba(255,153,0,0.1);
      border: 1px solid rgba(255,153,0,0.25); color: var(--primary); margin-bottom: 1rem;
    }
    .navbar {
      position: fixed; top: 0; left: 0; width: 100%; z-index: 100; padding: 1rem 0; transition: all 0.4s ease;
    }
    .navbar.scrolled { background: rgba(10,12,18,0.85); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(255,153,0,0.2); padding: 0.6rem 0; }
    .nav-container { max-width: 1280px; margin: 0 auto; padding: 0 2rem; display: flex; justify-content: space-between; align-items: center; }
    .logo { display: flex; align-items: center; gap: 0.6rem; font-weight: 800; font-size: 1.2rem; }
    .logo svg { width: 36px; height: 36px; }
    .nav-links { display: flex; gap: 2rem; }
    .nav-links a { color: var(--text-secondary); text-decoration: none; font-weight: 500; transition: color 0.2s; position: relative; }
    .nav-links a:hover { color: var(--primary); }
    .hamburger { display: none; background: none; border: none; color: white; font-size: 1.8rem; cursor: pointer; }
    .mobile-menu {
      position: fixed; top: 0; right: -100%; width: 70%; max-width: 300px; height: 100%;
      background: rgba(10,12,18,0.95); backdrop-filter: blur(20px); z-index: 200; padding: 5rem 2rem;
      transition: right 0.4s ease; display: flex; flex-direction: column; gap: 1.5rem;
      border-left: 1px solid rgba(255,153,0,0.2);
    }
    .mobile-menu.active { right: 0; }
    .mobile-menu a { color: var(--text-primary); text-decoration: none; font-size: 1.2rem; font-weight: 500; }
    .close-menu { position: absolute; top: 1rem; right: 1.5rem; font-size: 2rem; background: none; border: none; color: white; cursor: pointer; }
    .page-hero { padding-top: 8rem; padding-bottom: 2rem; text-align: center; }
    .container { max-width: 1280px; margin: 0 auto; padding: 0 1.5rem; }
    .team-section { margin-bottom: 3rem; }
    .team-title { font-size: 1.5rem; margin-bottom: 1.2rem; padding-bottom: 0.3rem; border-bottom: 2px solid rgba(255,153,0,0.3); display: inline-block; }
    .members-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem; }
    .member-card { padding: 1.25rem; text-align: center; }
    .member-avatar {
      width: 80px; height: 80px; margin: 0 auto 0.75rem;
      background: linear-gradient(135deg, #ff9900, #ec7211);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      font-weight: bold;
      color: #0a0c12;
      text-transform: uppercase;
      overflow: hidden;
    }
    .member-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .member-name { font-size: 1.15rem; font-weight: 700; margin-bottom: 0.2rem; }
    .member-role { color: var(--primary); font-weight: 600; font-size: 0.8rem; margin-bottom: 0.5rem; }
    .member-bio { color: var(--text-secondary); font-size: 0.75rem; margin-bottom: 0.75rem; line-height: 1.4; }
    .member-responsibilities {
      background: rgba(255,153,0,0.05); border-radius: 0.75rem; padding: 0.6rem; margin: 0.75rem 0; text-align: left;
    }
    .member-responsibilities p { font-size: 0.7rem; color: var(--text-muted); margin-bottom: 0.2rem; }
    .member-responsibilities strong { color: var(--text-secondary); font-size: 0.7rem; }
    .member-links { display: flex; justify-content: center; gap: 1rem; margin-top: 0.5rem; }
    .member-links a { color: var(--text-muted); text-decoration: none; font-size: 1.1rem; transition: color 0.2s; }
    .member-links a:hover { color: var(--primary); }
    @media (max-width: 768px) { .nav-links { display: none; } .hamburger { display: block; } .team-title { font-size: 1.3rem; } .members-grid { grid-template-columns: 1fr; } }
    .footer { border-top: 1px solid rgba(255,153,0,0.15); padding: 2.5rem 0; text-align: center; margin-top: 2rem; }
    .social-links { display: flex; justify-content: center; gap: 1.2rem; margin: 1.2rem 0; }
    .social-links a { width: 38px; height: 38px; border-radius: 0.8rem; background: rgba(255,255,255,0.05); display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; color: var(--text-secondary); text-decoration: none; }
    .social-links a:hover { background: var(--primary); color: #0a0c12; }
    .cursor-glow {
      width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,153,0,0.08) 0%, transparent 70%);
      position: fixed; pointer-events: none; border-radius: 50%; z-index: 9999;
      transform: translate(-50%, -50%); transition: transform 0.05s linear; opacity: 0;
    }
    .back-to-top {
      position: fixed; bottom: 2rem; right: 2rem; background: var(--primary); color: #0a0c12;
      width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
      cursor: pointer; opacity: 0; transition: all 0.3s; z-index: 99; border: none; font-size: 1.1rem;
    }
    .back-to-top.visible { opacity: 1; }
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
      <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
        <circle cx="18" cy="18" r="16" stroke="#ff9900" stroke-width="2" fill="rgba(255,153,0,0.1)"/>
        <path d="M10 22c2-6 4-10 8-10s6 4 8 10" stroke="#ffb347" stroke-width="2" fill="none" stroke-linecap="round"/>
        <circle cx="18" cy="14" r="3" fill="#ff9900"/>
      </svg>
      AWS Cloud Club <span style="color:#ff9900;">TCOER</span>
    </div>
    <div class="nav-links">
      <a href="index.php">Home</a>
      <a href="events.php">Events</a>
      <a href="members.php" style="color:#ff9900;">Members</a>
      <a href="index.php#contact">Contact</a>
    </div>
    <button class="hamburger" id="menuToggle">☰</button>
  </div>
</nav>

<div class="mobile-menu" id="mobileMenu">
  <button class="close-menu" id="closeMenu">✕</button>
  <a href="index.php">Home</a>
  <a href="index.php">Events</a>
  <a href="members.php">Members</a>
  <a href="index.php#contact">Contact</a>
</div>

<section class="page-hero">
  <div class="container">
    <span class="section-tag">Our Crew</span>
    <h1 style="font-size: clamp(2rem, 6vw, 3rem); margin-bottom: 0.5rem;">Meet the <span class="gradient-text">Members</span></h1>
    <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto; font-size: 0.9rem;">Organized by teams – all members are displayed dynamically from the database.</p>
  </div>
</section>

<div class="container">
  <?php foreach ($teams as $team): ?>
    <?php $members = $teamMembers[$team['id']]; ?>
    <?php if (count($members) > 0): ?>
      <div class="team-section">
        <h2 class="team-title"><?= htmlspecialchars($team['icon']) ?> <?= htmlspecialchars($team['name']) ?></h2>
        <div class="members-grid">
          <?php foreach ($members as $member): ?>
            <?php
              // Generate avatar initials (fallback if no image)
              $nameParts = explode(' ', trim($member['name']));
              $initials = '';
              if (count($nameParts) >= 2) {
                  $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
              } else {
                  $initials = strtoupper(substr($member['name'], 0, 2));
              }
              // Check if image exists
              $hasImage = !empty($member['image_url']) && file_exists(__DIR__ . '/' . $member['image_url']);
            ?>
            <div class="glass-card member-card">
              <div class="member-avatar">
                <?php if ($hasImage): ?>
                  <img src="<?= htmlspecialchars($member['image_url']) ?>" alt="<?= htmlspecialchars($member['name']) ?>">
                <?php else: ?>
                  <?= htmlspecialchars($initials) ?>
                <?php endif; ?>
              </div>
              <div class="member-name"><?= htmlspecialchars($member['name']) ?></div>
              <div class="member-role"><?= htmlspecialchars($member['role']) ?></div>
              <div class="member-bio"><?= nl2br(htmlspecialchars($member['bio'])) ?></div>
              <?php if (!empty($member['bio'])): ?>
              <div class="member-responsibilities">
                <p><strong>🎯 Responsibilities:</strong> <?= htmlspecialchars($member['bio']) ?></p>
              </div>
              <?php endif; ?>
              <div class="member-links">
                <?php if (!empty($member['linkedin'])): ?>
                  <a href="<?= htmlspecialchars($member['linkedin']) ?>" target="_blank"><i class="fab fa-linkedin"></i></a>
                <?php endif; ?>
                <?php if (!empty($member['instagram'])): ?>
                  <a href="<?= htmlspecialchars($member['instagram']) ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
</div>

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
      <a href="#"><i class="fab fa-twitter"></i></a>
      <a href="#"><i class="fab fa-linkedin-in"></i></a>
      <a href="#"><i class="fab fa-github"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
    </div>
    <p style="color:var(--text-muted); font-size:0.75rem;">© 2025 AWS CLUB TCOER — Built by students, for students.</p>
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

  const cursorGlow = document.getElementById('cursorGlow');
  document.addEventListener('mousemove', (e) => {
    cursorGlow.style.opacity = '1';
    cursorGlow.style.transform = `translate(${e.clientX}px, ${e.clientY}px) translate(-50%, -50%)`;
  });
  document.addEventListener('mouseleave', () => cursorGlow.style.opacity = '0');
</script>
</body>
</html>