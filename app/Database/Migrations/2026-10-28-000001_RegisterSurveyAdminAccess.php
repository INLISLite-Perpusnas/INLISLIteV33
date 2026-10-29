<?php

namespace App\Database\Migrations;

use App\Database\Seeds\SurveyAdminAccessSeeder;
use CodeIgniter\Database\Migration;

class RegisterSurveyAdminAccess extends Migration
{
    protected $DBGroup = 'data';

    public function up()
    {
        (new SurveyAdminAccessSeeder(config(\Config\Database::class), $this->db))->run();
    }

    public function down()
    {
    }
}
