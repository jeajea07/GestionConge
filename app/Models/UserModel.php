<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'employes';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'nom', 'prenom', 'email', 'password',
        'role', 'departement_id', 'date_embauche', 'actif',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'nom'    => 'required|min_length[2]|max_length[100]',
        'prenom' => 'required|min_length[2]|max_length[100]',
        'email'  => 'required|valid_email|is_unique[employes.email,id,{id}]',
        'role'   => 'required|in_list[employe,rh,admin]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Cette adresse email est déjà utilisée.',
        ],
    ];

    public function verifyCredentials(string $email, string $plainPassword): ?array
    {
        $user = $this->where('email', $email)
                     ->where('actif', 1)
                     ->first();

        if (! $user) {
            return null;
        }

        if (! password_verify($plainPassword, $user['password'])) {
            return null;
        }

        return $user;
    }

    public function getAllWithDepartement(): array
    {
        return $this->select('employes.*, departements.nom AS departement_nom')
                    ->join('departements', 'departements.id = employes.departement_id', 'left')
                    ->orderBy('employes.actif', 'DESC')
                    ->orderBy('employes.nom', 'ASC')
                    ->findAll();
    }

    public function getByIdWithDepartement(int $id): ?array
    {
        return $this->select('employes.*, departements.nom AS departement_nom')
                    ->join('departements', 'departements.id = employes.departement_id', 'left')
                    ->where('employes.id', $id)
                    ->first();
    }

    public function createEmployee(array $data): int|false
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $data['actif'] = 1;

        return $this->insert($data, true);
    }

    public function updateEmployee(int $id, array $data): bool
    {
        if (! empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }

        // Bypass model-level validation here because the controller already
        // validates the payload and the model's unique email rule can block
        // unchanged emails during updates.
        return (bool) $this->builder()
            ->where($this->primaryKey, $id)
            ->update($data);
    }

    public function deactivate(int $id): bool
    {
        return $this->update($id, ['actif' => 0]);
    }

    public function updateProfile(int $id, array $data): bool
    {
        $allowed = ['nom', 'prenom'];
        $update  = array_intersect_key($data, array_flip($allowed));

        if (! empty($data['new_password'])) {
            $update['password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
        }

        return $this->update($id, $update);
    }
}
