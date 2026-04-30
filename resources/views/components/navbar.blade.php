<div class="navbar bg-base-100 shadow-sm px-4">

  <!-- LEFT -->
  <div class="navbar-start">
    <a href="#">
        <img src="./favicon.png" alt="Logo" class="w-14 mr-2 transition active:scale-95">
    </a>
  </div>


  <!-- CENTER -->
  <div class="navbar-center hidden lg:flex font-medium">
    <ul class="menu menu-horizontal px-1">
      <li><a>Beranda</a></li>
      <li><a>Menu</a></li>
      <li><a>Riwayat</a></li>
      <li><a>Tentang Kami</a></li>
    </ul>
  </div>


  <!-- RIGHT -->
  <div class="navbar-end">

    <!-- CART -->
    <div class="dropdown dropdown-end">
      <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
        <div class="indicator">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <path fill="currentColor" d="M17 18c-1.11 0-2 .89-2 2a2 2 0 0 0 2 
            2a2 2 0 0 0 2-2a2 2 0 0 0-2-2M1 2v2h2l3.6 7.59l-1.36 
            2.45c-.15.28-.24.61-.24.96a2 2 0 0 0 2 2h12v-2H7.42a.25.25 0 0 1-.25-.25q0-.075.03-.12L8.1 
            13h7.45c.75 0 1.41-.42 1.75-1.03l3.58-6.47c.07-.16.12-.33.12-.5a1 1 0 0 0-1-1H5.21l-.94-2M7 
            18c-1.11 0-2 .89-2 2a2 2 0 0 0 2 2a2 2 0 0 0 2-2a2 2 0 0 0-2-2"/></svg>
          <span class="badge badge-sm indicator-item">8</span>
        </div>
      </div>

      <div tabindex="0"
        class="card card-compact dropdown-content bg-base-100 z-10 mt-3 w-52 shadow">
        <div class="card-body">
          <span class="text-lg font-bold">8 Items</span>
          <span class="text-info">Subtotal: $999</span>
          <div class="card-actions">
            <button class="btn btn-primary btn-block">View cart</button>
          </div>
        </div>
      </div>
    </div>


    <!-- AVATAR -->
    <div class="dropdown dropdown-end ml-2">
      <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
        <div class="w-10 rounded-full">
          <img src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" />
        </div>
      </div>

      <ul tabindex="0"
        class="menu menu-sm dropdown-content bg-base-100 rounded-box z-10 mt-3 w-52 p-2 shadow">
        <li>
          <a class="justify-between">
            Profile
            <span class="badge">New</span>
          </a>
        </li>
        <li><a>Settings</a></li>
        <li><a>Logout</a></li>
      </ul>
    </div>

  </div>
</div>