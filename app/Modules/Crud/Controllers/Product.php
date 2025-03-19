<?php

namespace Crud\Controllers;

use App\Controllers\BaseController;

class Product extends BaseController
{
    public function index()
    {
        $data['title'] = "Product";
        $data['sub_title'] = "Daftar Semua Product";

        $crud = $this->_getGroceryCrudEnterprise();
        $crud->setCsrfTokenName(csrf_token());
        $crud->setCsrfTokenValue(csrf_hash());
        $crud->setTable('products');
        // $crud->setSubject('Product', 'Daftar Product');
        $crud->columns(['productCode', 'productName', 'productLine', 'productScale', 'productVendor', 'productDescription']);
        $crud->fields(['productCode', 'productName', 'productLine', 'productScale', 'productVendor', 'productDescription', 'quantityInStock', 'buyPrice', 'MSRP']);
        $crud->requiredFields(['productCode', 'productName', 'productLine', 'productScale', 'productVendor', 'productDescription', 'quantityInStock', 'buyPrice', 'MSRP']);

        $output = $crud->render();


        if (isset($output->isJSONResponse) && $output->isJSONResponse) {
            header('Content-Type: application/json; charset=utf-8');
            echo $output->output;
            exit;
        }

        return view('Crud\Views\layout', array_merge((array)$output, $data));
    }
}
