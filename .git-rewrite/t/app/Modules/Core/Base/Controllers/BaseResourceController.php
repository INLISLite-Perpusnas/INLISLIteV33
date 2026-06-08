<?php

namespace Base\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class BaseResourceController extends ResourceController
{
    use ResponseTrait;

    public function paginatedResponse($result, $totalRecord, $limit, $offset)
    {
        $response = [
            'total_record' => (int)$totalRecord,
            'per_page' => (int)$limit,
            'total_page' => (int)($limit == 0 ? 0 : ceil($totalRecord / $limit)),
            'current_page' => (int)$limit == 0 ? 0 : floor($offset / $limit) + 1,
            'result' => $result
        ];
        return $this->respond($response);
    }

    public function simpleResponse($result)
    {
        return $this->respond($result);
    }
}
