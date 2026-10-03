<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$id = $_GET['id'] ?? $_SESSION['user_id'];
$res = $conn->query("SELECT id, username, email, role, bio FROM users WHERE id = $id");
$user = $res->fetch_assoc();
if (!$user) { die("User not found"); }
?>
<!DOCTYPE html>
<html><head><title>Profile</title>
<style>body{font-family:system-ui;max-width:520px;margin:60px auto;background:#0b0f19;color:#e6edf7;padding:20px}
.field{background:#111827;padding:14px;border-radius:8px;margin-bottom:10px}
.field b{color:#10b981;display:inline-block;width:100px}</style></head>
<body>
  <h1>Profile of <?= $user['username'] ?></h1>
  <div class="field"><b>ID:</b> <?= $user['id'] ?></div>
  <div class="field"><b>Username:</b> <?= $user['username'] ?></div>
  <div class="field"><b>Email:</b> <?= $user['email'] ?></div>
  <div class="field"><b>Role:</b> <?= $user['role'] ?></div>
  <div class="field"><b>Bio:</b> <?= $user['bio'] ?></div>
  <p style="color:#8b9bb4;font-size:13px">Try changing <code>?id=1</code> to <code>?id=2</code>.</p>
</body></html>
