<?php

namespace Crud\Controllers;

use App\Controllers\BaseController;

class Catalog extends BaseController
{
    public function index()
    {
        $data['title'] = "Katalog";
        $data['sub_title'] = "Daftar Semua Katalog";

        $crud = $this->_getGroceryCrudEnterprise("data");
        $crud->setCsrfTokenName(csrf_token());
        $crud->setCsrfTokenValue(csrf_hash());
        $crud->setTable('catalogs');
        $crud->setSubject('Katalog', 'Daftar Katalog');
        $crud->columns(['BIBID', 'Title', 'Edition', 'Publisher', 'PhysicalDescription', 'CallNumber', 'IsOPAC', 'ControlNumber', 'Author']);
        $crud->displayAs(array(
            'BIBID' => 'BIBID',
            'Title' => 'Judul',
            'Edition' => 'Edisi',
            'Publisher' => 'Publisher',
            'PhysicalDescription' => 'Deskripsi Fisik',
            'CallNumber' => 'Nomor Panggil',
            'IsOPAC' => 'OPAC'
        ));
        $crud->fields(['BIBID', 'ControlNumber', 'CallNumber', 'Title', 'Author', 'Edition', 'Publisher', 'PhysicalDescription', 'IsOPAC']);
        $crud->requiredFields(['BIBID', 'CallNumber']);
        $crud->defaultColumnWidth('BIBID', '150px');
        $crud->defaultColumnWidth('Title', '500px');
        $crud->defaultColumnWidth('PhysicalDescription', '500px');
        $crud->unsetAdd();
        $crud->unsetDelete();
        // $crud->unsetExportPdf();
        $crud->unsetPrint();
        $crud->unsetSettings();


        $output = $crud->render();
        if (isset($output->isJSONResponse) && $output->isJSONResponse) {
            header('Content-Type: application/json; charset=utf-8');
            echo $output->output;
            exit;
        }
        return view('Crud\Views\layout', array_merge((array)$output, $data));
    }
}
