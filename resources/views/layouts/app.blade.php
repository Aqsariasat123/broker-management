<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Keystone Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="container-custom">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      @include('partials.sidebar')
    </div>
   
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
      <!-- Top Header with Profile -->
      <div class="top-header">
        <button class="toggle-btn" id="toggleBtn" aria-label="Toggle sidebar">
          <span class="toggle-icon-open">☰</span>
          <span class="toggle-icon-close">✕</span>
        </button>
        <div class="profile-dropdown">
          <div style="display:flex; align-items:center; gap:10px;">
            <span class="profile-name" style="font-size:14px; color:#2d2d2d;">{{ auth()->user()->name ?? 'User' }}</span>
            <a href="{{ route('logout') }}" class="btn" style="background:#dc3545; color:#fff; border-color:#dc3545; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:13px;">Logout</a>
          </div>
          <button class="profile-btn" id="profileBtn" aria-label="Profile menu" style="display:none;">
            <div class="profile-avatar">
              <span>{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
            </div>
            <span class="profile-name">{{ auth()->user()->name ?? 'User' }}</span>
            <svg class="profile-arrow" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <div class="profile-dropdown-menu" id="profileDropdown">
            <div class="profile-dropdown-header">
              <div class="profile-avatar-large">
                <span>{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
              </div>
              <div class="profile-info">
                <div class="profile-name-large">{{ auth()->user()->name ?? 'User' }}</div>
                <div class="profile-email">{{ auth()->user()->email ?? '' }}</div>
              </div>
            </div>
            <div class="profile-dropdown-divider"></div>
            <a href="{{ route('users.show', auth()->id()) }}" class="profile-dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span>My Profile</span>
            </a>
            <a href="{{ route('users.edit', auth()->id()) }}" class="profile-dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M18.5 2.5C18.8978 2.10218 19.4374 1.87868 20 1.87868C20.5626 1.87868 21.1022 2.10218 21.5 2.5C21.8978 2.89782 22.1213 3.43739 22.1213 4C22.1213 4.56261 21.8978 5.10218 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span>Settings</span>
            </a>
            <div class="profile-dropdown-divider"></div>
            <a href="{{ route('logout') }}" class="profile-dropdown-item profile-dropdown-item-danger">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M21 12H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span>Logout</span>
            </a>
          </div>
        </div>
      </div>
  
      @yield('content')
    </div>
  </div>

  <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
