# Fix: Stockopname Scan – Spinner Tidak Berhenti

**Tanggal:** 06-06-2026  
**Issue:** Setelah scan barcode ke stockopname, tombol spinner terus muter. Data sebenarnya sudah tersimpan, tapi halaman tidak merespons.  
**Root Cause:** Controller `scanBarcode` tidak mengembalikan field `data` dalam JSON response. JavaScript memanggil `addNewRowToTable(response.data)` dengan nilai `undefined`, sehingga `data.ID` melempar `TypeError` dan callback `complete` tidak memulihkan tombol.

---

## 1. StockopnamedetailModel.php

**Path:** `app/Modules/SubModule/Stockopname/Models/StockopnamedetailModel.php`

### BEFORE

```php
/**
 * Get detail by ID with joins
 */
 
```

### AFTER

```php
/**
 * Get detail by ID with joins
 */
public function getDetailById($id)
{
    return $this->db->table('stockopnamedetail sd')
        ->select('
            sd.*,
            c.NomorBarcode,
            c.CallNumber,
            cat.Title,
            cat.Author,
            cat.Publisher,
            prevLoc.Name as PrevLocationName,
            currLoc.Name as CurrentLocationName,
            prevStatus.Name as PrevStatusName,
            currStatus.Name as CurrentStatusName,
            prevRule.Name as PrevRuleName,
            currRule.Name as CurrentRuleName
        ')
        ->join('collections c', 'sd.CollectionID = c.id', 'left')
        ->join('catalogs cat', 'c.catalog_id = cat.id', 'left')
        ->join('locations prevLoc', 'sd.PrevLocationID = prevLoc.ID', 'left')
        ->join('locations currLoc', 'sd.CurrentLocationID = currLoc.ID', 'left')
        ->join('collectionstatus prevStatus', 'sd.PrevStatusID = prevStatus.ID', 'left')
        ->join('collectionstatus currStatus', 'sd.CurrentStatusID = currStatus.ID', 'left')
        ->join('collectionrules prevRule', 'sd.PrevCollectionRuleID = prevRule.ID', 'left')
        ->join('collectionrules currRule', 'sd.CurrentCollectionRuleID = currRule.ID', 'left')
        ->where('sd.ID', $id)
        ->get()
        ->getRowArray();
}
```

---

## 2. Stockopname.php (Controller) — method `scanBarcode()`

**Path:** `app/Modules/SubModule/Stockopname/Controllers/Stockopname.php`

### BEFORE

```php
$result = $this->stockopnamedetailModel->insert($detailData);

    // Get the inserted detail with joins for response
    
    
    return $this->response->setJSON([
        'status' => 'success',
        'message' => 'Koleksi berhasil ditambahkan ke stockopname.',
    ]);
```

### AFTER

```php
$result = $this->stockopnamedetailModel->insert($detailData);

$insertedDetail = $this->stockopnamedetailModel->getDetailById($nextId);

return $this->response->setJSON([
    'status' => 'success',
    'message' => 'Koleksi berhasil ditambahkan ke stockopname.',
    'data' => $insertedDetail,
]);
```

---

## 3. detail.php (View) — fungsi `scanBarcode()` JavaScript

**Path:** `app/Modules/SubModule/Stockopname/Views/detail.php`

### BEFORE

```javascript
success: function(response) {
    if (response.status === 'success') {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message, timer: 1500, showConfirmButton: false });
        addNewRowToTable(response.data);   // <-- response.data = undefined → TypeError → spinner macet
        $('#barcodeInput').val('').focus();
        updateSummary();
    } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
        $('#barcodeInput').select();
    }
},
```

### AFTER

```javascript
success: function(response) {
    if (response.status === 'success') {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message, timer: 1500, showConfirmButton: false })
            .then(function() {
                location.reload();   // reload halaman setelah notifikasi tutup
            });
    } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
        $('#barcodeInput').select();
    }
},
```

---

## Alur Setelah Fix

1. User scan barcode → AJAX POST ke `stockopname/scanBarcode`
2. Controller simpan data ke DB → kembalikan JSON `{ status, message, data }`
3. Swal notifikasi "Berhasil" muncul selama 1,5 detik
4. Halaman otomatis di-reload → tabel menampilkan data terbaru termasuk koleksi yang baru di-scan
