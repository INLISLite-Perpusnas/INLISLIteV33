<?php

namespace App\Libraries;;

class App
{
    protected $parameterModel;
    protected $logModel;
    public function __construct()
    {
        $this->parameterModel = new \Parameter\Models\ParameterModel();
        $this->logModel = new \App\Models\LogModel();
    }

    function addLog($message, $controller = null, $operation = null, $ref_table = null, $ref_id = null)
    {
        $save_data = [
            'message' => $message,
            'controller' => $controller,
            'operation' => $operation,
            'ref_table' => $ref_table,
            'ref_id' => $ref_id,
            'created_by' => user_id(),
        ];

        return $this->logModel->insert($save_data);
    }

    public function getParameter($name, $default = null, $create_param = false)
    {
        if ($param = $this->parameterExists($name)) {
            if (!empty($param)) {
                return $param;
            } else {
                return $default;
            }
        } else {
            if ($create_param) {
                $this->addParameter($name, $default);
            }
        }

        return $default;
    }

    public function addParameter($name =  null, $value = null)
    {
        if ($this->parameterExists($name) === false) {
            $data = array(
                'name' => $name,
                'value' => $value
            );
            $ret = $this->parameterModel->insert($data);
            // $params = $this->parameterModel->findAll();
            // set_cookie('params', json_encode($params), 3600 * 24 * 1);
            return $ret;
        }
        return false;
    }

    public function setParameter($name =  null, $value = null)
    {
        if ($this->parameterExists($name) === false) {
            $this->addParameter($name, $value);
        } else {
            $param = $this->parameterModel->where('name', $name)->first();
            $data = array(
                'value' => $value
            );
            $ret = $this->parameterModel->update($param->id, $data);
            // $params = $this->parameterModel->findAll();
            // set_cookie('params', json_encode($params), 3600 * 24 * 1);
            return $ret;
        }
    }

    public function deleteParam($name =  null)
    {
        $param = $this->parameterModel->where('name', $name)->first();
        return $this->parameterModel->delete($param->id);
    }

    public function parameterExists($name =  null)
    {
        // $params = json_decode(get_cookie('params'));
        // dd($params);
        // if (!$params) {
        //     $params = $this->parameterModel->findAll();
        //     set_cookie('params', json_encode($params), 3600 * 24 * 1);
        // }

        $params = $this->parameterModel->findAll();
        $param = [];

        foreach ($params as $row) {
            $param[$row->name] = $row;
        }
        if (isset($param[$name])) {
            return $param[$name]->value;
        }

        return false;
    }
}
