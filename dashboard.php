<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$logged_in_name = htmlspecialchars($_SESSION['user_name']);

$result = $conn->query("SELECT name, email, gender, created_at FROM users ORDER BY created_at DESC");
$users  = $result->fetch_all(MYSQLI_ASSOC);
$total  = count($users);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard — UserHub</title>
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
      --border:      #e8edf2;
      --text:        #1a202c;
      --muted:       #718096;
      --sidebar-bg:  #001e2b;
      --sidebar-w:   240px;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Roboto', sans-serif;
      background: var(--off-white);
      color: var(--text);
      display: flex; min-height: 100vh;
    }

    /* ── SIDEBAR ── */
    .sidebar {
      width: var(--sidebar-w);
      background: var(--sidebar-bg);
      min-height: 100vh;
      display: flex; flex-direction: column;
      position: fixed; top: 0; left: 0;
      z-index: 100;
    }

    .sidebar-logo {
      padding: 26px 22px 18px;
      display: flex; align-items: center; gap: 10px;
      border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .logo-icon {
      width: 36px; height: 36px;
      background: var(--green); border-radius: 9px;
      display: flex; align-items: center; justify-content: center;
      font-size: 15px; color: #001e2b;
    }

    .logo-text { font-size: 17px; font-weight: 700; color: #fff; }
    .logo-text span { color: var(--green); }

    .sidebar-section {
      padding: 18px 16px 6px;
      font-size: 10px; font-weight: 700;
      letter-spacing: 1.5px; text-transform: uppercase;
      color: rgba(255,255,255,0.22);
    }

    .nav-item {
      display: flex; align-items: center; gap: 11px;
      padding: 10px 18px; margin: 2px 10px;
      border-radius: 9px;
      color: rgba(255,255,255,0.5);
      font-size: 13px; font-weight: 500;
      text-decoration: none;
      transition: all 0.2s;
    }

    .nav-item i { width: 16px; font-size: 13px; }

    .nav-item:hover { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.85); }

    .nav-item.active {
      background: rgba(0,237,100,0.12);
      color: var(--green);
      border: 1px solid rgba(0,237,100,0.15);
    }

    .sidebar-footer {
      margin-top: auto;
      padding: 16px 14px;
      border-top: 1px solid rgba(255,255,255,0.06);
    }

    .user-profile {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 10px; border-radius: 9px;
      background: rgba(255,255,255,0.04);
      margin-bottom: 8px;
    }

    .user-avatar {
      width: 34px; height: 34px; border-radius: 50%;
      background: linear-gradient(135deg, var(--green), var(--green-mid));
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 13px; color: #001e2b;
      flex-shrink: 0;
    }

    .user-info-name { font-size: 12px; font-weight: 600; color: #fff; }
    .user-info-role { font-size: 11px; color: rgba(255,255,255,0.35); }

    .logout-link {
      display: flex; align-items: center; gap: 9px;
      padding: 9px 10px; border-radius: 9px;
      color: rgba(255,255,255,0.35); font-size: 13px;
      text-decoration: none; transition: all 0.2s;
    }

    .logout-link:hover { background: rgba(229,62,62,0.1); color: #fc8181; }

    /* ── MAIN ── */
    .main-content { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; }

    .topnav {
      height: 62px;
      background: var(--white);
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 30px;
      position: sticky; top: 0; z-index: 50;
      box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }

    .topnav-left h2 { font-size: 17px; font-weight: 700; color: var(--text); }
    .topnav-left p  { font-size: 12px; color: var(--muted); margin-top: 1px; }

    .topnav-badge {
      display: flex; align-items: center; gap: 6px;
      background: var(--green-light); color: var(--green-dark);
      font-size: 12px; font-weight: 600;
      padding: 6px 14px; border-radius: 20px;
      border: 1px solid rgba(0,163,92,0.2);
    }

    .page-body { padding: 28px 30px; flex: 1; }

    /* ── STATS ── */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 18px; margin-bottom: 26px;
    }

    .stat-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 14px; padding: 22px 22px;
      display: flex; align-items: center; gap: 16px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.05);
      transition: box-shadow 0.2s, transform 0.2s;
    }

    .stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); transform: translateY(-2px); }

    .stat-card.primary {
      background: linear-gradient(135deg, #001e2b, #023430);
      border-color: rgba(0,237,100,0.2);
      box-shadow: 0 4px 16px rgba(0,237,100,0.15);
    }

    .stat-icon {
      width: 48px; height: 48px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; flex-shrink: 0;
    }

    .stat-icon.g  { background: var(--green-light); color: var(--green-mid); }
    .stat-icon.dk { background: rgba(0,237,100,0.15); color: var(--green); }
    .stat-icon.b  { background: #ebf8ff; color: #3182ce; }
    .stat-icon.p  { background: #fff5f7; color: #d53f8c; }
    .stat-icon.v  { background: #faf5ff; color: #805ad5; }

    .stat-label {
      font-size: 11px; font-weight: 600;
      text-transform: uppercase; letter-spacing: 0.8px;
      color: var(--muted); margin-bottom: 4px;
    }

    .stat-card.primary .stat-label { color: rgba(255,255,255,0.45); }

    .stat-value {
      font-size: 32px; font-weight: 900;
      color: var(--text); line-height: 1;
    }

    .stat-card.primary .stat-value { color: var(--green); }

    /* ── TABLE ── */
    .table-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 16px; overflow: hidden;
      box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }

    .table-card-header {
      padding: 18px 24px;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
    }

    .table-card-header h3 {
      font-size: 15px; font-weight: 700; color: var(--text);
      display: flex; align-items: center; gap: 8px;
    }

    .table-card-header h3 i { color: var(--green-mid); }

    .count-badge {
      background: var(--green-light); color: var(--green-dark);
      font-size: 12px; font-weight: 600;
      padding: 4px 12px; border-radius: 20px;
      border: 1px solid rgba(0,163,92,0.2);
    }

    table { width: 100%; border-collapse: collapse; }

    thead th {
      text-align: left; padding: 12px 22px;
      font-size: 11px; font-weight: 700;
      text-transform: uppercase; letter-spacing: 1px;
      color: var(--muted); background: #fafbfc;
      border-bottom: 1px solid var(--border);
    }

    tbody tr {
      border-bottom: 1px solid var(--border);
      transition: background 0.15s;
    }

    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #f7fdf9; }

    tbody td { padding: 14px 22px; font-size: 13px; vertical-align: middle; }

    .user-cell { display: flex; align-items: center; gap: 10px; }

    .avatar {
      width: 34px; height: 34px; border-radius: 50%;
      background: linear-gradient(135deg, var(--green), var(--green-mid));
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 13px; color: #001e2b;
      flex-shrink: 0;
    }

    .user-name  { font-weight: 600; color: var(--text); }
    .user-email { font-size: 11px; color: var(--muted); }

    .gender-badge {
      display: inline-block; padding: 3px 10px;
      border-radius: 20px; font-size: 11px; font-weight: 600;
    }

    .gender-badge.male   { background: #ebf8ff; color: #2b6cb0; }
    .gender-badge.female { background: #fff5f7; color: #97266d; }
    .gender-badge.other  { background: #faf5ff; color: #553c9a; }

    .date-text { color: var(--muted); font-size: 12px; }

    .empty-state {
      text-align: center; padding: 56px;
      color: var(--muted); font-size: 14px;
    }

    .empty-state i { font-size: 36px; color: #cbd5e0; display: block; margin-bottom: 12px; }

    @media (max-width: 1024px) { .stats-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 768px)  { .sidebar { display: none; } .main-content { margin-left: 0; } }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon"><i class="fas fa-leaf"></i></div>
    <div class="logo-text">User<span>Hub</span></div>
  </div>

  <div class="sidebar-section">Menu</div>
  <a href="dashboard.php" class="nav-item active"><i class="fas fa-th-large"></i> Dashboard</a>

  <div class="sidebar-footer">
    <div class="user-profile">
      <div class="user-avatar"><?= strtoupper(mb_substr($_SESSION['user_name'], 0, 1)) ?></div>
      <div>
        <div class="user-info-name"><?= $logged_in_name ?></div>
        <div class="user-info-role">Member</div>
      </div>
    </div>
    <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Log out</a>
  </div>
</aside>

<!-- MAIN -->
<div class="main-content">
  <div class="topnav">
    <div class="topnav-left">
      <h2>Member Dashboard</h2>
      <p>Welcome back, <?= $logged_in_name ?></p>
    </div>
    <div class="topnav-badge"><i class="fas fa-circle" style="font-size:8px;"></i> <?= date('M j, Y') ?></div>
  </div>

  <div class="page-body">

    <!-- STATS -->
    <div class="stats-grid">
      <div class="stat-card primary">
        <div class="stat-icon dk"><i class="fas fa-users"></i></div>
        <div>
          <div class="stat-label">Total Members</div>
          <div class="stat-value"><?= $total ?></div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon b"><i class="fas fa-mars"></i></div>
        <div>
          <div class="stat-label">Male</div>
          <div class="stat-value"><?= count(array_filter($users, fn($u) => $u['gender'] === 'Male')) ?></div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon p"><i class="fas fa-venus"></i></div>
        <div>
          <div class="stat-label">Female</div>
          <div class="stat-value"><?= count(array_filter($users, fn($u) => $u['gender'] === 'Female')) ?></div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon v"><i class="fas fa-genderless"></i></div>
        <div>
          <div class="stat-label">Other</div>
          <div class="stat-value"><?= count(array_filter($users, fn($u) => $u['gender'] === 'Other')) ?></div>
        </div>
      </div>
    </div>

    <!-- TABLE -->
    <div class="table-card">
      <div class="table-card-header">
        <h3><i class="fas fa-users"></i> All Members</h3>
        <span class="count-badge"><?= $total ?> registered</span>
      </div>

      <?php if ($total === 0): ?>
        <div class="empty-state">
          <i class="fas fa-user-slash"></i>
          No members registered yet.
        </div>
      <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Member</th>
            <th>Email</th>
            <th>Gender</th>
            <th>Joined</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $i => $user): ?>
            <?php
              $initial = strtoupper(mb_substr($user['name'], 0, 1));
              $gclass  = strtolower($user['gender']);
              $date    = date('M j, Y', strtotime($user['created_at']));
            ?>
            <tr>
              <td class="date-text"><?= $i + 1 ?></td>
              <td>
                <div class="user-cell">
                  <div class="avatar"><?= $initial ?></div>
                  <div class="user-name"><?= htmlspecialchars($user['name']) ?></div>
                </div>
              </td>
              <td class="date-text"><?= htmlspecialchars($user['email']) ?></td>
              <td><span class="gender-badge <?= $gclass ?>"><?= $user['gender'] ?></span></td>
              <td class="date-text"><?= $date ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>

  </div>
</div>

</body>
</html>
