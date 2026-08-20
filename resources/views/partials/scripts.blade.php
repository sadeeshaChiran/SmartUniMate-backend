<script>
function showStudentWarningModal(msg, count) {
    document.getElementById('studentWarningCount').textContent = count || 0;
    document.getElementById('studentWarningMessageBody').textContent = msg || '';
    document.getElementById('studentWarningDetailModalOverlay').style.display = 'flex';
}
function closeStudentWarningModal() {
    document.getElementById('studentWarningDetailModalOverlay').style.display = 'none';
    const userStr = localStorage.getItem('susl_user');
    const user = userStr ? JSON.parse(userStr) : null;
    if (user && user.warning_message) {
        localStorage.setItem('susl_last_read_warning_msg', user.warning_message);
        localStorage.setItem('susl_last_read_warning_count', user.warnings_count);
        updateProfileUI(user);
        if (typeof renderNotifications === 'function') renderNotifications();
        if (typeof renderProfileNotifications === 'function') renderProfileNotifications();
    }
}
function triggerStudentWarningModalFromProfile() {
    const userStr = localStorage.getItem('susl_user');
    const user = userStr ? JSON.parse(userStr) : null;
    if (user && user.warning_message) {
        // Mark as read when they click to open
        localStorage.setItem('susl_last_read_warning_msg', user.warning_message);
        localStorage.setItem('susl_last_read_warning_count', user.warnings_count);
        if (typeof renderNotifications === 'function') renderNotifications();
        if (typeof renderProfileNotifications === 'function') renderProfileNotifications();
        
        showStudentWarningModal(user.warning_message, user.warnings_count);
    } else {
        showStudentWarningModal("No active warning message.", 0);
    }
}

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

// ── Language Switcher ────────────────────────────────────────────
const LANG_LABELS = { en: 'EN', si: 'සිං', ta: 'தமி' };
let currentLang = localStorage.getItem('susl_locale') || 'en';

function initLang() {
    document.cookie = `locale=${currentLang};path=/;max-age=31536000`;
    const label = document.getElementById('langLabel');
    if (label) label.textContent = LANG_LABELS[currentLang] || 'EN';
    document.querySelectorAll('.lang-option').forEach(el => {
        el.classList.toggle('active', el.getAttribute('onclick').includes(`'${currentLang}'`));
    });
}

function setLang(lang) {
    currentLang = lang;
    localStorage.setItem('susl_locale', lang);
    document.cookie = `locale=${lang};path=/;max-age=31536000`;
    const label = document.getElementById('langLabel');
    if (label) label.textContent = LANG_LABELS[lang] || 'EN';
    document.querySelectorAll('.lang-option').forEach(el => {
        el.classList.toggle('active', el.getAttribute('onclick').includes(`'${lang}'`));
    });
    document.getElementById('langMenu').classList.remove('open');
    
    // Reload page with query param so backend strictly serves translated content
    const url = new URL(window.location.href);
    url.searchParams.set('locale', lang);
    window.location.href = url.toString();
}

function toggleLangMenu() {
    document.getElementById('langMenu').classList.toggle('open');
}

// Close lang menu on outside click
document.addEventListener('click', function(e) {
    const switcher = document.getElementById('langSwitcher');
    if (switcher && !switcher.contains(e.target)) {
        document.getElementById('langMenu').classList.remove('open');
    }
});
// ────────────────────────────────────────────────────────────────

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
function toggleMobileNav() {
    document.body.classList.toggle('mobile-nav-open');
}
function closeMobileNav() {
    document.body.classList.remove('mobile-nav-open');
}
function nav(id) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    const ni = document.getElementById('nav-'+id);
    if(ni) ni.classList.add('active');
    closeMobileNav();
    if(notifPanelOpen) toggleNotifPanel();
}

window.addEventListener('resize', function () {
    if (window.innerWidth > 992) {
        closeMobileNav();
    }
});

document.addEventListener('click', function (event) {
    const navOpen = document.body.classList.contains('mobile-nav-open');
    const clickedInsideNav = event.target.closest('aside') || event.target.closest('.menu-toggle');
    if (navOpen && !clickedInsideNav) {
        closeMobileNav();
    }
});

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
    if (!p) return;
    const shareText = `Check out this post on SmartUniMate:\n"${p.text}"\n— ${p.author}`;
    // Attempt to use the Web Share API if supported
    if (navigator.share) {
        navigator.share({
            title: 'SmartUniMate Post',
            text: shareText,
            url: window.location.href
        }).then(() => {
            addNotif('📤 Post shared via native share!');
        }).catch((err) => {
            // If sharing fails, fall back to clipboard copy
            fallbackCopy(shareText);
        });
    } else {
        // Fallback for browsers without Web Share API
        fallbackCopy(shareText);
    }
}

function fallbackCopy(text) {
    try {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        addNotif('📋 Post copied to clipboard!');
    } catch (e) {
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
    if(typeof renderNotifications === 'function') renderNotifications();
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

        // Render warnings
        const alertsEl = document.getElementById('homeAlertsContainer');
        
        let isWarnRead = false;
        if (user.warning_message) {
            const lastReadMsg = localStorage.getItem('susl_last_read_warning_msg');
            const lastReadCount = parseInt(localStorage.getItem('susl_last_read_warning_count') || '0');
            if (user.warning_message === lastReadMsg && user.warnings_count === lastReadCount) {
                isWarnRead = true;
            }
        }

        if (user.warning_message && !isWarnRead && alertsEl) {
            alertsEl.innerHTML = `
                <div class="card" style="border:1px solid var(--danger);background:rgba(192,57,43,0.08);color:var(--text);padding:16px;border-radius:12px;display:flex;align-items:center;gap:12px;text-align:left;margin-bottom:20px;cursor:pointer;" onclick="triggerStudentWarningModalFromProfile()">
                    <div style="width:36px;height:36px;border-radius:50%;background:var(--danger);color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div style="flex:1;">
                        <h4 style="margin:0 0 4px 0;font-size:13px;font-weight:700;color:var(--danger);">Account Warning from Admin (Click to read)</h4>
                        <p style="margin:0;font-size:12px;line-height:1.4;">"${escHtml(user.warning_message)}" (Warnings Count: ${user.warnings_count})</p>
                    </div>
                </div>
            `;
        } else if (alertsEl) {
            alertsEl.innerHTML = '';
        }
    } else {
        document.getElementById('profileNameBig').textContent = 'Guest User';
        document.getElementById('profileIndexBig').textContent = 'Index: N/A';
        document.getElementById('profileYearBig').textContent = 'Sign in to see your profile';
        const alertsEl = document.getElementById('homeAlertsContainer');
        if (alertsEl) alertsEl.innerHTML = '';
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
        if (isLoggedIn) {
            apiFetch('/api/v1/profile').then(data => {
                if (data && data.student) {
                    localStorage.setItem('susl_user', JSON.stringify(data.student));
                    updateProfileUI(data.student);
                    renderProfileNotifications();
                }
            }).catch(e => {
                console.error('Failed to sync profile for notifications tab:', e);
            });
        }
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
    const userStr = localStorage.getItem('susl_user');
    const user = userStr ? JSON.parse(userStr) : null;
    
    let isWarnRead = false;
    if (user && user.warning_message) {
        const lastReadMsg = localStorage.getItem('susl_last_read_warning_msg');
        const lastReadCount = parseInt(localStorage.getItem('susl_last_read_warning_count') || '0');
        if (user.warning_message === lastReadMsg && user.warnings_count === lastReadCount) {
            isWarnRead = true;
        }
    }
    
    // Create a copy of local notifications
    let activeNotifs = [...NOTIFICATIONS];
    
    // If the student has an active warning, prepend it
    if (isLoggedIn && user && user.warning_message) {
        activeNotifs.unshift({
            text: `⚠️ ACCOUNT WARNING: ${user.warning_message}`,
            time: 'Important',
            read: isWarnRead,
            isWarning: true
        });
    }

    if(!activeNotifs.length) {
        container.innerHTML = '<p style="color:var(--text-muted);font-size:13px;">No notifications yet.</p>';
        return;
    }
    
    container.innerHTML = activeNotifs.map(n => {
        if (n.isWarning) {
            return `
                <div style="padding:16px;border-bottom:1px solid var(--border);display:flex;gap:12px;align-items:flex-start;background:${n.read ? 'transparent' : 'rgba(192,57,43,0.06)'};border-left:4px solid var(--danger);cursor:pointer;" onclick="triggerStudentWarningModalFromProfile()">
                    <div style="width:8px;height:8px;border-radius:50%;background:${n.read ? 'transparent' : 'var(--danger)'};margin-top:6px;"></div>
                    <div>
                        <div style="font-size:14px;font-weight:700;color:var(--danger);">${escHtml(n.text)}</div>
                        <div style="font-size:12px;color:var(--danger);font-weight:700;margin-top:4px;">${n.time}</div>
                    </div>
                </div>
            `;
        }
        return `
            <div style="padding:16px 0;border-bottom:1px solid var(--border);display:flex;gap:12px;align-items:flex-start;">
                <div style="width:8px;height:8px;border-radius:50%;background:${n.read ? 'transparent' : 'var(--danger)'};margin-top:6px;"></div>
                <div>
                    <div style="font-size:14px;font-weight:${n.read ? '400' : '600'};">${n.text}</div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">${n.time}</div>
                </div>
            </div>
        `;
    }).join('');
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
        : '<i class="fa-brands fa-microsoft"></i> <span>{{ __("messages.sign_in") }}</span>';
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
        student_id: document.getElementById('regStudentId').value.trim(),
        year: parseInt(document.getElementById('regYear').value) || 1,
        faculty: document.getElementById('regFaculty').value,
        password: document.getElementById('regPassword').value,
        password_confirmation: document.getElementById('regPasswordConfirm').value
    };
    const errEl = document.getElementById('authErrorMsg');
    
    if (!payload.student_id) {
        errEl.textContent = "Student ID (Index Number) is required.";
        errEl.style.display = 'block';
        return;
    }
    
    const termsCheck = document.getElementById('regTermsCheck');
    if (!termsCheck || !termsCheck.checked) {
        errEl.textContent = "You must read and accept the Terms & Conditions and Privacy Policy to register.";
        errEl.style.display = 'block';
        return;
    }
    
    try {
        const data = await apiFetch('/api/v1/register', {
            method: 'POST',
            body: JSON.stringify(payload)
        });
        
        if(data && data.message) {
            // Reset T&C checkbox for next use
            const tc = document.getElementById('regTermsCheck');
            if (tc) tc.checked = false;
            
            // clear form
            document.getElementById('regName').value = '';
            document.getElementById('regEmail').value = '';
            document.getElementById('regStudentId').value = '';
            document.getElementById('regFaculty').value = '';
            document.getElementById('regPassword').value = '';
            document.getElementById('regPasswordConfirm').value = '';

            closeAuthModal();
            addNotif(data.message || 'Registration successful! Please wait for admin approval.');
        }
    } catch(e) {
        let msg = e.message;
        if(msg.includes('HTTP error!')) msg = "Registration failed. Please check your inputs.";
        errEl.textContent = msg;
        errEl.style.display = 'block';
    }
}

// ── TERMS & PRIVACY POLICY MODAL ──
function openTermsModal(tab) {
    tab = tab || 'privacy';
    document.getElementById('termsModalOverlay').style.display = 'flex';
    switchTermsTab(tab);
    document.getElementById('termsScrollBody').scrollTop = 0;
}
function closeTermsModal(event) {
    // Only close if clicking the backdrop itself
    if (event && event.target !== document.getElementById('termsModalOverlay')) return;
    document.getElementById('termsModalOverlay').style.display = 'none';
}
function closeTermsModalBtn() {
    document.getElementById('termsModalOverlay').style.display = 'none';
}
function switchTermsTab(tab) {
    const panels = { privacy: 'termsPanelPrivacy', terms: 'termsPanelTerms' };
    const tabs   = { privacy: 'termsTabPrivacy',   terms: 'termsTabTerms' };
    Object.keys(panels).forEach(key => {
        document.getElementById(panels[key]).style.display = key === tab ? 'block' : 'none';
        const btn = document.getElementById(tabs[key]);
        if (key === tab) {
            btn.style.borderBottomColor = 'var(--primary)';
            btn.style.color = 'var(--primary)';
        } else {
            btn.style.borderBottomColor = 'transparent';
            btn.style.color = 'var(--text-muted)';
        }
    });
    document.getElementById('termsScrollBody').scrollTop = 0;
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
    const userStr = localStorage.getItem('susl_user');
    const user = userStr ? JSON.parse(userStr) : null;
    
    let isWarnRead = false;
    if (user && user.warning_message) {
        const lastReadMsg = localStorage.getItem('susl_last_read_warning_msg');
        const lastReadCount = parseInt(localStorage.getItem('susl_last_read_warning_count') || '0');
        if (user.warning_message === lastReadMsg && user.warnings_count === lastReadCount) {
            isWarnRead = true;
        }
    }
    
    // Create a copy of local notifications
    let activeNotifs = [...NOTIFICATIONS];
    let warningCount = 0;
    
    // If the student has an active warning, prepend it
    if (isLoggedIn && user && user.warning_message) {
        activeNotifs.unshift({
            text: `⚠️ ACCOUNT WARNING: ${user.warning_message}`,
            time: 'Important',
            read: isWarnRead,
            isWarning: true
        });
        if (!isWarnRead) {
            warningCount = 1;
        }
    }

    const unread = NOTIFICATIONS.filter(n => !n.read).length + warningCount;
    const badge = document.getElementById('notifBadge');
    badge.style.display = unread > 0 ? 'block' : 'none';
    
    const list = document.getElementById('notifList');
    list.innerHTML = activeNotifs.map(n => {
        if (n.isWarning) {
            return `
                <div class="notif-item" style="background:${n.read ? 'transparent' : 'rgba(192,57,43,0.06)'}; cursor:pointer; border-left:4px solid var(--danger);" onclick="triggerStudentWarningModalFromProfile()">
                    <div class="notif-dot ${n.read ? 'read' : ''}"></div>
                    <div>
                        <div class="notif-text" style="font-weight:600; color:var(--danger);">${escHtml(n.text)}</div>
                        <div class="notif-time" style="color:var(--danger); font-weight:700;">${n.time}</div>
                    </div>
                </div>
            `;
        }
        return `
            <div class="notif-item">
                <div class="notif-dot ${n.read ? 'read' : ''}"></div>
                <div>
                    <div class="notif-text">${n.text}</div>
                    <div class="notif-time">${n.time}</div>
                </div>
            </div>
        `;
    }).join('');
}

function toggleNotifPanel() {
    notifPanelOpen = !notifPanelOpen;
    document.getElementById('notifPanel').classList.toggle('open', notifPanelOpen);
    if (notifPanelOpen && isLoggedIn) {
        apiFetch('/api/v1/profile').then(data => {
            if (data && data.student) {
                localStorage.setItem('susl_user', JSON.stringify(data.student));
                updateProfileUI(data.student);
                renderNotifications();
            }
        }).catch(e => {
            console.error('Failed to sync profile for notifications:', e);
        });
    }
}

function markAllRead() {
    NOTIFICATIONS.forEach(n => n.read = true);
    
    // Also mark warning as read if user is logged in
    const userStr = localStorage.getItem('susl_user');
    const user = userStr ? JSON.parse(userStr) : null;
    if (user && user.warning_message) {
        localStorage.setItem('susl_last_read_warning_msg', user.warning_message);
        localStorage.setItem('susl_last_read_warning_count', user.warnings_count);
    }
    
    renderNotifications();
    if (typeof renderProfileNotifications === 'function') renderProfileNotifications();
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
            <div class="module-result" style="display:flex; justify-content:space-between; align-items:center; gap:16px;">
                <div style="flex:1;">
                    <h4><strong>${m.code}</strong> — ${m.name} <span class="tag tag-blue">${m.credits} Credits</span></h4>
                    <p style="margin:4px 0 0 0;">${m.desc}</p>
                    <p style="margin-top:4px;font-size:12px;">👤 ${m.faculty} &nbsp;|&nbsp; Pre-req: ${m.prereq}</p>
                </div>
                <button class="btn btn-outline" style="font-size:11px; padding:6px 12px; margin:0;" onclick="addCatalogModuleToGpa('${m.code}', '${m.name.replace(/'/g, "\\'")}', ${m.credits})">
                    <i class="fa-solid fa-plus"></i> Add to GPA
                </button>
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
        <div class="module-result" style="display:flex; justify-content:space-between; align-items:center; gap:16px;">
            <div style="flex:1;">
                <h4><strong>${m.code}</strong> — ${m.name} <span class="tag tag-blue">${m.credits} Cr</span></h4>
                <p style="font-size:12px; margin:4px 0 0 0;">${m.faculty || 'Unassigned'} · Pre-req: ${m.prereq || 'None'}</p>
            </div>
            <button class="btn btn-outline" style="font-size:11px; padding:6px 12px; margin:0;" onclick="addCatalogModuleToGpa('${m.code}', '${m.name.replace(/'/g, "\\'")}', ${m.credits})">
                <i class="fa-solid fa-plus"></i> Add to GPA
            </button>
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
                    image: p.image_path ? (p.image_path.startsWith('http') ? p.image_path : window.location.origin + '/storage/' + p.image_path) : (imgStore[p.post_content.substring(0,50)] || null)
                };
            });
            renderPosts(currentFilter);
            renderAdminModQueue();
// renderHomeTrendingPosts(); // removed per request
            
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

function renderHomeTrendingPosts() {
    const container = document.getElementById('homeTrendingPosts');
    if (!container) return;
    
    if (posts.length === 0) {
        container.innerHTML = '<div style="font-size:12px; color:var(--text-muted); text-align:center; padding: 20px 0;">No discussions yet.</div>';
        return;
    }
    
    // Take the 3 most recent posts
    const trending = posts.slice(0, 3);
    
    container.innerHTML = trending.map(p => `
        <div style="padding: 12px 0; border-bottom: 1px solid var(--border); display:flex; gap:12px;">
            <div class="avatar-sm">${p.initials}</div>
            <div>
                <div style="font-size:13px; font-weight:600;">${p.author}</div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">${p.text.length > 50 ? p.text.substring(0, 50) + '...' : p.text}</div>
            </div>
        </div>
    `).join('');
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
            ${p.image ? `<div style="max-height:300px; overflow:hidden; display:flex; justify-content:center; align-items:center; border-radius:8px; margin-bottom:12px; border:1px solid var(--border);"><img src="${p.image}" style="max-height:300px; width:100%; object-fit:contain;" alt="post image"></div>` : ''}
            <div class="post-actions" style="display:flex; align-items:center;">
                <button class="post-action-btn" onclick="likePost(${p.id})" style="${isLiked ? 'color:var(--danger);' : ''}"><i class="fa-${isLiked ? 'solid' : 'regular'} fa-heart"></i> ${p.likes || ''}</button>
                <button class="post-action-btn" onclick="toggleCommentSection(${p.id})"><i class="fa-regular fa-comment"></i> ${comments.length || ''} Comment</button>
                <button class="post-action-btn" onclick="sharePost(${p.id})"><i class="fa-solid fa-share-nodes"></i> Share</button>
                <button class="post-action-btn" onclick="openReportPostModal(${p.id})" style="color:var(--danger); margin-left:auto;"><i class="fa-solid fa-flag"></i> Report</button>
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
    if (tabName === 'reports') {
        loadAdminComplaints();
    }
    if (tabName === 'approvals') {
        loadPendingApprovals();
    }
}

// ── ADMIN STUDENT APPROVALS ──
async function loadPendingApprovals() {
    const tbody = document.getElementById('adminApprovalsTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:20px;">Loading pending approvals...</td></tr>';
    try {
        const data = await apiFetch('/api/v1/admin/students/pending');
        if (data && data.students && data.students.length > 0) {
            tbody.innerHTML = data.students.map(s => `
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:10px;">${escHtml(s.name)}</td>
                    <td style="padding:10px;">${escHtml(s.student_id)}</td>
                    <td style="padding:10px;">${escHtml(s.email)}</td>
                    <td style="padding:10px;">${escHtml(s.faculty || '-')}</td>
                    <td style="padding:10px;">${s.year || '-'}</td>
                    <td style="padding:10px;color:var(--text-muted);font-size:11px;">${new Date(s.created_at).toLocaleDateString()}</td>
                    <td style="padding:10px;text-align:center;">
                        <div style="display:flex;gap:6px;justify-content:center;">
                            <button class="btn btn-primary" style="padding:4px 8px;font-size:11px;" onclick="approveStudent(${s.id})"><i class="fa-solid fa-check"></i></button>
                            <button class="btn btn-outline" style="padding:4px 8px;font-size:11px;color:var(--danger);border-color:var(--danger);" onclick="rejectStudent(${s.id})"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:20px;">No pending registrations found.</td></tr>';
        }
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--danger);padding:20px;">Error loading pending approvals.</td></tr>';
    }
}

async function approveStudent(id) {
    if (!confirm('Are you sure you want to approve this student?')) return;
    try {
        await apiFetch(`/api/v1/admin/students/${id}/approve`, { method: 'POST' });
        addNotif('Student approved successfully.');
        loadPendingApprovals();
    } catch(e) {
        addNotif('⚠️ Failed to approve student.');
    }
}

async function rejectStudent(id) {
    if (!confirm('Are you sure you want to reject this student? Their registration record will be deleted.')) return;
    try {
        await apiFetch(`/api/v1/admin/students/${id}/reject`, { method: 'POST' });
        addNotif('Student registration rejected and removed.');
        loadPendingApprovals();
    } catch(e) {
        addNotif('⚠️ Failed to reject student.');
    }
}

function updateAdminProfileUI(admin) {
    if(admin) {
        document.getElementById('adminNameBig').textContent = admin.name || 'System Administrator';
            document.getElementById('adminEmailBig').textContent = admin.email || 'admin@mate.com';
            document.getElementById('adminProfName').value = admin.name || 'System Administrator';
            document.getElementById('adminProfEmail').value = admin.email || 'admin@mate.com';
            if (admin.created_at) {
                document.getElementById('adminMemberSince').value = new Date(admin.created_at).toLocaleDateString();
            } else {
                document.getElementById('adminMemberSince').value = 'N/A';
            }
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

// ── STUDENT PEER CHAT (BLADE INTERFACE) ──
let currentSelectedPeer = null;
let peerMessages = [];
let selectedPeerFile = null;
let peerMessagesInterval = null;
let recentChatsInterval = null;

function switchChatTab(tab) {
    document.getElementById('btnAiChatTab').className = tab === 'ai' ? 'btn btn-primary' : 'btn btn-outline';
    document.getElementById('btnPeerChatTab').className = tab === 'peer' ? 'btn btn-primary' : 'btn btn-outline';
    
    // Toggle displays
    document.getElementById('aiChatWrap').style.display = tab === 'ai' ? 'block' : 'none';
    document.getElementById('peerChatWrap').style.display = tab === 'peer' ? 'flex' : 'none';
    
    // Quick prompt chips display toggle
    const quickChips = document.querySelector('#chatbot div[style*="flex-wrap:wrap"]');
    if (quickChips) {
        quickChips.style.display = tab === 'ai' ? 'flex' : 'none';
    }
    
    if (tab === 'peer') {
        loadRecentPeerChats();
        startPeerPolling();
    } else {
        stopPeerPolling();
    }
}

function startPeerPolling() {
    stopPeerPolling();
    loadRecentPeerChats();
    recentChatsInterval = setInterval(loadRecentPeerChats, 8000);
    
    if (currentSelectedPeer) {
        loadPeerMessages();
        peerMessagesInterval = setInterval(loadPeerMessages, 4000);
    }
}

function stopPeerPolling() {
    if (peerMessagesInterval) clearInterval(peerMessagesInterval);
    if (recentChatsInterval) clearInterval(recentChatsInterval);
}

async function loadRecentPeerChats() {
    if (!isLoggedIn) return;
    try {
        const res = await apiFetch('/api/v1/peer-chats');
        const container = document.getElementById('peerThreadsContainer');
        if (!res || res.length === 0) {
            container.innerHTML = '<p style="font-size:11px; color:var(--text-muted); text-align:center; padding:20px 0;">No active chats.</p>';
            return;
        }
        
        const userStr = localStorage.getItem('susl_user');
        const currentUser = userStr ? JSON.parse(userStr) : null;
        const currentUserId = currentUser ? currentUser.id : null;
        
        container.innerHTML = res.map(conv => {
            const peer = conv.peer;
            const lastMsg = conv.last_message;
            const isActive = currentSelectedPeer && currentSelectedPeer.student_id === peer.student_id;
            
            const initials = peer.name.split(' ').map(n=>n[0]).join('').substring(0,2).toUpperCase();
            const avatarHtml = peer.avatar 
                ? `<img src="${window.location.origin}/storage/${peer.avatar}" style="width:34px;height:34px;border-radius:50%;object-fit:cover;">`
                : `<div style="width:34px;height:34px;border-radius:50%;background:rgba(128,0,0,0.1);color:#800000;font-weight:700;display:flex;align-items:center;justify-content:center;font-size:12px;">${initials}</div>`;
                
            const lastMsgSnippet = lastMsg 
                ? (lastMsg.message || '📎 File attachment') 
                : `Index: ${peer.student_id}`;
                
            const unreadBadge = conv.unread_count > 0 
                ? `<span style="background:#800000;color:#fff;font-size:9px;font-weight:700;border-radius:50%;width:16px;height:16px;display:flex;align-items:center;justify-content:center;margin-left:auto;">${conv.unread_count}</span>` 
                : '';
                
            const timeHtml = conv.last_interaction_time 
                ? `<span style="font-size:9px;color:var(--text-muted);margin-left:auto;">${new Date(conv.last_interaction_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>` 
                : '';

            return `
                <div onclick='selectPeer(${JSON.stringify(peer)})' style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;cursor:pointer;transition:background 0.2s;background:${isActive ? 'rgba(128,0,0,0.05)' : 'transparent'};border-left:${isActive ? '3px solid #800000' : 'none'};" class="peer-item-hover">
                    ${avatarHtml}
                    <div style="flex:1;min-width:0;text-align:left;">
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <span style="font-size:12px;font-weight:700;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${escHtml(peer.name)}</span>
                            ${timeHtml}
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:2px;">
                            <span style="font-size:10px;color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:140px;">${escHtml(lastMsgSnippet)}</span>
                            ${unreadBadge}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    } catch (err) {
        console.error(err);
    }
}

let peerSearchTimeout = null;
function doPeerSearch() {
    const q = document.getElementById('peerSearchInput').value.trim();
    const dropdown = document.getElementById('peerSearchResults');
    if (!q) {
        dropdown.style.display = 'none';
        return;
    }
    if (peerSearchTimeout) clearTimeout(peerSearchTimeout);
    peerSearchTimeout = setTimeout(async () => {
        try {
            const res = await apiFetch(`/api/v1/peer-chats/search?query=${encodeURIComponent(q)}`);
            if (!res || res.length === 0) {
                dropdown.innerHTML = '<div style="padding:10px;text-align:center;font-size:11px;color:var(--text-muted);">No student found. Search by full Index Number (e.g. 22FIS0580)</div>';
                dropdown.style.display = 'block';
                return;
            }
            dropdown.innerHTML = res.map(st => {
                const initials = st.name.split(' ').map(n=>n[0]).join('').substring(0,2).toUpperCase();
                const avatarHtml = st.avatar 
                    ? `<img src="${window.location.origin}/storage/${st.avatar}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;">`
                    : `<div style="width:28px;height:28px;border-radius:50%;background:rgba(128,0,0,0.1);color:#800000;font-weight:700;display:flex;align-items:center;justify-content:center;font-size:10px;">${initials}</div>`;
                return `
                    <div onclick='selectPeer(${JSON.stringify(st)})' style="display:flex;align-items:center;gap:10px;padding:8px 10px;cursor:pointer;" class="peer-item-hover">
                        ${avatarHtml}
                        <div style="min-width:0;text-align:left;">
                            <div style="font-size:11px;font-weight:700;color:var(--text);">${escHtml(st.name)}</div>
                            <div style="font-size:9px;color:var(--text-muted);">${st.student_id} · ${st.faculty || 'SUSL'}</div>
                        </div>
                    </div>
                `;
            }).join('');
            dropdown.style.display = 'block';
        } catch(e) {
            console.error(e);
        }
    }, 400);
}

function selectPeer(peer) {
    currentSelectedPeer = peer;
    document.getElementById('peerSearchInput').value = '';
    document.getElementById('peerSearchResults').style.display = 'none';
    
    // UI transitions
    document.getElementById('peerChatEmptyState').style.display = 'none';
    document.getElementById('peerChatActiveFrame').style.display = 'flex';
    
    // Header details
    const initials = peer.name.split(' ').map(n=>n[0]).join('').substring(0,2).toUpperCase();
    const avatarBox = document.getElementById('peerActiveAvatar');
    if (peer.avatar) {
        avatarBox.innerHTML = `<img src="${window.location.origin}/storage/${peer.avatar}" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">`;
    } else {
        avatarBox.innerHTML = initials;
    }
    document.getElementById('peerActiveName').textContent = peer.name;
    document.getElementById('peerActiveId').textContent = `${peer.student_id} · ${peer.faculty || 'SUSL Student'}`;
    
    // Refresh message stream
    document.getElementById('peerChatMessagesBox').innerHTML = '<p style="text-align:center;font-size:11px;color:var(--text-muted);padding:20px 0;">Loading conversation history...</p>';
    loadPeerMessages();
    
    // Restart polling for active chat
    startPeerPolling();
}

async function loadPeerMessages() {
    if (!currentSelectedPeer || !isLoggedIn) return;
    try {
        const res = await apiFetch(`/api/v1/peer-chats/${currentSelectedPeer.student_id}/messages`);
        if (!res) return;
        
        peerMessages = res.messages || [];
        
        const userStr = localStorage.getItem('susl_user');
        const currentUser = userStr ? JSON.parse(userStr) : null;
        const currentUserId = currentUser ? currentUser.id : null;
        
        const box = document.getElementById('peerChatMessagesBox');
        if (peerMessages.length === 0) {
            box.innerHTML = '<p style="text-align:center;font-size:11px;color:var(--text-muted);padding:20px 0;">No messages yet. Send a message to start chatting!</p>';
            return;
        }
        
        let autoScroll = box.scrollTop + box.clientHeight >= box.scrollHeight - 50;
        
        box.innerHTML = peerMessages.map(m => {
            const isOurs = Number(m.sender_id) === Number(currentUserId);
            const isImg = isImageFile(m.file_type, m.file_name);
            
            let attachmentHtml = '';
            if (m.file_path) {
                if (isImg) {
                    attachmentHtml = `
                        <div style="margin-bottom:6px;">
                            <a href="${window.location.origin}/storage/${m.file_path}" target="_blank" style="display:block;">
                                <img src="${window.location.origin}/storage/${m.file_path}" style="max-width:200px;max-height:150px;border-radius:8px;object-fit:contain;border:1px solid var(--border);">
                            </a>
                        </div>
                    `;
                } else {
                    attachmentHtml = `
                        <div style="margin-bottom:6px;">
                            <a href="${window.location.origin}/storage/${m.file_path}" target="_blank" style="display:flex;align-items:center;gap:8px;padding:6px 10px;border:1px solid var(--border);border-radius:8px;background:${isOurs ? 'rgba(0,0,0,0.1)' : 'var(--surface2)'};color:${isOurs ? '#fff' : 'var(--primary)'};font-size:11px;text-decoration:none;min-width:0;max-width:200px;">
                                <i class="fa-solid fa-file-lines" style="flex-shrink:0;"></i>
                                <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;">${escHtml(m.file_name || 'Document')}</span>
                                <i class="fa-solid fa-download" style="flex-shrink:0;opacity:0.7;"></i>
                            </a>
                        </div>
                    `;
                }
            }
            
            const messageText = m.message ? `<p style="margin:0;word-break:break-all;">${escHtml(m.message)}</p>` : '';
            const time = new Date(m.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            
            const ticks = isOurs 
                ? (m.is_read 
                    ? '<i class="fa-solid fa-check-double" style="color:#64b5f6;font-size:10px;margin-left:4px;"></i>' 
                    : '<i class="fa-solid fa-check" style="color:rgba(255,255,255,0.6);font-size:10px;margin-left:4px;"></i>')
                : '';

            return `
                <div class="msg ${isOurs ? 'user' : 'bot'}" style="max-width:75%;padding:8px 12px;border-radius:12px;font-size:12px;line-height:1.4;align-self:${isOurs ? 'flex-end' : 'flex-start'};background:${isOurs ? '#800000' : '#fff'};color:${isOurs ? '#fff' : 'var(--text)'};border:${isOurs ? 'none' : '1px solid var(--border)'};border-bottom-${isOurs ? 'right' : 'left'}-radius:2px;">
                    ${attachmentHtml}
                    ${messageText}
                    <div style="display:flex;align-items:center;justify-content:flex-end;margin-top:4px;font-size:8px;opacity:0.75;user-select:none;">
                        <span>${time}</span>
                        ${ticks}
                    </div>
                </div>
            `;
        }).join('');
        
        if (autoScroll) {
            box.scrollTop = box.scrollHeight;
        }
        
        // Mark unread messages as read
        const hasUnread = peerMessages.some(m => Number(m.sender_id) !== Number(currentUserId) && !m.is_read);
        if (hasUnread) {
            apiFetch(`/api/v1/peer-chats/${currentSelectedPeer.student_id}/read`, { method: 'POST' })
                .then(() => {
                    loadRecentPeerChats();
                })
                .catch(e => console.error(e));
        }
    } catch (err) {
        console.error(err);
    }
}

function isImageFile(mime, filename) {
    if (mime && mime.startsWith('image/')) return true;
    if (filename) {
        const ext = filename.split('.').pop().toLowerCase();
        return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(ext);
    }
    return false;
}

function handlePeerFileSelect() {
    const input = document.getElementById('peerFileInput');
    if (input.files && input.files[0]) {
        selectedPeerFile = input.files[0];
        document.getElementById('peerFilePreviewName').textContent = selectedPeerFile.name;
        document.getElementById('peerFilePreviewChip').style.display = 'flex';
    }
}

function clearSelectedPeerFile() {
    selectedPeerFile = null;
    document.getElementById('peerFileInput').value = '';
    document.getElementById('peerFilePreviewChip').style.display = 'none';
}

async function sendPeerMessage() {
    if (!currentSelectedPeer || !isLoggedIn) return;
    const txtInput = document.getElementById('peerMessageInput');
    const msgText = txtInput.value.trim();
    if (!msgText && !selectedPeerFile) return;
    
    const btn = document.getElementById('peerSendBtn');
    btn.disabled = true;
    
    try {
        const formData = new FormData();
        if (msgText) formData.append('message', msgText);
        if (selectedPeerFile) formData.append('file', selectedPeerFile);
        
        txtInput.value = '';
        clearSelectedPeerFile();
        
        const res = await apiFetch(`/api/v1/peer-chats/${currentSelectedPeer.student_id}/messages`, {
            method: 'POST',
            body: formData
        });
        
        // Append to list and scroll
        peerMessages.push(res);
        loadPeerMessages();
        
        // Refresh active list
        loadRecentPeerChats();
    } catch(e) {
        addNotif('⚠️ Failed to send message: ' + e.message);
    } finally {
        btn.disabled = false;
    }
}

// ── COMPLAINTS / REPORTING MANAGEMENT ──
function openReportPostModal(postId) {
    if(!isLoggedIn) { toggleAuth(); return; }
    document.getElementById('reportPostId').value = postId;
    document.getElementById('reportStudentId').value = '';
    document.getElementById('reportReason').value = 'Inappropriate Content';
    document.getElementById('reportDetails').value = '';
    document.getElementById('reportScreenshot').value = '';
    document.getElementById('reportModalOverlay').style.display = 'flex';
}

function openReportPeerModal() {
    if(!isLoggedIn || !currentSelectedPeer) return;
    document.getElementById('reportPostId').value = '';
    document.getElementById('reportStudentId').value = currentSelectedPeer.id;
    document.getElementById('reportReason').value = 'Harassment or Abuse';
    document.getElementById('reportDetails').value = '';
    document.getElementById('reportScreenshot').value = '';
    document.getElementById('reportModalOverlay').style.display = 'flex';
}

function closeReportModal() {
    document.getElementById('reportModalOverlay').style.display = 'none';
}

async function submitReport() {
    const postId = document.getElementById('reportPostId').value;
    const studentId = document.getElementById('reportStudentId').value;
    const reason = document.getElementById('reportReason').value;
    const details = document.getElementById('reportDetails').value.trim();
    const screenshotFile = document.getElementById('reportScreenshot').files[0];
    
    if(!details) {
        alert('Please fill out the explanation details.');
        return;
    }
    
    const btn = document.querySelector('#reportModalOverlay .btn-primary');
    btn.disabled = true;
    
    try {
        const formData = new FormData();
        if(postId) formData.append('reported_post_id', postId);
        if(studentId) formData.append('reported_student_id', studentId);
        formData.append('reason', reason);
        formData.append('details', details);
        if(screenshotFile) formData.append('screenshot', screenshotFile);
        
        await apiFetch('/api/v1/reports', {
            method: 'POST',
            body: formData
        });
        
        addNotif('Report submitted successfully.');
        closeReportModal();
    } catch(e) {
        addNotif('⚠️ Failed to submit report: ' + e.message);
    } finally {
        btn.disabled = false;
    }
}

// Admin actions
async function loadAdminComplaints() {
    try {
        const data = await apiFetch('/api/v1/admin/reports');
        const tbody = document.getElementById('adminReportsTableBody');
        if (!tbody) return;
        
        if(!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:20px;">No complaints filed yet.</td></tr>';
            return;
        }
        
        tbody.innerHTML = data.map(rep => {
            const reporter = rep.reporter ? `${rep.reporter.name} (${rep.reporter.student_id})` : 'System';
            
            let reportedEntity = 'N/A';
            let deletePostBtn = '';
            
            if (rep.reported_post_id) {
                const author = rep.reported_post && rep.reported_post.student ? rep.reported_post.student.name : 'Unknown';
                const snippet = rep.reported_post ? rep.reported_post.post_content.substring(0, 40) + '...' : '[Deleted Post]';
                reportedEntity = `<div><strong>Community Post</strong><div style="font-size:10px;color:var(--text-muted);">Author: ${author}</div><div style="font-size:10px;font-style:italic;max-width:200px;overflow:hidden;text-overflow:ellipsis;">"${snippet}"</div></div>`;
                
                if (rep.reported_post && rep.status === 'pending') {
                    deletePostBtn = `<button class="btn btn-outline" style="color:var(--danger); border-color:var(--danger); font-size:10px; padding:3px 6px; margin:2px;" onclick="resolveComplaint(${rep.id}, 'delete_post')"><i class="fa-solid fa-trash"></i> Delete Post</button>`;
                }
            } else if (rep.reported_student_id) {
                const name = rep.reported_student ? rep.reported_student.name : 'Unknown';
                const sId = rep.reported_student ? rep.reported_student.student_id : rep.reported_student_id;
                reportedEntity = `<div><strong>Student Chat Report</strong><div style="font-size:10px;color:var(--text-muted);">${name} (${sId})</div></div>`;
            }
            
            const screenshotHtml = rep.screenshot_path 
                ? `<a href="${window.location.origin}/storage/${rep.screenshot_path}" target="_blank" style="color:var(--primary);text-decoration:underline;">View Attachment</a>`
                : '<span style="color:var(--text-muted);">None</span>';
                
            let statusBadge = '';
            if (rep.status === 'pending') {
                statusBadge = '<span class="tag tag-blue" style="background:#0284c7;color:#fff;">Pending</span>';
            } else {
                statusBadge = `<span class="tag" style="background:var(--success);color:#fff;">Resolved (${rep.action_taken || 'resolved'})</span>`;
            }
            
            let actionsHtml = '-';
            if (rep.status === 'pending') {
                actionsHtml = `
                    <div style="display:flex; flex-direction:column; align-items:center;">
                        <button class="btn btn-outline" style="font-size:10px; padding:3px 6px; margin:2px;" onclick="openAdminWarnModal(${rep.id})"><i class="fa-solid fa-triangle-exclamation"></i> Warn User</button>
                        ${deletePostBtn}
                        <button class="btn btn-primary" style="background:var(--danger); border-color:var(--danger); font-size:10px; padding:3px 6px; margin:2px;" onclick="resolveComplaint(${rep.id}, 'ban')"><i class="fa-solid fa-ban"></i> Ban User</button>
                    </div>
                `;
            }
            
            return `
                <tr style="border-bottom:1px solid var(--border); vertical-align:top;">
                    <td style="padding:10px;">${escHtml(reporter)}</td>
                    <td style="padding:10px;">${reportedEntity}</td>
                    <td style="padding:10px; font-weight:600; color:#800000;">${escHtml(rep.reason)}</td>
                    <td style="padding:10px; max-width:200px; word-break:break-all;">${escHtml(rep.details || 'N/A')}</td>
                    <td style="padding:10px;">${screenshotHtml}</td>
                    <td style="padding:10px;">${statusBadge}</td>
                    <td style="padding:10px; text-align:center;">${actionsHtml}</td>
                </tr>
            `;
        }).join('');
    } catch (e) {
        console.error(e);
    }
}

function openAdminWarnModal(reportId) {
    document.getElementById('warnReportId').value = reportId;
    document.getElementById('adminWarningMsgText').value = '';
    document.getElementById('adminWarnModalOverlay').style.display = 'flex';
}
function closeAdminWarnModal() {
    document.getElementById('adminWarnModalOverlay').style.display = 'none';
}
async function submitAdminWarning() {
    const reportId = document.getElementById('warnReportId').value;
    const warningMsg = document.getElementById('adminWarningMsgText').value.trim();
    
    if(!warningMsg) {
        alert('Please fill out the warning message.');
        return;
    }
    
    try {
        await apiFetch(`/api/v1/admin/reports/${reportId}/action`, {
            method: 'POST',
            body: JSON.stringify({
                action: 'warn',
                warning_message: warningMsg
            })
        });
        addNotif('Warning message has been sent to the student.');
        closeAdminWarnModal();
        loadAdminComplaints();
    } catch(e) {
        addNotif('⚠️ Failed to issue warning: ' + e.message);
    }
}

async function resolveComplaint(reportId, action) {
    let confirmMsg = 'Are you sure you want to perform this moderation action?';
    if (action === 'ban') confirmMsg = 'Are you sure you want to permanently BAN this student from the campus database?';
    if (action === 'delete_post') confirmMsg = 'Are you sure you want to delete this community post?';
    
    if (!confirm(confirmMsg)) return;
    
    try {
        await apiFetch(`/api/v1/admin/reports/${reportId}/action`, {
            method: 'POST',
            body: JSON.stringify({ action: action })
        });
        addNotif(`Action "${action}" executed successfully.`);
        loadAdminComplaints();
        fetchPosts(); // Refresh community posts if one was deleted
    } catch(e) {
        addNotif('⚠️ Failed to resolve complaint: ' + e.message);
    }
}

// ── GPA MODULE SYNC FROM SYLLABUS CATALOG ──
function addCatalogModuleToGpa(code, name, credits) {
    if(!isLoggedIn) { toggleAuth(); return; }
    
    const fullName = `${code} ${name}`;
    const exists = gpaModules.some(m => m.name.toLowerCase() === fullName.toLowerCase() || m.name.toLowerCase().includes(code.toLowerCase()));
    
    if (exists) {
        addNotif(`⚠️ Module ${code} is already in your GPA list!`);
        return;
    }
    
    gpaModules.push({ name: fullName, credits: parseInt(credits), grade: 'A' });
    renderGpa();
    addNotif(`🎓 Added ${code} to GPA Calculator!`);
}

// ── INIT ──
window.onload = () => {
    initLang();
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