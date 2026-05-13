<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        $departements = [
            [
                'nom' => 'Administration',
                'description' => 'Administration generale',
            ],
            [
                'nom' => 'Ressources Humaines',
                'description' => 'Gestion des ressources humaines',
            ],
            [
                'nom' => 'Technique',
                'description' => 'Equipe technique',
            ],
        ];

        $this->db->table('departements')->ignore(true)->insertBatch($departements);

        $adminDeptId = $this->db->table('departements')
            ->select('id')
            ->where('nom', 'Administration')
            ->get()
            ->getRowArray()['id'] ?? 1;

        $rhDeptId = $this->db->table('departements')
            ->select('id')
            ->where('nom', 'Ressources Humaines')
            ->get()
            ->getRowArray()['id'] ?? $adminDeptId;

        $techDeptId = $this->db->table('departements')
            ->select('id')
            ->where('nom', 'Technique')
            ->get()
            ->getRowArray()['id'] ?? $adminDeptId;

        $users = [
            [
                'nom'            => 'Admin',
                'prenom'         => 'TechMada',
                'email'          => 'admin@techmada.mg',
                'password'       => password_hash('admin123', PASSWORD_DEFAULT),
                'role'           => 'admin',
                'departement_id' => $adminDeptId,
                'date_embauche'  => date('Y-m-d'),
                'actif'          => 1,
            ],
            [
                'nom'            => 'RH',
                'prenom'         => 'TechMada',
                'email'          => 'rh@techmada.mg',
                'password'       => password_hash('rh123', PASSWORD_DEFAULT),
                'role'           => 'rh',
                'departement_id' => $rhDeptId,
                'date_embauche'  => date('Y-m-d'),
                'actif'          => 1,
            ],
            [
                'nom'            => 'Employe',
                'prenom'         => 'TechMada',
                'email'          => 'employe@techmada.mg',
                'password'       => password_hash('emp123', PASSWORD_DEFAULT),
                'role'           => 'employe',
                'departement_id' => $techDeptId,
                'date_embauche'  => date('Y-m-d'),
                'actif'          => 1,
            ],
        ];

        foreach ($users as $user) {
            $exists = $this->db->table('employes')
                ->where('email', $user['email'])
                ->countAllResults();

            if ($exists === 0) {
                $this->db->table('employes')->insert($user);
            }
        }
    }
}
