<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
          <span class="login-status online"></span>
          <!--change to offline or busy as needed-->
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">{{ Auth::user()->name ?? 'Guest' }}</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('dashboard') }}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>
    <li class="nav-item {{ request()->routeIs(['book.index', 'book.create', 'book.store']) ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('book.index') }}">
        <span class="menu-title">Buku</span>
        <i class="mdi mdi-book-open-page-variant menu-icon"></i>
      </a>
    </li>
    <li class="nav-item {{ request()->routeIs(['category.index', 'category.create', 'category.store']) ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('category.index') }}">
        <span class="menu-title">Kategori</span>
        <i class="mdi mdi-format-list-bulleted menu-icon"></i>
      </a>
    </li>
    <li class="nav-item {{ request()->is('customer*') ? 'active' : '' }}">
      <a class="nav-link" data-bs-toggle="collapse" href="#customerDropdown" aria-expanded="{{ request()->is('customer*') ? 'true' : 'false' }}" aria-controls="customerDropdown">
        <span class="menu-title">Customer</span>
        <i class="mdi mdi-account-group menu-icon"></i>
      </a>
      <div class="collapse {{ request()->is('customer*') ? 'show' : '' }}" id="customerDropdown">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.index') ? 'active' : '' }}" href="{{ route('customer.index') }}">Data Customer</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.create1') ? 'active' : '' }}" href="{{ route('customer.create1') }}">Tambah Customer 1</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.create2') ? 'active' : '' }}" href="{{ route('customer.create2') }}">Tambah Customer 2</a>
          </li>
        </ul>
      </div>
    </li>
    <li class="nav-item {{ request()->routeIs(['barang.index', 'barang.create', 'barang.store']) ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('barang.index') }}">
        <span class="menu-title">Barang</span>
        <i class="mdi mdi-tag-multiple menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('antrian.admin') }}">
        <span class="menu-title">Antrian</span>
        <i class="mdi mdi-monitor-dashboard menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
    <a class="nav-link" href="{{ route('absensi.scan') }}">
        <span class="menu-title">Absensi NFC</span>
        <i class="mdi mdi-cellphone-nfc menu-icon"></i>
    </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('barang.kasir') }}">
        <span class="menu-title">Kasir (POS)</span>
        <i class="mdi mdi-cart menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('barang.scanner') }}">
        <span class="menu-title">Scanner Barang</span>
        <i class="mdi mdi-barcode-scan menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('kantin.scanner') }}">
        <span class="menu-title">Scanner Kantin</span>
        <i class="mdi mdi-qrcode-scan menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('toko.index') }}">
        <span class="menu-title">Toko</span>
        <i class="mdi mdi-store menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('kunjungan.index') }}">
        <span class="menu-title">Kunjungan</span>
        <i class="mdi mdi-map-marker-distance menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('wilayah.index') }}">
        <span class="menu-title">Wilayah</span>
        <i class="mdi mdi-map-marker-radius menu-icon"></i>
      </a>
    </li>
    <li class="nav-item {{ request()->routeIs('pdf.*') ? 'active' : '' }}">
      <a class="nav-link" data-bs-toggle="collapse" href="#pdfMenu" aria-expanded="false">
        <span class="menu-title">Generate PDF</span>
        <i class="mdi mdi-file-pdf-box menu-icon"></i>
      </a>
      <div class="collapse" id="pdfMenu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('pdf.sertifikat') }}">Sertifikat</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('pdf.undangan') }}">Undangan</a>
          </li>
        </ul>
      </div>
    </li>
  </ul>
</nav>
<!-- partial -->