<?php
session_start();
require 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($id, $name, $hashed);
        $stmt->fetch();
        $stmt->close();

        if ($id && password_verify($password, $hashed)) {
            $upd = $conn->prepare("UPDATE users SET last_login = NOW(), login_count = login_count + 1 WHERE id = ?");
            $upd->bind_param("i", $id);
            $upd->execute();
            $upd->close();

            $_SESSION['user_id']   = $id;
            $_SESSION['user_name'] = $name;
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Incorrect email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Log In — UserHub</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <style>
    :root {
      --green:       #00ed64;
      --green-dark:  #00684a;
      --green-mid:   #00a35c;
      --green-light: #e8fdf5;
      --white:       #ffffff;
      --off-white:   #f7f9fc;
      --border:      #e2e8f0;
      --text:        #1a202c;
      --muted:       #718096;
      --error:       #e53e3e;
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
      width: 320px; height: 320px;
      border-radius: 50%;
      background: rgba(0,237,100,0.06);
      top: -80px; right: -80px;
    }

    .left-panel::after {
      content: '';
      position: absolute;
      width: 200px; height: 200px;
      border-radius: 50%;
      background: rgba(0,237,100,0.05);
      bottom: 60px; left: -60px;
    }

    .panel-logo {
      display: flex; align-items: center; gap: 10px; z-index: 1;
    }

    .logo-icon {
      width: 40px; height: 40px;
      background: var(--green);
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; color: #001e2b;
    }

    .logo-text { font-size: 20px; font-weight: 700; color: var(--white); }
    .logo-text span { color: var(--green); }

    .panel-body { z-index: 1; }

    .panel-body h2 {
      font-size: 30px; font-weight: 900;
      color: var(--white); line-height: 1.25; margin-bottom: 14px;
    }

    .panel-body h2 span { color: var(--green); }

    .panel-body p {
      color: rgba(255,255,255,0.5);
      font-size: 14px; line-height: 1.7;
    }

    .panel-features { z-index: 1; display: flex; flex-direction: column; gap: 12px; }

    .feature-item {
      display: flex; align-items: center; gap: 10px;
      color: rgba(255,255,255,0.65); font-size: 13px;
    }

    .feature-item i { color: var(--green); width: 16px; }

    /* ── RIGHT PANEL ── */
    .right-panel {
      flex: 1;
      display: flex; align-items: center; justify-content: center;
      padding: 48px 24px;
      background: var(--white);
    }

    .form-box { width: 100%; max-width: 400px; }

    .form-box .top-tag {
      display: inline-flex; align-items: center; gap: 6px;
      background: var(--green-light);
      color: var(--green-dark);
      font-size: 12px; font-weight: 600;
      padding: 5px 14px; border-radius: 20px;
      border: 1px solid rgba(0,163,92,0.2);
      margin-bottom: 22px;
    }

    .form-box h1 {
      font-size: 28px; font-weight: 900;
      color: var(--text); margin-bottom: 6px; letter-spacing: -0.5px;
    }

    .form-box .sub {
      color: var(--muted); font-size: 14px; margin-bottom: 32px;
    }

    .field { margin-bottom: 18px; }

    .field label {
      display: block; font-size: 13px; font-weight: 500;
      color: var(--text); margin-bottom: 7px;
    }

    .input-wrap { position: relative; }

    .input-wrap i {
      position: absolute; left: 14px; top: 50%;
      transform: translateY(-50%);
      color: var(--muted); font-size: 14px;
    }

    .input-wrap input {
      width: 100%;
      border: 1.5px solid var(--border);
      border-radius: 10px;
      padding: 13px 16px 13px 42px;
      font-family: 'Roboto', sans-serif;
      font-size: 14px; color: var(--text);
      background: var(--white); outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .input-wrap input:focus {
      border-color: var(--green-mid);
      box-shadow: 0 0 0 3px rgba(0,163,92,0.12);
    }

    .btn {
      width: 100%;
      background: var(--green);
      color: #001e2b;
      border: none; border-radius: 10px;
      padding: 14px;
      font-family: 'Roboto', sans-serif;
      font-weight: 700; font-size: 15px;
      cursor: pointer; margin-top: 6px;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      box-shadow: 0 4px 14px rgba(0,237,100,0.3);
      transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
    }

    .btn:hover  { background: #00c853; box-shadow: 0 6px 20px rgba(0,237,100,0.4); }
    .btn:active { transform: scale(0.98); }

    .msg-error {
      display: flex; align-items: center; gap: 10px;
      padding: 12px 16px; border-radius: 10px;
      font-size: 13px; margin-bottom: 18px;
      background: #fff5f5; border: 1px solid #fed7d7; color: var(--error);
    }

    .footer-link {
      text-align: center; margin-top: 22px;
      font-size: 13px; color: var(--muted);
    }

    .footer-link a { color: var(--green-dark); font-weight: 600; text-decoration: none; }
    .footer-link a:hover { text-decoration: underline; }

    .admin-link {
      text-align: center; margin-top: 12px;
    }

    .admin-link a {
      font-size: 12px; color: #b0bec5; text-decoration: none;
      display: inline-flex; align-items: center; gap: 5px;
    }

    .admin-link a:hover { color: var(--muted); }

    @media (max-width: 768px) { .left-panel { display: none; } }
  </style>
</head>
<body>

<div class="left-panel">
  <div class="panel-logo">
    <div class="logo-icon"><i class="fas fa-leaf"></i></div>
    <div class="logo-text">User<span>Hub</span></div>
  </div>
  <div class="panel-body">
    <h2>Welcome <span>Back</span></h2>
    <p>Sign in to access your account and connect with the UserHub community.</p>
  </div>
  <div class="panel-features">
    <div class="feature-item"><i class="fas fa-check-circle"></i> Secure authentication</div>
    <div class="feature-item"><i class="fas fa-check-circle"></i> Personal dashboard</div>
    <div class="feature-item"><i class="fas fa-check-circle"></i> Community access</div>
  </div>
</div>

<div class="right-panel">
  <div class="form-box">
    <div class="top-tag"><i class="fas fa-user-circle"></i> Member Login</div>
    <h1>Sign in</h1>
    <p class="sub">Enter your credentials to continue</p>

    <?php if ($error): ?>
      <div class="msg-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="field">
        <label>Email Address</label>
        <div class="input-wrap">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" placeholder="john@example.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required/>
        </div>
      </div>

      <div class="field">
        <label>Password</label>
        <div class="input-wrap">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" placeholder="Your password" required/>
        </div>
      </div>

      <button type="submit" class="btn"><i class="fas fa-sign-in-alt"></i> Log In</button>
    </form>

    <div class="footer-link">Don't have an account? <a href="signup.php">Sign up</a></div>
    <div class="admin-link"><a href="admin_login.php"><i class="fas fa-shield-alt"></i> Admin login</a></div>
  </div>
</div>

</body>
</html>
