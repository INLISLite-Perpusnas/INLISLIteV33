<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSemanticSearchSetting extends Migration
{
    public function up()
    {
        $settings = $this->db->table('settingparameters');
        if (!$settings->where('Name', 'semantic_enabled')->countAllResults()) {
            $settings->insert(['Name' => 'semantic_enabled', 'Value' => '1']);
        }
    }

    public function down()
    {
    }
}
