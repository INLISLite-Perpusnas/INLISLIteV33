# Laporan Kandidat Foreign Key — `inlislite_v33_progo`

Tanggal analisis: 2026-11-03. Sumber koneksi: `.env` (MySQL 8.4, `localhost:3306`).

## Ringkasan

| Item | Jumlah |
|---|---|
| Tabel (BASE TABLE) | 165 (161 InnoDB, 4 MyISAM) |
| Foreign key yang sudah ada | 7 |
| Kandidat relasi dianalisis | 260 |
| A. Siap dibuat (termasuk eks-bagian B setelah tipe disamakan) | 110 (+4 perlu konfirmasi) |
| B. Tipe kolom disamakan (FK-nya masuk bagian A) | 14 kolom |
| C. Ada data yatim (orphan), perlu dibersihkan dulu | 23 |
| D. Relasi `Branch_id` → `branchs.ID` | 122 (56 bersih) |

**Metode.** Kandidat diambil dari kolom berpola `*_id` / `*ID` lalu dipetakan ke tabel induk berdasarkan nama dan konvensi INLISLite. Tiap kandidat dicek: engine InnoDB, kecocokan tipe (termasuk `UNSIGNED`), dan jumlah baris anak yang nilainya tidak ada di tabel induk (orphan). Nilai `NULL` tidak dihitung orphan.

**Catatan.** Pemetaan berbasis nama. Cek ulang relasi yang ambigu (mis. `catalogs.Member_id`, `catalogfiles.Member_id`, `historydata.Member_id` — bisa jadi merujuk ke `users`, bukan `members`). Backup DB sebelum menjalankan SQL.

## FK yang sudah ada

- `opaclogs_keyword.OpaclogsId → opaclogs.ID`
- `perpus_keliling_dokumentasi.jadwal_id → perpus_keliling_jadwal.id`
- `perpus_keliling_jadwal.lokasi_id → perpus_keliling_lokasi.id`
- `perpus_keliling_jadwal.unit_id → perpus_keliling_unit.id`
- `perpus_keliling_jadwal_petugas.jadwal_id → perpus_keliling_jadwal.id`
- `perpus_keliling_jadwal_petugas.petugas_id → perpus_keliling_petugas.id`
- `perpus_keliling_unit_foto.unit_id → perpus_keliling_unit.id`

## A. Siap dibuat

Tipe cocok (setelah bagian B dijalankan) dan tidak ada orphan.

**File SQL:** [app/Database/fk_bagian_a_tambah_foreign_key.sql](../app/Database/fk_bagian_a_tambah_foreign_key.sql) — sudah diuji di salinan database: semua FK terbentuk tanpa error. FK per tabel digabung dalam satu `ALTER`. Kolom nullable memakai `ON DELETE SET NULL`, kolom `NOT NULL` memakai `ON DELETE RESTRICT`.

| Tabel anak | Kolom | Tabel induk | Null? | Baris | Asal |
|---|---|---|---|---|---|
| auth_groups_permissions | group_id | auth_groups.id | NO | 821 | A |
| auth_groups_permissions | permission_id | auth_permissions.id | NO | 821 | A |
| auth_groups_users | group_id | auth_groups.id | NO | 119 | A |
| auth_tokens | user_id | users.id | NO | 0 | A |
| auth_users_permissions | permission_id | auth_permissions.id | NO | 17 | A |
| bacaditempat | collection_id | collections.ID | NO | 0 | B |
| bacaditempat | Location_Id | locations.ID | YES | 0 | A |
| bacaditempat | Member_id | members.ID | YES | 0 | A |
| bacaditempat_locations | Location_ID | locations.ID | YES | 0 | A |
| bookinglogs | collectionId | collections.ID | YES | 1 | B |
| c_menus | category_id | c_categories.id | NO | 187 | B |
| catalog_embeddings | Catalog_id | catalogs.ID | NO | 21852 | A |
| catalogfiles | Catalog_id | catalogs.ID | NO | 0 | A |
| catalogs | Location_id | locations.ID | YES | 21936 | A |
| catalogs | Worksheet_id | worksheets.ID | NO | 21936 | A |
| catalogstaging | Worksheet_id | worksheets.ID | YES | 0 | A |
| collectioncategorysdefault | JenisAnggota_id | jenis_anggota.id | NO | 11 | A |
| collectioncategorysloanhari | Peminjaman_hari_id | peraturan_peminjaman_hari.ID | YES | 4 | A |
| collectionloanextends | Collection_id | collections.ID | NO | 18 | B |
| collectionloanextends | CollectionLoan_id | collectionloans.ID | NO | 18 | B |
| collectionloanextends | CollectionLoanItem_id | collectionloanitems.ID | NO | 18 | A |
| collectionloanextends | Member_id | members.ID | NO | 18 | A |
| collectionloanitems | Collection_id | collections.ID | NO | 12257 | B |
| collectionloans | LocationLibrary_id | location_library.ID | YES | 11521 | A |
| collectionmedias | Worksheet_id | worksheets.ID | YES | 29 | A |
| collectionrulesitems | CollectionRules_id | collectionrules.ID | NO | 0 | A |
| collections | Catalog_id | catalogs.ID | YES | 36220 | A |
| collections | Location_Library_id | location_library.ID | YES | 36220 | A |
| collections | Partner_id | partners.ID | YES | 36220 | A |
| collections | Rule_id | collectionrules.ID | YES | 36220 | A |
| collections | Status_id | collectionstatus.ID | YES | 36220 | A |
| colloclib | Col_id | collections.ID | NO | 0 | B |
| colloclib | LocLib_id | location_library.ID | NO | 0 | A |
| departments | ParentID | departments.ID | YES | 24 | A |
| djkn_data | Collection_id | collections.ID | YES | 0 | B |
| fielddatas | Field_id | fields.ID | NO | 866 | A |
| fieldindicator1s | Field_id | fields.ID | NO | 302 | A |
| fieldindicator2s | Field_id | fields.ID | NO | 254 | A |
| fields | Format_id | formats.ID | NO | 138 | A |
| fields | Group_id | fieldgroups.ID | NO | 138 | A |
| groupguesses | Location_ID | locations.ID | YES | 631 | A |
| groupguesses | LocationLoans_ID | location_library.ID | YES | 631 | A |
| groupguesses | TujuanKunjungan_ID | tujuan_kunjungan.ID | YES | 631 | A |
| kabupaten | PropinsiID | propinsi.ID | NO | 498 | A |
| kriteria_koleksi | catalog_id | catalogs.ID | YES | 9 | A |
| layanan_perpustakaan | location_id | locations.ID | YES | 0 | A |
| librarysearchcriteria | LIBRARYID | library.ID | NO | 0 | A |
| location_library_default | JenisAnggota_id | jenis_anggota.id | YES | 6 | A |
| location_library_default | Location_Library_id | location_library.ID | YES | 6 | A |
| locations | LocationLibrary_id | location_library.ID | YES | 7 | A |
| lockers | loker_id | master_loker.ID | YES | 0 | A |
| logsdownload | catalogfilesID | catalogfiles.ID | YES | 0 | A |
| master_edisi_serial | Catalog_id | catalogs.ID | YES | 0 | A |
| memberguesses | JenisKelamin_id | jenis_kelamin.ID | YES | 85015 | A |
| memberguesses | Location_Id | locations.ID | YES | 85015 | A |
| memberguesses | LOCATIONLOANS_ID | location_library.ID | YES | 85015 | A |
| memberguesses | PendidikanTerakhir_id | master_pendidikan.id | YES | 85015 | A |
| memberguesses | Profesi_id | master_pekerjaan.id | YES | 85015 | A |
| memberguesses | Status_id | status_anggota.id | YES | 85015 | A |
| memberguesses | TujuanKunjungan_Id | tujuan_kunjungan.ID | YES | 85015 | A |
| memberloanauthorizecategory | CategoryLoan_id | collectioncategorys.ID | NO | 39 | A |
| memberloanauthorizelocation | LocationLoan_id | location_library.ID | NO | 15 | A |
| members | Agama_id | agama.ID | YES | 3697 | A |
| members | EducationLevel_id | master_pendidikan.id | YES | 3697 | A |
| members | Fakultas_id | master_fakultas.id | YES | 3697 | A |
| members | JenisAnggota_id | jenis_anggota.id | YES | 3697 | A |
| members | JenisPermohonan_id | jenis_permohonan.ID | YES | 3697 | A |
| members | Job_id | master_pekerjaan.id | YES | 3697 | A |
| members | Jurusan_id | master_jurusan.id | YES | 3697 | A |
| members | MaritalStatus_id | master_status_perkawinan.id | YES | 3697 | A |
| members | ProgramStudi_id | master_program_studi.id | YES | 3697 | A |
| members | Sex_id | jenis_kelamin.ID | YES | 3697 | A |
| members | StatusAnggota_id | status_anggota.id | YES | 3697 | A |
| members | UnitKerja_id | departments.ID | YES | 3697 | A |
| membersonline | LocationLoanId | location_library.ID | YES | 0 | A |
| pelanggaran | Collection_id | collections.ID | YES | 18 | B |
| pelanggaran | CollectionLoan_id | collectionloans.ID | YES | 18 | B |
| pelanggaran | CollectionLoanItem_id | collectionloanitems.ID | YES | 18 | A |
| pelanggaran | JenisDenda_id | jenis_denda.ID | YES | 18 | A |
| pelanggaran | JenisPelanggaran_id | jenis_pelanggaran.ID | YES | 18 | A |
| pelanggaran | Member_id | members.ID | YES | 18 | A |
| pengiriman | Collection_ID | collections.ID | NO | 0 | B |
| perpus_keliling_petugas | user_id | users.id | NO | 0 | A |
| readinlocation | LocationId | locations.ID | NO | 0 | A |
| refferenceitems | Refference_id | refferences.ID | NO | 1417 | A |
| refferences | Format_id | formats.ID | NO | 139 | A |
| serial_articlefiles | Articles_id | serial_articles.id | NO | 0 | A |
| serial_articles | Catalog_id | catalogs.ID | YES | 0 | A |
| serial_articles_repeatable | serial_article_ID | serial_articles.id | NO | 0 | A |
| settingcatalogdetail | Field_id | fields.ID | NO | 18 | A |
| stockopnamedetail | CollectionID | collections.ID | NO | 0 | B |
| stockopnamedetail | CurrentCollectionRuleID | collectionrules.ID | YES | 0 | A |
| stockopnamedetail | CurrentLocationID | locations.ID | YES | 0 | A |
| stockopnamedetail | CurrentStatusID | collectionstatus.ID | YES | 0 | A |
| stockopnamedetail | PrevCollectionRuleID | collectionrules.ID | YES | 0 | A |
| stockopnamedetail | PrevLocationID | locations.ID | YES | 0 | A |
| stockopnamedetail | PrevStatusID | collectionstatus.ID | YES | 0 | A |
| stockopnamedetail | StockOpnameID | stockopname.ID | NO | 0 | A |
| sumbangan_koleksi | Collection_id | collections.ID | YES | 0 | B |
| sumbangan_koleksi | Sumbangan_id | sumbangan.ID | YES | 0 | A |
| survey_isian | Survey_Pertanyaan_id | survey_pertanyaan.ID | YES | 0 | A |
| survey_pertanyaan | Survey_id | survey.ID | YES | 2 | A |
| survey_pilihan | Survey_Pertanyaan_id | survey_pertanyaan.ID | YES | 3 | A |
| survey_pilihan_sesi | Survey_Pilihan_id | survey_pilihan.ID | YES | 2 | A |
| users | member_id | members.ID | YES | 31 | A |
| worksheetfielditems | Refference_id | refferences.ID | YES | 263 | A |
| worksheetfielditems | WorksheetField_id | worksheetfields.ID | NO | 263 | A |
| worksheetfields | Field_id | fields.ID | YES | 766 | A |
| worksheetfields | Worksheet_id | worksheets.ID | YES | 766 | A |
| worksheets | Format_id | formats.ID | NO | 18 | A |

Perlu konfirmasi (dikomentari di file SQL; `Member_id` mungkin merujuk `users`, semua nilainya `NULL`/tabel kosong): `catalogfiles.Member_id`, `catalogs.Member_id`, `historydata.Member_id`, `serial_articlefiles.Member_id`.

## B. Samakan tipe kolom (prasyarat bagian A)

**File SQL:** [app/Database/fk_bagian_b_samakan_tipe.sql](../app/Database/fk_bagian_b_samakan_tipe.sql). File ini juga menghapus tabel tak terpakai `t_keranjanganggota`, `t_pemesanankoleksi`, `t_perpanjangan`, `t_sumbangan`, `t_usulankoleksi`. `t_template` tidak dihapus karena masih dipakai cetak kartu anggota.

| Kolom | Tipe lama | Tipe baru |
|---|---|---|
| `bacaditempat.collection_id` | int | int unsigned |
| `bookinglogs.collectionId` | int | int unsigned |
| `collectionloanextends.Collection_id` | int | int unsigned |
| `collectionloanitems.Collection_id` | int | int unsigned |
| `colloclib.Col_id` | int | int unsigned |
| `djkn_data.Collection_id` | int | int unsigned |
| `pelanggaran.Collection_id` | int | int unsigned |
| `stockopnamedetail.CollectionID` | int | int unsigned |
| `sumbangan_koleksi.Collection_id` | int | int unsigned |
| `c_menus.category_id` | int | mediumint unsigned |
| `c_references.menu_id` | int | mediumint unsigned |
| `collectionloanextends.CollectionLoan_id` | varchar(255) | bigint |
| `pelanggaran.CollectionLoan_id` | varchar(255) | bigint |
| `pengiriman.Collection_ID` | double | int unsigned |

Data sudah dicek: tidak ada nilai negatif, semua `CollectionLoan_id` numerik, tabel `pengiriman` kosong. `c_references.menu_id` sudah disamakan tipenya, tetapi FK-nya menunggu 3 orphan (`menu_id = 119`) dibersihkan.

## C. Ada data yatim (orphan)

Relasi valid secara struktur, tapi ada nilai anak yang tidak ditemukan di induk. Bersihkan dulu: set `NULL` (jika kolom nullable), hapus baris, atau tambahkan baris induk yang hilang.

| Relasi | Null? | Baris orphan / total | Nilai orphan terbanyak |
|---|---|---|---|
| `collections.Category_id` → `collectioncategorys.ID` | YES | 2452 / 36220 | `9`×2053, `13`×273, `14`×126 |
| `catalog_ruas.CatalogId` → `catalogs.ID` | NO | 508 / 367956 | `20820`×20, `20819`×20, `20712`×20 |
| `collectionloanitems.member_id` → `members.ID` | NO | 136 / 12257 | `2105`×19, `2045`×15, `2010`×10 |
| `collectionloanitems.CollectionLoan_id` → `collectionloans.ID` | NO | 115 / 12257 | `122011500002`×2, `123031700003`×2, `122061000005`×2 |
| `auth_groups_users.user_id` → `users.id` | NO | 114 / 119 | `94`×3, `157`×1, `164`×1 |
| `collectionloans.Member_id` → `members.ID` | NO | 112 / 11521 | `2105`×15, `2045`×10, `4146`×9 |
| `requestcatalog.WorksheetID` → `worksheets.ID` | YES | 109 / 119 | `0`×109 |
| `memberloanauthorizecategory.Member_id` → `members.ID` | NO | 18 / 39 | `2`×6, `3`×6, `1`×3 |
| `auth_users_permissions.user_id` → `users.id` | NO | 17 / 17 | `272`×17 |
| `collections.Media_id` → `collectionmedias.ID` | YES | 13 / 36220 | `41`×13 |
| `auth_logins.user_id` → `users.id` | YES | 9 / 220 | `294`×6, `291`×3 |
| `memberloanauthorizelocation.Member_id` → `members.ID` | NO | 8 / 15 | `1`×2, `2`×2, `3`×2 |
| `collections.Source_id` → `collectionsources.ID` | YES | 7 / 36220 | `11`×7 |
| `requestcatalog.MemberID` → `members.ID` | YES | 7 / 119 | `1517`×4, `1559`×1, `1572`×1 |
| `master_loker.locations_id` → `locations.ID` | YES | 5 / 5 | `3`×2, `1`×1, `2`×1 |
| `member_perpanjangan.Member_id` → `members.ID` | YES | 2 / 57 | `2045`×1, `4044`×1 |
| `members.IdentityType_id` → `master_jenis_identitas.id` | YES | 2 / 3697 | `2`×2 |
| `sumbangan.Member_id` → `members.ID` | YES | 2 / 2 | `2`×1, `1492`×1 |
| `collectioncategorysdefault.CollectionCategory_id` → `collectioncategorys.ID` | YES | 1 / 11 | `9`×1 |
| `collectioncategorysloanhari.Category_id` → `collectioncategorys.ID` | YES | 1 / 4 | `9`×1 |
| `collections.Location_id` → `locations.ID` | YES | 1 / 36220 | `477`×1 |
| `members.Kelas_id` → `kelas_siswa.id` | YES | 1 / 3697 | `0`×1 |
| `members.JenjangPendidikan_id` → `master_jenjang_pendidikan.ID` | YES | 1 / 3697 | `8`×1 |

Temuan penting:
- `collections.Category_id`: 2.452 eksemplar merujuk kategori `9`, `13`, `14` yang tidak ada di `collectioncategorys`. Kemungkinan kategori terhapus — lebih aman dibuat ulang daripada mengosongkan data.
- `collectionloans` / `collectionloanitems`: ada peminjaman milik anggota yang sudah dihapus, dan 115 item pinjaman tanpa header. Ini data histori; FK dengan `RESTRICT` akan memaksa anggota tidak bisa dihapus selama punya riwayat.
- `catalog_ruas.CatalogId`: 508 baris ruas MARC milik katalog yang sudah dihapus — aman dihapus.
- `requestcatalog.WorksheetID` dan `members.Kelas_id` memakai `0` sebagai 'kosong' — ubah ke `NULL`.
- `auth_*.user_id`: sisa data user yang sudah dihapus — aman dihapus.

Contoh pembersihan:
```sql
DELETE r FROM catalog_ruas r LEFT JOIN catalogs c ON c.ID = r.CatalogId WHERE c.ID IS NULL;
UPDATE requestcatalog SET WorksheetID = NULL WHERE WorksheetID = 0;
UPDATE members SET Kelas_id = NULL WHERE Kelas_id = 0;
DELETE g FROM auth_groups_users g LEFT JOIN users u ON u.id = g.user_id WHERE u.id IS NULL;
```

## D. Relasi `Branch_id`

Ada 122 tabel dengan kolom `Branch_id`. Hanya 56 yang bersih. Mayoritas data master memakai `Branch_id = 20986` (tidak ada di `branchs`, yang hanya berisi 43 baris ID 1–43) atau `0`. Kolom ini tampaknya penanda instalasi/tenant warisan, bukan relasi aktif.

**Rekomendasi:** jangan buat FK `Branch_id` sekarang. Putuskan dulu apakah kolom ini masih dipakai. Jika ya, normalisasi nilai `20986`/`0` jadi `NULL` atau ID cabang yang valid.

| Tabel | Orphan / total | Nilai |
|---|---|---|
| agama | 8 / 8 | `20986`×8 |
| auth_groups | 5 / 5 | `0`×5 |
| auth_groups_permissions | 821 / 821 | `0`×821 |
| cardformats | 6 / 6 | `20986`×6 |
| collectioncategorys | 6 / 6 | `20986`×4, `0`×2 |
| collectionloanitems | 2 / 12257 | `0`×2 |
| collectionloans | 1 / 11521 | `0`×1 |
| collectionmedias | 29 / 29 | `20986`×29 |
| collectionrules | 3 / 3 | `20986`×3 |
| collectionsources | 7 / 7 | `20986`×7 |
| collectionstatus | 11 / 11 | `20986`×11 |
| currency | 184 / 184 | `20986`×184 |
| departments | 24 / 24 | `20986`×24 |
| fielddatas | 866 / 866 | `20986`×866 |
| fieldgroups | 2 / 2 | `20986`×2 |
| fieldindicator1s | 302 / 302 | `20986`×302 |
| fieldindicator2s | 254 / 254 | `20986`×254 |
| fields | 138 / 138 | `20986`×138 |
| formats | 2 / 2 | `20986`×2 |
| holidays | 17 / 17 | `0`×16, `20986`×1 |
| jenis_denda | 5 / 5 | `20986`×5 |
| jenis_kelamin | 2 / 2 | `20986`×2 |
| jenis_pelanggaran | 4 / 4 | `20986`×4 |
| jenis_permohonan | 2 / 2 | `20986`×2 |
| kabupaten | 498 / 498 | `20986`×498 |
| kata_sandang | 9 / 9 | `20986`×9 |
| kelas_siswa | 5 / 5 | `0`×5 |
| kriteria_koleksi | 9 / 9 | `20986`×9 |
| master_jam_buka | 7 / 7 | `20986`×7 |
| master_jenis_identitas | 4 / 4 | `20986`×4 |
| master_kelas_besar | 12 / 12 | `20986`×12 |
| master_kependudukan | 2 / 2 | `20986`×2 |
| master_loker | 5 / 5 | `20986`×5 |
| master_pelanggaran_locker | 3 / 3 | `20986`×3 |
| master_range_umur | 7 / 8 | `20986`×7 |
| master_status_perkawinan | 4 / 4 | `20986`×4 |
| memberguesses | 1 / 85015 | `0`×1 |
| memberrules | 2 / 2 | `20986`×2 |
| members | 1 / 3697 | `0`×1 |
| members_form_list | 34 / 34 | `20986`×34 |
| members_info_form | 53 / 53 | `20986`×53 |
| members_loan_form | 17 / 17 | `20986`×17 |
| members_loanreturn_form | 16 / 16 | `20986`×16 |
| members_online_form | 99 / 99 | `20986`×99 |
| members_online_form_edit | 28 / 28 | `20986`×28 |
| menu | 206 / 206 | `20986`×206 |
| propinsi | 33 / 33 | `20986`×33 |
| refferenceitems | 1417 / 1417 | `20986`×1417 |
| refferences | 139 / 139 | `20986`×139 |
| requestcatalog | 119 / 119 | `20986`×119 |
| settingcatalogdetail | 18 / 18 | `20986`×18 |
| settingparameters | 134 / 160 | `20986`×134 |
| status_anggota | 6 / 6 | `20986`×6 |
| survey | 1 / 2 | `20986`×1 |
| survey_pertanyaan | 1 / 2 | `20986`×1 |
| survey_pilihan | 3 / 3 | `20986`×3 |
| survey_pilihan_sesi | 2 / 2 | `20986`×2 |
| tempnoinduk | 1519 / 1519 | `20986`×1519 |
| tujuan_kunjungan | 7 / 8 | `20986`×7 |
| users | 2 / 31 | `0`×1, `20986`×1 |
| worksheetfielditems | 263 / 263 | `20986`×263 |
| worksheetfields | 717 / 766 | `20986`×717 |
| worksheets | 17 / 18 | `20986`×17 |

Tabel dengan `Branch_id` bersih: `bacaditempat`, `bacaditempat_locations`, `bookinglogs`, `catalog_ruas`, `catalogfiles`, `catalogs`, `catalogstaging`, `collectioncategorysloanhari`, `collectionloanextends`, `collectionlocations`, `collectionrulesitems`, `collections`, `colloclib`, `djkn_data`, `groupguesses`, `historydata`, `jenis_perpustakaan`, `judul_koleksi`, `kelompok_pelanggaran`, `library`, `librarysearchcriteria`, `location_library`, `locations`, `lockers`, `logsdownload`, `master_djkn`, `master_edisi_serial`, `master_fakultas`, `master_jenjang_pendidikan`, `master_jurusan`, `master_pekerjaan`, `master_pendidikan`, `master_program_studi`, `member_perpanjangan`, `memberloanauthorizecategory`, `memberloanauthorizelocation`, `membersonline`, `migration`, `partners`, `pelanggaran`, `pengiriman`, `peraturan_peminjaman_hari`, `peraturan_peminjaman_tanggal`, `publishers`, `readinlocation`, `rfid_temp`, `serial_articlefiles`, `serial_articles`, `serial_articles_repeatable`, `stockopname`, `stockopnamedetail`, `sumbangan`, `sumbangan_koleksi`, `survey_isian`, `t_template`, `membersonline`.

## E. Tidak bisa / tidak disarankan

- **Tabel MyISAM** (`category`, `inbox`, `phones`, `smslogs`): MyISAM tidak mendukung FK. Konversi ke InnoDB dulu (`ALTER TABLE x ENGINE=InnoDB`).
- **Kolom rujukan non-PK**: `bookinglogs.memberId`, `collections.BookingMemberID` (VARCHAR, kemungkinan `members.MemberNo`), `opaclogs.User_id`, `logsdownload.User_id` (VARCHAR). FK hanya bisa jika kolom induk punya UNIQUE index dan tipe sama.
- **Tujuan tidak jelas**: `c_logs.ref_id` (polimorfik), `memberguesses.MasaBerlaku_id`, `catalogs.PRESERVASI_ID`, `users.npp_*_id`, `master_djkn.Option_id`, `collections.deposit_*_ID`.
- **Tabel log/histori** (`opaclogs`, `bookinglogs`, `historydata`, `c_logs`): FK membuat penghapusan induk gagal atau menghapus jejak audit. Pertimbangkan tanpa FK.

## Urutan eksekusi yang disarankan

1. Backup database (`mysqldump`).
2. Jalankan `fk_bagian_b_samakan_tipe.sql` (hapus tabel `t_*` + samakan tipe).
3. Jalankan `fk_bagian_a_tambah_foreign_key.sql`.
4. Bersihkan orphan per relasi (bagian C), lalu buat FK-nya.
5. Putuskan nasib `Branch_id` (bagian D) sebelum menyentuhnya.
6. Uji aplikasi: hapus/ubah data master dan transaksi, cek tidak ada error constraint yang tak terduga.
