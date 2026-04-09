<?php
session_start();
require_once __DIR__ . '/../config/db.php';

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

// Helper function to redirect with tab parameter
function redirectWithTab($tab) {
    header("Location: admin.php?tab=" . urlencode($tab));
    exit;
}

// Get current tab from URL (default to 'teams')
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'teams';

// Image upload helper for events
function uploadEventImage($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) return null;
    $filename = time() . '_' . uniqid() . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        return 'uploads/' . $filename;
    }
    return null;
}

// Image upload helper for members (stores in separate folder)
function uploadMemberImage($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    $uploadDir = __DIR__ . '/uploads/members/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) return null;
    $filename = time() . '_' . uniqid() . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        return 'uploads/members/' . $filename;
    }
    return null;
}

// Handle POST actions (add/edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Teams
    if (isset($_POST['add_team'])) {
        $stmt = $pdo->prepare("INSERT INTO teams (name, description, icon) VALUES (?,?,?)");
        $stmt->execute([$_POST['name'], $_POST['description'], $_POST['icon']]);
        redirectWithTab('teams');
    } elseif (isset($_POST['edit_team'])) {
        $stmt = $pdo->prepare("UPDATE teams SET name=?, description=?, icon=? WHERE id=?");
        $stmt->execute([$_POST['name'], $_POST['description'], $_POST['icon'], $_POST['id']]);
        redirectWithTab('teams');
    }
    // Members (with image upload)
    elseif (isset($_POST['add_member'])) {
        $imagePath = null;
        if (isset($_FILES['member_image']) && $_FILES['member_image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = uploadMemberImage($_FILES['member_image']);
        }
        $stmt = $pdo->prepare("INSERT INTO members (name, role, bio, team_id, linkedin, instagram, image_url) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$_POST['name'], $_POST['role'], $_POST['bio'], $_POST['team_id'], $_POST['linkedin'], $_POST['instagram'], $imagePath]);
        redirectWithTab('members');
    } elseif (isset($_POST['edit_member'])) {
        $imagePath = null;
        if (isset($_FILES['member_image']) && $_FILES['member_image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = uploadMemberImage($_FILES['member_image']);
        }
        if ($imagePath) {
            $stmt = $pdo->prepare("UPDATE members SET name=?, role=?, bio=?, team_id=?, linkedin=?, instagram=?, image_url=? WHERE id=?");
            $stmt->execute([$_POST['name'], $_POST['role'], $_POST['bio'], $_POST['team_id'], $_POST['linkedin'], $_POST['instagram'], $imagePath, $_POST['id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE members SET name=?, role=?, bio=?, team_id=?, linkedin=?, instagram=? WHERE id=?");
            $stmt->execute([$_POST['name'], $_POST['role'], $_POST['bio'], $_POST['team_id'], $_POST['linkedin'], $_POST['instagram'], $_POST['id']]);
        }
        redirectWithTab('members');
    }
    // Events (with image upload)
    elseif (isset($_POST['add_event'])) {
        $imagePath = null;
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = uploadEventImage($_FILES['event_image']);
        }
        $stmt = $pdo->prepare("INSERT INTO events (title, event_date, location, type, description, register_link, icon_emoji, image_url) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$_POST['title'], $_POST['event_date'], $_POST['location'], $_POST['type'], $_POST['description'], $_POST['register_link'], $_POST['icon_emoji'], $imagePath]);
        redirectWithTab('events');
    } elseif (isset($_POST['edit_event'])) {
        $imagePath = null;
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = uploadEventImage($_FILES['event_image']);
        }
        if ($imagePath) {
            $stmt = $pdo->prepare("UPDATE events SET title=?, event_date=?, location=?, type=?, description=?, register_link=?, icon_emoji=?, image_url=? WHERE id=?");
            $stmt->execute([$_POST['title'], $_POST['event_date'], $_POST['location'], $_POST['type'], $_POST['description'], $_POST['register_link'], $_POST['icon_emoji'], $imagePath, $_POST['id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE events SET title=?, event_date=?, location=?, type=?, description=?, register_link=?, icon_emoji=? WHERE id=?");
            $stmt->execute([$_POST['title'], $_POST['event_date'], $_POST['location'], $_POST['type'], $_POST['description'], $_POST['register_link'], $_POST['icon_emoji'], $_POST['id']]);
        }
        redirectWithTab('events');
    }
    // Stats
    elseif (isset($_POST['add_stat'])) {
        $stmt = $pdo->prepare("INSERT INTO stats (label, value, icon, display_order) VALUES (?,?,?,?)");
        $stmt->execute([$_POST['label'], $_POST['value'], $_POST['icon'], $_POST['display_order']]);
        redirectWithTab('stats');
    } elseif (isset($_POST['edit_stat'])) {
        $stmt = $pdo->prepare("UPDATE stats SET label=?, value=?, icon=?, display_order=? WHERE id=?");
        $stmt->execute([$_POST['label'], $_POST['value'], $_POST['icon'], $_POST['display_order'], $_POST['id']]);
        redirectWithTab('stats');
    }
    // Resources
    elseif (isset($_POST['add_resource'])) {
        $stmt = $pdo->prepare("INSERT INTO resources (title, description, link, icon) VALUES (?,?,?,?)");
        $stmt->execute([$_POST['title'], $_POST['description'], $_POST['link'], $_POST['icon']]);
        redirectWithTab('resources');
    } elseif (isset($_POST['edit_resource'])) {
        $stmt = $pdo->prepare("UPDATE resources SET title=?, description=?, link=?, icon=? WHERE id=?");
        $stmt->execute([$_POST['title'], $_POST['description'], $_POST['link'], $_POST['icon'], $_POST['id']]);
        redirectWithTab('resources');
    }
    // Contact
    elseif (isset($_POST['update_contact'])) {
        $pdo->prepare("UPDATE contact SET email=?, social_message=?, phone_info=? WHERE id=1")->execute([$_POST['email'], $_POST['social_message'], $_POST['phone_info']]);
        redirectWithTab('contact');
    }
}

// Handle GET deletions – redirect with tab parameter as well
if (isset($_GET['delete_team'])) {
    $pdo->prepare("DELETE FROM teams WHERE id=?")->execute([$_GET['delete_team']]);
    redirectWithTab('teams');
}
if (isset($_GET['delete_member'])) {
    $pdo->prepare("DELETE FROM members WHERE id=?")->execute([$_GET['delete_member']]);
    redirectWithTab('members');
}
if (isset($_GET['delete_event'])) {
    $pdo->prepare("DELETE FROM events WHERE id=?")->execute([$_GET['delete_event']]);
    redirectWithTab('events');
}
if (isset($_GET['delete_stat'])) {
    $pdo->prepare("DELETE FROM stats WHERE id=?")->execute([$_GET['delete_stat']]);
    redirectWithTab('stats');
}
if (isset($_GET['delete_resource'])) {
    $pdo->prepare("DELETE FROM resources WHERE id=?")->execute([$_GET['delete_resource']]);
    redirectWithTab('resources');
}
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Fetch data for display
$teams = $pdo->query("SELECT * FROM teams")->fetchAll();
$members = $pdo->query("SELECT m.*, t.name as team_name FROM members m LEFT JOIN teams t ON m.team_id = t.id")->fetchAll();
$events = $pdo->query("SELECT * FROM events ORDER BY id DESC")->fetchAll();
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
    .form-row { display: flex; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap; }
    .logout { text-align: right; margin-bottom: 1rem; }
    .nav-tabs { display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; border-bottom: 1px solid #333; padding-bottom: 0.5rem; }
    .nav-tab { background: transparent; border: none; color: #9aa4bf; padding: 0.5rem 1rem; cursor: pointer; font-size: 1rem; }
    .nav-tab.active { color: #ff9900; border-bottom: 2px solid #ff9900; margin-bottom: -1px; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .modal { position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); backdrop-filter:blur(8px); display:flex; justify-content:center; align-items:center; z-index:1000; }
    .modal-content { background:#0f1117; border-radius:1rem; padding:2rem; width:90%; max-width:600px; border:1px solid #ff9900; max-height:80vh; overflow-y:auto; }
    .close-modal { float:right; cursor:pointer; font-size:1.5rem; color:#ff9900; }
  </style>
</head>
<body>
<div class="container">
  <div class="logout"><a href="?logout=1" class="btn btn-outline" onclick="return confirm('Logout?')">Logout</a></div>
  <h1>🗂️ AWS Club Admin Dashboard</h1>

  <div class="nav-tabs">
    <button class="nav-tab <?= $current_tab == 'teams' ? 'active' : '' ?>" data-tab="teams">Teams</button>
    <button class="nav-tab <?= $current_tab == 'members' ? 'active' : '' ?>" data-tab="members">Members</button>
    <button class="nav-tab <?= $current_tab == 'events' ? 'active' : '' ?>" data-tab="events">Events</button>
    <button class="nav-tab <?= $current_tab == 'stats' ? 'active' : '' ?>" data-tab="stats">Stats</button>
    <button class="nav-tab <?= $current_tab == 'resources' ? 'active' : '' ?>" data-tab="resources">Resources</button>
    <button class="nav-tab <?= $current_tab == 'contact' ? 'active' : '' ?>" data-tab="contact">Contact</button>
  </div>

  <!-- TEAMS TAB -->
  <div id="teams" class="tab-content <?= $current_tab == 'teams' ? 'active' : '' ?>">
    <div class="card"><h2>➕ Add Team</h2><form method="POST"><div class="form-row"><input type="text" name="name" placeholder="Team Name" required><input type="text" name="description" placeholder="Description"><input type="text" name="icon" placeholder="Icon (emoji)" value="🤝"></div><button type="submit" name="add_team" class="btn">Add Team</button></form></div>
    <div class="card"><h2>📋 Teams List</h2><table class="data-table"><thead><tr><th>Icon</th><th>Name</th><th>Description</th><th>Actions</th></tr></thead><tbody><?php foreach($teams as $t): ?><tr><td><?= htmlspecialchars($t['icon']) ?></td><td><?= htmlspecialchars($t['name']) ?></td><td><?= htmlspecialchars($t['description']) ?></td><td><button class="btn btn-outline" onclick="openEditModal('team', <?= htmlspecialchars(json_encode($t)) ?>)">Edit</button> <a href="?delete_team=<?= $t['id'] ?>&tab=teams" class="btn btn-danger" onclick="return confirm('Delete?')">Del</a></td></tr><?php endforeach; ?></tbody></table></div>
  </div>

  <!-- MEMBERS TAB -->
  <div id="members" class="tab-content <?= $current_tab == 'members' ? 'active' : '' ?>">
    <div class="card"><h2>➕ Add Member</h2>
      <form method="POST" enctype="multipart/form-data">
        <div class="form-row"><input type="text" name="name" placeholder="Name" required><input type="text" name="role" placeholder="Role" required></div>
        <textarea name="bio" placeholder="Bio"></textarea>
        <select name="team_id"><?php foreach($teams as $t): ?><option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option><?php endforeach; ?></select>
        <div class="form-row"><input type="text" name="linkedin" placeholder="LinkedIn URL"><input type="text" name="instagram" placeholder="Instagram URL"></div>
        <input type="file" name="member_image" accept="image/*">
        <button type="submit" name="add_member" class="btn">Add Member</button>
      </form>
    </div>
    <div class="card"><h2>📋 Members</h2><table class="data-table"><thead><tr><th>Name</th><th>Role</th><th>Team</th><th>Image</th><th>Actions</th></tr></thead><tbody><?php foreach($members as $m): ?><tr><td><?= htmlspecialchars($m['name']) ?></td><td><?= htmlspecialchars($m['role']) ?></td><td><?= htmlspecialchars($m['team_name']) ?></td><td><?php if($m['image_url']): ?><img src="<?= htmlspecialchars($m['image_url']) ?>" style="height:40px; width:40px; object-fit:cover; border-radius:50%;"><?php else: ?>No image<?php endif; ?></td><td><button class="btn btn-outline" onclick="openEditModal('member', <?= htmlspecialchars(json_encode($m)) ?>)">Edit</button> <a href="?delete_member=<?= $m['id'] ?>&tab=members" class="btn btn-danger" onclick="return confirm('Delete?')">Del</a></td></tr><?php endforeach; ?></tbody></table></div>
  </div>

  <!-- EVENTS TAB -->
  <div id="events" class="tab-content <?= $current_tab == 'events' ? 'active' : '' ?>">
    <div class="card"><h2>➕ Add Event</h2><form method="POST" enctype="multipart/form-data"><div class="form-row"><input type="text" name="title" placeholder="Title" required><input type="text" name="event_date" placeholder="Date (e.g., 24 Jan, 2025)" required><input type="text" name="location" placeholder="Location" required></div><div class="form-row"><select name="type"><option>Upcoming</option><option>Featured</option><option>AI/ML</option></select><input type="text" name="icon_emoji" placeholder="Icon Emoji" value="📅"><input type="text" name="register_link" placeholder="Register Link (URL)" value="#"></div><textarea name="description" placeholder="Description" rows="2" required></textarea><input type="file" name="event_image" accept="image/*"><button type="submit" name="add_event" class="btn">Add Event</button></form></div>
    <div class="card"><h2>📋 Events</h2><table class="data-table"><thead><tr><th>Title</th><th>Date</th><th>Location</th><th>Type</th><th>Image</th><th>Actions</th></tr></thead><tbody><?php foreach($events as $e): ?><tr><td><?= htmlspecialchars($e['title']) ?></td><td><?= htmlspecialchars($e['event_date']) ?></td><td><?= htmlspecialchars($e['location']) ?></td><td><?= htmlspecialchars($e['type']) ?></td><td><?php if($e['image_url']): ?><img src="<?= htmlspecialchars($e['image_url']) ?>" style="height:40px;"><?php else: ?>No image<?php endif; ?></td><td><button class="btn btn-outline" onclick="openEditModal('event', <?= htmlspecialchars(json_encode($e)) ?>)">Edit</button> <a href="?delete_event=<?= $e['id'] ?>&tab=events" class="btn btn-danger" onclick="return confirm('Delete?')">Del</a></td></tr><?php endforeach; ?></tbody></table></div>
  </div>

  <!-- STATS TAB -->
  <div id="stats" class="tab-content <?= $current_tab == 'stats' ? 'active' : '' ?>">
    <div class="card"><h2>➕ Add Stat</h2><form method="POST"><div class="form-row"><input type="text" name="label" placeholder="Label" required><input type="number" name="value" placeholder="Value" required><input type="text" name="icon" placeholder="Icon Emoji" required><input type="number" name="display_order" placeholder="Order" value="0"></div><button type="submit" name="add_stat" class="btn">Add Stat</button></form></div>
    <div class="card"><h2>📋 Stats</h2><table class="data-table"><thead><tr><th>Icon</th><th>Label</th><th>Value</th><th>Actions</th></tr></thead><tbody><?php foreach($stats as $s): ?><td><td><?= htmlspecialchars($s['icon']) ?></td><td><?= htmlspecialchars($s['label']) ?></td><td><?= $s['value'] ?></td><td><button class="btn btn-outline" onclick="openEditModal('stat', <?= htmlspecialchars(json_encode($s)) ?>)">Edit</button> <a href="?delete_stat=<?= $s['id'] ?>&tab=stats" class="btn btn-danger" onclick="return confirm('Delete?')">Del</a></td></tr><?php endforeach; ?></tbody></table></div>
  </div>

  <!-- RESOURCES TAB -->
  <div id="resources" class="tab-content <?= $current_tab == 'resources' ? 'active' : '' ?>">
    <div class="card"><h2>➕ Add Resource</h2><form method="POST"><input type="text" name="title" placeholder="Title" required><textarea name="description" placeholder="Description" rows="2"></textarea><div class="form-row"><input type="text" name="link" placeholder="Link" value="#"><input type="text" name="icon" placeholder="Icon Emoji" value="📘"></div><button type="submit" name="add_resource" class="btn">Add Resource</button></form></div>
    <div class="card"><h2>📋 Resources</h2><table class="data-table"><thead><tr><th>Icon</th><th>Title</th><th>Description</th><th>Actions</th></tr></thead><tbody><?php foreach($resources as $r): ?><tr><td><?= htmlspecialchars($r['icon']) ?></td><td><?= htmlspecialchars($r['title']) ?></td><td><?= htmlspecialchars($r['description']) ?></td><td><button class="btn btn-outline" onclick="openEditModal('resource', <?= htmlspecialchars(json_encode($r)) ?>)">Edit</button> <a href="?delete_resource=<?= $r['id'] ?>&tab=resources" class="btn btn-danger" onclick="return confirm('Delete?')">Del</a></td></tr><?php endforeach; ?></tbody></table></div>
  </div>

  <!-- CONTACT TAB -->
  <div id="contact" class="tab-content <?= $current_tab == 'contact' ? 'active' : '' ?>">
    <div class="card"><h2>✉️ Contact Info</h2><form method="POST"><input type="email" name="email" value="<?= htmlspecialchars($contact['email']) ?>" required><input type="text" name="social_message" value="<?= htmlspecialchars($contact['social_message']) ?>"><input type="text" name="phone_info" value="<?= htmlspecialchars($contact['phone_info']) ?>"><button type="submit" name="update_contact" class="btn">Update Contact</button></form></div>
  </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal" style="display:none;">
  <div class="modal-content">
    <span class="close-modal" onclick="closeModal()">&times;</span>
    <h2 id="modalTitle">Edit Item</h2>
    <form id="editForm" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" id="edit_id">
      <div id="editFields"></div>
      <button type="submit" name="edit_submit" class="btn" style="margin-top:1rem;">Save Changes</button>
    </form>
  </div>
</div>

<script>
// Tab persistence – update URL without reload when switching tabs
document.querySelectorAll('.nav-tab').forEach(btn => {
    btn.addEventListener('click', function() {
        const tab = this.getAttribute('data-tab');
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tab);
        window.history.pushState({}, '', url);
        document.querySelectorAll('.tab-content').forEach(tabDiv => tabDiv.classList.remove('active'));
        document.getElementById(tab).classList.add('active');
        document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
    });
});

function openEditModal(type, data) {
    document.getElementById('editModal').style.display = 'flex';
    document.getElementById('modalTitle').innerText = `Edit ${type.charAt(0).toUpperCase() + type.slice(1)}`;
    document.getElementById('edit_id').value = data.id;
    let fieldsHtml = '';
    
    if (type === 'team') {
        fieldsHtml = `
            <input type="text" name="name" value="${escapeHtml(data.name)}" placeholder="Team Name" required>
            <input type="text" name="description" value="${escapeHtml(data.description)}" placeholder="Description">
            <input type="text" name="icon" value="${escapeHtml(data.icon)}" placeholder="Icon (emoji)">
        `;
    } else if (type === 'member') {
        let teamOptions = '';
        <?php foreach($teams as $t): ?>
            teamOptions += `<option value="<?= $t['id'] ?>" ${data.team_id == <?= $t['id'] ?> ? 'selected' : ''}>${escapeHtml('<?= addslashes($t['name']) ?>')}</option>`;
        <?php endforeach; ?>
        fieldsHtml = `
            <input type="text" name="name" value="${escapeHtml(data.name)}" placeholder="Name" required>
            <input type="text" name="role" value="${escapeHtml(data.role)}" placeholder="Role" required>
            <textarea name="bio" placeholder="Bio">${escapeHtml(data.bio)}</textarea>
            <select name="team_id">${teamOptions}</select>
            <input type="text" name="linkedin" value="${escapeHtml(data.linkedin)}" placeholder="LinkedIn URL">
            <input type="text" name="instagram" value="${escapeHtml(data.instagram)}" placeholder="Instagram URL">
            <input type="file" name="member_image" accept="image/*">
            ${data.image_url ? `<p>Current image: <img src="${escapeHtml(data.image_url)}" style="height:50px; width:50px; object-fit:cover; border-radius:50%;"></p>` : ''}
        `;
    } else if (type === 'event') {
        fieldsHtml = `
            <input type="text" name="title" value="${escapeHtml(data.title)}" placeholder="Title" required>
            <input type="text" name="event_date" value="${escapeHtml(data.event_date)}" placeholder="Date (e.g., 24 Jan, 2025)" required>
            <input type="text" name="location" value="${escapeHtml(data.location)}" placeholder="Location" required>
            <select name="type">
                <option ${data.type === 'Upcoming' ? 'selected' : ''}>Upcoming</option>
                <option ${data.type === 'Featured' ? 'selected' : ''}>Featured</option>
                <option ${data.type === 'AI/ML' ? 'selected' : ''}>AI/ML</option>
            </select>
            <input type="text" name="icon_emoji" value="${escapeHtml(data.icon_emoji)}" placeholder="Icon Emoji">
            <input type="text" name="register_link" value="${escapeHtml(data.register_link)}" placeholder="Register Link (URL)">
            <textarea name="description" placeholder="Description" rows="2">${escapeHtml(data.description)}</textarea>
            <input type="file" name="event_image" accept="image/*">
            ${data.image_url ? `<p>Current image: <img src="${escapeHtml(data.image_url)}" style="height:50px;"></p>` : ''}
        `;
    } else if (type === 'stat') {
        fieldsHtml = `
            <input type="text" name="label" value="${escapeHtml(data.label)}" placeholder="Label" required>
            <input type="number" name="value" value="${data.value}" placeholder="Value" required>
            <input type="text" name="icon" value="${escapeHtml(data.icon)}" placeholder="Icon Emoji" required>
            <input type="number" name="display_order" value="${data.display_order}" placeholder="Order">
        `;
    } else if (type === 'resource') {
        fieldsHtml = `
            <input type="text" name="title" value="${escapeHtml(data.title)}" placeholder="Title" required>
            <textarea name="description" placeholder="Description">${escapeHtml(data.description)}</textarea>
            <input type="text" name="link" value="${escapeHtml(data.link)}" placeholder="Link">
            <input type="text" name="icon" value="${escapeHtml(data.icon)}" placeholder="Icon Emoji">
        `;
    }
    
    document.getElementById('editFields').innerHTML = fieldsHtml;
    const form = document.getElementById('editForm');
    // Remove any existing hidden input for this action
    const oldInput = form.querySelector(`input[name="edit_${type}"]`);
    if (oldInput) oldInput.remove();
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = `edit_${type}`;
    actionInput.value = '1';
    form.appendChild(actionInput);
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}
</script>
</body>
</html>