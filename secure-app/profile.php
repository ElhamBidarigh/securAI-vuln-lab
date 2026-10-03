<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$id = (int)$_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, username, email, role, bio FROM users WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) { http_response_code(404); exit('Not found'); }
?>
<!DOCTYPE html>
<html><head><title>My profile</title>
<style>body{font-family:system-ui;max-width:520px;margin:60px auto;background:#0b0f19;color:#e6edf7;padding:20px}
.field{background:#111827;padding:14px;border-radius:8px;margin-bottom:10px}
.field b{color:#10b981;display:inline-block;width:100px}</style></head>
<body>
  <h1>My Profile</h1>
  <div class="field"><b>Username:</b> <?= htmlspecialchars($user['username'], ENT_QUOTES) ?></div>
  <div class="field"><b>Email:</b> <?= htmlspecialchars($user['email'], ENT_QUOTES) ?></div>
  <div class="field"><b>Role:</b> <?= htmlspecialchars($user['role'], ENT_QUOTES) ?></div>
  <div class="field"><b>Bio:</b> <?= htmlspecialchars($user['bio'] ?? '', ENT_QUOTES) ?></div>
  <p style="color:#8b9bb4;font-size:13px;margin-top:20px">
    Note: <code>?id=</code> in the URL is ignored. You can only see your own profile.
  </p>
</body></html>
