<!--CORE-->
<script src="<?= base_url('assets/js'); ?>/jquery-4.0.0.min.js"></script>
<script>
    // Memastikan jQuery sudah ter-load sebelum menambal fungsinya
    if (typeof jQuery !== 'undefined') {
        
        // 1. Perbaikan $.isFunction (Lebih aman dari sekadar typeof)
        if (!jQuery.isFunction) {
            jQuery.isFunction = function(obj) {
                return typeof obj === "function" && 
                       typeof obj.nodeType !== "number" && 
                       typeof obj.item !== "function";
            };
        }

        // 2. Perbaikan $.isArray
        if (!jQuery.isArray) {
            jQuery.isArray = Array.isArray;
        }

        // 3. Perbaikan $.trim (Sering dipakai oleh Select2 & DataTables)
        if (!jQuery.trim) {
            jQuery.trim = function(text) {
                return text == null ? "" : (text + "").trim();
            };
        }

        // 4. Perbaikan $.type (Mencegah error saat membaca tipe objek kompleks)
        if (!jQuery.type) {
            jQuery.type = function(obj) {
                if (obj == null) {
                    return obj + "";
                }
                return typeof obj === "object" || typeof obj === "function" ?
                    Object.prototype.toString.call(obj).match(/\s([a-z]+)/i)[1].toLowerCase() : 
                    typeof obj;
            };
        }

        // 5. Perbaikan $.isWindow (Kadang dipakai Datepicker untuk posisi kalender)
        if (!jQuery.isWindow) {
            jQuery.isWindow = function(obj) {
                return obj != null && obj === obj.window;
            };
        }
    }
</script>
<script src="<?= base_url('assets/js'); ?>/bootstrap.min.js"></script>
<script src="<?= base_url('assets/js'); ?>/metisMenu.min.js"></script>
<script src="<?= base_url('assets/js'); ?>/scrollbar.min.js"></script>
<script src="<?= base_url('assets/js'); ?>/toastr.min.js"></script>
<script src="<?= base_url('assets/js'); ?>/sweetalert2@8.js"></script>
<script src="<?= base_url('assets/js'); ?>/datatables.min.js"></script>
<script src="<?= base_url('assets/vendors'); ?>/jquery.lazy/jquery.lazy.min.js"></script>

<!--VENDORS-->
<script src="<?= base_url('assets/vendors'); ?>/dropzone/min/dropzone.min.js"></script>
<script src="<?= base_url('assets/vendors'); ?>/magnific-popup/jquery.magnific-popup.min.js"></script>
<script src="<?= base_url('assets/vendors'); ?>/form-components/toggle-switch.js"></script>
<script src="<?= base_url('assets/vendors'); ?>/form-components/moment.js"></script>
<script src="<?= base_url('assets/vendors'); ?>/form-components/datepicker.js"></script>
<script src="<?= base_url('assets/vendors'); ?>/form-components/daterangepicker.js"></script>
<script src="<?= base_url('assets/vendors'); ?>/form-components/bootstrap-multiselect.js"></script>
<script src="<?= base_url('assets/vendors'); ?>/select2/js/select2.min.js"></script>


<script src="<?= base_url('assets/vendors'); ?>/datatables/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url('assets/vendors'); ?>/datatables/js/responsive.bootstrap.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.tiny.cloud/1/32x284pjuqkz6bpk6b6dnfpxfestvjmqesa33sgixmunt7sh/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!--INIT -->
<script src="<?= base_url('themes/uigniter/js/vendors') ?>/blockui.js"></script>
<script src="<?= base_url('themes/uigniter/js/scripts-init'); ?>/app.js"></script>
<script src="<?= base_url('themes/uigniter/js/scripts-init'); ?>/scrollbar.js"></script>