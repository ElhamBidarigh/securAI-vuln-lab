<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
?>
<!DOCTYPE html>
<html><head><title>Dashboard</title>
<style>body{font-family:system-ui;max-width:520px;margin:60px auto;background:#0b0f19;color:#e6edf7;padding:20px}
a{color:#10b981;margin-right:14px}</style></head>
<body>
  <h1>Hi, <?= $_SESSION['username'] ?> 👋</h1>
  <nav>
    <a href="profile.php?id=<?= $_SESSION['user_id'] ?>">My profile</a>
    <a href="comment.php">Comments</a>
    <a href="upload.php">Upload</a>
    <a href="index.php">Home</a>
  </nav>
  <p style="color:#8b9bb4;font-size:13px;margin-top:30px">
    You are logged in as <b><?= $_SESSION['role'] ?></b>.
  </p>
</body></html>
