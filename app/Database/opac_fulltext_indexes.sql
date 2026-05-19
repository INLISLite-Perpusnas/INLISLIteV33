-- OPAC FULLTEXT Index Setup
-- Jalankan sekali di database 'data' (bukan database default):
--   mysql -u root -p nama_database_data < opac_fulltext_indexes.sql
--
-- Estimasi waktu: 1-5 menit tergantung jumlah data.
-- Tidak perlu stop server, bisa dijalankan saat aplikasi aktif (online DDL).

-- Index per kolom (untuk search by Title/Author/Subject/Publisher)
ALTER TABLE catalogs ADD FULLTEXT INDEX ft_title     (Title);
ALTER TABLE catalogs ADD FULLTEXT INDEX ft_author    (Author);
ALTER TABLE catalogs ADD FULLTEXT INDEX ft_subject   (Subject);
ALTER TABLE catalogs ADD FULLTEXT INDEX ft_publisher (Publisher);

-- Index gabungan untuk search "Semua Kolom" (MATCH terhadap 4 kolom sekaligus)
ALTER TABLE catalogs ADD FULLTEXT INDEX ft_all (Title, Author, Subject, Publisher);

-- Index B-Tree biasa untuk filter & sort (jika belum ada)
ALTER TABLE catalogs ADD INDEX idx_publish_year (PublishYear);
ALTER TABLE catalogs ADD INDEX idx_languages    (Languages(50));
ALTER TABLE catalogs ADD INDEX idx_create_date  (CreateDate);

-- Verifikasi index sudah terpasang:
-- SHOW INDEX FROM catalogs;
