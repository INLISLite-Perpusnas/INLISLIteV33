<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
	<li class="nav-item">
		<a class="nav-link <?=($slug == 'profile')?'active':''?>" href="<?=base_url('anggota/online?slug=profile')?>">
			<span><i class="header-icon lnr-user"></i> Profil Saya</span>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?=($slug == 'loan')?'active':''?>" href="<?=base_url('anggota/online?slug=loan')?>">
			<span><i class="header-icon lnr-enter"></i> Daftar Peminjaman</span>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?=($slug == 'reserve')?'active':''?>" href="<?=base_url('anggota/online?slug=reserve')?>">
			<span><i class="header-icon lnr-cart"></i> Daftar Pemesanan</span>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?=($slug == 'request')?'active':''?>" href="<?=base_url('anggota/online?slug=request')?>">
			<span><i class="header-icon lnr-bookmark"></i> Daftar Usulan</span>
		</a>
	</li>

</ul>