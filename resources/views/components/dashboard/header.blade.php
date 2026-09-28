<header class="dashboard-header">
    <div class="header-left">
        <div class="header-search">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search..." id="headerSearch">
        </div>
    </div>
    <div class="header-right">
        <div class="header-date">
            <i class="far fa-calendar-alt"></i>
            <span id="currentDate"></span>
        </div>
        <div class="header-notifications">
            <i class="far fa-bell"></i>
            <span class="notification-dot"></span>
        </div>
        <div class="header-user" id="headerUserMenu">
        <div class="user-avatar-sm" style="overflow:hidden;cursor:pointer;" onclick="toggleUserMenu(event)">
            <img src="{{ asset('pngtree-vector-users-icon-png-image_4144740.jpg') }}" alt="Avatar" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
        </div>
        <div class="user-dropdown" id="userDropdown">
            <a href="#" class="dropdown-item"><i class="fas fa-user"></i> Profile</a>
            <a href="#" class="dropdown-item"><i class="fas fa-cog"></i> Settings</a>
            <div class="dropdown-divider"></div>
            <a href="{{ route('logout') }}" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const now = new Date();
    const options = { year: 'numeric', month: 'long', day: 'numeric', weekday: 'long' };
    document.getElementById('currentDate').textContent = now.toLocaleDateString('ne-NP', options);
});

function toggleUserMenu(e) {
    e.stopPropagation();
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('userDropdown');
    const menu = document.getElementById('headerUserMenu');
    if (dropdown.classList.contains('show') && !menu.contains(e.target)) {
        dropdown.classList.remove('show');
    }
});
</script>

<style>
.dashboard-header {
    height: 60px;
    background: #ffffff;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 24px;
    position: sticky;
    top: 0;
    z-index: 100;
}

.header-left {
    display: flex;
    align-items: center;
    flex: 1;
}

.header-search {
    display: flex;
    align-items: center;
    background: #f8f9fa;
    border-radius: 10px;
    padding: 0 16px;
    border: 2px solid transparent;
    transition: all 0.2s;
    max-width: 400px;
    width: 100%;
}

.header-search:focus-within {
    border-color: #CD2737;
    background: white;
}

.header-search i {
    color: #6c757d;
    margin-right: 10px;
}

.header-search input {
    border: none;
    background: transparent;
    padding: 10px 0;
    font-size: 1.3rem;
    font-family: 'Noto Sans Devanagari', sans-serif;
    outline: none;
    width: 100%;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 24px;
}

.header-date {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 1.3rem;
    color: #6c757d;
    white-space: nowrap;
}

.header-notifications {
    position: relative;
    font-size: 1.8rem;
    color: #6c757d;
    cursor: pointer;
}

.notification-dot {
    position: absolute;
    top: -2px;
    right: -4px;
    width: 8px;
    height: 8px;
    background: #CD2737;
    border-radius: 50%;
}

.header-user {
    position: relative;
}

.header-user .user-avatar-sm {
    width: 36px;
    height: 36px;
    background: #CD2737;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1.4rem;
    cursor: pointer;
}

.user-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    min-width: 200px;
    padding: 8px 0;
    display: none;
    z-index: 200;
    animation: dropdownFadeIn 0.2s ease;
}

.user-dropdown.show {
    display: block;
}

@keyframes dropdownFadeIn {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 18px;
    color: #333;
    font-size: 1.3rem;
    text-decoration: none;
    transition: background 0.2s;
    cursor: pointer;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    font-family: 'Noto Sans Devanagari', sans-serif;
}

.dropdown-item i {
    width: 18px;
    color: #6c757d;
    font-size: 1.4rem;
}

.dropdown-item:hover {
    background: #f8f9fa;
    color: #CD2737;
}

.dropdown-item:hover i {
    color: #CD2737;
}

.dropdown-divider {
    height: 1px;
    background: #e9ecef;
    margin: 4px 0;
}

@media (max-width: 768px) {
    .dashboard-header {
        padding: 0 16px;
    }
    .header-date {
        display: none;
    }
    .header-search {
        max-width: 200px;
    }
}
</style>

