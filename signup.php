<?php
session_start();
require 'config.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $gender   = $_POST['gender'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (!$name || !$email || !$gender || !$password || !$confirm) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'An account with this email already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $insert = $conn->prepare("INSERT INTO users (name, email, gender, password) VALUES (?, ?, ?, ?)");
            $insert->bind_param("ssss", $name, $email, $gender, $hashed);

            if ($insert->execute()) {
                $success = 'Account created! You can now <a href="login.php">log in</a>.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign Up — UserHub</title>
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
      --success:     #00684a;
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
      width: 380px;
      background: linear-gradient(160deg, #001e2b 0%, #023430 60%, #00684a 100%);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 48px 40px;
      position: relative;
      overflow: hidden;
      flex-shrink: 0;
    }

    .left-panel::before {
      content: '';
      position: absolute;
      width: 300px; height: 300px; border-radius: 50%;
      background: rgba(0,237,100,0.06);
      top: -60px; right: -80px;
    }

    .left-panel::after {
      content: '';
      position: absolute;
      width: 180px; height: 180px; border-radius: 50%;
      background: rgba(0,237,100,0.05);
      bottom: 80px; left: -50px;
    }

    .panel-logo {
      display: flex; align-items: center; gap: 10px; z-index: 1;
    }

    .logo-icon {
      width: 38px; height: 38px;
      background: var(--green); border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; color: #001e2b;
    }

    .logo-text { font-size: 19px; font-weight: 700; color: var(--white); }
    .logo-text span { color: var(--green); }

    .panel-body { z-index: 1; }

    .panel-body h2 {
      font-size: 28px; font-weight: 900;
      color: var(--white); line-height: 1.25; margin-bottom: 14px;
    }

    .panel-body h2 span { color: var(--green); }

    .panel-body p {
      color: rgba(255,255,255,0.5); font-size: 13px; line-height: 1.7;
    }

    .panel-steps { z-index: 1; display: flex; flex-direction: column; gap: 14px; }

    .step-item {
      display: flex; align-items: flex-start; gap: 12px;
    }

    .step-num {
      width: 24px; height: 24px; border-radius: 50%;
      background: rgba(0,237,100,0.15);
      border: 1px solid rgba(0,237,100,0.3);
      color: var(--green); font-size: 11px; font-weight: 700;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0; margin-top: 1px;
    }

    .step-text { font-size: 13px; color: rgba(255,255,255,0.6); line-height: 1.5; }

    /* ── RIGHT PANEL ── */
    .right-panel {
      flex: 1;
      display: flex; align-items: center; justify-content: center;
      padding: 40px 24px;
      background: var(--white);
      overflow-y: auto;
    }

    .form-box { width: 100%; max-width: 420px; }

    .form-box .top-tag {
      display: inline-flex; align-items: center; gap: 6px;
      background: var(--green-light); color: var(--green-dark);
      font-size: 12px; font-weight: 600;
      padding: 5px 14px; border-radius: 20px;
      border: 1px solid rgba(0,163,92,0.2);
      margin-bottom: 20px;
    }

    .form-box h1 {
      font-size: 26px; font-weight: 900;
      color: var(--text); margin-bottom: 4px; letter-spacing: -0.5px;
    }

    .form-box .sub {
      color: var(--muted); font-size: 13px; margin-bottom: 28px;
    }

    .field { margin-bottom: 16px; }

    .field label {
      display: block; font-size: 13px; font-weight: 500;
      color: var(--text); margin-bottom: 6px;
    }

    .input-wrap { position: relative; }

    .input-wrap i {
      position: absolute; left: 14px; top: 50%;
      transform: translateY(-50%);
      color: var(--muted); font-size: 13px;
    }

    .input-wrap input {
      width: 100%;
      border: 1.5px solid var(--border); border-radius: 10px;
      padding: 12px 16px 12px 40px;
      font-family: 'Roboto', sans-serif;
      font-size: 14px; color: var(--text);
      background: var(--white); outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .input-wrap input:focus {
      border-color: var(--green-mid);
      box-shadow: 0 0 0 3px rgba(0,163,92,0.12);
    }

    /* Gender selector */
    .gender-group { display: flex; gap: 8px; }

    .gender-option { flex: 1; }

    .gender-option input[type="radio"] { display: none; }

    .gender-option label {
      display: flex; align-items: center; justify-content: center; gap: 6px;
      background: var(--off-white);
      border: 1.5px solid var(--border); border-radius: 10px;
      padding: 11px 8px; cursor: pointer;
      font-size: 13px; font-weight: 500;
      color: var(--muted); text-transform: none; letter-spacing: 0;
      transition: all 0.2s;
    }

    .gender-option input[type="radio"]:checked + label {
      border-color: var(--green-mid);
      color: var(--green-dark);
      background: var(--green-light);
    }

    .btn {
      width: 100%;
      background: var(--green); color: #001e2b;
      border: none; border-radius: 10px; padding: 14px;
      font-family: 'Roboto', sans-serif;
      font-weight: 700; font-size: 15px;
      cursor: pointer; margin-top: 6px;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      box-shadow: 0 4px 14px rgba(0,237,100,0.3);
      transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
    }

    .btn:hover  { background: #00c853; box-shadow: 0 6px 20px rgba(0,237,100,0.4); }
    .btn:active { transform: scale(0.98); }

    .msg {
      display: flex; align-items: flex-start; gap: 10px;
      padding: 12px 16px; border-radius: 10px;
      font-size: 13px; margin-bottom: 18px;
    }

    .msg.error   { background: #fff5f5; border: 1px solid #fed7d7; color: var(--error); }
    .msg.success { background: var(--green-light); border: 1px solid rgba(0,163,92,0.3); color: var(--success); }
    .msg.success a { color: var(--green-dark); font-weight: 600; }

    .footer-link {
      text-align: center; margin-top: 20px;
      font-size: 13px; color: var(--muted);
    }

    .footer-link a { color: var(--green-dark); font-weight: 600; text-decoration: none; }
    .footer-link a:hover { text-decoration: underline; }

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
    <h2>Join <span>UserHub</span></h2>
    <p>Create your free account in seconds and become part of our growing community.</p>
  </div>
  <div class="panel-steps">
    <div class="step-item">
      <div class="step-num">1</div>
      <div class="step-text">Fill in your name and email address</div>
    </div>
    <div class="step-item">
      <div class="step-num">2</div>
      <div class="step-text">Choose a secure password</div>
    </div>
    <div class="step-item">
      <div class="step-num">3</div>
      <div class="step-text">Done — access your dashboard instantly</div>
    </div>
  </div>
</div>

<div class="right-panel">
  <div class="form-box">
    <div class="top-tag"><i class="fas fa-user-plus"></i> Create Account</div>
    <h1>Get started</h1>
    <p class="sub">It's free and takes less than a minute</p>

    <?php if ($error):   ?>
      <div class="msg error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="msg success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="field">
        <label>Full Name</label>
        <div class="input-wrap">
          <i class="fas fa-user"></i>
          <input type="text" name="name" placeholder="John Doe"
                 value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required/>
        </div>
      </div>

      <div class="field">
        <label>Email Address</label>
        <div class="input-wrap">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" placeholder="john@example.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required/>
        </div>
      </div>

      <div class="field">
        <label>Gender</label>
        <div class="gender-group">
          <div class="gender-option">
            <input type="radio" name="gender" id="male" value="Male"
                   <?= (($_POST['gender'] ?? '') === 'Male') ? 'checked' : '' ?>>
            <label for="male"><i class="fas fa-mars"></i> Male</label>
          </div>
          <div class="gender-option">
            <input type="radio" name="gender" id="female" value="Female"
                   <?= (($_POST['gender'] ?? '') === 'Female') ? 'checked' : '' ?>>
            <label for="female"><i class="fas fa-venus"></i> Female</label>
          </div>
          <div class="gender-option">
            <input type="radio" name="gender" id="other" value="Other"
                   <?= (($_POST['gender'] ?? '') === 'Other') ? 'checked' : '' ?>>
            <label for="other"><i class="fas fa-genderless"></i> Other</label>
          </div>
        </div>
      </div>

      <div class="field">
        <label>Password</label>
        <div class="input-wrap">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" placeholder="Min. 6 characters" required/>
        </div>
      </div>

      <div class="field">
        <label>Confirm Password</label>
        <div class="input-wrap">
          <i class="fas fa-check-circle"></i>
          <input type="password" name="confirm" placeholder="Repeat your password" required/>
        </div>
      </div>

      <button type="submit" class="btn"><i class="fas fa-user-plus"></i> Create Account</button>
    </form>

    <div class="footer-link">Already have an account? <a href="login.php">Log in</a></div>
  </div>
</div>

</body>
</html>
