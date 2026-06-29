<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GHURI — Setup Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Outfit', sans-serif;
            background: #0a0a0f;
            color: #e2e8f0;
            min-height: 100vh;
            padding: 24px 16px;
        }

        .container { max-width: 900px; margin: 0 auto; }

        /* Header */
        .header {
            display: flex; align-items: center; gap: 16px;
            margin-bottom: 32px; padding-bottom: 24px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .logo {
            font-weight: 900; font-size: 1.8rem; letter-spacing: -0.04em;
            color: white;
        }
        .logo span { color: #ef4444; }
        .badge {
            background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5; font-size: 0.72rem; font-weight: 700;
            padding: 4px 12px; border-radius: 50px; letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .env-tag {
            margin-left: auto; background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1); border-radius: 8px;
            padding: 6px 14px; font-size: 0.8rem; color: #94a3b8;
        }
        .env-tag strong { color: #e2e8f0; }

        /* Alert boxes */
        .alert {
            border-radius: 12px; padding: 16px 20px; margin-bottom: 24px;
            font-size: 0.9rem; display: flex; align-items: flex-start; gap: 12px;
        }
        .alert-success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.25); color: #6ee7b7; }
        .alert-error   { background: rgba(239,68,68,0.1);  border: 1px solid rgba(239,68,68,0.25);  color: #fca5a5; }
        .alert-icon { font-size: 1.1rem; flex-shrink: 0; margin-top: 1px; }
        .log-list { list-style: none; }
        .log-list li { padding: 3px 0; font-family: 'JetBrains Mono', monospace; font-size: 0.82rem; }

        /* Section title */
        .section-title {
            font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em;
            text-transform: uppercase; color: #64748b; margin-bottom: 14px;
        }

        /* Action cards */
        .actions-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 12px; margin-bottom: 32px;
        }
        .action-card {
            background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);
            border-radius: 14px; padding: 20px 18px; text-decoration: none; color: inherit;
            transition: all 0.22s; display: block; cursor: pointer;
        }
        .action-card:hover {
            background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.14);
            transform: translateY(-2px);
        }
        .action-card.danger:hover { border-color: rgba(239,68,68,0.4); background: rgba(239,68,68,0.06); }
        .action-card.primary:hover { border-color: rgba(99,102,241,0.4); background: rgba(99,102,241,0.06); }
        .action-card.green:hover { border-color: rgba(16,185,129,0.4); background: rgba(16,185,129,0.06); }

        .action-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; margin-bottom: 12px;
        }
        .icon-red    { background: rgba(239,68,68,0.15); }
        .icon-indigo { background: rgba(99,102,241,0.15); }
        .icon-green  { background: rgba(16,185,129,0.15); }
        .icon-blue   { background: rgba(59,130,246,0.15); }
        .icon-amber  { background: rgba(245,158,11,0.15); }

        .action-title { font-weight: 700; font-size: 0.95rem; margin-bottom: 4px; }
        .action-desc  { color: #64748b; font-size: 0.8rem; line-height: 1.5; }

        /* Accounts table */
        .table-wrap {
            background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06);
            border-radius: 14px; overflow: hidden; margin-bottom: 32px;
        }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            text-align: left; padding: 12px 16px;
            font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em;
            text-transform: uppercase; color: #475569;
            background: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        tbody td {
            padding: 12px 16px; font-size: 0.875rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: rgba(255,255,255,0.02); }

        .role-badge {
            display: inline-block; font-size: 0.7rem; font-weight: 700;
            padding: 2px 10px; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.06em;
        }
        .role-admin    { background: rgba(239,68,68,0.15);    color: #fca5a5; }
        .role-manager  { background: rgba(99,102,241,0.15);   color: #a5b4fc; }
        .role-support  { background: rgba(59,130,246,0.15);   color: #93c5fd; }
        .role-ticket   { background: rgba(245,158,11,0.15);   color: #fcd34d; }
        .role-accounts { background: rgba(168,85,247,0.15);   color: #d8b4fe; }
        .role-owner    { background: rgba(16,185,129,0.15);   color: #6ee7b7; }
        .role-customer { background: rgba(255,255,255,0.08);  color: #cbd5e1; }

        .mono { font-family: 'JetBrains Mono', monospace; font-size: 0.82rem; color: #7dd3fc; }
        .pass { font-family: 'JetBrains Mono', monospace; font-size: 0.82rem; color: #86efac; }

        .link-btn {
            display: inline-flex; align-items: center; gap: 4px;
            color: #818cf8; font-size: 0.8rem; text-decoration: none;
            border: 1px solid rgba(99,102,241,0.25); padding: 3px 10px;
            border-radius: 6px; transition: all 0.18s;
        }
        .link-btn:hover { background: rgba(99,102,241,0.12); border-color: rgba(99,102,241,0.5); }

        /* Instructions */
        .instructions {
            background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06);
            border-radius: 14px; padding: 20px 22px;
        }
        .step {
            display: flex; gap: 14px; padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        .step:last-child { border-bottom: none; }
        .step-num {
            width: 26px; height: 26px; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 700; color: #fca5a5; flex-shrink: 0;
        }
        .step-body { font-size: 0.875rem; color: #94a3b8; line-height: 1.55; }
        .step-body strong { color: #e2e8f0; }
        code {
            font-family: 'JetBrains Mono', monospace; font-size: 0.78rem;
            background: rgba(255,255,255,0.07); padding: 2px 7px; border-radius: 5px;
            color: #7dd3fc;
        }

        .warning-box {
            background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2);
            border-radius: 10px; padding: 12px 16px; font-size: 0.82rem; color: #fcd34d;
            margin-top: 20px; display: flex; gap: 10px; align-items: flex-start;
        }
    </style>
</head>
<body>
<div class="container">

    {{-- ── Header ── --}}
    <div class="header">
        <div class="logo">GHURI<span>.</span></div>
        <div class="badge">Setup Panel</div>
        <div class="env-tag">
            ENV: <strong>{{ config('app.env', 'unknown') }}</strong>
            &nbsp;|&nbsp; DB: <strong>{{ config('database.default') }}</strong>
        </div>
    </div>

    {{-- ── Result alerts ── --}}
    @if(!empty($logs))
    <div class="alert alert-success">
        <div class="alert-icon">✅</div>
        <div>
            <strong>Done!</strong>
            <ul class="log-list" style="margin-top:6px;">
                @foreach($logs as $log)
                <li>{{ $log }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    @if($error)
    <div class="alert alert-error">
        <div class="alert-icon">❌</div>
        <div><strong>Error:</strong> {{ $error }}</div>
    </div>
    @endif

    {{-- ── Actions ── --}}
    <div class="section-title">Actions</div>
    <div class="actions-grid">

        <a href="?token={{ $t }}&action=fresh" class="action-card danger"
           onclick="return confirm('⚠️ This will DROP ALL TABLES and reseed. Are you sure?')">
            <div class="action-icon icon-red">🔄</div>
            <div class="action-title">Fresh Setup</div>
            <div class="action-desc">migrate:fresh + DemoSeeder + AirportSeeder.<br><strong style="color:#fca5a5;">Drops all data!</strong></div>
        </a>

        <a href="?token={{ $t }}&action=migrate" class="action-card primary">
            <div class="action-icon icon-indigo">📦</div>
            <div class="action-title">Run Migrations</div>
            <div class="action-desc">Run only pending migrations. Safe — does not touch existing data.</div>
        </a>

        <a href="?token={{ $t }}&action=seed-demo" class="action-card green">
            <div class="action-icon icon-green">👤</div>
            <div class="action-title">Seed Demo Accounts</div>
            <div class="action-desc">Creates all 7 demo accounts (uses firstOrCreate — safe to re-run).</div>
        </a>

        <a href="?token={{ $t }}&action=seed-airports" class="action-card" style="">
            <div class="action-icon icon-blue">✈️</div>
            <div class="action-title">Seed Airports</div>
            <div class="action-desc">Loads all airport data for flight search. Safe to re-run.</div>
        </a>

        <a href="?token={{ $t }}&action=seed-all" class="action-card">
            <div class="action-icon icon-amber">🌱</div>
            <div class="action-title">Seed All</div>
            <div class="action-desc">Runs DemoSeeder + AirportSeeder. Does not drop tables.</div>
        </a>

    </div>

    {{-- ── Demo Accounts ── --}}
    <div class="section-title">Demo Accounts</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Login</th>
                </tr>
            </thead>
            <tbody>
                @php
                $roleCss = [
                    'Admin'             => 'role-admin',
                    'Manager'           => 'role-manager',
                    'Support Agent'     => 'role-support',
                    'Ticketing Officer' => 'role-ticket',
                    'Accounts Officer'  => 'role-accounts',
                    'Property Owner'    => 'role-owner',
                    'Customer'          => 'role-customer',
                ];
                @endphp
                @foreach($accounts as [$role, $email, $password, $dash])
                <tr>
                    <td><span class="role-badge {{ $roleCss[$role] ?? '' }}">{{ $role }}</span></td>
                    <td><span class="mono">{{ $email }}</span></td>
                    <td><span class="pass">{{ $password }}</span></td>
                    <td>
                        <a href="{{ $dash }}" class="link-btn" target="_blank">
                            Login →
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ── How to use instructions ── --}}
    <div class="section-title">How to use on Vercel</div>
    <div class="instructions">
        <div class="step">
            <div class="step-num">1</div>
            <div class="step-body">
                In your Vercel project → <strong>Settings → Environment Variables</strong>, add:<br>
                <code>SETUP_TOKEN</code> = any secret string, e.g. <code>ghuri-setup-2024</code>
            </div>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <div class="step-body">
                After every deploy, open your browser and go to:<br>
                <code>https://your-app.vercel.app/setup?token=ghuri-setup-2024</code>
            </div>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <div class="step-body">
                <strong>First time only</strong> — click <strong>"Fresh Setup"</strong> to create all tables and seed everything.<br>
                <strong>After updates</strong> — click <strong>"Run Migrations"</strong> to apply only new changes.
            </div>
        </div>
        <div class="step">
            <div class="step-num">4</div>
            <div class="step-body">
                All demo accounts use <strong>pre-verified email</strong> — no email confirmation required. Log in immediately.
            </div>
        </div>

        <div class="warning-box">
            ⚠️ <div>Keep your <code>SETUP_TOKEN</code> secret. Anyone with this URL can reset your database. Never use "Fresh Setup" on a live site with real data.</div>
        </div>
    </div>

</div>
</body>
</html>
