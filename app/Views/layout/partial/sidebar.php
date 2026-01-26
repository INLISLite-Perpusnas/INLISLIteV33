<?php
$db=db_connect();
$logo = $db->table('settingparameters')->where('Name', 'Logo')->get()->getRow()->Value;
use Dompdf\Css\Style;

$request = service('request');
helper('menu');
$group = user()->category ?? 'admin';
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
    }

    .sidebar-brand {
        padding: 10px 15px;
        margin-bottom: 30px;
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
        color: #8392ab;
        text-transform: uppercase;
        margin: 20px 0 10px 10px;
        letter-spacing: 0.5px;
    }

    .nav-menu-argon li a {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        color: #67748e;
        text-decoration: none;
        border-radius: 8px;
        font-size: 14px;
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

    /* Sidebar */
.sidebar-argon {
    width: 250px;
    background: #fff;
    height: calc(100vh - 40px);
    position: fixed;
    top: 20px;
    left: 20px;
    border-radius: 15px;
    padding: 20px;
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

/* Menu */
.nav-menu-argon,
.submenu-argon {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-menu-argon li a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 15px;
    border-radius: 8px;
    color: #67748e;
    text-decoration: none;
    font-size: 14px;
}

.nav-menu-argon li a:hover,
.nav-menu-argon li a.active {
    background: #f6f9fc;
    color: #344767;
    font-weight: 600;
}

/* Icons */
.nav-menu-argon i {
    width: 30px;
    height: 30px;
    background: #fff;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #5e72e4;
    box-shadow: 0 2px 4px rgba(0,0,0,.05);
}

/* Submenu */
.submenu-argon {
    display: none;
    padding-left: 20px;
    margin-top: 5px;
}

.submenu-argon li a {
    font-size: 13px;
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
/* ===== Layout ===== */
.app-container {
    padding-left: 250px; /* RAPAT */
    transition: padding-left 0.3s ease;
}

/* Sidebar */
.sidebar-argon {
    width: 250px;
    transition: width 0.3s ease;
}

/* ===== Collapsed State ===== */
body.sidebar-collapsed .sidebar-argon {
    width: 80px;
}

body.sidebar-collapsed .app-container {
    padding-left: 110px;
}

/* Hide text */
body.sidebar-collapsed .nav-menu-argon span,
body.sidebar-collapsed .brand-text {
    display: none;
}

/* Center icons */
body.sidebar-collapsed .nav-menu-argon a {
    justify-content: center;
}

body.sidebar-collapsed .nav-menu-argon i {
    margin-right: 0;
}

/* Submenu behavior */
body.sidebar-collapsed .submenu-argon {
    display: none !important;
}

/* Toggle button */
.sidebar-toggle {
    margin-left: auto;
    background: none;
    border: none;
    font-size: 16px;
    cursor: pointer;
    color: #5e72e4;
}

</style>

<aside class="sidebar-argon">
   <div class="sidebar-brand">
    <div>
        <img src="<?= !empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/default-perpus.png') ?>" style="width: 80px; height: 80px; object-fit: contain; border-radius: 16px; margin-bottom: 20px;">
    </div>
    <span class="brand-text">INLISLite</span><br>
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
</div>

    <?= display_menu_backend(0, 1, user()->category ?? 'admin'); ?>

    <div class="sidebar-footer">
        <img src="<?= base_url('themes/uigniter/images/avatars/2.jpg') ?>" width="35">
        <div>
            <strong><?= user()->username ?></strong><br>
            <a href="<?= base_url('logout') ?>" class="text-danger">Logout</a>
        </div>
    </div>
</aside>
