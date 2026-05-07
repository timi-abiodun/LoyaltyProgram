<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Elite Loyalty | Member Dashboard</title>
  <meta name="color-scheme" content="dark light" />
  <!-- Inter Font for that premium tech feel -->
  <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
  <style>
    :root {
      --bg: #030712;
      --card-bg: rgba(17, 24, 39, 0.7);
      --panel: rgba(255, 255, 255, 0.03);
      --text-main: #f9fafb;
      --text-muted: #9ca3af;
      --border: rgba(255, 255, 255, 0.08);
      --accent: #fbbf24;
      --accent-glow: rgba(251, 191, 36, 0.15);
      --success: #10b981;
      --radius-lg: 24px;
      --radius-md: 16px;
      --font: 'Inter', system-ui, -apple-system, sans-serif;
    }

    @media (prefers-color-scheme: light) {
      :root {
        --bg: #f8fafc;
        --card-bg: rgba(255, 255, 255, 0.8);
        --panel: rgba(0, 0, 0, 0.02);
        --text-main: #111827;
        --text-muted: #6b7280;
        --border: rgba(0, 0, 0, 0.06);
        --accent-glow: rgba(251, 191, 36, 0.1);
      }
    }

    * { box-sizing: border-box; -webkit-font-smoothing: antialiased; }
    body {
      margin: 0;
      font-family: var(--font);
      background: var(--bg);
      color: var(--text-main);
      line-height: 1.5;
      background-attachment: fixed;
      background-image: 
        radial-gradient(at 0% 0%, rgba(251, 191, 36, 0.08) 0px, transparent 50%),
        radial-gradient(at 100% 100%, rgba(245, 48, 3, 0.05) 0px, transparent 50%);
    }

    .container { max-width: 1200px; margin: 0 auto; padding: 40px 24px; }

    /* Header */
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 48px; }
    .brand-group { display: flex; align-items: center; gap: 16px; }
    .logo-mark {
      width: 48px; height: 48px; border-radius: 14px;
      background: linear-gradient(135deg, #fbbf24, #f59e0b);
      box-shadow: 0 0 20px rgba(251, 191, 36, 0.3);
      display: grid; place-items: center; font-weight: 900; color: #000;
    }
    .brand-group h1 { font-size: 24px; margin: 0; font-weight: 800; letter-spacing: -0.025em; }

    /* Layout Grid */
    .dashboard-grid { display: grid; grid-template-columns: 1fr; gap: 24px; }
    @media (min-width: 1024px) {
      .dashboard-grid { grid-template-columns: 380px 1fr; }
    }

    .card {
      background: var(--card-bg);
      backdrop-filter: blur(12px);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 32px;
      box-shadow: 0 10px 30px -10px rgba(0,0,0,0.2);
    }

    .section-title {
      font-size: 12px; text-transform: uppercase; letter-spacing: 0.1em;
      color: var(--text-muted); font-weight: 700; margin-bottom: 24px;
      display: flex; align-items: center; gap: 8px;
    }

    /* Inputs & Buttons */
    input {
      width: 100%; padding: 14px 16px; background: var(--panel);
      border: 1px solid var(--border); border-radius: 12px;
      color: var(--text-main); font-family: inherit; transition: 0.2s; margin-bottom: 16px;
    }
    input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px var(--accent-glow); }

    .btn {
      width: 100%; padding: 14px; border-radius: 12px; border: none;
      font-weight: 600; cursor: pointer; transition: 0.2s; font-family: inherit;
    }
    .btn-primary { background: var(--text-main); color: var(--bg); }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-ghost { background: transparent; border: 1px solid var(--border); color: var(--text-muted); }
    .btn-ghost:hover { border-color: var(--text-muted); color: var(--text-main); }

    /* Stats & KPI */
    .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; margin-bottom: 32px; }
    .kpi-card { padding: 20px; background: var(--panel); border-radius: var(--radius-md); border: 1px solid var(--border); }
    .kpi-val { font-size: 24px; font-weight: 800; margin-top: 4px; }

    /* Rewards / Badge */
    .badge-card {
      background: linear-gradient(145deg, rgba(251, 191, 36, 0.1), transparent);
      border: 1px solid rgba(251, 191, 36, 0.3);
      padding: 24px; border-radius: var(--radius-md); position: relative; overflow: hidden;
    }
    .badge-ring {
      width: 60px; height: 60px; border-radius: 50%;
      background: conic-gradient(var(--accent) var(--p), var(--border) 0);
      display: grid; place-items: center; margin-bottom: 16px;
    }
    .badge-ring::after { content: ''; width: 48px; height: 48px; background: var(--bg); border-radius: 50%; position: absolute; }

    /* The Reward Unlocked Card */
    #cashbackReward {
      display: none;
      background: linear-gradient(135deg, #059669 0%, #10b981 100%);
      color: white; margin-top: 24px; animation: slideUp 0.5s ease-out;
    }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    /* Toasts */
    .toast {
      position: fixed; bottom: 32px; right: 32px; background: #111827; color: white;
      padding: 16px 24px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.4);
      display: flex; align-items: center; gap: 12px; z-index: 100;
      transform: translateY(100px); transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .toast.active { transform: translateY(0); }
    .toast-icon { width: 24px; height: 24px; background: var(--success); border-radius: 50%; }

    .list-item {
      padding: 16px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between;
    }
    .list-item:last-child { border: none; }
  </style>
</head>
<body>

  <div class="container">
    <header class="header">
      <div class="brand-group">
        <div class="logo-mark">L</div>
        <div>
          <h1>Elite Rewards</h1>
          <div id="authStatus" style="font-size: 13px; color: var(--text-muted)">Welcome, Guest</div>
        </div>
      </div>
      <button class="btn btn-ghost" id="btnLogout" style="width: auto; padding: 8px 16px; display:none">Log Out</button>
    </header>

    <div class="dashboard-grid">
      <!-- Action Column -->
      <aside style="display:flex; flex-direction:column; gap:24px">
        <div class="card" id="authSection">
          <div class="section-title">Membership</div>
          <div id="authFormGroup">
            <input type="text" id="username" placeholder="Username">
            <input type="password" id="password" placeholder="Password">
            <button class="btn btn-primary" onclick="handleLogin()">Sign In</button>
            <p style="font-size:12px; color:var(--text-muted); margin-top:16px; text-align:center">Use demo credentials to explore.</p>
          </div>
        </div>

        <div class="card">
          <div class="section-title">Quick Purchase</div>
          <input type="number" id="purchaseAmount" placeholder="Amount (e.g. 500)">
          <button class="btn btn-primary" id="btnPurchase" onclick="handlePurchase()">Complete Purchase</button>
        </div>
      </aside>

      <!-- Main Dashboard -->
      <main>
        <div class="card">
          <div class="section-title">Status Overview</div>
          <div class="kpi-grid">
            <div class="kpi-card">
              <div class="section-title" style="margin:0">Unlocked</div>
              <div class="kpi-val" id="countUnlocked">0</div>
            </div>
            <div class="kpi-card">
              <div class="section-title" style="margin:0">Points Left</div>
              <div class="kpi-val" id="pointsRemaining">—</div>
            </div>
          </div>

          <div id="dashboardContent" style="opacity: 0.5; pointer-events: none;">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px">
              <div>
                <div class="section-title">Current Badge</div>
                <div class="badge-card">
                  <div class="badge-ring" id="progressRing" style="--p: 0%"></div>
                  <div id="badgeName" style="font-weight: 800; font-size: 20px;">Standard</div>
                  <div id="badgeStatus" style="color: var(--text-muted); font-size: 13px;">Member since 2026</div>
                </div>
                
                <!-- Special Reward Card -->
                <div class="card" id="cashbackReward">
                   <div style="font-weight: 800; font-size: 18px;">🎉 Reward Unlocked!</div>
                   <div style="opacity: 0.9; font-size: 14px; margin-top: 4px;">You've earned ₦300.00 Cashback for reaching the Elite tier.</div>
                </div>
              </div>

              <div>
                <div class="section-title">Recent Achievements</div>
                <div id="achievementList" class="kpi-card" style="padding:0">
                  <div class="list-item" style="color:var(--text-muted)">Log in to see progress...</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <div class="toast" id="mainToast">
    <div class="toast-icon"></div>
    <div id="toastText">Action successful</div>
  </div>

  <script>
    const API_BASE = '/api/v1';
    
    function showToast(msg) {
      const t = document.getElementById('mainToast');
      document.getElementById('toastText').textContent = msg;
      t.classList.add('active');
      setTimeout(() => t.classList.remove('active'), 3000);
    }

    async function handleLogin() {
      // Mocked login for UI demonstration
      localStorage.setItem('access_token', 'demo_token');
      showToast('Authentication Successful');
      updateUI();
    }

    async function handlePurchase() {
      const amt = document.getElementById('purchaseAmount').value;
      if(!amt) return alert('Enter amount');
      
      // Mimic API logic
      showToast('Purchase Completed');
      document.getElementById('purchaseAmount').value = '';
      
      // After purchase, we "refresh" and check for the reward
      // In a real app, this would be based on the badge rank returned from API
      checkRewards(amt);
    }

    function checkRewards(amt) {
      // Logic: If user spends enough to trigger the top badge
      if(amt >= 1000) {
        document.getElementById('cashbackReward').style.display = 'block';
        document.getElementById('progressRing').style.setProperty('--p', '100%');
        document.getElementById('badgeName').textContent = 'Elite Gold';
        document.getElementById('pointsRemaining').textContent = 'MAX';
      }
    }

    function updateUI() {
      const authed = localStorage.getItem('access_token');
      if(authed) {
        document.getElementById('authFormGroup').style.display = 'none';
        document.getElementById('btnLogout').style.display = 'block';
        document.getElementById('dashboardContent').style.opacity = '1';
        document.getElementById('dashboardContent').style.pointerEvents = 'auto';
        document.getElementById('authStatus').textContent = 'Premium Member';
        
        // Populate mock data
        document.getElementById('countUnlocked').textContent = '4';
        document.getElementById('pointsRemaining').textContent = '120';
        document.getElementById('achievementList').innerHTML = `
          <div class="list-item"><div>First Purchase</div><div style="color:var(--success)">✓</div></div>
          <div class="list-item"><div>High Roller</div><div style="color:var(--success)">✓</div></div>
        `;
      }
    }

    document.getElementById('btnLogout').onclick = () => {
      localStorage.removeItem('access_token');
      location.reload();
    };

    updateUI();
  </script>
</body>
</html>