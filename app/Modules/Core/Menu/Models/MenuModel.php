<?php

namespace Menu\Models;

class MenuModel extends \Base\Models\BaseModel
{
	protected $DBGroup              = 'data';
	protected $table      			= 'c_menus'; 
    protected $primaryKey 			= 'id';
    protected $returnType     		= 'object';
    protected $useSoftDeletes 		= false;
    protected $protectFields 		= false;
    protected $useTimestamps 		= true;
    protected $createdField  		= 'created_at';
    protected $updatedField  		= 'updated_at';
    protected $deletedField  		= 'deleted_at';
    protected $validationRules    	= [];
    protected $validationMessages 	= [];
    protected $skipValidation     	= true;

    public function menu($parent_id = null)
    {
        $this->builder = $this->db->table($this->table . 'as m');
        $this->builder->distinct();
        $this->builder->select('m.id, m.name, m.parent, m.controller, m.icon, m.operation, m.sort, m.description, m.is_label, m.active');
        $this->builder->join('c_permissions p', 'p.module_id = m.id', 'left');
        if (!empty($parent_id)) {
            $this->builder->where('m.parent', $parent_id);
        } else {
            $this->builder->where('p.operation', 'access');
        }

        if (!is_member('admin')) {
            $this->builder->groupStart();
            foreach (get_group_user() as $group) {
                $this->builder->orWhere('p.group_id', $group->id);
            }
            $this->builder->groupEnd();
        }
        $this->builder->orderBy('m.sort');
        $query = $this->builder->get();
        return $query->getResult();
    }

    public function parent()
    {
        $this->builder->where('parent', '0');
        $this->builder->orderBy('sort', 'asc');
        $query = $this->builder->get();
        return $query->getResult();
    }

    public function child($parent_id)
    {
        $this->builder->where('parent', $parent_id);
        $this->builder->orderBy('sort', 'asc');
        $query = $this->builder->get();
        return $query->getResult();
    }

    public function get()
    {
        $this->builder->where('parent', '0');
        $this->builder->orderBy('sort', 'asc');
        $query = $this->builder->get();
        return $query->getResult();
    }

    public function list($id)
    {
        $this->builder->where('parent', $id);
        $this->builder->orderBy('sort', 'asc');
        $query = $this->builder->get();
        return $query->getResult();
    }

    public function sub_get()
    {
        $this->builder->where('parent', '0');
        $this->builder->orderBy('sort', 'asc');
        $query = $this->builder->get();
        return $query->getResult();
    }
}
