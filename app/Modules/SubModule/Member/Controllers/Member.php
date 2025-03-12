<?php

namespace Member\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Member extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $uploadPath;
    public $modulePath;
    public $db;

    public function __construct()
    {
        $this->language = \Config\Services::language();
        $this->language->setLocale('id');

        $this->memberModel = new \Member\Models\MemberModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/member/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }


        helper('adminigniter');
        helper('reference');
        helper('date_id');
    }
}
