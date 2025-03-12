<?php
$menuData = [];
$uriService = service('uri');
$segment1 = $uriService->getSegment(1);
$segment2 = '';

if ($uriService->getTotalSegments() >= 2) {
	$segment2 = $uriService->getSegment(2);
}

$menuData[] = get_menu_item("Navigasi", "", false, "", "heading");
$menuData[] = get_menu_item("Dashboard", "dashboard", in_array($segment1, ['', 'dashboard']), "pe-7s-home");
$laporan = [
	"title" => "Laporan",
	"icon" => "pe-7s-graph2",
	"url" => "#",
	"active" => ($segment1 === 'report'),
	"subMenu" => [
		get_menu_item("Laporan Anggota", "report/laporan_anggota", in_array($segment2, ['laporan_anggota']), "pe-7s-angle-right-circle"),
		get_menu_item("Laporan Kunjungan", "report/laporan_kunjungan", in_array($segment2, ['laporan_kunjungan'])),
	],
];
$menuData[] = $laporan;

// $menuData[] = get_menu_item("Koleksi", "", false, "", "heading");
$koleksi = [
	"title" => "Katalog",
	"icon" => "pe-7s-notebook",
	"url" => "#",
	"active" => in_array($segment1, ['katalog']),
	"subMenu" => [
		get_menu_item("Daftar Katalog", "katalog/index", in_array($segment1, ['katalog'])),
		get_menu_item("Karantina Katalog", "katalog/karantina", in_array($segment1, ['katalog']) && in_array($segment2, ['karantina'])),
	],
];

$menuData[] = $koleksi;

$eksemplar = [
	"title" => "Eksemplar",
	"icon" => "pe-7s-albums",
	"url" => "#",
	"active" => in_array($segment1, ['eksemplar']),
	"subMenu" => [
		get_menu_item("Daftar Eksemplar", "eksemplar/index", in_array($segment1, ['eksemplar'])),
		get_menu_item("Karantina Eksemplar", "eksemplar/karantina", in_array($segment1, ['eksemplar']) && in_array($segment2, ['karantina'])),
	],
];

$menuData[] = $eksemplar;

$menuData[] = get_menu_item("Artikel", "artikel", in_array($segment1, ['', 'artikel']), "pe-7s-news-paper");

$menuData[] = [
	"title" => "Sirkulasi",
	"icon" => "pe-7s-refresh-2",
	"url" => "#",
	"active" => ($segment1 === 'sirkulasi'),
	"subMenu" => [
		get_menu_item("Peminjaman", "sirkulasi/peminjaman", in_array($segment2, ['peminjaman'])),
		get_menu_item("Pengembalian", "sirkulasi/pengembalian", in_array($segment2, ['pengembalian'])),
		get_menu_item("Perpanjangan", "sirkulasi/perpanjangan", in_array($segment2, ['perpanjangan'])),
		get_menu_item("Pelanggaran", "sirkulasi/pelanggaran", in_array($segment2, ['pelanggaran'])),
	],
];

$menuData[] = [
	"title" => "Keanggotaan",
	"icon" => "pe-7s-users",
	"url" => "#",
	"active" => in_array($segment1, ['anggota']),
	"subMenu" => [
		get_menu_item("Daftar Anggota", "anggota/index", in_array($segment1, ['anggota'])),
		get_menu_item("Keranjang Anggota", "anggota/keranjang", in_array($segment2, ['keranjang'])),
		get_menu_item("Sumbangan", "sumbangan", in_array($segment1, ['sumbangan']), "pe-7s-wallet"),
	],
];

$menuData[] = get_menu_item("Buku Tamu", "bukutamu", in_array($segment1, ['bukutamu']), "pe-7s-note");
$menuData[] = get_menu_item("Baca Ditempat", "bacaditempat", in_array($segment1, ['bacaditempat']), "pe-7s-study");
$menuData[] = get_menu_item("Survei Pemustaka", "surveipemustaka", in_array($segment1, ['surveipemustaka']), "pe-7s-graph2");

$menuData[] = get_menu_item("Pengaturan", "", false, "", "heading");
$menuData[] = [
	"title" => "Administrasi",
	"icon" => "pe-7s-server",
	"url" => "#",
	"active" => in_array($segment2, ['kategori-koleksi', 'sumber-koleksi']),
	"subMenu" => [
		[
			"title" => "Pengaturan Akuisisi",
			"active" => in_array($segment2, ['kategori-koleksi', 'sumber-koleksi']),
			"subMenu" => [
				get_menu_item("Kategori Koleksi", "master/kategori-koleksi", in_array($segment2, ['kategori-koleksi'])),
				get_menu_item("Sumber Koleksi", "master-sumber-koleksi", in_array($segment2, ['sumber-koleksi'])),
			],
		],
		[
			"title" => "Pengaturan Katalog",
			"active" => in_array($segment1, ['tag', 'referensi', 'katasandang', 'jenisbahanpustaka', 'formatkartu', 'pengaturandetailkatalog', 'penyediakatalog', 'formentri']),
			"subMenu" => [
				get_menu_item("Tag", "tag", in_array($segment1, ['tag'])),
				get_menu_item("Referensi", "referensi", in_array($segment1, ['referensi'])),
				get_menu_item("Kata Sandang", "katasandang", in_array($segment1, ['katasandang'])),
				get_menu_item("Jenis Bahan Pustaka", "jenisbahanpustaka", in_array($segment1, ['jenisbahanpustaka'])),
				get_menu_item("Format Kartu", "formatkartu", in_array($segment1, ['formatkartu'])),
				get_menu_item("Pengaturan Detail Katalog", "pengaturandetailkatalog", in_array($segment1, ['pengaturandetailkatalog'])),
				get_menu_item("Penyedia Katalog", "penyediakatalog", in_array($segment1, ['penyediakatalog'])),
				get_menu_item("Form Entri", "formentri", in_array($segment1, ['formentri'])),
			],
		],
		[
			"title" => "Pengaturan SSKCKR",
			"active" => in_array($segment1, ['pengaturan-sskckr']),
			"subMenu" => [
				get_menu_item("Item Pengaturan", "pengaturan-sskckr", in_array($segment1, ['pengaturan-sskckr'])),
			]
		],
		[
			"title" => "Pengaturan Keanggotaan",
			"active" => in_array($segment2, ['kartuanggota', 'jenis-anggota', 'jenis-identitas', 'jenis-pekerjaan', 'jenis-pendidikan']),
			"subMenu" => [
				get_menu_item("Kartu Anggota", "master/kartuanggota", in_array($segment2, ['kartuanggota'])),
				get_menu_item("Redaksi Keanggotaan", "redaksikeanggotaan", in_array($segment1, ['redaksikeanggotaan'])),
				get_menu_item("Jenis Anggota", "master/jenis-anggota", in_array($segment1, ['jenis-anggota'])),
				get_menu_item("Jenis Identitas", "master/jenis-identitas", in_array($segment2, ['jenis-identitas'])),
				get_menu_item("Pekerjaan", "master/jenis-pekerjaan", in_array($segment2, ['jenis-pekerjaan'])),
				get_menu_item("Pendidikan", "master/jenis-pendidikan", in_array($segment2, ['jenis-pendidikan'])),
			],
		],
		[
			"title" => "Pengaturan Sirkulasi",
			"active" => in_array($segment2, ['jenis-akses', 'jenis-bahan', 'jenis-denda', 'jenis-pelanggaran', 'peraturan-peminjaman-hari', 'peraturan-peminjaman-tanggal']),
			"subMenu" => [
				get_menu_item("Jenis Akses", "master/jenis-akses", in_array($segment2, ['jenis-akses'])),
				get_menu_item("Jenis Bahan", "master/jenis-bahan", in_array($segment2, ['jenis-bahan'])),
				get_menu_item("Jenis Denda", "master/jenis-denda", in_array($segment2, ['jenis-denda'])),
				get_menu_item("Jenis Pelanggaran", "master/jenis-pelanggaran", in_array($segment2, ['jenis-pelanggaran'])),
				get_menu_item("Peminjaman Hari", "master/peraturan-peminjaman-hari", in_array($segment2, ['peraturan-peminjaman-hari'])),
				get_menu_item("Peminjaman Tanggal", "master/peraturan-peminjaman-tanggal", in_array($segment2, ['peraturan-peminjaman-tanggal'])),
			],
		],
		[
			"title" => "Pengaturan Locker",
			"active" => in_array($segment2, ['pengaturan-locker']),
			"subMenu" => [
				get_menu_item("Item", "pengaturan-locker", in_array($segment2, ['pengaturan-locker'])),
			]
		],
		[
			"title" => "Pengaturan Opac",
			"active" => in_array($segment2, ['pengaturan-opac']),
			"subMenu" => [
				get_menu_item("Item", "pengaturan-opac", in_array($segment2, ['pengaturan-opac'])),
			]
		],
		[
			"title" => "Pengaturan LKD",
			"active" => in_array($segment1, ['pengaturan-lkd']),
			"subMenu" => [
				get_menu_item("Item", "pengaturan-lkd", in_array($segment1, ['pengaturan-lkd'])),
			]
		],
		[
			"title" => "Pengaturan Umum",
			"active" => in_array($segment1, ['namaperpustakaan']),
			"subMenu" => [
				get_menu_item("Nama Perpustakaan", "namaperpustakaan", in_array($segment1, ['namaperpustakaan'])),
			]
		],
		[
			"title" => "Pengaturan Audio",
			"active" => in_array($segment1, ['pengaturan-audio']),
			"subMenu" => [
				get_menu_item("Item", "pengaturan-audio", in_array($segment1, ['pengaturan-audio'])),
			]
		],
		[
			"title" => "Pengaturan Buku Tamu",
			"active" => in_array($segment1, ['pengaturan-buku-tamu']),
			"subMenu" => [
				get_menu_item("Item", "pengaturan-buku-tamu", in_array($segment1, ['pengaturan-buku-tamu'])),
			]
		],
	],
];
$menuData[] = [
	"title" => "Otorisasi",
	"icon" => "pe-7s-users",
	"url" => "#",
	"active" => in_array($segment1, ['user', 'group', 'permission']),
	"subMenu" => [
		get_menu_item("User", "user", in_array($segment1, ['user'])),
		get_menu_item("Group", "group", in_array($segment1, ['group'])),
		get_menu_item("Permission", "permission", in_array($segment1, ['permission'])),
	],
];
$menuData[] = [
	"title" => "Sistem",
	"icon" => "pe-7s-config",
	"url" => "#",
	"active" => in_array($segment1, ['parameter', 'menu', 'reference']) || in_array($segment2, ['mitra-perpustakaan']),
	"subMenu" => [
		get_menu_item("Paremeter", "parameter", in_array($segment1, ['parameter'])),
		get_menu_item("Menu", "menu", in_array($segment1, ['menu'])),
		get_menu_item("Referensi", "reference", in_array($segment1, ['reference'])),
		get_menu_item("Mitra Perpustakaan", "master-mitra-perpustakaan", in_array($segment2, ['mitra-perpustakaan'])),
	],
];
