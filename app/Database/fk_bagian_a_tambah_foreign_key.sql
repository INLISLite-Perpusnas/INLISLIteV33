-- =====================================================================
-- Bagian A laporan foreign key (termasuk bagian B setelah tipe disamakan)
-- Referensi: docs/laporan-foreign-key.md
-- Database : inlislite_v33_progo
--
-- PRASYARAT: jalankan app/Database/fk_bagian_b_samakan_tipe.sql lebih dulu.
--
-- Aturan aksi:
--   - Kolom NULL     -> ON DELETE SET NULL
--   - Kolom NOT NULL -> ON DELETE RESTRICT (induk tidak bisa dihapus selama dirujuk)
--   - Semua          -> ON UPDATE CASCADE
--
-- Tidak termasuk: relasi Branch_id, relasi dengan data orphan (bagian C),
-- tabel t_* yang dihapus, dan c_references.menu_id (masih 3 orphan).
-- WAJIB backup database sebelum menjalankan.
-- =====================================================================

-- Menambah FK membangun ulang tabel. Beberapa tabel (mis. members.DateOfBirth)
-- berisi tanggal '0000-00-00' yang ditolak strict mode, jadi NO_ZERO_DATE
-- dilonggarkan hanya untuk sesi ini lalu dikembalikan di akhir.
SET @old_sql_mode = @@SESSION.sql_mode;
SET SESSION sql_mode = REPLACE(REPLACE(@@SESSION.sql_mode, 'NO_ZERO_IN_DATE', ''), 'NO_ZERO_DATE', '');

ALTER TABLE `auth_groups_permissions`
    ADD CONSTRAINT `fk_auth_groups_permissions_group_id` FOREIGN KEY (`group_id`) REFERENCES `auth_groups` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_auth_groups_permissions_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `auth_permissions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `auth_groups_users`
    ADD CONSTRAINT `fk_auth_groups_users_group_id` FOREIGN KEY (`group_id`) REFERENCES `auth_groups` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `auth_tokens`
    ADD CONSTRAINT `fk_auth_tokens_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `auth_users_permissions`
    ADD CONSTRAINT `fk_auth_users_permissions_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `auth_permissions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `bacaditempat`
    ADD CONSTRAINT `fk_bacaditempat_collection_id` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_bacaditempat_member_id` FOREIGN KEY (`Member_id`) REFERENCES `members` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_bacaditempat_location_id` FOREIGN KEY (`Location_Id`) REFERENCES `locations` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `bacaditempat_locations`
    ADD CONSTRAINT `fk_bacaditempat_locations_location_id` FOREIGN KEY (`Location_ID`) REFERENCES `locations` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `bookinglogs`
    ADD CONSTRAINT `fk_bookinglogs_collectionid` FOREIGN KEY (`collectionId`) REFERENCES `collections` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `c_menus`
    ADD CONSTRAINT `fk_c_menus_category_id` FOREIGN KEY (`category_id`) REFERENCES `c_categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `catalog_embeddings`
    ADD CONSTRAINT `fk_catalog_embeddings_catalog_id` FOREIGN KEY (`Catalog_id`) REFERENCES `catalogs` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `catalogfiles`
    ADD CONSTRAINT `fk_catalogfiles_catalog_id` FOREIGN KEY (`Catalog_id`) REFERENCES `catalogs` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `catalogs`
    ADD CONSTRAINT `fk_catalogs_location_id` FOREIGN KEY (`Location_id`) REFERENCES `locations` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_catalogs_worksheet_id` FOREIGN KEY (`Worksheet_id`) REFERENCES `worksheets` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `catalogstaging`
    ADD CONSTRAINT `fk_catalogstaging_worksheet_id` FOREIGN KEY (`Worksheet_id`) REFERENCES `worksheets` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `collectioncategorysdefault`
    ADD CONSTRAINT `fk_collectioncategorysdefault_jenisanggota_id` FOREIGN KEY (`JenisAnggota_id`) REFERENCES `jenis_anggota` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `collectioncategorysloanhari`
    ADD CONSTRAINT `fk_collectioncategorysloanhari_peminjaman_hari_id` FOREIGN KEY (`Peminjaman_hari_id`) REFERENCES `peraturan_peminjaman_hari` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `collectionloanextends`
    ADD CONSTRAINT `fk_collectionloanextends_collectionloan_id` FOREIGN KEY (`CollectionLoan_id`) REFERENCES `collectionloans` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_collectionloanextends_collectionloanitem_id` FOREIGN KEY (`CollectionLoanItem_id`) REFERENCES `collectionloanitems` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_collectionloanextends_collection_id` FOREIGN KEY (`Collection_id`) REFERENCES `collections` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_collectionloanextends_member_id` FOREIGN KEY (`Member_id`) REFERENCES `members` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `collectionloanitems`
    ADD CONSTRAINT `fk_collectionloanitems_collection_id` FOREIGN KEY (`Collection_id`) REFERENCES `collections` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `collectionloans`
    ADD CONSTRAINT `fk_collectionloans_locationlibrary_id` FOREIGN KEY (`LocationLibrary_id`) REFERENCES `location_library` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `collectionmedias`
    ADD CONSTRAINT `fk_collectionmedias_worksheet_id` FOREIGN KEY (`Worksheet_id`) REFERENCES `worksheets` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `collectionrulesitems`
    ADD CONSTRAINT `fk_collectionrulesitems_collectionrules_id` FOREIGN KEY (`CollectionRules_id`) REFERENCES `collectionrules` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `collections`
    ADD CONSTRAINT `fk_collections_catalog_id` FOREIGN KEY (`Catalog_id`) REFERENCES `catalogs` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_collections_partner_id` FOREIGN KEY (`Partner_id`) REFERENCES `partners` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_collections_rule_id` FOREIGN KEY (`Rule_id`) REFERENCES `collectionrules` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_collections_status_id` FOREIGN KEY (`Status_id`) REFERENCES `collectionstatus` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_collections_location_library_id` FOREIGN KEY (`Location_Library_id`) REFERENCES `location_library` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `colloclib`
    ADD CONSTRAINT `fk_colloclib_col_id` FOREIGN KEY (`Col_id`) REFERENCES `collections` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_colloclib_loclib_id` FOREIGN KEY (`LocLib_id`) REFERENCES `location_library` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `departments`
    ADD CONSTRAINT `fk_departments_parentid` FOREIGN KEY (`ParentID`) REFERENCES `departments` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `djkn_data`
    ADD CONSTRAINT `fk_djkn_data_collection_id` FOREIGN KEY (`Collection_id`) REFERENCES `collections` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `fielddatas`
    ADD CONSTRAINT `fk_fielddatas_field_id` FOREIGN KEY (`Field_id`) REFERENCES `fields` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `fieldindicator1s`
    ADD CONSTRAINT `fk_fieldindicator1s_field_id` FOREIGN KEY (`Field_id`) REFERENCES `fields` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `fieldindicator2s`
    ADD CONSTRAINT `fk_fieldindicator2s_field_id` FOREIGN KEY (`Field_id`) REFERENCES `fields` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `fields`
    ADD CONSTRAINT `fk_fields_format_id` FOREIGN KEY (`Format_id`) REFERENCES `formats` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_fields_group_id` FOREIGN KEY (`Group_id`) REFERENCES `fieldgroups` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `groupguesses`
    ADD CONSTRAINT `fk_groupguesses_tujuankunjungan_id` FOREIGN KEY (`TujuanKunjungan_ID`) REFERENCES `tujuan_kunjungan` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_groupguesses_locationloans_id` FOREIGN KEY (`LocationLoans_ID`) REFERENCES `location_library` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_groupguesses_location_id` FOREIGN KEY (`Location_ID`) REFERENCES `locations` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `kabupaten`
    ADD CONSTRAINT `fk_kabupaten_propinsiid` FOREIGN KEY (`PropinsiID`) REFERENCES `propinsi` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `kriteria_koleksi`
    ADD CONSTRAINT `fk_kriteria_koleksi_catalog_id` FOREIGN KEY (`catalog_id`) REFERENCES `catalogs` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `layanan_perpustakaan`
    ADD CONSTRAINT `fk_layanan_perpustakaan_location_id` FOREIGN KEY (`location_id`) REFERENCES `locations` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `librarysearchcriteria`
    ADD CONSTRAINT `fk_librarysearchcriteria_libraryid` FOREIGN KEY (`LIBRARYID`) REFERENCES `library` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `location_library_default`
    ADD CONSTRAINT `fk_location_library_default_location_library_id` FOREIGN KEY (`Location_Library_id`) REFERENCES `location_library` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_location_library_default_jenisanggota_id` FOREIGN KEY (`JenisAnggota_id`) REFERENCES `jenis_anggota` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `locations`
    ADD CONSTRAINT `fk_locations_locationlibrary_id` FOREIGN KEY (`LocationLibrary_id`) REFERENCES `location_library` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `lockers`
    ADD CONSTRAINT `fk_lockers_loker_id` FOREIGN KEY (`loker_id`) REFERENCES `master_loker` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `logsdownload`
    ADD CONSTRAINT `fk_logsdownload_catalogfilesid` FOREIGN KEY (`catalogfilesID`) REFERENCES `catalogfiles` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `master_edisi_serial`
    ADD CONSTRAINT `fk_master_edisi_serial_catalog_id` FOREIGN KEY (`Catalog_id`) REFERENCES `catalogs` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `memberguesses`
    ADD CONSTRAINT `fk_memberguesses_status_id` FOREIGN KEY (`Status_id`) REFERENCES `status_anggota` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_memberguesses_profesi_id` FOREIGN KEY (`Profesi_id`) REFERENCES `master_pekerjaan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_memberguesses_pendidikanterakhir_id` FOREIGN KEY (`PendidikanTerakhir_id`) REFERENCES `master_pendidikan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_memberguesses_jeniskelamin_id` FOREIGN KEY (`JenisKelamin_id`) REFERENCES `jenis_kelamin` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_memberguesses_locationloans_id` FOREIGN KEY (`LOCATIONLOANS_ID`) REFERENCES `location_library` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_memberguesses_location_id` FOREIGN KEY (`Location_Id`) REFERENCES `locations` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_memberguesses_tujuankunjungan_id` FOREIGN KEY (`TujuanKunjungan_Id`) REFERENCES `tujuan_kunjungan` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `memberloanauthorizecategory`
    ADD CONSTRAINT `fk_memberloanauthorizecategory_categoryloan_id` FOREIGN KEY (`CategoryLoan_id`) REFERENCES `collectioncategorys` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `memberloanauthorizelocation`
    ADD CONSTRAINT `fk_memberloanauthorizelocation_locationloan_id` FOREIGN KEY (`LocationLoan_id`) REFERENCES `location_library` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `members`
    ADD CONSTRAINT `fk_members_educationlevel_id` FOREIGN KEY (`EducationLevel_id`) REFERENCES `master_pendidikan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_members_sex_id` FOREIGN KEY (`Sex_id`) REFERENCES `jenis_kelamin` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_members_maritalstatus_id` FOREIGN KEY (`MaritalStatus_id`) REFERENCES `master_status_perkawinan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_members_job_id` FOREIGN KEY (`Job_id`) REFERENCES `master_pekerjaan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_members_jenispermohonan_id` FOREIGN KEY (`JenisPermohonan_id`) REFERENCES `jenis_permohonan` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_members_jenisanggota_id` FOREIGN KEY (`JenisAnggota_id`) REFERENCES `jenis_anggota` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_members_statusanggota_id` FOREIGN KEY (`StatusAnggota_id`) REFERENCES `status_anggota` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_members_agama_id` FOREIGN KEY (`Agama_id`) REFERENCES `agama` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_members_fakultas_id` FOREIGN KEY (`Fakultas_id`) REFERENCES `master_fakultas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_members_jurusan_id` FOREIGN KEY (`Jurusan_id`) REFERENCES `master_jurusan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_members_programstudi_id` FOREIGN KEY (`ProgramStudi_id`) REFERENCES `master_program_studi` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_members_unitkerja_id` FOREIGN KEY (`UnitKerja_id`) REFERENCES `departments` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `membersonline`
    ADD CONSTRAINT `fk_membersonline_locationloanid` FOREIGN KEY (`LocationLoanId`) REFERENCES `location_library` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `pelanggaran`
    ADD CONSTRAINT `fk_pelanggaran_collectionloan_id` FOREIGN KEY (`CollectionLoan_id`) REFERENCES `collectionloans` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_pelanggaran_collectionloanitem_id` FOREIGN KEY (`CollectionLoanItem_id`) REFERENCES `collectionloanitems` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_pelanggaran_jenispelanggaran_id` FOREIGN KEY (`JenisPelanggaran_id`) REFERENCES `jenis_pelanggaran` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_pelanggaran_jenisdenda_id` FOREIGN KEY (`JenisDenda_id`) REFERENCES `jenis_denda` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_pelanggaran_member_id` FOREIGN KEY (`Member_id`) REFERENCES `members` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_pelanggaran_collection_id` FOREIGN KEY (`Collection_id`) REFERENCES `collections` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `pengiriman`
    ADD CONSTRAINT `fk_pengiriman_collection_id` FOREIGN KEY (`Collection_ID`) REFERENCES `collections` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `perpus_keliling_petugas`
    ADD CONSTRAINT `fk_perpus_keliling_petugas_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `readinlocation`
    ADD CONSTRAINT `fk_readinlocation_locationid` FOREIGN KEY (`LocationId`) REFERENCES `locations` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `refferenceitems`
    ADD CONSTRAINT `fk_refferenceitems_refference_id` FOREIGN KEY (`Refference_id`) REFERENCES `refferences` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `refferences`
    ADD CONSTRAINT `fk_refferences_format_id` FOREIGN KEY (`Format_id`) REFERENCES `formats` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `serial_articlefiles`
    ADD CONSTRAINT `fk_serial_articlefiles_articles_id` FOREIGN KEY (`Articles_id`) REFERENCES `serial_articles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `serial_articles`
    ADD CONSTRAINT `fk_serial_articles_catalog_id` FOREIGN KEY (`Catalog_id`) REFERENCES `catalogs` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `serial_articles_repeatable`
    ADD CONSTRAINT `fk_serial_articles_repeatable_serial_article_id` FOREIGN KEY (`serial_article_ID`) REFERENCES `serial_articles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `settingcatalogdetail`
    ADD CONSTRAINT `fk_settingcatalogdetail_field_id` FOREIGN KEY (`Field_id`) REFERENCES `fields` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `stockopnamedetail`
    ADD CONSTRAINT `fk_stockopnamedetail_stockopnameid` FOREIGN KEY (`StockOpnameID`) REFERENCES `stockopname` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_stockopnamedetail_collectionid` FOREIGN KEY (`CollectionID`) REFERENCES `collections` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_stockopnamedetail_prevlocationid` FOREIGN KEY (`PrevLocationID`) REFERENCES `locations` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_stockopnamedetail_currentlocationid` FOREIGN KEY (`CurrentLocationID`) REFERENCES `locations` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_stockopnamedetail_prevstatusid` FOREIGN KEY (`PrevStatusID`) REFERENCES `collectionstatus` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_stockopnamedetail_currentstatusid` FOREIGN KEY (`CurrentStatusID`) REFERENCES `collectionstatus` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_stockopnamedetail_prevcollectionruleid` FOREIGN KEY (`PrevCollectionRuleID`) REFERENCES `collectionrules` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_stockopnamedetail_currentcollectionruleid` FOREIGN KEY (`CurrentCollectionRuleID`) REFERENCES `collectionrules` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `sumbangan_koleksi`
    ADD CONSTRAINT `fk_sumbangan_koleksi_sumbangan_id` FOREIGN KEY (`Sumbangan_id`) REFERENCES `sumbangan` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_sumbangan_koleksi_collection_id` FOREIGN KEY (`Collection_id`) REFERENCES `collections` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `survey_isian`
    ADD CONSTRAINT `fk_survey_isian_survey_pertanyaan_id` FOREIGN KEY (`Survey_Pertanyaan_id`) REFERENCES `survey_pertanyaan` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `survey_pertanyaan`
    ADD CONSTRAINT `fk_survey_pertanyaan_survey_id` FOREIGN KEY (`Survey_id`) REFERENCES `survey` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `survey_pilihan`
    ADD CONSTRAINT `fk_survey_pilihan_survey_pertanyaan_id` FOREIGN KEY (`Survey_Pertanyaan_id`) REFERENCES `survey_pertanyaan` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `survey_pilihan_sesi`
    ADD CONSTRAINT `fk_survey_pilihan_sesi_survey_pilihan_id` FOREIGN KEY (`Survey_Pilihan_id`) REFERENCES `survey_pilihan` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `users`
    ADD CONSTRAINT `fk_users_member_id` FOREIGN KEY (`member_id`) REFERENCES `members` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `worksheetfielditems`
    ADD CONSTRAINT `fk_worksheetfielditems_worksheetfield_id` FOREIGN KEY (`WorksheetField_id`) REFERENCES `worksheetfields` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_worksheetfielditems_refference_id` FOREIGN KEY (`Refference_id`) REFERENCES `refferences` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `worksheetfields`
    ADD CONSTRAINT `fk_worksheetfields_field_id` FOREIGN KEY (`Field_id`) REFERENCES `fields` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_worksheetfields_worksheet_id` FOREIGN KEY (`Worksheet_id`) REFERENCES `worksheets` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `worksheets`
    ADD CONSTRAINT `fk_worksheets_format_id` FOREIGN KEY (`Format_id`) REFERENCES `formats` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;


-- ---------------------------------------------------------------------
-- Perlu konfirmasi: Member_id di tabel ini mungkin merujuk users, bukan members.
-- Saat ini semua nilainya NULL / tabelnya kosong. Aktifkan setelah dicek di kode.
-- ---------------------------------------------------------------------
-- ALTER TABLE `catalogfiles` ADD CONSTRAINT `fk_catalogfiles_member_id` FOREIGN KEY (`Member_id`) REFERENCES `members` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;
-- ALTER TABLE `catalogs` ADD CONSTRAINT `fk_catalogs_member_id` FOREIGN KEY (`Member_id`) REFERENCES `members` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;
-- ALTER TABLE `historydata` ADD CONSTRAINT `fk_historydata_member_id` FOREIGN KEY (`Member_id`) REFERENCES `members` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;
-- ALTER TABLE `serial_articlefiles` ADD CONSTRAINT `fk_serial_articlefiles_member_id` FOREIGN KEY (`Member_id`) REFERENCES `members` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

SET SESSION sql_mode = @old_sql_mode;

-- ---------------------------------------------------------------------
-- Verifikasi: daftar FK yang terbentuk
-- ---------------------------------------------------------------------
SELECT table_name, column_name, referenced_table_name, referenced_column_name, constraint_name
FROM information_schema.key_column_usage
WHERE table_schema = DATABASE() AND referenced_table_name IS NOT NULL
ORDER BY table_name, column_name;
