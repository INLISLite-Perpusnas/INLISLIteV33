<style>
html {
	font-size: 100% !important;
}

.toggle-group label.toggle-off{
	left: 47% !important;
}

.select-wrapper > .select2-container--bootstrap4 { 
    width: auto;
    flex: 1 1 auto;
}

.select-wrapper > .select2-container--bootstrap4 .select2-selection--single {
    height: 100%;
    line-height: inherit;
    padding: 0.5rem 1rem;
}

div.dataTables_wrapper div.dataTables_filter {
  float: none;
  text-align: left;
}

div.dataTables_wrapper div.dataTables_paginate {
  float: none;
  text-align: right;
}

:root {
  --sidebar-width: 300px;
  --min-sidebar-width: -300px;
}

.drawer-open {
	/* width: calc(100% - var(--sidebar-width)) !important; */
	width: 75% !important;
	z-index: 1000 !important;
}

.swal2-container {
    z-index: 2000 !important;
}

.select2-container--default .select2-selection--single{
	padding:6px;
	height: 37px;
	font-size: 1.1em;  
	position: relative;
}

.tox.tox-tinymce.tox-fullscreen {
    z-index: 1050;
    top: 60px!important;
    left: 85px!important;
    width: calc(100% - 90px) !important;
}

/* Reset & Clean Up */
.app-sidebar {
    width: 250px !important;
    background: white !important;
    border-radius: 1rem !important;
    margin: 1.5rem 0 1.5rem 1.5rem !important;
    height: calc(100vh - 3rem) !important;
    position: fixed !important;
    box-shadow: 0 20px 27px 0 rgba(0, 0, 0, 0.05) !important;
    border: none !important;
    z-index: 1000;
}

/* Hilangkan bullet points dari metisMenu */
.vertical-nav-menu, .vertical-nav-menu ul {
    list-style-type: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

/* Link Menu Utama */
.vertical-nav-menu li a {
    display: flex !important;
    align-items: center !important;
    padding: 0.675rem 1rem !important;
    color: #67748e !important;
    font-weight: 500;
    font-size: 0.875rem;
    border-radius: 0.5rem;
    margin: 0 1rem !important;
    transition: all 0.2s ease;
    text-decoration: none !important;
}

.vertical-nav-menu li a:hover, 
.vertical-nav-menu li a.mm-active {
    background-color: #f6f9fc !important;
    color: #344767 !important;
}

/* Container Icon (Kotak Putih di belakang Icon) */
.vertical-nav-menu i.menu-icon {
    width: 32px;
    height: 32px;
    background: #ffffff;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    color: #5e72e4; /* Warna Biru Argon */
    font-size: 0.8rem;
}

/* Meratakan Sub-menu (Dropdown) */
.vertical-nav-menu ul li a {
    margin-left: 2.5rem !important;
    font-size: 0.8rem !important;
    padding: 0.5rem 1rem !important;
}

/* Section Title (NAVIGASI, PAGES) */
.nav-title-argon {
    padding: 1.5rem 1.5rem 0.5rem 1.5rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #8392ab;
    letter-spacing: 0.05rem;
}


</style>

<style>
    .bg-corporate-primary{
        background-color: <?=get_parameter('corporate-primary','#C21B18')?> !important;
    }
    .bg-corporate-primary2{
        background-color: <?=get_parameter('corporate-primary2','#E9583C')?> !important;
    }
    .bg-corporate-secondary{
        background-color: <?=get_parameter('corporate-secondary','#7B0E23')?>  !important;
    }
    .bg-corporate-secondary2{
        background-color: <?=get_parameter('corporate-secondary2','#8D1230')?>  !important;
    }
    
    .app-page-title .page-title-icon {
        padding: 0px;
        width: 50px;
        height: 50px;
    }

    .errors {
        color: red;
    }

    .app-header__logo .logo-src {
        width: 150px;
    }

    i.adminigniter-icon {
        text-align: center;
        width: 34px;
        height: 34px;
        line-height: 34px;
        position: absolute;
        left: 5px;
        top: 50%;
        margin-top: -17px;
        font-size: 1.5rem;
        opacity: .3;
        transition: color .3s;
    }

    i.adminigniter-fa {
        display: inline-block;
        speak: none;
        font-style: normal;
        font-weight: 400;
        font-variant: normal;
        text-transform: none;
        line-height: 1;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    
</style>