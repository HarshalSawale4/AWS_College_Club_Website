<?php
session_start();
require_once 'config/db.php';

// Hardcoded admin credentials
$admin_user = 'admin';
$admin_pass_hash = password_hash('admin123', PASSWORD_DEFAULT);

if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        if ($_POST['username'] === 'admin' && password_verify($_POST['password'], $admin_pass_hash)) {
            $_SESSION['admin_logged_in'] = true;
            header('Location: admin.php');
            exit;
        } else $error = "Invalid credentials";
    }
    // Show login form
    ?>
    <!DOCTYPE html><html><head><title>Admin Login</title><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet"><style>body{font-family:Inter;background:#0a0c12;color:#fff;display:flex;justify-content:center;align-items:center;height:100vh;}.login-box{background:rgba(18,22,32,0.9);padding:2rem;border-radius:1rem;border:1px solid #ff9900;width:300px;}input,button{width:100%;margin:0.5rem 0;padding:0.5rem;border-radius:0.5rem;}button{background:#ff9900;color:#000;font-weight:bold;cursor:pointer;}</style></head>
    <body><div class="login-box"><h2>Admin Login</h2><?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?><form method="POST"><input type="text" name="username" placeholder="Username" required><input type="password" name="password" placeholder="Password" required><button type="submit" name="login">Login</button></form></div></body></html>
    <?php exit;
}

// Handle all CRUD actions (simplified – you can expand)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Teams
    if (isset($_POST['add_team'])) {
        $stmt = $pdo->prepare("INSERT INTO teams (name, description, icon) VALUES (?,?,?)");
        $stmt->execute([$_POST['name'], $_POST['description'], $_POST['icon']]);
    } elseif (isset($_POST['edit_team'])) {
        $stmt = $pdo->prepare("UPDATE teams SET name=?, description=?, icon=? WHERE id=?");
        $stmt->execute([$_POST['name'], $_POST['description'], $_POST['icon'], $_POST['id']]);
    } elseif (isset($_GET['delete_team'])) {
        $pdo->prepare("DELETE FROM teams WHERE id=?")->execute([$_GET['delete_team']]);
    }
    // Members
    elseif (isset($_POST['add_member'])) {
        $stmt = $pdo->prepare("INSERT INTO members (name, role, bio, team_id, linkedin, instagram) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$_POST['name'], $_POST['role'], $_POST['bio'], $_POST['team_id'], $_POST['linkedin'], $_POST['instagram']]);
    } elseif (isset($_POST['edit_member'])) {
        $stmt = $pdo->prepare("UPDATE members SET name=?, role=?, bio=?, team_id=?, linkedin=?, instagram=? WHERE id=?");
        $stmt->execute([$_POST['name'], $_POST['role'], $_POST['bio'], $_POST['team_id'], $_POST['linkedin'], $_POST['instagram'], $_POST['id']]);
    } elseif (isset($_GET['delete_member'])) {
        $pdo->prepare("DELETE FROM members WHERE id=?")->execute([$_GET['delete_member']]);
    }
    // Events
    elseif (isset($_POST['add_event'])) {
        $stmt = $pdo->prepare("INSERT INTO events (title, event_date, location, type, description, register_link, icon_emoji) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$_POST['title'], $_POST['event_date'], $_POST['location'], $_POST['type'], $_POST['description'], $_POST['register_link'], $_POST['icon_emoji']]);
    } elseif (isset($_POST['edit_event'])) {
        $stmt = $pdo->prepare("UPDATE events SET title=?, event_date=?, location=?, type=?, description=?, register_link=?, icon_emoji=? WHERE id=?");
        $stmt->execute([$_POST['title'], $_POST['event_date'], $_POST['location'], $_POST['type'], $_POST['description'], $_POST['register_link'], $_POST['icon_emoji'], $_POST['id']]);
    } elseif (isset($_GET['delete_event'])) {
        $pdo->prepare("DELETE FROM events WHERE id=?")->execute([$_GET['delete_event']]);
    }
    // Stats
    elseif (isset($_POST['add_stat'])) {
        $stmt = $pdo->prepare("INSERT INTO stats (label, value, icon, display_order) VALUES (?,?,?,?)");
        $stmt->execute([$_POST['label'], $_POST['value'], $_POST['icon'], $_POST['display_order']]);
    } elseif (isset($_POST['edit_stat'])) {
        $stmt = $pdo->prepare("UPDATE stats SET label=?, value=?, icon=?, display_order=? WHERE id=?");
        $stmt->execute([$_POST['label'], $_POST['value'], $_POST['icon'], $_POST['display_order'], $_POST['id']]);
    } elseif (isset($_GET['delete_stat'])) {
        $pdo->prepare("DELETE FROM stats WHERE id=?")->execute([$_GET['delete_stat']]);
    }
    // Resources
    elseif (isset($_POST['add_resource'])) {
        $stmt = $pdo->prepare("INSERT INTO resources (title, description, link, icon) VALUES (?,?,?,?)");
        $stmt->execute([$_POST['title'], $_POST['description'], $_POST['link'], $_POST['icon']]);
    } elseif (isset($_POST['edit_resource'])) {
        $stmt = $pdo->prepare("UPDATE resources SET title=?, description=?, link=?, icon=? WHERE id=?");
        $stmt->execute([$_POST['title'], $_POST['description'], $_POST['link'], $_POST['icon'], $_POST['id']]);
    } elseif (isset($_GET['delete_resource'])) {
        $pdo->prepare("DELETE FROM resources WHERE id=?")->execute([$_GET['delete_resource']]);
    }
    // Contact
    elseif (isset($_POST['update_contact'])) {
        $pdo->prepare("UPDATE contact SET email=?, social_message=?, phone_info=? WHERE id=1")->execute([$_POST['email'], $_POST['social_message'], $_POST['phone_info']]);
    }
    header('Location: admin.php');
    exit;
}

// Fetch all data for display
$teams = $pdo->query("SELECT * FROM teams")->fetchAll();
$members = $pdo->query("SELECT m.*, t.name as team_name FROM members m LEFT JOIN teams t ON m.team_id = t.id")->fetchAll();
$events = $pdo->query("SELECT * FROM events")->fetchAll();
$stats = $pdo->query("SELECT * FROM stats ORDER BY display_order")->fetchAll();
$resources = $pdo->query("SELECT * FROM resources")->fetchAll();
$contact = $pdo->query("SELECT * FROM contact WHERE id=1")->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>AWS Club Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; background: #0a0c12; color: #f0f4fa; padding: 2rem; }
    .container { max-width: 1400px; margin: 0 auto; }
    h1, h2 { margin-bottom: 1rem; }
    .card { background: rgba(18,22,32,0.8); backdrop-filter: blur(10px); border: 1px solid #ff990033; border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #333; }
    .btn { background: #ff9900; color: #000; border: none; padding: 0.5rem 1rem; border-radius: 2rem; cursor: pointer; display: inline-block; margin: 0.2rem; text-decoration: none; font-size: 0.85rem; }
    .btn-danger { background: #dc3545; color: #fff; }
    .btn-outline { background: transparent; border: 1px solid #ff9900; color: #ff9900; }
    input, textarea, select { width: 100%; padding: 0.5rem; margin: 0.5rem 0; background: #1e1e2f; border: 1px solid #ff990033; border-radius: 0.5rem; color: white; }
    .form-row { display: flex; gap: 1rem; margin-bottom: 1rem; }
    .logout { text-align: right; margin-bottom: 1rem; }
    .nav-tabs { display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; }
    .nav-tab { background: transparent; border: none; color: var(--text-secondary); padding: 0.5rem 1rem; cursor: pointer; border-bottom: 2px solid transparent; }
    .nav-tab.active { color: #ff9900; border-bottom-color: #ff9900; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
  </style>
</head>
<body>
<div class="container">
  <div class="logout"><a href="?logout=1" class="btn btn-outline" onclick="return confirm('Logout?')">Logout</a></div>
  <h1>🗂️ AWS Club Admin Dashboard</h1>

  <div class="nav-tabs">
    <button class="nav-tab active" onclick="showTab('teams')">Teams</button>
    <button class="nav-tab" onclick="showTab('members')">Members</button>
    <button class="nav-tab" onclick="showTab('events')">Events</button>
    <button class="nav-tab" onclick="showTab('stats')">Stats</button>
    <button class="nav-tab" onclick="showTab('resources')">Resources</button>
    <button class="nav-tab" onclick="showTab('contact')">Contact</button>
  </div>

  <!-- TEAMS TAB -->
  <div id="teams" class="tab-content active">
    <div class="card"><h2>➕ Add Team</h2><form method="POST"><div class="form-row"><input type="text" name="name" placeholder="Team Name" required><input type="text" name="description" placeholder="Description"><input type="text" name="icon" placeholder="Icon (emoji)" value="🤝"></div><button type="submit" name="add_team" class="btn">Add Team</button></form></div>
    <div class="card"><h2>📋 Teams List</h2><table><thead><tr><th>Icon</th><th>Name</th><th>Description</th><th>Actions</th></tr></thead><tbody><?php foreach($teams as $t): ?><tr><td><?= htmlspecialchars($t['icon']) ?></td><td><?= htmlspecialchars($t['name']) ?></td><td><?= htmlspecialchars($t['description']) ?></td><td><a href="?edit_team=<?= $t['id'] ?>" class="btn btn-outline" onclick="editTeam(<?= htmlspecialchars(json_encode($t)) ?>)">Edit</a> <a href="?delete_team=<?= $t['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete?')">Del</a></td></tr><?php endforeach; ?></tbody></table></div>
  </div>

  <!-- MEMBERS TAB (similar structure – you can expand) -->
  <div id="members" class="tab-content"><div class="card"><h2>➕ Add Member</h2><form method="POST"><div class="form-row"><input type="text" name="name" placeholder="Name" required><input type="text" name="role" placeholder="Role" required></div><textarea name="bio" placeholder="Bio"></textarea><select name="team_id"><?php foreach($teams as $t): ?><option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option><?php endforeach; ?></select><div class="form-row"><input type="text" name="linkedin" placeholder="LinkedIn URL"><input type="text" name="instagram" placeholder="Instagram URL"></div><button type="submit" name="add_member" class="btn">Add Member</button></form></div><div class="card"><h2>📋 Members</h2><table><thead><tr><th>Name</th><th>Role</th><th>Team</th><th>Actions</th></tr></thead><tbody><?php foreach($members as $m): ?><tr><td><?= htmlspecialchars($m['name']) ?></td><td><?= htmlspecialchars($m['role']) ?></td><td><?= htmlspecialchars($m['team_name']) ?></td><td><a href="?edit_member=<?= $m['id'] ?>" class="btn btn-outline">Edit</a> <a href="?delete_member=<?= $m['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete?')">Del</a></td></tr><?php endforeach; ?></tbody></table></div></div>

  <!-- EVENTS TAB -->
  <div id="events" class="tab-content"><div class="card"><h2>➕ Add Event</h2><form method="POST"><div class="form-row"><input type="text" name="title" placeholder="Title" required><input type="text" name="event_date" placeholder="Date (e.g., 24 Jan, 2025)" required><input type="text" name="location" placeholder="Location" required></div><div class="form-row"><select name="type"><option>Upcoming</option><option>Featured</option><option>AI/ML</option></select><input type="text" name="icon_emoji" placeholder="Icon Emoji" value="📅"><input type="text" name="register_link" placeholder="Register Link" value="#"></div><textarea name="description" placeholder="Description" rows="2" required></textarea><button type="submit" name="add_event" class="btn">Add Event</button></form></div><div class="card"><h2>📋 Events</h2><table><thead><tr><th>Title</th><th>Date</th><th>Location</th><th>Type</th><th>Actions</th></tr></thead><tbody><?php foreach($events as $e): ?><tr><td><?= htmlspecialchars($e['title']) ?></td><td><?= htmlspecialchars($e['event_date']) ?></td><td><?= htmlspecialchars($e['location']) ?></td><td><?= htmlspecialchars($e['type']) ?></td><td><a href="?edit_event=<?= $e['id'] ?>" class="btn btn-outline">Edit</a> <a href="?delete_event=<?= $e['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete?')">Del</a></td></tr><?php endforeach; ?></tbody></table></div></div>

  <!-- STATS TAB -->
  <div id="stats" class="tab-content"><div class="card"><h2>➕ Add Stat</h2><form method="POST"><div class="form-row"><input type="text" name="label" placeholder="Label" required><input type="number" name="value" placeholder="Value" required><input type="text" name="icon" placeholder="Icon Emoji" required><input type="number" name="display_order" placeholder="Order" value="0"></div><button type="submit" name="add_stat" class="btn">Add Stat</button></form></div><div class="card"><h2>📋 Stats</h2><table><thead><tr><th>Icon</th><th>Label</th><th>Value</th><th>Actions</th></tr></thead><tbody><?php foreach($stats as $s): ?><tr><td><?= htmlspecialchars($s['icon']) ?></td><td><?= htmlspecialchars($s['label']) ?></td><td><?= $s['value'] ?></td><td><a href="?edit_stat=<?= $s['id'] ?>" class="btn btn-outline">Edit</a> <a href="?delete_stat=<?= $s['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete?')">Del</a></td></tr><?php endforeach; ?></tbody></table></div></div>

  <!-- RESOURCES TAB -->
  <div id="resources" class="tab-content"><div class="card"><h2>➕ Add Resource</h2><form method="POST"><input type="text" name="title" placeholder="Title" required><textarea name="description" placeholder="Description" rows="2"></textarea><div class="form-row"><input type="text" name="link" placeholder="Link" value="#"><input type="text" name="icon" placeholder="Icon Emoji" value="📘"></div><button type="submit" name="add_resource" class="btn">Add Resource</button></form></div><div class="card"><h2>📋 Resources</h2><table><thead><tr><th>Icon</th><th>Title</th><th>Description</th><th>Actions</th></tr></thead><tbody><?php foreach($resources as $r): ?><tr><td><?= htmlspecialchars($r['icon']) ?></td><td><?= htmlspecialchars($r['title']) ?></td><td><?= htmlspecialchars($r['description']) ?></td><td><a href="?edit_resource=<?= $r['id'] ?>" class="btn btn-outline">Edit</a> <a href="?delete_resource=<?= $r['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete?')">Del</a></td></tr><?php endforeach; ?></tbody></table></div></div>

  <!-- CONTACT TAB -->
  <div id="contact" class="tab-content"><div class="card"><h2>✉️ Contact Info</h2><form method="POST"><input type="email" name="email" value="<?= htmlspecialchars($contact['email']) ?>" required><input type="text" name="social_message" value="<?= htmlspecialchars($contact['social_message']) ?>"><input type="text" name="phone_info" value="<?= htmlspecialchars($contact['phone_info']) ?>"><button type="submit" name="update_contact" class="btn">Update Contact</button></form></div></div>
</div>

<script>
function showTab(tabId) {
  document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
  document.getElementById(tabId).classList.add('active');
  document.querySelectorAll('.nav-tab').forEach(btn => btn.classList.remove('active'));
  event.target.classList.add('active');
}
</script>
</body>
</html>

<?php
if (isset($_GET['logout'])) { session_destroy(); header('Location: admin.php'); exit; }
?>