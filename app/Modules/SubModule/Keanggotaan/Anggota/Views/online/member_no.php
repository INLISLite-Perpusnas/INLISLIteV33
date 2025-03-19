<form method="get" action="<?=$action?>">
	<input type="hidden" name="slug" value="<?=$slug?>">
	<div class="select-wrapper input-group mb-3">
		<select class="form-control select2" name="member_no" id="member_no" style="min-width:360px">
			<option value="">Nomor Anggota</option>
			<?php foreach (get_dropdown('t_anggota',"MemberNo IS NOT NULL",'MemberNo','name') as $row) : ?>
				<option value="<?= $row->code ?>" <?=($row->code == $member_no)?'selected':''?>><?= $row->code ?> <?= $row->text ?></option>
			<?php endforeach; ?>
		</select>
		<div class="input-group-append">
			<button class="btn btn-shadow btn bg-corporate-primary2 text-white" type="submit"><i class="fa fa-check-circle"></i> Pilih</button>
		</div>
	</div> 
</form>