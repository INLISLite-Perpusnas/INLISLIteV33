<?php
if (!function_exists('clean_fullscreen')) {
    function clean_fullscreen($permission)
    {
		$permission = str_replace("&fullscreen=1", "", $permission);
		$permission = str_replace("&fullscreen=0", "", $permission);
		$permission = str_replace("?fullscreen=1", "", $permission);
		$permission = str_replace("?fullscreen=0", "", $permission);

        return $permission;
    }
}