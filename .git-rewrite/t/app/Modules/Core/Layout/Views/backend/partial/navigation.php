<?php
$request = service('request'); ?>


<li class="app-sidebar__heading">Menu</li>
<li class="mm-active "><a href="<?= base_url() ?>/dashboard" class="mm-active" aria-expanded="true"><i class="metismenu-icon pe-7s-display1" style=""></i>
        Dashboard
    </a></li>
<li class=" "><a href="<?= base_url() ?>/page" class=""><i class="metismenu-icon pe-7s-display2" style=""></i>
        Halaman
    </a></li>
<li class=" ">
    <a href="#" class=""><i class="metismenu-icon pe-7s-photo-gallery" style=""></i>
        Koleksi Digital <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>
    <ul class="mm-collapse">
        <li class=" "><a href="<?= base_url() ?>/media/category_index" class=""><i class="metismenu-icon " style=""></i>
                Kategori Koleksi
            </a></li>
        <li class=" "><a href="<?= base_url() ?>/media/index?type=all" class=""><i class="metismenu-icon " style=""></i>
                Semua Koleksi
            </a></li>
        <li class=" "><a href="<?= base_url() ?>/media/index?type=image" class=""><i class="metismenu-icon " style=""></i>
                Koleksi Foto
            </a></li>
        <li class=" "><a href="<?= base_url() ?>/media/index?type=video" class=""><i class="metismenu-icon " style=""></i>
                Koleksi Video
            </a></li>
        <li class=" "><a href="<?= base_url() ?>/media/index?type=audio" class=""><i class="metismenu-icon " style=""></i>
                Koleksi Audio
            </a></li>
        <li class=" "><a href="<?= base_url() ?>/media/index?type=pdf" class=""><i class="metismenu-icon " style=""></i>
                Koleksi PDF Flipbook
            </a></li>
    </ul>
</li>
<li class=" "><a href="<?= base_url() ?>/announcement" class=""><i class="metismenu-icon pe-7s-volume" style=""></i>
        Pengumuman
    </a></li>
<li class=" "><a href="<?= base_url() ?>/event" class=""><i class="metismenu-icon pe-7s-date" style=""></i>
        Agenda
    </a></li>
<li class=" ">
    <a href="#" class=""><i class="metismenu-icon pe-7s-news-paper" style=""></i>
        Artikel <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>
    <ul class="mm-collapse">
        <li class=" "><a href="<?= base_url() ?>/blog/category_index" class=""><i class="metismenu-icon " style=""></i>
                Kategori Artikel
            </a></li>
        <li class=" "><a href="<?= base_url() ?>/blog" class=""><i class="metismenu-icon " style=""></i>
                Semua Artikel
            </a></li>
    </ul>
</li>
<li class=" "><a href="<?= base_url() ?>/banner" class=""><i class="metismenu-icon pe-7s-photo" style=""></i>
        Banner
    </a></li>
<li class=" "><a href="<?= base_url() ?>/faq" class=""><i class="metismenu-icon pe-7s-study" style=""></i>
        FAQ
    </a></li>
<li class=" "><a href="<?= base_url() ?>/testimonial" class=""><i class="metismenu-icon pe-7s-comment" style=""></i>
        Testimoni
    </a></li>
<li class=" "><a href="<?= base_url() ?>/contact" class=""><i class="metismenu-icon pe-7s-id" style=""></i>
        Kontak
    </a></li>
<li class=" ">
    <a href="#" class=""><i class="metismenu-icon pe-7s-graph2" style=""></i>
        Laporan <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>
    <ul class="mm-collapse">
        <li class=" "><a href="<?= base_url() ?>/report/visitors" class=""><i class="metismenu-icon pe-7s-graph2" style=""></i>
                Laporan Kunjungan
            </a></li>
    </ul>
</li>
<li class="app-sidebar__heading">Admin</li>
<li class=" "><a href="<?= base_url() ?>/user" class=""><i class="metismenu-icon pe-7s-user" style=""></i>
        User
    </a></li>
<li class=" "><a href="<?= base_url() ?>/group" class=""><i class="metismenu-icon pe-7s-users" style=""></i>
        Group
    </a></li>
<li class=" ">
    <a href="#" class=""><i class="metismenu-icon pe-7s-menu" style=""></i>
        Menu <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>
    <ul class="mm-collapse">
        <li class=" "><a href="<?= base_url() ?>/menu?category=backend" class=""><i class="metismenu-icon " style=""></i>
                Backend
            </a></li>
        <li class=" "><a href="<?= base_url() ?>/menu?category=frontend" class=""><i class="metismenu-icon " style=""></i>
                Frontend
            </a></li>
    </ul>
</li>
<li class=" ">
    <a href="#" class=""><i class="metismenu-icon pe-7s-config" style=""></i>
        Setting <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>
    <ul class="mm-collapse">
        <li class=" "><a href="<?= base_url() ?>/param" class=""><i class="metismenu-icon " style=""></i>
                Parameter
            </a></li>
    </ul>
</li>