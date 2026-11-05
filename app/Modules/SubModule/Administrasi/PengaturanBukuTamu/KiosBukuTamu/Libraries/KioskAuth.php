<?php

namespace KiosBukuTamu\Libraries;

/**
 * Aktivasi perangkat kios (buku tamu, baca di tempat, peminjaman dan pengembalian mandiri).
 *
 * Admin membuat kode sekali pakai di CMS; kios menukarnya dengan cookie bertanda tangan
 * yang hanya berlaku untuk layanan kios di lokasi tersebut. Tiap cookie terikat ke satu baris
 * guestbook_kiosks sehingga bisa dicabut dari CMS.
 */
class KioskAuth
{
	public const COOKIE = 'guestbook_kiosk';
	public const CODE_TTL = 600; // 10 menit
	public const TOKEN_TTL = 31536000; // 1 tahun

	// Tanpa karakter yang mirip (I, L, O, 0, 1)
	private const ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

	private $db;

	public function __construct($db = null)
	{
		$this->db = $db ?? \Config\Database::connect('data');
	}

	/** Buat kode aktivasi untuk lokasi; yang tersimpan hanya hash-nya. @return string kode berformat XXXX-XXXX */
	public function createCode(int $locationId, ?int $userId, string $terminal): string
	{
		$now = date('Y-m-d H:i:s');
		$this->db->table('guestbook_kiosks')
			->where('ActivatedAt', null)
			->where('CodeExpiresAt <', $now)
			->delete();

		$code = '';
		$last = strlen(self::ALPHABET) - 1;
		for ($i = 0; $i < 8; $i++) {
			$code .= self::ALPHABET[random_int(0, $last)];
		}

		$this->db->table('guestbook_kiosks')->insert([
			'Location_id' => $locationId,
			'CodeHash' => $this->hashCode($code),
			'CodeExpiresAt' => date('Y-m-d H:i:s', time() + self::CODE_TTL),
			'CreateBy' => $userId,
			'CreateDate' => $now,
			'CreateTerminal' => $terminal,
		]);

		return substr($code, 0, 4) . '-' . substr($code, 4);
	}

	/** Tukar kode dengan cookie. @return string|null nilai cookie, null bila kode salah/kedaluwarsa/terpakai/lokasi nonaktif */
	public function activate(string $code, string $terminal): ?string
	{
		$now = date('Y-m-d H:i:s');
		$kiosk = $this->db->table('guestbook_kiosks as a')
			->select('a.ID, a.Location_id')
			->join('locations as b', 'b.ID = a.Location_id')
			->where('a.CodeHash', $this->hashCode($code))
			->where('a.ActivatedAt', null)
			->where('a.RevokedAt', null)
			->where('a.CodeExpiresAt >=', $now)
			->where('b.active', 1)
			->get()
			->getRow();
		if (!$kiosk) {
			return null;
		}

		// Syarat ActivatedAt IS NULL menjaga kode tetap sekali pakai saat dua permintaan bersamaan
		$expiresAt = time() + self::TOKEN_TTL;
		$this->db->table('guestbook_kiosks')
			->where('ID', $kiosk->ID)
			->where('ActivatedAt', null)
			->update([
				'ActivatedAt' => $now,
				'ActivatedTerminal' => $terminal,
				'ExpiresAt' => date('Y-m-d H:i:s', $expiresAt),
			]);
		if ($this->db->affectedRows() !== 1) {
			return null;
		}

		return $this->sign(['k' => (int) $kiosk->ID, 'l' => (int) $kiosk->Location_id, 'e' => $expiresAt]);
	}

	/** @return int|null ID lokasi bila cookie sah dan kiosnya belum dicabut */
	public function locationIdFromCookie($cookie): ?int
	{
		if (!is_string($cookie) || substr_count($cookie, '.') !== 1) {
			return null;
		}
		[$payload, $signature] = explode('.', $cookie);

		$key = $this->key();
		if ($key === '' || !hash_equals(hash_hmac('sha256', $payload, $key), $signature)) {
			return null;
		}

		$data = json_decode((string) base64_decode(strtr($payload, '-_', '+/'), true), true);
		if (!is_array($data) || !is_int($data['k'] ?? null) || !is_int($data['l'] ?? null) || !is_int($data['e'] ?? null) || $data['e'] < time()) {
			return null;
		}

		$active = $this->db->table('guestbook_kiosks as a')
			->join('locations as b', 'b.ID = a.Location_id')
			->where('a.ID', $data['k'])
			->where('a.Location_id', $data['l'])
			->where('a.ActivatedAt !=', null)
			->where('a.RevokedAt', null)
			->where('b.active', 1)
			->countAllResults();

		return $active > 0 ? $data['l'] : null;
	}

	/** @return int|null ID lokasi kios dari cookie pada request ini */
	public static function requestLocationId($request): ?int
	{
		return (new self())->locationIdFromCookie($request->getCookie(self::COOKIE));
	}

	public function revoke(int $id, ?int $userId): bool
	{
		$this->db->table('guestbook_kiosks')
			->where('ID', $id)
			->where('RevokedAt', null)
			->update(['RevokedAt' => date('Y-m-d H:i:s'), 'RevokedBy' => $userId]);

		return $this->db->affectedRows() === 1;
	}

	public function all(): array
	{
		return $this->db->table('guestbook_kiosks as a')
			->select('a.*, b.Code as LocationCode, b.Name as LocationName')
			->join('locations as b', 'b.ID = a.Location_id', 'left')
			->orderBy('a.ID', 'DESC')
			->limit(100)
			->get()
			->getResult();
	}

	public function activeLocations(): array
	{
		return $this->db->table('locations')
			->select('ID, Code, Name')
			->where('active', 1)
			->orderBy('Code')
			->get()
			->getResult();
	}

	/** Huruf besar tanpa spasi dan tanda hubung, sehingga "k7qm 4xpd" = "K7QM-4XPD" */
	public static function normalizeCode(string $code): string
	{
		return strtoupper(str_replace(['-', ' '], '', trim($code)));
	}

	private function hashCode(string $code): string
	{
		return hash_hmac('sha256', $code, $this->requireKey());
	}

	private function sign(array $data): string
	{
		$payload = rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');

		return $payload . '.' . hash_hmac('sha256', $payload, $this->requireKey());
	}

	private function requireKey(): string
	{
		$key = $this->key();
		if ($key === '') {
			throw new \RuntimeException('encryption.key belum diatur di .env');
		}

		return $key;
	}

	/** Kunci turunan dari encryption.key, khusus untuk token kios */
	private function key(): string
	{
		$key = (string) config('Encryption')->key;

		return $key === '' ? '' : hash_hkdf('sha256', $key, 32, 'guestbook-kiosk');
	}
}
