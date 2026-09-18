<?php
$request = service('request');
helper('menu');
$user = user();
$group = isset($user->category) ? $user->category : 'admin';
$menuHtml = null;
if (!empty($isLightweightBackendPage)) {
    $menuCacheKey = 'dashboard_menu_' . hash('sha256', $group . ':' . $request->getUri()->getPath());
    $menuHtml = cache($menuCacheKey);
}

if (!is_string($menuHtml)) {
    $menuHtml = display_menu_backend(0, 1, $group);
    if (!empty($menuCacheKey)) {
        cache()->save($menuCacheKey, $menuHtml, 300);
    }
}
?>

<style>
    .sidebar-argon {
        width: 250px;
        background: white;
        height: calc(100vh - 40px);
        position: fixed;
        top: 20px;
        left: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 20px 0 rgba(0,0,0,0.05);
        z-index: 1000;
        overflow-y: auto;
        padding: 20px;
        transition: width 0.3s ease;
    }

    .sidebar-brand {
        position: relative;
        padding: 10px 45px 10px 15px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        color: #344767;
        font-size: 16px;
    }

    .brand-icon {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #5e72e4, #825ee4);
        border-radius: 8px;
    }

    .nav-menu-argon {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .nav-label-argon {
        font-size: 11px;
        font-weight: 700;
        color: #526079;
        text-transform: uppercase;
        margin: 20px 0 10px 10px;
        letter-spacing: 0.5px;
    }

    .nav-menu-argon li a {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        color: #526079;
        text-decoration: none;
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.2s;
        margin-bottom: 4px;
    }

    .nav-menu-argon li a:hover, .nav-menu-argon li a.active {
        background-color: #f6f9fc;
        color: #344767;
    }

    .nav-menu-argon li a.active {
        background: #f6f9fc;
        font-weight: 600;
    }

    .nav-menu-argon li a i {
        width: 30px;
        height: 30px;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        color: #5e72e4;
        font-size: 12px;
    }

    .nav-menu-argon li a.active i {
        background: linear-gradient(135deg, #5e72e4, #825ee4);
        color: white;
    }

    /* Parent menu highlight when child is active */
    .has-submenu.open > a.submenu-toggle.active {
        background: #f6f9fc;
        color: #344767;
        font-weight: 600;
    }

    .has-submenu.open > a.submenu-toggle.active i:first-child {
        background: linear-gradient(135deg, #5e72e4, #825ee4);
        color: white;
    }

    /* Active submenu item highlight */
    .submenu-argon li a.active {
        color: #5e72e4;
        font-weight: 600;
        background: #eef0fd;
        border-radius: 8px;
    }

    /* Submenu */
    .submenu-argon {
        display: none;
        list-style: none;
        padding: 0;
        margin: 0;
        padding-left: 20px;
        margin-top: 5px;
    }

    .submenu-argon li a {
        font-size: 15px;
        padding: 8px 15px;
    }

    /* Open state */
    .has-submenu.open > .submenu-argon {
        display: block;
    }

    /* Caret */
    .caret {
        margin-left: auto;
        font-size: 12px;
        transition: transform .3s;
    }

    .has-submenu.open .caret {
        transform: rotate(180deg);
    }

    /* Toggle button - selalu visible */
    .sidebar-toggle {
        position: absolute;
        top: 15px;
        right: 15px;
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #5e72e4;
        z-index: 10;
        padding: 8px;
        transition: all 0.3s ease;
        border-radius: 6px;
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sidebar-toggle:hover {
        background: #f6f9fc;
        color: #344767;
    }

    /* Logo container */
    .sidebar-brand > div {
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .sidebar-brand > div img {
        transition: all 0.3s ease;
    }

    /* ===== Collapsed State ===== */
    body.sidebar-collapsed .sidebar-argon {
        width: 80px;
    }

    body.sidebar-collapsed .app-container {
        padding-left: 100px;
    }

    /* Hide text */
    body.sidebar-collapsed .nav-menu-argon span,
    body.sidebar-collapsed .brand-text {
        display: none;
    }

    /* Center icons */
    body.sidebar-collapsed .nav-menu-argon a {
        justify-content: center;
        padding: 12px 10px;
    }

    body.sidebar-collapsed .nav-menu-argon i {
        margin-right: 0;
    }

    /* Submenu behavior */
    body.sidebar-collapsed .submenu-argon {
        display: none !important;
    }

    body.sidebar-collapsed .caret {
        display: none;
    }

    /* Center logo saat collapsed */
    body.sidebar-collapsed .sidebar-brand {
        justify-content: center;
        padding: 10px 15px;
        flex-direction: column;
        align-items: center;
    }

    body.sidebar-collapsed .sidebar-brand > div {
        margin: 0 auto;
    }

    body.sidebar-collapsed .sidebar-brand > div img {
        width: 45px !important;
        height: 45px !important;
        margin-bottom: 10px !important;
    }

    /* Toggle button saat collapsed - tetap di kanan atas */
    body.sidebar-collapsed .sidebar-toggle {
        top: 15px;
        right: 15px;
        left: auto;
        transform: none;
    }

    /* Sidebar footer */
    .sidebar-footer {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px;
        margin-top: 20px;
        border-top: 1px solid #e9ecef;
    }

    .sidebar-footer img {
        border-radius: 50%;
    }

    .sidebar-footer div {
        flex: 1;
    }

    .sidebar-footer strong {
        display: block;
        font-size: 14px;
        color: #344767;
        margin-bottom: 3px;
    }

    .sidebar-footer a {
        font-size: 13px;
        text-decoration: none;
    }

    body.sidebar-collapsed .sidebar-footer {
        flex-direction: column;
        text-align: center;
    }

    body.sidebar-collapsed .sidebar-footer div {
        display: none;
    }

    /* Scrollbar styling */
    .sidebar-argon::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-argon::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-argon::-webkit-scrollbar-thumb {
        background: #e9ecef;
        border-radius: 10px;
    }

    .sidebar-argon::-webkit-scrollbar-thumb:hover {
        background: #cbd5e0;
    }
</style>

<aside class="sidebar-argon" id="dashboard-sidebar" aria-label="Navigasi utama">
    <div class="sidebar-brand">
        <!-- Toggle button - posisi absolute di kanan atas -->
        <button class="sidebar-toggle" id="sidebarToggle" type="button"
            title="Buka atau tutup sidebar" aria-label="Buka atau tutup sidebar"
            aria-controls="dashboard-sidebar" aria-expanded="true">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>
        
        <div>
            <img src="<?= esc($sidebarLogoUrl ?? (!empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/default-perpus.png'))) ?>"
                alt="Logo <?= esc($nama_perpustakaan ?? 'perpustakaan') ?>" width="80" height="80"
                style="width:80px;height:80px;object-fit:contain;border-radius:16px;margin-bottom:20px;" fetchpriority="high">
        </div>
        <span class="brand-text">INLISLite</span>
    </div>

    <?= $menuHtml ?>

    <div class="sidebar-footer">
        <img src="<?= base_url('themes/uigniter/images/avatars/2.jpg') ?>" width="35" height="35" alt="Avatar pengguna" loading="lazy">
        <div>
            <strong><?= esc(user()->username) ?></strong>
            <a href="<?= base_url('logout') ?>" class="text-danger">Logout</a>
        </div>
    </div>
</aside>
