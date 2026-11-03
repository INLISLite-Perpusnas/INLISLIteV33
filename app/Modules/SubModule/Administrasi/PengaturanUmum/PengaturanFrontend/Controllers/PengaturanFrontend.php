<?php

namespace PengaturanFrontend\Controllers;

class PengaturanFrontend extends \Base\Controllers\BaseController
{
	// Toggle fitur di settingparameters, per grup: Name => [label, nilai aktif, nilai nonaktif, default aktif?, keterangan]
	// Default dipakai bila baris setting belum ada (samakan dengan default di pemakainya).
	// Keterangan ditampilkan sebagai tooltip saat kursor diarahkan ke tombol.
	public const FEATURE_GROUPS = [
		'Cache' => [
			'is_opac_cache'      => ['Cache Halaman OPAC', '1', '0', false, 'Menyimpan sementara hasil halaman OPAC agar lebih cepat dimuat. Perubahan data katalog bisa terlambat tampil beberapa menit.'],
			'is_dashboard_cache' => ['Cache Dashboard', '1', '0', false, 'Menyimpan sementara data statistik dashboard admin agar lebih cepat dimuat. Angka bisa terlambat diperbarui beberapa menit.'],
			'is_home_cache'      => ['Cache Halaman Beranda', '1', '0', false, 'Menyimpan sementara data halaman beranda (koleksi, berita, statistik) agar lebih cepat dimuat. Perubahan data bisa terlambat tampil sesuai waktu cache.'],
			'is_statistik_cache' => ['Cache Statistik OPAC', '1', '0', false, 'Menyimpan sementara hasil halaman statistik OPAC (katalog, anggota, kunjungan, sirkulasi) agar lebih cepat dimuat. Angka bisa terlambat diperbarui sesuai waktu cache.'],
		],
		'Halaman Frontend' => [
			'is_berita'                => ['Berita & Pengumuman', '1', '0', true, 'Menampilkan section Berita & Pengumuman di halaman beranda.'],
			// Default aktif: Opac::drmEnabled() menganggap DRM aktif bila setting belum ada
			'isdrm'                    => ['DRM Konten Digital', '1', '0', true, 'Melindungi konten digital: bila aktif, konten digital hanya dapat dibaca setelah login dan tidak dapat diakses publik.'],
			'is_keanggotaan_online'    => ['Pendaftaran Keanggotaan Online', '1', '0', false, 'Membuka formulir pendaftaran anggota secara online di halaman frontend.'],
			'is_peminjaman_mandiri'    => ['Peminjaman Mandiri', '1', '0', true, 'Menampilkan layanan peminjaman koleksi mandiri oleh anggota di halaman frontend.'],
			'is_pengembalian_mandiri'  => ['Pengembalian Mandiri', '1', '0', true, 'Menampilkan layanan pengembalian koleksi mandiri oleh anggota di halaman frontend.'],
			'is_perpustakaan_keliling' => ['Perpustakaan Keliling', '1', '0', false, 'Mengaktifkan halaman layanan Perpustakaan Keliling di frontend.'],
			'is_sering_dibaca'         => ['Koleksi Sering Dibaca', '1', '0', false, 'Menampilkan section Koleksi Sering Dibaca di halaman beranda.'],
			'UsulanKoleksiLKD'         => ['Usulan Koleksi', 'True', 'False', false, 'Membuka formulir usulan koleksi oleh pemustaka di halaman frontend.'],
		],
	];

	// Lama cache (menit) per toggle cache: toggle => [nama setting, default menit]
	// Dibaca lewat setting_cache_ttl() di Opac, Dashboard, dan Home.
	public const CACHE_TTL = [
		'is_opac_cache'      => ['opac_cache_ttl', 60],
		'is_dashboard_cache' => ['dashboard_cache_ttl', 60],
		'is_home_cache'      => ['home_cache_ttl', 5],
		'is_statistik_cache' => ['statistik_cache_ttl', 60],
	];

	public function index()
	{
		$this->data['title'] = 'Pengaturan Cache & Halaman Frontend';

		if ($this->request->getMethod() === 'POST') {
			if (!is_allowed('master-pengaturan-frontend/edit', null, false)) {
				set_message('swal_icon', 'error');
				set_message('swal_title', 'Akses Ditolak');
				set_message('swal_text', lang('App.permission.not.have'));
				return redirect()->to('/master-pengaturan-frontend');
			}

			$rules = [];
			foreach (self::CACHE_TTL as $toggle => [$ttlName]) {
				$rules[$ttlName] = [
					'label' => 'Waktu ' . self::FEATURE_GROUPS['Cache'][$toggle][0],
					'rules' => 'required|is_natural_no_zero|less_than_equal_to[1440]',
				];
			}
			$this->validation->setRules($rules);
			if (!$this->validation->withRequest($this->request)->run()) {
				set_message('swal_icon', 'error');
				set_message('swal_title', 'Validasi Gagal');
				set_message('swal_text', implode(', ', $this->validation->getErrors()));
				return redirect()->to('/master-pengaturan-frontend');
			}

			$toggles = (array) ($this->request->getPost('feature_toggles') ?? []);
			foreach (self::FEATURE_GROUPS as $features) {
				foreach ($features as $name => [, $on, $off]) {
					$value = !empty($toggles[$name]) ? $on : $off;
					set_setting_parameter($name, $value);
					if ($name === 'is_sering_dibaca') {
						set_setting_parameter('show_sering_dibaca', $value);
					}
				}
			}
			foreach (self::CACHE_TTL as [$ttlName]) {
				set_setting_parameter($ttlName, (string) (int) $this->request->getPost($ttlName));
			}

			// Buang cache halaman agar perubahan (termasuk waktu cache baru) langsung berlaku
			cache()->delete('home_index_data');
			cache()->delete('dashboard_stats_data');
			cache()->deleteMatching('catalog_data_*');
			cache()->deleteMatching('opac_stat_*');

			set_message('swal_icon', 'success');
			set_message('swal_title', 'Berhasil');
			set_message('swal_text', 'Pengaturan Cache & Halaman Frontend berhasil disimpan');
			return redirect()->to('/master-pengaturan-frontend');
		}

		$groups = [];
		foreach (self::FEATURE_GROUPS as $group => $features) {
			foreach ($features as $name => [$label, $on, , $default, $description]) {
				$value = get_setting_parameter($name);
				$ttl = null;
				if (isset(self::CACHE_TTL[$name])) {
					[$ttlName, $ttlDefault] = self::CACHE_TTL[$name];
					$ttlValue = (int) get_setting_parameter($ttlName);
					$ttl = ['name' => $ttlName, 'value' => $ttlValue > 0 ? $ttlValue : $ttlDefault];
				}
				$groups[$group][$name] = [
					'label'       => $label,
					'description' => $description,
					'ttl'         => $ttl,
					'checked' => $value === null || $value === ''
						? $default
						: strtoupper(trim((string) $value)) === strtoupper($on),
				];
			}
		}
		$this->data['groups'] = $groups;
		echo view('PengaturanFrontend\Views\update', $this->data);
	}
}
