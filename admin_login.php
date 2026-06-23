<?php
session_start();
require 'config.php';

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Please fill in all fields.';
    } elseif ($email !== ADMIN_EMAIL) {
        $error = 'Access denied. Unauthorized email.';
    } elseif ($password !== ADMIN_PASSWORD) {
        $error = 'Incorrect password.';
    } else {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email']     = $email;
        header('Location: admin_dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login — UserHub</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <style>
    :root {
      --green:       #00ed64;
      --green-dark:  #00684a;
      --green-light: #e8fdf5;
      --green-mid:   #00a35c;
      --white:       #ffffff;
      --off-white:   #f9fafb;
      --border:      #e2e8f0;
      --text:        #1a202c;
      --muted:       #718096;
      --error:       #e53e3e;
      --shadow:      0 4px 24px rgba(0,0,0,0.08);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      background: var(--off-white);
      min-height: 100vh;
      display: flex;
      font-family: 'Roboto', sans-serif;
      color: var(--text);
    }

    /* ── LEFT PANEL ── */
    .left-panel {
      width: 420px;
      background: linear-gradient(160deg, #001e2b 0%, #023430 60%, #00684a 100%);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 48px 44px;
      position: relative;
      overflow: hidden;
      flex-shrink: 0;
    }

    .left-panel::before {
      content: '';
      position: absolute;
      width: 320px;
      height: 320px;
      border-radius: 50%;
      background: rgba(0, 237, 100, 0.06);
      top: -80px;
      right: -80px;
    }

    .left-panel::after {
      content: '';
      position: absolute;
      width: 200px;
      height: 200px;
      border-radius: 50%;
      background: rgba(0, 237, 100, 0.05);
      bottom: 60px;
      left: -60px;
    }

    .panel-logo {
      display: flex;
      align-items: center;
      gap: 10px;
      z-index: 1;
    }

    .panel-logo .logo-icon {
      width: 40px;
      height: 40px;
      background: var(--green);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      color: #001e2b;
      font-weight: 900;
    }

    .panel-logo .logo-text {
      font-size: 20px;
      font-weight: 700;
      color: var(--white);
      letter-spacing: -0.3px;
    }

    .panel-logo .logo-text span {
      color: var(--green);
    }

    .panel-body {
      z-index: 1;
    }

    .panel-body h2 {
      font-size: 32px;
      font-weight: 900;
      color: var(--white);
      line-height: 1.2;
      margin-bottom: 16px;
    }

    .panel-body h2 span { color: var(--green); }

    .panel-body p {
      color: rgba(255,255,255,0.55);
      font-size: 14px;
      line-height: 1.7;
    }

    .panel-features {
      z-index: 1;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .feature-item {
      display: flex;
      align-items: center;
      gap: 12px;
      color: rgba(255,255,255,0.7);
      font-size: 13px;
    }

    .feature-item i {
      color: var(--green);
      width: 16px;
    }

    /* ── RIGHT PANEL ── */
    .right-panel {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 48px 24px;
    }

    .login-box {
      width: 100%;
      max-width: 420px;
    }

    .login-box .top-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--green-light);
      color: var(--green-dark);
      font-size: 12px;
      font-weight: 600;
      padding: 6px 14px;
      border-radius: 20px;
      margin-bottom: 24px;
      border: 1px solid rgba(0,163,92,0.2);
    }

    .login-box h1 {
      font-size: 28px;
      font-weight: 900;
      color: var(--text);
      margin-bottom: 6px;
      letter-spacing: -0.5px;
    }

    .login-box .sub {
      color: var(--muted);
      font-size: 14px;
      margin-bottom: 36px;
    }

    .field { margin-bottom: 20px; }

    .field label {
      display: block;
      font-size: 13px;
      font-weight: 500;
      color: var(--text);
      margin-bottom: 8px;
    }

    .input-wrap {
      position: relative;
    }

    .input-wrap i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--muted);
      font-size: 14px;
    }

    .input-wrap input {
      width: 100%;
      border: 1.5px solid var(--border);
      border-radius: 10px;
      padding: 13px 16px 13px 42px;
      font-family: 'Roboto', sans-serif;
      font-size: 14px;
      color: var(--text);
      background: var(--white);
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .input-wrap input:focus {
      border-color: var(--green-mid);
      box-shadow: 0 0 0 3px rgba(0,163,92,0.12);
    }

    .btn-login {
      width: 100%;
      background: var(--green);
      color: #001e2b;
      border: none;
      border-radius: 10px;
      padding: 14px;
      font-family: 'Roboto', sans-serif;
      font-weight: 700;
      font-size: 15px;
      cursor: pointer;
      margin-top: 8px;
      transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      box-shadow: 0 4px 14px rgba(0,237,100,0.3);
    }

    .btn-login:hover  { background: #00c853; box-shadow: 0 6px 20px rgba(0,237,100,0.4); }
    .btn-login:active { transform: scale(0.98); }

    .msg-error {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 16px;
      border-radius: 10px;
      font-size: 13px;
      margin-bottom: 20px;
      background: #fff5f5;
      border: 1px solid #fed7d7;
      color: var(--error);
    }

    .divider {
      text-align: center;
      margin: 28px 0 20px;
      position: relative;
      color: var(--muted);
      font-size: 12px;
    }

    .divider::before, .divider::after {
      content: '';
      position: absolute;
      top: 50%;
      width: 42%;
      height: 1px;
      background: var(--border);
    }

    .divider::before { left: 0; }
    .divider::after  { right: 0; }

    .back-link {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      color: var(--muted);
      font-size: 13px;
      text-decoration: none;
      transition: color 0.2s;
    }

    .back-link:hover { color: var(--green-dark); }

    @media (max-width: 768px) {
      .left-panel { display: none; }
    }
  </style>
</head>
<body>

<!-- LEFT PANEL -->
<div class="left-panel">
  <div class="panel-logo">
    <div class="logo-icon"><i class="fas fa-leaf"></i></div>
    <div class="logo-text">User<span>Hub</span></div>
  </div>

  <div class="panel-body">
    <h2>Admin <span>Control</span> Center</h2>
    <p>Manage your platform, monitor user activity, and gain full visibility into your application data.</p>
  </div>

  <div class="panel-features">
    <div class="feature-item"><i class="fas fa-check-circle"></i> Real-time user analytics</div>
    <div class="feature-item"><i class="fas fa-check-circle"></i> Login activity tracking</div>
    <div class="feature-item"><i class="fas fa-check-circle"></i> Member management</div>
    <div class="feature-item"><i class="fas fa-check-circle"></i> Secure admin-only access</div>
  </div>
</div>

<!-- RIGHT PANEL -->
<div class="right-panel">
  <div class="login-box">
    <div class="top-badge"><i class="fas fa-shield-alt"></i> Admin Access Only</div>
    <h1>Welcome back</h1>
    <p class="sub">Sign in to your admin dashboard</p>

    <?php if ($error): ?>
      <div class="msg-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="field">
        <label>Admin Email</label>
        <div class="input-wrap">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" placeholder="admin@example.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required/>
        </div>
      </div>

      <div class="field">
        <label>Password</label>
        <div class="input-wrap">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" placeholder="Enter admin password" required/>
        </div>
      </div>

      <button type="submit" class="btn-login">
        <i class="fas fa-sign-in-alt"></i> Sign In to Dashboard
      </button>
    </form>

    <div class="divider">or</div>
    <a href="login.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to user login</a>
  </div>
</div>

</body>
</html>
