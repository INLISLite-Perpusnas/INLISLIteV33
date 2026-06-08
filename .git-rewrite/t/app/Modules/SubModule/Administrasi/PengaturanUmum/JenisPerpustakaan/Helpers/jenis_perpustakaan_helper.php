<?php

use Base\Models\BaseModel;
use Base\Models\DataModel;

function get_member_form($field_id, $jenis_perpustakaan_id)
{
    $active = 0;
    $tableName = 'members_form';
    $builder = new DataModel($tableName);
    $query = $builder->where('Member_Field_id', $field_id)->where('Jenis_Perpustakaan_id', $jenis_perpustakaan_id);

   
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
function set_member_form($form_id, $field_id, $jenis_perpustakaan_id, $active)
{
 
        $builder = new DataModel('members_form');
        $query = $builder->where('Member_Field_id', $field_id);
        $query = $builder->where('Jenis_Perpustakaan_id', $jenis_perpustakaan_id);
        $data = $query->get()->getRow();

        $update_data = array('active' => $active);
        $builder->update($form_id, $update_data);
        return true;
    
}
