<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $target = __DIR__ . '/uploads/' . basename($_FILES['file']['name']);
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
        $msg = "Uploaded to <a href='uploads/" . basename($_FILES['file']['name']) . "'>uploads/" . basename($_FILES['file']['name']) . "</a>";
    } else { $msg = "Upload failed."; }
}
?>
<!DOCTYPE html>
<html><head><title>Upload</title>
<style>body{font-family:system-ui;max-width:520px;margin:60px auto;background:#0b0f19;color:#e6edf7;padding:20px}
input,button{margin-top:14px}button{background:#10b981;color:#04121a;padding:10px 20px;border:0;border-radius:6px;font-weight:700;cursor:pointer}
.msg{background:#111827;padding:10px;border-radius:6px;margin:14px 0}</style></head>
<body>
  <h1>Upload a file</h1>
  <?php if ($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>
  <form method="POST" enctype="multipart/form-data">
    <input type="file" name="file" required><button>Upload</button>
  </form>
  <p style="color:#8b9bb4;font-size:13px">Try uploading a <code>.php</code> file — it will execute.</p>
</body></html>
