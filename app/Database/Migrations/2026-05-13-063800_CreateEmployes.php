<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmployes extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id' => [
                'type' => 'INTEGER',
                'auto_increment' => true,
            ],

            'nom' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],

            'prenom' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],

            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'unique' => true,
            ],

            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],

            'role' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'employe',
            ],

            'departement_id' => [
                'type' => 'INTEGER',
            ],

            'date_embauche' => [
                'type' => 'DATE',
            ],

            'actif' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],

        ]);

        $this->forge->addKey('id', true);

        $this->forge->addForeignKey(
            'departement_id',
            'departements',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('employes');
    }

    public function down()
    {
        $this->forge->dropTable('employes');
    }
}