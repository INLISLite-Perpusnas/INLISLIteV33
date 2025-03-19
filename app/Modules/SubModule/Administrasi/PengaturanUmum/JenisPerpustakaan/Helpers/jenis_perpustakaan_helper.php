<?php

use Base\Models\BaseModel;
use Base\Models\DataModel;

function get_member_form($field_id, $jenis_perpustakaan_id, $branch = false)
{
    $active = 0;
    $tableName = $branch ? 'members_form_branchs' : 'members_form';
    $builder = new DataModel($tableName);
    $query = $builder->where('Member_Field_id', $field_id)->where('Jenis_Perpustakaan_id', $jenis_perpustakaan_id);

    if ($branch) {
        $query->where('Branch_id', user()->branch_id);
    }

    $data = $query->get()->getRow();
    if ($data) {
        $active = $data->active;
    } else {
        $defTableName = 'members_form';
        $defBuilder = new DataModel($defTableName);
        $defQuery = $defBuilder->where('Member_Field_id', $field_id)->where('Jenis_Perpustakaan_id', $jenis_perpustakaan_id);
        $defData = $defQuery->get()->getRow();
        $active = $defData->active;
    }
    return $active;
}
function set_member_form($form_id, $field_id, $jenis_perpustakaan_id, $active, $branch = false)
{
    if (branch_id()) {
        $builder = new DataModel('members_form_branchs');
        $query = $builder->where('Member_Field_id', $field_id);
        $query = $builder->where('Jenis_Perpustakaan_id', $jenis_perpustakaan_id);
        $query = $builder->where('Branch_id', user()->branch_id);
        $data = $query->get()->getRow();

        if (empty($data)) {
            $save_data = array('Member_Field_id' => $field_id, 'active' => $active, 'Jenis_Perpustakaan_id' => $jenis_perpustakaan_id, 'Branch_id' => branch_id());
            $builder->insert($save_data);

            return true;
        } else {
            $update_data = array('active' => $active);
            $builder->update($form_id, $update_data);
            return true;
        }
    } else {
        $builder = new DataModel('members_form');
        $query = $builder->where('Member_Field_id', $field_id);
        $query = $builder->where('Jenis_Perpustakaan_id', $jenis_perpustakaan_id);
        $data = $query->get()->getRow();

        $update_data = array('active' => $active);
        $builder->update($form_id, $update_data);
        return true;
    }
}
