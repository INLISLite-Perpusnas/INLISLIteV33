-- =====================================================================
-- Sinkronisasi RBAC dan settingparameters ke database lain
-- Sumber : inlislite_v33_progo (2026-11-04)
--
-- auth_groups (5 baris), auth_permissions (637 baris),
-- auth_groups_permissions (840 baris)
-- c_menus category_id = 1 / menu backend (132 baris)
--   -> DITIMPA: isi lama dihapus, diganti isi dari sumber (ID dipertahankan).
--      Menu frontend dan referensi tidak disentuh.
-- settingparameters (158 Name unik)
--   -> Hanya menambah Name yang belum ada. Value yang sudah ada tidak diubah.
-- auth_groups_users, auth_users_permissions
--   -> TIDAK disentuh, karena ID user tiap database berbeda.
--
-- WAJIB backup database tujuan sebelum menjalankan.
-- Jalankan lewat mysql CLI, supaya berhenti dan rollback saat ada error:
--   mysql -uroot -p --default-character-set=utf8mb4 NAMA_DB_TUJUAN < sync_rbac_settingparameters.sql
-- Baca hasil verifikasi di akhir: Cek 1 harus 0 baris.
-- =====================================================================

SET NAMES utf8mb4;
SET @old_fk_checks = @@SESSION.FOREIGN_KEY_CHECKS;
SET SESSION FOREIGN_KEY_CHECKS = 0;

-- Simpan nama lama untuk mendeteksi user yang artinya berubah setelah penimpaan
DROP TEMPORARY TABLE IF EXISTS tmp_rbac_old_groups;
DROP TEMPORARY TABLE IF EXISTS tmp_rbac_old_permissions;
CREATE TEMPORARY TABLE tmp_rbac_old_groups AS SELECT id, name FROM `auth_groups`;
CREATE TEMPORARY TABLE tmp_rbac_old_permissions AS SELECT id, name FROM `auth_permissions`;

START TRANSACTION;

-- ---------------------------------------------------------------------
-- 1. Timpa RBAC
-- ---------------------------------------------------------------------
DELETE FROM `auth_groups_permissions`;
DELETE FROM `auth_permissions`;
DELETE FROM `auth_groups`;
DELETE FROM `c_menus` WHERE `category_id` = 1;

-- auth_groups
INSERT INTO `auth_groups` (`id`, `name`, `description`, `sort`, `category`, `level`, `branch_id`) VALUES
('1', 'admin', 'Admin Pusat', NULL, '0', '0', '0'),
('8', 'keanggotaan', 'Layanan Keanggotaan', NULL, '0', '7', '0'),
('10', 'kataloger', 'Pengelola Perpustakaan', NULL, '0', '0', '0'),
('11', 'sirkulasi', 'Sirkulasi', NULL, '0', '0', '0'),
('14', 'anggota_online', '', NULL, '0', '0', '0');

-- auth_permissions
INSERT INTO `auth_permissions` (`id`, `name`, `route`, `menu`, `description`, `category`) VALUES
('362', 'dashboard/access', 'dashboard', 'Navigasi', 'Update Permission for menu Dashboard', NULL),
('363', 'dashboard/index', 'dashboard', 'Dashboard', 'Update Permission for menu Dashboard', NULL),
('364', 'laporan/access', 'laporan', 'Laporan', 'Create Permission for menu Laporan', NULL),
('365', 'laporan-anggota/access', 'laporan-anggota', 'Laporan Anggota', 'Update Permission for menu Laporan Anggota', NULL),
('366', 'laporan-anggota/index', 'laporan-anggota', 'Laporan Anggota', 'Update Permission for menu Laporan Anggota', NULL),
('372', 'karantina-katalog/access', 'karantina-katalog', 'Karantina Katalog', 'Update Permission for menu Karantina Katalog', NULL),
('373', 'karantina-katalog/index', 'karantina-katalog', 'Karantina Katalog', 'Update Permission for menu Karantina Katalog', NULL),
('384', 'sirkulasi/access', 'sirkulasi', 'Sirkulasi', 'Create Permission for menu Sirkulasi', NULL),
('385', 'sirkulasi-peminjaman/access', 'sirkulasi-peminjaman', 'Peminjaman', 'Update Permission for menu Peminjaman', NULL),
('386', 'sirkulasi-peminjaman/index', 'sirkulasi-peminjaman', 'Peminjaman', 'Update Permission for menu Peminjaman', NULL),
('387', 'sirkulasi-peminjaman/create', 'sirkulasi-peminjaman/create', 'Entri Peminjaman', 'Update Permission for menu Peminjaman', NULL),
('390', 'sirkulasi-perpanjangan/access', 'sirkulasi-perpanjangan', 'Perpanjangan', 'Update Permission for menu Perpanjangan', NULL),
('391', 'sirkulasi-perpanjangan/index', 'sirkulasi-perpanjangan', 'Perpanjangan', 'Update Permission for menu Perpanjangan', NULL),
('392', 'sirkulasi-perpanjangan/create', 'sirkulasi-perpanjangan', 'Perpanjangan', 'Update Permission for menu Perpanjangan', NULL),
('393', 'sirkulasi-perpanjangan/edit', 'sirkulasi-perpanjangan', 'Perpanjangan', 'Update Permission for menu Perpanjangan', NULL),
('394', 'sirkulasi-perpanjangan/delete', 'sirkulasi-perpanjangan', 'Perpanjangan', 'Update Permission for menu Perpanjangan', NULL),
('395', 'sirkulasi-pengembalian/access', 'sirkulasi-pengembalian', 'Pengembalian', 'Update Permission for menu Pengembalian', NULL),
('396', 'sirkulasi-pengembalian/index', 'sirkulasi-pengembalian', 'Pengembalian', 'Update Permission for menu Pengembalian', NULL),
('397', 'sirkulasi-pengembalian/create', 'sirkulasi-pengembalian/create', 'Entri  Pengembalian', 'Update Permission for menu Pengembalian', NULL),
('400', 'sirkulasi-pelanggaran/access', 'sirkulasi-pelanggaran', 'Pelanggaran', 'Update Permission for menu Pelanggaran', NULL),
('401', 'sirkulasi-pelanggaran/index', 'sirkulasi-pelanggaran', 'Pelanggaran', 'Update Permission for menu Pelanggaran', NULL),
('402', 'sirkulasi-pelanggaran/create', 'sirkulasi-pelanggaran', 'Pelanggaran', 'Update Permission for menu Pelanggaran', NULL),
('403', 'sirkulasi-pelanggaran/edit', 'sirkulasi-pelanggaran', 'Pelanggaran', 'Update Permission for menu Pelanggaran', NULL),
('404', 'sirkulasi-pelanggaran/delete', 'sirkulasi-pelanggaran', 'Pelanggaran', 'Update Permission for menu Pelanggaran', NULL),
('405', 'keanggotaan/access', 'keanggotaan', 'Keanggotaan', 'Create Permission for menu Keanggotaan', NULL),
('406', 'anggota/access', 'anggota', 'Daftar Anggota', 'Update Permission for menu Daftar Anggota', NULL),
('407', 'anggota/index', 'anggota', 'Daftar Anggota', 'Update Permission for menu Daftar Anggota', NULL),
('408', 'anggota/create', 'anggota', 'Daftar Anggota', 'Update Permission for menu Daftar Anggota', NULL),
('1183', 'anggota/create/access', 'anggota/create', 'Entri Anggota', 'Permission access menu Entri Anggota', NULL),
('1184', 'anggota/export_view/access', 'anggota/export_view', 'Export Anggota', 'Permission access menu Export Anggota', NULL),
('1185', 'anggota/export_view/index', 'anggota/export_view', 'Export Anggota', 'Permission index menu Export Anggota', NULL),
('1186', 'anggota/export', 'anggota/export', 'Export Anggota', 'Permission export menu Export Anggota', NULL),
('409', 'anggota/edit', 'anggota', 'Daftar Anggota', 'Update Permission for menu Daftar Anggota', NULL),
('410', 'anggota/delete', 'anggota', 'Daftar Anggota', 'Update Permission for menu Daftar Anggota', NULL),
('411', 'keranjang-anggota/access', 'keranjang-anggota', 'Keranjang Anggota', 'Update Permission for menu Keranjang Anggota', NULL),
('412', 'keranjang-anggota/index', 'keranjang-anggota', 'Keranjang Anggota', 'Update Permission for menu Keranjang Anggota', NULL),
('413', 'keranjang-anggota/create', 'keranjang-anggota', 'Keranjang Anggota', 'Update Permission for menu Keranjang Anggota', NULL),
('414', 'keranjang-anggota/edit', 'keranjang-anggota', 'Keranjang Anggota', 'Update Permission for menu Keranjang Anggota', NULL),
('415', 'keranjang-anggota/delete', 'keranjang-anggota', 'Keranjang Anggota', 'Update Permission for menu Keranjang Anggota', NULL),
('416', 'sumbangan/access', 'sumbangan', 'Sumbangan', 'Update Permission for menu Sumbangan', NULL),
('417', 'sumbangan/index', 'sumbangan', 'Sumbangan', 'Update Permission for menu Sumbangan', NULL),
('418', 'sumbangan/create', 'sumbangan', 'Sumbangan', 'Update Permission for menu Sumbangan', NULL),
('419', 'sumbangan/edit', 'sumbangan', 'Sumbangan', 'Update Permission for menu Sumbangan', NULL),
('420', 'sumbangan/delete', 'sumbangan', 'Sumbangan', 'Update Permission for menu Sumbangan', NULL),
('422', 'pengaturan-keanggotaan/access', 'pengaturan-keanggotaan', 'Pengaturan Keanggotaan', 'Update Permission for menu Pengaturan Keanggotaan', NULL),
('423', 'master-jenis-anggota/access', 'master-jenis-anggota', 'Jenis Anggota', 'Update Permission for menu Jenis Anggota', NULL),
('424', 'master-jenis-anggota/index', 'master-jenis-anggota', 'Jenis Anggota', 'Update Permission for menu Jenis Anggota', NULL),
('425', 'master-jenis-anggota/create', 'master-jenis-anggota', 'Jenis Anggota', 'Update Permission for menu Jenis Anggota', NULL),
('426', 'master-jenis-anggota/edit', 'master-jenis-anggota', 'Jenis Anggota', 'Update Permission for menu Jenis Anggota', NULL),
('427', 'master-jenis-anggota/delete', 'master-jenis-anggota', 'Jenis Anggota', 'Update Permission for menu Jenis Anggota', NULL),
('428', 'master-jenis-identitas/access', 'master-jenis-identitas', 'Jenis Identitas', 'Update Permission for menu Jenis Identitas', NULL),
('429', 'master-jenis-identitas/index', 'master-jenis-identitas', 'Jenis Identitas', 'Update Permission for menu Jenis Identitas', NULL),
('430', 'master-jenis-identitas/create', 'master-jenis-identitas', 'Jenis Identitas', 'Update Permission for menu Jenis Identitas', NULL),
('431', 'master-jenis-identitas/edit', 'master-jenis-identitas', 'Jenis Identitas', 'Update Permission for menu Jenis Identitas', NULL),
('432', 'master-jenis-identitas/delete', 'master-jenis-identitas', 'Jenis Identitas', 'Update Permission for menu Jenis Identitas', NULL),
('433', 'master-jenis-kelamin/access', 'master-jenis-kelamin', 'Jenis Kelamin', 'Update Permission for menu Jenis Kelamin', NULL),
('434', 'master-jenis-kelamin/index', 'master-jenis-kelamin', 'Jenis Kelamin', 'Update Permission for menu Jenis Kelamin', NULL),
('435', 'master-jenis-kelamin/create', 'master-jenis-kelamin', 'Jenis Kelamin', 'Update Permission for menu Jenis Kelamin', NULL),
('436', 'master-jenis-kelamin/edit', 'master-jenis-kelamin', 'Jenis Kelamin', 'Update Permission for menu Jenis Kelamin', NULL),
('437', 'master-jenis-kelamin/delete', 'master-jenis-kelamin', 'Jenis Kelamin', 'Update Permission for menu Jenis Kelamin', NULL),
('438', 'master-status-perkawinan/access', 'master-status-perkawinan', 'Status Perkawinan', 'Update Permission for menu Status Perkawinan', NULL),
('439', 'master-status-perkawinan/index', 'master-status-perkawinan', 'Status Perkawinan', 'Update Permission for menu Status Perkawinan', NULL),
('440', 'master-status-perkawinan/create', 'master-status-perkawinan', 'Status Perkawinan', 'Update Permission for menu Status Perkawinan', NULL),
('441', 'master-status-perkawinan/edit', 'master-status-perkawinan', 'Status Perkawinan', 'Update Permission for menu Status Perkawinan', NULL),
('442', 'master-status-perkawinan/delete', 'master-status-perkawinan', 'Status Perkawinan', 'Update Permission for menu Status Perkawinan', NULL),
('448', 'pengaturan-katalog/access', 'pengaturan-katalog', 'Pengaturan Katalog', 'Update Permission for menu Pengaturan Katalog', NULL),
('479', 'master-tag/access', 'master-tag', 'Tag', 'Update Permission for menu Tag', NULL),
('480', 'master-tag/index', 'master-tag', 'Tag', 'Update Permission for menu Tag', NULL),
('481', 'master-tag/create', 'master-tag', 'Tag', 'Update Permission for menu Tag', NULL),
('482', 'master-tag/edit', 'master-tag', 'Tag', 'Update Permission for menu Tag', NULL),
('483', 'master-tag/delete', 'master-tag', 'Tag', 'Update Permission for menu Tag', NULL),
('495', 'user/access', 'user', 'User', 'Update Permission for menu User', NULL),
('496', 'user/index', 'user', 'User', 'Update Permission for menu User', NULL),
('497', 'user/create', 'user', 'User', 'Update Permission for menu User', NULL),
('498', 'user/edit', 'user', 'User', 'Update Permission for menu User', NULL),
('499', 'user/delete', 'user', 'User', 'Update Permission for menu User', NULL),
('500', 'group/access', 'group', 'Group', 'Update Permission for menu Group', NULL),
('501', 'group/index', 'group', 'Group', 'Update Permission for menu Group', NULL),
('502', 'group/create', 'group', 'Group', 'Update Permission for menu Group', NULL),
('503', 'group/edit', 'group', 'Group', 'Update Permission for menu Group', NULL),
('504', 'group/delete', 'group', 'Group', 'Update Permission for menu Group', NULL),
('505', 'permission/access', 'permission', 'Permission', 'Update Permission for menu Permission', NULL),
('506', 'permission/index', 'permission', 'Permission', 'Update Permission for menu Permission', NULL),
('507', 'permission/create', 'permission', 'Permission', 'Update Permission for menu Permission', NULL),
('508', 'permission/edit', 'permission', 'Permission', 'Update Permission for menu Permission', NULL),
('509', 'permission/delete', 'permission', 'Permission', 'Update Permission for menu Permission', NULL),
('511', 'menu/access', 'menu', 'Menu', 'Update Permission for menu Menu', NULL),
('512', 'menu/index', 'menu', 'Menu', 'Update Permission for menu Menu', NULL),
('513', 'menu/create', 'menu', 'Menu', 'Update Permission for menu Menu', NULL),
('514', 'menu/edit', 'menu', 'Menu', 'Update Permission for menu Menu', NULL),
('515', 'menu/delete', 'menu', 'Menu', 'Update Permission for menu Menu', NULL),
('516', 'parameter/access', 'parameter', 'Parameter', 'Update Permission for menu Parameter', NULL),
('517', 'parameter/index', 'parameter', 'Parameter', 'Update Permission for menu Parameter', NULL),
('518', 'parameter/create', 'parameter', 'Parameter', 'Update Permission for menu Parameter', NULL),
('519', 'parameter/edit', 'parameter', 'Parameter', 'Update Permission for menu Parameter', NULL),
('520', 'parameter/delete', 'parameter', 'Parameter', 'Update Permission for menu Parameter', NULL),
('526', 'master-referensi/access', 'master-referensi', 'Referensi', 'Update Permission for menu Referensi', NULL),
('527', 'master-referensi/index', 'master-referensi', 'Referensi', 'Update Permission for menu Referensi', NULL),
('528', 'master-referensi/create', 'master-referensi', 'Referensi', 'Update Permission for menu Referensi', NULL),
('529', 'master-referensi/edit', 'master-referensi', 'Referensi', 'Update Permission for menu Referensi', NULL),
('530', 'master-referensi/delete', 'master-referensi', 'Referensi', 'Update Permission for menu Referensi', NULL),
('531', 'master-kata-sandang/access', 'master-kata-sandang', 'Kata Sandang', 'Update Permission for menu Kata Sandang', NULL),
('532', 'master-kata-sandang/index', 'master-kata-sandang', 'Kata Sandang', 'Update Permission for menu Kata Sandang', NULL),
('533', 'master-kata-sandang/create', 'master-kata-sandang', 'Kata Sandang', 'Update Permission for menu Kata Sandang', NULL),
('534', 'master-kata-sandang/edit', 'master-kata-sandang', 'Kata Sandang', 'Update Permission for menu Kata Sandang', NULL),
('535', 'master-kata-sandang/delete', 'master-kata-sandang', 'Kata Sandang', 'Update Permission for menu Kata Sandang', NULL),
('536', 'master-jenis-bahan-pustaka/access', 'master-jenis-bahan-pustaka', 'Jenis Bahan Pustaka', 'Update Permission for menu Jenis Bahan Pustaka', NULL),
('537', 'master-jenis-bahan-pustaka/index', 'master-jenis-bahan-pustaka', 'Jenis Bahan Pustaka', 'Update Permission for menu Jenis Bahan Pustaka', NULL),
('538', 'master-jenis-bahan-pustaka/create', 'master-jenis-bahan-pustaka', 'Jenis Bahan Pustaka', 'Update Permission for menu Jenis Bahan Pustaka', NULL),
('539', 'master-jenis-bahan-pustaka/edit', 'master-jenis-bahan-pustaka', 'Jenis Bahan Pustaka', 'Update Permission for menu Jenis Bahan Pustaka', NULL),
('540', 'master-jenis-bahan-pustaka/delete', 'master-jenis-bahan-pustaka', 'Jenis Bahan Pustaka', 'Update Permission for menu Jenis Bahan Pustaka', NULL),
('542', 'karantina-katalog/create', 'karantina-katalog', 'Karantina Katalog', 'Update Permission for menu Karantina Katalog', NULL),
('543', 'karantina-katalog/edit', 'karantina-katalog', 'Karantina Katalog', 'Update Permission for menu Karantina Katalog', NULL),
('544', 'karantina-katalog/delete', 'karantina-katalog', 'Karantina Katalog', 'Update Permission for menu Karantina Katalog', NULL),
('545', 'menu-sirkulasi/access', 'menu-sirkulasi', 'Sirkulasi', 'Update Permission for menu Sirkulasi', NULL),
('546', 'menu-keanggotaan/access', 'menu-keanggotaan', 'Keanggotaan', 'Update Permission for menu Keanggotaan', NULL),
('548', 'pengaturan-akuisisi/access', 'pengaturan-akuisisi', 'Pengaturan Eksemplar', 'Update Permission for menu Pengaturan Eksemplar', NULL),
('550', 'pengaturan-sirkulasi/access', 'pengaturan-sirkulasi', 'Pengaturan Sirkulasi', 'Update Permission for menu Pengaturan Sirkulasi', NULL),
('551', 'pengaturan-umum/access', 'pengaturan-umum', 'Pengaturan Umum', 'Update Permission for menu Pengaturan Umum', NULL),
('552', 'menu-laporan/access', 'menu-laporan', 'Laporan', 'Update Permission for menu Laporan', NULL),
('553', 'menu-otorisasi/access', 'menu-otorisasi', 'Otorisasi', 'Update Permission for menu Otorisasi', NULL),
('554', 'menu-setelan/access', 'menu-setelan', 'Setelan', 'Update Permission for menu Setelan', NULL),
('556', 'label_menu/access', 'label_menu', 'Navigasi', 'Create Permission for menu Navigasi', NULL),
('557', 'label-navigasi/access', 'label-navigasi', 'Navigasi', 'Update Permission for menu Navigasi', NULL),
('574', 'master-penyedia-katalog/access', 'master-penyedia-katalog', 'Penyedia Katalog', 'Update Permission for menu Penyedia Katalog', NULL),
('575', 'master-penyedia-katalog/index', 'master-penyedia-katalog', 'Penyedia Katalog', 'Update Permission for menu Penyedia Katalog', NULL),
('576', 'master-penyedia-katalog/create', 'master-penyedia-katalog', 'Penyedia Katalog', 'Update Permission for menu Penyedia Katalog', NULL),
('577', 'master-penyedia-katalog/edit', 'master-penyedia-katalog', 'Penyedia Katalog', 'Update Permission for menu Penyedia Katalog', NULL),
('578', 'master-penyedia-katalog/delete', 'master-penyedia-katalog', 'Penyedia Katalog', 'Update Permission for menu Penyedia Katalog', NULL),
('579', 'master-form-entri/access', 'master-form-entri', 'Form Entri', 'Update Permission for menu Form Entri', NULL),
('580', 'master-form-entri/index', 'master-form-entri', 'Form Entri', 'Update Permission for menu Form Entri', NULL),
('581', 'master-form-entri/create', 'master-form-entri', 'Form Entri', 'Update Permission for menu Form Entri', NULL),
('582', 'master-form-entri/edit', 'master-form-entri', 'Form Entri', 'Update Permission for menu Form Entri', NULL),
('583', 'master-form-entri/delete', 'master-form-entri', 'Form Entri', 'Update Permission for menu Form Entri', NULL),
('584', 'master-kelas-besar/access', 'master-kelas-besar', 'Kelas Besar', 'Update Permission for menu Kelas Besar', NULL),
('585', 'master-kelas-besar/index', 'master-kelas-besar', 'Kelas Besar', 'Update Permission for menu Kelas Besar', NULL),
('586', 'master-kelas-besar/create', 'master-kelas-besar', 'Kelas Besar', 'Update Permission for menu Kelas Besar', NULL),
('587', 'master-kelas-besar/edit', 'master-kelas-besar', 'Kelas Besar', 'Update Permission for menu Kelas Besar', NULL),
('588', 'master-kelas-besar/delete', 'master-kelas-besar', 'Kelas Besar', 'Update Permission for menu Kelas Besar', NULL),
('589', 'master-nama-perpustakaan/access', 'master-nama-perpustakaan', 'Nama Perpustakaan', 'Update Permission for menu Nama Perpustakaan', NULL),
('590', 'master-nama-perpustakaan/index', 'master-nama-perpustakaan', 'Nama Perpustakaan', 'Update Permission for menu Nama Perpustakaan', NULL),
('591', 'master-nama-perpustakaan/create', 'master-nama-perpustakaan', 'Nama Perpustakaan', 'Update Permission for menu Nama Perpustakaan', NULL),
('592', 'master-nama-perpustakaan/edit', 'master-nama-perpustakaan', 'Nama Perpustakaan', 'Update Permission for menu Nama Perpustakaan', NULL),
('593', 'master-nama-perpustakaan/delete', 'master-nama-perpustakaan', 'Nama Perpustakaan', 'Update Permission for menu Nama Perpustakaan', NULL),
('594', 'master-jenis-perpustakaan/access', 'master-jenis-perpustakaan', 'Jenis Perpustakaan', 'Update Permission for menu Jenis Perpustakaan', NULL),
('595', 'master-jenis-perpustakaan/index', 'master-jenis-perpustakaan', 'Jenis Perpustakaan', 'Update Permission for menu Jenis Perpustakaan', NULL),
('596', 'master-jenis-perpustakaan/create', 'master-jenis-perpustakaan', 'Jenis Perpustakaan', 'Update Permission for menu Jenis Perpustakaan', NULL),
('597', 'master-jenis-perpustakaan/edit', 'master-jenis-perpustakaan', 'Jenis Perpustakaan', 'Update Permission for menu Jenis Perpustakaan', NULL),
('598', 'master-jenis-perpustakaan/delete', 'master-jenis-perpustakaan', 'Jenis Perpustakaan', 'Update Permission for menu Jenis Perpustakaan', NULL),
('604', 'master-lokasi-perpustakaan/access', 'master-lokasi-perpustakaan', 'Lokasi Perpustakaan', 'Update Permission for menu Lokasi Perpustakaan', NULL),
('605', 'master-lokasi-perpustakaan/index', 'master-lokasi-perpustakaan', 'Lokasi Perpustakaan', 'Update Permission for menu Lokasi Perpustakaan', NULL),
('606', 'master-lokasi-perpustakaan/create', 'master-lokasi-perpustakaan', 'Lokasi Perpustakaan', 'Update Permission for menu Lokasi Perpustakaan', NULL),
('607', 'master-lokasi-perpustakaan/edit', 'master-lokasi-perpustakaan', 'Lokasi Perpustakaan', 'Update Permission for menu Lokasi Perpustakaan', NULL),
('608', 'master-lokasi-perpustakaan/delete', 'master-lokasi-perpustakaan', 'Lokasi Perpustakaan', 'Update Permission for menu Lokasi Perpustakaan', NULL),
('609', 'master-lokasi-ruang/access', 'master-lokasi-ruang', 'Lokasi Ruang', 'Update Permission for menu Lokasi Ruang', NULL),
('610', 'master-lokasi-ruang/index', 'master-lokasi-ruang', 'Lokasi Ruang', 'Update Permission for menu Lokasi Ruang', NULL),
('611', 'master-lokasi-ruang/create', 'master-lokasi-ruang', 'Lokasi Ruang', 'Update Permission for menu Lokasi Ruang', NULL),
('612', 'master-lokasi-ruang/edit', 'master-lokasi-ruang', 'Lokasi Ruang', 'Update Permission for menu Lokasi Ruang', NULL),
('613', 'master-lokasi-ruang/delete', 'master-lokasi-ruang', 'Lokasi Ruang', 'Update Permission for menu Lokasi Ruang', NULL),
('619', 'master-hari-libur/access', 'master-hari-libur', 'Hari Libur', 'Update Permission for menu Hari Libur', NULL),
('620', 'master-hari-libur/index', 'master-hari-libur', 'Hari Libur', 'Update Permission for menu Hari Libur', NULL),
('621', 'master-hari-libur/create', 'master-hari-libur', 'Hari Libur', 'Update Permission for menu Hari Libur', NULL),
('622', 'master-hari-libur/edit', 'master-hari-libur', 'Hari Libur', 'Update Permission for menu Hari Libur', NULL),
('623', 'master-hari-libur/delete', 'master-hari-libur', 'Hari Libur', 'Update Permission for menu Hari Libur', NULL),
('624', 'apply_status/apply_status', 'apply_status', '203', '', NULL),
('625', 'master-kategori-koleksi/access', 'master-kategori-koleksi', 'Kategori Koleksi', 'Update Permission for menu Kategori Koleksi', NULL),
('626', 'master-kategori-koleksi/index', 'master-kategori-koleksi', 'Kategori Koleksi', 'Update Permission for menu Kategori Koleksi', NULL),
('627', 'master-kategori-koleksi/create', 'master-kategori-koleksi', 'Kategori Koleksi', 'Update Permission for menu Kategori Koleksi', NULL),
('628', 'master-kategori-koleksi/edit', 'master-kategori-koleksi', 'Kategori Koleksi', 'Update Permission for menu Kategori Koleksi', NULL),
('629', 'master-kategori-koleksi/delete', 'master-kategori-koleksi', 'Kategori Koleksi', 'Update Permission for menu Kategori Koleksi', NULL),
('630', 'master-media-koleksi/access', 'master-media-koleksi', 'Media Koleksi', 'Update Permission for menu Media Koleksi', NULL),
('631', 'master-media-koleksi/index', 'master-media-koleksi', 'Media Koleksi', 'Update Permission for menu Media Koleksi', NULL),
('632', 'master-media-koleksi/create', 'master-media-koleksi', 'Media Koleksi', 'Update Permission for menu Media Koleksi', NULL),
('633', 'master-media-koleksi/edit', 'master-media-koleksi', 'Media Koleksi', 'Update Permission for menu Media Koleksi', NULL),
('634', 'master-media-koleksi/delete', 'master-media-koleksi', 'Media Koleksi', 'Update Permission for menu Media Koleksi', NULL),
('635', 'master-sumber-koleksi/access', 'master-sumber-koleksi', 'Sumber Koleksi', 'Update Permission for menu Sumber Koleksi', NULL),
('636', 'master-sumber-koleksi/index', 'master-sumber-koleksi', 'Sumber Koleksi', 'Update Permission for menu Sumber Koleksi', NULL),
('637', 'master-sumber-koleksi/create', 'master-sumber-koleksi', 'Sumber Koleksi', 'Update Permission for menu Sumber Koleksi', NULL),
('638', 'master-sumber-koleksi/edit', 'master-sumber-koleksi', 'Sumber Koleksi', 'Update Permission for menu Sumber Koleksi', NULL),
('639', 'master-sumber-koleksi/delete', 'master-sumber-koleksi', 'Sumber Koleksi', 'Update Permission for menu Sumber Koleksi', NULL),
('640', 'master-jenis-akses/access', 'master-jenis-akses', 'Jenis Akses', 'Update Permission for menu Jenis Akses', NULL),
('641', 'master-jenis-akses/index', 'master-jenis-akses', 'Jenis Akses', 'Update Permission for menu Jenis Akses', NULL),
('642', 'master-jenis-akses/create', 'master-jenis-akses', 'Jenis Akses', 'Update Permission for menu Jenis Akses', NULL),
('643', 'master-jenis-akses/edit', 'master-jenis-akses', 'Jenis Akses', 'Update Permission for menu Jenis Akses', NULL),
('644', 'master-jenis-akses/delete', 'master-jenis-akses', 'Jenis Akses', 'Update Permission for menu Jenis Akses', NULL),
('645', 'mater-jenis-bahan/access', 'mater-jenis-bahan', 'Jenis Bahan', 'Update Permission for menu Jenis Bahan', NULL),
('646', 'mater-jenis-bahan/index', 'mater-jenis-bahan', 'Jenis Bahan', 'Update Permission for menu Jenis Bahan', NULL),
('647', 'mater-jenis-bahan/create', 'mater-jenis-bahan', 'Jenis Bahan', 'Update Permission for menu Jenis Bahan', NULL),
('648', 'mater-jenis-bahan/edit', 'mater-jenis-bahan', 'Jenis Bahan', 'Update Permission for menu Jenis Bahan', NULL),
('649', 'mater-jenis-bahan/delete', 'mater-jenis-bahan', 'Jenis Bahan', 'Update Permission for menu Jenis Bahan', NULL),
('650', 'master-jenis-denda/access', 'master-jenis-denda', 'Jenis Denda', 'Update Permission for menu Jenis Denda', NULL),
('651', 'master-jenis-denda/index', 'master-jenis-denda', 'Jenis Denda', 'Update Permission for menu Jenis Denda', NULL),
('652', 'master-jenis-denda/create', 'master-jenis-denda', 'Jenis Denda', 'Update Permission for menu Jenis Denda', NULL),
('653', 'master-jenis-denda/edit', 'master-jenis-denda', 'Jenis Denda', 'Update Permission for menu Jenis Denda', NULL),
('654', 'master-jenis-denda/delete', 'master-jenis-denda', 'Jenis Denda', 'Update Permission for menu Jenis Denda', NULL),
('655', 'master-peraturan-peminjaman-hari/access', 'master-peraturan-peminjaman-hari', 'Peraturan Peminjaman Hari', 'Update Permission for menu Peraturan Peminjaman Hari', NULL),
('656', 'master-peraturan-peminjaman-hari/index', 'master-peraturan-peminjaman-hari', 'Peraturan Peminjaman Hari', 'Update Permission for menu Peraturan Peminjaman Hari', NULL),
('657', 'master-peraturan-peminjaman-hari/create', 'master-peraturan-peminjaman-hari', 'Peraturan Peminjaman Hari', 'Update Permission for menu Peraturan Peminjaman Hari', NULL),
('658', 'master-peraturan-peminjaman-hari/edit', 'master-peraturan-peminjaman-hari', 'Peraturan Peminjaman Hari', 'Update Permission for menu Peraturan Peminjaman Hari', NULL),
('659', 'master-peraturan-peminjaman-hari/delete', 'master-peraturan-peminjaman-hari', 'Peraturan Peminjaman Hari', 'Update Permission for menu Peraturan Peminjaman Hari', NULL),
('660', 'karantina-eksemplar/access', 'karantina-eksemplar', 'Karantina Eksemplar', 'Update Permission for menu Karantina Eksemplar', NULL),
('661', 'karantina-eksemplar/index', 'karantina-eksemplar', 'Karantina Eksemplar', 'Update Permission for menu Karantina Eksemplar', NULL),
('662', 'karantina-eksemplar/create', 'karantina-eksemplar', 'Karantina Eksemplar', 'Update Permission for menu Karantina Eksemplar', NULL),
('663', 'karantina-eksemplar/edit', 'karantina-eksemplar', 'Karantina Eksemplar', 'Update Permission for menu Karantina Eksemplar', NULL);
INSERT INTO `auth_permissions` (`id`, `name`, `route`, `menu`, `description`, `category`) VALUES
('664', 'karantina-eksemplar/delete', 'karantina-eksemplar', 'Karantina Eksemplar', 'Update Permission for menu Karantina Eksemplar', NULL),
('671', 'master-jenis-bahan/access', 'master-jenis-bahan', 'Jenis Bahan', 'Update Permission for menu Jenis Bahan', NULL),
('672', 'master-jenis-bahan/index', 'master-jenis-bahan', 'Jenis Bahan', 'Update Permission for menu Jenis Bahan', NULL),
('673', 'master-jenis-bahan/create', 'master-jenis-bahan', 'Jenis Bahan', 'Update Permission for menu Jenis Bahan', NULL),
('674', 'master-jenis-bahan/edit', 'master-jenis-bahan', 'Jenis Bahan', 'Update Permission for menu Jenis Bahan', NULL),
('675', 'master-jenis-bahan/delete', 'master-jenis-bahan', 'Jenis Bahan', 'Update Permission for menu Jenis Bahan', NULL),
('676', '/access', '', 'Koleksi', 'Update Permission for menu Koleksi', NULL),
('677', 'master-tujuan-buku-tamu/access', 'master-tujuan-buku-tamu', 'Tujuan Kunjungan', 'Create Permission for menu Tujuan Kunjungan', NULL),
('678', 'master-tujuan-buku-tamu/index', 'master-tujuan-buku-tamu', 'Tujuan Kunjungan', 'Create Permission for menu Tujuan Kunjungan', NULL),
('679', 'master-tujuan-buku-tamu/create', 'master-tujuan-buku-tamu', 'Tujuan Kunjungan', 'Create Permission for menu Tujuan Kunjungan', NULL),
('680', 'master-tujuan-buku-tamu/edit', 'master-tujuan-buku-tamu', 'Tujuan Kunjungan', 'Create Permission for menu Tujuan Kunjungan', NULL),
('681', 'master-tujuan-buku-tamu/delete', 'master-tujuan-buku-tamu', 'Tujuan Kunjungan', 'Create Permission for menu Tujuan Kunjungan', NULL),
('682', 'master-setting-buku-tamu/access', 'master-setting-buku-tamu', 'Setting Buku Tamu', 'Update Permission for menu Setting Buku Tamu', NULL),
('683', 'master-setting-buku-tamu/index', 'master-setting-buku-tamu', 'Setting Buku Tamu', 'Update Permission for menu Setting Buku Tamu', NULL),
('684', 'master-setting-buku-tamu/create', 'master-setting-buku-tamu', 'Setting Buku Tamu', 'Update Permission for menu Setting Buku Tamu', NULL),
('685', 'master-setting-buku-tamu/edit', 'master-setting-buku-tamu', 'Setting Buku Tamu', 'Update Permission for menu Setting Buku Tamu', NULL),
('686', 'master-setting-buku-tamu/delete', 'master-setting-buku-tamu', 'Setting Buku Tamu', 'Update Permission for menu Setting Buku Tamu', NULL),
('687', 'master-tujuan-kunjungan/access', 'master-tujuan-kunjungan', 'Tujuan Kunjungan', 'Update Permission for menu Tujuan Kunjungan', NULL),
('688', 'master-tujuan-kunjungan/index', 'master-tujuan-kunjungan', 'Tujuan Kunjungan', 'Update Permission for menu Tujuan Kunjungan', NULL),
('689', 'master-tujuan-kunjungan/create', 'master-tujuan-kunjungan', 'Tujuan Kunjungan', 'Update Permission for menu Tujuan Kunjungan', NULL),
('690', 'master-tujuan-kunjungan/edit', 'master-tujuan-kunjungan', 'Tujuan Kunjungan', 'Update Permission for menu Tujuan Kunjungan', NULL),
('691', 'master-tujuan-kunjungan/delete', 'master-tujuan-kunjungan', 'Tujuan Kunjungan', 'Update Permission for menu Tujuan Kunjungan', NULL),
('692', 'master-jenis-pekerjaan/access', 'master-jenis-pekerjaan', 'Pekerjaan', 'Update Permission for menu Pekerjaan', NULL),
('693', 'master-jenis-pekerjaan/index', 'master-jenis-pekerjaan', 'Pekerjaan', 'Update Permission for menu Pekerjaan', NULL),
('694', 'master-jenis-pekerjaan/create', 'master-jenis-pekerjaan', 'Pekerjaan', 'Update Permission for menu Pekerjaan', NULL),
('695', 'master-jenis-pekerjaan/edit', 'master-jenis-pekerjaan', 'Pekerjaan', 'Update Permission for menu Pekerjaan', NULL),
('696', 'master-jenis-pekerjaan/delete', 'master-jenis-pekerjaan', 'Pekerjaan', 'Update Permission for menu Pekerjaan', NULL),
('697', 'master-jenis-pendidikan/access', 'master-jenis-pendidikan', 'Pendidikan', 'Update Permission for menu Pendidikan', NULL),
('698', 'master-jenis-pendidikan/index', 'master-jenis-pendidikan', 'Pendidikan', 'Update Permission for menu Pendidikan', NULL),
('699', 'master-jenis-pendidikan/create', 'master-jenis-pendidikan', 'Pendidikan', 'Update Permission for menu Pendidikan', NULL),
('700', 'master-jenis-pendidikan/edit', 'master-jenis-pendidikan', 'Pendidikan', 'Update Permission for menu Pendidikan', NULL),
('701', 'master-jenis-pendidikan/delete', 'master-jenis-pendidikan', 'Pendidikan', 'Update Permission for menu Pendidikan', NULL),
('702', 'master-form-sabtuminggu/access', 'master-form-sabtuminggu', 'Layanan Sabtu & Minggu', 'Update Permission for menu Layanan Sabtu & Minggu', NULL),
('703', 'master-form-sabtuminggu/index', 'master-form-sabtuminggu', 'Layanan Sabtu & Minggu', 'Update Permission for menu Layanan Sabtu & Minggu', NULL),
('704', 'master-form-sabtuminggu/create', 'master-form-sabtuminggu', 'Layanan Sabtu & Minggu', 'Update Permission for menu Layanan Sabtu & Minggu', NULL),
('705', 'master-form-sabtuminggu/edit', 'master-form-sabtuminggu', 'Layanan Sabtu & Minggu', 'Update Permission for menu Layanan Sabtu & Minggu', NULL),
('706', 'master-form-sabtuminggu/delete', 'master-form-sabtuminggu', 'Layanan Sabtu & Minggu', 'Update Permission for menu Layanan Sabtu & Minggu', NULL),
('712', 'bukutamu/access', 'bukutamu', 'Buku Tamu', 'Create Permission for menu Buku Tamu', NULL),
('713', 'bukutamu/index', 'bukutamu', 'Buku Tamu', 'Create Permission for menu Buku Tamu', NULL),
('714', 'bukutamu/create', 'bukutamu', 'Buku Tamu', 'Create Permission for menu Buku Tamu', NULL),
('715', 'bukutamu/edit', 'bukutamu', 'Buku Tamu', 'Create Permission for menu Buku Tamu', NULL),
('716', 'bukutamu/delete', 'bukutamu', 'Buku Tamu', 'Create Permission for menu Buku Tamu', NULL),
('717', 'laporan-katalog/access', 'laporan-katalog', 'Laporan Katalog', 'Update Permission for menu Laporan Katalog', NULL),
('718', 'laporan-katalog/index', 'laporan-katalog', 'Laporan Katalog', 'Update Permission for menu Laporan Katalog', NULL),
('719', 'laporan-eksemplar/access', 'laporan-eksemplar', 'Laporan Eksemplar', 'Update Permission for menu Laporan Eksemplar', NULL),
('720', 'laporan-eksemplar/index', 'laporan-eksemplar', 'Laporan Eksemplar', 'Update Permission for menu Laporan Eksemplar', NULL),
('721', 'laporan-katalog/create', 'laporan-katalog', 'Laporan Katalog', 'Update Permission for menu Laporan Katalog', NULL),
('722', 'laporan-katalog/edit', 'laporan-katalog', 'Laporan Katalog', 'Update Permission for menu Laporan Katalog', NULL),
('723', 'laporan-katalog/delete', 'laporan-katalog', 'Laporan Katalog', 'Update Permission for menu Laporan Katalog', NULL),
('724', 'laporan-eksemplar/create', 'laporan-eksemplar', 'Laporan Eksemplar', 'Update Permission for menu Laporan Eksemplar', NULL),
('725', 'laporan-eksemplar/edit', 'laporan-eksemplar', 'Laporan Eksemplar', 'Update Permission for menu Laporan Eksemplar', NULL),
('726', 'laporan-eksemplar/delete', 'laporan-eksemplar', 'Laporan Eksemplar', 'Update Permission for menu Laporan Eksemplar', NULL),
('727', 'eksemplar/create/access', 'eksemplar/create', 'Daftar Eksemplar', 'Update Permission for menu Entri Eksemplar', NULL),
('728', 'eksemplar/create/index', 'eksemplar/create', 'Daftar Eksemplar', 'Update Permission for menu Entri Eksemplar', NULL),
('729', 'eksemplar/create/create', 'eksemplar/create', 'Daftar Eksemplar', 'Update Permission for menu Entri Eksemplar', NULL),
('730', 'eksemplar/create/edit', 'eksemplar/create', 'Daftar Eksemplar', 'Update Permission for menu Entri Eksemplar', NULL),
('731', 'eksemplar/create/delete', 'eksemplar/create', 'Daftar Eksemplar', 'Update Permission for menu Entri Eksemplar', NULL),
('732', 'eksemplar/import/access', 'eksemplar/import', 'Import Excel', 'Create Permission for menu Import Eksemplar', NULL),
('733', 'eksemplar/import/index', 'eksemplar/import', 'Import Excel', 'Create Permission for menu Import Eksemplar', NULL),
('734', 'eksemplar/import/create', 'eksemplar/import', 'Import Excel', 'Create Permission for menu Import Eksemplar', NULL),
('735', 'eksemplar/import/edit', 'eksemplar/import', 'Import Excel', 'Create Permission for menu Import Eksemplar', NULL),
('736', 'eksemplar/import/delete', 'eksemplar/import', 'Import Excel', 'Create Permission for menu Import Eksemplar', NULL),
('743', 'katalog/create', 'katalog/create', 'Daftar Katalog', 'Create Permission for menu Entri Katalog', NULL),
('744', 'katalog/create/edit', 'katalog/create', 'Daftar Katalog', 'Create Permission for menu Entri Katalog', NULL),
('745', 'katalog/create/delete', 'katalog/create', 'Daftar Katalog', 'Create Permission for menu Entri Katalog', NULL),
('746', 'katalog/create?rda=0/access', 'katalog/create?rda=0', 'Daftar Katalog', 'Create Permission for menu Entri Katalog AACR', NULL),
('747', 'katalog/create?rda=0/index', 'katalog/create?rda=0', 'Daftar Katalog', 'Create Permission for menu Entri Katalog AACR', NULL),
('748', 'katalog/create?rda=0/create', 'katalog/create?rda=0', 'Daftar Katalog', 'Create Permission for menu Entri Katalog AACR', NULL),
('749', 'katalog/create?rda=0/edit', 'katalog/create?rda=0', 'Daftar Katalog', 'Create Permission for menu Entri Katalog AACR', NULL),
('750', 'katalog/create?rda=0/delete', 'katalog/create?rda=0', 'Daftar Katalog', 'Create Permission for menu Entri Katalog AACR', NULL),
('751', 'katalog/create?rda=1/access', 'katalog/create?rda=1', 'Daftar Katalog', 'Create Permission for menu Entri Katalog RDA', NULL),
('752', 'katalog/create?rda=1/index', 'katalog/create?rda=1', 'Daftar Katalog', 'Create Permission for menu Entri Katalog RDA', NULL),
('753', 'katalog/create?rda=1/create', 'katalog/create?rda=1', 'Daftar Katalog', 'Create Permission for menu Entri Katalog RDA', NULL),
('754', 'katalog/create?rda=1/edit', 'katalog/create?rda=1', 'Daftar Katalog', 'Create Permission for menu Entri Katalog RDA', NULL),
('755', 'katalog/create?rda=1/delete', 'katalog/create?rda=1', 'Daftar Katalog', 'Create Permission for menu Entri Katalog RDA', NULL),
('756', 'perpanjangan-anggota/access', 'perpanjangan-anggota', 'Perpanjangan Anggota', 'Update Permission for menu Perpanjangan Anggota', NULL),
('757', 'perpanjangan-anggota/index', 'perpanjangan-anggota', 'Perpanjangan Anggota', 'Update Permission for menu Perpanjangan Anggota', NULL),
('758', 'perpanjangan-anggota/create', 'perpanjangan-anggota', 'Perpanjangan Anggota', 'Update Permission for menu Perpanjangan Anggota', NULL),
('759', 'perpanjangan-anggota/edit', 'perpanjangan-anggota', 'Perpanjangan Anggota', 'Update Permission for menu Perpanjangan Anggota', NULL),
('760', 'perpanjangan-anggota/delete', 'perpanjangan-anggota', 'Perpanjangan Anggota', 'Update Permission for menu Perpanjangan Anggota', NULL),
('776', 'ruas-data-bibliografis/access', 'ruas-data-bibliografis', 'Ruas Data Bibliografis', 'Update Permission for menu Ruas Data Bibliografis', NULL),
('777', 'ruas-data-bibliografis/index', 'ruas-data-bibliografis', 'Ruas Data Bibliografis', 'Update Permission for menu Ruas Data Bibliografis', NULL),
('778', 'ruas-data-bibliografis/create', 'ruas-data-bibliografis', 'Ruas Data Bibliografis', 'Update Permission for menu Ruas Data Bibliografis', NULL),
('779', 'ruas-data-bibliografis/edit', 'ruas-data-bibliografis', 'Ruas Data Bibliografis', 'Update Permission for menu Ruas Data Bibliografis', NULL),
('780', 'ruas-data-bibliografis/delete', 'ruas-data-bibliografis', 'Ruas Data Bibliografis', 'Update Permission for menu Ruas Data Bibliografis', NULL),
('781', 'master-penomoran-koleksi/access', 'master-penomoran-koleksi', 'Penomoran koleksi', 'Update Permission for menu Penomoran koleksi', NULL),
('782', 'master-penomoran-koleksi/index', 'master-penomoran-koleksi', 'Penomoran koleksi', 'Update Permission for menu Penomoran koleksi', NULL),
('783', 'master-penomoran-koleksi/create', 'master-penomoran-koleksi', 'Penomoran koleksi', 'Update Permission for menu Penomoran koleksi', NULL),
('784', 'master-penomoran-koleksi/edit', 'master-penomoran-koleksi', 'Penomoran koleksi', 'Update Permission for menu Penomoran koleksi', NULL),
('785', 'master-penomoran-koleksi/delete', 'master-penomoran-koleksi', 'Penomoran koleksi', 'Update Permission for menu Penomoran koleksi', NULL),
('791', 'master-redaksi-keanggotaan/access', 'master-redaksi-keanggotaan', 'Redaksi Keanggotaan', 'Update Permission for menu Redaksi Keanggotaan', NULL),
('792', 'master-redaksi-keanggotaan/index', 'master-redaksi-keanggotaan', 'Redaksi Keanggotaan', 'Update Permission for menu Redaksi Keanggotaan', NULL),
('793', 'master-redaksi-keanggotaan/create', 'master-redaksi-keanggotaan', 'Redaksi Keanggotaan', 'Update Permission for menu Redaksi Keanggotaan', NULL),
('794', 'master-redaksi-keanggotaan/edit', 'master-redaksi-keanggotaan', 'Redaksi Keanggotaan', 'Update Permission for menu Redaksi Keanggotaan', NULL),
('795', 'master-redaksi-keanggotaan/delete', 'master-redaksi-keanggotaan', 'Redaksi Keanggotaan', 'Update Permission for menu Redaksi Keanggotaan', NULL),
('796', 'master-kelompok-umur/access', 'master-kelompok-umur', 'Kelompok Umur', 'Update Permission for menu Kelompok Umur', NULL),
('797', 'master-kelompok-umur/index', 'master-kelompok-umur', 'Kelompok Umur', 'Update Permission for menu Kelompok Umur', NULL),
('798', 'master-kelompok-umur/create', 'master-kelompok-umur', 'Kelompok Umur', 'Update Permission for menu Kelompok Umur', NULL),
('799', 'master-kelompok-umur/edit', 'master-kelompok-umur', 'Kelompok Umur', 'Update Permission for menu Kelompok Umur', NULL),
('800', 'master-kelompok-umur/delete', 'master-kelompok-umur', 'Kelompok Umur', 'Update Permission for menu Kelompok Umur', NULL),
('801', 'master-fakultas/access', 'master-fakultas', 'Master Fakultas', 'Update Permission for menu Master Fakultas', NULL),
('802', 'master-fakultas/index', 'master-fakultas', 'Master Fakultas', 'Update Permission for menu Master Fakultas', NULL),
('803', 'master-fakultas/create', 'master-fakultas', 'Master Fakultas', 'Update Permission for menu Master Fakultas', NULL),
('804', 'master-fakultas/edit', 'master-fakultas', 'Master Fakultas', 'Update Permission for menu Master Fakultas', NULL),
('805', 'master-fakultas/delete', 'master-fakultas', 'Master Fakultas', 'Update Permission for menu Master Fakultas', NULL),
('806', 'master-jurusan/access', 'master-jurusan', 'Master Jurusan', 'Update Permission for menu Master Jurusan', NULL),
('807', 'master-entri-keanggotaan/access', 'master-entri-keanggotaan', 'Entri Keanggotaan', 'Update Permission for menu Entri Keanggotaan', NULL),
('808', 'master-entri-keanggotaan/index', 'master-entri-keanggotaan', 'Entri Keanggotaan', 'Update Permission for menu Entri Keanggotaan', NULL),
('809', 'master-entri-keanggotaan/create', 'master-entri-keanggotaan', 'Entri Keanggotaan', 'Update Permission for menu Entri Keanggotaan', NULL),
('810', 'master-entri-keanggotaan/edit', 'master-entri-keanggotaan', 'Entri Keanggotaan', 'Update Permission for menu Entri Keanggotaan', NULL),
('811', 'master-entri-keanggotaan/delete', 'master-entri-keanggotaan', 'Entri Keanggotaan', 'Update Permission for menu Entri Keanggotaan', NULL),
('812', 'katalog/marc-import/access', 'katalog/marc-import', 'Salin Katalog', 'Update Permission for menu Salin Katalog', NULL),
('813', 'katalog/marc-import/index', 'katalog/marc-import', 'Salin Katalog', 'Update Permission for menu Salin Katalog', NULL),
('814', 'katalog/marc-import/create', 'katalog/marc-import', 'Salin Katalog', 'Update Permission for menu Salin Katalog', NULL),
('815', 'katalog/marc-import/edit', 'katalog/marc-import', 'Salin Katalog', 'Update Permission for menu Salin Katalog', NULL),
('816', 'katalog/marc-import/delete', 'katalog/marc-import', 'Salin Katalog', 'Update Permission for menu Salin Katalog', NULL),
('817', 'pengaturan-katalog/index', 'pengaturan-katalog', 'Pengaturan Katalog', 'Update Permission for menu Pengaturan Katalog', NULL),
('818', 'pengaturan-katalog/create', 'pengaturan-katalog', 'Pengaturan Katalog', 'Update Permission for menu Pengaturan Katalog', NULL),
('819', 'pengaturan-katalog/edit', 'pengaturan-katalog', 'Pengaturan Katalog', 'Update Permission for menu Pengaturan Katalog', NULL),
('820', 'pengaturan-katalog/delete', 'pengaturan-katalog', 'Pengaturan Katalog', 'Update Permission for menu Pengaturan Katalog', NULL),
('826', 'master-jenis-pelanggaran/access', 'master-jenis-pelanggaran', 'Jenis Pelanggaran', 'Update Permission for menu Jenis Pelanggaran', NULL),
('827', 'master-jenis-pelanggaran/index', 'master-jenis-pelanggaran', 'Jenis Pelanggaran', 'Update Permission for menu Jenis Pelanggaran', NULL),
('828', 'master-jenis-pelanggaran/create', 'master-jenis-pelanggaran', 'Jenis Pelanggaran', 'Update Permission for menu Jenis Pelanggaran', NULL),
('829', 'master-jenis-pelanggaran/edit', 'master-jenis-pelanggaran', 'Jenis Pelanggaran', 'Update Permission for menu Jenis Pelanggaran', NULL),
('830', 'master-jenis-pelanggaran/delete', 'master-jenis-pelanggaran', 'Jenis Pelanggaran', 'Update Permission for menu Jenis Pelanggaran', NULL),
('831', 'laporan-ai/access', 'laporan-ai', 'Laporan AI', 'Create Permission for menu Laporan AI', NULL),
('832', 'laporan-ai/index', 'laporan-ai', 'Laporan AI', 'Create Permission for menu Laporan AI', NULL),
('833', 'laporan-ai/create', 'laporan-ai', 'Laporan AI', 'Create Permission for menu Laporan AI', NULL),
('834', 'laporan-ai/edit', 'laporan-ai', 'Laporan AI', 'Create Permission for menu Laporan AI', NULL),
('835', 'laporan-ai/delete', 'laporan-ai', 'Laporan AI', 'Create Permission for menu Laporan AI', NULL),
('841', 'anggota/online/anggota/online', 'anggota/online', '23', '', NULL),
('842', 'anggota/online/access', 'anggota/online', 'Profil Anggota', 'Update Permission for menu Profil Anggota', NULL),
('843', 'anggota/online/index', 'anggota/online', 'Profil Anggota', 'Update Permission for menu Profil Anggota', NULL),
('844', 'anggota/online/create', 'anggota/online', 'Profil Anggota', 'Update Permission for menu Profil Anggota', NULL),
('845', 'anggota/online/edit', 'anggota/online', 'Profil Anggota', 'Update Permission for menu Profil Anggota', NULL),
('846', 'anggota/online/delete', 'anggota/online', 'Profil Anggota', 'Update Permission for menu Profil Anggota', NULL),
('847', 'stockopname/access', 'stockopname', 'Stockopname', 'Update Permission for menu Stockopname', NULL),
('848', 'stockopname/index', 'stockopname', 'Stockopname', 'Update Permission for menu Stockopname', NULL),
('849', 'stockopname/create', 'stockopname', 'Stockopname', 'Update Permission for menu Stockopname', NULL),
('850', 'stockopname/edit', 'stockopname', 'Stockopname', 'Update Permission for menu Stockopname', NULL),
('851', 'stockopname/delete', 'stockopname', 'Stockopname', 'Update Permission for menu Stockopname', NULL),
('852', 'stockopname/store', 'stockopname', 'Stockopname', 'Create Permission for menu Stockopname', ''),
('853', 'master-nama-perpustakaan/update', 'master-nama-perpustakaan', 'Nama Perpustakaan', 'Create Permission for menu Nama Perpustakaan', NULL),
('854', 'stockopname/detail', 'stockopname', 'Stockopname', 'Create Permission for menu Stockopname', NULL),
('855', 'laporan-buku-tamu/access', 'laporan-buku-tamu', 'Laporan Buku Tamu', 'Update Permission for menu Laporan Buku Tamu', NULL),
('856', 'laporan-buku-tamu/index', 'laporan-buku-tamu', 'Laporan Buku Tamu', 'Update Permission for menu Laporan Buku Tamu', NULL),
('857', 'laporan-buku-tamu/create', 'laporan-buku-tamu', 'Laporan Buku Tamu', 'Update Permission for menu Laporan Buku Tamu', NULL),
('858', 'laporan-buku-tamu/edit', 'laporan-buku-tamu', 'Laporan Buku Tamu', 'Update Permission for menu Laporan Buku Tamu', NULL),
('859', 'laporan-buku-tamu/delete', 'laporan-buku-tamu', 'Laporan Buku Tamu', 'Update Permission for menu Laporan Buku Tamu', NULL),
('860', 'katalog/edit_marc', 'katalog', 'Daftar Katalog', 'Update Permission for menu Katalog', NULL),
('862', 'master-jenis-anggota/apply_status', 'master-jenis-anggota', 'Jenis Anggota', '', NULL),
('863', 'master-jenis-anggota/defaultlokasi', 'master-jenis-anggota', 'Jenis Anggota', '', NULL),
('864', 'master-jenis-anggota/defaultbahan', 'master-jenis-anggota', 'Jenis Anggota', '', NULL),
('865', 'master-kelas-besar/apply_status', 'master-kelas-besar', 'Kelas Besar', '', NULL),
('866', 'master-kelas/access', 'master-kelas', 'Master Kelas', 'Update Permission for menu Master Kelas', NULL),
('867', 'master-kelas/index', 'master-kelas', 'Master Kelas', 'Update Permission for menu Master Kelas', NULL),
('868', 'master-kelas/create', 'master-kelas', 'Master Kelas', 'Update Permission for menu Master Kelas', NULL),
('869', 'master-kelas/edit', 'master-kelas', 'Master Kelas', 'Update Permission for menu Master Kelas', NULL),
('870', 'master-kelas/delete', 'master-kelas', 'Master Kelas', 'Update Permission for menu Master Kelas', NULL),
('871', 'master-kelas/apply_status', 'master-kelas', 'Master Kelas', '', NULL),
('872', 'master-peraturan-peminjaman-hari/apply_status', 'master-peraturan-peminjaman-hari', 'Peraturan Peminjaman Hari', '', NULL),
('873', 'cms/berita/access', 'cms/berita', 'Berita', 'Update Permission for menu Berita', NULL),
('874', 'cms/berita/index', 'cms/berita', 'Berita', 'Update Permission for menu Berita', NULL),
('875', 'cms/berita/create', 'cms/berita', 'Berita', 'Update Permission for menu Berita', NULL),
('876', 'cms/berita/edit', 'cms/berita', 'Berita', 'Update Permission for menu Berita', NULL),
('877', 'cms/berita/delete', 'cms/berita', 'Berita', 'Update Permission for menu Berita', NULL),
('879', 'master-peraturan-peminjaman-tanggal/access', 'master-peraturan-peminjaman-tanggal', 'Peraturan Peminjaman Tanggal', 'Update Permission for menu Peraturan Peminjaman Tanggal', NULL),
('880', 'master-peraturan-peminjaman-tanggal/index', 'master-peraturan-peminjaman-tanggal', 'Peraturan Peminjaman Tanggal', 'Update Permission for menu Peraturan Peminjaman Tanggal', NULL),
('881', 'master-peraturan-peminjaman-tanggal/create', 'master-peraturan-peminjaman-tanggal', 'Peraturan Peminjaman Tanggal', 'Update Permission for menu Peraturan Peminjaman Tanggal', NULL),
('882', 'master-peraturan-peminjaman-tanggal/edit', 'master-peraturan-peminjaman-tanggal', 'Peraturan Peminjaman Tanggal', 'Update Permission for menu Peraturan Peminjaman Tanggal', NULL),
('883', 'master-peraturan-peminjaman-tanggal/delete', 'master-peraturan-peminjaman-tanggal', 'Peraturan Peminjaman Tanggal', 'Update Permission for menu Peraturan Peminjaman Tanggal', NULL),
('885', 'master-peraturan-peminjaman-tanggal/apply_status', 'master-peraturan-peminjaman-tanggal', 'Peraturan Peminjaman Tanggal', '', NULL),
('887', 'eksemplar/importviews/access', 'eksemplar/importviews', 'Import Excel', 'Update Permission for menu Import Excel', NULL),
('888', 'eksemplar/importviews/index', 'eksemplar/importviews', 'Import Excel', 'Update Permission for menu Import Excel', NULL),
('889', 'eksemplar/importviews/create', 'eksemplar/importviews', 'Import Excel', 'Update Permission for menu Import Excel', NULL),
('890', 'eksemplar/importviews/edit', 'eksemplar/importviews', 'Import Excel', 'Update Permission for menu Import Excel', NULL),
('891', 'eksemplar/importviews/delete', 'eksemplar/importviews', 'Import Excel', 'Update Permission for menu Import Excel', NULL),
('892', 'eksemplar/importviews', 'eksemplar', 'Import Excel', '', NULL),
('893', 'eksemplar/download-template', 'eksemplar', 'Import Excel', '', NULL),
('894', 'laporan-sirkulasi/access', 'laporan-sirkulasi', 'Laporan Sirkulasi', 'Update Permission for menu Laporan Sirkulasi', NULL),
('895', 'laporan-sirkulasi/index', 'laporan-sirkulasi', 'Laporan Sirkulasi', 'Update Permission for menu Laporan Sirkulasi', NULL),
('896', 'laporan-sirkulasi/create', 'laporan-sirkulasi', 'Laporan Sirkulasi', 'Update Permission for menu Laporan Sirkulasi', NULL),
('897', 'laporan-sirkulasi/edit', 'laporan-sirkulasi', 'Laporan Sirkulasi', 'Update Permission for menu Laporan Sirkulasi', NULL),
('898', 'laporan-sirkulasi/delete', 'laporan-sirkulasi', 'Laporan Sirkulasi', 'Update Permission for menu Laporan Sirkulasi', NULL),
('904', 'katalog/proses_karantina', 'katalog/proses_karantina', 'Daftar Katalog', '', NULL),
('905', 'katalog/pulihkan_katalog', 'katalog/pulihkan_katalog', 'Karantina Katalog', '', NULL),
('906', 'laporan-eksemplar/export', 'laporan-eksemplar/export', 'Laporan Eksemplar', '', NULL),
('907', 'laporan-anggota/export', 'laporan-anggota/export', 'Laporan Anggota', '', NULL),
('908', 'katalog/view_decrypted', 'katalog/view_decrypted', 'Daftar Katalog', '', NULL),
('909', 'katalog/get_decrypted_content', 'katalog/get_decrypted_content', 'Daftar Katalog', '', NULL),
('911', 'master-nama-perpustakaan/searchperpustakaan', 'master-nama-perpustakaan/searchperpustakaan', 'Nama Perpustakaan', '', NULL),
('912', 'eksemplar/print_label', 'eksemplar/print_label', 'Daftar Eksemplar', '', NULL),
('913', 'master-jurusan/index', 'master-jurusan/index', 'Master Jurusan', '', NULL),
('914', 'laporan-katalog/export', 'laporan-katalog/export', 'Laporan Katalog', '', NULL),
('915', 'cms/banner/access', 'cms/banner', 'Banner', 'Create Permission for menu Banner', NULL),
('916', 'cms/banner/index', 'cms/banner', 'Banner', 'Create Permission for menu Banner', NULL),
('917', 'cms/banner/create', 'cms/banner', 'Banner', 'Create Permission for menu Banner', NULL),
('918', 'cms/banner/edit', 'cms/banner', 'Banner', 'Create Permission for menu Banner', NULL),
('919', 'cms/banner/delete', 'cms/banner', 'Banner', 'Create Permission for menu Banner', NULL),
('920', 'user/profile', 'user/profile', 'User', '', NULL);
INSERT INTO `auth_permissions` (`id`, `name`, `route`, `menu`, `description`, `category`) VALUES
('921', 'group/permission', 'group/permission', 'Group', '', NULL),
('922', 'sirkulasi-peminjaman/loan_digital_store', 'sirkulasi-peminjaman/loan_digital_store', 'Peminjaman', '', NULL),
('923', 'anggota/import_anggota/access', 'anggota/import_anggota', 'Import Anggota', 'Update Permission for menu Import Anggota', NULL),
('924', 'anggota/import_anggota/index', 'anggota/import_anggota', 'Import Anggota', 'Update Permission for menu Import Anggota', NULL),
('925', 'anggota/import', 'anggota/import', 'Import Anggota', 'Create Permission for menu Import Anggota', NULL),
('931', 'baca-di-tempat/access', 'baca-di-tempat', 'Baca di Tempat', 'Create Permission for menu Baca di Tempat', NULL),
('932', 'baca-di-tempat/index', 'baca-di-tempat', 'Baca di Tempat', 'Create Permission for menu Baca di Tempat', NULL),
('935', 'baca-di-tempat/delete', 'baca-di-tempat', 'Baca di Tempat', 'Create Permission for menu Baca di Tempat', NULL),
('936', 'api/eksemplar/collectionsources', 'api/eksemplar/collectionsources', 'Daftar Eksemplar', '', NULL),
('937', 'api/eksemplar/collectionmedias', 'api/eksemplar/collectionmedias', 'Daftar Eksemplar', '', NULL),
('938', 'api/eksemplar/collectionrules', 'api/eksemplar/collectionrules', 'Daftar Eksemplar', '', NULL),
('939', 'api/eksemplar/collectionmedias', 'api/eksemplar/collectionmedias', 'Daftar Eksemplar', '', NULL),
('940', 'api/eksemplar/collectioncategory', 'api/eksemplar/collectioncategory', 'Daftar Eksemplar', '', NULL),
('941', 'api/eksemplar/collectionstatus', 'api/eksemplar/collectionstatus', 'Daftar Eksemplar', '', NULL),
('942', 'api/eksemplar/collectionpartners', 'api/eksemplar/collectionpartners', 'Daftar Eksemplar', '', NULL),
('943', 'api/eksemplar/locationlibrary', 'api/eksemplar/locationlibrary', 'Lokasi Perpustakaan', '', NULL),
('944', 'api/eksemplar/locations', 'api/eksemplar/locations', 'Lokasi Ruang', '', NULL),
('945', 'api/eksemplar/collectioncurrency', 'api/eksemplar/collectioncurrency', 'Daftar Eksemplar', '', NULL),
('946', 'api/eksemplar/collectionpricetype', 'api/eksemplar/collectionpricetype', 'Daftar Eksemplar', '', NULL),
('947', 'anggota/get_defaults', 'anggota/get_defaults', 'Daftar Anggota', '', NULL),
('948', 'master-entri-keanggotaan/update_data', 'master-entri-keanggotaan/update_data', 'Entri Keanggotaan', '', NULL),
('949', 'anggota/printanggota', 'anggota/printanggota', 'Daftar Anggota', '', NULL),
('950', 'anggota/printkartubelakang', 'anggota/printkartubelakang', 'Daftar Anggota', '', NULL),
('951', 'anggota/bebaspustaka', 'anggota/bebaspustaka', 'Daftar Anggota', '', NULL),
('952', 'api/katalog/get_all_tags', 'api/katalog/get_all_tags', 'Daftar Katalog', '', NULL),
('953', 'anggota/import_view/access', 'anggota/import_view', 'Import Anggota', 'Create Permission for menu Import Anggota', NULL),
('954', 'anggota/import_view/index', 'anggota/import_view', 'Import Anggota', 'Create Permission for menu Import Anggota', NULL),
('955', 'anggota/import_view/create', 'anggota/import_view', 'Import Anggota', 'Create Permission for menu Import Anggota', NULL),
('956', 'anggota/import_view/edit', 'anggota/import_view', 'Import Anggota', 'Create Permission for menu Import Anggota', NULL),
('957', 'anggota/import_view/delete', 'anggota/import_view', 'Import Anggota', 'Create Permission for menu Import Anggota', NULL),
('963', 'laporan-baca-ditempat/access', 'laporan-baca-ditempat', 'laporan-baca-ditempat', 'Create Permission for menu laporan-baca-ditempat', NULL),
('964', 'laporan-baca-ditempat/index', 'laporan-baca-ditempat', 'laporan-baca-ditempat', 'Create Permission for menu laporan-baca-ditempat', NULL),
('965', 'laporan-baca-ditempat/export', 'laporan-baca-ditempat/export', 'laporan-baca-ditempat', '', NULL),
('967', 'laporan-baca-ditempat/preview', 'laporan-baca-ditempat/preview', 'laporan-baca-ditempat', '', NULL),
('970', 'sirkulasi-peminjaman/process-loan', 'sirkulasi-peminjaman/process-loan', 'Entri Peminjaman', '', NULL),
('972', 'sirkulasi-peminjaman/remove-book', 'sirkulasi-peminjaman/remove-book', 'Entri Peminjaman', '', NULL),
('973', 'sirkulasi-peminjaman/success', 'sirkulasi-peminjaman/success', 'Entri Peminjaman', '', NULL),
('976', 'sirkulasi-pengembalian/process-return', 'sirkulasi-pengembalian/process-return', 'Entri  Pengembalian', '', NULL),
('977', 'sirkulasi-pengembalian/check-book', 'sirkulasi-pengembalian/check-book', 'Entri  Pengembalian', '', NULL),
('979', 'sirkulasi-pengembalian/get-jenis-pelanggaran', 'sirkulasi-pengembalian/get-jenis-pelanggaran', 'Entri  Pengembalian', '', NULL),
('981', 'sirkulasi-pengembalian/get-jenis-denda', 'sirkulasi-pengembalian/get-jenis-denda', 'Entri  Pengembalian', '', NULL),
('983', 'sirkulasi-pengembalian/getreturnhistory', 'sirkulasi-pengembalian/getreturnhistory', 'Entri  Pengembalian', '', NULL),
('984', 'sirkulasi-pengembalian/getreturnhistory', 'sirkulasi-pengembalian/getreturnhistory', 'Entri  Pengembalian', '', NULL),
('985', 'sirkulasi-perpanjangan/check-book', 'sirkulasi-perpanjangan/check-book', 'Perpanjangan', '', NULL),
('987', 'sirkulasi-perpanjangan/process-extend', 'sirkulasi-perpanjangan/process-extend', 'Perpanjangan', '', NULL),
('989', 'sirkulasi-perpanjangan/get-extend-history', 'sirkulasi-perpanjangan/get-extend-history', 'Perpanjangan', '', NULL),
('990', 'sirkulasi-perpanjangan/get-extend-history', 'sirkulasi-perpanjangan/get-extend-history', 'Perpanjangan', '', NULL),
('991', 'cms/berita/apply_status', 'cms/berita/apply_status', 'Berita', '', NULL),
('992', 'eksemplar/proses_opac', 'eksemplar/proses_opac', 'Daftar Eksemplar', '', NULL),
('994', 'eksemplar/proses_karantina', 'eksemplar/proses_karantina', 'Daftar Eksemplar', '', NULL),
('995', 'eksemplar/pulihkan_eksemplar', 'eksemplar/pulihkan_eksemplar', 'Karantina Eksemplar', '', NULL),
('996', 'eksemplar/hapus_permanen', 'eksemplar/hapus_permanen', 'Karantina Eksemplar', '', NULL),
('997', 'katalog/hapus_permanen', 'katalog/hapus_permanen', 'Karantina Katalog', '', NULL),
('998', 'anggota/multipleprint', 'anggota/multipleprint', 'Daftar Anggota', '', NULL),
('999', 'user/detail', 'user/detail', 'User', '', NULL),
('1000', 'perpanjangan-anggota/apply_status', 'perpanjangan-anggota/apply_status', 'Perpanjangan Anggota', '', NULL),
('1001', 'katalog/create-marc-from-file', 'katalog/create-marc-from-file', 'Salin Katalog', '', NULL),
('1002', 'katalog/preview-marc-file', 'katalog/preview-marc-file', 'Salin Katalog', '', NULL),
('1003', 'anggota/kirim_kartu_digital', 'anggota/kirim_kartu_digital', 'Daftar Anggota', '', NULL),
('1004', 'master-form-sabtuminggu/update_data', 'master-form-sabtuminggu/update_data', 'Layanan Sabtu & Minggu', '', NULL),
('1005', 'cms/banner/apply_status', 'cms/banner/apply_status', 'Banner', '', NULL),
('1006', 'api/master-hari-libur/datatable', 'api/master-hari-libur/datatable', 'Hari Libur', '', NULL),
('1007', 'master-jenis-perpustakaan/apply_status', 'master-jenis-perpustakaan/apply_status', 'Jenis Perpustakaan', '', NULL),
('1008', 'master-lokasi-perpustakaan/apply_status', 'master-lokasi-perpustakaan/apply_status', 'Lokasi Perpustakaan', '', NULL),
('1009', 'master-lokasi-ruang/apply_status', 'master-lokasi-ruang/apply_status', 'Lokasi Ruang', '', NULL),
('1010', 'master-kategori-koleksi/apply_status', 'master-kategori-koleksi/apply_status', 'Kategori Koleksi', '', NULL),
('1011', 'master-media-koleksi/apply_status', 'master-media-koleksi/apply_status', 'Media Koleksi', '', NULL),
('1012', 'master-sumber-koleksi/apply_status', 'master-sumber-koleksi/apply_status', 'Sumber Koleksi', '', NULL),
('1013', 'master-fakultas/apply_status', 'master-fakultas/apply_status', 'Master Fakultas', '', NULL),
('1014', 'master-jurusan/delete', 'master-jurusan/delete', 'Master Jurusan', '', NULL),
('1015', 'master-jurusan/apply_status', 'master-jurusan/apply_status', 'Master Jurusan', '', NULL),
('1016', 'master-jenis-identitas/apply_status', 'master-jenis-identitas/apply_status', 'Jenis Identitas', '', NULL),
('1017', 'master-jenis-kelamin/apply_status', 'master-jenis-kelamin/apply_status', 'Jenis Kelamin', '', NULL),
('1018', 'master-jenis-pekerjaan/apply_status', 'master-jenis-pekerjaan/apply_status', 'Pekerjaan', '', NULL),
('1019', 'master-jenis-pendidikan/apply_status', 'master-jenis-pendidikan/apply_status', 'Pendidikan', '', NULL),
('1020', 'master-status-perkawinan/apply_status', 'master-status-perkawinan/apply_status', 'Status Perkawinan', '', NULL),
('1021', 'master-jenis-akses/apply_status', 'master-jenis-akses/apply_status', 'Jenis Akses', '', NULL),
('1022', 'master-jenis-denda/apply_status', 'master-jenis-denda/apply_status', 'Jenis Denda', '', NULL),
('1023', 'master-jenis-pelanggaran/apply_status', 'master-jenis-pelanggaran/apply_status', 'Jenis Pelanggaran', '', NULL),
('1024', 'baca-di-tempat/non_anggota', 'baca-di-tempat/non_anggota', 'Baca di Tempat', '', NULL),
('1025', 'anggota/uploadbackground', 'anggota/uploadBackground', 'Daftar Anggota', '', NULL),
('1026', 'sirkulasi-peminjaman/create/access', 'sirkulasi-peminjaman/create/access', 'Entri Peminjaman', '', NULL),
('1027', 'sirkulasi-pengembalian/create/access', 'sirkulasi-pengembalian/create/access', 'Entri  Pengembalian', '', NULL),
('1028', 'sirkulasi-pengembalian/success', 'sirkulasi-pengembalian/success', 'Entri  Pengembalian', '', NULL),
('1029', 'sirkulasi-pengembalian/send-struk', 'sirkulasi-pengembalian/send-struk', 'Entri  Pengembalian', '', NULL),
('1030', 'sirkulasi-perpanjangan/create/access', 'sirkulasi-perpanjangan/create', 'Entri Perpanjangan', 'Create Permission for menu Entri Perpanjangan', NULL),
('1031', 'sirkulasi-perpanjangan/success', 'sirkulasi-perpanjangan/success', 'Entri Perpanjangan', '', NULL),
('1032', 'sirkulasi-perpanjangan/send-struk', 'sirkulasi-perpanjangan/send-struk', 'Entri Perpanjangan', '', NULL),
('1033', 'laporan-buku-tamu/export_pdf', 'laporan-buku-tamu/export_pdf', 'Laporan Buku Tamu', '', NULL),
('1034', 'laporan-katalog/export_pdf', 'laporan-katalog/export_pdf', 'Laporan Katalog', '', NULL),
('1035', 'laporan-eksemplar/export_pdf', 'laporan-eksemplar/export_pdf', 'Laporan Eksemplar', '', NULL),
('1036', 'laporan-anggota/export_pdf', 'laporan-anggota/export_pdf', 'Laporan Anggota', '', NULL),
('1037', 'laporan-sirkulasi/export_pdf', 'laporan-sirkulasi/export_pdf', 'Laporan Sirkulasi', '', NULL),
('1038', 'laporan-baca-ditempat/export_pdf', 'laporan-baca-ditempat/export_pdf', 'laporan-baca-ditempat', '', NULL),
('1039', 'cms/profil/access', 'cms/profil', 'Profil & Layanan', 'Permission untuk Profil & Layanan', NULL),
('1040', 'cms/profil/index', 'cms/profil', 'Profil & Layanan', 'Permission untuk Profil & Layanan', NULL),
('1041', 'cms/profil/edit', 'cms/profil', 'Profil & Layanan', 'Permission untuk Profil & Layanan', NULL),
('1042', 'cms/profil/layanan/datatable', 'cms/profil', 'Profil & Layanan', 'Permission untuk Profil & Layanan', NULL),
('1043', 'cms/profil/layanan/save', 'cms/profil', 'Profil & Layanan', 'Permission untuk Profil & Layanan', NULL),
('1044', 'cms/profil/layanan/update-order', 'cms/profil', 'Profil & Layanan', 'Permission untuk Profil & Layanan', NULL),
('1045', 'cms/profil/layanan/get', 'cms/profil', 'Profil & Layanan', 'Permission untuk Profil & Layanan', NULL),
('1046', 'cms/profil/layanan/delete', 'cms/profil', 'Profil & Layanan', 'Permission untuk Profil & Layanan', NULL),
('1047', 'master-nama-perpustakaan/update-database', 'master-nama-perpustakaan', 'Nama Perpustakaan', 'Admin dapat menjalankan update database', NULL),
('1048', 'usulankoleksi/access', 'usulankoleksi', 'Usulan Koleksi', 'Update Permission for menu Usulan Koleksi', NULL),
('1049', 'usulankoleksi/index', 'usulankoleksi', 'Usulan Koleksi', 'Update Permission for menu Usulan Koleksi', NULL),
('1050', 'surveipemustaka/access', 'surveipemustaka', 'Survei Pemustaka', 'Update Permission for menu Survei Pemustaka', NULL),
('1051', 'surveipemustaka/index', 'surveipemustaka', 'Survei Pemustaka', 'Update Permission for menu Survei Pemustaka', NULL),
('1052', 'surveipemustaka/manage', 'surveipemustaka', 'Survei Pemustaka', 'Kelola Survei Pemustaka', NULL),
('1053', 'surveipemustaka/save-survey', 'surveipemustaka', 'Survei Pemustaka', 'Simpan survei', NULL),
('1054', 'surveipemustaka/save-question', 'surveipemustaka', 'Survei Pemustaka', 'Simpan pertanyaan survei', NULL),
('1055', 'surveipemustaka/save-option', 'surveipemustaka', 'Survei Pemustaka', 'Simpan pilihan survei', NULL),
('1056', 'survei/create', 'survei', 'Survei Pemustaka', 'Izin mengelola survei pada controller lama', NULL),
('1057', 'stockopname/exportexcel', 'stockopname/exportexcel', 'Stockopname', '', NULL),
('1058', 'surveipemustaka/create', 'surveipemustaka', 'Survei Pemustaka', 'Update Permission for menu Survei Pemustaka', NULL),
('1059', 'surveipemustaka/edit', 'surveipemustaka', 'Survei Pemustaka', 'Update Permission for menu Survei Pemustaka', NULL),
('1060', 'surveipemustaka/delete', 'surveipemustaka', 'Survei Pemustaka', 'Update Permission for menu Survei Pemustaka', NULL),
('1061', 'surveipemustaka/hasil', 'surveipemustaka', 'Survei Pemustaka', 'Lihat hasil survei', NULL),
('1062', 'surveipemustaka/pertanyaan', 'surveipemustaka', 'Survei Pemustaka', 'Kelola pertanyaan survei', NULL),
('1063', 'surveipemustaka/pilihan', 'surveipemustaka', 'Survei Pemustaka', 'Kelola pilihan jawaban survei', NULL),
('1064', 'surveipemustaka/isian', 'surveipemustaka', 'Survei Pemustaka', 'Lihat isian jawaban survei', NULL),
('1065', 'sirkulasi-pemesanan/access', 'sirkulasi-pemesanan', 'Koleksi Dipesan', 'Update Permission for menu Koleksi Dipesan', NULL),
('1066', 'sirkulasi-pemesanan/index', 'sirkulasi-pemesanan', 'Koleksi Dipesan', 'Update Permission for menu Koleksi Dipesan', NULL),
('1067', 'sirkulasi-pemesanan/delete', 'sirkulasi-pemesanan', 'Koleksi Dipesan', 'Update Permission for menu Koleksi Dipesan', NULL),
('1068', 'kardeks-terbitan-berkala/access', 'kardeks-terbitan-berkala', 'Kardeks Terbitan Berkala', 'Update Permission for menu Kardeks Terbitan Berkala', NULL),
('1069', 'kardeks-terbitan-berkala/index', 'kardeks-terbitan-berkala', 'Kardeks Terbitan Berkala', 'Update Permission for menu Kardeks Terbitan Berkala', NULL),
('1070', 'katalog/export_data_tag', 'katalog/export_data_tag', 'Daftar Katalog', 'Export data tag katalog ke Excel', NULL),
('1071', 'master-nama-sumber-perolehan/access', 'master-nama-sumber-perolehan', 'Nama Sumber Perolehan', 'Update Permission for menu Nama Sumber Perolehan', NULL),
('1072', 'master-nama-sumber-perolehan/index', 'master-nama-sumber-perolehan', 'Nama Sumber Perolehan', 'Update Permission for menu Nama Sumber Perolehan', NULL),
('1073', 'master-nama-sumber-perolehan/create', 'master-nama-sumber-perolehan', 'Nama Sumber Perolehan', 'Update Permission for menu Nama Sumber Perolehan', NULL),
('1074', 'master-nama-sumber-perolehan/edit', 'master-nama-sumber-perolehan', 'Nama Sumber Perolehan', 'Update Permission for menu Nama Sumber Perolehan', NULL),
('1075', 'master-nama-sumber-perolehan/delete', 'master-nama-sumber-perolehan', 'Nama Sumber Perolehan', 'Update Permission for menu Nama Sumber Perolehan', NULL),
('1076', 'pengaturan-opac/access', 'pengaturan-opac', 'Pengaturan Opac', 'Akses menu Pengaturan Opac', NULL),
('1077', 'master-opac-koleksi-unggulan/access', 'master-opac-koleksi-unggulan', 'Koleksi Unggulan', 'Permission access menu Koleksi Unggulan', NULL),
('1078', 'master-opac-koleksi-unggulan/index', 'master-opac-koleksi-unggulan', 'Koleksi Unggulan', 'Permission index menu Koleksi Unggulan', NULL),
('1079', 'master-opac-koleksi-unggulan/create', 'master-opac-koleksi-unggulan', 'Koleksi Unggulan', 'Permission create menu Koleksi Unggulan', NULL),
('1080', 'master-opac-koleksi-unggulan/edit', 'master-opac-koleksi-unggulan', 'Koleksi Unggulan', 'Permission edit menu Koleksi Unggulan', NULL),
('1081', 'master-opac-koleksi-unggulan/delete', 'master-opac-koleksi-unggulan', 'Koleksi Unggulan', 'Permission delete menu Koleksi Unggulan', NULL),
('1082', 'master-opac-koleksi-terbaru/access', 'master-opac-koleksi-terbaru', 'Koleksi Terbaru', 'Permission access menu Koleksi Terbaru', NULL),
('1083', 'master-opac-koleksi-terbaru/index', 'master-opac-koleksi-terbaru', 'Koleksi Terbaru', 'Permission index menu Koleksi Terbaru', NULL),
('1084', 'master-opac-koleksi-terbaru/create', 'master-opac-koleksi-terbaru', 'Koleksi Terbaru', 'Permission create menu Koleksi Terbaru', NULL),
('1085', 'master-opac-koleksi-terbaru/edit', 'master-opac-koleksi-terbaru', 'Koleksi Terbaru', 'Permission edit menu Koleksi Terbaru', NULL),
('1086', 'master-opac-koleksi-terbaru/delete', 'master-opac-koleksi-terbaru', 'Koleksi Terbaru', 'Permission delete menu Koleksi Terbaru', NULL),
('1087', 'master-opac-koleksi-sering-dipinjam/access', 'master-opac-koleksi-sering-dipinjam', 'Koleksi Sering Dipinjam', 'Permission access menu Koleksi Sering Dipinjam', NULL),
('1088', 'master-opac-koleksi-sering-dipinjam/index', 'master-opac-koleksi-sering-dipinjam', 'Koleksi Sering Dipinjam', 'Permission index menu Koleksi Sering Dipinjam', NULL),
('1089', 'master-opac-koleksi-sering-dipinjam/create', 'master-opac-koleksi-sering-dipinjam', 'Koleksi Sering Dipinjam', 'Permission create menu Koleksi Sering Dipinjam', NULL),
('1090', 'master-opac-koleksi-sering-dipinjam/edit', 'master-opac-koleksi-sering-dipinjam', 'Koleksi Sering Dipinjam', 'Permission edit menu Koleksi Sering Dipinjam', NULL),
('1091', 'master-opac-koleksi-sering-dipinjam/delete', 'master-opac-koleksi-sering-dipinjam', 'Koleksi Sering Dipinjam', 'Permission delete menu Koleksi Sering Dipinjam', NULL),
('1092', 'master-opac-booking/access', 'master-opac-booking', 'Pemesanan Koleksi', 'Permission access menu Pemesanan Koleksi', NULL),
('1093', 'master-opac-booking/index', 'master-opac-booking', 'Pemesanan Koleksi', 'Permission index menu Pemesanan Koleksi', NULL),
('1094', 'master-opac-booking/create', 'master-opac-booking', 'Pemesanan Koleksi', 'Permission create menu Pemesanan Koleksi', NULL),
('1095', 'master-opac-booking/edit', 'master-opac-booking', 'Pemesanan Koleksi', 'Permission edit menu Pemesanan Koleksi', NULL),
('1096', 'master-opac-booking/delete', 'master-opac-booking', 'Pemesanan Koleksi', 'Permission delete menu Pemesanan Koleksi', NULL),
('1097', 'master-opac-usulan-koleksi/access', 'master-opac-usulan-koleksi', 'Usulan Koleksi', 'Permission access menu Usulan Koleksi', NULL),
('1098', 'master-opac-usulan-koleksi/index', 'master-opac-usulan-koleksi', 'Usulan Koleksi', 'Permission index menu Usulan Koleksi', NULL),
('1099', 'master-opac-usulan-koleksi/create', 'master-opac-usulan-koleksi', 'Usulan Koleksi', 'Permission create menu Usulan Koleksi', NULL),
('1100', 'master-opac-usulan-koleksi/edit', 'master-opac-usulan-koleksi', 'Usulan Koleksi', 'Permission edit menu Usulan Koleksi', NULL),
('1101', 'master-opac-usulan-koleksi/delete', 'master-opac-usulan-koleksi', 'Usulan Koleksi', 'Permission delete menu Usulan Koleksi', NULL),
('1107', 'master-opac-histori-pencarian/access', 'master-opac-histori-pencarian', 'Histori Pencarian', 'Permission access menu Histori Pencarian', NULL),
('1108', 'master-opac-histori-pencarian/index', 'master-opac-histori-pencarian', 'Histori Pencarian', 'Permission index menu Histori Pencarian', NULL),
('1109', 'master-opac-histori-pencarian/create', 'master-opac-histori-pencarian', 'Histori Pencarian', 'Permission create menu Histori Pencarian', NULL),
('1110', 'master-opac-histori-pencarian/edit', 'master-opac-histori-pencarian', 'Histori Pencarian', 'Permission edit menu Histori Pencarian', NULL),
('1111', 'master-opac-histori-pencarian/delete', 'master-opac-histori-pencarian', 'Histori Pencarian', 'Permission delete menu Histori Pencarian', NULL),
('1112', 'master-pengaturan-frontend/access', 'master-pengaturan-frontend', 'Cache & Halaman Frontend', 'Permission access menu Cache & Halaman Frontend', NULL),
('1113', 'master-pengaturan-frontend/index', 'master-pengaturan-frontend', 'Cache & Halaman Frontend', 'Permission index menu Cache & Halaman Frontend', NULL),
('1114', 'master-pengaturan-frontend/create', 'master-pengaturan-frontend', 'Cache & Halaman Frontend', 'Permission create menu Cache & Halaman Frontend', NULL),
('1115', 'master-pengaturan-frontend/edit', 'master-pengaturan-frontend', 'Cache & Halaman Frontend', 'Permission edit menu Cache & Halaman Frontend', NULL),
('1116', 'master-pengaturan-frontend/delete', 'master-pengaturan-frontend', 'Cache & Halaman Frontend', 'Permission delete menu Cache & Halaman Frontend', NULL),
('1117', 'export-eksemplar/access', 'export-eksemplar', 'Export Excel', 'Permission access menu Export Excel Eksemplar', NULL),
('1118', 'export-eksemplar/index', 'export-eksemplar', 'Export Excel', 'Permission index menu Export Excel Eksemplar', NULL),
('1119', 'export-eksemplar/export', 'export-eksemplar', 'Export Excel', 'Permission export menu Export Excel Eksemplar', NULL),
('1120', 'export-eksemplar/download', 'export-eksemplar', 'Export Excel', 'Permission download menu Export Excel Eksemplar', NULL),
('1121', 'bukutamu/non_anggota', 'bukutamu/non_anggota', 'Buku Tamu', '', NULL),
('1122', 'bukutamu/rombongan', 'bukutamu/rombongan', 'Buku Tamu', '', NULL),
('1123', 'master-opac-baca-ditempat/access', 'master-opac-baca-ditempat', 'Koleksi Sering Baca di Tempat', 'Permission access menu Baca di Tempat', NULL),
('1124', 'master-opac-baca-ditempat/index', 'master-opac-baca-ditempat', 'Koleksi Sering Baca di Tempat', 'Permission index menu Baca di Tempat', NULL),
('1125', 'master-opac-baca-ditempat/create', 'master-opac-baca-ditempat', 'Koleksi Sering Baca di Tempat', 'Permission create menu Baca di Tempat', NULL),
('1126', 'master-opac-baca-ditempat/edit', 'master-opac-baca-ditempat', 'Koleksi Sering Baca di Tempat', 'Permission edit menu Baca di Tempat', NULL),
('1127', 'master-opac-baca-ditempat/delete', 'master-opac-baca-ditempat', 'Koleksi Sering Baca di Tempat', 'Permission delete menu Baca di Tempat', NULL),
('1128', 'cms/perpustakaan-keliling/access', 'cms/perpustakaan-keliling', 'Perpustakaan Keliling', 'Akses menu Perpustakaan Keliling', NULL),
('1129', 'cms/perpustakaan-keliling/jadwal', 'cms/perpustakaan-keliling', 'Jadwal Layanan', 'Permission menu Jadwal Layanan', NULL),
('1130', 'cms/perpustakaan-keliling/jadwal/access', 'cms/perpustakaan-keliling', 'Jadwal Layanan', 'Permission access menu Jadwal Layanan', NULL),
('1131', 'cms/perpustakaan-keliling/jadwal/index', 'cms/perpustakaan-keliling', 'Jadwal Layanan', 'Permission index menu Jadwal Layanan', NULL),
('1132', 'cms/perpustakaan-keliling/jadwal/create', 'cms/perpustakaan-keliling', 'Jadwal Layanan', 'Permission create menu Jadwal Layanan', NULL),
('1133', 'cms/perpustakaan-keliling/jadwal/edit', 'cms/perpustakaan-keliling', 'Jadwal Layanan', 'Permission edit menu Jadwal Layanan', NULL),
('1134', 'cms/perpustakaan-keliling/jadwal/delete', 'cms/perpustakaan-keliling', 'Jadwal Layanan', 'Permission delete menu Jadwal Layanan', NULL),
('1135', 'cms/perpustakaan-keliling/unit', 'cms/perpustakaan-keliling', 'Unit Kendaraan', 'Permission menu Unit Kendaraan', NULL),
('1136', 'cms/perpustakaan-keliling/unit/access', 'cms/perpustakaan-keliling', 'Unit Kendaraan', 'Permission access menu Unit Kendaraan', NULL),
('1137', 'cms/perpustakaan-keliling/unit/index', 'cms/perpustakaan-keliling', 'Unit Kendaraan', 'Permission index menu Unit Kendaraan', NULL),
('1138', 'cms/perpustakaan-keliling/unit/create', 'cms/perpustakaan-keliling', 'Unit Kendaraan', 'Permission create menu Unit Kendaraan', NULL),
('1139', 'cms/perpustakaan-keliling/unit/edit', 'cms/perpustakaan-keliling', 'Unit Kendaraan', 'Permission edit menu Unit Kendaraan', NULL),
('1140', 'cms/perpustakaan-keliling/unit/delete', 'cms/perpustakaan-keliling', 'Unit Kendaraan', 'Permission delete menu Unit Kendaraan', NULL),
('1141', 'cms/perpustakaan-keliling/lokasi', 'cms/perpustakaan-keliling', 'Lokasi Layanan', 'Permission menu Lokasi Layanan', NULL),
('1142', 'cms/perpustakaan-keliling/lokasi/access', 'cms/perpustakaan-keliling', 'Lokasi Layanan', 'Permission access menu Lokasi Layanan', NULL),
('1143', 'cms/perpustakaan-keliling/lokasi/index', 'cms/perpustakaan-keliling', 'Lokasi Layanan', 'Permission index menu Lokasi Layanan', NULL),
('1144', 'cms/perpustakaan-keliling/lokasi/create', 'cms/perpustakaan-keliling', 'Lokasi Layanan', 'Permission create menu Lokasi Layanan', NULL),
('1145', 'cms/perpustakaan-keliling/lokasi/edit', 'cms/perpustakaan-keliling', 'Lokasi Layanan', 'Permission edit menu Lokasi Layanan', NULL),
('1146', 'cms/perpustakaan-keliling/lokasi/delete', 'cms/perpustakaan-keliling', 'Lokasi Layanan', 'Permission delete menu Lokasi Layanan', NULL),
('1147', 'cms/perpustakaan-keliling/petugas', 'cms/perpustakaan-keliling', 'Petugas', 'Permission menu Petugas', NULL),
('1148', 'cms/perpustakaan-keliling/petugas/access', 'cms/perpustakaan-keliling', 'Petugas', 'Permission access menu Petugas', NULL),
('1149', 'cms/perpustakaan-keliling/petugas/index', 'cms/perpustakaan-keliling', 'Petugas', 'Permission index menu Petugas', NULL);
INSERT INTO `auth_permissions` (`id`, `name`, `route`, `menu`, `description`, `category`) VALUES
('1150', 'cms/perpustakaan-keliling/petugas/create', 'cms/perpustakaan-keliling', 'Petugas', 'Permission create menu Petugas', NULL),
('1151', 'cms/perpustakaan-keliling/petugas/edit', 'cms/perpustakaan-keliling', 'Petugas', 'Permission edit menu Petugas', NULL),
('1152', 'cms/perpustakaan-keliling/petugas/delete', 'cms/perpustakaan-keliling', 'Petugas', 'Permission delete menu Petugas', NULL),
('1153', 'cms/perpustakaan-keliling/dokumentasi', 'cms/perpustakaan-keliling', 'Jadwal Layanan', 'Permission dokumentasi kegiatan Perpustakaan Keliling', NULL),
('1154', 'master-opac-logs/access', 'master-opac-logs', 'Log Pencarian OPAC', 'Permission access menu Log Pencarian OPAC', NULL),
('1155', 'master-opac-logs/index', 'master-opac-logs', 'Log Pencarian OPAC', 'Permission index menu Log Pencarian OPAC', NULL),
('1156', 'master-opac-logs/create', 'master-opac-logs', 'Log Pencarian OPAC', 'Permission create menu Log Pencarian OPAC', NULL),
('1157', 'master-opac-logs/edit', 'master-opac-logs', 'Log Pencarian OPAC', 'Permission edit menu Log Pencarian OPAC', NULL),
('1158', 'master-opac-logs/delete', 'master-opac-logs', 'Log Pencarian OPAC', 'Permission delete menu Log Pencarian OPAC', NULL),
('1159', 'daftar-artikel/access', 'daftar-artikel', 'Daftar Artikel', 'Permission access menu Daftar Artikel', NULL),
('1160', 'daftar-artikel/index', 'daftar-artikel', 'Daftar Artikel', 'Permission index menu Daftar Artikel', NULL),
('1161', 'sirkulasi-peminjaman/edit', 'sirkulasi-peminjaman', 'Peminjaman', 'Permission aksi tulis API Sirkulasi Peminjaman', NULL),
('1162', 'sirkulasi-pengembalian/edit', 'sirkulasi-pengembalian', 'Pengembalian', 'Permission aksi tulis API Sirkulasi Pengembalian', NULL),
('1163', 'sirkulasi-pengembalian/delete', 'sirkulasi-pengembalian', 'Pengembalian', 'Permission aksi tulis API Sirkulasi Pengembalian', NULL),
('1164', 'katalog/access', 'katalog/access', 'Daftar Katalog', '', NULL),
('1165', 'katalog/index', 'katalog/index', 'Daftar Katalog', '', NULL),
('1166', 'eksemplar/index', 'eksemplar/index', 'Daftar Eksemplar', '', NULL),
('1167', 'eksemplar/access', 'eksemplar/access', 'Daftar Eksemplar', '', NULL),
('1168', 'keranjang-katalog/access', 'keranjang-katalog', 'Keranjang Katalog', 'Permission access menu Keranjang Katalog', NULL),
('1169', 'keranjang-katalog/index', 'keranjang-katalog', 'Keranjang Katalog', 'Permission index menu Keranjang Katalog', NULL),
('1170', 'keranjang-katalog/masukkan', 'keranjang-katalog', 'Keranjang Katalog', 'Permission masukkan menu Keranjang Katalog', NULL),
('1171', 'keranjang-katalog/kembalikan', 'keranjang-katalog', 'Keranjang Katalog', 'Permission kembalikan menu Keranjang Katalog', NULL),
('1172', 'keranjang-eksemplar/access', 'keranjang-eksemplar', 'Keranjang Eksemplar', 'Permission access menu Keranjang Eksemplar', NULL),
('1173', 'keranjang-eksemplar/index', 'keranjang-eksemplar', 'Keranjang Eksemplar', 'Permission index menu Keranjang Eksemplar', NULL),
('1174', 'keranjang-eksemplar/masukkan', 'keranjang-eksemplar', 'Keranjang Eksemplar', 'Permission masukkan menu Keranjang Eksemplar', NULL),
('1175', 'keranjang-eksemplar/kembalikan', 'keranjang-eksemplar', 'Keranjang Eksemplar', 'Permission kembalikan menu Keranjang Eksemplar', NULL),
('1176', 'opac/baca-digital', 'opac/baca-digital', 'OPAC', 'Pinjam dan baca buku digital dari OPAC', NULL),
('1177', 'eksemplar/edit', 'eksemplar/edit', 'Daftar Eksemplar', '', NULL),
('1178', 'katalog/edit', 'katalog/edit', 'Daftar Katalog', '', NULL),
('1179', 'master-kios-buku-tamu/access', 'master-kios-buku-tamu', 'Aktivasi Kios', 'Permission access menu Aktivasi Kios', NULL),
('1180', 'master-kios-buku-tamu/index', 'master-kios-buku-tamu', 'Aktivasi Kios', 'Permission index menu Aktivasi Kios', NULL),
('1181', 'master-kios-buku-tamu/create', 'master-kios-buku-tamu', 'Aktivasi Kios', 'Permission create menu Aktivasi Kios', NULL),
('1182', 'master-kios-buku-tamu/delete', 'master-kios-buku-tamu', 'Aktivasi Kios', 'Permission delete menu Aktivasi Kios', NULL);

-- auth_groups_permissions
INSERT INTO `auth_groups_permissions` (`group_id`, `permission_id`, `category`, `branch_id`) VALUES
('1', '362', '0', '0'),
('1', '363', '0', '0'),
('1', '364', '0', '0'),
('1', '365', '0', '0'),
('1', '366', '0', '0'),
('1', '372', '0', '0'),
('1', '373', '0', '0'),
('1', '384', '0', '0'),
('1', '385', '0', '0'),
('1', '386', '0', '0'),
('1', '387', '0', '0'),
('1', '390', '0', '0'),
('1', '391', '0', '0'),
('1', '392', '0', '0'),
('1', '393', '0', '0'),
('1', '394', '0', '0'),
('1', '395', '0', '0'),
('1', '396', '0', '0'),
('1', '397', '0', '0'),
('1', '400', '0', '0'),
('1', '401', '0', '0'),
('1', '402', '0', '0'),
('1', '403', '0', '0'),
('1', '404', '0', '0'),
('1', '405', '0', '0'),
('1', '406', '0', '0'),
('1', '407', '0', '0'),
('1', '408', '0', '0'),
('1', '1183', '0', '0'),
('1', '1184', '0', '0'),
('1', '1185', '0', '0'),
('1', '1186', '0', '0'),
('1', '409', '0', '0'),
('1', '410', '0', '0'),
('1', '411', '0', '0'),
('1', '412', '0', '0'),
('1', '413', '0', '0'),
('1', '414', '0', '0'),
('1', '415', '0', '0'),
('1', '416', '0', '0'),
('1', '417', '0', '0'),
('1', '418', '0', '0'),
('1', '419', '0', '0'),
('1', '420', '0', '0'),
('1', '422', '0', '0'),
('1', '423', '0', '0'),
('1', '424', '0', '0'),
('1', '425', '0', '0'),
('1', '426', '0', '0'),
('1', '427', '0', '0'),
('1', '428', '0', '0'),
('1', '429', '0', '0'),
('1', '430', '0', '0'),
('1', '431', '0', '0'),
('1', '432', '0', '0'),
('1', '433', '0', '0'),
('1', '434', '0', '0'),
('1', '435', '0', '0'),
('1', '436', '0', '0'),
('1', '437', '0', '0'),
('1', '438', '0', '0'),
('1', '439', '0', '0'),
('1', '440', '0', '0'),
('1', '441', '0', '0'),
('1', '442', '0', '0'),
('1', '448', '0', '0'),
('1', '479', '0', '0'),
('1', '480', '0', '0'),
('1', '481', '0', '0'),
('1', '482', '0', '0'),
('1', '483', '0', '0'),
('1', '495', '0', '0'),
('1', '496', '0', '0'),
('1', '497', '0', '0'),
('1', '498', '0', '0'),
('1', '499', '0', '0'),
('1', '500', '0', '0'),
('1', '501', '0', '0'),
('1', '502', '0', '0'),
('1', '503', '0', '0'),
('1', '504', '0', '0'),
('1', '505', '0', '0'),
('1', '506', '0', '0'),
('1', '507', '0', '0'),
('1', '508', '0', '0'),
('1', '509', '0', '0'),
('1', '511', '0', '0'),
('1', '512', '0', '0'),
('1', '513', '0', '0'),
('1', '514', '0', '0'),
('1', '515', '0', '0'),
('1', '516', '0', '0'),
('1', '517', '0', '0'),
('1', '518', '0', '0'),
('1', '519', '0', '0'),
('1', '520', '0', '0'),
('1', '526', '0', '0'),
('1', '527', '0', '0'),
('1', '528', '0', '0'),
('1', '529', '0', '0'),
('1', '530', '0', '0'),
('1', '531', '0', '0'),
('1', '532', '0', '0'),
('1', '533', '0', '0'),
('1', '534', '0', '0'),
('1', '535', '0', '0'),
('1', '536', '0', '0'),
('1', '537', '0', '0'),
('1', '538', '0', '0'),
('1', '539', '0', '0'),
('1', '540', '0', '0'),
('1', '542', '0', '0'),
('1', '543', '0', '0'),
('1', '544', '0', '0'),
('1', '545', '0', '0'),
('1', '546', '0', '0'),
('1', '548', '0', '0'),
('1', '550', '0', '0'),
('1', '551', '0', '0'),
('1', '552', '0', '0'),
('1', '553', '0', '0'),
('1', '554', '0', '0'),
('1', '556', '0', '0'),
('1', '557', '0', '0'),
('1', '574', '0', '0'),
('1', '575', '0', '0'),
('1', '576', '0', '0'),
('1', '577', '0', '0'),
('1', '578', '0', '0'),
('1', '579', '0', '0'),
('1', '580', '0', '0'),
('1', '581', '0', '0'),
('1', '582', '0', '0'),
('1', '583', '0', '0'),
('1', '584', '0', '0'),
('1', '585', '0', '0'),
('1', '586', '0', '0'),
('1', '587', '0', '0'),
('1', '588', '0', '0'),
('1', '589', '0', '0'),
('1', '590', '0', '0'),
('1', '591', '0', '0'),
('1', '592', '0', '0'),
('1', '593', '0', '0'),
('1', '594', '0', '0'),
('1', '595', '0', '0'),
('1', '596', '0', '0'),
('1', '597', '0', '0'),
('1', '598', '0', '0'),
('1', '604', '0', '0'),
('1', '605', '0', '0'),
('1', '606', '0', '0'),
('1', '607', '0', '0'),
('1', '608', '0', '0'),
('1', '609', '0', '0'),
('1', '610', '0', '0'),
('1', '611', '0', '0'),
('1', '612', '0', '0'),
('1', '613', '0', '0'),
('1', '619', '0', '0'),
('1', '620', '0', '0'),
('1', '621', '0', '0'),
('1', '622', '0', '0'),
('1', '623', '0', '0'),
('1', '625', '0', '0'),
('1', '626', '0', '0'),
('1', '627', '0', '0'),
('1', '628', '0', '0'),
('1', '629', '0', '0'),
('1', '630', '0', '0'),
('1', '631', '0', '0'),
('1', '632', '0', '0'),
('1', '633', '0', '0'),
('1', '634', '0', '0'),
('1', '635', '0', '0'),
('1', '636', '0', '0'),
('1', '637', '0', '0'),
('1', '638', '0', '0'),
('1', '639', '0', '0'),
('1', '640', '0', '0'),
('1', '641', '0', '0'),
('1', '642', '0', '0'),
('1', '643', '0', '0'),
('1', '644', '0', '0'),
('1', '645', '0', '0'),
('1', '646', '0', '0'),
('1', '647', '0', '0'),
('1', '648', '0', '0'),
('1', '649', '0', '0'),
('1', '650', '0', '0'),
('1', '651', '0', '0'),
('1', '652', '0', '0'),
('1', '653', '0', '0'),
('1', '654', '0', '0'),
('1', '655', '0', '0'),
('1', '656', '0', '0'),
('1', '657', '0', '0'),
('1', '658', '0', '0'),
('1', '659', '0', '0'),
('1', '660', '0', '0'),
('1', '661', '0', '0'),
('1', '662', '0', '0'),
('1', '663', '0', '0'),
('1', '664', '0', '0');
INSERT INTO `auth_groups_permissions` (`group_id`, `permission_id`, `category`, `branch_id`) VALUES
('1', '671', '0', '0'),
('1', '672', '0', '0'),
('1', '673', '0', '0'),
('1', '674', '0', '0'),
('1', '675', '0', '0'),
('1', '676', '0', '0'),
('1', '677', '0', '0'),
('1', '678', '0', '0'),
('1', '679', '0', '0'),
('1', '680', '0', '0'),
('1', '681', '0', '0'),
('1', '682', '0', '0'),
('1', '683', '0', '0'),
('1', '684', '0', '0'),
('1', '685', '0', '0'),
('1', '686', '0', '0'),
('1', '687', '0', '0'),
('1', '688', '0', '0'),
('1', '689', '0', '0'),
('1', '690', '0', '0'),
('1', '691', '0', '0'),
('1', '692', '0', '0'),
('1', '693', '0', '0'),
('1', '694', '0', '0'),
('1', '695', '0', '0'),
('1', '696', '0', '0'),
('1', '697', '0', '0'),
('1', '698', '0', '0'),
('1', '699', '0', '0'),
('1', '700', '0', '0'),
('1', '701', '0', '0'),
('1', '702', '0', '0'),
('1', '703', '0', '0'),
('1', '704', '0', '0'),
('1', '705', '0', '0'),
('1', '706', '0', '0'),
('1', '712', '0', '0'),
('1', '713', '0', '0'),
('1', '714', '0', '0'),
('1', '715', '0', '0'),
('1', '716', '0', '0'),
('1', '717', '0', '0'),
('1', '718', '0', '0'),
('1', '719', '0', '0'),
('1', '720', '0', '0'),
('1', '721', '0', '0'),
('1', '722', '0', '0'),
('1', '723', '0', '0'),
('1', '724', '0', '0'),
('1', '725', '0', '0'),
('1', '726', '0', '0'),
('1', '727', '0', '0'),
('1', '728', '0', '0'),
('1', '729', '0', '0'),
('1', '730', '0', '0'),
('1', '731', '0', '0'),
('1', '732', '0', '0'),
('1', '733', '0', '0'),
('1', '734', '0', '0'),
('1', '735', '0', '0'),
('1', '736', '0', '0'),
('1', '743', '0', '0'),
('1', '744', '0', '0'),
('1', '745', '0', '0'),
('1', '746', '0', '0'),
('1', '747', '0', '0'),
('1', '748', '0', '0'),
('1', '749', '0', '0'),
('1', '750', '0', '0'),
('1', '751', '0', '0'),
('1', '752', '0', '0'),
('1', '753', '0', '0'),
('1', '754', '0', '0'),
('1', '755', '0', '0'),
('1', '756', '0', '0'),
('1', '757', '0', '0'),
('1', '758', '0', '0'),
('1', '759', '0', '0'),
('1', '760', '0', '0'),
('1', '776', '0', '0'),
('1', '777', '0', '0'),
('1', '778', '0', '0'),
('1', '779', '0', '0'),
('1', '780', '0', '0'),
('1', '781', '0', '0'),
('1', '782', '0', '0'),
('1', '783', '0', '0'),
('1', '784', '0', '0'),
('1', '785', '0', '0'),
('1', '791', '0', '0'),
('1', '792', '0', '0'),
('1', '793', '0', '0'),
('1', '794', '0', '0'),
('1', '795', '0', '0'),
('1', '796', '0', '0'),
('1', '797', '0', '0'),
('1', '798', '0', '0'),
('1', '799', '0', '0'),
('1', '800', '0', '0'),
('1', '801', '0', '0'),
('1', '802', '0', '0'),
('1', '803', '0', '0'),
('1', '804', '0', '0'),
('1', '805', '0', '0'),
('1', '806', '0', '0'),
('1', '807', '0', '0'),
('1', '808', '0', '0'),
('1', '809', '0', '0'),
('1', '810', '0', '0'),
('1', '811', '0', '0'),
('1', '812', '0', '0'),
('1', '813', '0', '0'),
('1', '814', '0', '0'),
('1', '815', '0', '0'),
('1', '816', '0', '0'),
('1', '817', '0', '0'),
('1', '818', '0', '0'),
('1', '819', '0', '0'),
('1', '820', '0', '0'),
('1', '826', '0', '0'),
('1', '827', '0', '0'),
('1', '828', '0', '0'),
('1', '829', '0', '0'),
('1', '830', '0', '0'),
('1', '831', '0', '0'),
('1', '832', '0', '0'),
('1', '833', '0', '0'),
('1', '834', '0', '0'),
('1', '835', '0', '0'),
('1', '847', '0', '0'),
('1', '848', '0', '0'),
('1', '849', '0', '0'),
('1', '850', '0', '0'),
('1', '851', '0', '0'),
('1', '852', '0', '0'),
('1', '853', '0', '0'),
('1', '854', '0', '0'),
('1', '855', '0', '0'),
('1', '856', '0', '0'),
('1', '857', '0', '0'),
('1', '858', '0', '0'),
('1', '859', '0', '0'),
('1', '860', '0', '0'),
('1', '862', '0', '0'),
('1', '863', '0', '0'),
('1', '864', '0', '0'),
('1', '865', '0', '0'),
('1', '866', '0', '0'),
('1', '867', '0', '0'),
('1', '868', '0', '0'),
('1', '869', '0', '0'),
('1', '870', '0', '0'),
('1', '871', '0', '0'),
('1', '872', '0', '0'),
('1', '873', '0', '0'),
('1', '874', '0', '0'),
('1', '875', '0', '0'),
('1', '876', '0', '0'),
('1', '877', '0', '0'),
('1', '879', '0', '0'),
('1', '880', '0', '0'),
('1', '881', '0', '0'),
('1', '882', '0', '0'),
('1', '883', '0', '0'),
('1', '885', '0', '0'),
('1', '887', '0', '0'),
('1', '888', '0', '0'),
('1', '889', '0', '0'),
('1', '890', '0', '0'),
('1', '891', '0', '0'),
('1', '892', '0', '0'),
('1', '893', '0', '0'),
('1', '894', '0', '0'),
('1', '895', '0', '0'),
('1', '896', '0', '0'),
('1', '897', '0', '0'),
('1', '898', '0', '0'),
('1', '904', '0', '0'),
('1', '905', '0', '0'),
('1', '906', '0', '0'),
('1', '907', '0', '0'),
('1', '908', '0', '0'),
('1', '909', '0', '0'),
('1', '911', '0', '0'),
('1', '912', '0', '0'),
('1', '913', '0', '0'),
('1', '914', '0', '0'),
('1', '915', '0', '0'),
('1', '916', '0', '0'),
('1', '917', '0', '0'),
('1', '918', '0', '0'),
('1', '919', '0', '0'),
('1', '920', '0', '0'),
('1', '921', '0', '0'),
('1', '922', '0', '0'),
('1', '923', '0', '0'),
('1', '924', '0', '0'),
('1', '925', '0', '0'),
('1', '931', '0', '0'),
('1', '932', '0', '0');
INSERT INTO `auth_groups_permissions` (`group_id`, `permission_id`, `category`, `branch_id`) VALUES
('1', '935', '0', '0'),
('1', '936', '0', '0'),
('1', '937', '0', '0'),
('1', '938', '0', '0'),
('1', '939', '0', '0'),
('1', '940', '0', '0'),
('1', '941', '0', '0'),
('1', '942', '0', '0'),
('1', '943', '0', '0'),
('1', '944', '0', '0'),
('1', '945', '0', '0'),
('1', '946', '0', '0'),
('1', '947', '0', '0'),
('1', '948', '0', '0'),
('1', '949', '0', '0'),
('1', '950', '0', '0'),
('1', '951', '0', '0'),
('1', '952', '0', '0'),
('1', '953', '0', '0'),
('1', '954', '0', '0'),
('1', '955', '0', '0'),
('1', '956', '0', '0'),
('1', '957', '0', '0'),
('1', '963', '0', '0'),
('1', '964', '0', '0'),
('1', '965', '0', '0'),
('1', '967', '0', '0'),
('1', '970', '0', '0'),
('1', '972', '0', '0'),
('1', '973', '0', '0'),
('1', '976', '0', '0'),
('1', '977', '0', '0'),
('1', '979', '0', '0'),
('1', '981', '0', '0'),
('1', '983', '0', '0'),
('1', '984', '0', '0'),
('1', '985', '0', '0'),
('1', '987', '0', '0'),
('1', '989', '0', '0'),
('1', '990', '0', '0'),
('1', '991', '0', '0'),
('1', '992', '0', '0'),
('1', '994', '0', '0'),
('1', '995', '0', '0'),
('1', '996', '0', '0'),
('1', '997', '0', '0'),
('1', '998', '0', '0'),
('1', '999', '0', '0'),
('1', '1000', '0', '0'),
('1', '1001', '0', '0'),
('1', '1002', '0', '0'),
('1', '1003', '0', '0'),
('1', '1004', '0', '0'),
('1', '1005', '0', '0'),
('1', '1006', '0', '0'),
('1', '1007', '0', '0'),
('1', '1008', '0', '0'),
('1', '1009', '0', '0'),
('1', '1010', '0', '0'),
('1', '1011', '0', '0'),
('1', '1012', '0', '0'),
('1', '1013', '0', '0'),
('1', '1014', '0', '0'),
('1', '1015', '0', '0'),
('1', '1016', '0', '0'),
('1', '1017', '0', '0'),
('1', '1018', '0', '0'),
('1', '1019', '0', '0'),
('1', '1020', '0', '0'),
('1', '1021', '0', '0'),
('1', '1022', '0', '0'),
('1', '1023', '0', '0'),
('1', '1024', '0', '0'),
('1', '1025', '0', '0'),
('1', '1026', '0', '0'),
('1', '1027', '0', '0'),
('1', '1028', '0', '0'),
('1', '1029', '0', '0'),
('1', '1030', '0', '0'),
('1', '1031', '0', '0'),
('1', '1032', '0', '0'),
('1', '1033', '0', '0'),
('1', '1034', '0', '0'),
('1', '1035', '0', '0'),
('1', '1036', '0', '0'),
('1', '1037', '0', '0'),
('1', '1038', '0', '0'),
('1', '1039', '0', '0'),
('1', '1040', '0', '0'),
('1', '1041', '0', '0'),
('1', '1042', '0', '0'),
('1', '1043', '0', '0'),
('1', '1044', '0', '0'),
('1', '1045', '0', '0'),
('1', '1046', '0', '0'),
('1', '1047', '0', '0'),
('1', '1048', '0', '0'),
('1', '1049', '0', '0'),
('1', '1050', '0', '0'),
('1', '1051', '0', '0'),
('1', '1052', '0', '0'),
('1', '1053', '0', '0'),
('1', '1054', '0', '0'),
('1', '1055', '0', '0'),
('1', '1056', '0', '0'),
('1', '1057', '0', '0'),
('1', '1058', '0', '0'),
('1', '1059', '0', '0'),
('1', '1060', '0', '0'),
('1', '1061', '0', '0'),
('1', '1062', '0', '0'),
('1', '1063', '0', '0'),
('1', '1064', '0', '0'),
('1', '1065', '0', '0'),
('1', '1066', '0', '0'),
('1', '1067', '0', '0'),
('1', '1068', '0', '0'),
('1', '1069', '0', '0'),
('1', '1070', '0', '0'),
('1', '1071', '0', '0'),
('1', '1072', '0', '0'),
('1', '1073', '0', '0'),
('1', '1074', '0', '0'),
('1', '1075', '0', '0'),
('1', '1076', '0', '0'),
('1', '1077', '0', '0'),
('1', '1078', '0', '0'),
('1', '1079', '0', '0'),
('1', '1080', '0', '0'),
('1', '1081', '0', '0'),
('1', '1082', '0', '0'),
('1', '1083', '0', '0'),
('1', '1084', '0', '0'),
('1', '1085', '0', '0'),
('1', '1086', '0', '0'),
('1', '1087', '0', '0'),
('1', '1088', '0', '0'),
('1', '1089', '0', '0'),
('1', '1090', '0', '0'),
('1', '1091', '0', '0'),
('1', '1092', '0', '0'),
('1', '1093', '0', '0'),
('1', '1094', '0', '0'),
('1', '1095', '0', '0'),
('1', '1096', '0', '0'),
('1', '1097', '0', '0'),
('1', '1098', '0', '0'),
('1', '1099', '0', '0'),
('1', '1100', '0', '0'),
('1', '1101', '0', '0'),
('1', '1107', '0', '0'),
('1', '1108', '0', '0'),
('1', '1109', '0', '0'),
('1', '1110', '0', '0'),
('1', '1111', '0', '0'),
('1', '1112', '0', '0'),
('1', '1113', '0', '0'),
('1', '1114', '0', '0'),
('1', '1115', '0', '0'),
('1', '1116', '0', '0'),
('1', '1117', '0', '0'),
('1', '1118', '0', '0'),
('1', '1119', '0', '0'),
('1', '1120', '0', '0'),
('1', '1121', '0', '0'),
('1', '1122', '0', '0'),
('1', '1123', '0', '0'),
('1', '1124', '0', '0'),
('1', '1125', '0', '0'),
('1', '1126', '0', '0'),
('1', '1127', '0', '0'),
('1', '1128', '0', '0'),
('1', '1129', '0', '0'),
('1', '1130', '0', '0'),
('1', '1131', '0', '0'),
('1', '1132', '0', '0'),
('1', '1133', '0', '0'),
('1', '1134', '0', '0'),
('1', '1135', '0', '0'),
('1', '1136', '0', '0'),
('1', '1137', '0', '0'),
('1', '1138', '0', '0'),
('1', '1139', '0', '0'),
('1', '1140', '0', '0'),
('1', '1141', '0', '0'),
('1', '1142', '0', '0'),
('1', '1143', '0', '0'),
('1', '1144', '0', '0'),
('1', '1145', '0', '0'),
('1', '1146', '0', '0'),
('1', '1147', '0', '0'),
('1', '1148', '0', '0'),
('1', '1149', '0', '0'),
('1', '1150', '0', '0'),
('1', '1151', '0', '0'),
('1', '1152', '0', '0'),
('1', '1153', '0', '0'),
('1', '1154', '0', '0'),
('1', '1155', '0', '0'),
('1', '1156', '0', '0');
INSERT INTO `auth_groups_permissions` (`group_id`, `permission_id`, `category`, `branch_id`) VALUES
('1', '1157', '0', '0'),
('1', '1158', '0', '0'),
('1', '1159', '0', '0'),
('1', '1160', '0', '0'),
('1', '1161', '0', '0'),
('1', '1162', '0', '0'),
('1', '1163', '0', '0'),
('1', '1164', '0', '0'),
('1', '1165', '0', '0'),
('1', '1166', '0', '0'),
('1', '1167', '0', '0'),
('1', '1168', '0', '0'),
('1', '1169', '0', '0'),
('1', '1170', '0', '0'),
('1', '1171', '0', '0'),
('1', '1172', '0', '0'),
('1', '1173', '0', '0'),
('1', '1174', '0', '0'),
('1', '1175', '0', '0'),
('1', '1176', '0', '0'),
('1', '1177', '0', '0'),
('1', '1178', '0', '0'),
('1', '1179', '0', '0'),
('1', '1180', '0', '0'),
('1', '1181', '0', '0'),
('1', '1182', '0', '0'),
('8', '363', '0', '0'),
('8', '365', '0', '0'),
('8', '366', '0', '0'),
('8', '405', '0', '0'),
('8', '406', '0', '0'),
('8', '407', '0', '0'),
('8', '408', '0', '0'),
('8', '1183', '0', '0'),
('8', '1184', '0', '0'),
('8', '1185', '0', '0'),
('8', '1186', '0', '0'),
('8', '409', '0', '0'),
('8', '410', '0', '0'),
('8', '411', '0', '0'),
('8', '412', '0', '0'),
('8', '413', '0', '0'),
('8', '414', '0', '0'),
('8', '415', '0', '0'),
('8', '416', '0', '0'),
('8', '417', '0', '0'),
('8', '418', '0', '0'),
('8', '419', '0', '0'),
('8', '420', '0', '0'),
('8', '546', '0', '0'),
('8', '557', '0', '0'),
('8', '756', '0', '0'),
('8', '757', '0', '0'),
('8', '758', '0', '0'),
('8', '759', '0', '0'),
('8', '760', '0', '0'),
('8', '907', '0', '0'),
('8', '923', '0', '0'),
('8', '924', '0', '0'),
('8', '925', '0', '0'),
('8', '947', '0', '0'),
('8', '949', '0', '0'),
('8', '950', '0', '0'),
('8', '951', '0', '0'),
('8', '953', '0', '0'),
('8', '954', '0', '0'),
('8', '955', '0', '0'),
('8', '956', '0', '0'),
('8', '957', '0', '0'),
('8', '998', '0', '0'),
('8', '1000', '0', '0'),
('8', '1003', '0', '0'),
('8', '1025', '0', '0'),
('8', '1036', '0', '0'),
('10', '363', '0', '0'),
('10', '372', '0', '0'),
('10', '373', '0', '0'),
('10', '542', '0', '0'),
('10', '543', '0', '0'),
('10', '544', '0', '0'),
('10', '557', '0', '0'),
('10', '660', '0', '0'),
('10', '661', '0', '0'),
('10', '662', '0', '0'),
('10', '663', '0', '0'),
('10', '664', '0', '0'),
('10', '676', '0', '0'),
('10', '717', '0', '0'),
('10', '718', '0', '0'),
('10', '719', '0', '0'),
('10', '720', '0', '0'),
('10', '721', '0', '0'),
('10', '722', '0', '0'),
('10', '723', '0', '0'),
('10', '724', '0', '0'),
('10', '725', '0', '0'),
('10', '726', '0', '0'),
('10', '727', '0', '0'),
('10', '728', '0', '0'),
('10', '729', '0', '0'),
('10', '730', '0', '0'),
('10', '731', '0', '0'),
('10', '732', '0', '0'),
('10', '733', '0', '0'),
('10', '734', '0', '0'),
('10', '735', '0', '0'),
('10', '736', '0', '0'),
('10', '743', '0', '0'),
('10', '744', '0', '0'),
('10', '745', '0', '0'),
('10', '746', '0', '0'),
('10', '747', '0', '0'),
('10', '748', '0', '0'),
('10', '749', '0', '0'),
('10', '750', '0', '0'),
('10', '751', '0', '0'),
('10', '752', '0', '0'),
('10', '753', '0', '0'),
('10', '754', '0', '0'),
('10', '755', '0', '0'),
('10', '812', '0', '0'),
('10', '813', '0', '0'),
('10', '814', '0', '0'),
('10', '815', '0', '0'),
('10', '816', '0', '0'),
('10', '847', '0', '0'),
('10', '848', '0', '0'),
('10', '849', '0', '0'),
('10', '850', '0', '0'),
('10', '851', '0', '0'),
('10', '852', '0', '0'),
('10', '854', '0', '0'),
('10', '860', '0', '0'),
('10', '887', '0', '0'),
('10', '888', '0', '0'),
('10', '889', '0', '0'),
('10', '890', '0', '0'),
('10', '891', '0', '0'),
('10', '892', '0', '0'),
('10', '893', '0', '0'),
('10', '894', '0', '0'),
('10', '895', '0', '0'),
('10', '896', '0', '0'),
('10', '897', '0', '0'),
('10', '898', '0', '0'),
('10', '904', '0', '0'),
('10', '905', '0', '0'),
('10', '906', '0', '0'),
('10', '908', '0', '0'),
('10', '909', '0', '0'),
('10', '912', '0', '0'),
('10', '936', '0', '0'),
('10', '937', '0', '0'),
('10', '938', '0', '0'),
('10', '939', '0', '0'),
('10', '940', '0', '0'),
('10', '941', '0', '0'),
('10', '942', '0', '0'),
('10', '945', '0', '0'),
('10', '946', '0', '0'),
('10', '952', '0', '0'),
('10', '992', '0', '0'),
('10', '994', '0', '0'),
('10', '995', '0', '0'),
('10', '996', '0', '0'),
('10', '997', '0', '0'),
('10', '1001', '0', '0'),
('10', '1002', '0', '0'),
('10', '1037', '0', '0'),
('10', '1048', '0', '0'),
('10', '1049', '0', '0'),
('10', '1057', '0', '0'),
('10', '1068', '0', '0'),
('10', '1069', '0', '0'),
('10', '1070', '0', '0'),
('10', '1117', '0', '0'),
('10', '1118', '0', '0'),
('10', '1119', '0', '0'),
('10', '1120', '0', '0'),
('10', '1159', '0', '0'),
('10', '1160', '0', '0'),
('10', '1164', '0', '0'),
('10', '1165', '0', '0'),
('10', '1166', '0', '0'),
('10', '1167', '0', '0'),
('10', '1168', '0', '0'),
('10', '1169', '0', '0'),
('10', '1170', '0', '0'),
('10', '1171', '0', '0'),
('11', '384', '0', '0'),
('11', '385', '0', '0'),
('11', '386', '0', '0'),
('11', '387', '0', '0'),
('11', '390', '0', '0'),
('11', '391', '0', '0'),
('11', '392', '0', '0'),
('11', '393', '0', '0'),
('11', '394', '0', '0'),
('11', '395', '0', '0'),
('11', '396', '0', '0'),
('11', '397', '0', '0'),
('11', '400', '0', '0'),
('11', '401', '0', '0'),
('11', '402', '0', '0');
INSERT INTO `auth_groups_permissions` (`group_id`, `permission_id`, `category`, `branch_id`) VALUES
('11', '403', '0', '0'),
('11', '404', '0', '0'),
('11', '405', '0', '0'),
('11', '406', '0', '0'),
('11', '407', '0', '0'),
('11', '408', '0', '0'),
('11', '1183', '0', '0'),
('11', '409', '0', '0'),
('11', '410', '0', '0'),
('11', '411', '0', '0'),
('11', '412', '0', '0'),
('11', '413', '0', '0'),
('11', '414', '0', '0'),
('11', '415', '0', '0'),
('11', '545', '0', '0'),
('11', '546', '0', '0'),
('11', '970', '0', '0'),
('11', '972', '0', '0'),
('11', '973', '0', '0'),
('11', '1065', '0', '0'),
('11', '1066', '0', '0'),
('11', '1067', '0', '0'),
('14', '363', '0', '0'),
('14', '842', '0', '0'),
('14', '843', '0', '0'),
('14', '844', '0', '0'),
('14', '845', '0', '0'),
('14', '846', '0', '0'),
('14', '908', '0', '0'),
('14', '909', '0', '0'),
('14', '920', '0', '0'),
('14', '1176', '0', '0');

-- c_menus (menu backend)
INSERT INTO `c_menus` (`id`, `name`, `parent`, `controller`, `slug`, `icon`, `permission`, `file_image`, `type`, `category_id`, `category`, `sort`, `description`, `active`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
('1', 'Navigasi', '0', 'label-navigasi', 'label-navigasi', '', 'access', NULL, 'label', '1', 'backend-menu', '1', NULL, '1', NULL, NULL, '2021-08-04 07:47:59', '2026-05-28 12:44:23', NULL),
('2', 'Dashboard', '0', 'dashboard', 'dashboard', 'fas fa-tv', 'access|index', NULL, 'menu', '1', 'backend-menu', '2', NULL, '1', NULL, NULL, NULL, '2026-05-28 12:44:23', NULL),
('3', 'Laporan', '0', 'menu-laporan', 'menu-laporan', 'fas fa-archive', 'access', NULL, 'menu', '1', 'backend-menu', '31', NULL, '1', NULL, NULL, NULL, '2026-05-28 12:44:23', NULL),
('6', 'Otorisasi', '0', 'menu-otorisasi', 'menu-otorisasi', 'fas fa-user-lock', 'access', NULL, 'menu', '1', 'backend-menu', '85', NULL, '1', NULL, NULL, NULL, '2026-05-28 12:44:23', NULL),
('7', 'User', '6', 'user', 'user', 'fas fa-user-friends', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '88', NULL, '1', NULL, NULL, NULL, '2026-05-28 12:44:23', NULL),
('10', 'Permission', '6', 'permission', 'permission', 'fas fa-cogs', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '89', NULL, '1', NULL, NULL, NULL, '2026-05-28 12:44:23', NULL),
('11', 'Menu', '12', 'menu', 'menu', 'fab fa-readme', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '91', NULL, '1', NULL, NULL, NULL, '2026-05-28 12:44:23', NULL),
('12', 'Setelan', '0', 'menu-setelan', 'menu-setelan', 'fas fa-wrench', 'access', NULL, 'menu', '1', 'backend-menu', '90', NULL, '1', NULL, NULL, NULL, '2026-05-28 12:44:23', NULL),
('13', 'Parameter', '12', 'parameter', 'parameter', 'fas fa-scroll', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '92', NULL, '1', NULL, NULL, NULL, '2026-05-28 12:44:23', NULL),
('23', 'Keanggotaan', '0', 'menu-keanggotaan', 'menu-keanggotaan', 'far fa-id-card', 'access', NULL, 'menu', '1', 'backend-menu', '13', NULL, '1', NULL, NULL, '2021-08-04 09:35:25', '2026-05-28 12:44:23', NULL),
('51', 'Tag', '158', 'master-tag', 'master-tag', 'fas fa-folder', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '48', NULL, '1', NULL, NULL, '2022-03-10 10:46:59', '2026-05-28 12:44:23', NULL),
('52', 'Lokasi Ruang', '161', 'master-lokasi-ruang', 'master-lokasi-ruang', 'fas fa-door-closed', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '43', NULL, '1', NULL, NULL, '2022-03-10 10:47:14', '2026-05-28 12:44:23', NULL),
('53', 'Sirkulasi', '0', 'menu-sirkulasi', 'menu-sirkulasi', 'fas fa-sync', 'access', NULL, 'menu', '1', 'backend-menu', '20', NULL, '1', NULL, NULL, '2022-03-10 10:47:47', '2026-05-28 12:44:23', NULL),
('54', 'Lokasi Perpustakaan', '161', 'master-lokasi-perpustakaan', 'master-lokasi-perpustakaan', 'fas fa-hospital', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '42', NULL, '1', NULL, NULL, '2022-03-10 10:48:37', '2026-05-28 12:44:23', NULL),
('55', 'Administrasi', '0', 'menu-administrasi', 'menu-administrasi', 'pe-7s-server', 'access', NULL, 'label', '1', 'backend-menu', '39', NULL, '1', NULL, NULL, '2022-03-10 10:48:55', '2026-05-28 12:44:23', NULL),
('59', 'Entri Eksemplar', '59', 'eksemplar/create', 'menu-entri-eksemplar', 'pe-7s-albums', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '11', NULL, '1', NULL, NULL, '2022-03-10 10:51:33', '2025-03-09 07:47:42', NULL),
('60', 'Jenis Anggota', '159', 'master-jenis-anggota', 'master-jenis-anggota', 'fas fa-venus-mars', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '67', NULL, '1', NULL, NULL, '2022-03-10 10:52:05', '2026-05-28 12:44:23', NULL),
('73', 'Sumbangan', '23', 'sumbangan', 'sumbangan', 'fa fa-credit-card', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '19', NULL, '1', NULL, NULL, '2022-03-10 11:00:56', '2026-05-28 12:44:23', NULL),
('151', 'Jenis Identitas', '159', 'master-jenis-identitas', 'master-jenis-identitas', 'fas fa-address-book', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '68', NULL, '1', NULL, NULL, '2022-09-13 06:55:30', '2026-05-28 12:44:23', NULL),
('152', 'Jenis Perpustakaan', '161', 'master-jenis-perpustakaan', 'master-jenis-perpustakaan', 'fas fa-building', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '44', NULL, '1', NULL, NULL, '2022-09-13 08:22:36', '2026-05-28 12:44:23', NULL),
('153', 'Jenis Kelamin', '159', 'master-jenis-kelamin', 'master-jenis-kelamin', 'fas fa-venus-mars', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '69', NULL, '1', NULL, NULL, '2022-09-13 08:26:27', '2026-05-28 12:44:23', NULL),
('154', 'Status Perkawinan', '159', 'master-status-perkawinan', 'master-status-perkawinan', 'fas fa-restroom', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '74', NULL, '1', NULL, NULL, '2022-09-13 08:30:10', '2026-05-28 12:44:23', NULL),
('155', 'Jenis Pelanggaran', '156', 'master-jenis-pelanggaran', 'master-jenis-pelanggaran', 'fas fa-exclamation-circle', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '81', NULL, '1', NULL, NULL, '2022-09-13 08:45:26', '2026-05-28 12:44:23', NULL),
('156', 'Pengaturan Sirkulasi', '0', 'pengaturan-sirkulasi', 'pengaturan-sirkulasi', 'fab fa-confluence', 'access', NULL, 'menu', '1', 'backend-menu', '75', NULL, '1', NULL, NULL, '2022-09-14 05:16:35', '2026-05-28 12:44:23', NULL),
('158', 'Pengaturan Katalog', '0', 'pengaturan-katalog', 'pengaturan-katalog', 'fas fa-id-card-alt', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '47', NULL, '1', NULL, NULL, '2022-09-14 05:17:29', '2026-05-28 12:44:23', NULL),
('159', 'Pengaturan Keanggotaan', '0', 'pengaturan-keanggotaan', 'pengaturan-keanggotaan', 'fas fa-address-card', 'access', NULL, 'menu', '1', 'backend-menu', '62', NULL, '1', NULL, NULL, '2022-09-14 05:17:52', '2026-05-28 12:44:23', NULL),
('160', 'Pengaturan Eksemplar', '0', 'pengaturan-akuisisi', 'pengaturan-akuisisi', 'fab fa-readme', 'access', NULL, 'menu', '1', 'backend-menu', '56', NULL, '1', NULL, NULL, '2022-09-14 05:18:27', '2026-05-28 12:44:23', NULL),
('161', 'Pengaturan Umum', '0', 'pengaturan-umum', 'pengaturan-umum', 'fas fa-folder-open', 'access', NULL, 'menu', '1', 'backend-menu', '40', NULL, '1', NULL, NULL, '2022-09-14 05:18:47', '2026-05-28 12:44:23', NULL),
('163', 'Peminjaman', '53', 'sirkulasi-peminjaman', 'sirkulasi-peminjaman', 'fas fa-window-restore', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '24', NULL, '1', NULL, NULL, '2022-09-15 08:30:42', '2026-05-28 12:44:23', NULL),
('165', 'Perpanjangan', '53', 'sirkulasi-perpanjangan', 'sirkulasi-perpanjangan', 'fas fa-layer-group', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '25', NULL, '1', NULL, NULL, '2022-09-15 08:31:55', '2026-05-28 12:44:23', NULL),
('166', 'Pelanggaran', '53', 'sirkulasi-pelanggaran', 'sirkulasi-pelanggaran', 'fas fa-exclamation-triangle', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '27', NULL, '1', NULL, NULL, '2022-09-15 08:32:16', '2026-05-28 12:44:23', NULL),
('167', 'Daftar Katalog', '56', 'katalog', 'katalog', '', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '21', NULL, '1', NULL, NULL, '2022-10-06 07:12:27', '2025-09-08 11:10:59', NULL),
('168', 'Karantina Katalog', '56', 'karantina-katalog', 'karantina-katalog', '', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '22', NULL, '1', NULL, NULL, '2022-10-06 07:12:48', '2025-09-08 11:10:59', NULL),
('169', 'Daftar Anggota', '23', 'anggota', 'anggota', 'fa fa-users', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '15', NULL, '1', NULL, NULL, '2022-10-11 04:50:34', '2026-05-28 12:44:23', NULL),
('170', 'Keranjang Anggota', '23', 'keranjang-anggota', 'keranjang-anggota', 'fas fa-shopping-cart', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '18', NULL, '1', NULL, NULL, '2022-10-11 04:51:06', '2026-05-28 12:44:23', NULL),
('177', 'Pengembalian', '53', 'sirkulasi-pengembalian', 'sirkulasi-pengembalian', 'fas fa-paste', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '26', NULL, '1', NULL, NULL, '2022-11-19 16:38:54', '2026-05-28 12:44:23', NULL),
('178', 'Laporan Anggota', '3', 'laporan-anggota', 'laporan-anggota', 'fas fa-theater-masks', 'access|index', NULL, 'menu', '1', 'backend-menu', '34', NULL, '1', NULL, NULL, '2022-11-21 12:08:39', '2026-05-28 12:44:23', NULL),
('191', 'Nama Perpustakaan', '161', 'master-nama-perpustakaan', 'master-nama-perpustakaan', 'fas fa-school', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '41', NULL, '1', NULL, NULL, '2023-10-13 01:10:02', '2026-05-28 12:44:23', NULL),
('194', 'Referensi', '158', 'master-referensi', 'referensi', 'fas fa-file-signature', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '49', NULL, '1', NULL, NULL, '2023-10-15 04:45:56', '2026-05-28 12:44:23', NULL),
('195', 'Kelas Besar', '158', 'master-kelas-besar', 'master-kelas-besar', 'fas fa-door-open', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '50', NULL, '1', NULL, NULL, '2023-10-15 04:47:08', '2026-05-28 12:44:23', NULL),
('196', 'Kata Sandang', '158', 'master-kata-sandang', 'kata-sandang', 'fas fa-book-open', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '51', NULL, '1', NULL, NULL, '2023-10-15 04:48:01', '2026-05-28 12:44:23', NULL),
('197', 'Jenis Bahan Pustaka', '158', 'master-jenis-bahan-pustaka', 'jenis-bahan-pustaka', 'fas fa-laptop-code', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '52', NULL, '1', NULL, NULL, '2023-10-15 04:48:27', '2026-05-28 12:44:23', NULL),
('199', 'Detail Katalog', '158', 'master-detail-katalog', 'master-detail-katalog', 'fas fa-book', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '53', NULL, '1', NULL, NULL, '2023-10-15 04:50:56', '2026-05-28 12:44:23', NULL),
('200', 'Penyedia Katalog', '158', 'master-penyedia-katalog', 'master-penyedia-katalog', 'fas fa-book', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '54', NULL, '1', NULL, NULL, '2023-10-15 05:11:55', '2026-05-28 12:44:23', NULL),
('201', 'Form Entri', '158', 'master-form-entri', 'master-form-entri', 'fas fa-address-book', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '55', NULL, '1', NULL, NULL, '2023-10-15 05:13:10', '2026-05-28 12:44:23', NULL),
('203', 'Hari Libur', '161', 'master-hari-libur', 'hari-libur', 'fas fa-calendar-times', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '46', NULL, '1', NULL, NULL, '2023-11-05 07:19:41', '2026-05-28 12:44:23', NULL),
('204', 'Kategori Koleksi', '160', 'master-kategori-koleksi', 'kategori-koleksi', 'fas fa-pen-square', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '59', NULL, '1', NULL, NULL, '2023-11-13 01:38:01', '2026-05-28 12:44:23', NULL),
('205', 'Media Koleksi', '160', 'master-media-koleksi', 'media-koleksi', 'fas fa-mail-bulk', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '60', NULL, '1', NULL, NULL, '2023-11-13 01:40:42', '2026-05-28 12:44:23', NULL),
('206', 'Sumber Koleksi', '160', 'master-sumber-koleksi', 'sumber-koleksi', 'fas fa-clipboard-list', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '61', NULL, '1', NULL, NULL, '2023-11-13 01:42:02', '2026-05-28 12:44:23', NULL),
('207', 'Jenis Akses', '156', 'master-jenis-akses', 'jenis-akses', 'fas fa-exclamation-circle', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '78', NULL, '1', NULL, NULL, '2023-11-13 01:46:49', '2026-05-28 12:44:23', NULL),
('208', 'Jenis Bahan', '156', 'master-jenis-bahan', 'jenis-bahan', 'fas fa-clone', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '79', NULL, '1', NULL, NULL, '2023-11-13 01:47:27', '2026-05-28 12:44:23', NULL),
('209', 'Jenis Denda', '156', 'master-jenis-denda', 'jenis-denda', 'fas fa-money-check-alt', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '80', NULL, '1', NULL, NULL, '2023-11-13 01:48:12', '2026-05-28 12:44:23', NULL),
('210', 'Peraturan Peminjaman Hari', '156', 'master-peraturan-peminjaman-hari', 'peraturan-peminjaman-hari', 'fas fa-calendar-check', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '76', NULL, '1', NULL, NULL, '2023-11-13 01:51:12', '2026-05-28 12:44:23', NULL),
('211', 'Daftar Eksemplar', '59', 'eksemplar', 'daftar-eksemplar', '', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '12', NULL, '1', NULL, NULL, '2024-01-10 07:55:23', '2024-12-11 10:21:16', NULL),
('212', 'Karantina Eksemplar', '59', 'karantina-eksemplar', 'karantina-eksemplar', '', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '13', NULL, '1', NULL, NULL, '2024-01-10 07:55:59', '2024-12-11 10:21:16', NULL),
('214', 'Pengaturan Buku Tamu', '0', '', 'pengaturan-buku-tamu', 'fas fa-id-badge', 'access', NULL, 'menu', '1', 'backend-menu', '82', NULL, '1', NULL, NULL, '2024-03-19 22:54:42', '2026-05-28 12:44:23', NULL),
('215', 'Tujuan Kunjungan', '214', 'master-tujuan-kunjungan', 'tujuan-kunjungan', 'fas fa-arrow-alt-circle-right', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '83', NULL, '1', NULL, NULL, '2024-03-19 22:56:19', '2026-05-28 12:44:23', NULL),
('216', 'Setting Buku Tamu', '214', 'master-setting-buku-tamu', 'setting-buku-tamu', 'fas fa-wrench', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '84', NULL, '1', NULL, NULL, '2024-03-19 22:56:45', '2026-05-28 12:44:23', NULL),
('217', 'Pekerjaan', '159', 'master-jenis-pekerjaan', 'pekerjaan', 'fas fa-id-card-alt', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '70', NULL, '1', NULL, NULL, '2024-03-19 23:04:11', '2026-05-28 12:44:23', NULL),
('218', 'Pendidikan', '159', 'master-jenis-pendidikan', 'pendidikan', 'fas fa-id-card-alt', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '73', NULL, '1', NULL, NULL, '2024-03-19 23:04:32', '2026-05-28 12:44:23', NULL),
('219', 'Layanan Sabtu & Minggu', '161', 'master-form-sabtuminggu', 'layanan-sabtu-minggu', 'fas fa-calendar-check', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '45', NULL, '1', NULL, NULL, '2024-07-21 11:28:12', '2026-05-28 12:44:23', NULL),
('221', 'Buku Tamu', '0', 'bukutamu', 'buku-tamu', 'fa fa-user', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '29', NULL, '1', NULL, NULL, '2024-09-19 23:20:43', '2026-05-28 12:44:23', NULL),
('222', 'Laporan Katalog', '3', 'laporan-katalog', 'laporan-katalog', 'fas fa-book', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '32', NULL, '1', NULL, NULL, '2024-12-11 10:20:17', '2026-05-28 12:44:23', NULL),
('223', 'Laporan Eksemplar', '3', 'laporan-eksemplar', 'laporan-eksemplar', 'fas fa-file-alt', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '33', NULL, '1', NULL, NULL, '2024-12-11 10:21:01', '2026-05-28 12:44:23', NULL),
('224', 'Import Eksemplar', '59', 'eksemplar/import', 'import-eksemplar', 'pe-7s-angle-left-circle', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '0', NULL, '1', NULL, NULL, '2025-03-09 07:51:52', '2025-03-09 07:51:52', NULL),
('226', 'Daftar Eksemplar', '225', 'eksemplar', 'daftar-eksemplar', 'pe-7s-angle-left-circle', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '13', NULL, '1', NULL, NULL, '2025-03-09 07:59:06', '2025-09-08 11:10:59', NULL),
('227', 'Entri Eksemplar', '254', 'eksemplar/create', 'entri-eksemplar', 'fas fa-plus', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '9', NULL, '1', NULL, NULL, '2025-03-09 08:01:00', '2025-09-08 11:10:59', NULL),
('228', 'Import Eksemplar', '225', 'eksemplar/import', 'import-eksemplar', 'pe-7s-angle-left-circle', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '15', NULL, '1', NULL, NULL, '2025-03-09 08:01:37', '2025-09-08 11:10:59', NULL),
('229', 'Karantina Eksemplar', '225', 'karantina-eksemplar', 'karantina-eksemplar', '', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '16', NULL, '1', NULL, NULL, '2025-03-09 08:05:19', '2025-09-08 11:10:59', NULL),
('230', 'Entri Katalog AACR', '254', 'katalog/create?rda=0', 'entri-katalog', 'fas fa-plus', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '3', NULL, '1', NULL, NULL, '2025-03-18 07:18:19', '2025-09-08 11:10:59', NULL),
('232', 'Entri Katalog RDA', '254', 'katalog/create?rda=1', 'entri-katalog-rda', 'fas fa-plus', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '2', NULL, '1', NULL, NULL, '2025-03-18 07:38:58', '2025-09-08 11:10:59', NULL),
('233', 'Perpanjangan Anggota', '23', 'perpanjangan-anggota', 'perpanjangan-anggota', 'fa fa-user-circle', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '16', NULL, '1', NULL, NULL, '2025-03-18 15:16:53', '2026-05-28 12:44:23', NULL),
('234', 'Entri Peminjaman', '53', 'sirkulasi-peminjaman/create', 'entri-peminjaman', 'fas fa-sync', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '21', NULL, '1', NULL, NULL, '2025-03-18 17:58:49', '2026-05-28 12:44:23', NULL),
('235', 'Entri  Pengembalian', '53', 'sirkulasi-pengembalian/create', 'entri-pengembalian', 'fas fa-share-square', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '23', NULL, '1', NULL, NULL, '2025-03-18 18:00:24', '2026-05-28 12:44:23', NULL),
('236', 'Import Anggota', '23', 'anggota/import_view', 'import-anggota', 'fas fa-file-excel', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '17', NULL, '1', NULL, NULL, '2025-03-27 01:53:42', '2026-05-28 12:44:23', NULL),
('296', 'Export Anggota', '23', 'anggota/export_view', 'export-anggota', 'fas fa-file-export', 'access|index', NULL, 'menu', '1', 'backend-menu', '20', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('237', 'Ruas Data Bibliografis', '160', 'ruas-data-bibliografis', 'ruas-data-bibliografis', 'fas fa-file-code', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '57', NULL, '1', NULL, NULL, '2025-06-26 01:08:32', '2026-05-28 12:44:23', NULL),
('238', 'Penomoran koleksi', '160', 'master-penomoran-koleksi', 'penomoran-koleksi', 'fas fa-keyboard', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '58', NULL, '1', NULL, NULL, '2025-06-26 16:34:12', '2026-05-28 12:44:23', NULL),
('239', 'Redaksi Keanggotaan', '159', 'master-redaksi-keanggotaan', 'redaksi-keanggotaan', 'fas fa-chalkboard-teacher', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '71', NULL, '1', NULL, NULL, '2025-06-26 19:46:31', '2026-05-28 12:44:23', NULL),
('240', 'Kelompok Umur', '159', 'master-kelompok-umur', 'kelompok-umur', 'fas fa-venus', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '64', NULL, '1', NULL, NULL, '2025-06-27 07:14:16', '2026-05-28 12:44:23', NULL),
('241', 'Master Fakultas', '159', 'master-fakultas', 'master-fakultas', 'fas fa-hotel', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '65', NULL, '1', NULL, NULL, '2025-06-27 07:17:19', '2026-05-28 12:44:23', NULL),
('242', 'Master Jurusan', '159', 'master-jurusan', 'master-jurusan', 'fas fa-hotel', 'access', NULL, 'menu', '1', 'backend-menu', '66', NULL, '1', NULL, NULL, '2025-06-27 07:17:51', '2026-05-28 12:44:23', NULL),
('243', 'Entri Keanggotaan', '159', 'master-entri-keanggotaan', 'entri-keanggotaan', 'fas fa-child', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '72', NULL, '1', NULL, NULL, '2025-06-27 09:08:03', '2026-05-28 12:44:23', NULL),
('244', 'Salin Katalog', '56', 'katalog/marc-import', 'salin-katalog', '', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '18', NULL, '1', NULL, NULL, '2025-07-03 11:59:13', '2025-09-08 11:10:59', NULL),
('245', 'Akses', '6', 'access', 'akses', 'pe-7s-users', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '87', NULL, '1', NULL, NULL, '2025-07-07 07:41:28', '2026-05-28 12:44:23', NULL),
('246', 'Laporan AI', '3', 'laporan-ai', 'laporan-ai', 'fas fa-file-alt', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '38', NULL, '1', NULL, NULL, '2025-07-07 07:54:54', '2026-05-28 12:44:23', NULL),
('247', 'Entri Anggota', '23', 'anggota/create', 'entri-anggota', 'fa fa-user-plus', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '14', NULL, '1', NULL, NULL, '2025-07-26 19:15:37', '2026-05-28 12:44:23', NULL),
('248', 'Profil Anggota', '0', 'anggota/online', 'profil-anggota', '', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '12', NULL, '1', NULL, NULL, '2025-07-30 23:42:39', '2026-05-28 12:44:23', NULL),
('249', 'Stockopname', '0', 'stockopname', 'stockopname', 'fas fa-file-alt', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '28', NULL, '1', NULL, NULL, '2025-08-07 09:04:51', '2026-05-28 12:44:23', NULL),
('250', 'Laporan Buku Tamu', '3', 'laporan-buku-tamu', 'laporan-buku-tamu', 'fas fa-book-reader', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '36', NULL, '1', NULL, NULL, '2025-08-15 15:02:05', '2026-05-28 12:44:23', NULL),
('251', 'Master Kelas', '159', 'master-kelas', 'master-kelas', 'fas fa-door-closed', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '63', NULL, '1', NULL, NULL, '2025-09-02 15:02:35', '2026-05-28 12:44:23', NULL),
('252', 'Berita', '0', 'cms/berita', 'berita', 'fas fa-newspaper', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '3', NULL, '1', NULL, NULL, '2025-09-03 10:54:29', '2026-05-28 12:44:23', NULL),
('253', 'Peraturan Peminjaman Tanggal', '156', 'master-peraturan-peminjaman-tanggal', 'peraturan-peminjaman-tanggal', 'fas fa-sync-alt', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '77', NULL, '1', NULL, NULL, '2025-09-06 15:25:33', '2026-05-28 12:44:23', NULL),
('254', 'Koleksi', '0', '', 'fas-fa-book', 'fas fa-book', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '5', NULL, '1', NULL, NULL, '2025-09-08 10:45:57', '2026-05-28 12:44:23', NULL),
('255', 'Daftar Eksemplar', '254', 'eksemplar', 'daftar-eksemplar', 'fa fa-clipboard', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '8', NULL, '1', NULL, NULL, '2025-09-08 10:48:08', '2026-05-28 12:44:23', NULL),
('256', 'Karantina Eksemplar', '254', 'karantina-eksemplar', 'karantina-eksemplar', 'fa fa-archive', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '12', NULL, '1', NULL, NULL, '2025-09-08 10:49:18', '2026-05-28 12:44:23', NULL),
('257', 'Daftar Katalog', '254', 'katalog', 'daftar-katalog', 'fas fa-book-reader', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '1', NULL, '1', NULL, NULL, '2025-09-08 10:50:32', '2026-05-28 12:44:23', NULL),
('258', 'Karantina Katalog', '254', 'karantina-katalog', 'karantina-katalog', 'fa fa-archive', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '5', NULL, '1', NULL, NULL, '2025-09-08 10:52:38', '2026-05-28 12:44:23', NULL),
('259', 'Salin Katalog', '254', 'katalog/marc-import', 'salin-katalog', 'fa fa-folder-open', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '7', NULL, '1', NULL, NULL, '2025-09-08 11:02:59', '2026-05-28 12:44:23', NULL),
('260', 'Import Excel', '254', 'eksemplar/importviews', 'import-excel', 'fas fa-file-excel', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '10', NULL, '1', NULL, NULL, '2025-09-08 11:10:26', '2026-05-28 12:44:23', NULL),
('261', 'Group', '6', 'group', 'group', 'fas fa-user-friends', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '86', NULL, '1', NULL, NULL, '2025-09-14 23:48:37', '2026-05-28 12:44:23', NULL),
('262', 'Laporan Sirkulasi', '3', 'laporan-sirkulasi', 'laporan-sirkulasi', 'fas fa-sync', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '35', NULL, '1', NULL, NULL, '2025-09-17 08:58:36', '2026-05-28 12:44:23', NULL),
('265', 'Baca di Tempat', '0', 'baca-di-tempat', 'baca-di-tempat', 'fas fa-newspaper', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '30', NULL, '1', NULL, NULL, '2025-09-22 00:48:19', '2026-05-28 12:44:23', NULL),
('266', 'Banner', '0', 'cms/banner', 'banner', 'fas fa-images', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '4', NULL, '1', NULL, NULL, '2025-12-07 15:11:11', '2026-05-28 12:44:23', NULL),
('267', 'laporan-baca-ditempat', '3', 'laporan-baca-ditempat', 'laporan-baca-ditempat', 'fas fa-book-reader', 'access|index', NULL, 'menu', '1', 'backend-menu', '37', NULL, '1', NULL, NULL, '2026-05-08 21:55:44', '2026-05-28 12:44:23', NULL),
('268', 'Entri Perpanjangan', '53', 'sirkulasi-perpanjangan/create', 'entri-perpanjangan', 'fas fa-chalkboard', 'access', NULL, 'menu', '1', 'backend-menu', '22', NULL, '1', NULL, NULL, '2026-05-28 12:39:07', '2026-05-28 12:44:23', NULL),
('269', 'Profil & Layanan', '0', 'cms/profil', 'profil-layanan', 'fas fa-address-card', 'access|index|edit', NULL, 'menu', '1', 'backend-menu', '5', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('270', 'Usulan Koleksi', '254', 'usulankoleksi', 'usulan-koleksi', 'fa fa-laptop', 'access|index', NULL, 'menu', '1', 'backend-menu', '15', NULL, '1', NULL, NULL, NULL, '2026-10-31 11:18:49', NULL),
('271', 'Survei Pemustaka', '0', 'surveipemustaka', 'survei-pemustaka', 'fas fa-chart-bar', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '30', NULL, '1', NULL, NULL, NULL, '2026-10-31 11:23:34', NULL),
('272', 'Koleksi Dipesan', '53', 'sirkulasi-pemesanan', 'sirkulasi-pemesanan', 'fas fa-bookmark', 'access|index|delete', NULL, 'menu', '1', 'backend-menu', '28', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('273', 'Kardeks Terbitan Berkala', '254', 'kardeks-terbitan-berkala', 'kardeks-terbitan-berkala', 'fas fa-newspaper', 'access|index', NULL, 'menu', '1', 'backend-menu', '14', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('274', 'Nama Sumber Perolehan', '160', 'master-nama-sumber-perolehan', 'nama-sumber-perolehan', 'fas fa-handshake', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '62', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('275', 'Pengaturan Opac', '0', 'pengaturan-opac', 'pengaturan-opac', 'fas fa-globe', 'access', NULL, 'menu', '1', 'backend-menu', '83', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('276', 'Koleksi Unggulan', '275', 'master-opac-koleksi-unggulan', 'opac-koleksi-unggulan', 'fas fa-star', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '1', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('277', 'Koleksi Terbaru', '275', 'master-opac-koleksi-terbaru', 'opac-koleksi-terbaru', 'fas fa-book-open', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '2', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('278', 'Koleksi Sering Dipinjam', '275', 'master-opac-koleksi-sering-dipinjam', 'opac-koleksi-sering-dipinjam', 'fas fa-fire', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '3', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('279', 'Pemesanan Koleksi', '275', 'master-opac-booking', 'opac-booking', 'fas fa-shopping-basket', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '5', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('280', 'Usulan Koleksi', '275', 'master-opac-usulan-koleksi', 'opac-usulan-koleksi', 'fas fa-lightbulb', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '6', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('282', 'Histori Pencarian', '275', 'master-opac-histori-pencarian', 'opac-histori-pencarian', 'fas fa-history', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '8', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('283', 'Cache & Halaman Frontend', '161', 'master-pengaturan-frontend', 'pengaturan-frontend', 'fas fa-sliders-h', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '47', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('284', 'Export Excel', '254', 'export-eksemplar', 'export-eksemplar', 'fas fa-file-export', 'access|index|export|download', NULL, 'menu', '1', 'backend-menu', '11', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('285', 'Koleksi Sering Baca di Tempat', '275', 'master-opac-baca-ditempat', 'opac-baca-ditempat', 'fas fa-book-reader', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '4', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('286', 'Perpustakaan Keliling', '0', 'cms/perpustakaan-keliling', 'perpustakaan-keliling-cms', 'fas fa-bus', 'access', NULL, 'menu', '1', 'backend-menu', '6', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('287', 'Jadwal Layanan', '286', 'cms/perpustakaan-keliling/jadwal', 'perpustakaan-keliling-jadwal', 'fas fa-calendar-alt', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '1', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('288', 'Unit Kendaraan', '286', 'cms/perpustakaan-keliling/unit', 'perpustakaan-keliling-unit', 'fas fa-bus', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '2', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('289', 'Lokasi Layanan', '286', 'cms/perpustakaan-keliling/lokasi', 'perpustakaan-keliling-lokasi', 'fas fa-map-marker-alt', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '3', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('290', 'Petugas', '286', 'cms/perpustakaan-keliling/petugas', 'perpustakaan-keliling-petugas', 'fas fa-user-friends', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '4', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('291', 'Log Pencarian OPAC', '275', 'master-opac-logs', 'opac-logs', 'fas fa-clipboard-list', 'access|index|create|edit|delete', NULL, 'menu', '1', 'backend-menu', '7', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('292', 'Daftar Artikel', '254', 'daftar-artikel', 'daftar-artikel', 'fas fa-file-alt', 'access|index', NULL, 'menu', '1', 'backend-menu', '4', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('293', 'Keranjang Katalog', '254', 'keranjang-katalog', 'keranjang-katalog', 'fas fa-shopping-cart', 'access|index', NULL, 'menu', '1', 'backend-menu', '6', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('294', 'Keranjang Eksemplar', '254', 'keranjang-eksemplar', 'keranjang-eksemplar', 'fas fa-shopping-cart', 'access|index', NULL, 'menu', '1', 'backend-menu', '13', NULL, '1', NULL, NULL, NULL, NULL, NULL),
('295', 'Aktivasi Kios', '214', 'master-kios-buku-tamu', 'kios-buku-tamu', 'fas fa-desktop', 'access|index|create|delete', NULL, 'menu', '1', 'backend-menu', '85', NULL, '1', NULL, NULL, NULL, NULL, NULL);

-- ---------------------------------------------------------------------
-- 2. settingparameters: hanya Name yang belum ada
-- ---------------------------------------------------------------------
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'MaksJumlahPeminjaman', '2', NULL, '2012-09-12 13:56:31', 'DeniPC', NULL, '2015-10-02 02:46:43', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'MaksJumlahPeminjaman');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'MaksJumlahPeminjamanTerlambatUtkSuspend', '1', NULL, '2012-09-12 13:56:31', 'DeniPC', NULL, '2015-10-02 02:46:43', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'MaksJumlahPeminjamanTerlambatUtkSuspend');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'IsSaturdayHoliday', 'True', NULL, '2012-09-12 13:56:31', 'DeniPC', NULL, '2026-05-26 10:21:38', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'IsSaturdayHoliday');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'IsSundayHoliday', 'True', NULL, '2012-09-12 13:56:31', 'DeniPC', NULL, '2026-05-26 10:21:38', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'IsSundayHoliday');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'TipeSirkulasi', 'BARCODE', NULL, '2012-09-12 13:56:32', 'DeniPC', NULL, '2015-10-02 02:46:44', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'TipeSirkulasi');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'NomorIndukTengah', '/PN/', NULL, '2012-09-12 13:56:32', 'DeniPC', NULL, '2015-12-30 10:16:17', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'NomorIndukTengah');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FontBarcode', 'IDAutomationHC39M', NULL, '2013-04-21 19:48:14', 'DeniPC', NULL, '2015-07-23 11:05:06', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FontBarcode');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'NomorInduk', 'Otomatis', NULL, '2013-04-21 19:48:14', 'DeniPC', NULL, '2026-05-26 11:23:19', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'NomorInduk');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'NamaPerpustakaan', 'Dinas Arsip dan Perpustakaan Kabupaten Kulon Progo', NULL, '2013-04-21 19:48:14', 'DeniPC', NULL, '2026-10-31 14:35:32', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'NamaPerpustakaan');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'CurrentVersion', '3.0', NULL, '2013-04-21 19:48:14', 'DeniPC', NULL, '2013-05-01 12:35:35', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'CurrentVersion');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FileKartuAnggota', '1793682812_f417413f248d70a7f4c6.jpg', NULL, '2015-03-13 10:28:58', 'DeniPC', NULL, '2013-05-01 12:35:35', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FileKartuAnggota');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'Text_BELAKANG', '<br><br><ol><li>Kartu ini adalah <b>milik Perpustakaan Mahkamah Konstitusi</b></li><li>Kartu ini harus selalu dibawa saat berkunjung</li><li>Kartu ini <u>tidak dapat dipinjamkan/dipindahtangankan</u></li><li>Jika hilang segeralah melapor dengan melampirkan surat keterangan hilang dari pihak berwenang</li><li>Bagi yang menemukan harap dikembailkan kepada<i> Perpustakaan Mahkamah Konstitusi</i></li></ol><br><p></p>', NULL, '2015-05-18 16:30:25', '::1', NULL, '2015-10-13 18:19:07', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'Text_BELAKANG');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'JenisPerpustakaan', 'UMUM', NULL, '2015-06-26 10:24:20', '::1', NULL, '2026-10-31 14:35:32', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'JenisPerpustakaan');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FormatNomorBarcode', 'No. Induk', NULL, '2015-08-21 00:00:00', 'DeniPC', NULL, '2026-05-26 11:23:19', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FormatNomorBarcode');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'TipeNomorAnggota', 'Otomatis', NULL, '2015-08-26 02:14:35', 'HenryZone', NULL, '2026-05-22 21:08:11', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'TipeNomorAnggota');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'MasaBerlakuAnggota', NULL, NULL, '2015-08-26 02:14:35', 'HenryZone', NULL, '2015-10-15 12:00:45', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'MasaBerlakuAnggota');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'BiayaPendaftaran', '1', NULL, '2015-08-26 02:14:35', 'HenryZone', NULL, '2015-08-26 02:14:35', NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'BiayaPendaftaran');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'BiayaPerpanjangan', '1', NULL, '2015-08-26 02:14:35', 'HenryZone', NULL, '2015-08-26 02:14:35', NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'BiayaPerpanjangan');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FormEntriKatalog', 'Simple', NULL, '2015-08-26 00:00:00', 'DeniPC', NULL, '2026-05-24 20:09:21', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FormEntriKatalog');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FormEntriKoleksi', 'Simple', NULL, '2015-08-26 00:00:00', 'DeniPC', NULL, '2015-08-27 13:42:27', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FormEntriKoleksi');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'NamaLokasiPerpustakaan', 'Jalan Gebang Sari Nomor 38, RT.2/RW.5', NULL, NULL, NULL, NULL, '2026-10-31 14:35:32', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'NamaLokasiPerpustakaan');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'AuthorityDataLastDate', '2015-12-14 00:00:00 ', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'AuthorityDataLastDate');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'IsActivatingImportingAuthorityData', '0', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'IsActivatingImportingAuthorityData');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'TipeMaksKoleksiDapatPinjam', 'Khusus', NULL, NULL, NULL, NULL, '2015-10-02 02:46:43', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'TipeMaksKoleksiDapatPinjam');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'TipePenomoranAnggota', '4', NULL, NULL, NULL, NULL, '2026-05-22 21:08:11', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'TipePenomoranAnggota');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'KartuAnggota', '4', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'KartuAnggota');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'SumberPengkatalogan', 'JKPNPNA', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'SumberPengkatalogan');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'IsCetakSlipPeminjaman', '1', NULL, NULL, NULL, NULL, '2015-10-02 02:46:44', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'IsCetakSlipPeminjaman');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'IsCetakSlipPengembalian', '1', NULL, NULL, NULL, NULL, '2015-10-02 02:46:44', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'IsCetakSlipPengembalian');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'IsCetakSlipPerpanjangan', NULL, NULL, NULL, NULL, NULL, '2026-05-22 21:08:11', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'IsCetakSlipPerpanjangan');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'IsCetakSlipPelanggaran', NULL, NULL, NULL, NULL, NULL, '2026-05-22 21:08:11', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'IsCetakSlipPelanggaran');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'IsCetakSlipPendaftaran', NULL, NULL, NULL, NULL, NULL, '2026-05-22 21:08:11', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'IsCetakSlipPendaftaran');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'IsUseKop', '1', NULL, NULL, NULL, NULL, '2026-10-31 14:35:32', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'IsUseKop');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'IMAGES_BELAKANG', 'bg_cardmemberbelakang.png', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'IMAGES_BELAKANG');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'ShowKoleksiUnggulan', 'TRUE', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'ShowKoleksiUnggulan');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'ShowKoleksiTerbaru', 'TRUE', NULL, '2015-11-10 10:57:42', NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'ShowKoleksiTerbaru');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'ShowKoleksiSeringDipinjam', 'TRUE', NULL, '2015-11-10 10:57:47', NULL, NULL, '2026-11-01 16:42:31', NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'ShowKoleksiSeringDipinjam');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'KoleksiUnggulanShow', '8', NULL, '2015-11-10 10:57:51', NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'KoleksiUnggulanShow');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'KoleksiTerbaruShow', '8', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'KoleksiTerbaruShow');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'KoleksiSeringDipinjamShow', '8', NULL, NULL, NULL, NULL, '2026-11-01 16:42:31', NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'KoleksiSeringDipinjamShow');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'JumlahBookingMaksimal', '3', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'JumlahBookingMaksimal');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'BookingExpired', '03:00:00', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'BookingExpired');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'IsActivatingKII', '0', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'IsActivatingKII');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'IsKIIActivated', '1', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'IsKIIActivated');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'KIICode', 'XUFZRWS78BHEVLA9365C', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'KIICode');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'KIILastUploadDate', '2008-02-11 00:00:00 ', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'KIILastUploadDate');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'BranchCode', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'BranchCode');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FormatNomorInduk', '{DISPUSIP}|3|7|3|6|2|0|2|0', NULL, NULL, NULL, NULL, '2026-05-26 11:23:19', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FormatNomorInduk');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FormatNomorRFID', 'No. Induk', NULL, NULL, NULL, NULL, '2026-05-26 11:23:19', '::1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FormatNomorRFID');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'AudioBukutamu', ' ', NULL, NULL, NULL, NULL, '2015-12-08 15:20:33', '127.0.0.1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'AudioBukutamu');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'JaminanUangLoker', '1', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'JaminanUangLoker');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'JaminanIdentitasLoker', '1', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'JaminanIdentitasLoker');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'CetakBuktiTransaksi', '1', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'CetakBuktiTransaksi');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'CetakBuktiPelanggaran', '1', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'CetakBuktiPelanggaran');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'IsMemberAllowedToBorrowMultipleLocker', '1', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'IsMemberAllowedToBorrowMultipleLocker');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'IsBookingActivated', '1', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'IsBookingActivated');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedAuthorMax', '15', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedAuthorMax');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedAuthorMin', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedAuthorMin');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublisherMax', '15', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublisherMax');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublisherMin', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublisherMin');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublishLocationMax', '15', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublishLocationMax');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublishLocationMin', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublishLocationMin');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublishYearMax', '15', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublishYearMax');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublishYearMin', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublishYearMin');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedSubjectMax', '15', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedSubjectMax');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedSubjectMin', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedSubjectMin');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'UsulanKoleksi', 'TRUE', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'UsulanKoleksi');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'SmsJatuhTempoAktif', 'TRUE', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'SmsJatuhTempoAktif');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'SmsJatuhTempoJam', '11:30', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'SmsJatuhTempoJam');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'SmsJatuhTempoPesan', 'isi pesan \r\nudah jatuh tempo ni bro. balikin dong bukunya', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'SmsJatuhTempoPesan');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'SmsBelumJatuhTempoAktif', 'TRUE', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'SmsBelumJatuhTempoAktif');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'SmsBelumJatuhTempoJam', '11:30', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'SmsBelumJatuhTempoJam');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'SmsBelumJatuhTempoPesan', 'Anggota yang terhormat, Buku yang anda pinjam akan habis masa pinjamnya. harap segera dikembalikan ke perpusnas ya. terima kasih', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'SmsBelumJatuhTempoPesan');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'JedaBelumJatuhTempo', '1', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'JedaBelumJatuhTempo');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'JedaJatuhTempo', '3', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'JedaJatuhTempo');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'NomorIndukCounter', '0019', NULL, NULL, NULL, NULL, '2016-05-16 14:43:11', '192.168.0.1', '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'NomorIndukCounter');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'ShowKoleksiSeringDipinjamLKD', 'FALSE', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'ShowKoleksiSeringDipinjamLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'KoleksiSeringDipinjamShowLKD', '4', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'KoleksiSeringDipinjamShowLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'ActivationCode', 'EubYFYtwfgDwve3nM6ldXrqUnHqjzcja_1464160672', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'ActivationCode');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedAuthorMaxLKD', '15', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedAuthorMaxLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedAuthorMinLKD', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedAuthorMinLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublisherMaxLKD', '15', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublisherMaxLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublisherMinLKD', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublisherMinLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublishLocationMaxLKD', '15', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublishLocationMaxLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublishLocationMinLKD', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublishLocationMinLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublishYearMaxLKD', '15', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublishYearMaxLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublishYearMinLKD', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublishYearMinLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedSubjectMaxLKD', '15', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedSubjectMaxLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedSubjectMinLKD', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedSubjectMinLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'UsulanKoleksiLKD', 'False', NULL, NULL, NULL, NULL, '2026-11-02 10:29:50', NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'UsulanKoleksiLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'ShowKoleksiTerbaruLKD', 'FALSE', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'ShowKoleksiTerbaruLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'ShowKoleksiUnggulanLKD', 'TRUE', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'ShowKoleksiUnggulanLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'KoleksiTerbaruShowLKD', '4', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'KoleksiTerbaruShowLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'KoleksiUnggulanShowLKD', '4', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'KoleksiUnggulanShowLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FormatNomorIndukx', '0|2|0|3|5|3|1|2|0', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FormatNomorIndukx');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedBahasaMax', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedBahasaMax');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedBahasaMin', '5', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedBahasaMin');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedBahasaMaxLKD', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedBahasaMaxLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedBahasaMinLKD', '5', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedBahasaMinLKD');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'OpacIndexer', '0', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'OpacIndexer');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedAuthorMaxArticle', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedAuthorMaxArticle');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedAuthorMinArticle', '5', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedAuthorMinArticle');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublisherMaxArticle', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublisherMaxArticle');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublisherMinArticle', '5', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublisherMinArticle');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublishLocationMaxArticle', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublishLocationMaxArticle');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublishLocationMinArticle', '5', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublishLocationMinArticle');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublishYearMaxArticle', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublishYearMaxArticle');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedPublishYearMinArticle', '5', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedPublishYearMinArticle');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedSubjectMaxArticle', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedSubjectMaxArticle');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedSubjectMinArticle', '5', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedSubjectMinArticle');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedBahasaMaxArticle', '10', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedBahasaMaxArticle');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'FacedBahasaMinArticle', '5', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'FacedBahasaMinArticle');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'language', 'idn', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'language');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'IsEntryCatalog', '0', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'IsEntryCatalog');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'CountingBukuTamu', '0', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'CountingBukuTamu');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'PeminjamanLewatJatuhTempo', '0', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'PeminjamanLewatJatuhTempo');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'PerpanjanganKoleksiMandiri', '0', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'PerpanjanganKoleksiMandiri');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'OtomatisInsertBukuTamu', '1', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'OtomatisInsertBukuTamu');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'PerpanjanganKenggotaanMandiri', '1', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'PerpanjanganKenggotaanMandiri');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'Logo', 'logo_20260527205025_8ad16539.webp', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'Logo');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'LogoKop', 'logo_20260527195238_a8846975.png', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'LogoKop');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'NPPPerpustakaan', '3175101E1000007', NULL, NULL, NULL, NULL, '2026-10-31 14:35:32', NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'NPPPerpustakaan');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'EmailPerpustakaan', 'perpustakaansrma9@gmail.com', NULL, NULL, NULL, NULL, '2026-10-31 14:35:32', NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'EmailPerpustakaan');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'Instagram', '@dispusip7f', NULL, NULL, NULL, NULL, '2026-10-31 14:35:32', NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'Instagram');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'Youtube', 'dispusip7f', NULL, NULL, NULL, NULL, '2026-10-31 14:35:32', NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'Youtube');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'Facebook', 'test22f7', NULL, NULL, NULL, NULL, '2026-10-31 14:35:32', NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'Facebook');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'Phone', '081180557777', NULL, NULL, NULL, NULL, '2026-10-31 14:35:32', NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'Phone');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'LayananOperasional', '&lt;b&gt;Jam Operasional Layanan&lt;/b&gt;&lt;br&gt;', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'LayananOperasional');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'KartuAnggota1', '1756785115_27f0d1b82bbc25230614.png', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'KartuAnggota1');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'TulisanBanner', 'Selamat Datang di Perpustakaan Mitra', NULL, NULL, NULL, NULL, '2026-10-31 14:35:32', NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'TulisanBanner');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'SettingBukuTamu', '1', NULL, NULL, NULL, NULL, NULL, NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'SettingBukuTamu');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'TentangKami', 'Dinas Perpustakaan Mitra berkomitmen untuk menyediakan layanan perpustakaan berkualitas bagi seluruh lapisan masyarakat di wilayah Daerah kami', NULL, NULL, NULL, NULL, '2026-10-31 14:35:32', NULL, '20986' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'TentangKami');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'ProvinsiID', '31', NULL, NULL, NULL, NULL, '2026-10-31 14:35:32', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'ProvinsiID');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'KabKotaID', '31.75', NULL, NULL, NULL, NULL, '2026-10-31 14:35:32', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'KabKotaID');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'KecamatanID', '31.75.10', NULL, NULL, NULL, NULL, '2026-10-31 14:35:32', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'KecamatanID');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'KelurahanID', '31.75.10.1006', NULL, NULL, NULL, NULL, '2026-10-31 14:35:32', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'KelurahanID');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'is_opac_cache', '0', NULL, NULL, NULL, NULL, '2026-11-02 10:29:50', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'is_opac_cache');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'is_dashboard_cache', '1', NULL, NULL, NULL, NULL, '2026-11-02 10:29:50', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'is_dashboard_cache');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'is_home_cache', '0', NULL, NULL, NULL, NULL, '2026-11-02 10:29:50', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'is_home_cache');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'isdrm', '1', NULL, NULL, NULL, NULL, '2026-11-02 10:29:50', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'isdrm');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'is_keanggotaan_online', '1', NULL, NULL, NULL, NULL, '2026-11-02 10:29:50', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'is_keanggotaan_online');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'is_perpustakaan_keliling', '1', NULL, NULL, NULL, NULL, '2026-11-02 10:29:50', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'is_perpustakaan_keliling');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'is_populer', '1', NULL, NULL, NULL, NULL, '2026-10-31 14:35:32', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'is_populer');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'is_sering_dipinjam', '1', NULL, NULL, NULL, NULL, '2026-10-31 14:35:32', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'is_sering_dipinjam');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'is_sering_dibaca', '0', NULL, NULL, NULL, NULL, '2026-11-02 10:29:50', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'is_sering_dibaca');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'Branch_id', 'ID Perpustakaan Mitra', NULL, '2026-10-31 10:14:22', NULL, NULL, '2026-10-31 14:35:32', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'Branch_id');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'JamOperasional', 'Jam Operasional Perpustakaan Mitra', NULL, '2026-10-31 10:14:22', NULL, NULL, '2026-10-31 14:35:32', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'JamOperasional');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'is_peminjaman_mandiri', '1', NULL, '2026-10-31 10:14:22', NULL, NULL, '2026-11-02 10:29:50', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'is_peminjaman_mandiri');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'is_pengembalian_mandiri', '1', NULL, '2026-10-31 10:14:22', NULL, NULL, '2026-11-02 10:29:50', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'is_pengembalian_mandiri');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'is_berita', '0', NULL, '2026-11-01 17:08:22', NULL, NULL, '2026-11-02 10:29:50', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'is_berita');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'opac_cache_ttl', '60', NULL, NULL, NULL, NULL, '2026-11-02 10:29:50', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'opac_cache_ttl');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'dashboard_cache_ttl', '60', NULL, NULL, NULL, NULL, '2026-11-02 10:29:50', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'dashboard_cache_ttl');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'home_cache_ttl', '60', NULL, NULL, NULL, NULL, '2026-11-02 10:29:50', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'home_cache_ttl');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'is_statistik_cache', '0', NULL, NULL, NULL, NULL, '2026-11-02 10:29:50', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'is_statistik_cache');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'statistik_cache_ttl', '60', NULL, NULL, NULL, NULL, '2026-11-02 10:29:50', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'statistik_cache_ttl');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'sering_dibaca_show', 'TRUE', NULL, '2026-11-02 13:16:50', NULL, NULL, '2026-11-02 13:16:50', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'sering_dibaca_show');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'show_sering_dibaca', '12', NULL, '2026-11-02 13:20:09', NULL, NULL, '2026-11-02 13:20:09', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'show_sering_dibaca');
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateBy`, `CreateDate`, `CreateTerminal`, `UpdateBy`, `UpdateDate`, `UpdateTerminal`, `Branch_id`)
SELECT 'opac_log', 'FALSE', NULL, '2026-11-03 10:15:16', NULL, NULL, '2026-11-03 10:16:59', NULL, NULL FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'opac_log');

COMMIT;

SET SESSION FOREIGN_KEY_CHECKS = @old_fk_checks;

-- ---------------------------------------------------------------------
-- 3. Verifikasi
-- ---------------------------------------------------------------------
-- Cek 1: user yang grup / permission-nya berubah arti atau hilang. Harus 0 baris.
SELECT 'auth_groups_users' AS sumber, gu.user_id, gu.group_id AS ref_id, og.name AS nama_lama, g.name AS nama_baru
FROM `auth_groups_users` gu
LEFT JOIN tmp_rbac_old_groups og ON og.id = gu.group_id
LEFT JOIN `auth_groups` g ON g.id = gu.group_id
WHERE NOT (og.name <=> g.name) OR g.id IS NULL
UNION ALL
SELECT 'auth_users_permissions', up.user_id, up.permission_id, op.name, p.name
FROM `auth_users_permissions` up
LEFT JOIN tmp_rbac_old_permissions op ON op.id = up.permission_id
LEFT JOIN `auth_permissions` p ON p.id = up.permission_id
WHERE NOT (op.name <=> p.name) OR p.id IS NULL;

-- Cek 2: grup dan permission lama yang hilang setelah penimpaan, hanya untuk informasi. Boleh ada isinya.
SELECT 'grup hilang' AS jenis, og.id, og.name FROM tmp_rbac_old_groups og
LEFT JOIN `auth_groups` g ON g.id = og.id AND g.name = og.name WHERE g.id IS NULL
UNION ALL
SELECT 'permission hilang', op.id, op.name FROM tmp_rbac_old_permissions op
LEFT JOIN `auth_permissions` p ON p.id = op.id AND p.name = op.name WHERE p.id IS NULL;

-- Cek 3: jumlah baris akhir
SELECT (SELECT COUNT(*) FROM `auth_groups`) AS auth_groups_harus_5,
       (SELECT COUNT(*) FROM `auth_permissions`) AS auth_permissions_harus_633,
       (SELECT COUNT(*) FROM `auth_groups_permissions`) AS auth_groups_permissions_harus_831,
       (SELECT COUNT(*) FROM `c_menus` WHERE `category_id` = 1) AS c_menus_backend_harus_131,
       (SELECT COUNT(*) FROM `settingparameters`) AS settingparameters;

DROP TEMPORARY TABLE IF EXISTS tmp_rbac_old_groups;
DROP TEMPORARY TABLE IF EXISTS tmp_rbac_old_permissions;
