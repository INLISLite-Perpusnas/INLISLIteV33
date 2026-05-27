<?php

namespace Config;

class Autoload extends \CodeIgniter\Config\AutoloadConfig {
    public $psr4 = [
        APP_NAMESPACE => APPPATH,
        'Config'      => APPPATH.'Config'
    ];
    function __construct() {
        if (!($tmp=read_cache('routes_list'))) {
            $loadModules = function($dir, &$modules=[]) use(&$loadModules) {
                if (is_dir($dir)&&($scan=scandir($dir))) {
                    foreach (($scan=array_diff($scan, ['..', '.'])) as $value) if (is_dir($newPath=$dir.DIRECTORY_SEPARATOR.$value)) if (is_file($newPath.DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR.'Routes.php')) $modules[$value] = $newPath; else $loadModules($newPath, $modules);
                }
                return $modules;
            };
            $this->psr4 = array_merge($this->psr4, $loadModules(APPPATH.'Modules', $this->psr4));
            write_cache('routes_list', $this->psr4);
        } else $this->psr4 = $tmp;
        parent::__construct();
    }
    public $classmap = [];
    public $files    = [];
    public $helpers  = ['url', 'text', 'form', 'html', 'file', 'cookie', 'auth', 'app', 'common', 'image'];
}