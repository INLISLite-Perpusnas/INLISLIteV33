<?php

namespace Crud\Controllers;

use App\Controllers\BaseController;

class Order extends BaseController
{
    public function index()
    {
        $data['title'] = "Order";
        $data['sub_title'] = "Daftar Semua Order";

        $crud = $this->_getGroceryCrudEnterprise();
        $crud->setCsrfTokenName(csrf_token());
        $crud->setCsrfTokenValue(csrf_hash());
        $crud->setTable('orders');
        $crud->setSubject('Order', 'Daftar Order');
        $crud->columns(['orderNumber', 'orderDate', 'requiredDate', 'status', 'comments', 'customerNumber']);
        $crud->fields(['orderNumber', 'orderDate', 'requiredDate', 'status', 'comments', 'customerNumber']);
        $crud->requiredFields(['orderNumber', 'orderDate', 'requiredDate', 'status', 'comments', 'customerNumber']);

        $crud->setRelation('customerNumber', 'customers', 'customerName');

        $output = $crud->render();

        if (isset($output->isJSONResponse) && $output->isJSONResponse) {
            header('Content-Type: application/json; charset=utf-8');
            echo $output->output;
            exit;
        }

        return view('Crud\Views\layout', array_merge((array)$output, $data));
    }
}
