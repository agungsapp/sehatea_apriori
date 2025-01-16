<nav id="sidebarMenu" class="sidebar d-lg-block collapse bg-gray-800 text-white" data-simplebar>
  <div class="sidebar-inner px-4 pt-3">
    <div class="user-card d-flex d-md-none align-items-center justify-content-between justify-content-md-center pb-4">
      <div class="d-flex align-items-center">
        <div class="avatar-lg me-4">
          <img src="{{ asset('volt') }}/assets/img/team/profile-picture-3.jpg"
            class="card-img-top rounded-circle border-white" alt="Bonnie Green" />
        </div>
        <div class="d-block">
          <h2 class="h5 mb-3">Hi, admin</h2>
          <a href="{{ asset('volt') }}/pages/examples/sign-in.html"
            class="btn btn-secondary btn-sm d-inline-flex align-items-center">
            <svg class="icon icon-xxs me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            Sign Out
          </a>
        </div>
      </div>
      <div class="collapse-close d-md-none">
        <a href="#sidebarMenu" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu"
          aria-expanded="true" aria-label="Toggle navigation">
          <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"></path>
          </svg>
        </a>
      </div>
    </div>
    <ul class="nav flex-column pt-md-0 pt-3 pb-5">
      <li class="nav-item">
        <a href="{{ asset('volt') }}/index.html" class="nav-link d-flex align-items-center">
          <span class="sidebar-icon">
            <img src="{{ asset('volt') }}/assets/img/brand/light.svg" height="20" width="20" alt="Volt Logo" />
          </span>
          <span class="sidebar-text ms-1 mt-1">Sehatea Admin</span>
        </a>
      </li>
      <li class="nav-item {{ Route::is('dashboard') ? 'active' : '' }}">
        <a href="{{ route('dashboard') }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
              <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
            </svg>
          </span>
          <span class="sidebar-text">Dashboard</span>
        </a>
      </li>
      <li class="nav-item {{ Route::is('pengguna') ? 'active' : '' }}">
        <a href="{{ route('pengguna') }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
              <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
            </svg>
          </span>
          <span class="sidebar-text">Data Pengguna</span>
        </a>
      </li>

      <li role="separator" class="dropdown-divider mb-3 mt-4 border-gray-700"></li>

      {{-- master data --}}
      @php
        $currentMaster = ['cabang', 'kategori', 'merk', 'brand', 'supplier', 'bank', 'instansi', 'jasa'];
      @endphp
      <li class="nav-item {{ in_array(Route::currentRouteName(), $currentMaster) ? 'active' : '' }}">
        <span
          class="nav-link {{ in_array(Route::currentRouteName(), $currentMaster) ? 'collapsed' : '' }} d-flex justify-content-between align-items-center"
          data-bs-toggle="collapse" data-bs-target="#submenu-master">
          <span>
            <span class="sidebar-icon">
              <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                  d="M5 4a3 3 0 00-3 3v6a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3H5zm-1 9v-1h5v2H5a1 1 0 01-1-1zm7 1h4a1 1 0 001-1v-1h-5v2zm0-4h5V8h-5v2zM9 8H4v2h5V8z"
                  clip-rule="evenodd"></path>
              </svg>
            </span>
            <span class="sidebar-text">Master Data</span>
          </span>
          <span class="link-arrow">
            <svg class="icon icon-sm" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"></path>
            </svg>
          </span>
        </span>
        <div
          class="multi-level collapsed {{ in_array(Route::currentRouteName(), $currentMaster) ? 'show' : '' }} collapse"
          role="list" id="submenu-master" aria-expanded="false">
          @php
            $masterDataItems = [
                ['route' => 'jenis-pengeluaran', 'text' => 'Jenis Pengeluaran'],
                ['route' => 'sumber-dana', 'text' => 'Sumber Dana'],

                // ['route' => 'brand', 'text' => 'Brand'],
                // ['route' => 'cabang', 'text' => 'Cabang'],
                // ['route' => 'instansi', 'text' => 'Instansi'],
                // ['route' => 'jasa', 'text' => 'Jasa'],
                // ['route' => 'kategori', 'text' => 'Kategori'],
                // ['route' => 'supplier', 'text' => 'Supplier'],
            ];
          @endphp

          @foreach ($masterDataItems as $item)
            <ul class="flex-column nav">
              <li class="nav-item {{ Route::currentRouteName() == $item['route'] ? 'active' : '' }}">
                <a class="nav-link" href="{{ route($item['route']) }}">
                  <span class="sidebar-text">{{ $item['text'] }}</span>
                </a>
              </li>
            </ul>
          @endforeach
        </div>
      </li>

      <li class="nav-item {{ Route::is('produk') ? 'active' : '' }}">
        <a href="{{ route('produk') }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
              <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
            </svg>
          </span>
          <span class="sidebar-text">Data Produk</span>
        </a>
      </li>
      <li class="nav-item {{ Route::is('komposisi') ? 'active' : '' }}">
        <a href="{{ route('komposisi') }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
              <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
            </svg>
          </span>
          <span class="sidebar-text">Data Komposisi</span>
        </a>
      </li>
      <li class="nav-item {{ Route::is('bahan') ? 'active' : '' }}">
        <a href="{{ route('bahan') }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
              <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
            </svg>
          </span>
          <span class="sidebar-text">Data Bahan</span>
        </a>
      </li>
      <li class="nav-item {{ Route::is('konversi-satuan') ? 'active' : '' }}">
        <a href="{{ route('konversi-satuan') }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
              <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
            </svg>
          </span>
          <span class="sidebar-text">Data Konversi Bahan</span>
        </a>
      </li>
      <li class="nav-item {{ Route::is('pembelian-bahan') ? 'active' : '' }}">
        <a href="{{ route('pembelian-bahan') }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
              <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
            </svg>
          </span>
          <span class="sidebar-text">Pembelian Bahan</span>
        </a>
      </li>
      <li class="nav-item {{ Route::is('pengeluaran-lain') ? 'active' : '' }}">
        <a href="{{ route('pengeluaran-lain') }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
              <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
            </svg>
          </span>
          <span class="sidebar-text">Pengeluaran Lain</span>
        </a>
      </li>
      <li class="nav-item {{ Route::is('mode-kasir') ? 'active' : '' }}">
        <a href="{{ route('mode-kasir') }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
              <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
            </svg>
          </span>
          <span class="sidebar-text">Mode Kasir</span>
        </a>
      </li>




    </ul>
  </div>
</nav>
