<?php
/**
 * Field form Jenis Anggota, dipakai bersama oleh modal tambah dan modal edit.
 * $prefix: awalan id elemen ('frm_add' atau 'frm_update').
 * Kolom audit (CreateBy, CreateDate, UpdateBy, dst.) dan KIILastUploadDate diisi sistem, jadi tidak ada di form.
 */
$id = static fn (string $field): string => $prefix . '_' . $field;
$satuan = ['Hari', 'Minggu', 'Bulan', 'Tahun'];
$tipe = ['Konstan', 'Berkelipatan'];
?>
<h6 class="text-primary mb-3"><i class="fas fa-id-card"></i> Keanggotaan</h6>
<div class="form-row">
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('jenisanggota') ?>">Jenis Anggota</label>
			<input required type="text" maxlength="50" class="form-control" id="<?= $id('jenisanggota') ?>" name="jenisanggota" placeholder="Jenis Anggota" />
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('MasaBerlakuAnggota') ?>">Masa Berlaku (Hari)</label>
			<input required type="number" min="0" class="form-control" id="<?= $id('MasaBerlakuAnggota') ?>" name="MasaBerlakuAnggota" placeholder="Masa Berlaku" value="365" />
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('BiayaPendaftaran') ?>">Biaya Pendaftaran</label>
			<input required type="number" min="0" class="form-control" id="<?= $id('BiayaPendaftaran') ?>" name="BiayaPendaftaran" placeholder="Biaya Pendaftaran" value="0" />
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('BiayaPerpanjangan') ?>">Biaya Perpanjangan</label>
			<input required type="number" min="0" class="form-control" id="<?= $id('BiayaPerpanjangan') ?>" name="BiayaPerpanjangan" placeholder="Biaya Perpanjangan" value="0" />
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('UploadDokumenKeanggotaanOnline') ?>">Upload Dokumen Keanggotaan Online</label>
			<select class="form-control" id="<?= $id('UploadDokumenKeanggotaanOnline') ?>" name="UploadDokumenKeanggotaanOnline">
				<option value="1">Ya</option>
				<option value="0">Tidak</option>
			</select>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('active') ?>">Status</label>
			<select class="form-control" id="<?= $id('active') ?>" name="active">
				<option value="1">Aktif</option>
				<option value="0">Non Aktif</option>
			</select>
		</div>
	</div>
</div>

<h6 class="text-primary mb-3 mt-2"><i class="fas fa-book"></i> Peminjaman</h6>
<div class="form-row">
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('MaxPinjamKoleksi') ?>">Maksimal Pinjam Koleksi</label>
			<input required type="number" min="0" class="form-control" id="<?= $id('MaxPinjamKoleksi') ?>" name="MaxPinjamKoleksi" placeholder="Maksimal Pinjam Koleksi" />
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('MaxLoanDays') ?>">Maksimal Lama Pinjam (Hari)</label>
			<input required type="number" min="0" class="form-control" id="<?= $id('MaxLoanDays') ?>" name="MaxLoanDays" placeholder="Maksimal Lama Pinjam" />
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('DayPerpanjang') ?>">Maks Lama Perpanjangan (Hari)</label>
			<input required type="number" min="0" class="form-control" id="<?= $id('DayPerpanjang') ?>" name="DayPerpanjang" placeholder="Lama Perpanjangan" value="0" />
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('CountPerpanjang') ?>">Maks Banyaknya Perpanjang</label>
			<input required type="number" min="0" class="form-control" id="<?= $id('CountPerpanjang') ?>" name="CountPerpanjang" placeholder="Maks Perpanjang" value="0" />
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('WarningLoanDueDay') ?>">Jeda Peringatan Jatuh Tempo (Hari)</label>
			<input required type="number" min="0" class="form-control" id="<?= $id('WarningLoanDueDay') ?>" name="WarningLoanDueDay" placeholder="Jeda Peringatan" value="0" />
		</div>
	</div>
</div>

<h6 class="text-primary mb-3 mt-2"><i class="fas fa-money-bill"></i> Denda</h6>
<div class="form-row">
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('DendaType') ?>">Jenis Denda</label>
			<select required class="form-control" id="<?= $id('DendaType') ?>" name="DendaType">
				<?php foreach ($tipe as $value): ?>
					<option value="<?= $value ?>"><?= $value ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('DendaPerTenor') ?>">Jumlah Denda per Tenor</label>
			<input required type="number" min="0" class="form-control" id="<?= $id('DendaPerTenor') ?>" name="DendaPerTenor" placeholder="Jumlah Denda" value="0" />
		</div>
	</div>
	<div class="col-lg-4">
		<div class="form-group">
			<label for="<?= $id('DendaTenorJumlah') ?>">Tenor Denda</label>
			<input required type="number" min="0" step="any" class="form-control" id="<?= $id('DendaTenorJumlah') ?>" name="DendaTenorJumlah" placeholder="Tenor Denda" value="0" />
		</div>
	</div>
	<div class="col-lg-4">
		<div class="form-group">
			<label for="<?= $id('DendaTenorSatuan') ?>">Satuan Tenor Denda</label>
			<select required class="form-control" id="<?= $id('DendaTenorSatuan') ?>" name="DendaTenorSatuan">
				<?php foreach ($satuan as $value): ?>
					<option value="<?= $value ?>"><?= $value ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="form-group">
			<label for="<?= $id('DendaTenorMultiply') ?>">Pengali Tenor Denda</label>
			<input required type="number" min="0" class="form-control" id="<?= $id('DendaTenorMultiply') ?>" name="DendaTenorMultiply" placeholder="Pengali Tenor Denda" value="1" />
		</div>
	</div>
</div>

<h6 class="text-primary mb-3 mt-2"><i class="fas fa-user-slash"></i> Skorsing (Suspend)</h6>
<div class="form-row">
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('SuspendMember') ?>">Suspend Anggota</label>
			<select class="form-control" id="<?= $id('SuspendMember') ?>" name="SuspendMember">
				<option value="0">Tidak</option>
				<option value="1">Ya</option>
			</select>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('SuspendType') ?>">Jenis Skorsing</label>
			<select required class="form-control" id="<?= $id('SuspendType') ?>" name="SuspendType">
				<?php foreach ($tipe as $value): ?>
					<option value="<?= $value ?>"><?= $value ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('DaySuspend') ?>">Lama Skorsing (Hari)</label>
			<input required type="number" min="0" class="form-control" id="<?= $id('DaySuspend') ?>" name="DaySuspend" placeholder="Lama Skorsing" value="0" />
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('SuspendTenorJumlah') ?>">Tenor Skorsing</label>
			<input required type="number" min="0" step="any" class="form-control" id="<?= $id('SuspendTenorJumlah') ?>" name="SuspendTenorJumlah" placeholder="Tenor Skorsing" value="0" />
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('SuspendTenorSatuan') ?>">Satuan Tenor Skorsing</label>
			<select required class="form-control" id="<?= $id('SuspendTenorSatuan') ?>" name="SuspendTenorSatuan">
				<?php foreach ($satuan as $value): ?>
					<option value="<?= $value ?>"><?= $value ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label for="<?= $id('SuspendTenorMultiply') ?>">Pengali Tenor Skorsing</label>
			<input required type="number" min="0" class="form-control" id="<?= $id('SuspendTenorMultiply') ?>" name="SuspendTenorMultiply" placeholder="Pengali Tenor Skorsing" value="1" />
		</div>
	</div>
</div>
