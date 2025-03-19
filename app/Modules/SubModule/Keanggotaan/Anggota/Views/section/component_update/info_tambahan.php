<div class="row">
	<div class="col-md-12">
		<div id="accordion_tambahan" class="accordion-wrapper mb-3">
			<div class="card">
				<div class="card-header-tab card-header">
					<button type="button" data-toggle="collapse" data-target="#collapse_tambahan" aria-expanded="true" aria-controls="collapse_tambahan" class="text-left m-0 p-0 btn btn-link">
						<h5 class="m-0 p-0">
							<i class="header-icon lnr-layers icon-gradient bg-primary"></i> Info Tambahan
						</h5>
					</button>
				</div>
				<div data-parent="#accordion_tambahan" id="collapse_tambahan" class="collapse" style="">
					<div class="card-body">
						<div class="form-row">
							<div class="col-md-12">
								<h5>Pekerjaan</h5>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label>Unit Kerja</label>
									<select class="form-control" name="Job_id" id="Job_id" tabindex="-1" aria-hidden="true">
										<?php foreach (get_ref_table('master_pekerjaan', 'id,Pekerjaan',null,'data') as $row): ?>
											<option value="<?=$row->id?>"> <?=$row->Pekerjaan?></option>
										<?php endforeach;?>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="name">Nama Institusi</label>
									<div>
										<input type="text" class="form-control" id="frm_create_InstitutionName" name="InstitutionName" placeholder="Nama Institusi" value="<?=set_value('InstitutionName', $anggota->InstitutionName);?>" />
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="name">Alamat Institusi</label>
									<div>
										<input type="text" class="form-control" id="frm_create_InstitutionAddress" name="InstitutionAddress" placeholder="Alamat Institusi" value="<?=set_value('InstitutionAddress', $anggota->InstitutionAddress);?>" />
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="name">Telepon Institusi</label>
									<div>
										<input type="text" class="form-control" id="frm_create_InstitutionPhone" name="InstitutionPhone" placeholder="Telepon Institusi" value="<?=set_value('InstitutionPhone', $anggota->InstitutionPhone);?>" />
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-12">
								<h5>Pendidikan</h5>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label>Pendidikan</label>
									<select class="form-control" name="JenjangPendidikan_id" id="JenjangPendidikan_id" tabindex="-1" aria-hidden="true">
										<?php foreach (get_ref_table('master_pendidikan', 'id, Nama',null,'data') as $row): ?>
											<option value="<?=$row->id?>"><?=$row->Nama?></option>
										<?php endforeach;?>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>