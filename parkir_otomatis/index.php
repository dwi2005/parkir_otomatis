<?php
// Start PHP session
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$userNama = $isLoggedIn ? $_SESSION['nama'] : '';
$userRole = $isLoggedIn ? $_SESSION['role'] : '';
$userUsername = $isLoggedIn ? $_SESSION['username'] : '';

// Jika belum login, tampilkan halaman Login
if (!$isLoggedIn):
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HITAMIYOPARKING - Login</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;800&family=Rajdhani:wght@400;500;600&display=swap');

    :root {
      --primary: #00f5ff;
      --secondary: #7b2fff;
      --accent: #ff00aa;
      --bg-dark: #050b14;
      --bg-card: #0a1628;
      --text: #cceeff;
      --text-muted: #5580aa;
      --border: rgba(0, 245, 255, 0.15);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Rajdhani', sans-serif;
      background: var(--bg-dark);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      position: relative;
    }

    body::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image:
        linear-gradient(rgba(0, 245, 255, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 245, 255, 0.03) 1px, transparent 1px);
      background-size: 40px 40px;
      z-index: 0;
    }

    .login-container {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 400px;
      padding: 2.5rem;
      background: rgba(10, 22, 40, 0.7);
      backdrop-filter: blur(16px);
      border: 1px solid var(--border);
      border-radius: 20px;
      box-shadow: 0 0 50px rgba(0, 245, 255, 0.15);
      text-align: center;
    }

    .logo {
      font-family: 'Orbitron', monospace;
      font-size: 1.35rem;
      font-weight: 800;
      color: var(--primary);
      letter-spacing: 2px;
      margin-bottom: 2rem;
      text-shadow: 0 0 25px rgba(0, 245, 255, 0.6);
      word-break: break-word;
      overflow-wrap: break-word;
    }

    .logo span {
      color: var(--secondary);
    }

    .form-group {
      text-align: left;
      margin-bottom: 1.25rem;
    }

    .form-label {
      display: block;
      font-size: 0.8rem;
      color: var(--text-muted);
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 6px;
    }

    .form-input {
      width: 100%;
      background: rgba(0, 245, 255, 0.04);
      border: 1px solid rgba(0, 245, 255, 0.15);
      border-radius: 8px;
      color: var(--text);
      font-family: 'Rajdhani', sans-serif;
      font-size: 1rem;
      padding: 0.75rem 1rem;
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(0, 245, 255, 0.1);
    }

    .btn {
      width: 100%;
      padding: 0.75rem;
      border: none;
      border-radius: 8px;
      font-family: 'Orbitron', monospace;
      font-size: 0.9rem;
      font-weight: 600;
      letter-spacing: 1px;
      cursor: pointer;
      transition: all 0.2s;
      margin-top: 1rem;
      background: linear-gradient(135deg, rgba(0, 245, 255, 0.2), rgba(123, 47, 255, 0.2));
      color: var(--primary);
      border: 1px solid var(--primary);
      text-transform: uppercase;
    }

    .btn:hover {
      background: linear-gradient(135deg, rgba(0, 245, 255, 0.35), rgba(123, 47, 255, 0.35));
      box-shadow: 0 0 20px rgba(0, 245, 255, 0.3);
    }

    .error-msg {
      color: #ff3366;
      font-size: 0.85rem;
      margin-top: 1rem;
      display: none;
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="logo">HITAMIYO<span>PARKING</span></div>
    <form id="loginForm" onsubmit="handleLogin(event)">
      <div class="form-group">
        <label class="form-label">Username</label>
        <input type="text" id="username" class="form-input" placeholder="Masukkan username..." required autocomplete="username">
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" id="password" class="form-input" placeholder="Masukkan password..." required autocomplete="current-password">
      </div>
      <button type="submit" class="btn">Login 🔓</button>
      <div id="errorMsg" class="error-msg"></div>
    </form>
  </div>

  <script>
    function handleLogin(e) {
      e.preventDefault();
      const user = document.getElementById('username').value.trim();
      const pass = document.getElementById('password').value;
      const errEl = document.getElementById('errorMsg');

      errEl.style.display = 'none';

      const formData = new FormData();
      formData.append('username', user);
      formData.append('password', pass);

      fetch('api.php?action=login', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(res => {
        if (res.status === 'success') {
          window.location.reload();
        } else {
          errEl.textContent = res.message;
          errEl.style.display = 'block';
        }
      })
      .catch(err => {
        errEl.textContent = 'Gagal menghubungi server.';
        errEl.style.display = 'block';
      });
    }
  </script>
</body>

</html>
<?php
exit();
endif;
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HITAMIYOPARKING - Sistem Parkiran + QR Code</title>
  <!-- Library QR Code Generator -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <!-- Library Scan QR Code via Camera -->
  <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;800&family=Rajdhani:wght@400;500;600&display=swap');

    :root {
      --primary: #00f5ff;
      --secondary: #7b2fff;
      --accent: #ff00aa;
      --bg-dark: #050b14;
      --bg-card: #0a1628;
      --bg-card2: #0d1f3c;
      --text: #cceeff;
      --text-muted: #5580aa;
      --success: #00ff88;
      --danger: #ff3366;
      --warning: #ffaa00;
      --border: rgba(0, 245, 255, 0.15);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Rajdhani', sans-serif;
      background: var(--bg-dark);
      color: var(--text);
      min-height: 100vh;
      overflow-x: hidden;
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image:
        linear-gradient(rgba(0, 245, 255, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 245, 255, 0.03) 1px, transparent 1px);
      background-size: 40px 40px;
      pointer-events: none;
      z-index: 0;
    }

    /* HEADER */
    header {
      position: sticky;
      top: 0;
      z-index: 100;
      background: rgba(5, 11, 20, 0.92);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--border);
      padding: 0 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 64px;
    }

    .logo {
      font-family: 'Orbitron', monospace;
      font-size: 1.4rem;
      font-weight: 800;
      color: var(--primary);
      letter-spacing: 2px;
      text-shadow: 0 0 20px rgba(0, 245, 255, 0.5);
    }

    .logo span {
      color: var(--secondary);
    }

    .clock {
      font-family: 'Orbitron', monospace;
      font-size: 1rem;
      color: var(--primary);
      letter-spacing: 2px;
    }

    .date-text {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    /* NAV TABS */
    .nav-tabs {
      display: flex;
      gap: 0;
      border-bottom: 1px solid var(--border);
      background: var(--bg-card2);
      position: relative;
      z-index: 1;
      padding: 0 2rem;
    }

    .nav-tab {
      padding: 0.85rem 1.5rem;
      font-family: 'Orbitron', monospace;
      font-size: 0.75rem;
      letter-spacing: 1px;
      color: var(--text-muted);
      cursor: pointer;
      border: none;
      background: none;
      border-bottom: 2px solid transparent;
      transition: all 0.2s;
      text-transform: uppercase;
    }

    .nav-tab:hover {
      color: var(--text);
    }

    .nav-tab.active {
      color: var(--primary);
      border-bottom-color: var(--primary);
    }

    /* LAYOUT */
    .container {
      position: relative;
      z-index: 1;
      padding: 1.5rem 2rem;
      max-width: 1400px;
      margin: 0 auto;
    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
    }

    /* STATS BAR */
    .stats-bar {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .stat-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 1rem 1.25rem;
      display: flex;
      flex-direction: column;
      gap: 4px;
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 2px;
    }

    .stat-card.cyan::before {
      background: var(--primary);
    }

    .stat-card.purple::before {
      background: var(--secondary);
    }

    .stat-card.green::before {
      background: var(--success);
    }

    .stat-card.pink::before {
      background: var(--accent);
    }

    .stat-label {
      font-size: 0.75rem;
      color: var(--text-muted);
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    .stat-value {
      font-family: 'Orbitron', monospace;
      font-size: 1.8rem;
      font-weight: 800;
    }

    .stat-card.cyan .stat-value {
      color: var(--primary);
    }

    .stat-card.purple .stat-value {
      color: var(--secondary);
    }

    .stat-card.green .stat-value {
      color: var(--success);
    }

    .stat-card.pink .stat-value {
      color: var(--accent);
    }

    .stat-sub {
      font-size: 0.8rem;
      color: var(--text-muted);
    }

    /* MAIN GRID */
    .main-grid {
      display: grid;
      grid-template-columns: 380px 1fr;
      gap: 1.5rem;
      margin-bottom: 1.5rem;
    }

    /* PANEL */
    .panel {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      overflow: hidden;
    }

    .panel-header {
      padding: 1rem 1.25rem;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      gap: 10px;
      background: var(--bg-card2);
    }

    .panel-icon {
      width: 32px;
      height: 32px;
      background: rgba(0, 245, 255, 0.1);
      border: 1px solid rgba(0, 245, 255, 0.3);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
    }

    .panel-title {
      font-family: 'Orbitron', monospace;
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--primary);
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    .panel-body {
      padding: 1.25rem;
    }

    /* FORM */
    .form-group {
      margin-bottom: 1rem;
    }

    .form-label {
      display: block;
      font-size: 0.75rem;
      color: var(--text-muted);
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 6px;
    }

    .form-input,
    .form-select {
      width: 100%;
      background: rgba(0, 245, 255, 0.04);
      border: 1px solid rgba(0, 245, 255, 0.15);
      border-radius: 8px;
      color: var(--text);
      font-family: 'Rajdhani', sans-serif;
      font-size: 1rem;
      padding: 0.6rem 0.9rem;
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-input:focus,
    .form-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(0, 245, 255, 0.1);
    }

    .form-select option {
      background: #0a1628;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0.75rem;
    }

    /* BUTTONS */
    .btn {
      width: 100%;
      padding: 0.75rem;
      border: none;
      border-radius: 8px;
      font-family: 'Orbitron', monospace;
      font-size: 0.85rem;
      font-weight: 600;
      letter-spacing: 1px;
      cursor: pointer;
      transition: all 0.2s;
      margin-bottom: 0.5rem;
    }

    .btn-primary {
      background: linear-gradient(135deg, rgba(0, 245, 255, 0.2), rgba(123, 47, 255, 0.2));
      color: var(--primary);
      border: 1px solid var(--primary);
      text-transform: uppercase;
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, rgba(0, 245, 255, 0.35), rgba(123, 47, 255, 0.35));
      box-shadow: 0 0 20px rgba(0, 245, 255, 0.3);
    }

    .btn-success {
      background: rgba(0, 255, 136, 0.1);
      color: var(--success);
      border: 1px solid rgba(0, 255, 136, 0.4);
      text-transform: uppercase;
    }

    .btn-success:hover {
      background: rgba(0, 255, 136, 0.2);
    }

    .btn-danger {
      background: rgba(255, 51, 102, 0.1);
      color: var(--danger);
      border: 1px solid rgba(255, 51, 102, 0.4);
      text-transform: uppercase;
    }

    .btn-danger:hover {
      background: rgba(255, 51, 102, 0.2);
      box-shadow: 0 0 20px rgba(255, 51, 102, 0.3);
    }

    .btn:active {
      transform: scale(0.98);
    }

    /* SLOT GRID */
    .slot-filter {
      display: flex;
      gap: 0.5rem;
      padding: 0 1.25rem 1rem;
      flex-wrap: wrap;
    }

    .filter-btn {
      padding: 0.3rem 0.9rem;
      border-radius: 20px;
      border: 1px solid var(--border);
      background: transparent;
      color: var(--text-muted);
      font-family: 'Rajdhani', sans-serif;
      font-size: 0.85rem;
      cursor: pointer;
      transition: all 0.2s;
    }

    .filter-btn.active,
    .filter-btn:hover {
      border-color: var(--primary);
      color: var(--primary);
      background: rgba(0, 245, 255, 0.07);
    }

    .slot-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
      gap: 0.6rem;
      padding: 0 1.25rem 1.25rem;
      max-height: 280px;
      overflow-y: auto;
    }

    .slot-grid::-webkit-scrollbar {
      width: 4px;
    }

    .slot-grid::-webkit-scrollbar-thumb {
      background: var(--border);
      border-radius: 2px;
    }

    .slot-item {
      aspect-ratio: 1;
      border-radius: 10px;
      border: 1px solid;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 4px;
      cursor: pointer;
      transition: all 0.2s;
      font-size: 0.7rem;
      text-align: center;
      padding: 4px;
    }

    .slot-item .slot-num {
      font-family: 'Orbitron', monospace;
      font-size: 0.8rem;
      font-weight: 600;
    }

    .slot-item.available {
      border-color: rgba(0, 255, 136, 0.3);
      background: rgba(0, 255, 136, 0.05);
      color: var(--success);
    }

    .slot-item.available:hover {
      border-color: var(--success);
      background: rgba(0, 255, 136, 0.12);
      box-shadow: 0 0 12px rgba(0, 255, 136, 0.2);
    }

    .slot-item.occupied {
      border-color: rgba(255, 51, 102, 0.3);
      background: rgba(255, 51, 102, 0.06);
      color: var(--danger);
      cursor: default;
    }

    .slot-item.selected {
      border-color: var(--primary);
      background: rgba(0, 245, 255, 0.12);
      color: var(--primary);
      box-shadow: 0 0 16px rgba(0, 245, 255, 0.25);
    }

    /* TABLE */
    .data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.9rem;
    }

    .data-table th {
      background: var(--bg-card2);
      color: var(--text-muted);
      font-size: 0.7rem;
      letter-spacing: 1px;
      text-transform: uppercase;
      padding: 0.7rem 1rem;
      text-align: left;
      border-bottom: 1px solid var(--border);
    }

    .data-table td {
      padding: 0.7rem 1rem;
      border-bottom: 1px solid rgba(0, 245, 255, 0.05);
      vertical-align: middle;
    }

    .data-table tr:hover td {
      background: rgba(0, 245, 255, 0.03);
    }

    .badge {
      display: inline-block;
      padding: 2px 10px;
      border-radius: 20px;
      font-size: 0.72rem;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .badge-motor {
      background: rgba(0, 245, 255, 0.1);
      color: var(--primary);
      border: 1px solid rgba(0, 245, 255, 0.3);
    }

    .badge-mobil {
      background: rgba(123, 47, 255, 0.12);
      color: #a87fff;
      border: 1px solid rgba(123, 47, 255, 0.35);
    }

    .badge-truk {
      background: rgba(255, 170, 0, 0.1);
      color: var(--warning);
      border: 1px solid rgba(255, 170, 0, 0.3);
    }

    .plat {
      font-family: 'Orbitron', monospace;
      font-size: 0.8rem;
      color: var(--text);
    }

    .btn-sm {
      padding: 0.25rem 0.75rem;
      border-radius: 6px;
      font-family: 'Rajdhani', sans-serif;
      font-size: 0.8rem;
      cursor: pointer;
      transition: all 0.2s;
      border: 1px solid;
      background: transparent;
    }

    .btn-sm-qr {
      color: var(--primary);
      border-color: rgba(0, 245, 255, 0.4);
    }

    .btn-sm-qr:hover {
      background: rgba(0, 245, 255, 0.1);
    }

    .btn-sm-out {
      color: var(--danger);
      border-color: rgba(255, 51, 102, 0.4);
    }

    .btn-sm-out:hover {
      background: rgba(255, 51, 102, 0.1);
    }

    /* MODAL */
    .modal-overlay {
      position: fixed;
      inset: 0;
      z-index: 999;
      background: rgba(5, 11, 20, 0.88);
      backdrop-filter: blur(6px);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.2s;
    }

    .modal-overlay.open {
      opacity: 1;
      pointer-events: all;
    }

    .modal {
      background: var(--bg-card);
      border: 1px solid var(--primary);
      border-radius: 16px;
      width: 380px;
      box-shadow: 0 0 40px rgba(0, 245, 255, 0.15);
      transform: scale(0.95);
      transition: transform 0.2s;
    }

    .modal-overlay.open .modal {
      transform: scale(1);
    }

    .modal-header {
      padding: 1.25rem 1.5rem;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .modal-title {
      font-family: 'Orbitron', monospace;
      font-size: 0.9rem;
      color: var(--primary);
    }

    .modal-close {
      background: none;
      border: none;
      color: var(--text-muted);
      cursor: pointer;
      font-size: 1.2rem;
    }

    .modal-close:hover {
      color: var(--danger);
    }

    .modal-body {
      padding: 1.5rem;
    }

    .modal-wide {
      width: 480px;
    }

    .receipt-row {
      display: flex;
      justify-content: space-between;
      padding: 0.4rem 0;
      border-bottom: 1px dashed rgba(0, 245, 255, 0.08);
      font-size: 0.9rem;
    }

    .receipt-row:last-child {
      border-bottom: none;
    }

    .receipt-label {
      color: var(--text-muted);
    }

    .receipt-value {
      color: var(--text);
      font-weight: 600;
    }

    .receipt-total {
      font-family: 'Orbitron', monospace;
      font-size: 1.2rem;
      color: var(--success);
    }

    /* QR STYLES */
    .qr-wrapper {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1rem;
      padding: 1rem;
      background: rgba(0, 245, 255, 0.03);
      border: 1px solid var(--border);
      border-radius: 12px;
      margin-bottom: 1rem;
    }

    .qr-box {
      background: white;
      padding: 12px;
      border-radius: 8px;
      display: inline-block;
      position: relative;
    }

    .qr-box::before,
    .qr-box::after {
      content: '';
      position: absolute;
      width: 20px;
      height: 20px;
      border-color: var(--primary);
      border-style: solid;
    }

    .qr-box::before {
      top: -4px;
      left: -4px;
      border-width: 3px 0 0 3px;
      border-radius: 4px 0 0 0;
    }

    .qr-box::after {
      bottom: -4px;
      right: -4px;
      border-width: 0 3px 3px 0;
      border-radius: 0 0 4px 0;
    }

    .qr-label {
      font-family: 'Orbitron', monospace;
      font-size: 0.7rem;
      color: var(--text-muted);
      letter-spacing: 2px;
      text-transform: uppercase;
      text-align: center;
    }

    .qr-id {
      font-family: 'Orbitron', monospace;
      font-size: 0.85rem;
      color: var(--primary);
      letter-spacing: 1px;
    }

    .ticket-header {
      text-align: center;
      margin-bottom: 1rem;
      padding-bottom: 1rem;
      border-bottom: 1px dashed rgba(0, 245, 255, 0.2);
    }

    .ticket-title {
      font-family: 'Orbitron', monospace;
      font-size: 1rem;
      color: var(--primary);
      letter-spacing: 2px;
    }

    .ticket-sub {
      font-size: 0.75rem;
      color: var(--text-muted);
      margin-top: 4px;
    }

    /* QR SCANNER AREA */
    .scanner-area {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      overflow: hidden;
      margin-bottom: 1.5rem;
    }

    .scanner-body {
      padding: 1.5rem;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;
    }

    .scan-input-box {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .scan-input-big {
      width: 100%;
      background: rgba(0, 245, 255, 0.04);
      border: 2px solid rgba(0, 245, 255, 0.2);
      border-radius: 12px;
      color: var(--text);
      font-family: 'Orbitron', monospace;
      font-size: 1rem;
      padding: 0.75rem 1rem;
      outline: none;
      text-align: center;
      letter-spacing: 2px;
      transition: all 0.2s;
    }

    .scan-input-big:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(0, 245, 255, 0.1);
    }

    .scan-input-big::placeholder {
      color: var(--text-muted);
      font-size: 0.8rem;
      letter-spacing: 1px;
    }

    .scan-result-box {
      background: rgba(0, 245, 255, 0.03);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 1.25rem;
      min-height: 200px;
    }

    .scan-result-empty {
      text-align: center;
      padding: 2rem 0;
      color: var(--text-muted);
      font-size: 0.85rem;
    }

    .scan-result-icon {
      font-size: 2.5rem;
      margin-bottom: 0.5rem;
    }

    /* TARIF */
    .tarif-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 0.5rem;
      margin-bottom: 1rem;
    }

    .tarif-item {
      background: rgba(0, 245, 255, 0.04);
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 0.6rem;
      text-align: center;
    }

    .tarif-item .t-type {
      font-size: 0.7rem;
      color: var(--text-muted);
      text-transform: uppercase;
    }

    .tarif-item .t-price {
      font-family: 'Orbitron', monospace;
      font-size: 0.75rem;
      color: var(--primary);
      margin-top: 2px;
    }

    /* LEGEND */
    .legend {
      display: flex;
      gap: 1rem;
      align-items: center;
      padding: 0.75rem 1.25rem;
      background: var(--bg-card2);
      border-top: 1px solid var(--border);
    }

    .legend-item {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 0.78rem;
      color: var(--text-muted);
    }

    .legend-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      border: 1.5px solid;
    }

    .legend-dot.available {
      border-color: var(--success);
      background: rgba(0, 255, 136, 0.2);
    }

    .legend-dot.occupied {
      border-color: var(--danger);
      background: rgba(255, 51, 102, 0.2);
    }

    .legend-dot.selected {
      border-color: var(--primary);
      background: rgba(0, 245, 255, 0.2);
    }

    /* SEARCH */
    .search-bar {
      padding: 0.75rem 1.25rem;
      border-bottom: 1px solid var(--border);
    }

    .search-input {
      width: 100%;
      background: rgba(0, 245, 255, 0.04);
      border: 1px solid var(--border);
      border-radius: 8px;
      color: var(--text);
      font-family: 'Rajdhani', sans-serif;
      font-size: 0.9rem;
      padding: 0.5rem 0.75rem;
      outline: none;
    }

    .search-input:focus {
      border-color: var(--primary);
    }

    .search-input::placeholder {
      color: var(--text-muted);
    }

    .empty-state {
      text-align: center;
      padding: 2rem;
      color: var(--text-muted);
      font-size: 0.9rem;
    }

    /* TOAST */
    .toast-container {
      position: fixed;
      top: 80px;
      right: 1.5rem;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .toast {
      background: var(--bg-card2);
      border-left: 3px solid var(--primary);
      border-radius: 8px;
      padding: 0.75rem 1rem;
      min-width: 240px;
      font-size: 0.85rem;
      animation: slideIn 0.3s ease;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
    }

    .toast.success {
      border-color: var(--success);
      color: var(--success);
    }

    .toast.danger {
      border-color: var(--danger);
      color: var(--danger);
    }

    @keyframes slideIn {
      from {
        transform: translateX(40px);
        opacity: 0;
      }

      to {
        transform: translateX(0);
        opacity: 1;
      }
    }

    /* PRINT */
    @media print {
      body {
        background: white;
        color: black;
      }

      body::before {
        display: none;
      }

      header,
      .nav-tabs,
      .no-print,
      .modal-overlay,
      .toast-container {
        display: none !important;
      }

      .print-ticket {
        display: block !important;
      }
    }

    .pulse {
      animation: pulse 2s infinite;
    }

    @keyframes pulse {

      0%,
      100% {
        opacity: 1;
      }

      50% {
        opacity: 0.5;
      }
    }

    .scanning-indicator {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.8rem;
      color: var(--primary);
      padding: 0.5rem;
      background: rgba(0, 245, 255, 0.05);
      border-radius: 8px;
      border: 1px solid rgba(0, 245, 255, 0.15);
    }

    .dot-pulse {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--primary);
      animation: pulse 1s infinite;
    }

    @media (max-width: 900px) {
      .main-grid {
        grid-template-columns: 1fr;
      }

      .stats-bar {
        grid-template-columns: repeat(2, 1fr);
      }

      .scanner-body {
        grid-template-columns: 1fr;
      }

      .modal {
        width: 95vw;
      }

      .modal-wide {
        width: 95vw;
      }
    }
    /* ===================== REKAP HARIAN CUSTOM STYLES ===================== */
    .rekap-summary-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.25rem;
      margin-bottom: 1.5rem;
    }
    .rekap-sum-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 1.25rem;
      position: relative;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .rekap-sum-card::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 3px;
      background: var(--primary);
      opacity: 0.8;
    }
    .rekap-blue::after { background: var(--primary); }
    .rekap-green::after { background: var(--success); }
    .rekap-purple::after { background: var(--secondary); }
    .rekap-orange::after { background: var(--warning); }

    .rekap-sum-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 30px rgba(0, 245, 255, 0.15);
    }
    .rekap-sum-icon {
      font-size: 1.75rem;
      margin-bottom: 0.5rem;
    }
    .rekap-sum-label {
      font-size: 0.8rem;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .rekap-sum-val {
      font-family: 'Orbitron', monospace;
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--text);
      margin-top: 4px;
      text-shadow: 0 0 10px rgba(0, 245, 255, 0.2);
    }
    .rekap-blue .rekap-sum-val { color: var(--primary); }
    .rekap-green .rekap-sum-val { color: var(--success); }
    .rekap-purple .rekap-sum-val { color: var(--secondary); }
    .rekap-orange .rekap-sum-val { color: var(--warning); }

    .rekap-row-2col {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;
      margin-bottom: 1.5rem;
    }
    .rekap-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    .rekap-card-header {
      padding: 1rem 1.25rem;
      background: var(--bg-card2);
      border-bottom: 1px solid var(--border);
      font-family: 'Orbitron', monospace;
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--primary);
      text-transform: uppercase;
      letter-spacing: 1px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .rekap-card-body {
      padding: 1.25rem;
    }
    .rekap-badge-count {
      background: rgba(0, 245, 255, 0.15);
      border: 1px solid var(--primary);
      color: var(--primary);
      font-size: 0.75rem;
      padding: 0.15rem 0.6rem;
      border-radius: 20px;
      font-family: 'Orbitron', monospace;
    }
    .rekap-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.9rem;
    }
    .rekap-table th, .rekap-table td {
      padding: 0.75rem 1rem;
      text-align: left;
    }
    .rekap-table th {
      background: rgba(0, 245, 255, 0.03);
      color: var(--text-muted);
      font-weight: 600;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-bottom: 1px solid rgba(0, 245, 255, 0.15);
    }
    .rekap-table td {
      border-bottom: 1px solid rgba(0, 245, 255, 0.05);
      color: var(--text);
    }
    .rekap-table tr:hover td {
      background: rgba(0, 245, 255, 0.02);
    }
    .rekap-table-full {
      width: 100%;
    }
    .rekap-slot-vis {
      display: flex;
      flex-wrap: wrap;
      gap: 5px;
      padding: 0.5rem;
      border-radius: 8px;
      background: rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(0, 245, 255, 0.05);
    }
    .rekap-slot-dot {
      width: 12px;
      height: 12px;
      border-radius: 3px;
      display: inline-block;
    }
    .rekap-slot-dot.terisi {
      background: var(--danger);
      box-shadow: 0 0 5px var(--danger);
    }
    .rekap-slot-dot.kosong {
      background: var(--success);
      box-shadow: 0 0 5px var(--success);
    }

    /* Print Header & TTD defaults (Hidden in screen) */
    .rekap-print-header {
      display: none;
    }
    .rekap-ttd {
      display: none;
      justify-content: space-between;
      margin-top: 3rem;
      padding: 0 2rem;
    }
    .rekap-ttd-box {
      text-align: center;
      width: 200px;
    }
    .rekap-ttd-title {
      font-weight: 600;
      margin-top: 0.25rem;
    }
    .rekap-ttd-space {
      height: 60px;
    }

    /* Responsive grid styles for screen */
    @media (max-width: 992px) {
      .rekap-summary-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      .rekap-row-2col {
        grid-template-columns: 1fr;
      }
    }
    @media (max-width: 576px) {
      .rekap-summary-grid {
        grid-template-columns: 1fr;
      }
    }

    @media print {
      body {
        background: #fff !important;
        color: #000 !important;
      }
      body::before {
        display: none !important;
      }
      header, .nav-tabs, .nav-tab, .no-print, .btn, .search-bar, .panel-header button, input[type="date"] {
        display: none !important;
      }
      /* Ensure layout is single-column block and looks neat */
      .rekap-summary-grid {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 10px !important;
        margin-bottom: 20px !important;
      }
      .rekap-sum-card {
        background: #fff !important;
        border: 1px solid #000 !important;
        box-shadow: none !important;
        color: #000 !important;
        padding: 10px !important;
        border-radius: 8px !important;
      }
      .rekap-sum-card::after {
        display: none !important;
      }
      .rekap-sum-val {
        color: #000 !important;
        text-shadow: none !important;
      }
      .rekap-blue .rekap-sum-val, .rekap-green .rekap-sum-val, .rekap-purple .rekap-sum-val, .rekap-orange .rekap-sum-val {
        color: #000 !important;
      }
      .rekap-row-2col {
        display: grid !important;
        grid-template-columns: 1.2fr 0.8fr !important;
        gap: 20px !important;
        margin-bottom: 20px !important;
      }
      .rekap-card {
        background: #fff !important;
        border: 1px solid #000 !important;
        box-shadow: none !important;
        color: #000 !important;
        border-radius: 8px !important;
      }
      .rekap-card-header {
        background: #f0f0f0 !important;
        color: #000 !important;
        border-bottom: 1px solid #000 !important;
        font-weight: bold !important;
      }
      .rekap-badge-count {
        border: 1px solid #000 !important;
        color: #000 !important;
        background: transparent !important;
      }
      .rekap-table th {
        background: #e5e5e5 !important;
        color: #000 !important;
        border-bottom: 1px solid #000 !important;
      }
      .rekap-table td {
        border-bottom: 1px solid #ddd !important;
        color: #000 !important;
      }
      .rekap-slot-vis {
        border: 1px solid #ccc !important;
        background: transparent !important;
      }
      .rekap-slot-dot {
        border: 1px solid #000 !important;
      }
      .rekap-slot-dot.terisi {
        background: #555 !important;
        box-shadow: none !important;
      }
      .rekap-slot-dot.kosong {
        background: #eee !important;
        box-shadow: none !important;
      }
      .rekap-print-header {
        display: block !important;
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px double #000;
        padding-bottom: 10px;
      }
      .rekap-print-logo {
        font-family: 'Orbitron', monospace;
        font-size: 1.8rem;
        font-weight: 800;
        letter-spacing: 2px;
      }
      .rekap-print-sub {
        font-size: 1.1rem;
        margin-top: 5px;
        text-transform: uppercase;
        letter-spacing: 1px;
      }
      .rekap-print-tgl {
        font-size: 0.95rem;
        margin-top: 5px;
      }
      .rekap-ttd {
        display: flex !important;
        color: #000 !important;
      }
      /* Ensure other tables print well */
      .data-table {
        color: #000 !important;
      }
    }
    /* =========================================================================== */
  </style>
</head>

<body>

  <header>
    <div class="logo">HITAMIYO<span>PARKING</span></div>
    <div style="display:flex;gap:2rem;align-items:center">
      <div style="text-align:right">
        <div style="font-size:0.9rem;font-weight:600;color:var(--primary)">👤 <?= htmlspecialchars($userNama) ?> (<?= htmlspecialchars($userRole) ?>)</div>
        <div class="date-text" id="dateText"></div>
      </div>
      <div>
        <div class="clock" id="clockText">00:00:00</div>
      </div>
      <div>
        <button onclick="handleLogout()" class="btn-sm btn-sm-out" style="padding: 0.4rem 0.8rem; border-radius: 8px; font-family:'Orbitron', monospace; text-transform:uppercase; font-size:0.75rem;">🚪 Logout</button>
      </div>
    </div>
  </header>

  <!-- NAV TABS -->
  <div class="nav-tabs">
    <button class="nav-tab active" onclick="switchTab('dashboard')">🏠 Dashboard</button>
    <button class="nav-tab" onclick="switchTab('qrscan')">📷 Scan QR Keluar</button>
    <button class="nav-tab" onclick="switchTab('riwayat')">📋 Riwayat</button>
    <button class="nav-tab" onclick="switchTab('kendaraan')">🚘 Kelola Kendaraan</button>
    <?php if ($userRole === 'admin'): ?>
    <button class="nav-tab" onclick="switchTab('kelola-slot')">⚙️ Kelola Slot</button>
    <button class="nav-tab" onclick="switchTab('kelola-user')">👤 Kelola User</button>
    <button class="nav-tab" onclick="switchTab('laporan-inception')">📑 RECAP HARIAN</button>
    <?php endif; ?>
  </div>

  <!-- ===================== TAB: DASHBOARD ===================== -->
  <div class="tab-content active" id="tab-dashboard">
    <div class="container">

      <!-- STATS -->
      <div class="stats-bar">
        <div class="stat-card cyan">
          <div class="stat-label">Total Slot</div>
          <div class="stat-value" id="statTotal">--</div>
          <div class="stat-sub">Kapasitas penuh</div>
        </div>
        <div class="stat-card green">
          <div class="stat-label">Tersedia</div>
          <div class="stat-value" id="statAvail">--</div>
          <div class="stat-sub">Slot kosong</div>
        </div>
        <div class="stat-card purple">
          <div class="stat-label">Terisi</div>
          <div class="stat-value" id="statOcc">--</div>
          <div class="stat-sub">Kendaraan parkir</div>
        </div>
        <div class="stat-card pink">
          <div class="stat-label">Pendapatan</div>
          <div class="stat-value" id="statRev">0</div>
          <div class="stat-sub">Total hari ini (Rp)</div>
        </div>
      </div>

      <div class="main-grid">
        <!-- FORM -->
        <div>
          <div class="panel" style="margin-bottom:1rem">
            <div class="panel-header">
              <div class="panel-icon">🚗</div>
              <div class="panel-title">Masuk Kendaraan</div>
            </div>
            <div class="panel-body">
              <div class="form-group">
                <label class="form-label">Nomor Plat</label>
                <input class="form-input" id="inputPlat" placeholder="Contoh: B 1234 ABC"
                  style="text-transform:uppercase">
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Jenis Kendaraan</label>
                  <select class="form-select" id="inputJenis">
                    <option value="Motor">Motor</option>
                    <option value="Mobil">Mobil</option>
                    <option value="Truk">Truk</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="form-label">Nama Pemilik</label>
                  <input class="form-input" id="inputNama" placeholder="Nama">
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Slot Dipilih</label>
                <input class="form-input" id="inputSlot" placeholder="Klik slot di bawah..." readonly>
              </div>
              <div class="tarif-grid">
                <div class="tarif-item">
                  <div class="t-type">Motor</div>
                  <div class="t-price">Rp 2.000/jam</div>
                </div>
                <div class="tarif-item">
                  <div class="t-type">Mobil</div>
                  <div class="t-price">Rp 5.000/jam</div>
                </div>
                <div class="tarif-item">
                  <div class="t-type">Truk</div>
                  <div class="t-price">Rp 10.000/jam</div>
                </div>
              </div>
              <button class="btn btn-primary" onclick="kendaraanMasuk()">⬇ KENDARAAN MASUK + GENERATE QR</button>
            </div>
          </div>

          <!-- SLOT MAP -->
          <div class="panel">
            <div class="panel-header">
              <div class="panel-icon">🅿</div>
              <div class="panel-title">Peta Slot</div>
            </div>
            <div class="slot-filter">
              <button class="filter-btn active" onclick="filterSlot('semua',this)">Semua</button>
              <button class="filter-btn" onclick="filterSlot('available',this)">Kosong</button>
              <button class="filter-btn" onclick="filterSlot('occupied',this)">Terisi</button>
            </div>
            <div class="slot-grid" id="slotGrid"></div>
            <div class="legend">
              <div class="legend-item">
                <div class="legend-dot available"></div>Kosong
              </div>
              <div class="legend-item">
                <div class="legend-dot occupied"></div>Terisi
              </div>
              <div class="legend-item">
                <div class="legend-dot selected"></div>Dipilih
              </div>
            </div>
          </div>
        </div>

        <!-- TABLE -->
        <div class="panel">
          <div class="panel-header">
            <div class="panel-icon">📋</div>
            <div class="panel-title">Data Kendaraan Parkir</div>
          </div>
          <div class="search-bar">
            <input class="search-input" id="searchInput" placeholder="🔍  Cari nomor plat atau nama..."
              oninput="renderTable()">
          </div>
          <div style="overflow-x:auto;max-height:520px;overflow-y:auto;">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Slot</th>
                  <th>Plat</th>
                  <th>Jenis</th>
                  <th>Nama</th>
                  <th>Masuk</th>
                  <th>Durasi</th>
                  <th>Petugas</th>
                  <th>QR Ticket</th>
                  <th>Keluar</th>
                </tr>
              </thead>
              <tbody id="tableBody">
                <tr>
                  <td colspan="9" class="empty-state">Loading data...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ===================== TAB: QR SCAN ===================== -->
  <div class="tab-content" id="tab-qrscan">
    <div class="container">
      <div class="scanner-area">
        <div class="panel-header">
          <div class="panel-icon">📷</div>
          <div class="panel-title">Scan QR Code Keluar</div>
        </div>
        <div class="scanner-body">
          <!-- KIRI: Input + Kamera -->
          <div class="scan-input-box">
            <!-- Tombol Kamera -->
            <div style="margin-bottom:0.75rem">
              <button id="btnStartCamera" class="btn btn-primary" onclick="startCamera()" style="width:100%;margin:0">
                📸 Aktifkan Kamera Scan
              </button>
              <button id="btnStopCamera" class="btn" onclick="stopCamera()" style="width:100%;margin:0;display:none;background:transparent;border:1px solid var(--danger);color:var(--danger)">
                ⏹ Matikan Kamera
              </button>
            </div>

            <!-- Area viewport kamera -->
            <div id="qrReaderWrapper" style="display:none;margin-bottom:0.75rem;border-radius:12px;overflow:hidden;border:2px solid var(--primary);position:relative">
              <div id="qrReader" style="width:100%"></div>
              <div style="position:absolute;bottom:8px;left:0;right:0;text-align:center;font-size:0.72rem;color:var(--primary);background:rgba(5,11,20,0.7);padding:4px">
                📡 Arahkan kamera ke QR Code tiket parkir
              </div>
            </div>

            <!-- Pemisah -->
            <div style="text-align:center;font-size:0.78rem;color:var(--text-muted);margin:0.5rem 0">─── atau input manual ───</div>

            <!-- Input Manual -->
            <div>
              <div class="form-label" style="margin-bottom:6px">Kode QR Tiket (Manual)</div>
              <div class="scanning-indicator" style="margin-bottom:10px">
                <div class="dot-pulse"></div>
                <span>Siap menerima input QR Code</span>
              </div>
              <input class="scan-input-big" id="qrScanInput"
                placeholder="Scan atau ketik kode... (SP-XXXX-XXXXXXXX)"
                autocomplete="off"
                oninput="prosesQRInput(this.value)">
            </div>

            <div style="font-size:0.78rem;color:var(--text-muted);text-align:center;margin-top:0.5rem">
              Format kode: <strong style="color:var(--primary)">SP-XXXX-XXXXXXXX</strong>
            </div>

            <button class="btn btn-primary" onclick="prosesQRKeluar()" style="margin-top:0.5rem">
              🔍 PROSES KELUAR
            </button>
            <button class="btn" style="background:transparent;border:1px solid var(--border);color:var(--text-muted)"
              onclick="resetScan()">↺ Reset</button>
          </div>

          <!-- KANAN: Hasil Scan -->
          <div class="scan-result-box">
            <div class="scan-result-empty" id="scanEmpty">
              <div class="scan-result-icon">📷</div>
              <div>Scan QR Code atau masukkan kode tiket</div>
              <div style="margin-top:8px;font-size:0.75rem">Informasi kendaraan akan muncul di sini</div>
            </div>
            <div id="scanResult" style="display:none"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ===================== TAB: RIWAYAT ===================== -->
  <div class="tab-content" id="tab-riwayat">
    <div class="container">
      <div class="panel">
        <div class="panel-header">
          <div class="panel-icon">📜</div>
          <div class="panel-title">Riwayat Transaksi</div>
        </div>
        <div style="overflow-x:auto;max-height:600px;overflow-y:auto;">
          <table class="data-table">
            <thead>
              <tr>
                <th>Waktu Keluar</th>
                <th>Plat</th>
                <th>Jenis</th>
                <th>Nama</th>
                <th>Slot</th>
                <th>Durasi</th>
                <th>Biaya</th>
                <th>Petugas</th>
                <th>Kode QR</th>
              </tr>
            </thead>
            <tbody id="riwayatBody">
              <tr>
                <td colspan="9" class="empty-state">Belum ada riwayat transaksi</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php if ($userRole === 'admin'): ?>
  <!-- ===================== TAB: KELOLA SLOT ===================== -->
  <div class="tab-content" id="tab-kelola-slot">
    <div class="container">
      <div class="main-grid" style="grid-template-columns: 350px 1fr;">
        <!-- TAMBAH SLOT FORM -->
        <div class="panel">
          <div class="panel-header">
            <div class="panel-icon">➕</div>
            <div class="panel-title">Tambah Slot Parkir</div>
          </div>
          <div class="panel-body">
            <form id="addSlotForm" onsubmit="tambahSlot(event)">
              <div class="form-group">
                <label class="form-label">Kode Slot Baru</label>
                <input type="text" id="newSlotKode" class="form-input" placeholder="Contoh: C01, C02" required style="text-transform: uppercase;">
              </div>
              <button type="submit" class="btn btn-primary">➕ Tambah Slot</button>
            </form>
          </div>
        </div>
        <!-- DAFTAR SLOT TABLE -->
        <div class="panel">
          <div class="panel-header">
            <div class="panel-icon">⚙️</div>
            <div class="panel-title">Daftar & Hapus Slot</div>
          </div>
          <div style="overflow-x:auto;max-height:500px;overflow-y:auto;">
            <table class="data-table">
              <thead>
                <tr>
                  <th>ID Slot</th>
                  <th>Kode Slot</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="slotManageBody">
                <tr>
                  <td colspan="4" class="empty-state">Loading data slot...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ===================== TAB: KELOLA USER ===================== -->
  <div class="tab-content" id="tab-kelola-user">
    <div class="container">
      <div class="main-grid" style="grid-template-columns: 380px 1fr;">
        <!-- FORM USER (TAMBAH / EDIT) -->
        <div class="panel">
          <div class="panel-header">
            <div class="panel-icon">👤</div>
            <div class="panel-title" id="userFormTitle">Tambah User Baru</div>
          </div>
          <div class="panel-body">
            <form id="userForm" onsubmit="simpanUser(event)">
              <input type="hidden" id="userId" value="0">
              <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" id="formUserNama" class="form-input" placeholder="Nama Lengkap..." required>
              </div>
              <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" id="formUserUsername" class="form-input" placeholder="Username..." required>
              </div>
              <div class="form-group">
                <label class="form-label" id="userPasswordLabel">Password</label>
                <input type="password" id="userPassword" class="form-input" placeholder="Password...">
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px" id="userPasswordHelp">Wajib diisi untuk user baru.</div>
              </div>
              <div class="form-group">
                <label class="form-label">Role</label>
                <select id="userRole" class="form-select">
                  <option value="petugas">Petugas</option>
                  <option value="admin">Admin</option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary" id="btnUserSubmit">💾 Simpan User</button>
              <button type="button" class="btn" id="btnUserCancel" style="background:transparent;border:1px solid var(--border);color:var(--text-muted);display:none;margin-top:0.5rem" onclick="resetUserForm()">Batal Edit</button>
            </form>
          </div>
        </div>
        <!-- LIST USER TABLE -->
        <div class="panel">
          <div class="panel-header">
            <div class="panel-icon">📋</div>
            <div class="panel-title">Daftar Akun User</div>
          </div>
          <div style="overflow-x:auto;max-height:500px;overflow-y:auto;">
            <table class="data-table">
              <thead>
                <tr>
                  <th>ID User</th>
                  <th>Nama</th>
                  <th>Username</th>
                  <th>Role</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="userManageBody">
                <tr>
                  <td colspan="5" class="empty-state">Loading data user...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ===================== TAB: LAPORAN HARIAN ===================== -->
  <div class="tab-content" id="tab-laporan-inception">
    <div class="container">

      <!-- Control Bar (no-print) -->
      <div class="panel no-print" style="margin-bottom:1.5rem;">
        <div class="panel-header" style="justify-content:space-between; flex-wrap:wrap; gap:0.75rem;">
          <div style="display:flex;gap:10px;align-items:center;">
            <div class="panel-icon">📊</div>
            <div class="panel-title">Rekap Laporan Harian Parkir</div>
          </div>
          <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:8px;">
              <label style="font-size:0.78rem;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase;">Tanggal:</label>
              <input type="date" id="rekapTanggal" class="form-input" style="width:auto;padding:0.4rem 0.75rem;font-size:0.9rem;" oninput="loadRekap()">
            </div>
            <button class="btn btn-primary" onclick="loadRekap()" style="margin:0;width:auto;padding:0.5rem 1.25rem;font-size:0.8rem;">🔄 Muat Data</button>
            <button class="btn btn-success" onclick="window.print()" style="margin:0;width:auto;padding:0.5rem 1.25rem;font-size:0.8rem;">🖨️ CETAK (A4)</button>
          </div>
        </div>
      </div>

      <!-- ===== PRINT AREA: semua elemen di bawah ini yang akan dicetak ===== -->
      <div id="rekapPrintArea">

        <!-- PRINT HEADER (hanya muncul saat cetak) -->
        <div class="rekap-print-header">
          <div class="rekap-print-logo">HITAMIYOPARKING</div>
          <div class="rekap-print-sub">Laporan Rekap Harian</div>
          <div class="rekap-print-tgl" id="rekapTanggalPrint"></div>
          <div class="rekap-print-tgl" style="font-size:0.8rem;color:#666;" id="rekapCetakTime"></div>
        </div>

        <!-- RINGKASAN UTAMA -->
        <div class="rekap-summary-grid">
          <div class="rekap-sum-card rekap-blue">
            <div class="rekap-sum-icon">💰</div>
            <div class="rekap-sum-label">Total Pendapatan</div>
            <div class="rekap-sum-val" id="sumPendapatan">--</div>
          </div>
          <div class="rekap-sum-card rekap-green">
            <div class="rekap-sum-icon">🚘</div>
            <div class="rekap-sum-label">Kendaraan Masuk Hari Ini</div>
            <div class="rekap-sum-val" id="sumMasuk">--</div>
          </div>
          <div class="rekap-sum-card rekap-purple">
            <div class="rekap-sum-icon">✅</div>
            <div class="rekap-sum-label">Transaksi Selesai</div>
            <div class="rekap-sum-val" id="sumTransaksi">--</div>
          </div>
        </div>

        <!-- DETAIL KEUANGAN PER JENIS -->
        <div class="rekap-card" style="margin-bottom:1.5rem;">
          <div class="rekap-card-header">
            <span>💵 Rincian Keuangan per Jenis Kendaraan</span>
          </div>
          <div class="rekap-card-body">
            <table class="rekap-table">
              <thead>
                <tr>
                  <th>Jenis</th>
                  <th>Jml Kendaraan</th>
                  <th>Total Pendapatan</th>
                </tr>
              </thead>
              <tbody id="rekapKeuanganBody">
                <tr><td colspan="3" style="text-align:center;color:var(--text-muted);">Memuat data...</td></tr>
              </tbody>
              <tfoot id="rekapKeuanganFoot">
              </tfoot>
            </table>
          </div>
        </div>

        <!-- TABEL: Transaksi Selesai Hari Ini -->
        <div class="rekap-card" style="margin-top:1.5rem;">
          <div class="rekap-card-header">
            <span>📋 Detail Transaksi Keluar</span>
            <span class="rekap-badge-count" id="transaksiCount">0</span>
          </div>
          <div class="rekap-card-body" style="padding:0;">
            <div style="overflow-x:auto;">
              <table class="rekap-table rekap-table-full">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Slot</th>
                    <th>Plat</th>
                    <th>Jenis</th>
                    <th>Nama</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Durasi</th>
                    <th>Bayar</th>
                    <th>Metode</th>
                    <th>Petugas</th>
                  </tr>
                </thead>
                <tbody id="rekapTransaksiBody">
                  <tr><td colspan="11" style="text-align:center;color:var(--text-muted);">Memuat data...</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- TANDA TANGAN (hanya cetak) -->
        <div class="rekap-ttd">
          <div class="rekap-ttd-box">
            <div>Mengetahui,</div>
            <div class="rekap-ttd-title">Pimpinan / Admin</div>
            <div class="rekap-ttd-space"></div>
            <div>( _________________________ )</div>
          </div>
          <div class="rekap-ttd-box">
            <div>Dibuat oleh,</div>
            <div class="rekap-ttd-title">Petugas Jaga</div>
            <div class="rekap-ttd-space"></div>
            <div>( _________________________ )</div>
          </div>
        </div>

      </div><!-- end rekapPrintArea -->
    </div>
  </div>
  <?php endif; ?>

  <div class="tab-content" id="tab-kendaraan">
    <div class="container">
      <!-- Filter & Search Bar -->
      <div class="panel" style="margin-bottom:1rem">
        <div class="panel-header">
          <div class="panel-icon">🚘</div>
          <div class="panel-title">Data Kendaraan — Terhubung ke Database</div>
          <div style="margin-left:auto;display:flex;gap:0.5rem;align-items:center">
            <select id="kendaraanFilterStatus" class="form-select" style="width:auto;padding:0.4rem 0.75rem;font-size:0.85rem" onchange="refreshKendaraan()">
              <option value="">Semua Status</option>
              <option value="Masuk">Sedang Parkir</option>
              <option value="Selesai">Sudah Keluar</option>
            </select>
            <button class="btn btn-primary" onclick="refreshKendaraan()" style="width:auto;padding:0.4rem 1rem;margin:0;font-size:0.78rem">🔄 Refresh</button>
          </div>
        </div>
        <div class="search-bar">
          <input class="search-input" id="kendaraanSearch" placeholder="🔍  Cari plat, nama pemilik, atau kode slot..." oninput="filterKendaraanTable()">
        </div>
        <div style="overflow-x:auto;max-height:580px;overflow-y:auto;">
          <table class="data-table" id="kendaraanTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Plat Kendaraan</th>
                <th>Jenis</th>
                <th>Nama Pemilik</th>
                <th>Slot</th>
                <th>Waktu Masuk</th>
                <th>Waktu Keluar</th>
                <th>Status</th>
                <th>Petugas</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="kendaraanBody">
              <tr>
                <td colspan="10" class="empty-state">Loading data kendaraan...</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div style="padding:0.75rem 1.25rem;border-top:1px solid var(--border);font-size:0.8rem;color:var(--text-muted);display:flex;justify-content:space-between;align-items:center">
          <span id="kendaraanCount">Memuat data...</span>
          <span style="color:var(--primary);font-size:0.75rem">⚡ Klik tombol Edit untuk mengubah data langsung ke database</span>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL EDIT KENDARAAN -->
  <div class="modal-overlay" id="modalEditKendaraan">
    <div class="modal modal-wide">
      <div class="modal-header">
        <div class="modal-title">✏️ EDIT DATA KENDARAAN</div>
        <button class="modal-close" onclick="tutupModalEditKendaraan()">✕</button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editKendaraanId">
        <div style="margin-bottom:1rem;padding:0.75rem;background:rgba(0,245,255,0.05);border:1px solid var(--border);border-radius:8px;font-size:0.82rem;color:var(--text-muted)">
          ⚡ Perubahan akan langsung tersimpan ke database saat Anda klik <strong style="color:var(--success)">Simpan Perubahan</strong>.
        </div>
        <div class="form-group">
          <label class="form-label">Nomor Plat Kendaraan</label>
          <input type="text" id="editKendaraanPlat" class="form-input" placeholder="Contoh: B 1234 ABC" style="text-transform:uppercase">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Jenis Kendaraan</label>
            <select id="editKendaraanJenis" class="form-select">
              <option value="Motor">Motor</option>
              <option value="Mobil">Mobil</option>
              <option value="Truk">Truk</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Nama Pemilik</label>
            <input type="text" id="editKendaraanNama" class="form-input" placeholder="Nama pemilik kendaraan">
          </div>
        </div>
        <div style="display:flex;gap:0.75rem;margin-top:0.5rem">
          <button class="btn btn-success" onclick="simpanEditKendaraan()" style="margin-bottom:0">💾 Simpan Perubahan ke Database</button>
          <button class="btn" onclick="tutupModalEditKendaraan()" style="background:transparent;border:1px solid var(--border);color:var(--text-muted);margin-bottom:0">Batal</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL QR TICKET -->
  <div class="modal-overlay" id="modalQR">
    <div class="modal modal-wide">
      <div class="modal-header">
        <div class="modal-title">🎫 TIKET PARKIR - QR CODE</div>
        <button class="modal-close" onclick="tutupModalQR()">✕</button>
      </div>
      <div class="modal-body" id="modalQRBody"></div>
    </div>
  </div>

  <!-- MODAL STRUK KELUAR -->
  <div class="modal-overlay" id="modalStruk">
    <div class="modal">
      <div class="modal-header">
        <div class="modal-title">🧾 STRUK PARKIR</div>
        <button class="modal-close" onclick="tutupModalStruk()">✕</button>
      </div>
      <div class="modal-body" id="modalStrukBody"></div>
    </div>
  </div>

  <!-- TOAST -->
  <div class="toast-container" id="toastContainer"></div>

  <script>
    // ============ DATA STATE ============
    let slotsData = [];
    let riwayatData = [];
    let usersData = [];
    let totalPendapatan = 0;
    
    let selectedSlotId = null;
    let filterMode = 'semua';
    let currentTab = 'dashboard';
    
    const isUserAdmin = <?= $userRole === 'admin' ? 'true' : 'false' ?>;

    // ============ UTILS ============
    function escapeHTML(str) {
      if (!str) return '';
      return String(str).replace(/[&<>'"]/g, 
        tag => ({
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          "'": '&#39;',
          '"': '&quot;'
        }[tag] || tag)
      );
    }

    function updateClock() {
      const now = new Date();
      document.getElementById('clockText').textContent = now.toLocaleTimeString('id-ID');
      document.getElementById('dateText').textContent = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    }
    setInterval(updateClock, 1000); updateClock();

    // ============ TABS ============
    let html5QrcodeScanner = null;
    let cameraRunning = false;

    function switchTab(name) {
      // Matikan kamera jika berpindah dari tab qrscan
      if (currentTab === 'qrscan' && name !== 'qrscan') {
        stopCamera();
      }

      document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));

      const tabEl = document.getElementById('tab-' + name);
      if (tabEl) tabEl.classList.add('active');

      // Menangani active class pada tab button secara robust
      const clickedBtn = Array.from(document.querySelectorAll('.nav-tab')).find(b => b.getAttribute('onclick')?.includes(`'${name}'`));
      if (clickedBtn) clickedBtn.classList.add('active');

      currentTab = name;
      if (name === 'riwayat') renderRiwayat();
      if (name === 'kelola-slot') renderSlotManage();
      if (name === 'kelola-user') renderUserManage();
      if (name === 'laporan-inception') updateLaporanStats();

      // Auto-fokus input QR ketika masuk tab scan
      if (name === 'qrscan') {
        setTimeout(() => {
          const inp = document.getElementById('qrScanInput');
          if (inp) inp.focus();
          startCamera();
        }, 200);
      }
    }

    // ============ CAMERA QR SCANNER ============
    function startCamera() {
      if (cameraRunning) return;

      const wrapper = document.getElementById('qrReaderWrapper');
      const btnStart = document.getElementById('btnStartCamera');
      const btnStop = document.getElementById('btnStopCamera');

      wrapper.style.display = 'block';
      btnStart.style.display = 'none';
      btnStop.style.display = 'block';

      html5QrcodeScanner = new Html5Qrcode('qrReader');

      Html5Qrcode.getCameras()
        .then(cameras => {
          if (!cameras || cameras.length === 0) {
            toast('Kamera tidak ditemukan pada perangkat ini.', 'danger');
            stopCamera();
            return;
          }
          // Gunakan kamera belakang jika ada, atau kamera pertama
          const cameraId = cameras.find(c => c.label.toLowerCase().includes('back'))?.id || cameras[0].id;

          return html5QrcodeScanner.start(
            cameraId,
            { fps: 10, qrbox: { width: 240, height: 240 } },
            (decodedText) => {
              // QR berhasil terbaca
              const kode = decodedText.trim().toUpperCase().split('|')[0];
              document.getElementById('qrScanInput').value = kode;
              stopCamera();
              prosesQRKeluar();
            },
            () => { /* scanning frame - abaikan error kecil */ }
          );
        })
        .then(() => { cameraRunning = true; })
        .catch(err => {
          console.error('Kamera error:', err);
          toast('Gagal mengakses kamera: ' + err, 'danger');
          stopCamera();
        });
    }

    function stopCamera() {
      const wrapper = document.getElementById('qrReaderWrapper');
      const btnStart = document.getElementById('btnStartCamera');
      const btnStop = document.getElementById('btnStopCamera');

      if (html5QrcodeScanner && cameraRunning) {
        html5QrcodeScanner.stop().catch(() => {});
        html5QrcodeScanner = null;
      }
      cameraRunning = false;

      if (wrapper) wrapper.style.display = 'none';
      if (btnStart) btnStart.style.display = 'block';
      if (btnStop) btnStop.style.display = 'none';
    }

    // ============ LOGOUT ============
    function handleLogout() {
      fetch('api.php?action=logout')
        .then(res => res.json())
        .then(res => {
          if (res.status === 'success') {
            window.location.reload();
          }
        })
        .catch(err => console.error(err));
    }

    // ============ DATA SYNCHRONIZATION (AJAX) ============
    function refreshData() {
      // Fetch data slot
      fetch('api.php?action=get_slots')
        .then(res => {
          if (res.status === 401) {
            window.location.reload();
            return;
          }
          return res.json();
        })
        .then(res => {
          if (res && res.status === 'success') {
            slotsData = res.data;
            renderSlot();
            renderTable();
            if (isUserAdmin) renderSlotManage();
            updateLaporanStats();
          } else if (res) {
            console.error('Gagal mengambil data slot:', res.message);
          }
        })
        .catch(err => console.error('Koneksi API Error:', err));

      // Fetch data riwayat & revenue
      fetch('api.php?action=get_riwayat')
        .then(res => {
          if (res.status === 401) {
            window.location.reload();
            return;
          }
          return res.json();
        })
        .then(res => {
          if (res && res.status === 'success') {
            riwayatData = res.data;
            totalPendapatan = res.revenue_today;
            updateStats();
            if (currentTab === 'riwayat') renderRiwayat();
            updateLaporanStats();
          }
        })
        .catch(err => console.error('Koneksi API Error:', err));

      // Fetch data users (jika admin)
      if (isUserAdmin) {
        fetch('api.php?action=get_users')
          .then(res => {
            if (res.status === 401) {
              window.location.reload();
              return;
            }
            return res.json();
          })
          .then(res => {
            if (res && res.status === 'success') {
              usersData = res.data;
              renderUserManage();
              updateLaporanStats();
            }
          })
          .catch(err => console.error('Koneksi API Error:', err));
      }
    }

    // ============ STATS ============
    function updateStats() {
      const total = slotsData.length;
      const occupied = slotsData.filter(s => s.slot_status === 'Terisi').length;
      const available = total - occupied;

      document.getElementById('statTotal').textContent = total;
      document.getElementById('statAvail').textContent = available;
      document.getElementById('statOcc').textContent = occupied;
      document.getElementById('statRev').textContent = totalPendapatan.toLocaleString('id-ID');
    }

    // ============ SLOT RENDER ============
    function renderSlot() {
      const grid = document.getElementById('slotGrid');
      grid.innerHTML = '';
      
      slotsData.forEach(slot => {
        const isOccupied = slot.slot_status === 'Terisi';
        let state = isOccupied ? 'occupied' : 'available';
        
        // Tandai jika sedang dipilih di form
        if (slot.id_slot === selectedSlotId && !isOccupied) {
          state = 'selected';
        }
        
        // Filter slot kosong/terisi/semua
        if (filterMode !== 'semua' && state !== filterMode && !(state === 'selected' && filterMode === 'available')) {
          return;
        }

        const div = document.createElement('div');
        div.className = `slot-item ${state}`;
        
        let emoji = '—';
        if (isOccupied) {
          emoji = slot.jenis === 'Motor' ? '🏍' : slot.jenis === 'Mobil' ? '🚗' : '🚛';
        }

        div.innerHTML = `<div class="slot-num">${slot.kode_slot}</div><div style="font-size:1.1rem;margin-top:2px">${emoji}</div>`;
        
        if (!isOccupied) {
          div.onclick = () => pilihSlot(slot.id_slot, slot.kode_slot);
        } else {
          div.title = `${slot.plat} | ${slot.jenis} | ${slot.nama}`;
        }
        grid.appendChild(div);
      });
      updateStats();
    }

    function filterSlot(mode, btn) {
      filterMode = mode;
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      renderSlot();
    }

    function pilihSlot(id, kode) {
      selectedSlotId = id;
      document.getElementById('inputSlot').value = `Slot ${kode}`;
      renderSlot();
    }

    // ============ VEHICLE ENTRY ============
    function kendaraanMasuk() {
      const plat = document.getElementById('inputPlat').value.trim().toUpperCase();
      const jenis = document.getElementById('inputJenis').value;
      const nama = document.getElementById('inputNama').value.trim() || 'Tidak diketahui';

      if (!plat) { 
        toast('Nomor plat tidak boleh kosong!', 'danger'); 
        return; 
      }
      if (!selectedSlotId) { 
        toast('Pilih slot parkir terlebih dahulu!', 'danger'); 
        return; 
      }

      const formData = new FormData();
      formData.append('id_slot', selectedSlotId);
      formData.append('plat', plat);
      formData.append('jenis', jenis);
      formData.append('nama', nama);

      fetch('api.php?action=masuk', {
        method: 'POST',
        body: formData
      })
      .then(res => {
        if (res.status === 401) {
          window.location.reload();
          return;
        }
        return res.json();
      })
      .then(res => {
        if (res && res.status === 'success') {
          toast(`✅ ${plat} berhasil parkir!`, 'success');
          
          // Dapatkan detail slot kode untuk dicetak
          const targetSlot = slotsData.find(s => s.id_slot == selectedSlotId);
          const fullData = {
            ...res.data,
            kode_slot: targetSlot ? targetSlot.kode_slot : '--'
          };
          
          // Tampilkan tiket QR modal
          tampilkanQR(fullData);

          // Reset Form
          selectedSlotId = null;
          document.getElementById('inputPlat').value = '';
          document.getElementById('inputNama').value = '';
          document.getElementById('inputSlot').value = '';
          
          // Sync data dari database
          refreshData();
        } else if (res) {
          toast(res.message, 'danger');
        }
      })
      .catch(err => {
        console.error(err);
        toast('Gagal menghubungi server.', 'danger');
      });
    }

    // ============ QR TICKET DISPLAY & PRINT ============
    function tampilkanQR(d) {
      if (!d) return;

      const body = document.getElementById('modalQRBody');
      const wMasuk = new Date(d.waktu_masuk);
      
      body.innerHTML = `
        <div class="ticket-header">
          <div class="ticket-title">🅿 HITAMIYOPARKING</div>
          <div class="ticket-sub">Tiket Parkir Resmi — Simpan QR ini</div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;align-items:start">
          <div>
            <div class="qr-wrapper">
              <div class="qr-label">QR CODE TIKET</div>
              <div class="qr-box" id="qrCanvas"></div>
              <div class="qr-id">${d.qr_code}</div>
            </div>
            <button class="btn btn-success" onclick="printQR('${d.qr_code}')">🖨️ CETAK TIKET</button>
          </div>
          <div>
            <div class="receipt-row"><span class="receipt-label">Slot</span><span class="receipt-value" style="font-family:'Orbitron',monospace;color:var(--primary)">S-${d.kode_slot}</span></div>
            <div class="receipt-row"><span class="receipt-label">No. Plat</span><span class="receipt-value plat">${escapeHTML(d.plat)}</span></div>
            <div class="receipt-row"><span class="receipt-label">Jenis</span><span class="receipt-value">${escapeHTML(d.jenis)}</span></div>
            <div class="receipt-row"><span class="receipt-label">Nama</span><span class="receipt-value">${escapeHTML(d.nama)}</span></div>
            <div class="receipt-row"><span class="receipt-label">Waktu Masuk</span><span class="receipt-value">${wMasuk.toLocaleTimeString('id-ID')}</span></div>
            <div class="receipt-row"><span class="receipt-label">Tanggal</span><span class="receipt-value">${wMasuk.toLocaleDateString('id-ID')}</span></div>
            <div style="margin-top:1rem;padding:0.75rem;background:rgba(0,245,255,0.05);border:1px solid var(--border);border-radius:8px;font-size:0.78rem;color:var(--text-muted);line-height:1.6">
              ⚠️ Scan QR ini saat keluar.<br>Kehilangan tiket dikenakan biaya administrasi.
            </div>
          </div>
        </div>
      `;

      document.getElementById('modalQR').classList.add('open');

      // Generate QR Code menggunakan library
      setTimeout(() => {
        const qrEl = document.getElementById('qrCanvas');
        if (qrEl) {
          qrEl.innerHTML = '';
          new QRCode(qrEl, {
            text: d.qr_code + '|' + d.plat + '|' + d.kode_slot,
            width: 160,
            height: 160,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
          });
        }
      }, 150);
    }

    function tutupModalQR() { 
      document.getElementById('modalQR').classList.remove('open'); 
    }

    function tampilkanQRBySlot(idSlot) {
      const s = slotsData.find(x => x.id_slot == idSlot);
      if (s) {
        tampilkanQR(s);
      }
    }

    function printQR(qrCode) {
      // Cari kendaraan di slotsData atau cari di riwayat jika sudah keluar
      let d = slotsData.find(x => x.qr_code === qrCode);
      if (!d) d = riwayatData.find(x => x.qr_code === qrCode);
      if (!d) return;

      const w = window.open('', '_blank', 'width=400,height=550');
      const qrImgEl = document.querySelector('#qrCanvas img');
      const qrSrc = qrImgEl ? qrImgEl.src : '';
      const wMasuk = new Date(d.waktu_masuk);
      
      w.document.write(`
        <html>
        <head>
          <title>Tiket Parkir - ${d.plat}</title>
          <style>
            body { font-family: monospace; background: white; color: black; padding: 20px; text-align: center; }
            h2 { font-size: 18px; margin-bottom: 4px; }
            .sub { font-size: 11px; color: #666; margin-bottom: 16px; }
            .divider { border-top: 1px dashed #ccc; margin: 12px 0; }
            .row { display: flex; justify-content: space-between; font-size: 13px; margin: 4px 0; }
            .kode { font-size: 14px; font-weight: bold; letter-spacing: 2px; margin-top: 8px; }
            img { width: 160px; height: 160px; }
            .note { font-size: 10px; color: #888; margin-top: 10px; }
          </style>
        </head>
        <body>
          <h2>🅿 HITAMIYOPARKING</h2>
          <div class="sub">Tiket Parkir Resmi</div>
          <div class="divider"></div>
          ${qrSrc ? `<img src="${qrSrc}">` : '<div style="width:160px;height:160px;border:1px solid #ccc;margin:0 auto;display:flex;align-items:center;justify-content:center;font-size:12px;">QR Code</div>'}
          <div class="kode">${d.qr_code}</div>
          <div class="divider"></div>
          <div class="row"><span>Plat</span><span><b>${d.plat}</b></span></div>
          <div class="row"><span>Jenis</span><span>${d.jenis}</span></div>
          <div class="row"><span>Nama</span><span>${d.nama}</span></div>
          <div class="row"><span>Slot</span><span>S-${d.kode_slot}</span></div>
          <div class="row"><span>Masuk</span><span>${wMasuk.toLocaleTimeString('id-ID')}, ${wMasuk.toLocaleDateString('id-ID')}</span></div>
          <div class="divider"></div>
          <div class="note">Scan QR saat keluar. Jangan hilangkan tiket ini.</div>
        </body>
        </html>
      `);
      w.document.close();
      setTimeout(() => w.print(), 300);
    }

    // ============ QR SCAN & EXIT PROCESS ============
    function prosesQRInput(val) {
      val = val.trim().toUpperCase();
      // Auto-detect format QR Code (panjang kode SP-XXXX-XXXXXXXX adalah 13 karakter)
      if (val.length >= 13) {
        prosesQRKeluar();
      }
    }

    function prosesQRKeluar() {
      const raw = document.getElementById('qrScanInput').value.trim().toUpperCase();
      const kode = raw.split('|')[0]; // Memisahkan jika terdapat data pipa tambahan

      if (!kode) {
        toast('Silakan masukkan kode tiket terlebih dahulu', 'danger');
        return;
      }

      fetch(`api.php?action=hitung_keluar&qr_code=${kode}`)
        .then(res => {
          if (res.status === 401) {
            window.location.reload();
            return;
          }
          return res.json();
        })
        .then(res => {
          const resultBox = document.getElementById('scanResult');
          const emptyBox = document.getElementById('scanEmpty');

          emptyBox.style.display = 'none';
          resultBox.style.display = 'block';

          if (res && res.status === 'success') {
            const d = res.data;
            const wMasuk = new Date(d.waktu_masuk);
            
            const h = Math.floor(d.durasi_menit / 60);
            const m = d.durasi_menit % 60;
            const durasiText = `${h > 0 ? h + 'j ' : ''}${m}m (${d.durasi_jam} jam)`;

            resultBox.innerHTML = `
              <div style="margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid var(--border)">
                <div style="color:var(--success);font-family:'Orbitron',monospace;font-size:0.8rem;margin-bottom:4px">✅ KENDARAAN DITEMUKAN</div>
              </div>
              <div class="receipt-row"><span class="receipt-label">Kode</span><span class="receipt-value" style="font-size:0.78rem;color:var(--primary)">${d.qr_code}</span></div>
              <div class="receipt-row"><span class="receipt-label">Slot</span><span class="receipt-value" style="font-family:'Orbitron',monospace;color:var(--primary)">S-${d.kode_slot}</span></div>
              <div class="receipt-row"><span class="receipt-label">Plat</span><span class="plat">${escapeHTML(d.plat)}</span></div>
              <div class="receipt-row"><span class="receipt-label">Jenis</span><span class="receipt-value">${escapeHTML(d.jenis)}</span></div>
              <div class="receipt-row"><span class="receipt-label">Nama</span><span class="receipt-value">${escapeHTML(d.nama)}</span></div>
              <div class="receipt-row"><span class="receipt-label">Masuk</span><span class="receipt-value">${wMasuk.toLocaleTimeString('id-ID')}</span></div>
              <div class="receipt-row"><span class="receipt-label">Durasi</span><span class="receipt-value" style="color:var(--warning)">${durasiText}</span></div>
              <div style="margin-top:10px;padding:10px;background:rgba(0,255,136,0.05);border:1px solid rgba(0,255,136,0.2);border-radius:8px;text-align:center">
                <div style="font-size:0.7rem;color:var(--text-muted)">TOTAL BIAYA</div>
                <div style="font-family:'Orbitron',monospace;font-size:1.3rem;color:var(--success)">Rp ${d.total_bayar.toLocaleString('id-ID')}</div>
              </div>
              <button class="btn btn-danger" style="margin-top:10px" onclick="konfirmasiKeluar(${d.id_parkir}, ${d.total_bayar})">🚀 KONFIRMASI KELUAR</button>
            `;
          } else if (res) {
            resultBox.innerHTML = `
              <div style="text-align:center;padding:1rem">
                <div style="font-size:2rem;margin-bottom:0.5rem">❌</div>
                <div style="color:var(--danger);font-family:'Orbitron',monospace;font-size:0.85rem">KODE TIDAK DITEMUKAN</div>
                <div style="color:var(--text-muted);font-size:0.8rem;margin-top:8px">${res.message}</div>
              </div>
            `;
          }
        })
        .catch(err => {
          console.error(err);
          toast('Error memproses request keluar.', 'danger');
        });
    }

    function resetScan() {
      document.getElementById('qrScanInput').value = '';
      document.getElementById('scanResult').style.display = 'none';
      document.getElementById('scanEmpty').style.display = 'block';
      // Stop camera if it is running
      stopCamera();
    }

    // ============ EXIT PROCESS FROM TABLE VIEW ============
    function kendaraanKeluar(qrCode) {
      if (!qrCode) return;
      
      fetch(`api.php?action=hitung_keluar&qr_code=${qrCode}`)
        .then(res => {
          if (res.status === 401) {
            window.location.reload();
            return;
          }
          return res.json();
        })
        .then(res => {
          if (res && res.status === 'success') {
            const d = res.data;
            const wMasuk = new Date(d.waktu_masuk);
            const wKeluar = new Date(d.waktu_keluar);
            
            const h = Math.floor(d.durasi_menit / 60);
            const m = d.durasi_menit % 60;
            const durasiText = `${h > 0 ? h + 'j ' : ''}${m}m (${d.durasi_jam} jam)`;

            document.getElementById('modalStrukBody').innerHTML = `
              <div class="ticket-header">
                <div class="ticket-title">🅿 HITAMIYOPARKING</div>
                <div class="ticket-sub">Struk Pembayaran Parkir</div>
              </div>
              <div class="receipt-row"><span class="receipt-label">Kode QR</span><span class="receipt-value" style="font-size:0.78rem;color:var(--primary)">${d.qr_code}</span></div>
              <div class="receipt-row"><span class="receipt-label">No. Plat</span><span class="plat">${d.plat}</span></div>
              <div class="receipt-row"><span class="receipt-label">Jenis</span><span class="receipt-value">${d.jenis}</span></div>
              <div class="receipt-row"><span class="receipt-label">Nama</span><span class="receipt-value">${d.nama}</span></div>
              <div class="receipt-row"><span class="receipt-label">Slot</span><span class="receipt-value" style="font-family:'Orbitron',monospace;color:var(--primary)">S-${d.kode_slot}</span></div>
              <div class="receipt-row"><span class="receipt-label">Masuk</span><span class="receipt-value">${wMasuk.toLocaleTimeString('id-ID')}</span></div>
              <div class="receipt-row"><span class="receipt-label">Keluar</span><span class="receipt-value">${wKeluar.toLocaleTimeString('id-ID')}</span></div>
              <div class="receipt-row"><span class="receipt-label">Durasi</span><span class="receipt-value">${durasiText}</span></div>
              <div class="receipt-row"><span class="receipt-label">Tarif</span><span class="receipt-value">Rp ${d.tarif_per_jam.toLocaleString('id-ID')}/jam</span></div>
              <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border);text-align:center">
                <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:4px">TOTAL BIAYA</div>
                <div class="receipt-total">Rp ${d.total_bayar.toLocaleString('id-ID')}</div>
              </div>
              <button class="btn btn-danger" style="margin-top:1rem" onclick="konfirmasiKeluar(${d.id_parkir}, ${d.total_bayar})">🚀 KONFIRMASI KELUAR</button>
            `;
            document.getElementById('modalStruk').classList.add('open');
          } else if (res) {
            toast(res.message, 'danger');
          }
        })
        .catch(err => {
          console.error(err);
          toast('Error memproses kendaraan keluar.', 'danger');
        });
    }

    function konfirmasiKeluar(idParkir, totalBayar) {
      const formData = new FormData();
      formData.append('id_parkir', idParkir);
      formData.append('total_bayar', totalBayar);
      formData.append('metode_bayar', 'Cash');

      fetch('api.php?action=konfirmasi_keluar', {
        method: 'POST',
        body: formData
      })
      .then(res => {
        if (res.status === 401) {
          window.location.reload();
          return;
        }
        return res.json();
      })
      .then(res => {
        if (res && res.status === 'success') {
          toast(res.message, 'success');
          tutupModalStruk();
          tutupModalQR();
          resetScan();
          
          // Refresh state and components
          refreshData();
        } else if (res) {
          toast(res.message, 'danger');
        }
      })
      .catch(err => {
        console.error(err);
        toast('Gagal menyelesaikan proses keluar.', 'danger');
      });
    }

    function tutupModalStruk() { 
      document.getElementById('modalStruk').classList.remove('open'); 
    }

    // ============ ACTIVE VECHICLE TABLE RENDER ============
    function renderTable() {
      const q = document.getElementById('searchInput').value.toLowerCase();
      const tbody = document.getElementById('tableBody');
      
      const occupied = slotsData.filter(s => s.slot_status === 'Terisi');
      const filtered = occupied.filter(s => 
        (s.plat && s.plat.toLowerCase().includes(q)) || 
        (s.nama && s.nama.toLowerCase().includes(q))
      );

      if (!filtered.length) {
        tbody.innerHTML = `
          <tr>
            <td colspan="8" class="empty-state">
              ${occupied.length ? 'Pencarian tidak ditemukan' : 'Tidak ada kendaraan terparkir'}
            </td>
          </tr>
        `;
        return;
      }

      tbody.innerHTML = filtered.map(s => {
        // Hitung durasi realtime di frontend
        const dur = (() => {
          const ms = new Date() - new Date(s.waktu_masuk);
          const mn = Math.floor(ms / 60000);
          const h = Math.floor(mn / 60);
          const m = mn % 60;
          return h > 0 ? `${h}j ${m}m` : `${m}m`;
        })();
        
        const bj = s.jenis === 'Motor' ? 'badge-motor' : s.jenis === 'Mobil' ? 'badge-mobil' : 'badge-truk';
        const wMasuk = new Date(s.waktu_masuk);

        return `
          <tr>
            <td><span style="font-family:'Orbitron',monospace;font-size:0.8rem;color:var(--primary)">S-${s.kode_slot}</span></td>
            <td><span class="plat">${escapeHTML(s.plat)}</span></td>
            <td><span class="badge ${bj}">${escapeHTML(s.jenis)}</span></td>
            <td>${escapeHTML(s.nama)}</td>
            <td style="color:var(--text-muted);font-size:0.82rem">${wMasuk.toLocaleTimeString('id-ID')}</td>
            <td style="color:var(--warning);font-family:'Orbitron',monospace;font-size:0.78rem">${dur}</td>
            <td style="font-size:0.82rem;color:var(--text-muted)">${escapeHTML(s.nama_petugas || '-')}</td>
            <td><button class="btn-sm btn-sm-qr" onclick="tampilkanQRBySlot(${s.id_slot})">🎫 QR</button></td>
            <td><button class="btn-sm btn-sm-out" onclick="kendaraanKeluar('${s.qr_code}')">Keluar</button></td>
          </tr>
        `;
      }).join('');
    }

    // ============ TRANSACTION HISTORY RENDER ============
    function renderRiwayat() {
      const tbody = document.getElementById('riwayatBody');
      if (!riwayatData.length) {
        tbody.innerHTML = '<tr><td colspan="9" class="empty-state">Belum ada riwayat transaksi hari ini</td></tr>';
        return;
      }

      tbody.innerHTML = riwayatData.map(r => {
        const bj = r.jenis === 'Motor' ? 'badge-motor' : r.jenis === 'Mobil' ? 'badge-mobil' : 'badge-truk';
        const wMasuk = new Date(r.waktu_masuk);
        const wKeluar = new Date(r.waktu_keluar);
        
        // Hitung durasi jam bulat ke atas
        const durJam = Math.max(1, Math.ceil((wKeluar - wMasuk) / 3600000));
        
        return `
          <tr>
            <td style="font-size:0.82rem;color:var(--text-muted)">
              ${wKeluar.toLocaleTimeString('id-ID')}<br>${wKeluar.toLocaleDateString('id-ID')}
            </td>
            <td><span class="plat">${escapeHTML(r.plat)}</span></td>
            <td><span class="badge ${bj}">${escapeHTML(r.jenis)}</span></td>
            <td>${escapeHTML(r.nama)}</td>
            <td style="font-family:'Orbitron',monospace;color:var(--primary)">S-${r.kode_slot}</td>
            <td style="color:var(--warning)">${durJam} jam</td>
            <td style="font-family:'Orbitron',monospace;color:var(--success)">Rp ${parseFloat(r.total_bayar).toLocaleString('id-ID')}</td>
            <td style="font-size:0.82rem;color:var(--text-muted)">${escapeHTML(r.nama_petugas || '-')}</td>
            <td style="font-size:0.75rem;color:var(--text-muted);font-family:'Orbitron',monospace">${r.qr_code}</td>
          </tr>
        `;
      }).join('');
    }

    // ============ SLOT MANAGEMENT ============
    function renderSlotManage() {
      const tbody = document.getElementById('slotManageBody');
      if (!tbody) return;
      
      if (!slotsData.length) {
        tbody.innerHTML = '<tr><td colspan="4" class="empty-state">Tidak ada slot parkir terdaftar.</td></tr>';
        return;
      }
      
      tbody.innerHTML = slotsData.map(s => {
        const isOccupied = s.slot_status === 'Terisi';
        const badgeClass = isOccupied ? 'badge-truk' : 'badge-motor';
        const badgeText = isOccupied ? 'Terisi' : 'Kosong';
        
        return `
          <tr>
            <td>${s.id_slot}</td>
            <td><span style="font-family:'Orbitron',monospace;font-size:0.9rem;color:var(--primary)">S-${s.kode_slot}</span></td>
            <td><span class="badge ${badgeClass}">${badgeText}</span></td>
            <td>
              <button class="btn-sm btn-sm-out" onclick="hapusSlot(${s.id_slot}, '${s.kode_slot}')" ${isOccupied ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : ''}>🗑️ Hapus</button>
            </td>
          </tr>
        `;
      }).join('');
    }

    function tambahSlot(e) {
      e.preventDefault();
      const kode = document.getElementById('newSlotKode').value.trim().toUpperCase();
      if (!kode) return;
      
      const formData = new FormData();
      formData.append('kode_slot', kode);
      
      fetch('api.php?action=add_slot', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(res => {
        if (res.status === 'success') {
          toast(res.message, 'success');
          document.getElementById('newSlotKode').value = '';
          refreshData();
        } else {
          toast(res.message, 'danger');
        }
      })
      .catch(err => console.error(err));
    }

    function hapusSlot(id, kode) {
      if (!confirm(`Apakah Anda yakin ingin menghapus slot ${kode} beserta seluruh riwayat parkirnya? Tindakan ini tidak dapat dibatalkan.`)) {
        return;
      }
      
      const formData = new FormData();
      formData.append('id_slot', id);
      
      fetch('api.php?action=delete_slot', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(res => {
        if (res.status === 'success') {
          toast(res.message, 'success');
          refreshData();
        } else {
          toast(res.message, 'danger');
        }
      })
      .catch(err => console.error(err));
    }

    // ============ USER MANAGEMENT ============
    function renderUserManage() {
      const tbody = document.getElementById('userManageBody');
      if (!tbody) return;
      
      if (!usersData.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="empty-state">Tidak ada user terdaftar.</td></tr>';
        return;
      }
      
      tbody.innerHTML = usersData.map(u => {
        const roleBadge = u.role === 'admin' ? 'badge-mobil' : 'badge-motor';
        return `
          <tr>
            <td>${u.id_user}</td>
            <td><b>${u.nama}</b></td>
            <td><code>${u.username}</code></td>
            <td><span class="badge ${roleBadge}">${u.role.toUpperCase()}</span></td>
            <td>
              <button class="btn-sm btn-sm-qr" onclick="editUser(${u.id_user}, '${u.nama}', '${u.username}', '${u.role}')">✏️ Edit</button>
              <button class="btn-sm btn-sm-out" onclick="hapusUser(${u.id_user}, '${u.nama}')">🗑️ Hapus</button>
            </td>
          </tr>
        `;
      }).join('');
    }

    function simpanUser(e) {
      e.preventDefault();
      const id_user = document.getElementById('userId').value;
      const nama = document.getElementById('formUserNama').value.trim();
      const username = document.getElementById('formUserUsername').value.trim();
      const password = document.getElementById('userPassword').value;
      const role = document.getElementById('userRole').value;
      
      if (id_user === '0' && !password) {
        toast('Password wajib diisi untuk user baru!', 'danger');
        return;
      }
      
      const formData = new FormData();
      formData.append('id_user', id_user);
      formData.append('nama', nama);
      formData.append('username', username);
      formData.append('password', password);
      formData.append('role', role);
      
      fetch('api.php?action=save_user', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(res => {
        if (res.status === 'success') {
          toast(res.message, 'success');
          resetUserForm();
          refreshData();
        } else {
          toast(res.message, 'danger');
        }
      })
      .catch(err => console.error(err));
    }

    function editUser(id, nama, username, role) {
      document.getElementById('userId').value = id;
      document.getElementById('formUserNama').value = nama;
      document.getElementById('formUserUsername').value = username;
      document.getElementById('userRole').value = role;
      document.getElementById('userPassword').value = '';
      
      document.getElementById('userFormTitle').textContent = 'Edit User: ' + username;
      document.getElementById('userPasswordLabel').textContent = 'Password Baru (Kosongkan jika tidak diubah)';
      document.getElementById('userPasswordHelp').textContent = 'Hanya isi jika ingin mengganti password user ini.';
      document.getElementById('btnUserCancel').style.display = 'block';
      document.getElementById('btnUserSubmit').textContent = '💾 Update User';
    }

    function resetUserForm() {
      document.getElementById('userId').value = '0';
      document.getElementById('formUserNama').value = '';
      document.getElementById('formUserUsername').value = '';
      document.getElementById('userPassword').value = '';
      document.getElementById('userRole').value = 'petugas';
      
      document.getElementById('userFormTitle').textContent = 'Tambah User Baru';
      document.getElementById('userPasswordLabel').textContent = 'Password';
      document.getElementById('userPasswordHelp').textContent = 'Wajib diisi untuk user baru.';
      document.getElementById('btnUserCancel').style.display = 'none';
      document.getElementById('btnUserSubmit').textContent = '💾 Simpan User';
    }

    function hapusUser(id, nama) {
      if (!confirm(`Apakah Anda yakin ingin menghapus user ${nama}?`)) {
        return;
      }
      
      const formData = new FormData();
      formData.append('id_user', id);
      
      fetch('api.php?action=delete_user', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(res => {
        if (res.status === 'success') {
          toast(res.message, 'success');
          refreshData();
        } else {
          toast(res.message, 'danger');
        }
      })
      .catch(err => console.error(err));
    }

    // ============ KELOLA KENDARAAN ============
    let kendaraanData = [];

    function refreshKendaraan() {
      const statusFilter = document.getElementById('kendaraanFilterStatus');
      const status = statusFilter ? statusFilter.value : '';
      const url = `api.php?action=get_kendaraan${status ? '&status=' + status : ''}`;

      fetch(url)
        .then(res => {
          if (res.status === 401) { window.location.reload(); return; }
          return res.json();
        })
        .then(res => {
          if (res && res.status === 'success') {
            kendaraanData = res.data;
            renderKendaraanTable(kendaraanData);
          } else if (res) {
            toast(res.message, 'danger');
          }
        })
        .catch(err => console.error('Error load kendaraan:', err));
    }

    function renderKendaraanTable(data) {
      const tbody = document.getElementById('kendaraanBody');
      const countEl = document.getElementById('kendaraanCount');
      if (!tbody) return;

      if (!data || !data.length) {
        tbody.innerHTML = '<tr><td colspan="10" class="empty-state">Tidak ada data kendaraan ditemukan.</td></tr>';
        if (countEl) countEl.textContent = '0 data kendaraan';
        return;
      }

      if (countEl) countEl.textContent = `Menampilkan ${data.length} data kendaraan`;

      const isAdmin = <?= $userRole === 'admin' ? 'true' : 'false' ?>;

      tbody.innerHTML = data.map(k => {
        const statusClass = k.status === 'Masuk' ? 'color:var(--success)' : 'color:var(--text-muted)';
        const statusIcon = k.status === 'Masuk' ? '🟢' : '⬛';
        const bjClass = k.jenis === 'Motor' ? 'badge-motor' : k.jenis === 'Mobil' ? 'badge-mobil' : 'badge-truk';
        const wMasuk = k.waktu_masuk ? new Date(k.waktu_masuk).toLocaleString('id-ID') : '-';
        const wKeluar = k.waktu_keluar ? new Date(k.waktu_keluar).toLocaleString('id-ID') : '<span style="color:var(--text-muted)">Masih Parkir</span>';

        const deleteBtn = isAdmin
          ? `<button class="btn-sm btn-sm-out" onclick="hapusKendaraan(${k.id_parkir}, '${k.plat}')" style="margin-left:4px">🗑️</button>`
          : '';

        return `
          <tr>
            <td style="color:var(--text-muted);font-size:0.8rem">#${k.id_parkir}</td>
            <td><span class="plat">${escapeHTML(k.plat || '-')}</span></td>
            <td><span class="badge ${bjClass}">${escapeHTML(k.jenis || '-')}</span></td>
            <td>${escapeHTML(k.nama || '-')}</td>
            <td style="font-family:'Orbitron',monospace;font-size:0.82rem;color:var(--primary)">S-${k.kode_slot}</td>
            <td style="font-size:0.82rem;color:var(--text-muted)">${wMasuk}</td>
            <td style="font-size:0.82rem">${wKeluar}</td>
            <td><span style="${statusClass};font-size:0.8rem">${statusIcon} ${k.status}</span></td>
            <td style="font-size:0.82rem;color:var(--text-muted)">${escapeHTML(k.nama_petugas || '-')}</td>
            <td>
              <button class="btn-sm btn-sm-qr" onclick="bukaModalEditKendaraan(${k.id_parkir}, '${escapeHTML((k.plat||'').replace(/'/g,"\\'"))}', '${k.jenis||'Motor'}', '${escapeHTML((k.nama||'').replace(/'/g,"\\'"))}')">✏️ Edit</button>
              ${deleteBtn}
            </td>
          </tr>
        `;
      }).join('');
    }

    function filterKendaraanTable() {
      const q = (document.getElementById('kendaraanSearch')?.value || '').toLowerCase();
      if (!q) {
        renderKendaraanTable(kendaraanData);
        return;
      }
      const filtered = kendaraanData.filter(k =>
        (k.plat && k.plat.toLowerCase().includes(q)) ||
        (k.nama && k.nama.toLowerCase().includes(q)) ||
        (k.kode_slot && k.kode_slot.toLowerCase().includes(q)) ||
        (k.jenis && k.jenis.toLowerCase().includes(q))
      );
      renderKendaraanTable(filtered);
    }

    function bukaModalEditKendaraan(id, plat, jenis, nama) {
      document.getElementById('editKendaraanId').value  = id;
      document.getElementById('editKendaraanPlat').value = plat;
      document.getElementById('editKendaraanJenis').value = jenis;
      document.getElementById('editKendaraanNama').value  = nama;
      document.getElementById('modalEditKendaraan').classList.add('open');
    }

    function tutupModalEditKendaraan() {
      document.getElementById('modalEditKendaraan').classList.remove('open');
    }

    function simpanEditKendaraan() {
      const id   = document.getElementById('editKendaraanId').value;
      const plat = document.getElementById('editKendaraanPlat').value.trim().toUpperCase();
      const jenis = document.getElementById('editKendaraanJenis').value;
      const nama  = document.getElementById('editKendaraanNama').value.trim();

      if (!plat) { toast('Nomor plat tidak boleh kosong!', 'danger'); return; }

      const formData = new FormData();
      formData.append('id_parkir', id);
      formData.append('plat', plat);
      formData.append('jenis', jenis);
      formData.append('nama', nama);

      fetch('api.php?action=update_kendaraan', {
        method: 'POST',
        body: formData
      })
      .then(res => {
        if (res.status === 401) { window.location.reload(); return; }
        return res.json();
      })
      .then(res => {
        if (res && res.status === 'success') {
          toast(res.message, 'success');
          tutupModalEditKendaraan();
          refreshKendaraan();
          refreshData(); // Sync dashboard juga
        } else if (res) {
          toast(res.message, 'danger');
        }
      })
      .catch(err => { console.error(err); toast('Gagal menghubungi server.', 'danger'); });
    }

    function hapusKendaraan(id, plat) {
      if (!confirm(`Hapus data kendaraan plat "${plat}"? Transaksi terkait juga akan dihapus.`)) return;

      const formData = new FormData();
      formData.append('id_parkir', id);

      fetch('api.php?action=delete_kendaraan', {
        method: 'POST',
        body: formData
      })
      .then(res => {
        if (res.status === 401) { window.location.reload(); return; }
        return res.json();
      })
      .then(res => {
        if (res && res.status === 'success') {
          toast(res.message, 'success');
          refreshKendaraan();
          refreshData();
        } else if (res) {
          toast(res.message, 'danger');
        }
      })
      .catch(err => { console.error(err); toast('Gagal menghubungi server.', 'danger'); });
    }

    // ============ REKAP HARIAN HANDLERS ============
    function formatRp(val) {
      return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
    }

    function formatDuration(waktuMasuk, waktuKeluar = null) {
      const masuk = new Date(waktuMasuk);
      const keluar = waktuKeluar ? new Date(waktuKeluar) : new Date();
      const diffMs = keluar - masuk;
      if (isNaN(diffMs) || diffMs < 0) return '-';
      const hrs = Math.floor(diffMs / 3600000);
      const mins = Math.floor((diffMs % 3600000) / 60000);
      return hrs > 0 ? `${hrs} jam ${mins} mnt` : `${mins} mnt`;
    }

    function loadRekap() {
      const tglInput = document.getElementById('rekapTanggal');
      if (tglInput && !tglInput.value) {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        tglInput.value = `${yyyy}-${mm}-${dd}`;
      }
      const tanggal = tglInput ? tglInput.value : '';

      // Set print headers
      if (tanggal) {
        const parts = tanggal.split('-');
        if (parts.length === 3) {
          const formattedTgl = `${parts[2]}-${parts[1]}-${parts[0]}`;
          const tglPrint = document.getElementById('rekapTanggalPrint');
          if (tglPrint) tglPrint.textContent = `TANGGAL LAPORAN: ${formattedTgl}`;
        }
      }
      const now = new Date();
      const printTime = document.getElementById('rekapCetakTime');
      if (printTime) {
        printTime.textContent = `Waktu Cetak: ${now.toLocaleDateString('id-ID')} ${now.toLocaleTimeString('id-ID')}`;
      }

      fetch(`api.php?action=get_rekap_harian&tanggal=${tanggal}`)
        .then(res => {
          if (res.status === 401) { window.location.reload(); return; }
          return res.json();
        })
        .then(res => {
          if (res && res.status === 'success') {
            // Fill summary
            const keu = res.keuangan || { motor_count:0, motor_pendapatan:0, mobil_count:0, mobil_pendapatan:0, truk_count:0, truk_pendapatan:0, total_pendapatan:0, total_transaksi:0 };
            
            const elPendapatan = document.getElementById('sumPendapatan');
            if (elPendapatan) elPendapatan.textContent = formatRp(keu.total_pendapatan);
            const elMasuk = document.getElementById('sumMasuk');
            if (elMasuk) elMasuk.textContent = res.masuk_hari_ini || 0;
            const elTransaksi = document.getElementById('sumTransaksi');
            if (elTransaksi) elTransaksi.textContent = keu.total_transaksi || 0;
            const elAktif = document.getElementById('sumAktif');
            if (elAktif) elAktif.textContent = res.aktif_sekarang || 0;

            // Breakdown Keuangan
            const keuBody = document.getElementById('rekapKeuanganBody');
            if (keuBody) {
              keuBody.innerHTML = `
                <tr>
                  <td>Motor 🏍</td>
                  <td>${keu.motor_count || 0}</td>
                  <td style="font-weight:600;color:var(--primary)">${formatRp(keu.motor_pendapatan)}</td>
                </tr>
                <tr>
                  <td>Mobil 🚗</td>
                  <td>${keu.mobil_count || 0}</td>
                  <td style="font-weight:600;color:var(--primary)">${formatRp(keu.mobil_pendapatan)}</td>
                </tr>
                <tr>
                  <td>Truk 🚛</td>
                  <td>${keu.truk_count || 0}</td>
                  <td style="font-weight:600;color:var(--primary)">${formatRp(keu.truk_pendapatan)}</td>
                </tr>
              `;
            }

            const keuFoot = document.getElementById('rekapKeuanganFoot');
            if (keuFoot) {
              const totalCount = Number(keu.motor_count || 0) + Number(keu.mobil_count || 0) + Number(keu.truk_count || 0);
              keuFoot.innerHTML = `
                <tr style="font-weight: 700; background: rgba(0,245,255,0.05); border-top: 1.5px solid var(--border);">
                  <td>TOTAL</td>
                  <td>${totalCount}</td>
                  <td style="color:var(--success)">${formatRp(keu.total_pendapatan)}</td>
                </tr>
              `;
            }

            // Info Slot & Visualizer (Null-checked)
            const slotDataRes = res.slot || { total_slot: 0, terisi: 0, kosong: 0 };
            const elSlotTotal = document.getElementById('slotTotal');
            if (elSlotTotal) elSlotTotal.textContent = slotDataRes.total_slot;
            const elSlotTerisi = document.getElementById('slotTerisi');
            if (elSlotTerisi) elSlotTerisi.textContent = slotDataRes.terisi;
            const elSlotKosong = document.getElementById('slotKosong');
            if (elSlotKosong) elSlotKosong.textContent = slotDataRes.kosong;

            const slotVis = document.getElementById('rekapSlotVis');
            if (slotVis) {
              if (!slotsData || !slotsData.length) {
                slotVis.innerHTML = '<div style="text-align:center;color:var(--text-muted)">Tidak ada slot terdaftar</div>';
              } else {
                slotVis.innerHTML = slotsData.map(s => {
                  const isOcc = s.slot_status === 'Terisi';
                  const statusClass = isOcc ? 'terisi' : 'kosong';
                  const titleText = `${s.kode_slot}: ${s.slot_status}`;
                  return `<span class="rekap-slot-dot ${statusClass}" title="${titleText}"></span>`;
                }).join('');
              }
            }

            // Tabel Kendaraan Aktif (Null-checked)
            const aktifCount = document.getElementById('aktifCount');
            if (aktifCount) aktifCount.textContent = res.parkir_aktif ? res.parkir_aktif.length : 0;

            const aktifBody = document.getElementById('rekapAktifBody');
            if (aktifBody) {
              if (!res.parkir_aktif || !res.parkir_aktif.length) {
                aktifBody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:1rem;">Tidak ada kendaraan parkir aktif.</td></tr>';
              } else {
                aktifBody.innerHTML = res.parkir_aktif.map((p, idx) => `
                  <tr>
                    <td>${idx + 1}</td>
                    <td style="font-family:'Orbitron',monospace;color:var(--primary);font-size:0.85rem">S-${p.kode_slot}</td>
                    <td><span class="plat">${escapeHTML(p.plat || '-')}</span></td>
                    <td>${escapeHTML(p.jenis || '-')}</td>
                    <td>${escapeHTML(p.nama || '-')}</td>
                    <td style="font-size:0.8rem;color:var(--text-muted)">${new Date(p.waktu_masuk).toLocaleString('id-ID')}</td>
                    <td style="font-family:'Orbitron',monospace;font-size:0.82rem;">${formatDuration(p.waktu_masuk)}</td>
                    <td style="font-size:0.82rem;color:var(--text-muted)">${escapeHTML(p.nama_petugas || '-')}</td>
                  </tr>
                `).join('');
              }
            }

            // Tabel Transaksi Selesai
            const transaksiCount = document.getElementById('transaksiCount');
            if (transaksiCount) transaksiCount.textContent = res.transaksi ? res.transaksi.length : 0;

            const transaksiBody = document.getElementById('rekapTransaksiBody');
            if (transaksiBody) {
              if (!res.transaksi || !res.transaksi.length) {
                transaksiBody.innerHTML = '<tr><td colspan="11" style="text-align:center;color:var(--text-muted);padding:1rem;">Tidak ada transaksi selesai pada tanggal ini.</td></tr>';
              } else {
                transaksiBody.innerHTML = res.transaksi.map((t, idx) => `
                  <tr>
                    <td>${idx + 1}</td>
                    <td style="font-family:'Orbitron',monospace;color:var(--primary);font-size:0.85rem">S-${t.kode_slot}</td>
                    <td><span class="plat">${escapeHTML(t.plat || '-')}</span></td>
                    <td>${escapeHTML(t.jenis || '-')}</td>
                    <td>${escapeHTML(t.nama || '-')}</td>
                    <td style="font-size:0.8rem;color:var(--text-muted)">${new Date(t.waktu_masuk).toLocaleString('id-ID')}</td>
                    <td style="font-size:0.8rem;color:var(--text-muted)">${new Date(t.waktu_keluar).toLocaleString('id-ID')}</td>
                    <td style="font-family:'Orbitron',monospace;font-size:0.82rem;">${formatDuration(t.waktu_masuk, t.waktu_keluar)}</td>
                    <td style="font-weight:600;color:var(--success)">${formatRp(t.total_bayar)}</td>
                    <td style="font-size:0.82rem">${escapeHTML(t.metode_bayar || '-')}</td>
                    <td style="font-size:0.82rem;color:var(--text-muted)">${escapeHTML(t.nama_petugas || '-')}</td>
                  </tr>
                `).join('');
              }
            }
          } else if (res) {
            toast(res.message, 'danger');
          }
        })
        .catch(err => {
          console.error('Error load rekap:', err);
          toast('Gagal memuat rekap harian.', 'danger');
        });
    }

    function updateLaporanStats() {
      if (currentTab === 'laporan-inception') {
        loadRekap();
      }
    }

    // Patch switchTab to also handle 'kendaraan' and report tab
    const _origSwitchTab = switchTab;
    switchTab = function(name) {
      _origSwitchTab(name);
      if (name === 'kendaraan') refreshKendaraan();
      if (name === 'laporan-inception') {
        loadRekap();
      }
    };

    // ============ TOAST NOTIFICATION ============
    function toast(msg, type = 'success') {
      const c = document.getElementById('toastContainer');
      const t = document.createElement('div');
      t.className = `toast ${type}`;
      t.textContent = msg;
      c.appendChild(t);
      setTimeout(() => t.remove(), 4000);
    }

    // ============ INIT ON LOAD ============
    refreshData();
    
    // Interval update realtime
    setInterval(refreshData, 15000); // Sinkronisasi database tiap 15 detik
    setInterval(renderTable, 5000);   // Update counter durasi parkir tiap 5 detik
  </script>
</body>

</html>
