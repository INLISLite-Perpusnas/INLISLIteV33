-- =====================================================================
-- Bagian B laporan foreign key: samakan tipe kolom anak dengan induk
-- Referensi: docs/laporan-foreign-key.md
-- Database : inlislite_v33_progo
--
-- Hasil pengecekan data (2026-11-03):
--   - Tidak ada nilai negatif di kolom yang diubah ke UNSIGNED
--   - c_menus.category_id / c_references.menu_id muat di MEDIUMINT UNSIGNED
--   - Semua CollectionLoan_id (VARCHAR) berisi angka murni
--   - Tabel pengiriman kosong
--
-- Atribut NULL / NOT NULL dipertahankan sesuai definisi asli.
-- Tidak ada DEFAULT, COMMENT, atau EXTRA pada kolom-kolom ini.
-- WAJIB backup database sebelum menjalankan.
-- =====================================================================

-- ---------------------------------------------------------------------
-- 0. Hapus tabel t_* yang sudah tidak terpakai (semua kosong, 0 baris)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `t_keranjanganggota`;
DROP TABLE IF EXISTS `t_pemesanankoleksi`;
DROP TABLE IF EXISTS `t_perpanjangan`;
DROP TABLE IF EXISTS `t_sumbangan`;
DROP TABLE IF EXISTS `t_usulankoleksi`;

-- t_template BELUM dihapus: berisi 7 baris dan masih dipakai cetak kartu anggota
-- (AnggotaKartuController::printanggota, KartuAnggota/Views/card.php,
--  KartuAnggota/Controllers/Api/KartuAnggota.php, modul TemplateKartu).
-- DROP TABLE IF EXISTS `t_template`;

-- ---------------------------------------------------------------------
-- 1. Rujukan ke collections.ID (INT UNSIGNED)
-- ---------------------------------------------------------------------
ALTER TABLE `bacaditempat`       MODIFY `collection_id` INT UNSIGNED NOT NULL;
ALTER TABLE `bookinglogs`        MODIFY `collectionId`  INT UNSIGNED NULL;
ALTER TABLE `collectionloanitems` MODIFY `Collection_id` INT UNSIGNED NOT NULL;
ALTER TABLE `colloclib`          MODIFY `Col_id`        INT UNSIGNED NOT NULL;
ALTER TABLE `djkn_data`          MODIFY `Collection_id` INT UNSIGNED NULL;
ALTER TABLE `stockopnamedetail`  MODIFY `CollectionID`  INT UNSIGNED NOT NULL;
ALTER TABLE `sumbangan_koleksi`  MODIFY `Collection_id` INT UNSIGNED NULL;

-- DOUBLE -> INT UNSIGNED
ALTER TABLE `pengiriman`         MODIFY `Collection_ID` INT UNSIGNED NOT NULL;

-- ---------------------------------------------------------------------
-- 2. Rujukan ke collections.ID dan collectionloans.ID (BIGINT)
--    Dua kolom per tabel digabung dalam satu ALTER
-- ---------------------------------------------------------------------
ALTER TABLE `collectionloanextends`
    MODIFY `Collection_id`     INT UNSIGNED NOT NULL,
    MODIFY `CollectionLoan_id` BIGINT NOT NULL;

ALTER TABLE `pelanggaran`
    MODIFY `Collection_id`     INT UNSIGNED NULL,
    MODIFY `CollectionLoan_id` BIGINT NULL;

-- ---------------------------------------------------------------------
-- 3. Modul CMS: rujukan ke c_categories.id / c_menus.id (MEDIUMINT UNSIGNED)
-- ---------------------------------------------------------------------
ALTER TABLE `c_menus`      MODIFY `category_id` MEDIUMINT UNSIGNED NOT NULL;
ALTER TABLE `c_references` MODIFY `menu_id`     MEDIUMINT UNSIGNED NULL;

-- ---------------------------------------------------------------------
-- Verifikasi: semua baris harus menunjukkan tipe yang sama dengan induk
-- ---------------------------------------------------------------------
SELECT table_name, column_name, column_type, is_nullable
FROM information_schema.columns
WHERE table_schema = DATABASE()
  AND (table_name, column_name) IN (
    ('bacaditempat','collection_id'), ('bookinglogs','collectionId'),
    ('collectionloanitems','Collection_id'), ('colloclib','Col_id'),
    ('djkn_data','Collection_id'), ('stockopnamedetail','CollectionID'),
    ('sumbangan_koleksi','Collection_id'), ('pengiriman','Collection_ID'), ('collectionloanextends','Collection_id'),
    ('collectionloanextends','CollectionLoan_id'), ('pelanggaran','Collection_id'),
    ('pelanggaran','CollectionLoan_id'), ('c_menus','category_id'),
    ('c_references','menu_id')
  )
ORDER BY table_name, column_name;
