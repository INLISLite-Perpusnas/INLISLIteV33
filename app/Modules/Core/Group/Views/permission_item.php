<div class="widget-content p-0 mb-2 border-bottom pb-1">
    <div class="widget-content-wrapper">
        <div class="widget-content-left mr-2">
            <input type="checkbox" name="permissions[<?= $row->id; ?>][]" id="<?= $row->id; ?>" <?= (in_array($row->id, $permissions_users)) ? 'checked' : '' ?> data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini">
        </div>
        <div class="widget-content-left flex2">
            <div class="widget-heading" style="font-size: 0.85rem; line-height: 1.2;">
                <?= str_replace(['-', '_', '/'], [' ', ' ', '/<wbr>'], _spec($row->name)); ?>
            </div>
        </div>
        <?php if (is_member('admin')) : ?>
        <div class="widget-content-right">
            <a href="#" data-href="<?= base_url('permission/delete/' . $row->id) ?>" class="btn btn-outline-danger btn-sm py-0 px-1 remove-data" title="Hapus"><i class="fa fa-trash-alt"></i></a>
        </div>
        <?php endif; ?>
    </div>
</div>
