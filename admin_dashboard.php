<?php
session_start();
require 'config.php';

// Guard: admin only
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// ── Fetch stats ──
$total_users  = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$total_male   = $conn->query("SELECT COUNT(*) FROM users WHERE gender='Male'")->fetch_row()[0];
$total_female = $conn->query("SELECT COUNT(*) FROM users WHERE gender='Female'")->fetch_row()[0];
$total_other  = $conn->query("SELECT COUNT(*) FROM users WHERE gender='Other'")->fetch_row()[0];
$logged_in_today = $conn->query("SELECT COUNT(*) FROM users WHERE DATE(last_login) = CURDATE()")->fetch_row()[0];
$new_this_week   = $conn->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_row()[0];

// ── Fetch all users ──
$result = $conn->query("SELECT id, name, email, gender, created_at, last_login, login_count FROM users ORDER BY created_at DESC");
$users  = $result->fetch_all(MYSQLI_ASSOC);

// ── Recently logged in (last 10) ──
$recent_logins_res = $conn->query("SELECT name, email, last_login, login_count FROM users WHERE last_login IS NOT NULL ORDER BY last_login DESC LIMIT 10");
$recent_logins = $recent_logins_res->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard — UserHub</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <style>
    :root {
      --green:        #00ed64;
      --green-dark:   #00684a;
      --green-mid:    #00a35c;
      --green-light:  #e8fdf5;
      --green-glow:   rgba(0,237,100,0.15);
      --white:        #ffffff;
      --off-white:    #f7f9fc;
      --border:       #e8edf2;
      --text:         #1a202c;
      --muted:        #718096;
      --sidebar-bg:   #001e2b;
      --sidebar-w:    260px;
      --nav-h:        64px;
      --shadow-sm:    0 1px 4px rgba(0,0,0,0.06);
      --shadow-md:    0 4px 20px rgba(0,0,0,0.08);
      --shadow-green: 0 4px 20px rgba(0,237,100,0.2);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Roboto', sans-serif;
      background: var(--off-white);
      color: var(--text);
      display: flex;
      min-height: 100vh;
    }

    /* ════════════════════════════════
       SIDEBAR
    ════════════════════════════════ */
    .sidebar {
      width: var(--sidebar-w);
      background: var(--sidebar-bg);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0; left: 0;
      z-index: 100;
      overflow: hidden;
    }

    .sidebar::after {
      content: '';
      position: absolute;
      bottom: 0; left: 0; right: 0;
      height: 200px;
      background: linear-gradient(to top, rgba(0,163,92,0.12), transparent);
      pointer-events: none;
    }

    .sidebar-logo {
      padding: 28px 24px 20px;
      display: flex;
      align-items: center;
      gap: 12px;
      border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .logo-icon {
      width: 38px; height: 38px;
      background: var(--green);
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; color: #001e2b; font-weight: 900;
      flex-shrink: 0;
    }

    .logo-text {
      font-size: 18px; font-weight: 700;
      color: var(--white); letter-spacing: -0.3px;
    }

    .logo-text span { color: var(--green); }

    .sidebar-section {
      padding: 20px 16px 8px;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: rgba(255,255,255,0.25);
    }

    .nav-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 11px 20px;
      margin: 2px 10px;
      border-radius: 10px;
      color: rgba(255,255,255,0.55);
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
    }

    .nav-item i { width: 18px; font-size: 14px; }

    .nav-item:hover {
      background: rgba(255,255,255,0.06);
      color: rgba(255,255,255,0.9);
    }

    .nav-item.active {
      background: rgba(0,237,100,0.12);
      color: var(--green);
      border: 1px solid rgba(0,237,100,0.15);
    }

    .nav-item.active i { color: var(--green); }

    .sidebar-footer {
      margin-top: auto;
      padding: 20px 16px;
      border-top: 1px solid rgba(255,255,255,0.06);
      z-index: 1;
    }

    .admin-profile {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 12px;
      border-radius: 10px;
      background: rgba(255,255,255,0.04);
      margin-bottom: 10px;
    }

    .admin-avatar {
      width: 36px; height: 36px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--green), var(--green-mid));
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 14px; color: #001e2b;
      flex-shrink: 0;
    }

    .admin-info { overflow: hidden; }
    .admin-name { font-size: 13px; font-weight: 600; color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .admin-role { font-size: 11px; color: var(--green); }

    .logout-link {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 12px;
      border-radius: 10px;
      color: rgba(255,255,255,0.4);
      font-size: 13px;
      text-decoration: none;
      transition: all 0.2s;
    }

    .logout-link:hover { background: rgba(229,62,62,0.1); color: #fc8181; }

    /* ════════════════════════════════
       MAIN CONTENT
    ════════════════════════════════ */
    .main-content {
      margin-left: var(--sidebar-w);
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    /* ── TOP NAV ── */
    .topnav {
      height: var(--nav-h);
      background: var(--white);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 32px;
      position: sticky;
      top: 0;
      z-index: 50;
      box-shadow: var(--shadow-sm);
    }

    .topnav-left h2 {
      font-size: 18px;
      font-weight: 700;
      color: var(--text);
    }

    .topnav-left p {
      font-size: 12px;
      color: var(--muted);
      margin-top: 1px;
    }

    .topnav-right {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .topnav-badge {
      display: flex;
      align-items: center;
      gap: 6px;
      background: var(--green-light);
      color: var(--green-dark);
      font-size: 12px;
      font-weight: 600;
      padding: 6px 14px;
      border-radius: 20px;
      border: 1px solid rgba(0,163,92,0.2);
    }

    /* ── PAGE BODY ── */
    .page-body {
      padding: 32px;
      flex: 1;
    }

    /* ── STAT CARDS ── */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-bottom: 28px;
    }

    .stat-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 24px 26px;
      display: flex;
      align-items: center;
      gap: 18px;
      box-shadow: var(--shadow-sm);
      transition: box-shadow 0.2s, transform 0.2s;
    }

    .stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }

    .stat-card.highlight {
      background: linear-gradient(135deg, #001e2b 0%, #023430 100%);
      border-color: rgba(0,237,100,0.2);
      box-shadow: var(--shadow-green);
    }

    .stat-icon {
      width: 52px; height: 52px;
      border-radius: 14px;
      display: flex; align-items: center; justify-content: center;
      font-size: 20px;
      flex-shrink: 0;
    }

    .stat-icon.green  { background: var(--green-light); color: var(--green-mid); }
    .stat-icon.dark   { background: rgba(0,237,100,0.15); color: var(--green); }
    .stat-icon.blue   { background: #ebf8ff; color: #3182ce; }
    .stat-icon.pink   { background: #fff5f7; color: #d53f8c; }
    .stat-icon.purple { background: #faf5ff; color: #805ad5; }
    .stat-icon.orange { background: #fffaf0; color: #dd6b20; }

    .stat-info { flex: 1; }

    .stat-label {
      font-size: 12px;
      font-weight: 500;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-bottom: 6px;
    }

    .stat-card.highlight .stat-label { color: rgba(255,255,255,0.5); }

    .stat-value {
      font-size: 34px;
      font-weight: 900;
      color: var(--text);
      line-height: 1;
    }

    .stat-card.highlight .stat-value { color: var(--green); }

    .stat-sub {
      font-size: 11px;
      color: var(--muted);
      margin-top: 4px;
    }

    .stat-card.highlight .stat-sub { color: rgba(255,255,255,0.35); }

    /* ── SECTION HEADER ── */
    .section-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
    }

    .section-header h3 {
      font-size: 16px;
      font-weight: 700;
      color: var(--text);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .section-header h3 i { color: var(--green-mid); }

    .count-badge {
      background: var(--green-light);
      color: var(--green-dark);
      font-size: 12px;
      font-weight: 600;
      padding: 4px 12px;
      border-radius: 20px;
      border: 1px solid rgba(0,163,92,0.2);
    }

    /* ── TWO COLUMN LAYOUT ── */
    .two-col {
      display: grid;
      grid-template-columns: 1fr 380px;
      gap: 24px;
      margin-bottom: 28px;
    }

    /* ── TABLE CARD ── */
    .table-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 16px;
      overflow: hidden;
      box-shadow: var(--shadow-sm);
    }

    .table-card-header {
      padding: 18px 24px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    thead th {
      text-align: left;
      padding: 12px 20px;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--muted);
      background: #fafbfc;
      border-bottom: 1px solid var(--border);
    }

    tbody tr {
      border-bottom: 1px solid var(--border);
      transition: background 0.15s;
    }

    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #f7fdf9; }

    tbody td {
      padding: 14px 20px;
      font-size: 13px;
      vertical-align: middle;
    }

    .user-cell {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .avatar {
      width: 34px; height: 34px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--green), var(--green-mid));
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 13px; color: #001e2b;
      flex-shrink: 0;
    }

    .user-name  { font-weight: 600; color: var(--text); font-size: 13px; }
    .user-email { font-size: 11px; color: var(--muted); }

    .gender-badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
    }

    .gender-badge.male   { background: #ebf8ff; color: #2b6cb0; }
    .gender-badge.female { background: #fff5f7; color: #97266d; }
    .gender-badge.other  { background: #faf5ff; color: #553c9a; }

    .date-text { color: var(--muted); font-size: 12px; }

    .login-count-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      background: var(--green-light);
      color: var(--green-dark);
      font-size: 11px;
      font-weight: 600;
      padding: 3px 10px;
      border-radius: 20px;
    }

    .never-badge {
      color: #cbd5e0;
      font-size: 12px;
      font-style: italic;
    }

    /* ── RECENT LOGINS CARD ── */
    .activity-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 16px;
      overflow: hidden;
      box-shadow: var(--shadow-sm);
    }

    .activity-card-header {
      padding: 18px 20px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .activity-list { padding: 8px 0; }

    .activity-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 20px;
      border-bottom: 1px solid var(--border);
      transition: background 0.15s;
    }

    .activity-item:last-child { border-bottom: none; }
    .activity-item:hover { background: #f7fdf9; }

    .activity-avatar {
      width: 36px; height: 36px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--green), var(--green-mid));
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 13px; color: #001e2b;
      flex-shrink: 0;
    }

    .activity-info { flex: 1; overflow: hidden; }
    .activity-name  { font-size: 13px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .activity-email { font-size: 11px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .activity-time {
      font-size: 11px;
      color: var(--muted);
      white-space: nowrap;
      text-align: right;
    }

    .activity-time .dot {
      display: inline-block;
      width: 7px; height: 7px;
      border-radius: 50%;
      background: var(--green);
      margin-right: 4px;
      vertical-align: middle;
    }

    .empty-state {
      text-align: center;
      padding: 48px 20px;
      color: var(--muted);
    }

    .empty-state i { font-size: 32px; margin-bottom: 12px; color: #cbd5e0; display: block; }
    .empty-state p { font-size: 13px; }

    /* ── FULL TABLE SECTION ── */
    .full-table-section { margin-bottom: 28px; }

    /* ── SCROLLABLE TABLE ── */
    .table-scroll { overflow-x: auto; }

    @media (max-width: 1100px) {
      .two-col { grid-template-columns: 1fr; }
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 768px) {
      .sidebar { transform: translateX(-100%); }
      .main-content { margin-left: 0; }
      .stats-grid { grid-template-columns: 1fr; }
      .page-body { padding: 20px; }
    }
  </style>
</head>
<body>

<!-- ════════ SIDEBAR ════════ -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon"><i class="fas fa-leaf"></i></div>
    <div class="logo-text">User<span>Hub</span></div>
  </div>

  <div class="sidebar-section">Main Menu</div>

  <a href="#overview"      class="nav-item active"><i class="fas fa-th-large"></i> Overview</a>
  <a href="#all-members"   class="nav-item"><i class="fas fa-users"></i> All Members</a>
  <a href="#recent-logins" class="nav-item"><i class="fas fa-sign-in-alt"></i> Recent Logins</a>

  <div class="sidebar-section">System</div>
  <a href="login.php"      class="nav-item"><i class="fas fa-external-link-alt"></i> User Portal</a>

  <div class="sidebar-footer">
    <div class="admin-profile">
      <div class="admin-avatar">A</div>
      <div class="admin-info">
        <div class="admin-name">Administrator</div>
        <div class="admin-role"><i class="fas fa-circle" style="font-size:7px;"></i> Online</div>
      </div>
    </div>
    <a href="admin_logout.php" class="logout-link">
      <i class="fas fa-sign-out-alt"></i> Sign Out
    </a>
  </div>
</aside>

<!-- ════════ MAIN CONTENT ════════ -->
<div class="main-content">

  <!-- TOP NAV -->
  <div class="topnav">
    <div class="topnav-left">
      <h2>Admin Dashboard</h2>
      <p>Welcome back — here's what's happening today</p>
    </div>
    <div class="topnav-right">
      <div class="topnav-badge">
        <i class="fas fa-circle" style="font-size:8px;"></i>
        <?= date('D, M j Y') ?>
      </div>
    </div>
  </div>

  <!-- PAGE BODY -->
  <div class="page-body">

    <!-- ── STATS ── -->
    <div id="overview" class="stats-grid">
      <div class="stat-card highlight">
        <div class="stat-icon dark"><i class="fas fa-users"></i></div>
        <div class="stat-info">
          <div class="stat-label">Total Members</div>
          <div class="stat-value"><?= $total_users ?></div>
          <div class="stat-sub">Registered accounts</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-user-check"></i></div>
        <div class="stat-info">
          <div class="stat-label">Logged In Today</div>
          <div class="stat-value"><?= $logged_in_today ?></div>
          <div class="stat-sub">Active today</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-user-plus"></i></div>
        <div class="stat-info">
          <div class="stat-label">New This Week</div>
          <div class="stat-value"><?= $new_this_week ?></div>
          <div class="stat-sub">Last 7 days</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-mars"></i></div>
        <div class="stat-info">
          <div class="stat-label">Male</div>
          <div class="stat-value"><?= $total_male ?></div>
          <div class="stat-sub">Male members</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon pink"><i class="fas fa-venus"></i></div>
        <div class="stat-info">
          <div class="stat-label">Female</div>
          <div class="stat-value"><?= $total_female ?></div>
          <div class="stat-sub">Female members</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-genderless"></i></div>
        <div class="stat-info">
          <div class="stat-label">Other</div>
          <div class="stat-value"><?= $total_other ?></div>
          <div class="stat-sub">Other gender</div>
        </div>
      </div>
    </div>

    <!-- ── TWO COLUMN: ALL MEMBERS + RECENT LOGINS ── -->
    <div class="two-col">

      <!-- ALL MEMBERS TABLE -->
      <div id="all-members">
        <div class="section-header">
          <h3><i class="fas fa-users"></i> All Registered Members</h3>
          <span class="count-badge"><?= $total_users ?> total</span>
        </div>
        <div class="table-card">
          <div class="table-scroll">
            <?php if (empty($users)): ?>
              <div class="empty-state">
                <i class="fas fa-user-slash"></i>
                <p>No members registered yet.</p>
              </div>
            <?php else: ?>
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Member</th>
                  <th>Gender</th>
                  <th>Joined</th>
                  <th>Last Login</th>
                  <th>Logins</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $i => $u): ?>
                  <?php
                    $initial  = strtoupper(mb_substr($u['name'], 0, 1));
                    $gclass   = strtolower($u['gender']);
                    $joined   = date('M j, Y', strtotime($u['created_at']));
                    $last_log = $u['last_login'] ? date('M j, Y g:i A', strtotime($u['last_login'])) : null;
                  ?>
                  <tr>
                    <td class="date-text"><?= $i + 1 ?></td>
                    <td>
                      <div class="user-cell">
                        <div class="avatar"><?= $initial ?></div>
                        <div>
                          <div class="user-name"><?= htmlspecialchars($u['name']) ?></div>
                          <div class="user-email"><?= htmlspecialchars($u['email']) ?></div>
                        </div>
                      </div>
                    </td>
                    <td><span class="gender-badge <?= $gclass ?>"><?= $u['gender'] ?></span></td>
                    <td class="date-text"><?= $joined ?></td>
                    <td>
                      <?php if ($last_log): ?>
                        <span class="date-text"><?= $last_log ?></span>
                      <?php else: ?>
                        <span class="never-badge">Never</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="login-count-badge">
                        <i class="fas fa-sign-in-alt"></i> <?= (int)$u['login_count'] ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- RECENT LOGINS -->
      <div id="recent-logins">
        <div class="section-header">
          <h3><i class="fas fa-clock"></i> Recent Logins</h3>
          <span class="count-badge">Last 10</span>
        </div>
        <div class="activity-card">
          <?php if (empty($recent_logins)): ?>
            <div class="empty-state">
              <i class="fas fa-history"></i>
              <p>No login activity yet.</p>
            </div>
          <?php else: ?>
          <div class="activity-list">
            <?php foreach ($recent_logins as $rl): ?>
              <?php
                $initial  = strtoupper(mb_substr($rl['name'], 0, 1));
                $time_ago = $rl['last_login'] ? date('M j, g:i A', strtotime($rl['last_login'])) : '';
              ?>
              <div class="activity-item">
                <div class="activity-avatar"><?= $initial ?></div>
                <div class="activity-info">
                  <div class="activity-name"><?= htmlspecialchars($rl['name']) ?></div>
                  <div class="activity-email"><?= htmlspecialchars($rl['email']) ?></div>
                </div>
                <div class="activity-time">
                  <span class="dot"></span><?= $time_ago ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>

    </div><!-- end two-col -->

  </div><!-- end page-body -->
</div><!-- end main-content -->

<script>
  // Highlight active sidebar link on scroll/click
  document.querySelectorAll('.nav-item').forEach(link => {
    link.addEventListener('click', function() {
      document.querySelectorAll('.nav-item').forEach(l => l.classList.remove('active'));
      this.classList.add('active');
    });
  });
</script>

</body>
</html>
