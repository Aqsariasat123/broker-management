<div class="page-header-right">
  <div class="user-avatar">
    @if(auth()->check() && auth()->user()->profile_picture)
      <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="User">
    @else
      <img src="{{ asset('asset/user.png') }}" alt="User">
    @endif
  </div>
  <a href="{{ route('logout') }}" class="logout-btn" title="Logout">
    <i class="fas fa-sign-out-alt"></i>
  </a>
</div>
