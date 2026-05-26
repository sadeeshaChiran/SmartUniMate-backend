<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Smart UniMate - Sabaragamuwa University</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #800000;
            --primary-light: #a01010;
            --secondary: #005A9E;
            --bg: #f5f4f0;
            --surface: #ffffff;
            --surface2: #f9f8f5;
            --border: rgba(0,0,0,0.08);
            --text: #1a1a1a;
            --text-muted: #6b6b6b;
            --success: #2d7a4f;
            --danger: #c0392b;
            --radius: 14px;
            --shadow: 0 2px 16px rgba(0,0,0,0.07);
        }
        body.dark {
            --bg: #0f0f0f;
            --surface: #1a1a1a;
            --surface2: #222222;
            --border: rgba(255,255,255,0.08);
            --text: #f0ede8;
            --text-muted: #888;
            --shadow: 0 2px 16px rgba(0,0,0,0.4);
        }
        /* Color Palettes */
        body.palette-ocean { --primary: #0077b6; --primary-light: #0096c7; --danger: #023e8a; }
        body.palette-forest { --primary: #2d6a4f; --primary-light: #40916c; --danger: #1b4332; }
        body.palette-sunset { --primary: #e76f51; --primary-light: #f4a261; --danger: #e63946; }
        body.palette-purple { --primary: #7b2cbf; --primary-light: #9d4edd; --danger: #5a189a; }
        /* Glass Effect */
        body.glass-mode .card, body.glass-mode header, body.glass-mode aside {
            background: rgba(255,255,255,0.6) !important;
            backdrop-filter: blur(16px) !important;
            -webkit-backdrop-filter: blur(16px) !important;
            border: 1px solid rgba(255,255,255,0.3) !important;
        }
        body.dark.glass-mode .card, body.dark.glass-mode header, body.dark.glass-mode aside {
            background: rgba(30,30,30,0.6) !important;
            border: 1px solid rgba(255,255,255,0.08) !important;
        }
        /* Comment Sections */
        .comment-section { margin-top:12px; border-top:1px solid var(--border); padding-top:12px; }
        .comment-item { display:flex; gap:10px; padding:8px 0; border-bottom:1px solid var(--border); font-size:13px; }
        .comment-item:last-child { border-bottom:none; }
        .comment-input-row { display:flex; gap:8px; margin-top:8px; }
        .comment-input-row input { flex:1; font-size:13px; }
        .comment-input-row button { font-size:12px; white-space:nowrap; }
        /* Setting toggles */
        .setting-row { display:flex; justify-content:space-between; align-items:center; padding:16px 0; border-bottom:1px solid var(--border); }
        .setting-row:last-child { border-bottom:none; }
        .setting-label { font-weight:500; }
        .setting-desc { font-size:12px; color:var(--text-muted); margin-top:2px; }
        .toggle-switch { position:relative; width:44px; height:24px; cursor:pointer; }
        .toggle-switch input { opacity:0; width:0; height:0; }
        .toggle-track { position:absolute; inset:0; background:var(--border); border-radius:12px; transition:0.3s; }
        .toggle-track:after { content:''; position:absolute; width:18px; height:18px; left:3px; top:3px; background:#fff; border-radius:50%; transition:0.3s; }
        .toggle-switch input:checked + .toggle-track { background:var(--primary); }
        .toggle-switch input:checked + .toggle-track:after { transform:translateX(20px); }
        .palette-swatch { width:32px; height:32px; border-radius:50%; cursor:pointer; border:3px solid transparent; transition:0.2s; }
        .palette-swatch:hover, .palette-swatch.active { border-color:var(--text); transform:scale(1.15); }
        .post-image { max-width:100%; max-height:300px; border-radius:8px; margin-top:8px; object-fit:cover; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            transition: background 0.3s, color 0.3s;
            min-height: 100vh;
        }

        /* ── TOPBAR ── */
        header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            color: var(--primary);
            cursor: pointer;
            letter-spacing: -0.3px;
        }
        .topbar-right { display: flex; align-items: center; gap: 6px; }
        .icon-btn {
            background: none; border: 1px solid transparent;
            width: 36px; height: 36px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-muted); cursor: pointer; font-size: 15px;
            transition: 0.15s; position: relative;
        }
        .icon-btn:hover { background: var(--surface2); border-color: var(--border); color: var(--text); }
        .notif-badge {
            position: absolute; top: 4px; right: 4px;
            width: 8px; height: 8px; background: var(--danger);
            border-radius: 50%; border: 1.5px solid var(--surface);
        }
        .auth-btn {
            background: var(--primary); color: #fff; border: none;
            padding: 0 16px; height: 34px; border-radius: 8px;
            font-size: 13px; font-weight: 600; cursor: pointer;
            transition: 0.15s; font-family: inherit;
        }
        .auth-btn:hover { background: var(--primary-light); }

        /* ── LAYOUT ── */
        .app-shell { display: flex; min-height: calc(100vh - 60px); }

        /* ── SIDEBAR ── */
        aside {
            width: 220px; min-width: 220px;
            background: var(--surface);
            border-right: 1px solid var(--border);
            padding: 20px 12px;
            position: sticky;
            top: 60px;
            height: calc(100vh - 60px);
            overflow-y: auto;
        }
        .nav-section-label {
            font-size: 10px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1px;
            color: var(--text-muted); padding: 4px 12px 8px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 9px;
            cursor: pointer; font-size: 14px; font-weight: 500;
            color: var(--text-muted); transition: 0.15s; margin-bottom: 2px;
            border: 1px solid transparent;
        }
        .nav-item:hover { background: var(--surface2); color: var(--text); }
        .nav-item.active { background: #80000012; color: var(--primary); border-color: #80000022; }
        .nav-item i { width: 16px; text-align: center; font-size: 14px; }

        /* ── MAIN ── */
        main { flex: 1; padding: 32px; max-width: 960px; }
        .section { display: none; animation: up 0.25s ease; }
        .section.active { display: block; }
        @keyframes up { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

        /* ── COMPONENTS ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }
        .card-sm { padding: 16px; }
        h2.page-title {
            font-family: 'Playfair Display', serif;
            font-size: 26px; color: var(--primary);
            margin-bottom: 20px; font-weight: 700;
        }
        h3.section-title { font-size: 15px; font-weight: 600; margin-bottom: 16px; }
        .btn {
            padding: 9px 18px; border-radius: 8px; border: none;
            font-family: inherit; font-size: 14px; font-weight: 600;
            cursor: pointer; transition: 0.15s;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-light); }
        .btn-outline {
            background: none; border: 1px solid var(--border);
            color: var(--text); 
        }
        .btn-outline:hover { background: var(--surface2); }
        input, textarea, select {
            width: 100%; padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--surface2); color: var(--text);
            font-family: inherit; font-size: 14px;
            outline: none; transition: 0.15s;
        }
        input:focus, textarea:focus { border-color: var(--primary); }
        label { font-size: 13px; font-weight: 500; color: var(--text-muted); display: block; margin-bottom: 6px; }
        .form-group { margin-bottom: 16px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .stat-card {
            background: var(--surface2); border: 1px solid var(--border);
            border-radius: 10px; padding: 16px; text-align: center;
        }
        .stat-num { font-size: 28px; font-weight: 700; color: var(--primary); }
        .stat-label { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
        .tag {
            display: inline-block; padding: 3px 10px;
            border-radius: 20px; font-size: 12px; font-weight: 600;
        }
        .tag-blue { background: #e6f1fb; color: #185FA5; }
        .tag-green { background: #eaf3de; color: #3B6D11; }
        .tag-red { background: #fcebeb; color: #A32D2D; }
        body.dark .tag-blue { background: #0C447C; color: #B5D4F4; }
        body.dark .tag-green { background: #27500A; color: #C0DD97; }
        body.dark .tag-red { background: #791F1F; color: #F7C1C1; }

        /* ── NOTIFICATION PANEL ── */
        .notif-panel {
            position: fixed; top: 60px; right: 0;
            width: 340px; height: calc(100vh - 60px);
            background: var(--surface);
            border-left: 1px solid var(--border);
            padding: 20px; z-index: 900;
            transform: translateX(100%);
            transition: transform 0.25s ease;
            overflow-y: auto;
        }
        .notif-panel.open { transform: translateX(0); }
        .notif-item {
            display: flex; gap: 12px; padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }
        .notif-item:last-child { border-bottom: none; }
        .notif-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--primary); margin-top: 5px; flex-shrink: 0;
        }
        .notif-dot.read { background: var(--text-muted); }
        .notif-text { font-size: 13px; line-height: 1.5; }
        .notif-time { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

        /* ── CHAT ── */
        .chat-wrap {
            display: flex; flex-direction: column;
            height: 540px; border: 1px solid var(--border);
            border-radius: var(--radius); overflow: hidden;
            background: var(--surface);
        }
        .chat-header {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 10px;
            background: var(--surface2);
        }
        .ai-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--primary); display: flex;
            align-items: center; justify-content: center;
            color: #fff; font-size: 14px;
        }
        .chat-messages {
            flex: 1; overflow-y: auto; padding: 20px;
            display: flex; flex-direction: column; gap: 14px;
            background: var(--bg);
        }
        .msg { max-width: 78%; padding: 11px 15px; border-radius: 12px; font-size: 14px; line-height: 1.5; }
        .msg.bot {
            background: var(--surface); border: 1px solid var(--border);
            align-self: flex-start; border-bottom-left-radius: 3px;
        }
        .msg.user {
            background: var(--primary); color: #fff;
            align-self: flex-end; border-bottom-right-radius: 3px;
        }
        .msg.typing { color: var(--text-muted); font-style: italic; }
        .chat-input-row {
            display: flex; gap: 10px; padding: 14px 16px;
            border-top: 1px solid var(--border); background: var(--surface);
        }
        .chat-input-row input { margin: 0; }

        /* ── TIMETABLE ── */
        .timetable-grid {
            display: grid;
            grid-template-columns: 80px repeat(5, 1fr);
            gap: 2px; font-size: 12px;
        }
        .tt-header {
            background: var(--primary); color: #fff;
            padding: 8px; text-align: center;
            border-radius: 6px; font-weight: 600;
        }
        .tt-time {
            background: var(--surface2);
            padding: 8px; border-radius: 6px;
            text-align: center; color: var(--text-muted);
            font-weight: 500; display: flex; align-items: center; justify-content: center;
        }
        .tt-cell {
            min-height: 52px; border-radius: 6px;
            padding: 6px 8px; cursor: pointer; transition: 0.15s;
        }
        .tt-cell.empty { background: var(--surface2); border: 1px dashed var(--border); }
        .tt-cell.empty:hover { background: var(--surface); border-style: solid; }
        .tt-cell.filled { background: #80000015; border: 1px solid #80000030; }
        .tt-cell.filled:hover { background: #80000025; }
        .tt-cell .mod-code { font-weight: 700; color: var(--primary); font-size: 11px; }
        .tt-cell .mod-room { color: var(--text-muted); font-size: 10px; margin-top: 2px; }

        /* ── GPA ── */
        .gpa-display {
            text-align: center; padding: 32px 0;
        }
        .gpa-ring {
            width: 140px; height: 140px;
            border-radius: 50%; margin: 0 auto 16px;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            background: conic-gradient(var(--primary) 0deg, var(--primary) var(--gpa-deg, 0deg), var(--surface2) var(--gpa-deg, 0deg));
            position: relative;
        }
        .gpa-inner {
            width: 110px; height: 110px; border-radius: 50%;
            background: var(--surface); display: flex;
            flex-direction: column; align-items: center; justify-content: center;
        }
        .gpa-num { font-size: 32px; font-weight: 700; color: var(--primary); }
        .gpa-sub { font-size: 12px; color: var(--text-muted); }
        .module-row {
            display: grid; grid-template-columns: 1fr 100px 100px 80px;
            gap: 10px; align-items: center;
            padding: 10px 0; border-bottom: 1px solid var(--border);
        }
        .module-row:last-child { border-bottom: none; }
        .grade-input { width: 100%; }

        /* ── COMMUNITY ── */
        .post-card {
            padding: 16px 0; border-bottom: 1px solid var(--border);
        }
        .post-card:last-child { border-bottom: none; }
        .post-meta {
            display: flex; justify-content: space-between;
            align-items: center; margin-bottom: 8px;
        }
        .author-chip {
            display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600;
        }
        .avatar-sm {
            width: 28px; height: 28px; border-radius: 50%;
            background: var(--primary); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700;
        }
        .post-actions { display: flex; gap: 12px; margin-top: 10px; }
        .post-action-btn {
            background: none; border: none; color: var(--text-muted);
            font-size: 13px; cursor: pointer; display: flex;
            align-items: center; gap: 5px; padding: 4px 0;
            font-family: inherit; transition: 0.15s;
        }
        .post-action-btn:hover { color: var(--primary); }

        /* ── ADMIN ── */
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { text-align: left; padding: 10px 12px; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border); }
        td { padding: 12px; border-bottom: 1px solid var(--border); }
        .action-btn { padding: 5px 12px; border: none; border-radius: 6px; color: #fff; font-size: 12px; font-weight: 600; cursor: pointer; margin-right: 4px; font-family: inherit; }
        .btn-del { background: var(--danger); }
        .btn-approve { background: var(--success); }

        /* ── PROFILE ── */
        .profile-hero {
            display: flex; align-items: center; gap: 20px;
        }
        .profile-avatar {
            width: 80px; height: 80px; border-radius: 50%;
            background: var(--primary); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; font-family: 'Playfair Display', serif;
            flex-shrink: 0;
        }

        /* ── ACADEMIC SEARCH ── */
        .module-result {
            padding: 14px; border-radius: 10px;
            background: var(--surface2); border: 1px solid var(--border);
            margin-bottom: 10px;
        }
        .module-result h4 { font-size: 14px; margin-bottom: 4px; }
        .module-result p { font-size: 13px; color: var(--text-muted); }

        /* ── HOME HERO ── */
        .hero {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 56px 40px;
            text-align: center;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute; top: -60px; right: -60px;
            width: 240px; height: 240px;
            background: radial-gradient(circle, #80000018 0%, transparent 70%);
            border-radius: 50%;
        }
        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 40px; color: var(--primary); margin-bottom: 12px;
        }
        .hero p { font-size: 16px; color: var(--text-muted); max-width: 560px; margin: 0 auto 28px; }

        /* Quick search bar */
        .quick-search {
            display: flex; max-width: 480px; margin: 0 auto; gap: 8px;
        }
        .quick-search input { margin: 0; flex: 1; }

        /* Calendar date cell transitions */
        .cal-date-cell {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .cal-date-cell:hover {
            background: var(--surface2) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }
        @media (max-width: 768px) {
            .grid-4 {
                grid-template-columns: repeat(2, 1fr);
            }
            .grid-3 {
                grid-template-columns: 1fr;
            }
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(46, 204, 113, 0); }
            100% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0); }
        }
    </style>
</head>
<body>

<!-- NOTIFICATION PANEL -->
<div class="notif-panel" id="notifPanel">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h3 style="font-size:15px;font-weight:600;">Notifications</h3>
        <button class="btn btn-outline" style="font-size:12px;padding:5px 10px;" onclick="markAllRead()">Mark all read</button>
    </div>
    <div id="notifList"></div>
</div>

<!-- HEADER -->
<header>
    <div class="logo" onclick="nav('home')">◈ Smart UniMate</div>
    <div class="topbar-right">
        <button class="icon-btn" onclick="toggleTheme()" id="themeBtn" title="Toggle theme"><i class="fa-solid fa-moon"></i></button>
        <button class="icon-btn" onclick="toggleNotifPanel()" title="Notifications">
            <i class="fa-solid fa-bell"></i>
            <span class="notif-badge" id="notifBadge"></span>
        </button>
        <button class="auth-btn" id="authBtn" onclick="toggleAuth()"><i class="fa-brands fa-microsoft"></i> Sign In</button>
    </div>
</header>

<div class="app-shell">
<!-- SIDEBAR -->
<aside>
    <div class="nav-section-label">Main</div>
    <div class="nav-item active" onclick="nav('home')" id="nav-home"><i class="fa-solid fa-house"></i> Home</div>
    <div class="nav-item" onclick="nav('news')" id="nav-news"><i class="fa-solid fa-newspaper"></i> Campus News</div>
    <div class="nav-item" onclick="nav('kb')" id="nav-kb"><i class="fa-solid fa-book-open"></i> Knowledge Base</div>
    <div class="nav-item" onclick="nav('chatbot')" id="nav-chatbot"><i class="fa-solid fa-robot"></i> AI Chat</div>
    <div class="nav-item" onclick="nav('academic')" id="nav-academic"><i class="fa-solid fa-book"></i> Academic Modules</div>
    <div class="nav-item" onclick="nav('timetable')" id="nav-timetable"><i class="fa-solid fa-calendar-days"></i> Timetable</div>
    <div class="nav-item" onclick="nav('gpa')" id="nav-gpa"><i class="fa-solid fa-chart-line"></i> GPA Calculator</div>
    <div class="nav-item" onclick="nav('community')" id="nav-community"><i class="fa-solid fa-users"></i> Community</div>
    <div style="margin-top:16px;">
    <div class="nav-section-label">Account</div>
    <div class="nav-item" onclick="nav('profile')" id="nav-profile"><i class="fa-solid fa-user"></i> Profile</div>
    <div class="nav-item" onclick="nav('admin')" id="nav-admin" style="color:#c0392b;display:none;"><i class="fa-solid fa-shield-halved"></i> Admin</div>
    </div>
</aside>

<!-- MAIN -->
<main>

<!-- HOME -->
<div class="section active" id="home">
    <!-- STUDENT HOME VIEW -->
    <div id="student-home-view">
        <div class="hero">
            <h1>Your Digital Campus</h1>
            <p>AI-powered assistant for Sabaragamuwa University students. Get instant answers, manage your schedule, and connect with peers.</p>
            <div class="quick-search">
                <input type="text" placeholder="Search modules, faculty, events..." id="heroSearch" onkeydown="if(event.key==='Enter'){nav('academic');document.getElementById('academicSearch').value=this.value;doSearch();}">
                <button class="btn btn-primary" onclick="nav('academic');document.getElementById('academicSearch').value=document.getElementById('heroSearch').value;doSearch();">Search</button>
            </div>
        </div>
        <div class="grid-3">
            <div class="stat-card">
                <div class="stat-num" id="homeEnrolledModules">0</div>
                <div class="stat-label">Enrolled Modules</div>
            </div>
            <div class="stat-card">
                <div class="stat-num" id="homeGPA">0.00</div>
                <div class="stat-label">Current GPA</div>
            </div>
            <div class="stat-card">
                <div class="stat-num" id="homeClassesThisWeek">0</div>
                <div class="stat-label">Classes This Week</div>
            </div>
        </div>
        <div style="margin-top:20px;" id="homeAlertsContainer">
            <!-- Dynamic alert card will be populated here -->
        </div>
    </div>

    <!-- ADMIN HOME VIEW -->
    <div id="admin-home-view" style="display:none;">
        <div class="hero" style="background: linear-gradient(135deg, #2c3e50, #800000); color: #fff; text-align: left; padding: 40px 32px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.15); margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                <div>
                    <span style="background:rgba(255,255,255,0.15); font-size:11px; font-weight:700; padding:4px 12px; border-radius:20px; letter-spacing:1px; text-transform:uppercase;">Admin Portal</span>
                    <h1 style="color:#fff; font-size:32px; margin-top:8px; margin-bottom:4px; font-family:'Playfair Display', serif;" id="adminHomeWelcomeName">Welcome, Administrator</h1>
                    <p style="color:rgba(255,255,255,0.85); font-size:14px; max-width:100%; margin:0;">Manage the campus database, add syllabus modules, review posts, and broadcast announcements.</p>
                </div>
                <div style="background:rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); padding: 12px 20px; border-radius: 12px; backdrop-filter: blur(8px);">
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:rgba(255,255,255,0.6);">System Status</div>
                    <div style="font-weight:600; font-size:14px; margin-top:4px; display:flex; align-items:center; gap:6px;">
                        <span style="width:10px; height:10px; background:#2ecc71; border-radius:50%; display:inline-block; animation: pulse 2s infinite;"></span>
                        Services Active
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Quick Stats -->
        <div class="grid-4" style="margin-bottom: 24px;">
            <div class="stat-card" style="cursor:pointer;" onclick="nav('admin'); switchAdminTab('modules');">
                <div class="stat-num" id="adminHomeTotalModules" style="color: var(--primary);">0</div>
                <div class="stat-label" style="font-weight:600;"><i class="fa-solid fa-book-open"></i> Syllabus Modules</div>
            </div>
            <div class="stat-card" style="cursor:pointer;" onclick="nav('admin'); switchAdminTab('dashboard');">
                <div class="stat-num" id="adminHomeTotalPosts" style="color: var(--secondary);">0</div>
                <div class="stat-label" style="font-weight:600;"><i class="fa-solid fa-users"></i> Community Posts</div>
            </div>
            <div class="stat-card" style="cursor:pointer;" onclick="nav('admin'); switchAdminTab('dashboard');">
                <div class="stat-num" id="adminHomeTotalNews" style="color: var(--success);">0</div>
                <div class="stat-label" style="font-weight:600;"><i class="fa-solid fa-newspaper"></i> Campus News</div>
            </div>
            <div class="stat-card" style="cursor:pointer;" onclick="nav('admin'); switchAdminTab('dashboard');">
                <div class="stat-num" id="adminHomeTotalKB" style="color: #7b2cbf;">0</div>
                <div class="stat-label" style="font-weight:600;"><i class="fa-solid fa-book"></i> KB Resources</div>
            </div>
        </div>

        <div class="grid-2">
            <!-- Left Side: Quick Academic Module Cataloger -->
            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:10px;">
                    <h3 class="section-title" style="margin:0; display:flex; align-items:center; gap:8px; color:var(--primary); font-size:16px;">
                        <i class="fa-solid fa-folder-plus"></i> Quick Module Cataloger
                    </h3>
                    <span style="font-size:11px; color:var(--text-muted);">Syllabus Entry Form</span>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label>Module Code *</label>
                        <input type="text" id="adminHomeModCode" placeholder="e.g. IS 4110">
                    </div>
                    <div class="form-group">
                        <label>Module Name *</label>
                        <input type="text" id="adminHomeModName" placeholder="e.g. Advanced Web Development">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label>Credits *</label>
                        <input type="number" id="adminHomeModCredits" placeholder="e.g. 3">
                    </div>
                    <div class="form-group">
                        <label>Faculty</label>
                        <input type="text" id="adminHomeModFaculty" placeholder="e.g. Computing">
                    </div>
                </div>
                <div class="form-group">
                    <label>Prerequisite</label>
                    <input type="text" id="adminHomeModPrereq" placeholder="e.g. IS 3110 (Optional)">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="adminHomeModDesc" rows="2" placeholder="Course description, goals, and learning outcomes..."></textarea>
                </div>
                <button class="btn btn-primary" style="background:var(--danger); border-color:var(--danger); width:100%; display:flex; align-items:center; justify-content:center; gap:8px;" onclick="submitAdminModuleHome()">
                    <i class="fa-solid fa-plus"></i> Catalog Module & Update Database
                </button>
            </div>

            <!-- Right Side: Quick Broadcast & Moderation Queue -->
            <div style="display:flex; flex-direction:column; gap:20px;">
                <!-- Quick Broadcast / News announcement -->
                <div class="card">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:10px;">
                        <h3 class="section-title" style="margin:0; display:flex; align-items:center; gap:8px; color:var(--secondary); font-size:16px;">
                            <i class="fa-solid fa-bullhorn"></i> Campus Announcement
                        </h3>
                        <span style="font-size:11px; color:var(--text-muted);">Quick Broadcast</span>
                    </div>
                    <div class="form-group">
                        <label>Announcement Title</label>
                        <input type="text" id="adminHomeNewsTitle" placeholder="e.g. Exam Schedule Released for Year 4">
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select id="adminHomeNewsCat">
                            <option value="1">Academic Updates</option>
                            <option value="2">Campus Events</option>
                            <option value="3">General Announcements</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Announcement Message</label>
                        <textarea id="adminHomeNewsContent" rows="2" placeholder="Write full announcement details here..."></textarea>
                    </div>
                    <button class="btn btn-primary" style="background:var(--primary); border-color:var(--primary); width:100%; display:flex; align-items:center; justify-content:center; gap:8px;" onclick="submitAdminNewsHome()">
                        <i class="fa-solid fa-paper-plane"></i> Publish to Campus Bulletin
                    </button>
                </div>

                <!-- Moderation Feed Widget -->
                <div class="card" style="margin-bottom:0;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:10px;">
                        <h3 class="section-title" style="margin:0; display:flex; align-items:center; gap:8px; color:var(--danger); font-size:16px;">
                            <i class="fa-solid fa-shield-halved"></i> Community Moderation
                        </h3>
                        <span style="font-size:11px; color:var(--text-muted);" id="adminHomeModQueueCount">0 items pending</span>
                    </div>
                    <div id="adminHomeModList" style="display:flex; flex-direction:column; gap:10px; max-height:220px; overflow-y:auto; padding-right:4px;">
                        <!-- Populate dynamically with recent posts -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AI CHAT -->
<div class="section" id="chatbot">
    <h2 class="page-title">AI Chat Assistant</h2>

    <div class="chat-wrap">

        <!-- HEADER -->
        <div class="chat-header">
            <div class="ai-avatar">
                <i class="fa-solid fa-robot" style="font-size:13px;"></i>
            </div>

            <div>
                <div style="font-size:14px;font-weight:600;">UniMate AI</div>
                <div style="font-size:11px;color:var(--text-muted);">
                    Powered by Claude · RAG-enhanced
                </div>
            </div>

            <button class="btn btn-outline"
                style="margin-left:auto;font-size:12px;padding:5px 10px;"
                onclick="clearChat()">
                Clear
            </button>
        </div>

        <!-- MESSAGES -->
        <div class="chat-messages" id="chatBox"
            style="height:420px;overflow-y:auto;padding:12px;display:flex;flex-direction:column;gap:10px;">

            <div class="msg bot">
                👋 Hello! I'm UniMate, powered by Claude AI and SUSL knowledge base.
                Ask me about modules, timetables, faculty contacts, or campus life!
            </div>

        </div>

        <!-- INPUT -->
        <div class="chat-input-row" style="display:flex;gap:8px;padding:10px;border-top:1px solid #eee;">

            <input type="text"
                id="chatInput"
                placeholder="Ask about IS 4110, library hours, exam schedules..."
                style="flex:1;padding:10px;border:1px solid #ddd;border-radius:10px;"
                onkeydown="if(event.key==='Enter') sendChat()">

            <button class="btn btn-primary" onclick="sendChat()">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <!-- QUICK CHIPS -->
    <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;">

        <button class="btn btn-outline" onclick="quickAsk('What modules are in Year 4 IS?')">
            📘 Year 4 modules
        </button>

        <button class="btn btn-outline" onclick="quickAsk('When is the library open?')">
            🕘 Library hours
        </button>

        <button class="btn btn-outline" onclick="quickAsk('Who is the HOD of IS department?')">
            🏛️ IS Department HOD
        </button>

        <button class="btn btn-outline" onclick="quickAsk('What are the capstone project guidelines?')">
            🎓 Capstone guidelines
        </button>

    </div>
</div>

<!-- CAMPUS NEWS -->
<div class="section" id="news">
    <h2 class="page-title">Campus News & Announcements</h2>
    <div class="grid-2" id="newsGrid">
        <!-- Fetched from backend -->
    </div>
</div>

<!-- KNOWLEDGE BASE -->
<div class="section" id="kb">
    <h2 class="page-title">Knowledge Base & Resources</h2>
    <div class="card">
        <div style="display:flex;gap:10px;">
            <input type="text" id="kbSearch" placeholder="Search guidelines, policies..." style="margin:0;" onkeyup="filterKB()">
        </div>
    </div>
    <div id="kbContainer" style="display:flex;flex-direction:column;gap:12px;">
        <!-- Fetched from backend -->
    </div>
</div>

<!-- ACADEMIC -->
<div class="section" id="academic">
    <h2 class="page-title">Academic Hub</h2>
    <div class="card">
        <div style="display:flex;gap:10px;">
            <input type="text" id="academicSearch" placeholder="Search module code or name (e.g. IS 4110)..." style="margin:0;">
            <button class="btn btn-primary" onclick="doSearch()">Search</button>
        </div>
    </div>
    <div id="searchResults"></div>
    <div class="card" style="margin-top:0;">
        <h3 class="section-title">All Modules — Year 4</h3>
        <div id="allModules"></div>
    </div>
</div>

<!-- TIMETABLE -->
<div class="section" id="timetable">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <div>
            <h2 class="page-title" style="margin:0;">My Timetable & Calendar</h2>
            <div style="font-size:13px;color:var(--text-muted);">Manage weekly classes, view the calendar, and sync schedules</div>
        </div>
    </div>
    
    <div class="grid-2" style="gap:24px;align-items:start;">
        <!-- LEFT COLUMN: Grid & Upload -->
        <div style="display:flex;flex-direction:column;gap:24px;">
            <div class="card" style="overflow-x:auto;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                    <h3 class="section-title" style="margin:0;">Weekly Schedule</h3>
                    <div style="display:flex;gap:8px;">
                        <button class="btn btn-outline" style="font-size:12px;" onclick="openAddClass()"><i class="fa-solid fa-plus"></i> Add Class</button>
                    </div>
                </div>
                <div class="timetable-grid" id="ttGrid"></div>
            </div>
            
            <!-- UPLOAD CALENDAR CARD -->
            <div class="card">
                <h3 class="section-title"><i class="fa-solid fa-file-import" style="color:var(--danger);margin-right:8px;"></i> Import Calendar File</h3>
                <p style="font-size:12px;color:var(--text-muted);margin-bottom:16px;">Sync your classes instantly by uploading an iCalendar (`.ics`) file exported from Google Calendar, Outlook, or other timetable schedules.</p>
                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                    <input type="file" id="icsFileInput" accept=".ics" style="display:none;" onchange="handleIcsUpload(event)">
                    <button class="btn btn-primary" style="background:var(--danger);border-color:var(--danger);" onclick="document.getElementById('icsFileInput').click()"><i class="fa-solid fa-cloud-arrow-up"></i> Upload `.ics` File</button>
                    <span style="font-size:12px;color:var(--text-muted);" id="icsUploadStatus">No file chosen</span>
                </div>
            </div>
        </div>
        
        <!-- RIGHT COLUMN: Calendar & Today's Classes -->
        <div style="display:flex;flex-direction:column;gap:24px;">
            <!-- INTERACTIVE CALENDAR CARD -->
            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                    <h3 class="section-title" style="margin:0;" id="calMonthTitle">May 2026</h3>
                    <div style="display:flex;gap:4px;">
                        <button class="icon-btn" onclick="prevMonth()" style="padding:4px 8px;"><i class="fa-solid fa-chevron-left"></i></button>
                        <button class="icon-btn" onclick="nextMonth()" style="padding:4px 8px;"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
                <!-- Mini Calendar Grid -->
                <div id="calGridContainer" style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;text-align:center;font-size:12px;">
                    <!-- Headers and days will be rendered here -->
                </div>
            </div>

            <!-- TODAY'S CLASSES CARD -->
            <div class="card">
                <h3 class="section-title">Schedule for <span id="scheduleDayTitle">Today</span></h3>
                <div id="todayClasses"></div>
            </div>
        </div>
    </div>
</div>

<!-- GPA CALCULATOR -->
<div class="section" id="gpa">
    <h2 class="page-title">GPA Calculator</h2>
    <div class="grid-2">
        <div class="card">
            <h3 class="section-title">Module Grades</h3>
            <div class="module-row" style="font-weight:600;font-size:12px;color:var(--text-muted);padding-top:0;">
                <span>Module</span><span>Credits</span><span>Grade</span><span>Points</span>
            </div>
            <div id="gpaModules"></div>
            <button class="btn btn-outline" style="margin-top:12px;font-size:13px;width:100%;" onclick="addGpaModule()"><i class="fa-solid fa-plus"></i> Add Module</button>
        </div>
        <div class="card">
            <div class="gpa-display">
                <div class="gpa-ring" id="gpaRing">
                    <div class="gpa-inner">
                        <div class="gpa-num" id="gpaVal">0.00</div>
                        <div class="gpa-sub">GPA / 4.00</div>
                    </div>
                </div>
                <div style="font-size:14px;color:var(--text-muted);margin-top:8px;" id="gpaClass">—</div>
            </div>
            <div class="grid-2" style="gap:10px;">
                <div class="stat-card"><div class="stat-num" id="totalCredits">0</div><div class="stat-label">Total Credits</div></div>
                <div class="stat-card"><div class="stat-num" id="totalPoints">0</div><div class="stat-label">Grade Points</div></div>
            </div>
        </div>
    </div>
</div>

<!-- COMMUNITY -->
<div class="section" id="community">
    <h2 class="page-title">Student Community</h2>
    <div class="card">
        <textarea id="postContent" rows="3" placeholder="Share a tip, ask a question, or post an update..."></textarea>
        <div id="imagePreviewRow" style="display:none;margin-top:8px;position:relative;">
            <img id="postImagePreview" src="" alt="preview" style="max-height:150px;border-radius:8px;">
            <button class="icon-btn" onclick="removePostImage()" style="position:absolute;top:4px;right:4px;background:rgba(0,0,0,0.6);color:#fff;border-radius:50%;width:24px;height:24px;font-size:12px;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div style="display:flex;gap:8px;margin-top:8px;align-items:center;">
            <select id="postCategory" style="width:auto;margin:0;font-size:13px;">
                <option>Academic Help</option>
                <option>Campus Life</option>
                <option>Announcements</option>
                <option>General</option>
            </select>
            <input type="file" id="postImageInput" accept="image/*" style="display:none;" onchange="previewPostImage(event)">
            <button class="btn btn-outline" style="font-size:12px;" onclick="document.getElementById('postImageInput').click()"><i class="fa-solid fa-image"></i> Image</button>
            <button class="btn btn-primary" style="margin-left:auto;" onclick="submitPost()">Post</button>
        </div>
    </div>
    <div class="card">
        <div style="display:flex;gap:8px;margin-bottom:16px;">
            <button class="btn btn-outline" style="font-size:12px;" onclick="filterPosts('All')">All</button>
            <button class="btn btn-outline" style="font-size:12px;" onclick="filterPosts('Academic Help')">Academic Help</button>
            <button class="btn btn-outline" style="font-size:12px;" onclick="filterPosts('Campus Life')">Campus Life</button>
        </div>
        <div id="feedContainer"></div>
    </div>
</div>

<!-- ADMIN -->
<div class="section" id="admin">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <div>
            <h2 class="page-title" style="color:var(--danger);margin:0;">Admin Panel</h2>
            <div style="font-size:13px;color:var(--text-muted);">Manage content, moderate posts and view your account</div>
        </div>
    </div>
    <div style="display:flex;gap:12px;margin-bottom:24px;overflow-x:auto;padding-bottom:4px;" id="adminSubNav">
        <button class="btn btn-primary" onclick="switchAdminTab('dashboard')" style="border-radius:20px;padding:6px 16px;font-size:13px;background:var(--danger);border-color:var(--danger);" data-tab="dashboard"><i class="fa-solid fa-chart-line"></i> Dashboard</button>
        <button class="btn btn-outline" onclick="switchAdminTab('modules')" style="border-radius:20px;padding:6px 16px;font-size:13px;" data-tab="modules"><i class="fa-solid fa-book-open"></i> Syllabus Catalog</button>
        <button class="btn btn-outline" onclick="switchAdminTab('profile')" style="border-radius:20px;padding:6px 16px;font-size:13px;" data-tab="profile"><i class="fa-solid fa-user-shield"></i> Admin Profile</button>
        <button class="btn btn-outline" onclick="switchAdminTab('settings')" style="border-radius:20px;padding:6px 16px;font-size:13px;" data-tab="settings"><i class="fa-solid fa-gear"></i> Settings</button>
    </div>

    <!-- DASHBOARD -->
    <div id="adminView-dashboard" class="admin-view">
        <div class="grid-3" style="margin-bottom:20px;">
            <div class="stat-card"><div class="stat-num" id="adminTotalPosts">0</div><div class="stat-label">Total Posts</div></div>
            <div class="stat-card"><div class="stat-num" id="adminTotalNews">0</div><div class="stat-label">News Articles</div></div>
            <div class="stat-card"><div class="stat-num" id="adminTotalKB">0</div><div class="stat-label">KB Resources</div></div>
        </div>
        <div class="card">
            <h3 class="section-title">Community Posts — Moderation Queue</h3>
            <div style="overflow-x:auto;">
                <table>
                    <thead><tr><th>Student</th><th>Content</th><th>Category</th><th>Date</th><th>Action</th></tr></thead>
                    <tbody id="modTable">
                        <tr><td colspan="5" style="text-align:center;color:var(--text-muted);">Loading posts...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="grid-2">
            <div class="card">
                <h3 class="section-title">Post Campus News</h3>
                <div class="form-group"><label>Title</label><input type="text" id="adminNewsTitle" placeholder="e.g. Exam Schedule Released"></div>
                <div class="form-group"><label>Category</label>
                    <select id="adminNewsCat">
                        <option value="1">Academic</option>
                        <option value="2">Events</option>
                        <option value="3">General</option>
                    </select>
                </div>
                <div class="form-group"><label>Content</label><textarea id="adminNewsContent" rows="3" placeholder="Enter full announcement..."></textarea></div>
                <button class="btn btn-primary" style="background:var(--danger);" onclick="submitAdminNews()"><i class="fa-solid fa-paper-plane"></i> Publish News</button>
            </div>
            <div class="card">
                <h3 class="section-title">Add Knowledge Base Resource</h3>
                <div class="form-group"><label>Title</label><input type="text" id="adminKbTitle" placeholder="e.g. Thesis Guidelines"></div>
                <div class="grid-2">
                    <div class="form-group"><label>Category</label><input type="text" id="adminKbCat" placeholder="e.g. Guidelines"></div>
                    <div class="form-group"><label>Source</label><input type="text" id="adminKbSource" placeholder="e.g. IT Faculty"></div>
                </div>
                <div class="grid-2">
                    <div class="form-group"><label>URL (Optional)</label><input type="text" id="adminKbUrl" placeholder="https://..."></div>
                    <div class="form-group">
                        <label>Attachment File (Optional)</label>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <input type="file" id="adminKbFile" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt" style="display:none;" onchange="document.getElementById('adminKbFileName').textContent = this.files[0] ? this.files[0].name : 'No file selected'">
                            <button class="btn btn-outline" style="font-size:12px;margin:0;" onclick="document.getElementById('adminKbFile').click()"><i class="fa-solid fa-file-pdf"></i> Select File</button>
                            <span id="adminKbFileName" style="font-size:12px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:150px;">No file selected</span>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary" style="background:var(--danger);width:100%;margin-top:10px;" id="adminKbSubmitBtn" onclick="submitAdminKB()"><i class="fa-solid fa-upload"></i> Upload Resource</button>
            </div>
        </div>
    </div>

    <!-- MODULES CATALOG -->
    <div id="adminView-modules" class="admin-view" style="display:none;">
        <div class="card">
            <h3 class="section-title">Academic Modules Catalog</h3>
            <div class="grid-2">
                <div class="form-group"><label>Module Code *</label><input type="text" id="adminModCode" placeholder="e.g. IS 4110"></div>
                <div class="form-group"><label>Module Name *</label><input type="text" id="adminModName" placeholder="e.g. Advanced Web Dev"></div>
            </div>
            <div class="grid-2">
                <div class="form-group"><label>Credits *</label><input type="number" id="adminModCredits" placeholder="e.g. 3"></div>
                <div class="form-group"><label>Faculty</label><input type="text" id="adminModFaculty" placeholder="e.g. Computing"></div>
            </div>
            <div class="form-group"><label>Prerequisite</label><input type="text" id="adminModPrereq" placeholder="e.g. IS 3110"></div>
            <div class="form-group"><label>Description</label><textarea id="adminModDesc" rows="2" placeholder="Course description..."></textarea></div>
            <button class="btn btn-primary" style="background:var(--danger);width:100%;" onclick="submitAdminModule()"><i class="fa-solid fa-plus"></i> Catalog Module</button>
        </div>
        
        <div class="card" style="margin-top:20px;">
            <h3 class="section-title">Current Modules (<span id="adminModCount">0</span>)</h3>
            <div style="overflow-x:auto;">
                <table>
                    <thead><tr><th>Code</th><th>Name</th><th>Credits</th><th>Faculty</th><th>Action</th></tr></thead>
                    <tbody id="adminModTable">
                        <tr><td colspan="5" style="text-align:center;color:var(--text-muted);">Loading modules...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ADMIN PROFILE -->
    <div id="adminView-profile" class="admin-view" style="display:none;">
        <div class="card" style="display:flex;justify-content:space-between;align-items:center;padding:32px;flex-wrap:wrap;gap:20px;">
            <div style="display:flex;gap:24px;align-items:center;">
                <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--danger),#ff6b6b);display:flex;align-items:center;justify-content:center;font-size:32px;color:#fff;">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div>
                    <h3 style="font-size:20px;margin-bottom:4px;" id="adminNameBig">System Administrator</h3>
                    <div style="font-size:13px;color:var(--text-muted);margin-bottom:8px;" id="adminEmailBig">admin@smartunimate.com</div>
                    <div style="margin-bottom:12px;">
                        <span style="background:var(--danger);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:1px;margin-right:8px;">ADMINISTRATOR</span>
                    </div>
                    <button class="btn btn-primary" style="background:var(--danger);border-color:var(--danger);font-size:12px;padding:6px 16px;border-radius:6px;" onclick="toggleAdminProfileEdit()" id="btnAdminProfileEdit"><i class="fa-solid fa-pen"></i> Edit</button>
                </div>
            </div>
            <div style="display:flex;gap:32px;text-align:center;">
                <div><div style="font-size:24px;font-weight:700;color:var(--danger);" id="adminStatPosts">0</div><div style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;">POSTS</div></div>
                <div><div style="font-size:24px;font-weight:700;color:var(--danger);" id="adminStatNews">0</div><div style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;">NEWS</div></div>
                <div><div style="font-size:24px;font-weight:700;color:var(--danger);" id="adminStatKB">0</div><div style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;">RESOURCES</div></div>
            </div>
        </div>
        <div class="card" style="margin-top:20px;">
            <h3 class="section-title">Account Details</h3>
            <div class="grid-2" style="gap:24px;">
                <div class="form-group">
                    <label style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase;">Full Name</label>
                    <input type="text" id="adminProfName" disabled>
                </div>
                <div class="form-group">
                    <label style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase;">Email Address</label>
                    <input type="email" id="adminProfEmail" disabled>
                </div>
                <div class="form-group">
                    <label style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase;">Role</label>
                    <input type="text" value="System Administrator" disabled>
                </div>
                <div class="form-group">
                    <label style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase;">Member Since</label>
                    <input type="text" id="adminMemberSince" disabled>
                </div>
            </div>
            <div style="display:none;margin-top:16px;text-align:right;" id="adminProfSaveRow">
                <button class="btn btn-primary" onclick="saveAdminProfileDetails()">Save Changes</button>
            </div>
        </div>
        <div class="card" style="margin-top:20px;">
            <h3 class="section-title">Change Admin Password</h3>
            <div id="adminPwSuccess" style="color:var(--success);font-size:13px;margin-bottom:12px;display:none;padding:10px;background:rgba(45,122,79,0.1);border-radius:8px;"></div>
            <div id="adminPwError" style="color:var(--danger);font-size:13px;margin-bottom:12px;display:none;padding:10px;background:rgba(192,57,43,0.1);border-radius:8px;"></div>
            <div class="form-group"><label>Current Password</label><input type="password" id="adminCurrPw" placeholder="Enter current password"></div>
            <div class="grid-2">
                <div class="form-group"><label>New Password</label><input type="password" id="adminNewPw" placeholder="At least 8 characters"></div>
                <div class="form-group"><label>Confirm New Password</label><input type="password" id="adminConfPw" placeholder="Repeat new password"></div>
            </div>
            <button class="btn btn-primary" style="background:var(--danger);margin-top:12px;" onclick="changeAdminPassword()"><i class="fa-solid fa-lock"></i> Update Password</button>
        </div>
    </div>

    <!-- ADMIN SETTINGS -->
    <div id="adminView-settings" class="admin-view" style="display:none;">
        <div class="card">
            <h3 class="section-title">Appearance</h3>
            <div class="setting-row">
                <div><div class="setting-label"><i class="fa-solid fa-moon"></i> Dark Mode</div><div class="setting-desc">Switch between light and dark interface</div></div>
                <label class="toggle-switch"><input type="checkbox" id="adminDarkToggle" onchange="toggleThemeAdmin()"><span class="toggle-track"></span></label>
            </div>
            <div class="setting-row">
                <div><div class="setting-label"><i class="fa-solid fa-droplet"></i> Glass Effect</div><div class="setting-desc">Enable frosted-glass blur</div></div>
                <label class="toggle-switch"><input type="checkbox" id="adminGlassToggle" onchange="toggleGlass()"><span class="toggle-track"></span></label>
            </div>
            <div class="setting-row" style="flex-direction:column;align-items:flex-start;gap:12px;">
                <div><div class="setting-label"><i class="fa-solid fa-palette"></i> Color Palette</div><div class="setting-desc">Choose accent color</div></div>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <div class="palette-swatch" style="background:linear-gradient(135deg,#800000,#a01010);" onclick="setPalette('')" title="Default Maroon"></div>
                    <div class="palette-swatch" style="background:linear-gradient(135deg,#0077b6,#0096c7);" onclick="setPalette('palette-ocean')" title="Ocean Blue"></div>
                    <div class="palette-swatch" style="background:linear-gradient(135deg,#2d6a4f,#40916c);" onclick="setPalette('palette-forest')" title="Forest Green"></div>
                    <div class="palette-swatch" style="background:linear-gradient(135deg,#e76f51,#f4a261);" onclick="setPalette('palette-sunset')" title="Sunset Orange"></div>
                    <div class="palette-swatch" style="background:linear-gradient(135deg,#7b2cbf,#9d4edd);" onclick="setPalette('palette-purple')" title="Royal Purple"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PROFILE -->
<div class="section" id="profile">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <div>
            <h2 class="page-title" style="margin:0;">Profile</h2>
            <div style="font-size:13px;color:var(--text-muted);">Manage your account and preferences</div>
        </div>
    </div>
    
    <!-- Sub Nav -->
    <div style="display:flex;gap:12px;margin-bottom:24px;overflow-x:auto;padding-bottom:4px;" id="profileSubNav">
        <button class="btn btn-primary" onclick="switchProfileTab('profile')" style="border-radius:20px;padding:6px 16px;font-size:13px;background:var(--danger);border-color:var(--danger);" data-tab="profile"><i class="fa-solid fa-user"></i> My Profile</button>
        <button class="btn btn-outline" onclick="switchProfileTab('posts')" style="border-radius:20px;padding:6px 16px;font-size:13px;" data-tab="posts"><i class="fa-regular fa-pen-to-square"></i> My Posts</button>
        <button class="btn btn-outline" onclick="switchProfileTab('chat')" style="border-radius:20px;padding:6px 16px;font-size:13px;" data-tab="chat"><i class="fa-regular fa-comments"></i> Chat History</button>
        <button class="btn btn-outline" onclick="switchProfileTab('notifications')" style="border-radius:20px;padding:6px 16px;font-size:13px;" data-tab="notifications"><i class="fa-regular fa-bell"></i> Notifications</button>
        <button class="btn btn-outline" onclick="switchProfileTab('settings')" style="border-radius:20px;padding:6px 16px;font-size:13px;" data-tab="settings"><i class="fa-solid fa-gear"></i> Settings</button>
    </div>

    <!-- PROFILE VIEW -->
    <div id="profView-profile" class="prof-view">

    <!-- Summary Card -->
    <div class="card" style="display:flex;justify-content:space-between;align-items:center;padding:32px;">
        <div style="display:flex;gap:24px;align-items:center;">
            <div style="width:80px;height:80px;border-radius:50%;background:#ffe4e1;display:flex;align-items:center;justify-content:center;font-size:40px;" id="profileAvatarBig">
                👨‍🎓
            </div>
            <div>
                <h3 style="font-size:20px;margin-bottom:4px;" id="profileNameBig">User Name</h3>
                <div style="font-size:13px;color:var(--text-muted);margin-bottom:4px;" id="profileIndexBig">Index: N/A · Faculty</div>
                <div style="font-size:12px;color:var(--text-muted);margin-bottom:12px;" id="profileYearBig">Year 1</div>
                <button class="btn btn-primary" style="background:var(--danger);border-color:var(--danger);font-size:12px;padding:6px 16px;border-radius:6px;" onclick="toggleProfileEdit()" id="btnProfileEdit"><i class="fa-solid fa-pen"></i> Edit</button>
            </div>
        </div>
        <div style="display:flex;gap:32px;text-align:center;">
            <div><div style="font-size:24px;font-weight:700;color:var(--danger);" id="statPosts">0</div><div style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;">POSTS</div></div>
            <div><div style="font-size:24px;font-weight:700;color:var(--danger);" id="statQuestions">0</div><div style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;">QUESTIONS</div></div>
            <div><div style="font-size:24px;font-weight:700;color:var(--danger);" id="statComments">0</div><div style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;">COMMENTS</div></div>
        </div>
    </div>

    <!-- Form Grid -->
    <div class="card">
        <div class="grid-2" style="gap:24px;">
            <div class="form-group"><label style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase;">Full Name</label><input type="text" id="profName" disabled></div>
            <div class="form-group"><label style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase;">Index Number</label><input type="text" id="profId" disabled></div>
            <div class="form-group"><label style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase;">University Email</label><input type="email" id="profEmail" disabled></div>
            <div class="form-group"><label style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase;">Mobile</label><input type="text" id="profPhone" disabled></div>
            <div class="form-group"><label style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase;">Faculty</label><input type="text" id="profFaculty" disabled></div>
            <div class="form-group"><label style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase;">Department</label><input type="text" id="profDept" value="Computing & Information Systems" disabled></div>
            <div class="form-group"><label style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase;">Year / Semester</label><input type="text" id="profYear" disabled></div>
            <div class="form-group"><label style="font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase;">Hostel Block</label><input type="text" id="profHostel" value="Block B, Room 214" disabled></div>
        </div>
        <div style="display:none;margin-top:16px;text-align:right;" id="profSaveRow">
            <button class="btn btn-primary" onclick="saveProfileDetails()">Save Changes</button>
        </div>
    </div>

    <!-- Recent Posts -->
    <div class="card" style="margin-top:24px;">
        <h3 style="font-size:11px;font-weight:700;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase;margin-bottom:16px;">My Recent Posts</h3>
        <div id="profRecentPosts">
            <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--border);">
                <strong style="font-size:14px;">Tips for the IS2030 Database Assignment 📁</strong>
                <span style="font-size:12px;color:var(--text-muted);">Yesterday</span>
            </div>
        </div>
    </div>
    </div> <!-- end profView-profile -->

    <!-- POSTS VIEW -->
    <div id="profView-posts" class="prof-view" style="display:none;">
        <h3 class="section-title">My Posts</h3>
        <div id="profileMyPostsFeed">
            <p style="color:var(--text-muted);font-size:13px;">Loading posts...</p>
        </div>
    </div>

    <!-- CHAT VIEW -->
    <div id="profView-chat" class="prof-view" style="display:none;">
        <h3 class="section-title">Chat History</h3>
        <div class="card" style="max-height:500px;overflow-y:auto;padding:16px;background:var(--surface);">
            <div id="profileChatHistory">
                <p style="color:var(--text-muted);font-size:13px;text-align:center;">Loading chat history...</p>
            </div>
        </div>
    </div>

    <!-- NOTIFICATIONS VIEW -->
    <div id="profView-notifications" class="prof-view" style="display:none;">
        <h3 class="section-title">Notifications</h3>
        <div class="card" id="profileNotificationsList">
            <p style="color:var(--text-muted);font-size:13px;">Loading notifications...</p>
        </div>
    </div>

    <!-- SETTINGS VIEW -->
    <div id="profView-settings" class="prof-view" style="display:none;">
        <!-- Appearance -->
        <div class="card">
            <h3 class="section-title">Appearance</h3>
            <div class="setting-row">
                <div>
                    <div class="setting-label"><i class="fa-solid fa-moon"></i> Dark Mode</div>
                    <div class="setting-desc">Switch between light and dark interface</div>
                </div>
                <label class="toggle-switch"><input type="checkbox" id="settDarkToggle" onchange="toggleThemeFromSettings()"><span class="toggle-track"></span></label>
            </div>
            <div class="setting-row">
                <div>
                    <div class="setting-label"><i class="fa-solid fa-droplet"></i> Glass Effect</div>
                    <div class="setting-desc">Enable frosted-glass blur on cards and panels</div>
                </div>
                <label class="toggle-switch"><input type="checkbox" id="settGlassToggle" onchange="toggleGlass()"><span class="toggle-track"></span></label>
            </div>
            <div class="setting-row" style="flex-direction:column;align-items:flex-start;gap:12px;">
                <div>
                    <div class="setting-label"><i class="fa-solid fa-palette"></i> Color Palette</div>
                    <div class="setting-desc">Choose an accent color for the interface</div>
                </div>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <div class="palette-swatch" style="background:linear-gradient(135deg,#800000,#a01010);" onclick="setPalette('')" title="Default Maroon"></div>
                    <div class="palette-swatch" style="background:linear-gradient(135deg,#0077b6,#0096c7);" onclick="setPalette('palette-ocean')" title="Ocean Blue"></div>
                    <div class="palette-swatch" style="background:linear-gradient(135deg,#2d6a4f,#40916c);" onclick="setPalette('palette-forest')" title="Forest Green"></div>
                    <div class="palette-swatch" style="background:linear-gradient(135deg,#e76f51,#f4a261);" onclick="setPalette('palette-sunset')" title="Sunset Orange"></div>
                    <div class="palette-swatch" style="background:linear-gradient(135deg,#7b2cbf,#9d4edd);" onclick="setPalette('palette-purple')" title="Royal Purple"></div>
                </div>
            </div>
        </div>

        <!-- Password -->
        <div class="card" style="margin-top:20px;">
            <h3 class="section-title">Change Password</h3>
            <div id="pwSuccessMsg" style="color:var(--success);font-size:13px;margin-bottom:12px;display:none;"></div>
            <div id="pwErrorMsg" style="color:var(--danger);font-size:13px;margin-bottom:12px;display:none;"></div>
            
            <div class="form-group"><label>Current Password</label><input type="password" id="setCurrPw"></div>
            <div class="grid-2">
                <div class="form-group"><label>New Password</label><input type="password" id="setNewPw"></div>
                <div class="form-group"><label>Confirm</label><input type="password" id="setConfPw"></div>
            </div>
            <button class="btn btn-primary" onclick="submitChangePassword()" style="margin-top:12px;">Update Password</button>
        </div>

        <!-- Danger Zone -->
        <div class="card" style="margin-top:20px;border:1px solid var(--danger);">
            <h3 class="section-title" style="color:var(--danger);">Danger Zone</h3>
            <div class="setting-row">
                <div>
                    <div class="setting-label">Clear All Chat History</div>
                    <div class="setting-desc">Permanently delete all your AI chat messages</div>
                </div>
                <button class="btn btn-outline" style="color:var(--danger);border-color:var(--danger);font-size:12px;" onclick="clearChatHistoryFromSettings()">Clear History</button>
            </div>
        </div>
    </div>
</div>

</main>
</div>

<!-- ADD CLASS MODAL -->
<div id="modalOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1100;align-items:center;justify-content:center;">
    <div style="background:var(--surface);border-radius:var(--radius);padding:28px;width:400px;max-width:90vw;">
        <h3 style="margin-bottom:18px;font-size:16px;">Add Class to Timetable</h3>
        <div class="form-group"><label>Module Code</label><input type="text" id="mc_code" placeholder="e.g. IS 4110"></div>
        <div class="form-group"><label>Module Name</label><input type="text" id="mc_name" placeholder="e.g. Capstone Project"></div>
        <div class="grid-2">
            <div class="form-group"><label>Day</label><select id="mc_day"><option>Mon</option><option>Tue</option><option>Wed</option><option>Thu</option><option>Fri</option></select></div>
            <div class="form-group"><label>Time Slot</label><select id="mc_slot">
                <option value="0">8:00–9:00</option><option value="1">9:00–10:00</option>
                <option value="2">10:00–11:00</option><option value="3">11:00–12:00</option>
                <option value="4">13:00–14:00</option><option value="5">14:00–15:00</option>
                <option value="6">15:00–16:00</option>
            </select></div>
        </div>
        <div class="form-group"><label>Room</label><input type="text" id="mc_room" placeholder="e.g. Lab 3, E201"></div>
        <div style="display:flex;gap:10px;margin-top:8px;">
            <button class="btn btn-primary" onclick="saveClass()">Save</button>
            <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
        </div>
    </div>
</div>

<!-- AUTH MODAL -->
<div id="authModalOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1100;align-items:center;justify-content:center;">
    <div style="background:var(--surface);border-radius:var(--radius);padding:32px;width:400px;max-width:90vw;position:relative;">
        <button class="icon-btn" style="position:absolute;top:16px;right:16px;" onclick="closeAuthModal()"><i class="fa-solid fa-xmark"></i></button>
        <h3 style="margin-bottom:20px;font-size:20px;font-family:'Playfair Display',serif;color:var(--primary);" id="authTitle">Sign In</h3>
        <div id="authErrorMsg" style="color:var(--danger);font-size:13px;margin-bottom:12px;display:none;"></div>
        
        <div id="loginForm">
            <div class="form-group"><label>Sign in as</label>
                <select id="loginRole">
                    <option value="student">Student</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>
            <div class="form-group"><label>Email Address</label><input type="email" id="loginEmail" placeholder="student@susl.lk"></div>
            <div class="form-group"><label>Password</label><input type="password" id="loginPassword" placeholder="••••••••"></div>
            <button class="btn btn-primary" style="width:100%;margin-top:8px;" onclick="submitLogin()">Sign In</button>
            <div style="text-align:center;margin-top:16px;font-size:13px;color:var(--text-muted);">
                Don't have an account? <a href="#" style="color:var(--primary);text-decoration:none;font-weight:600;" onclick="toggleAuthMode('register');return false;">Register</a>
                <br><br>
                <a href="#" style="color:var(--primary);text-decoration:none;font-weight:600;" onclick="toggleAuthMode('forgot');return false;">Forgot Password?</a>
            </div>
        </div>

        <div id="registerForm" style="display:none;">
            <div class="form-group"><label>Full Name</label><input type="text" id="regName" placeholder="e.g. John Doe"></div>
            <div class="form-group"><label>Email Address</label><input type="email" id="regEmail" placeholder="student@susl.lk"></div>
            <div class="grid-2">
                <div class="form-group"><label>Student ID</label><input type="text" id="regStudentId" placeholder="e.g. 22FIS0100"></div>
                <div class="form-group"><label>Year</label>
                    <select id="regYear"><option value="1">Year 1</option><option value="2">Year 2</option><option value="3">Year 3</option><option value="4">Year 4</option></select>
                </div>
            </div>
            <div class="form-group"><label>Faculty/Department</label><input type="text" id="regFaculty" placeholder="e.g. Information Systems"></div>
            <div class="grid-2">
                <div class="form-group"><label>Password</label><input type="password" id="regPassword" placeholder="••••••••"></div>
                <div class="form-group"><label>Confirm</label><input type="password" id="regPasswordConfirm" placeholder="••••••••"></div>
            </div>
            <button class="btn btn-primary" style="width:100%;margin-top:8px;" onclick="submitRegister()">Create Account</button>
            <div style="text-align:center;margin-top:16px;font-size:13px;color:var(--text-muted);">
                Already have an account? <a href="#" style="color:var(--primary);text-decoration:none;font-weight:600;" onclick="toggleAuthMode('login');return false;">Sign In</a>
            </div>
        </div>
        <div id="forgotForm" style="display:none;">
            <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">Verify your identity to reset your password.</p>
            <div class="form-group"><label>University Email</label><input type="email" id="forgotEmail" placeholder="student@susl.lk"></div>
            <div class="form-group"><label>Student ID</label><input type="text" id="forgotId" placeholder="e.g. 22FIS0100"></div>
            <div class="grid-2">
                <div class="form-group"><label>New Password</label><input type="password" id="forgotPassword" placeholder="••••••••"></div>
                <div class="form-group"><label>Confirm</label><input type="password" id="forgotConfirm" placeholder="••••••••"></div>
            </div>
            <button class="btn btn-primary" style="width:100%;margin-top:8px;" onclick="submitForgotPassword()">Reset Password</button>
            <div style="text-align:center;margin-top:16px;font-size:13px;color:var(--text-muted);">
                Remembered it? <a href="#" style="color:var(--primary);text-decoration:none;font-weight:600;" onclick="toggleAuthMode('login');return false;">Sign In</a>
            </div>
        </div>
    </div>
</div>

<script>
// ── API HELPER ──
async function apiFetch(url, options = {}) {
    options.headers = options.headers || {};
    options.headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    options.headers['Accept'] = 'application/json';
    if (!(options.body instanceof FormData)) {
        options.headers['Content-Type'] = 'application/json';
    }
    
    // Add Bearer token if it exists
    const token = localStorage.getItem('susl_token');
    if(token) {
        options.headers['Authorization'] = `Bearer ${token}`;
    } else {
        options.credentials = 'same-origin';
    }
    
    const response = await fetch(url, options);
    
    let data = null;
    try {
        if (response.status !== 204) {
            data = await response.json();
        }
    } catch(e) {}

    if (!response.ok) {
        if (response.status === 401) {
            console.error("Unauthorized: Please sign in first.");
            localStorage.removeItem('susl_token');
            localStorage.removeItem('susl_role');
            localStorage.removeItem('susl_user');
            isLoggedIn = false;
            initAuth();
            addNotif('⚠️ Your session has expired. Please sign in again.');
            toggleAuth();
        }
        
        // If Laravel validation fails, it returns 422 with an "errors" object
        if (response.status === 422 && data && data.errors) {
            // Grab the very first validation error message to show to the user
            const firstError = Object.values(data.errors)[0][0];
            throw new Error(firstError);
        } else if (data && data.message) {
            throw new Error(data.message);
        }
        
        throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    return data;
}

// ── DATA ──
let MODULES_DB = [];

const NOTIFICATIONS = [
    { text:'Capstone proposal deadline in 3 days — IS 4110', time:'Just now', read:false },
    { text:'New reply on your community post', time:'1 hour ago', read:false },
    { text:'Timetable updated for Semester 2', time:'Yesterday', read:true },
    { text:'Library will be closed on 15 April', time:'2 days ago', read:true },
];

let isLoggedIn = false;
let currentFilter = 'All';
let notifPanelOpen = false;

let posts = [];

const TT_DAYS = ['Mon','Tue','Wed','Thu','Fri'];
const TT_SLOTS = ['8:00–9:00','9:00–10:00','10:00–11:00','11:00–12:00','13:00–14:00','14:00–15:00','15:00–16:00'];
let timetable = {};

let gpaModules = JSON.parse(localStorage.getItem('susl_gpa')) || [
    { name:'IS 4110 Capstone', credits:6, grade:'A' },
    { name:'IS 4102 Data Mining', credits:3, grade:'B+' },
    { name:'IS 4104 Cloud', credits:3, grade:'A-' },
    { name:'IS 4106 Research', credits:2, grade:'A' },
];

const GRADE_POINTS = { 'A+':4.0,'A':4.0,'A-':3.7,'B+':3.3,'B':3.0,'B-':2.7,'C+':2.3,'C':2.0,'C-':1.7,'D+':1.3,'D':1.0,'F':0.0 };

// ── NAVIGATION ──
function nav(id) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    const ni = document.getElementById('nav-'+id);
    if(ni) ni.classList.add('active');
    if(notifPanelOpen) toggleNotifPanel();
}

// ── THEME ──
function toggleTheme() {
    document.body.classList.toggle('dark');
    const isDark = document.body.classList.contains('dark');
    document.getElementById('themeBtn').innerHTML = isDark
        ? '<i class="fa-solid fa-sun"></i>'
        : '<i class="fa-solid fa-moon"></i>';
    localStorage.setItem('susl_theme', isDark ? 'dark' : 'light');
    // Sync settings toggle
    const t = document.getElementById('settDarkToggle');
    if(t) t.checked = isDark;
}
function toggleThemeFromSettings() {
    toggleTheme();
}
function toggleGlass() {
    const on = document.getElementById('settGlassToggle').checked;
    document.body.classList.toggle('glass-mode', on);
    localStorage.setItem('susl_glass', on ? 'on' : 'off');
}
function setPalette(cls) {
    // Remove all palette classes
    document.body.classList.remove('palette-ocean','palette-forest','palette-sunset','palette-purple');
    if(cls) document.body.classList.add(cls);
    localStorage.setItem('susl_palette', cls);
    // Update swatch active states
    document.querySelectorAll('.palette-swatch').forEach((s,i) => {
        const map = ['','palette-ocean','palette-forest','palette-sunset','palette-purple'];
        s.classList.toggle('active', map[i] === cls);
    });
}
// Restore saved settings on load
if(localStorage.getItem('susl_theme') === 'dark') {
    document.body.classList.add('dark');
    document.getElementById('themeBtn').innerHTML = '<i class="fa-solid fa-sun"></i>';
}
if(localStorage.getItem('susl_glass') === 'on') {
    document.body.classList.add('glass-mode');
}
(function() {
    const p = localStorage.getItem('susl_palette');
    if(p) document.body.classList.add(p);
})();

// ── IMAGE UPLOAD FOR POSTS ──
let pendingPostImage = null;
function previewPostImage(e) {
    const file = e.target.files[0];
    if(!file) return;
    pendingPostImage = file;
    const reader = new FileReader();
    reader.onload = function(ev) {
        document.getElementById('postImagePreview').src = ev.target.result;
        document.getElementById('imagePreviewRow').style.display = 'block';
    };
    reader.readAsDataURL(file);
}
function removePostImage() {
    pendingPostImage = null;
    document.getElementById('postImageInput').value = '';
    document.getElementById('imagePreviewRow').style.display = 'none';
}

// ── COMMENT STORAGE (client-side for now) ──
let postComments = JSON.parse(localStorage.getItem('susl_comments') || '{}');
function addComment(postId) {
    const input = document.getElementById('commentInput_' + postId);
    if(!input || !input.value.trim()) return;
    const userStr = localStorage.getItem('susl_user');
    const userName = userStr ? JSON.parse(userStr).name : 'You';
    if(!postComments[postId]) postComments[postId] = [];
    postComments[postId].push({ author: userName, text: input.value.trim(), time: 'Just now' });
    localStorage.setItem('susl_comments', JSON.stringify(postComments));
    input.value = '';
    renderPosts(currentFilter);
}
function sharePost(postId) {
    const p = posts.find(x => x.id == postId);
    if(!p) return;
    const shareText = `Check out this post on SmartUniMate:\n"${p.text}"\n— ${p.author}`;
    // Use fallback for non-HTTPS environments
    try {
        const ta = document.createElement('textarea');
        ta.value = shareText;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        addNotif('📋 Post copied to clipboard!');
    } catch(e) {
        addNotif('⚠️ Could not copy. Please try again.');
    }
}
async function clearChatHistoryFromSettings() {
    if(!confirm('Are you sure you want to delete all your chat history?')) return;
    try {
        await apiFetch('/api/v1/chat/history', { method: 'DELETE' });
        addNotif('Chat history cleared.');
    } catch(e) {
        addNotif('⚠️ Failed to clear chat history.');
    }
}

// ── AUTH ──
function initAuth() {
    const token = localStorage.getItem('susl_token');
    const role = localStorage.getItem('susl_role');
    const userStr = localStorage.getItem('susl_user');
    
    // Reset defaults
    isLoggedIn = false;
    document.getElementById('nav-admin').style.display = 'none';
    document.getElementById('nav-profile').style.display = 'block';
    document.getElementById('nav-gpa').style.display = 'block';
    document.getElementById('nav-timetable').style.display = 'block';
    
    // Reset home views
    document.getElementById('student-home-view').style.display = 'block';
    document.getElementById('admin-home-view').style.display = 'none';
    
    updateProfileUI(null);
    timetable = {};
    if(typeof renderTimetable === 'function') renderTimetable();

    if(token) {
        isLoggedIn = true;
        
        if (role === 'admin') {
            document.getElementById('nav-admin').style.display = 'block';
            document.getElementById('nav-profile').style.display = 'none';
            document.getElementById('nav-gpa').style.display = 'none';
            document.getElementById('nav-timetable').style.display = 'none';
            
            // Show Admin Home Dashboard
            document.getElementById('student-home-view').style.display = 'none';
            document.getElementById('admin-home-view').style.display = 'block';
            
            const admin = userStr ? JSON.parse(userStr) : null;
            updateAdminProfileUI(admin);
            if (admin && admin.name) {
                document.getElementById('adminHomeWelcomeName').textContent = `Welcome, ${admin.name}`;
            } else {
                document.getElementById('adminHomeWelcomeName').textContent = `Welcome, Administrator`;
            }
            
            loadAdminHomeData();
            
            // If we are on a student-only tab, redirect to admin
            const currentTab = document.querySelector('.section.active').id;
            if (['profile', 'gpa', 'timetable'].includes(currentTab)) {
                nav('admin');
            }
        } else {
            const user = userStr ? JSON.parse(userStr) : null;
            updateProfileUI(user);
            if(typeof fetchTimetable === 'function') fetchTimetable();
        }
    }
    
    updateAuthBtn();
}

function updateProfileUI(user) {
    if(user) {
        document.getElementById('profileNameBig').textContent = user.name || 'User';
        document.getElementById('profileIndexBig').textContent = `Index: ${user.student_id || 'N/A'} · ${user.faculty || 'N/A'}`;
        document.getElementById('profileYearBig').textContent = `Year ${user.year || 'N/A'}`;
        
        document.getElementById('profName').value = user.name || '';
        document.getElementById('profId').value = user.student_id || '';
        document.getElementById('profEmail').value = user.email || '';
        document.getElementById('profPhone').value = user.phone || '';
        document.getElementById('profFaculty').value = user.faculty || '';
        document.getElementById('profYear').value = `Year ${user.year || '1'}`;
        
        renderProfileRecentPosts(user.id);
        // Update profile stats dynamically
        const myPosts = posts.filter(p => p.user_id == user.id);
        document.getElementById('statPosts').textContent = myPosts.length;
        document.getElementById('statQuestions').textContent = myPosts.filter(p => p.category === 'Academic Help').length;
        const totalComments = myPosts.reduce((sum, p) => sum + (postComments[p.id] ? postComments[p.id].length : 0), 0);
        document.getElementById('statComments').textContent = totalComments;
    } else {
        document.getElementById('profileNameBig').textContent = 'Guest User';
        document.getElementById('profileIndexBig').textContent = 'Index: N/A';
        document.getElementById('profileYearBig').textContent = 'Sign in to see your profile';
    }
}

function switchProfileTab(tabName) {
    // Hide all prof-view containers
    document.querySelectorAll('.prof-view').forEach(el => el.style.display = 'none');
    
    // Show the targeted one
    const target = document.getElementById(`profView-${tabName}`);
    if(target) target.style.display = 'block';
    
    // Update active button styling
    document.querySelectorAll('#profileSubNav button').forEach(btn => {
        if(btn.dataset.tab === tabName) {
            btn.classList.remove('btn-outline');
            btn.classList.add('btn-primary');
            btn.style.background = 'var(--danger)';
            btn.style.borderColor = 'var(--danger)';
        } else {
            btn.classList.add('btn-outline');
            btn.classList.remove('btn-primary');
            btn.style.background = '';
            btn.style.borderColor = '';
        }
    });

    // Trigger load logic if needed
    if(tabName === 'posts') {
        renderProfilePosts();
    } else if(tabName === 'chat') {
        loadChatHistory();
    } else if(tabName === 'notifications') {
        renderProfileNotifications();
    } else if(tabName === 'settings') {
        // Sync toggle states
        document.getElementById('settDarkToggle').checked = document.body.classList.contains('dark');
        document.getElementById('settGlassToggle').checked = document.body.classList.contains('glass-mode');
        // Sync palette swatches
        const cur = localStorage.getItem('susl_palette') || '';
        document.querySelectorAll('.palette-swatch').forEach((s,i) => {
            const map = ['','palette-ocean','palette-forest','palette-sunset','palette-purple'];
            s.classList.toggle('active', map[i] === cur);
        });
    }
}

function renderProfileRecentPosts(userId) {
    const container = document.getElementById('profRecentPosts');
    const myPosts = posts.filter(p => p.user_id == userId).slice(0, 3);
    
    if(!myPosts.length) {
        container.innerHTML = '<p style="color:var(--text-muted);font-size:13px;padding:12px 0;">No recent posts found.</p>';
        return;
    }
    
    container.innerHTML = myPosts.map(p => `
        <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--border);">
            <strong style="font-size:14px;">${escHtml(p.text.substring(0, 50))}${p.text.length > 50 ? '...' : ''}</strong>
            <span style="font-size:12px;color:var(--text-muted);">${p.time}</span>
        </div>
    `).join('');
}

function renderProfilePosts() {
    const userStr = localStorage.getItem('susl_user');
    if(!userStr) return;
    const user = JSON.parse(userStr);
    
    const container = document.getElementById('profileMyPostsFeed');
    const myPosts = posts.filter(p => p.user_id == user.id);
    
    if(!myPosts.length) {
        container.innerHTML = '<p style="color:var(--text-muted);font-size:13px;padding:20px 0;">You have not posted anything yet.</p>';
        return;
    }
    
    container.innerHTML = myPosts.map(p => `
        <div class="post-card" style="margin-top:16px;">
            <div class="post-meta">
                <div class="author-chip">
                    <div class="avatar-sm">${p.initials}</div>
                    ${p.author}
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span class="tag tag-blue" style="font-size:11px;">${p.category}</span>
                    <span style="font-size:11px;color:var(--text-muted);">${p.time}</span>
                </div>
            </div>
            <p style="font-size:14px;line-height:1.6;">${escHtml(p.text)}</p>
        </div>
    `).join('');
}

async function loadChatHistory() {
    const container = document.getElementById('profileChatHistory');
    if(!isLoggedIn) {
        container.innerHTML = '<p style="color:var(--text-muted);font-size:13px;text-align:center;">Please sign in to view chat history.</p>';
        return;
    }
    
    try {
        const data = await apiFetch('/api/v1/chat/history');
        if(data && data.messages && data.messages.length > 0) {
            container.innerHTML = data.messages.map(m => `
                <div style="margin-bottom:16px;padding:12px;background:${m.role === 'user' ? 'var(--bg)' : 'var(--border)'};border-radius:8px;">
                    <strong style="font-size:12px;color:${m.role === 'user' ? 'var(--primary)' : 'var(--danger)'};text-transform:uppercase;">
                        ${m.role === 'user' ? 'You' : 'SmartUniMate AI'}
                    </strong>
                    <div style="font-size:13px;margin-top:4px;line-height:1.5;">${escHtml(m.message)}</div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p style="color:var(--text-muted);font-size:13px;text-align:center;">No chat history found.</p>';
        }
    } catch(e) {
        container.innerHTML = '<p style="color:var(--danger);font-size:13px;text-align:center;">Failed to load chat history.</p>';
    }
}

function renderProfileNotifications() {
    const container = document.getElementById('profileNotificationsList');
    if(!NOTIFICATIONS.length) {
        container.innerHTML = '<p style="color:var(--text-muted);font-size:13px;">No notifications yet.</p>';
        return;
    }
    
    container.innerHTML = NOTIFICATIONS.map(n => `
        <div style="padding:16px 0;border-bottom:1px solid var(--border);display:flex;gap:12px;align-items:flex-start;">
            <div style="width:8px;height:8px;border-radius:50%;background:${n.read ? 'transparent' : 'var(--danger)'};margin-top:6px;"></div>
            <div>
                <div style="font-size:14px;font-weight:${n.read ? '400' : '600'};">${n.text}</div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">${n.time}</div>
            </div>
        </div>
    `).join('');
}

async function submitChangePassword() {
    const currPw = document.getElementById('setCurrPw').value;
    const newPw = document.getElementById('setNewPw').value;
    const confPw = document.getElementById('setConfPw').value;
    
    const errEl = document.getElementById('pwErrorMsg');
    const sucEl = document.getElementById('pwSuccessMsg');
    errEl.style.display = 'none';
    sucEl.style.display = 'none';
    
    if(!currPw || !newPw || !confPw) {
        errEl.textContent = 'Please fill all password fields.';
        errEl.style.display = 'block';
        return;
    }
    
    try {
        const data = await apiFetch('/api/v1/profile/password', {
            method: 'PUT',
            body: JSON.stringify({
                current_password: currPw,
                password: newPw,
                password_confirmation: confPw
            })
        });
        
        sucEl.textContent = 'Password updated successfully!';
        sucEl.style.display = 'block';
        document.getElementById('setCurrPw').value = '';
        document.getElementById('setNewPw').value = '';
        document.getElementById('setConfPw').value = '';
    } catch(e) {
        errEl.textContent = e.message;
        errEl.style.display = 'block';
    }
}

function toggleProfileEdit() {
    const inputs = ['profName', 'profPhone', 'profFaculty']; // Editable fields
    const isEditing = !document.getElementById('profName').disabled;
    
    inputs.forEach(id => {
        document.getElementById(id).disabled = isEditing;
    });
    
    if (isEditing) {
        document.getElementById('profSaveRow').style.display = 'none';
        document.getElementById('btnProfileEdit').innerHTML = '<i class="fa-solid fa-pen"></i> Edit';
    } else {
        document.getElementById('profSaveRow').style.display = 'block';
        document.getElementById('btnProfileEdit').innerHTML = '<i class="fa-solid fa-xmark"></i> Cancel';
    }
}

async function saveProfileDetails() {
    if (!isLoggedIn) return;
    try {
        const payload = {
            name: document.getElementById('profName').value,
            phone: document.getElementById('profPhone').value,
            faculty: document.getElementById('profFaculty').value
        };
        const data = await apiFetch('/api/v1/profile', {
            method: 'PUT',
            body: JSON.stringify(payload)
        });
        
        localStorage.setItem('susl_user', JSON.stringify(data.student));
        updateProfileUI(data.student);
        toggleProfileEdit(); // turn off edit mode
        addNotif('Profile updated successfully!');
    } catch(e) {
        addNotif('⚠️ Failed to update profile: ' + e.message);
    }
}

function updateAuthBtn() {
    const btn = document.getElementById('authBtn');
    if(!btn) return;
    btn.innerHTML = isLoggedIn
        ? '<i class="fa-solid fa-right-from-bracket"></i> Sign Out'
        : '<i class="fa-brands fa-microsoft"></i> Sign In';
}

function toggleAuth() {
    if(isLoggedIn) {
        doLogout();
    } else {
        document.getElementById('authErrorMsg').style.display = 'none';
        document.getElementById('authModalOverlay').style.display = 'flex';
        toggleAuthMode('login');
    }
}

function closeAuthModal() {
    document.getElementById('authModalOverlay').style.display = 'none';
}

function toggleAuthMode(mode) {
    document.getElementById('authErrorMsg').style.display = 'none';
    if(mode === 'login') {
        document.getElementById('authTitle').textContent = 'Sign In';
        document.getElementById('loginForm').style.display = 'block';
        document.getElementById('registerForm').style.display = 'none';
        document.getElementById('forgotForm').style.display = 'none';
    } else if(mode === 'register') {
        document.getElementById('authTitle').textContent = 'Create Account';
        document.getElementById('loginForm').style.display = 'none';
        document.getElementById('registerForm').style.display = 'block';
        document.getElementById('forgotForm').style.display = 'none';
    } else {
        document.getElementById('authTitle').textContent = 'Forgot Password';
        document.getElementById('loginForm').style.display = 'none';
        document.getElementById('registerForm').style.display = 'none';
        document.getElementById('forgotForm').style.display = 'block';
    }
}

async function submitLogin() {
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    const role = document.getElementById('loginRole').value;
    const errEl = document.getElementById('authErrorMsg');
    
    if(!email || !password) {
        errEl.textContent = "Please enter both email and password.";
        errEl.style.display = 'block';
        return;
    }
    
    try {
        const endpoint = role === 'admin' ? '/api/v1/admin/login' : '/api/v1/login';
        const data = await apiFetch(endpoint, {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });
        
        if(data && data.token) {
            localStorage.setItem('susl_token', data.token);
            localStorage.setItem('susl_role', role);
            
            if (role === 'student' && data.student) {
                localStorage.setItem('susl_user', JSON.stringify(data.student));
                addNotif(`Welcome back, ${data.student.name}!`);
            } else if (role === 'admin' && data.admin) {
                localStorage.setItem('susl_user', JSON.stringify(data.admin));
                addNotif('Admin access granted.');
                nav('admin');
            } else {
                addNotif('Access granted.');
                nav('admin');
            }
            
            initAuth();
            closeAuthModal();
            
            // clear form
            document.getElementById('loginEmail').value = '';
            document.getElementById('loginPassword').value = '';
        }
    } catch(e) {
        let msg = e.message;
        if(msg.includes('HTTP error!') || msg.includes('Invalid')) msg = "Invalid credentials.";
        errEl.textContent = msg;
        errEl.style.display = 'block';
    }
}

async function submitRegister() {
    const payload = {
        name: document.getElementById('regName').value,
        email: document.getElementById('regEmail').value,
        student_id: document.getElementById('regStudentId').value,
        year: parseInt(document.getElementById('regYear').value) || 1,
        faculty: document.getElementById('regFaculty').value,
        password: document.getElementById('regPassword').value,
        password_confirmation: document.getElementById('regPasswordConfirm').value
    };
    const errEl = document.getElementById('authErrorMsg');
    
    try {
        const data = await apiFetch('/api/v1/register', {
            method: 'POST',
            body: JSON.stringify(payload)
        });
        
        if(data && data.token) {
            localStorage.setItem('susl_token', data.token);
            localStorage.setItem('susl_role', 'student');
            localStorage.setItem('susl_user', JSON.stringify(data.student));
            initAuth();
            closeAuthModal();
            addNotif('Registration successful! Welcome to UniMate.');
        }
    } catch(e) {
        let msg = e.message;
        if(msg.includes('HTTP error!')) msg = "Registration failed. Please check your inputs.";
        errEl.textContent = msg;
        errEl.style.display = 'block';
    }
}

async function submitForgotPassword() {
    const payload = {
        email: document.getElementById('forgotEmail').value,
        student_id: document.getElementById('forgotId').value,
        password: document.getElementById('forgotPassword').value,
        password_confirmation: document.getElementById('forgotConfirm').value
    };
    const errEl = document.getElementById('authErrorMsg');

    try {
        const data = await apiFetch('/api/v1/forgot-password', {
            method: 'POST',
            body: JSON.stringify(payload)
        });

        addNotif(data.message || 'Password reset successfully.');
        toggleAuthMode('login');
        document.getElementById('forgotPassword').value = '';
        document.getElementById('forgotConfirm').value = '';
    } catch(e) {
        errEl.textContent = e.message;
        errEl.style.display = 'block';
    }
}

async function doLogout() {
    try {
        const role = localStorage.getItem('susl_role');
        const endpoint = role === 'admin' ? '/api/v1/admin/logout' : '/api/v1/logout';
        await apiFetch(endpoint, { method: 'POST' });
    } catch(e) {}
    
    localStorage.removeItem('susl_token');
    localStorage.removeItem('susl_user');
    localStorage.removeItem('susl_role');
    
    // Redirect to home if on admin page
    if(document.querySelector('.section.active').id === 'admin') {
        nav('home');
    }
    
    initAuth();
    addNotif('You have been signed out.');
}

// ── NOTIFICATIONS ──
function renderNotifications() {
    const unread = NOTIFICATIONS.filter(n => !n.read).length;
    const badge = document.getElementById('notifBadge');
    badge.style.display = unread > 0 ? 'block' : 'none';
    const list = document.getElementById('notifList');
    list.innerHTML = NOTIFICATIONS.map(n => `
        <div class="notif-item">
            <div class="notif-dot ${n.read ? 'read' : ''}"></div>
            <div>
                <div class="notif-text">${n.text}</div>
                <div class="notif-time">${n.time}</div>
            </div>
        </div>
    `).join('');
}

function toggleNotifPanel() {
    notifPanelOpen = !notifPanelOpen;
    document.getElementById('notifPanel').classList.toggle('open', notifPanelOpen);
}

function markAllRead() {
    NOTIFICATIONS.forEach(n => n.read = true);
    renderNotifications();
}

function addNotif(text) {
    NOTIFICATIONS.unshift({ text, time:'Just now', read:false });
    renderNotifications();
}

// ── AI CHAT (Claude API) ──
async function sendChat() {
    const input = document.getElementById('chatInput');
    const box = document.getElementById('chatBox');
    const msg = input.value.trim();
    if(!msg) return;
    input.value = '';

    box.innerHTML += `<div class="msg user">${escHtml(msg)}</div>`;
    scrollChat();

    const tid = 'typing_' + Date.now();
    box.innerHTML += `<div class="msg bot typing" id="${tid}">UniMate is thinking...</div>`;
    scrollChat();

    try {
        const resp = await apiFetch('/api/v1/chat', {
            method: 'POST',
            body: JSON.stringify({ message: msg })
        });
        
        document.getElementById(tid)?.remove();
        
        if (resp && resp.reply) {
            box.innerHTML += `<div class="msg bot">${resp.reply}</div>`;
        } else {
            box.innerHTML += `<div class="msg bot">⚠️ Something went wrong.</div>`;
        }
    } catch(e) {
        document.getElementById(tid)?.remove();
        if (e.message.includes('401')) {
            box.innerHTML += `<div class="msg bot">⚠️ Unauthorized. Please Sign In first to use the AI chat!</div>`;
        } else {
            box.innerHTML += `<div class="msg bot">⚠️ Connection error. Please check your network and try again.</div>`;
        }
    }
    scrollChat();
}

function quickAsk(q) {
    document.getElementById('chatInput').value = q;
    sendChat();
    nav('chatbot');
}

function scrollChat() {
    const box = document.getElementById('chatBox');
    box.scrollTop = box.scrollHeight;
}

function clearChat() {
    document.getElementById('chatBox').innerHTML = '<div class="msg bot">Chat cleared. How can I help you?</div>';
}

function escHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ── ACADEMIC SEARCH ──
function doSearch() {
    const q = document.getElementById('academicSearch').value.trim().toLowerCase();
    const container = document.getElementById('searchResults');
    if(!q) { container.innerHTML = ''; renderAllModules(); return; }
    const results = MODULES_DB.filter(m =>
        m.code.toLowerCase().includes(q) || m.name.toLowerCase().includes(q)
    );
    if(!results.length) {
        container.innerHTML = '<div class="card"><p style="color:var(--text-muted);">No modules found matching your query.</p></div>';
    } else {
        container.innerHTML = `<div class="card"><h3 class="section-title">Search Results</h3>` +
            results.map(m => `
            <div class="module-result">
                <h4><strong>${m.code}</strong> — ${m.name} <span class="tag tag-blue">${m.credits} Credits</span></h4>
                <p>${m.desc}</p>
                <p style="margin-top:4px;font-size:12px;">👤 ${m.faculty} &nbsp;|&nbsp; Pre-req: ${m.prereq}</p>
            </div>`).join('') + `</div>`;
    }
}

function renderAllModules() {
    const container = document.getElementById('allModules');
    if (!MODULES_DB || !MODULES_DB.length) {
        container.innerHTML = '<div class="card"><p style="color:var(--text-muted);">No modules available.</p></div>';
        return;
    }
    container.innerHTML = MODULES_DB.map(m => `
        <div class="module-result">
            <h4><strong>${m.code}</strong> — ${m.name} <span class="tag tag-blue">${m.credits} Cr</span></h4>
            <p style="font-size:12px;">${m.faculty || 'Unassigned'} · Pre-req: ${m.prereq || 'None'}</p>
        </div>`).join('');
}

// ── TIMETABLE ──
async function fetchTimetable() {
    if(!isLoggedIn) {
        timetable = {};
        renderTimetable();
        return;
    }
    try {
        const data = await apiFetch('/api/v1/timetable');
        if(data && data.timetable) {
            timetable = {}; // Clear old data
            // Map backend data to frontend grid
            const dayMap = {'Monday':'Mon', 'Tuesday':'Tue', 'Wednesday':'Wed', 'Thursday':'Thu', 'Friday':'Fri', 'Saturday':'Sat'};
            
            for(const [fullDay, classes] of Object.entries(data.timetable)) {
                const shortDay = dayMap[fullDay];
                if(!shortDay) continue;
                
                classes.forEach(c => {
                    // Match start time to slot index
                    const slotIndex = TT_SLOTS.findIndex(s => s.startsWith(c.start_time.substring(0,5).replace(/^0/,'')));
                    if(slotIndex !== -1) {
                        timetable[`${shortDay}-${slotIndex}`] = { code: c.subject, name: c.subject, room: c.room, id: c.id };
                    }
                });
            }
            renderTimetable();
        }
    } catch(e) {
        console.error("Failed to fetch timetable", e);
    }
}
function renderTimetable() {
    const grid = document.getElementById('ttGrid');
    const today = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'][new Date().getDay()];
    
    let html = `<div class="tt-header" style="border-radius:6px;"></div>`;
    TT_DAYS.forEach(d => {
        const isToday = (d === today);
        if (isToday) {
            html += `<div class="tt-header" style="background:var(--danger);color:#fff;border-radius:4px;font-weight:700;box-shadow:0 0 10px rgba(192, 57, 43, 0.4);">${d} (Today)</div>`;
        } else {
            html += `<div class="tt-header">${d}</div>`;
        }
    });
    
    TT_SLOTS.forEach((slot, si) => {
        html += `<div class="tt-time">${slot.split('–')[0]}</div>`;
        TT_DAYS.forEach(day => {
            const key = `${day}-${si}`;
            const cls = timetable[key];
            const isToday = (day === today);
            const todayStyle = isToday ? 'border: 2px solid rgba(192, 57, 43, 0.5); background: rgba(192, 57, 43, 0.04);' : '';
            
            if(cls) {
                html += `<div class="tt-cell filled" onclick="openAddClass('${day}',${si})" style="${todayStyle}">
                    <div class="mod-code">${cls.code}</div>
                    <div class="mod-room">${cls.room}</div>
                </div>`;
            } else {
                html += `<div class="tt-cell empty" onclick="openAddClass('${day}',${si})" title="Add class" style="${todayStyle}"></div>`;
            }
        });
    });
    grid.innerHTML = html;

    // Render calendar views and home metrics updates
    renderCalendar();
    updateHomeDashboardMetrics();

    // Render today's classes
    const todayClasses = Object.entries(timetable)
        .filter(([k]) => k.startsWith(today))
        .map(([k,v]) => {
            const si = parseInt(k.split('-')[1]);
            return { slot: TT_SLOTS[si], ...v };
        });
    const tc = document.getElementById('todayClasses');
    if(!todayClasses.length) {
        tc.innerHTML = '<p style="color:var(--text-muted);font-size:13px;padding:8px 0;">No classes scheduled today.</p>';
    } else {
        tc.innerHTML = todayClasses.map(c =>
            `<div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid var(--border);">
                <span style="font-size:12px;color:var(--text-muted);min-width:90px;">${c.slot}</span>
                <strong>${c.code}</strong>
                <span style="color:var(--text-muted);font-size:13px;">${c.room}</span>
            </div>`
        ).join('');
    }
}

// ── CALENDAR & DYNAMIC METRICS HELPERS ──
let currentCalDate = new Date();

function updateHomeDashboardMetrics() {
    // 1. Enrolled Modules
    const enrolledEl = document.getElementById('homeEnrolledModules');
    if (enrolledEl) {
        enrolledEl.textContent = gpaModules.length;
    }
    
    // 2. GPA
    let totalCredits = 0, totalPoints = 0;
    gpaModules.forEach(m => {
        totalCredits += m.credits;
        totalPoints += GRADE_POINTS[m.grade] * m.credits;
    });
    const gpa = totalCredits > 0 ? (totalPoints / totalCredits) : 0;
    const gpaEl = document.getElementById('homeGPA');
    if (gpaEl) {
        gpaEl.textContent = gpa.toFixed(2);
    }
    
    // 3. Classes This Week
    const classesEl = document.getElementById('homeClassesThisWeek');
    if (classesEl) {
        classesEl.textContent = Object.keys(timetable).length;
    }

    // 4. Dynamic Alerts
    const alertsContainer = document.getElementById('homeAlertsContainer');
    if (alertsContainer) {
        const unreadDeadlines = NOTIFICATIONS.filter(n => !n.read && (n.text.toLowerCase().includes('proposal') || n.text.toLowerCase().includes('due') || n.text.toLowerCase().includes('deadline')));
        if (unreadDeadlines.length > 0) {
            alertsContainer.innerHTML = unreadDeadlines.map(n => `
                <div class="card card-sm" style="display:flex;gap:12px;align-items:center;margin-bottom:12px;border-left:4px solid var(--danger);">
                    <i class="fa-solid fa-circle-exclamation" style="color:var(--danger);font-size:18px;"></i>
                    <div style="flex:1;">
                        <div style="font-weight:600;font-size:14px;">${escHtml(n.text)}</div>
                        <div style="font-size:12px;color:var(--text-muted);">${n.time}</div>
                    </div>
                </div>
            `).join('');
        } else {
            alertsContainer.innerHTML = `
                <div class="card card-sm" style="display:flex;gap:12px;align-items:center;">
                    <i class="fa-solid fa-circle-check" style="color:#2ecc71;font-size:18px;"></i>
                    <div>
                        <div style="font-weight:600;font-size:14px;">You are all caught up!</div>
                        <div style="font-size:12px;color:var(--text-muted);">No urgent proposals or deadlines in the next 3 days.</div>
                    </div>
                </div>
            `;
        }
    }
}

function renderCalendar() {
    const container = document.getElementById('calGridContainer');
    const monthTitle = document.getElementById('calMonthTitle');
    if (!container || !monthTitle) return;

    const year = currentCalDate.getFullYear();
    const month = currentCalDate.getMonth();

    const monthNames = ["January", "February", "March", "April", "May", "June",
                        "July", "August", "September", "October", "November", "December"];
    monthTitle.textContent = `${monthNames[month]} ${year}`;

    let html = "";
    
    // Day Headers
    const daysOfWeek = ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"];
    daysOfWeek.forEach(d => {
        html += `<div style="font-weight:700;color:var(--text-muted);padding:4px 0;">${d}</div>`;
    });

    const firstDayIndex = new Date(year, month, 1).getDay();
    const numDays = new Date(year, month + 1, 0).getDate();
    const today = new Date();

    for (let i = 0; i < firstDayIndex; i++) {
        html += `<div style="padding:8px 0;opacity:0.2;"></div>`;
    }

    for (let d = 1; d <= numDays; d++) {
        const dateObj = new Date(year, month, d);
        const isToday = dateObj.toDateString() === today.toDateString();
        const hasClasses = timetableHasClassesForDay(dateObj.getDay());
        
        let style = "padding:8px 0;border-radius:4px;cursor:pointer;position:relative;";
        if (isToday) {
            style += "background:var(--danger);color:#fff;font-weight:700;box-shadow:0 0 8px rgba(192, 57, 43, 0.4);";
        } else if (hasClasses) {
            style += "background:rgba(192, 57, 43, 0.08);color:var(--danger);font-weight:600;";
        } else {
            style += "hover:background:var(--surface2);";
        }

        const classDot = (hasClasses && !isToday) ? `<span style="position:absolute;bottom:2px;left:50%;transform:translateX(-50%);width:4px;height:4px;border-radius:50%;background:var(--danger);"></span>` : "";

        html += `<div style="${style}" onclick="selectCalendarDate(${d})" class="cal-date-cell">
            ${d}
            ${classDot}
        </div>`;
    }

    container.innerHTML = html;
}

function timetableHasClassesForDay(dayIndex) {
    const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const dayStr = daysOfWeek[dayIndex];
    return Object.keys(timetable).some(k => k.startsWith(dayStr));
}

function selectCalendarDate(dayNum) {
    const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const year = currentCalDate.getFullYear();
    const month = currentCalDate.getMonth();
    const targetDate = new Date(year, month, dayNum);
    const dayStr = daysOfWeek[targetDate.getDay()];
    
    document.getElementById('scheduleDayTitle').textContent = targetDate.toLocaleDateString(undefined, { weekday: 'long', month: 'short', day: 'numeric' });
    
    const dayClasses = Object.entries(timetable)
        .filter(([k]) => k.startsWith(dayStr))
        .map(([k,v]) => {
            const si = parseInt(k.split('-')[1]);
            return { slot: TT_SLOTS[si], ...v };
        });

    const tc = document.getElementById('todayClasses');
    if(!dayClasses.length) {
        tc.innerHTML = '<p style="color:var(--text-muted);font-size:13px;padding:8px 0;">No classes scheduled for this day.</p>';
    } else {
        tc.innerHTML = dayClasses.map(c =>
            `<div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid var(--border);">
                <span style="font-size:12px;color:var(--text-muted);min-width:90px;">${c.slot}</span>
                <strong>${c.code}</strong>
                <span style="color:var(--text-muted);font-size:13px;">${c.room}</span>
            </div>`
        ).join('');
    }
}

function prevMonth() {
    currentCalDate.setMonth(currentCalDate.getMonth() - 1);
    renderCalendar();
}
function nextMonth() {
    currentCalDate.setMonth(currentCalDate.getMonth() + 1);
    renderCalendar();
}

async function handleIcsUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    document.getElementById('icsUploadStatus').textContent = "Processing " + file.name + "...";

    const reader = new FileReader();
    reader.onload = async function(e) {
        const text = e.target.result;
        try {
            const parsedEvents = parseIcsFile(text);
            if (parsedEvents.length === 0) {
                throw new Error("No valid weekly events or subject details found.");
            }

            let importCount = 0;
            const dayMapFull = {'Mon':'Monday', 'Tue':'Tuesday', 'Wed':'Wednesday', 'Thu':'Thursday', 'Fri':'Friday'};
            
            for (const ev of parsedEvents) {
                const slotIndex = TT_SLOTS.findIndex(s => s.startsWith(ev.start_time));
                if (slotIndex !== -1 && dayMapFull[ev.day]) {
                    if (isLoggedIn) {
                        const dayFull = dayMapFull[ev.day];
                        const slotTimeStr = TT_SLOTS[slotIndex];
                        const [startStr, endStr] = slotTimeStr.split('–');
                        const padTime = t => (t.length === 4 ? '0'+t : t);
                        
                        await apiFetch('/api/v1/timetable', {
                            method: 'POST',
                            body: JSON.stringify({
                                day: dayFull,
                                start_time: padTime(startStr) + ':00',
                                end_time: padTime(endStr) + ':00',
                                subject: ev.summary,
                                room: ev.location || 'Online'
                            })
                        });
                        importCount++;
                    } else {
                        timetable[`${ev.day}-${slotIndex}`] = { code: ev.summary, name: ev.summary, room: ev.location || 'Online' };
                        importCount++;
                    }
                }
            }

            document.getElementById('icsUploadStatus').textContent = `Success! Imported ${importCount} classes.`;
            addNotif(`📅 Successfully imported ${importCount} classes from calendar!`);
            
            if (isLoggedIn) {
                await fetchTimetable();
            } else {
                renderTimetable();
            }
        } catch(err) {
            document.getElementById('icsUploadStatus').textContent = "Failed to import.";
            addNotif("⚠️ Calendar import failed: " + err.message);
        }
    };
    reader.readAsText(file);
}

function parseIcsFile(icsText) {
    const events = [];
    const lines = icsText.split(/\r?\n/);
    let currentEvent = null;

    for (let i = 0; i < lines.length; i++) {
        let line = lines[i] ? lines[i].trim() : '';
        if (!line) continue;
        
        while (i + 1 < lines.length && (lines[i+1].startsWith(' ') || lines[i+1].startsWith('\t'))) {
            line += lines[i+1].substring(1);
            i++;
        }

        if (line === "BEGIN:VEVENT") {
            currentEvent = {};
        } else if (line === "END:VEVENT") {
            if (currentEvent && currentEvent.summary && currentEvent.start_time && currentEvent.day) {
                events.push(currentEvent);
            }
            currentEvent = null;
        } else if (currentEvent) {
            if (line.startsWith("SUMMARY:")) {
                currentEvent.summary = line.substring(8).trim();
            } else if (line.startsWith("LOCATION:")) {
                currentEvent.location = line.substring(9).trim();
            } else if (line.startsWith("DTSTART")) {
                const parts = line.split(":");
                const val = parts[parts.length - 1];
                const timeMatch = val.match(/T(\d{2})(\d{2})/);
                if (timeMatch) {
                    let hour = parseInt(timeMatch[1]);
                    let min = timeMatch[2];
                    currentEvent.start_time = `${hour}:${min}`;
                }
                
                const dateMatch = val.match(/^(\d{4})(\d{2})(\d{2})/);
                if (dateMatch) {
                    const y = parseInt(dateMatch[1]);
                    const m = parseInt(dateMatch[2]) - 1;
                    const d = parseInt(dateMatch[3]);
                    const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    currentEvent.day = daysOfWeek[new Date(y, m, d).getDay()];
                }
            } else if (line.startsWith("RRULE:")) {
                const byDayMatch = line.match(/BYDAY=([A-Z,]+)/);
                if (byDayMatch) {
                    const fullDay = byDayMatch[1].split(',')[0];
                    const map = {'MO':'Mon', 'TU':'Tue', 'WE':'Wed', 'TH':'Thu', 'FR':'Fri'};
                    if (map[fullDay]) {
                        currentEvent.day = map[fullDay];
                    }
                }
            }
        }
    }
    return events;
}

function openAddClass(day, slot) {
    if(day) { document.getElementById('mc_day').value = day; document.getElementById('mc_slot').value = slot; }
    document.getElementById('modalOverlay').style.display = 'flex';
}
function closeModal() { document.getElementById('modalOverlay').style.display = 'none'; }
async function saveClass() {
    const code = document.getElementById('mc_code').value.trim();
    const room = document.getElementById('mc_room').value.trim();
    const shortDay = document.getElementById('mc_day').value;
    const slot = document.getElementById('mc_slot').value;
    if(!code) return;
    
    if(!isLoggedIn) {
        addNotif('⚠️ You must be signed in to save timetable entries!');
        closeModal();
        toggleAuth();
        return;
    }

    const dayMapRev = {'Mon':'Monday', 'Tue':'Tuesday', 'Wed':'Wednesday', 'Thu':'Thursday', 'Fri':'Friday'};
    const slotTimeStr = TT_SLOTS[slot]; // e.g. "8:00–9:00"
    const [startStr, endStr] = slotTimeStr.split('–');
    
    const padTime = t => (t.length === 4 ? '0'+t : t);
    
    try {
        await apiFetch('/api/v1/timetable', {
            method: 'POST',
            body: JSON.stringify({
                subject: code,
                room: room || 'TBA',
                lecturer: 'TBA',
                day: dayMapRev[shortDay],
                start_time: padTime(startStr),
                end_time: padTime(endStr)
            })
        });
        
        closeModal();
        addNotif('Class added to timetable!');
        fetchTimetable(); // Reload from server
    } catch(e) {
        addNotif('⚠️ Failed to save class. Please try again.');
    }
}

// ── GPA ──
function renderGpa() {
    const grades = Object.keys(GRADE_POINTS);
    const container = document.getElementById('gpaModules');
    container.innerHTML = gpaModules.map((m, i) => `
        <div class="module-row">
            <input type="text" value="${m.name}" style="font-size:13px;" onchange="gpaModules[${i}].name=this.value;calcGpa()">
            <input type="number" value="${m.credits}" min="1" max="12" style="font-size:13px;" onchange="gpaModules[${i}].credits=+this.value;calcGpa()">
            <select style="font-size:13px;" onchange="gpaModules[${i}].grade=this.value;calcGpa()">
                ${grades.map(g => `<option ${g===m.grade?'selected':''}>${g}</option>`).join('')}
            </select>
            <span style="font-weight:700;color:var(--primary);text-align:right;">${(GRADE_POINTS[m.grade]*m.credits).toFixed(1)}</span>
        </div>
    `).join('');
    calcGpa();
}

function calcGpa() {
    let totalCredits = 0, totalPoints = 0;
    gpaModules.forEach(m => {
        totalCredits += m.credits;
        totalPoints += GRADE_POINTS[m.grade] * m.credits;
    });
    const gpa = totalCredits > 0 ? (totalPoints / totalCredits) : 0;
    const pct = (gpa / 4.0) * 360;
    document.getElementById('gpaVal').textContent = gpa.toFixed(2);
    document.getElementById('gpaRing').style.setProperty('--gpa-deg', pct + 'deg');
    document.getElementById('totalCredits').textContent = totalCredits;
    document.getElementById('totalPoints').textContent = totalPoints.toFixed(1);
    document.getElementById('homeGPA').textContent = gpa.toFixed(2);
    let cls = '—';
    if(gpa >= 3.7) cls = '🎓 First Class';
    else if(gpa >= 3.3) cls = 'Upper Second Class';
    else if(gpa >= 3.0) cls = 'Second Class';
    else if(gpa >= 2.0) cls = 'Pass';
    else if(gpa > 0) cls = 'Fail';
    document.getElementById('gpaClass').textContent = cls;
    localStorage.setItem('susl_gpa', JSON.stringify(gpaModules));
    if (typeof updateHomeDashboardMetrics === 'function') {
        updateHomeDashboardMetrics();
    }
}

function addGpaModule() {
    gpaModules.push({ name:'New Module', credits:3, grade:'B' });
    renderGpa();
}

// ── COMMUNITY ──
async function fetchPosts() {
    try {
        const data = await apiFetch('/api/v1/communities');
        if(data) {
            posts = data.map(p => {
                const imgStore = JSON.parse(localStorage.getItem('susl_post_images') || '{}');
                return {
                    id: p.id,
                    user_id: p.user_id,
                    author: p.student ? p.student.name : 'Unknown Student',
                    initials: p.student ? (p.student.name || 'U').split(' ').map(n=>n[0]).join('').substring(0,2).toUpperCase() : 'US',
                    text: p.post_content,
                    category: p.description || 'General',
                    time: new Date(p.created_at).toLocaleDateString(),
                    likes: 0,
                    image: imgStore[p.post_content.substring(0,50)] || null
                };
            });
            renderPosts(currentFilter);
            renderAdminModQueue();
            
            // If on profile page, re-render recent posts dynamically
            const userStr = localStorage.getItem('susl_user');
            const role = localStorage.getItem('susl_role');
            if(userStr) {
                const u = JSON.parse(userStr);
                if (role === 'admin') {
                    updateAdminProfileUI(u);
                } else {
                    renderProfileRecentPosts(u.id);
                    if (document.getElementById('profView-posts').style.display === 'block') {
                        renderProfilePosts();
                    }
                }
            }
        }
    } catch(e) {
        console.error("Failed to fetch posts", e);
    }
}
function renderPosts(filter) {
    const container = document.getElementById('feedContainer');
    const filtered = filter && filter !== 'All' ? posts.filter(p => p.category === filter) : posts;
    if(!filtered.length) {
        container.innerHTML = '<p style="color:var(--text-muted);font-size:13px;text-align:center;padding:20px;">No posts yet. Be the first to post!</p>';
        return;
    }
    container.innerHTML = filtered.map(p => {
        const likedPosts = JSON.parse(localStorage.getItem('susl_liked') || '[]');
        const isLiked = likedPosts.includes(p.id);
        const comments = postComments[p.id] || [];
        const commentHtml = comments.map(c => `
            <div class="comment-item">
                <div class="avatar-sm" style="width:24px;height:24px;font-size:10px;flex-shrink:0;">${c.author.split(' ').map(n=>n[0]).join('').substring(0,2).toUpperCase()}</div>
                <div><strong style="font-size:12px;">${escHtml(c.author)}</strong> <span style="color:var(--text-muted);font-size:11px;">${c.time}</span><br>${escHtml(c.text)}</div>
            </div>
        `).join('');
        return `
        <div class="post-card">
            <div class="post-meta">
                <div class="author-chip">
                    <div class="avatar-sm">${p.initials}</div>
                    ${p.author}
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span class="tag tag-blue" style="font-size:11px;">${p.category}</span>
                    <span style="font-size:11px;color:var(--text-muted);">${p.time}</span>
                </div>
            </div>
            <p style="font-size:14px;line-height:1.6;">${escHtml(p.text)}</p>
            ${p.image ? `<img src="${p.image}" class="post-image" alt="post image">` : ''}
            <div class="post-actions">
                <button class="post-action-btn" onclick="likePost(${p.id})" style="${isLiked ? 'color:var(--danger);' : ''}"><i class="fa-${isLiked ? 'solid' : 'regular'} fa-heart"></i> ${p.likes || ''}</button>
                <button class="post-action-btn" onclick="toggleCommentSection(${p.id})"><i class="fa-regular fa-comment"></i> ${comments.length || ''} Comment</button>
                <button class="post-action-btn" onclick="sharePost(${p.id})"><i class="fa-solid fa-share-nodes"></i> Share</button>
            </div>
            <div class="comment-section" id="commentSection_${p.id}" style="display:none;">
                ${commentHtml || '<p style="color:var(--text-muted);font-size:12px;">No comments yet.</p>'}
                <div class="comment-input-row">
                    <input type="text" id="commentInput_${p.id}" placeholder="Write a comment..." onkeypress="if(event.key==='Enter')addComment(${p.id})">
                    <button class="btn btn-primary" onclick="addComment(${p.id})" style="padding:6px 12px;">Post</button>
                </div>
            </div>
        </div>
    `}).join('');
}

function toggleCommentSection(postId) {
    const el = document.getElementById('commentSection_' + postId);
    if(el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

function filterPosts(cat) {
    currentFilter = cat;
    renderPosts(cat);
}

async function submitPost() {
    const text = document.getElementById('postContent').value.trim();
    const category = document.getElementById('postCategory').value;
    if(!text) return;
    
    if(!isLoggedIn) {
        addNotif('⚠️ You must be signed in to post!');
        toggleAuth();
        return;
    }
    
    try {
        await apiFetch('/api/v1/communities', {
            method: 'POST',
            body: JSON.stringify({ post_content: text, description: category })
        });
        
        // If there is a pending image, store it client-side as base64 in the last post
        if(pendingPostImage) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                // We'll store in localStorage keyed by post content
                const imgStore = JSON.parse(localStorage.getItem('susl_post_images') || '{}');
                imgStore[text.substring(0,50)] = ev.target.result;
                localStorage.setItem('susl_post_images', JSON.stringify(imgStore));
                removePostImage();
                fetchPosts();
            };
            reader.readAsDataURL(pendingPostImage);
        } else {
            fetchPosts();
        }
        
        document.getElementById('postContent').value = '';
        addNotif('Your post was published to the community');
    } catch(e) {
        addNotif('⚠️ Failed to publish post. Please try again.');
    }
}

function likePost(id) {
    let likedPosts = JSON.parse(localStorage.getItem('susl_liked') || '[]');
    const p = posts.find(x => x.id == id);
    if(!p) return;
    if(likedPosts.includes(id)) {
        // Unlike
        likedPosts = likedPosts.filter(x => x !== id);
        p.likes = Math.max(0, p.likes - 1);
    } else {
        // Like
        likedPosts.push(id);
        p.likes++;
    }
    localStorage.setItem('susl_liked', JSON.stringify(likedPosts));
    renderPosts(currentFilter);
}

// ── ADMIN ──
function renderAdminModQueue() {
    const table = document.getElementById('modTable');
    if(!table) return;
    if(!posts.length) {
        table.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);">No posts found.</td></tr>';
        return;
    }
    table.innerHTML = posts.map(p => `
        <tr id="arow_${p.id}">
            <td><strong>${escHtml(p.author)}</strong><br><span style="font-size:11px;color:var(--text-muted);">${p.initials}</span></td>
            <td style="max-width:300px;">${escHtml(p.text.substring(0, 100))}${p.text.length > 100 ? '...' : ''}</td>
            <td><span class="tag tag-blue">${p.category}</span></td>
            <td style="font-size:12px;color:var(--text-muted);">${p.time}</td>
            <td><button class="action-btn btn-del" onclick="adminDeletePost(${p.id})"><i class="fa-solid fa-trash"></i> Delete</button></td>
        </tr>
    `).join('');
    // Update stats
    document.getElementById('adminTotalPosts').textContent = posts.length;
    document.getElementById('adminTotalNews').textContent = allNews.length;
    document.getElementById('adminTotalKB').textContent = allKB.length;
}

async function adminDeletePost(postId) {
    if(!confirm('Are you sure you want to delete this post?')) return;
    try {
        await apiFetch('/api/v1/admin/communities/' + postId, { method: 'DELETE' });
        addNotif('Post deleted successfully.');
        // Remove from local array and re-render
        posts = posts.filter(p => p.id !== postId);
        renderAdminModQueue();
    } catch(e) {
        addNotif('⚠️ Failed to delete post: ' + e.message);
    }
}

// ── ADMIN PROFILE & TAB MANAGEMENT ──
function switchAdminTab(tabName) {
    // Hide all admin-view containers
    document.querySelectorAll('.admin-view').forEach(el => el.style.display = 'none');
    
    // Show the targeted one
    const target = document.getElementById(`adminView-${tabName}`);
    if(target) target.style.display = 'block';
    
    // Update active button styling
    document.querySelectorAll('#adminSubNav button').forEach(btn => {
        if(btn.dataset.tab === tabName) {
            btn.classList.remove('btn-outline');
            btn.classList.add('btn-primary');
            btn.style.background = 'var(--danger)';
            btn.style.borderColor = 'var(--danger)';
        } else {
            btn.classList.add('btn-outline');
            btn.classList.remove('btn-primary');
            btn.style.background = '';
            btn.style.borderColor = '';
        }
    });

    if (tabName === 'settings') {
        const toggleTheme = document.getElementById('adminDarkToggle');
        if(toggleTheme) toggleTheme.checked = document.body.classList.contains('dark');
        const toggleGlass = document.getElementById('adminGlassToggle');
        if(toggleGlass) toggleGlass.checked = document.body.classList.contains('glass-mode');
    }
}

function updateAdminProfileUI(admin) {
    if(admin) {
        document.getElementById('adminNameBig').textContent = admin.name || 'System Administrator';
            document.getElementById('adminEmailBig').textContent = admin.email || 'admin@mate.com';
            document.getElementById('adminProfName').value = admin.name || 'System Administrator';
            document.getElementById('adminProfEmail').value = admin.email || 'admin@mate.com';
        // Update stats
        document.getElementById('adminStatPosts').textContent = posts.length;
        document.getElementById('adminStatNews').textContent = allNews.length;
        document.getElementById('adminStatKB').textContent = allKB.length;
    }
}

let isAdminEditing = false;
function toggleAdminProfileEdit() {
    isAdminEditing = !isAdminEditing;
    const btn = document.getElementById('btnAdminProfileEdit');
    const saveRow = document.getElementById('adminProfSaveRow');
    
    document.getElementById('adminProfName').disabled = !isAdminEditing;
    document.getElementById('adminProfEmail').disabled = !isAdminEditing;
    
    if (isAdminEditing) {
        btn.innerHTML = '<i class="fa-solid fa-xmark"></i> Cancel';
        btn.style.background = 'var(--text-muted)';
        btn.style.borderColor = 'var(--text-muted)';
        saveRow.style.display = 'block';
    } else {
        btn.innerHTML = '<i class="fa-solid fa-pen"></i> Edit';
        btn.style.background = 'var(--danger)';
        btn.style.borderColor = 'var(--danger)';
        saveRow.style.display = 'none';
        // revert fields to original user info
        const admin = JSON.parse(localStorage.getItem('susl_user'));
        updateAdminProfileUI(admin);
    }
}

async function saveAdminProfileDetails() {
    const name = document.getElementById('adminProfName').value;
    const email = document.getElementById('adminProfEmail').value;
    
    if(!name || !email) {
        addNotif('⚠️ Name and Email cannot be empty!');
        return;
    }
    
    try {
        const data = await apiFetch('/api/v1/profile', {
            method: 'PUT',
            body: JSON.stringify({ name, email })
        });
        
        if (data && data.student) {
            localStorage.setItem('susl_user', JSON.stringify(data.student));
            updateAdminProfileUI(data.student);
            addNotif('Admin profile updated successfully!');
            toggleAdminProfileEdit();
        }
    } catch(e) {
        addNotif('⚠️ Failed to update admin profile: ' + e.message);
    }
}

function toggleThemeAdmin() {
    toggleTheme();
}

async function changeAdminPassword() {
    const currPw = document.getElementById('adminCurrPw').value;
    const newPw = document.getElementById('adminNewPw').value;
    const confPw = document.getElementById('adminConfPw').value;
    
    const errEl = document.getElementById('adminPwError');
    const sucEl = document.getElementById('adminPwSuccess');
    
    errEl.style.display = 'none';
    sucEl.style.display = 'none';
    
    if(!currPw || !newPw || !confPw) {
        errEl.textContent = 'Please fill all password fields.';
        errEl.style.display = 'block';
        return;
    }
    
    try {
        await apiFetch('/api/v1/profile/password', {
            method: 'PUT',
            body: JSON.stringify({
                current_password: currPw,
                password: newPw,
                password_confirmation: confPw
            })
        });
        
        sucEl.textContent = 'Admin password updated successfully!';
        sucEl.style.display = 'block';
        document.getElementById('adminCurrPw').value = '';
        document.getElementById('adminNewPw').value = '';
        document.getElementById('adminConfPw').value = '';
    } catch(e) {
        errEl.textContent = e.message;
        errEl.style.display = 'block';
    }
}




// ── NEWS ──
let allNews = [];
async function fetchNews() {
    try {
        const data = await apiFetch('/api/v1/news');
        if (data && Array.isArray(data)) {
            allNews = data;
            renderNews();
        }
    } catch(e) { console.error('Failed to fetch news', e); }
}

function renderNews() {
    const grid = document.getElementById('newsGrid');
    if(!grid) return;
    if(allNews.length === 0) {
        grid.innerHTML = '<p style="color:var(--text-muted);grid-column:1/-1;">No news available.</p>';
        return;
    }
    grid.innerHTML = allNews.map(n => `
        <div class="card" style="margin-top:0;">
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                <span class="tag tag-blue">${n.category ? n.category.name : 'Update'}</span>
                <span style="font-size:12px;color:var(--text-muted);">${n.date}</span>
            </div>
            <h3 style="font-size:16px;margin-bottom:8px;">${n.title}</h3>
            ${n.sub_topic ? `<p style="font-size:13px;font-weight:600;color:var(--primary);margin-bottom:8px;">${n.sub_topic}</p>` : ''}
            <p style="font-size:14px;color:var(--text-muted);line-height:1.6;">${n.content}</p>
        </div>
    `).join('');
}

// ── KNOWLEDGE BASE ──
let allKB = [];
async function fetchKB() {
    try {
        const data = await apiFetch('/api/v1/knowledge-bases');
        if(data && Array.isArray(data)) {
            allKB = data;
            filterKB();
        }
    } catch(e) { console.error('Failed to fetch kb', e); }
}

function filterKB() {
    const q = (document.getElementById('kbSearch')?.value || '').toLowerCase();
    const filtered = allKB.filter(k => k.title.toLowerCase().includes(q) || (k.category && k.category.toLowerCase().includes(q)));
    const container = document.getElementById('kbContainer');
    if(!container) return;
    if(filtered.length === 0) {
        container.innerHTML = '<div class="card"><p style="color:var(--text-muted);">No resources found.</p></div>';
        return;
    }
    container.innerHTML = filtered.map(k => `
        <div class="card card-sm" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
            <div>
                <div style="font-weight:600;margin-bottom:4px;">${k.title}</div>
                <div style="font-size:12px;color:var(--text-muted);">
                    <span class="tag tag-green" style="margin-right:8px;">${k.category}</span>
                    Source: ${k.source || 'N/A'}
                </div>
            </div>
            <div style="display:flex;gap:8px;">
                ${k.url ? `<a href="${k.url}" target="_blank" class="btn btn-outline" style="font-size:12px;padding:6px 12px;"><i class="fa-solid fa-arrow-up-right-from-square"></i> Open Link</a>` : ''}
                ${k.file_path ? `<a href="${k.file_path}" download target="_blank" class="btn btn-primary" style="font-size:12px;padding:6px 12px;background:var(--danger);border-color:var(--danger);"><i class="fa-solid fa-file-arrow-down"></i> Download PDF</a>` : ''}
            </div>
        </div>
    `).join('');
}

// ── ADMIN CONTENT MGMT ──
async function submitAdminNews() {
    const title = document.getElementById('adminNewsTitle').value;
    const cat = document.getElementById('adminNewsCat').value;
    const content = document.getElementById('adminNewsContent').value;
    
    if(!title || !content) { addNotif('⚠️ Title and Content are required!'); return; }
    
    try {
        await apiFetch('/api/v1/news', {
            method: 'POST',
            body: JSON.stringify({
                title: title,
                content: content,
                category_id: parseInt(cat),
                date: new Date().toISOString().split('T')[0]
            })
        });
        addNotif('News published successfully!');
        document.getElementById('adminNewsTitle').value = '';
        document.getElementById('adminNewsContent').value = '';
        fetchNews();
    } catch(e) {
        addNotif('⚠️ Failed to publish news: ' + e.message);
    }
}

async function submitAdminKB() {
    const title = document.getElementById('adminKbTitle').value.trim();
    const cat = document.getElementById('adminKbCat').value.trim();
    const source = document.getElementById('adminKbSource').value.trim();
    let url = document.getElementById('adminKbUrl').value.trim();
    const fileInput = document.getElementById('adminKbFile');
    
    if(!title || !cat) { addNotif('⚠️ Title and Category are required!'); return; }
    
    // Auto-fix URL protocol if typed without one
    if (url) {
        if (!/^https?:\/\//i.test(url)) {
            url = 'https://' + url;
        }
    }
    
    const submitBtn = document.getElementById('adminKbSubmitBtn');
    const origHtml = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Uploading...';
    
    try {
        const formData = new FormData();
        formData.append('title', title);
        formData.append('category', cat);
        formData.append('status', 'Active');
        if (source) formData.append('source', source);
        if (url) formData.append('url', url);
        
        if (fileInput && fileInput.files[0]) {
            formData.append('file', fileInput.files[0]);
        }
        
        await apiFetch('/api/v1/knowledge-bases', {
            method: 'POST',
            body: formData
        });
        
        addNotif('Resource added to Knowledge Base!');
        
        // Reset form
        document.getElementById('adminKbTitle').value = '';
        document.getElementById('adminKbCat').value = '';
        document.getElementById('adminKbSource').value = '';
        document.getElementById('adminKbUrl').value = '';
        if (fileInput) fileInput.value = '';
        document.getElementById('adminKbFileName').textContent = 'No file selected';
        
        fetchKB();
    } catch(e) {
        addNotif('⚠️ Failed to add resource: ' + e.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = origHtml;
    }
}

async function loadAdminModules() {
    try {
        const mods = await apiFetch('/api/v1/academic-modules');
        
        // Sync the student-facing DB
        MODULES_DB = mods;
        renderAllModules();
        
        const countEl = document.getElementById('adminModCount');
        if(countEl) countEl.innerText = mods.length;
        
        const tbody = document.getElementById('adminModTable');
        if(!tbody) return;
        
        if (mods.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);">No modules found</td></tr>';
            return;
        }
        
        tbody.innerHTML = mods.map(m => `
            <tr>
                <td style="font-weight:600;color:var(--primary);">${m.code}</td>
                <td>${m.name}</td>
                <td>${m.credits}</td>
                <td>${m.faculty || '-'}</td>
                <td><button class="btn btn-outline" style="color:var(--danger);border-color:var(--danger);padding:4px 8px;font-size:12px;margin:0;" onclick="deleteAdminModule(${m.id})"><i class="fa-solid fa-trash"></i></button></td>
            </tr>
        `).join('');
    } catch(e) {
        console.error('Failed to load modules:', e);
    }
}

async function submitAdminModule() {
    const code = document.getElementById('adminModCode').value.trim();
    const name = document.getElementById('adminModName').value.trim();
    const credits = document.getElementById('adminModCredits').value;
    const faculty = document.getElementById('adminModFaculty').value.trim();
    const prereq = document.getElementById('adminModPrereq').value.trim();
    const desc = document.getElementById('adminModDesc').value.trim();
    
    if(!code || !name || !credits) { addNotif('⚠️ Code, Name, and Credits are required!'); return; }
    
    try {
        await apiFetch('/api/v1/academic-modules', {
            method: 'POST',
            body: JSON.stringify({
                code, name, credits: parseInt(credits), faculty, prereq, desc
            })
        });
        addNotif('Module cataloged successfully!');
        
        document.getElementById('adminModCode').value = '';
        document.getElementById('adminModName').value = '';
        document.getElementById('adminModCredits').value = '';
        document.getElementById('adminModFaculty').value = '';
        document.getElementById('adminModPrereq').value = '';
        document.getElementById('adminModDesc').value = '';
        
        loadAdminModules();
    } catch(e) {
        addNotif('⚠️ Failed to add module: ' + e.message);
    }
}

async function deleteAdminModule(id) {
    if(!confirm("Are you sure you want to delete this module?")) return;
    try {
        await apiFetch('/api/v1/academic-modules/' + id, { method: 'DELETE' });
        addNotif('Module deleted.');
        loadAdminModules();
    } catch(e) {
        addNotif('⚠️ Error deleting module: ' + e.message);
    }
}

// ── ADMIN HOME DASHBOARD HANDLERS ──
async function loadAdminHomeData() {
    const role = localStorage.getItem('susl_role');
    if (role !== 'admin') return;

    try {
        // 1. Modules count
        let mods = MODULES_DB;
        if (!mods || mods.length === 0) {
            mods = await apiFetch('/api/v1/academic-modules');
            MODULES_DB = mods;
        }
        document.getElementById('adminHomeTotalModules').textContent = mods.length;

        // 2. Posts count
        const postsData = await apiFetch('/api/v1/communities');
        if (postsData) {
            posts = postsData.map(p => {
                const imgStore = JSON.parse(localStorage.getItem('susl_post_images') || '{}');
                return {
                    id: p.id,
                    user_id: p.user_id,
                    author: p.student ? p.student.name : 'Unknown Student',
                    initials: p.student ? (p.student.name || 'U').split(' ').map(n=>n[0]).join('').substring(0,2).toUpperCase() : 'US',
                    text: p.post_content,
                    category: p.description || 'General',
                    time: new Date(p.created_at).toLocaleDateString(),
                    likes: 0,
                    image: imgStore[p.post_content.substring(0,50)] || null
                };
            });
        }
        document.getElementById('adminHomeTotalPosts').textContent = posts.length;

        // 3. News count
        if (!allNews || allNews.length === 0) {
            const newsData = await apiFetch('/api/v1/news');
            if (newsData) allNews = newsData;
        }
        document.getElementById('adminHomeTotalNews').textContent = allNews.length;

        // 4. KB count
        if (!allKB || allKB.length === 0) {
            const kbData = await apiFetch('/api/v1/knowledge-bases');
            if (kbData) allKB = kbData;
        }
        document.getElementById('adminHomeTotalKB').textContent = allKB.length;

        // Render Home moderation widget
        renderHomeModWidget();

    } catch (e) {
        console.error("Error loading admin homepage statistics:", e);
    }
}

function renderHomeModWidget() {
    const listContainer = document.getElementById('adminHomeModList');
    const countBadge = document.getElementById('adminHomeModQueueCount');
    if (!listContainer) return;

    if (!posts.length) {
        listContainer.innerHTML = '<p style="color:var(--text-muted); font-size:13px; text-align:center; padding: 20px 0;">No active posts to moderate.</p>';
        countBadge.textContent = '0 items';
        return;
    }

    countBadge.textContent = `${posts.length} items`;
    listContainer.innerHTML = posts.map(p => `
        <div style="display:flex; justify-content:space-between; align-items:start; gap:12px; padding:10px; background:var(--surface2); border:1px solid var(--border); border-radius:8px; margin-bottom:8px;">
            <div style="flex:1; min-width:0;">
                <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px;">
                    <span style="font-weight:600; font-size:12px; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:120px;">${escHtml(p.author)}</span>
                    <span class="tag tag-blue" style="font-size:9px; padding:2px 6px;">${p.category}</span>
                </div>
                <div style="font-size:12px; color:var(--text-muted); line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; word-break:break-all;">
                    ${escHtml(p.text)}
                </div>
            </div>
            <div style="display:flex; gap:4px; flex-shrink:0;">
                <button class="btn btn-outline" style="color:var(--danger); border-color:var(--danger); padding:4px 8px; font-size:11px; margin:0;" onclick="adminDeletePostHome(${p.id})">
                    <i class="fa-solid fa-trash"></i> Delete
                </button>
            </div>
        </div>
    `).join('');
}

async function adminDeletePostHome(postId) {
    if(!confirm('Are you sure you want to delete this post?')) return;
    try {
        await apiFetch('/api/v1/admin/communities/' + postId, { method: 'DELETE' });
        addNotif('Post deleted successfully.');
        posts = posts.filter(p => p.id !== postId);
        
        // Synchronize widgets and admin panel view
        renderHomeModWidget();
        renderAdminModQueue();
        
        document.getElementById('adminHomeTotalPosts').textContent = posts.length;
    } catch(e) {
        addNotif('⚠️ Failed to delete post: ' + e.message);
    }
}

async function submitAdminModuleHome() {
    const code = document.getElementById('adminHomeModCode').value.trim();
    const name = document.getElementById('adminHomeModName').value.trim();
    const credits = document.getElementById('adminHomeModCredits').value;
    const faculty = document.getElementById('adminHomeModFaculty').value.trim();
    const prereq = document.getElementById('adminHomeModPrereq').value.trim();
    const desc = document.getElementById('adminHomeModDesc').value.trim();
    
    if(!code || !name || !credits) { addNotif('⚠️ Code, Name, and Credits are required!'); return; }
    
    try {
        await apiFetch('/api/v1/academic-modules', {
            method: 'POST',
            body: JSON.stringify({
                code, name, credits: parseInt(credits), faculty, prereq, desc
            })
        });
        addNotif('Module cataloged successfully!');
        
        // Reset home fields
        document.getElementById('adminHomeModCode').value = '';
        document.getElementById('adminHomeModName').value = '';
        document.getElementById('adminHomeModCredits').value = '';
        document.getElementById('adminHomeModFaculty').value = '';
        document.getElementById('adminHomeModPrereq').value = '';
        document.getElementById('adminHomeModDesc').value = '';
        
        // Refresh local module data structures and admin tab lists
        await loadAdminModules();
        await loadAdminHomeData();
    } catch(e) {
        addNotif('⚠️ Failed to add module: ' + e.message);
    }
}

async function submitAdminNewsHome() {
    const title = document.getElementById('adminHomeNewsTitle').value.trim();
    const cat = document.getElementById('adminHomeNewsCat').value;
    const content = document.getElementById('adminHomeNewsContent').value.trim();
    
    if(!title || !content) { addNotif('⚠️ Title and Content are required!'); return; }
    
    try {
        await apiFetch('/api/v1/news', {
            method: 'POST',
            body: JSON.stringify({
                title: title,
                content: content,
                category_id: parseInt(cat),
                date: new Date().toISOString().split('T')[0]
            })
        });
        addNotif('News published successfully!');
        
        // Reset news fields
        document.getElementById('adminHomeNewsTitle').value = '';
        document.getElementById('adminHomeNewsContent').value = '';
        
        // Refresh news listings and count
        await fetchNews();
        await loadAdminHomeData();
    } catch(e) {
        addNotif('⚠️ Failed to publish news: ' + e.message);
    }
}

// ── INIT ──
window.onload = () => {
    initAuth();
    renderNotifications();
    fetchPosts();
    renderGpa();
    loadAdminModules();
    renderAllModules();
    fetchNews();
    fetchKB();
};
</script>
</body>
</html>
