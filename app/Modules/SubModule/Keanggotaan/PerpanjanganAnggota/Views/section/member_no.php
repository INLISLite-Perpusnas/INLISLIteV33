<?php
$request = service('request');
$slug = $request->getGet('slug') ?? 'peminjaman';
?>
<form method="get" action="<?= base_url('perpanjangan-anggota/create') ?>">
	<input type="hidden" name="slug" value="<?= $slug ?>">
	<div class="form-row">
		<div class="col-md-9 col-sm-8">
			<div class="form-group">
				<select class="form-control select2" name="member_no" id="member_no" tabindex="-1" aria-hidden="true"
					placeholder="Nomor Anggota" style="width:100%">
					<option value="">Nomor Anggota</option>
					<?php foreach (get_dropdown('t_anggota', null, 'MemberNo', 'name') as $row): ?>
						<option value="<?= $row->code ?>" <?= ($row->code == user()->username) ? 'selected' : '' ?>>
							<?= $row->code ?> 	<?= $row->text ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="col-md-3 col-sm-4">
			<div class="position-relative form-group">
				<button type="submit" class="btn-shadow btn btn-primary btn-lg btn-block"><i
						class="fa fa-search"></i></button>
			</div>
		</div>
	</div>
</form>