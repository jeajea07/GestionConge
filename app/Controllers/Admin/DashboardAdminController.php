<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Database\BaseConnection;
use App\Models\UserModel;

class DashboardAdminController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        return view('admin/dashboard', $this->buildDashboardData($db));
    }

    public function employees()
    {
        $db = \Config\Database::connect();

        return view('admin/employees', $this->buildEmployeesData($db));
    }

    public function store()
    {
        $rules = [
            'nom'            => 'required|min_length[2]',
            'prenom'         => 'required|min_length[2]',
            'email'          => 'required|valid_email|is_unique[employes.email]',
            'password'       => 'required|min_length[6]',
            'departement_id' => 'required|is_not_unique[departements.id]',
            'role'           => 'required|in_list[employe,rh,admin]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez verifier les champs.');
        }

        $data = [
            'nom'            => $this->request->getPost('nom'),
            'prenom'         => $this->request->getPost('prenom'),
            'email'          => $this->request->getPost('email'),
            'password'       => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'departement_id' => $this->request->getPost('departement_id'),
            'role'           => $this->request->getPost('role'),
            'date_embauche'  => $this->request->getPost('date_embauche'),
            'actif'          => 1,
        ];

        $employeId = $this->userModel->insert($data);
        
        // Créer les soldes de congés
        $db = \Config\Database::connect();
        $typesConge = $db->table('types_conge')->get()->getResultArray();
        $year = (int) date('Y');
        
        $soldeModel = new \App\Models\SoldeModel();
        foreach ($typesConge as $type) {
            $soldeData = [
                'employe_id' => $employeId,
                'type_conge_id' => $type['id'],
                'annee' => $year,
                'jours_attribues' => (int) $this->request->getPost('solde_' . $type['id']) ?? 0,
                'jours_pris' => 0,
            ];
            $soldeModel->insert($soldeData);
        }

        return redirect()->to('/admin/employes')->with('success', 'Employe cree avec succes.');
    }

    public function update(int $id)
    {
        $rules = [
            'nom'            => 'required|min_length[2]',
            'prenom'         => 'required|min_length[2]',
            'email'          => 'required|valid_email',
            'departement_id' => 'required|is_not_unique[departements.id]',
            'role'           => 'required|in_list[employe,rh,admin]',
        ];

        $password = (string) $this->request->getPost('password');

        if ($password !== '') {
            $rules['password'] = 'min_length[6]';
        }

        if (! $this->validate($rules)) {
            return redirect()->to('/admin/employes?edit=' . $id . '#employee-form')
                ->withInput()
                ->with('error', 'Veuillez verifier les champs avant modification.');
        }

        $email = (string) $this->request->getPost('email');
        $emailExists = $this->userModel
            ->where('email', $email)
            ->where('id !=', $id)
            ->first();

        if ($emailExists !== null) {
            return redirect()->to('/admin/employes?edit=' . $id . '#employee-form')
                ->withInput()
                ->with('error', 'Cette adresse email est déjà utilisée par un autre employé.');
        }

        $data = [
            'nom'            => $this->request->getPost('nom'),
            'prenom'         => $this->request->getPost('prenom'),
            'email'          => $email,
            'departement_id' => $this->request->getPost('departement_id'),
            'role'           => $this->request->getPost('role'),
            'date_embauche'  => $this->request->getPost('date_embauche'),
        ];

        if ($password !== '') {
            $data['password'] = $password;
        }

        $updated = $this->userModel->updateEmployee($id, $data);

        if (! $updated) {
            return redirect()->to('/admin/employes?edit=' . $id . '#employee-form')
                ->withInput()
                ->with('error', "La modification de l'employé a échoué.");
        }

        return redirect()->to('/admin/employes')->with('success', 'Employe modifie avec succes.');
    }

    public function deactivate(int $id)
    {
        $this->userModel->deactivate($id);

        return redirect()->to('/admin/employes')->with('success', 'Employe desactive avec succes.');
    }

    public function reactivate(int $id)
    {
        $this->userModel->update($id, ['actif' => 1]);

        return redirect()->to('/admin/employes')->with('success', 'Employe reactive avec succes.');
    }

    private function buildDashboardData(BaseConnection $db): array
    {
        $now = date('Y-m-d H:i:s');
        $monthStart = date('Y-m-01 00:00:00');
        $monthEnd = date('Y-m-t 23:59:59');

        $pendingCount = $db->table('conges')
            ->where('statut', 'en_attente')
            ->countAllResults();

        $approvedThisMonth = $db->table('conges')
            ->where('statut', 'approuvee')
            ->where('date_debut >=', $monthStart)
            ->where('date_debut <=', $monthEnd)
            ->countAllResults();

        $absentToday = $db->table('conges')
            ->where('statut', 'approuvee')
            ->where('date_debut <=', $now)
            ->where('date_fin >=', $now)
            ->countAllResults();

        $recentRequests = $db->table('conges c')
            ->select('c.nb_jours, c.statut, e.nom, e.prenom, t.libelle')
            ->join('employes e', 'e.id = c.employe_id')
            ->join('types_conge t', 't.id = c.type_conge_id', 'left')
            ->orderBy('c.id', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $absentsToday = $db->table('conges c')
            ->select('e.nom, e.prenom, t.libelle, c.date_fin')
            ->join('employes e', 'e.id = c.employe_id')
            ->join('types_conge t', 't.id = c.type_conge_id', 'left')
            ->where('c.statut', 'approuvee')
            ->where('c.date_debut <=', $now)
            ->where('c.date_fin >=', $now)
            ->orderBy('c.date_fin', 'ASC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $criticalBalances = $db->table('soldes s')
            ->select('e.id')
            ->join('employes e', 'e.id = s.employe_id')
            ->where('e.actif', 1)
            ->where('(s.jours_attribues - s.jours_pris) <=', 2)
            ->groupBy('e.id')
            ->countAllResults();

        return array_merge($this->buildSharedData($db), [
            'title'             => "Vue d'ensemble",
            'activePage'        => 'dashboard',
            'metrics'           => [
                'activeEmployees'  => $this->userModel->where('actif', 1)->countAllResults(),
                'pendingRequests'  => $pendingCount,
                'approvedThisMonth'=> $approvedThisMonth,
                'departments'      => $db->table('departements')->countAllResults(),
                'absentToday'      => $absentToday,
            ],
            'recentRequests'    => $recentRequests,
            'absentsToday'      => $absentsToday,
            'criticalBalances'  => $criticalBalances,
        ]);
    }

    private function buildEmployeesData(BaseConnection $db): array
    {
        $editId = (int) ($this->request->getGet('edit') ?? 0);
        $editingEmployee = $editId > 0 ? $this->userModel->getByIdWithDepartement($editId) : null;

        return array_merge($this->buildSharedData($db), [
            'title'          => 'Gestion des employes',
            'activePage'     => 'employees',
            'employes'       => $this->userModel->getAllWithDepartement(),
            'departements'   => $db->table('departements')->get()->getResultArray(),
            'typesConge'     => $db->table('types_conge')->get()->getResultArray(),
            'annualBalances' => $this->fetchAnnualBalances($db),
            'editingEmployee'=> $editingEmployee,
        ]);
    }

    private function buildSharedData(BaseConnection $db): array
    {
        return [
            'user_name'          => session()->get('user_nom') ?: 'Administrateur',
            'user_role'          => session()->get('user_role') ?: 'admin',
            'pendingCount'       => $db->table('conges')->where('statut', 'en_attente')->countAllResults(),
            'departementCount'   => $db->table('departements')->countAllResults(),
            'typeCongeCount'     => $db->table('types_conge')->countAllResults(),
            'employeeCount'      => $this->userModel->countAllResults(),
        ];
    }

    private function fetchAnnualBalances(BaseConnection $db): array
    {
        $balances = $db->table('soldes s')
            ->select('s.employe_id, s.jours_attribues, s.jours_pris, t.libelle')
            ->join('types_conge t', 't.id = s.type_conge_id', 'left')
            ->get()
            ->getResultArray();

        $annualBalances = [];

        foreach ($balances as $balance) {
            $isAnnual = isset($balance['libelle']) && stripos($balance['libelle'], 'annuel') !== false;

            if ($isAnnual || ! isset($annualBalances[$balance['employe_id']])) {
                $annualBalances[$balance['employe_id']] = [
                    'attribues' => (int) $balance['jours_attribues'],
                    'restants'  => (int) $balance['jours_attribues'] - (int) $balance['jours_pris'],
                ];
            }
        }

        return $annualBalances;
    }
}
