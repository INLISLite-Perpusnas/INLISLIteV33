<?php

namespace App\Modules\Backend\Notification\Models;

class NotificationModel extends \App\Models\BaseModel
{
    protected $table      = 't_notification';
    protected $primaryKey = 'id';
    protected $returnType     = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'id', 'from', 'to', 'status', 'message', 'type', 'code', 'ref_table', 'ref_id', 'ref_url', 'is_read', 'start_date', 'end_date', 'description', 'created_by', 'updated_by',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;
}
