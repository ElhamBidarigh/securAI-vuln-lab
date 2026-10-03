<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$msg = ''; $err = '';
$UPLOAD_DIR = '/var/uploads/';
if (!is_dir($UPLOAD_DIR)) { @mkdir($UPLOAD_DIR, 0700, true); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) { $err = 'Upload failed.'; }
    else {
        $f = $_FILES['file'];
        $allowed = ['image/png'=>'png','image/jpeg'=>'jpg','application/pdf'=>'pdf','text/plain'=>'txt'];
        if ($f['size'] > 5 * 1024 * 1024) { $err = 'File too large (max 5 MB).'; }
        else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($f['tmp_name']);
            if (!isset($allowed[$mime])) { $err = 'File type not allowed.'; }
            else {
                $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
                if (move_uploaded_file($f['tmp_name'], $UPLOAD_DIR . $name)) {
                    $msg = 'Uploaded as ' . htmlspecialchars($name, ENT_QUOTES);
                } else { $err = 'Could not save file.'; }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html><head><title>Upload</title>
<style>body{font-family:system-ui;max-width:520px;margin:60px auto;background:#0b0f19;color:#e6edf7;padding:20px}
input,button{margin-top:14px}button{background:#10b981;color:#04121a;padding:10px 20px;border:0;border-radius:6px;font-weight:700;cursor:pointer}
.msg{background:#111827;padding:10px;border-radius:6px;margin:14px 0;color:#34d399}
.err{background:#111827;padding:10px;border-radius:6px;margin:14px 0;color:#f87171}</style></head>
<body>
  <h1>Upload a file — Secure</h1>
  <?php if ($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>
  <?php if ($err): ?><div class="err"><?= htmlspecialchars($err, ENT_QUOTES) ?></div><?php endif; ?>
  <form method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="file" name="file" accept=".png,.jpg,.jpeg,.pdf,.txt" required>
    <button>Upload</button>
  </form>
  <p style="color:#8b9bb4;font-size:13px">Only PNG, JPG, PDF, TXT (max 5 MB).</p>
</body></html>
