<form method="get" action="<?=$action?>">
	<div class="select-wrapper input-group mb-3">
		<select class="form-control select2" name="member_no" id="member_no" style="min-width:360px">
			<option value="">Nomor Anggota</option>
			<?php foreach (get_ref_table('members','MemberNo, Fullname','MemberNo IS NOT NULL','data') as $row) : ?>
				<option value="<?= $row->MemberNo ?>" <?=$member_no == $row->MemberNo ? 'selected':''?>><?= $row->MemberNo ?> <?= $row->Fullname ?></option>
			<?php endforeach; ?>
		</select>
		<div class="input-group-append">
			<button class="btn btn-shadow btn bg-corporate-primary2 text-white" type="submit"><i class="fa fa-check-circle"></i> Pilih</button>
		</div>
	</div> 
</form>