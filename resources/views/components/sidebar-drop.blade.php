{{-- resources/views/components/sidebar-drop.blade.php --}}
<li class="nav-item {{ $isActive() ? 'active' : '' }}">
  <span class="nav-link {{ $isActive() ? 'collapsed' : '' }} d-flex justify-content-between align-items-center"
    data-bs-toggle="collapse" data-bs-target="#submenu-{{ $menuId }}">
    <span>
      <span class="sidebar-icon">
        {!! $icon !!}
        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd"
            d="M5 4a3 3 0 00-3 3v6a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3H5zm-1 9v-1h5v2H5a1 1 0 01-1-1zm7 1h4a1 1 0 001-1v-1h-5v2zm0-4h5V8h-5v2zM9 8H4v2h5V8z"
            clip-rule="evenodd"></path>
        </svg>
      </span>
      <span class="sidebar-text">{{ $title }}</span>
    </span>
    <span class="link-arrow">
      <svg class="icon icon-sm" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
          d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
          clip-rule="evenodd"></path>
      </svg>
    </span>
  </span>
  <div class="multi-level collapsed {{ $isActive() ? 'show' : '' }} collapse" role="list"
    id="submenu-{{ $menuId }}" aria-expanded="false">
    @foreach ($menuItems as $item)
      <ul class="flex-column nav">
        <li class="nav-item {{ Route::currentRouteName() == $item['route'] ? 'active' : '' }}">
          <a class="nav-link" wire:navigate href="{{ route($item['route']) }}">
            <span class="sidebar-text">{{ $item['text'] }}</span>
          </a>
        </li>
      </ul>
    @endforeach
  </div>
</li>
