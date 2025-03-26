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