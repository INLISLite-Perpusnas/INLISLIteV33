<?php

use Base\Models\BaseModel;

if (!function_exists('display_menu_frontend')) {
    function display_menu_frontend($parent, $level = 1)
    {
        $request = service('request');

        $baseModel = new \Base\Models\BaseModel();

        $query = $baseModel->query(
            "select a.id, a.icon, a.name, a.controller, a.type, deriv.childs
            from `c_menus` a left outer join (
                select parent, count(*) as childs from `c_menus` group by parent
                ) deriv on a.id = deriv.parent where  a.parent= " .
                $parent .
                " and a.active = 1 and a.category_id = '2'
            order by `sort` asc"
        );
        $result = $query->getResult();

        $ret = '';
        if ($result) {
            if ($level > 1 and $parent > 0) {
                $ret .= '<ul class="dropdown-menu">';
            } else {
                $ret = '';
            }

            $class_li   = '';
            $class_a    = '';
            $class_icon = 'bx bx-chevron-down';

            foreach ($result as $row) {
                $active = strtolower(trim($request->getUri())) === strtolower(trim($row->controller)) ? 'active' : '';
                $target = '';
                $link   = base_url($row->controller);
                if (!filter_var($row->controller, FILTER_VALIDATE_URL) === false) {
                    $link   = $row->controller;
                    $target = '_blank';
                }

                if ($row->childs > 0) {
                    if ($level == 1) {
                        $ret .= '<li class="nav-item">';
                        $ret .= '<a href="#" class="nav-link ' . $active . '">' . $row->name . ' <i class="' . $class_icon . '"></i></a>';
                    } else {
                        $ret .= '<li class="nav-item">';
                        $ret .= '<a href="#" class="nav-link ' . $active . '">' . $row->name . ' <i class="' . $class_icon . '"></i></a>';
                    }
                    $ret .= display_menu_frontend($row->id, $level + 1);
                    $ret .= '</li>';
                } else {
                    if ($level == 1) {
                        $ret .= '<li class="nav-item">';
                        $ret .= '<a href="' . $link . '" class="nav-link ' . $active . '" target="' . $target . '">' . $row->name . '</a>';
                    } else {
                        $ret .= '<li class="nav-item">';
                        $ret .= '<a href="' . $link . '" class="nav-link ' . $active . '" target="' . $target . '">' . $row->name . '</a>';
                    }
                    $ret .= '</li>';
                }
            }
            if ($level > 1) {
                $ret .= '</ul>';
            }
        }
        return $ret;
    }
}

if (!function_exists('display_menu_module')) {
    function display_menu_module($category_id, $parent, $level)
    {
        $baseModel = new \Base\Models\BaseModel();
        $query = $baseModel->query(
            "select a.id, a.name as label, a.type, a.active, a.controller, a.slug, deriv.count 
            from c_menus a left outer join (
                select parent, count(*) as count 
                    from c_menus group by parent
                ) deriv on a.id = deriv.parent where  a.parent= " .
                $parent .
                ' and a.category_id = ' .
                $category_id .
                "
            order by `sort` asc"
        );
        $result = $query->getResult();

        $ret = '';

        if ($result) {
            $ret .= '<ol class="dd-list">';
            foreach ($result as $row) {
                if ($row->count > 0) {
                    $ret .=
                        '<li class="dd-item dd3-item ' .
                        ($row->active ? '' : 'menu-toggle-activate_inactive') .
                        ' menu-toggle-activate" data-id="' .
                        $row->id .
                        '" data-status="' .
                        $row->active .
                        '">';

                    if ($row->type != 'label') {
                        $ret .=
                            '<div class="dd-handle dd3-handle dd-handles"></div>';
                        $ret .= '<div class="dd3-content">' . _ent($row->label);
                    } else {
                        $ret .=
                            '<div class="dd-handle dd3-handle dd-handles dd-handle-label"></div>';
                        $ret .=
                            '<div class="dd3-content "><b>' .
                            _ent($row->label) .
                            '</b>';
                    }

                    $ret .= '</div>';
                    $ret .= display_menu_module(
                        $category_id,
                        $row->id,
                        $level + 1
                    );
                    $ret .= '</li>';
                } elseif ($row->count == 0) {
                    $ret .=
                        '<li class="dd-item dd3-item ' .
                        ($row->active ? '' : 'menu-toggle-activate_inactive') .
                        ' menu-toggle-activate" data-id="' .
                        $row->id .
                        '" data-status="' .
                        $row->active .
                        '">';

                    if ($row->type != 'label') {
                        $ret .=
                            '<div class="dd-handle dd3-handle dd-handles"></div>';
                        $ret .=
                            '<div class="dd3-content"><span data-toggle="tooltip" data-placement="top" title="c:' .
                            $row->controller .
                            '">' .
                            _ent($row->label) .
                            '</span>';
                    } else {
                        $ret .=
                            '<div class="dd-handle dd3-handle dd-handles dd-handle-label"></div>';
                        $ret .=
                            '<div class="dd3-content  "><b>' .
                            _ent($row->label) .
                            '</b>';
                    }

                    $ret .= display_menu_dropdown($row->id, $row->controller);

                    $ret .= '</div></li>';
                }
            }
            $ret .= '</ol>';
        }

        return $ret;
    }
}

if (!function_exists('display_menu_dropdown')) {
    function display_menu_dropdown($id, $controller)
    {
        $action = '';
        $action .= '<div class="pull-right">';
        $action .=
            '<div class="btn-group" style="margin-top:-5px">
						<a href="' .
            base_url('reference?menu_id=' . $id) .
            '" data-toggle="tooltip" data-placement="top" title="Daftar Item" class="btn btn-sm btn-primary"><i class="lnr-list font-weight-bold"></i></a>
						<button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="dropdown-toggle-split dropdown-toggle btn btn-primary btn-sm"><span class="sr-only">Dropdown</span> </button>
						<div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(51px, 29px, 0px);">
							<button type="button" tabindex="0" class="dropdown-item" data-toggle="modal" data-target="#modal_detail_category"><i class="dropdown-icon lnr-pencil"> </i><span>Ubah</span></button>
							<button type="button" tabindex="1" class="dropdown-item" data-toggle="modal" data-target="#modal_create_category"><i class="dropdown-icon lnr-plus-circle"> </i><span>Sub Menu</span></button>
							<button type="button" tabindex="2" class="dropdown-item"><i class="dropdown-icon lnr-trash remove-data"> </i><span>Hapus</span></button>
						</div>
					</div>';
        $action .= '</div>';
        return $action;
    }
}

if (!function_exists('uri_index')) {
    function uri_index($route)
    {
        if (strpos($route, '/index') !== false) {
            $route = $route;
        } else {
            $route = $route . '/index';
        }

        return $route;
    }
}

if (!function_exists('is_accessed')) {
    function is_accessed($permission, $user_id = null)
    {
        if (strpos($permission, '/access') !== false) {
            $permission = $permission;
        } else {
            $permission = $permission.'/access';
        }
      
        return is_allowed($permission, $user_id);
    }
}

if (!function_exists('display_menu_backend')) {
    function display_menu_backend($parent = 0, $level = 1, $group = null)
    {
        return _render_menu_backend($parent, $level, $group)['html'];
    }
}

if (!function_exists('_render_menu_backend')) {
    function _render_menu_backend($parent = 0, $level = 1, $group = null)
    {
        $request   = \Config\Services::request();
        $baseModel = new \App\Models\BaseModel();

        $sql = "
            SELECT
                cm.id,
                cm.icon,
                cm.name,
                cm.controller,
                cm.type,
                (
                    SELECT COUNT(*)
                    FROM c_menus c2
                    INNER JOIN auth_permissions ap2
                        ON c2.controller = REPLACE(ap2.name, '/access', '')
                    INNER JOIN auth_groups_permissions agp2
                        ON ap2.id = agp2.permission_id
                    INNER JOIN auth_groups ag2
                        ON ag2.id = agp2.group_id
                    WHERE c2.parent = cm.id
                    AND c2.active = 1
                    AND ag2.name = ?
                    AND ap2.name LIKE '%/access%'
                ) AS childs
            FROM c_menus cm
            WHERE
                cm.parent = ?
                AND cm.active = 1
                AND cm.category_id = '1'
                AND (
                    (
                        cm.type != 'label'
                        AND EXISTS (
                            SELECT 1 FROM auth_permissions ap
                            INNER JOIN auth_groups_permissions agp ON ap.id = agp.permission_id
                            INNER JOIN auth_groups ag ON ag.id = agp.group_id
                            WHERE REPLACE(ap.name, '/access', '') = cm.controller
                            AND ag.name = ?
                            AND ap.name LIKE '%/access%'
                        )
                    )
                    OR (
                        cm.type = 'label'
                        AND EXISTS (
                            SELECT 1 FROM c_menus c2
                            INNER JOIN auth_permissions ap2
                                ON c2.controller = REPLACE(ap2.name, '/access', '')
                            INNER JOIN auth_groups_permissions agp2 ON ap2.id = agp2.permission_id
                            INNER JOIN auth_groups ag2 ON ag2.id = agp2.group_id
                            WHERE c2.parent = cm.id
                            AND c2.active = 1
                            AND ag2.name = ?
                            AND ap2.name LIKE '%/access%'
                        )
                    )
                )
            ORDER BY cm.sort ASC
        ";

        $result = $baseModel->query($sql, [$group, $parent, $group, $group])->getResult();

        if (!$result) return ['html' => '', 'hasActive' => false];

        // Compute current path relative to base_url once per call
        $fullPath = $request->getUri()->getPath();
        $basePath = rtrim(parse_url(base_url(), PHP_URL_PATH) ?? '', '/');
        if ($basePath !== '' && strpos($fullPath, $basePath) === 0) {
            $fullPath = substr($fullPath, strlen($basePath));
        }
        $currentPath = trim($fullPath, '/');

        $html      = '<ul class="' . ($level === 1 ? 'nav-menu-argon' : 'submenu-argon') . '">';
        $hasActive = false;

        foreach ($result as $row) {

            if ($row->type === 'label') {
                $html .= '<li class="nav-label-argon">' . esc($row->name) . '</li>';
                continue;
            }

            $url            = $row->controller ? base_url($row->controller) : '#';
            $controllerPath = trim($row->controller, '/');
            $isActive       = ($currentPath === $controllerPath)
                || ($controllerPath !== '' && strpos($currentPath, $controllerPath . '/') === 0);

            if ($row->childs > 0) {
                $child         = _render_menu_backend($row->id, $level + 1, $group);
                $childActive   = $child['hasActive'];
                $liClasses     = 'has-submenu' . ($childActive ? ' open' : '');
                $aClasses      = 'submenu-toggle' . ($childActive ? ' active' : '');
                $html .= '
                <li class="' . $liClasses . '">
                    <a href="#" class="' . $aClasses . '">
                        <i class="' . esc($row->icon) . '"></i>
                        <span>' . esc($row->name) . '</span>
                        <i class="fas fa-chevron-down caret"></i>
                    </a>';
                $html .= $child['html'];
                $html .= '</li>';
                if ($childActive) $hasActive = true;
            } else {
                if ($isActive) $hasActive = true;
                $html .= '
                <li>
                    <a href="' . $url . '" class="' . ($isActive ? 'active' : '') . '">
                        <i class="' . esc($row->icon) . '"></i>
                        <span>' . esc($row->name) . '</span>
                    </a>
                </li>';
            }
        }

        $html .= '</ul>';

        return ['html' => $html, 'hasActive' => $hasActive];
    }
}


if (!function_exists('config_menu_frontend')) {
    function config_menu_frontend($parent, $level = 1)
    {
        $db = db_connect();
        $builder = $db
            ->table('c_menus as a')
            ->select(
                '0 as themes, a.id as key, "true" as is_public, a.name as label, a.description as label_en, a.controller as url'
            )
            ->select('deriv.childs as submenu')
            ->join(
                '(select parent, COUNT(*) AS childs from c_menus group by parent) deriv',
                'deriv.parent = a.id',
                'left'
            )
            ->where('a.parent', $parent)
            ->where('a.active', 1)
            ->where('a.category_id', 2)
            ->orderBy('a.sort', 'asc');

        $result = $builder->get()->getResult();
        foreach ($result as $idx => $row) {
            $result[$idx]->themes = (int) $row->themes;
            $result[$idx]->is_public = (bool) $row->is_public;
            $result[$idx]->label_en = empty($row->label_en)
                ? $row->label
                : $row->label_en;
            // $result[$idx]->url = permalink($result[$idx]->url);
            if ($row->submenu) {
                $result[$idx]->submenu = config_menu_frontend($row->key);
            } else {
                $result[$idx]->submenu = [];
            }
        }

        return $result;
    }
}
