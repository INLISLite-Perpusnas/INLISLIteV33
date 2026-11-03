<style>
    /* ul.pagination berupa flex, jadi text-right tidak menggesernya; ratakan kanan secara eksplisit */
    .survey-table-wrapper .dataTables_paginate .pagination {
        justify-content: flex-end;
        margin: 0;
    }
</style>
<script>
    // DataTable sisi klien untuk tabel daftar survei (setara GridView INLISLite 3: paging, cari, tampilkan N baris)
    $(document).ready(function() {
        $('.survey-table').closest('.card-body').addClass('survey-table-wrapper');
        $('.survey-table').DataTable({
            "dom": "<'row'<'col-md-6 col-sm-8 col-xs-12 text-left'l><'col-md-6 col-sm-4 col-xs-12 text-right'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row align-items-center'<'col-md-6 col-sm-12'i><'col-md-6 col-sm-12 d-flex justify-content-end'p>>",
            "pagingType": "full_numbers",
            "ordering": false,
            "oLanguage": {
                "sSearch": "<i class='fa fa-search'></i> _INPUT_",
                "sLengthMenu": "Tampilkan : _MENU_",
                "sInfo": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                "sInfoEmpty": "Tidak ada data",
                "sEmptyTable": "Tidak ada data",
                "sZeroRecords": "Data tidak ditemukan",
                "oPaginate": {
                    "sNext": "<i class='fa fa-chevron-right'></i>",
                    "sPrevious": "<i class='fa fa-chevron-left'></i>",
                    "sLast": "Akhir",
                    "sFirst": "Awal",
                }
            },
        });
    });

    // Tombol hapus: konfirmasi lalu kirim form POST (bawa token CSRF)
    $('body').on('click', '.btn-delete', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        Swal.fire({
            title: 'Apakah Anda yakin ingin menghapus item ini?',
            text: $(this).data('warning') || '',
            type: 'warning',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.value || result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
