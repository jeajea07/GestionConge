<?php

namespace App\Controllers\RH;

use App\Controllers\BaseController;
use App\Services\CongeService;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $year = (int) date('Y');

        $query = $db->table('conges c')
            ->select('c.id, c.date_debut, c.date_fin, c.nb_jours, c.statut, c.commentaire_rh, e.nom, e.prenom, e.departement_id, d.nom AS departement_nom, t.libelle AS type_libelle, s.jours_attribues, s.jours_pris')
            ->join('employes e', 'e.id = c.employe_id')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->join('types_conge t', 't.id = c.type_conge_id', 'left')
            ->join('soldes s', 's.employe_id = c.employe_id AND s.type_conge_id = c.type_conge_id AND s.annee = ' . $db->escape($year), 'left');

        // Filtre par statut
        $selectedStatut = $this->request->getGet('statut');
        if ($selectedStatut) {
            $query->where('c.statut', $selectedStatut);
        }

        // Filtre par département
        $selectedDept = $this->request->getGet('dept');
        if ($selectedDept) {
            $query->where('e.departement_id', $selectedDept);
        }

        $demandes = $query->orderBy('c.id', 'DESC')->get()->getResultArray();

        // Compte les statuts pour tous les demandes (sans filtre de statut)
        $allDemandes = $db->table('conges c')
            ->select('c.statut')
            ->join('employes e', 'e.id = c.employe_id')
            ->get()
            ->getResultArray();

        $pendingCount = 0;
        $approvedCount = 0;
        $refusedCount = 0;
        foreach ($allDemandes as $demande) {
            $statut = $demande['statut'] ?? '';
            if ($statut === 'en_attente') {
                $pendingCount++;
            } elseif ($statut === 'approuvee') {
                $approvedCount++;
            } elseif ($statut === 'refusee') {
                $refusedCount++;
            }
        }

        // Récupère les départements
        $departements = $db->table('departements')->get()->getResultArray();

        // Gestion des actions d'approbation/refus avec confirmation
        $action = $this->request->getGet('action');
        $selectedId = (int) ($this->request->getGet('id') ?? 0);
        $selectedDemand = null;
        
        if ($action && $selectedId) {
            foreach ($demandes as $d) {
                if ($d['id'] == $selectedId) {
                    $selectedDemand = $d;
                    break;
                }
            }
        }

        return view('rh/dashboard', [
            'demandes' => $demandes,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'refusedCount' => $refusedCount,
            'departements' => $departements,
            'user_name' => session()->get('user_nom') ?: 'Responsable RH',
            'action' => $action,
            'selectedDemand' => $selectedDemand,
        ]);
    }

    public function approve(int $id)
    {
        $service = new CongeService();
        $comment = (string) $this->request->getPost('commentaire_rh');
        $traitePar = (int) (session()->get('user_id') ?? 0);

        try {
            $service->approuverDemandeConge($id, $comment, $traitePar);
            return redirect()->back()->with('success', 'Demande approuvee avec succès.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'approbation: ' . $e->getMessage());
        }
    }

    public function refuse(int $id)
    {
        $service = new CongeService();
        $comment = (string) $this->request->getPost('commentaire_rh');
        $traitePar = (int) (session()->get('user_id') ?? 0);

        try {
            $service->refuserDemandeConge($id, $comment, $traitePar);
            return redirect()->back()->with('success', 'Demande refusee avec succès.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Erreur lors du refus: ' . $e->getMessage());
        }
    }

    public function historique()
    {
        $db = \Config\Database::connect();
        $year = (int) date('Y');

        $query = $db->table('conges c')
            ->select('c.id, c.date_debut, c.date_fin, c.nb_jours, c.statut, c.commentaire_rh, e.nom, e.prenom, e.departement_id, d.nom AS departement_nom, t.libelle AS type_libelle, s.jours_attribues, s.jours_pris')
            ->join('employes e', 'e.id = c.employe_id')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->join('types_conge t', 't.id = c.type_conge_id', 'left')
            ->join('soldes s', 's.employe_id = c.employe_id AND s.type_conge_id = c.type_conge_id AND s.annee = ' . $db->escape($year), 'left');

        // Filtre par statut
        $selectedStatut = $this->request->getGet('statut');
        if ($selectedStatut) {
            $query->where('c.statut', $selectedStatut);
        }

        // Filtre par département
        $selectedDept = $this->request->getGet('dept');
        if ($selectedDept) {
            $query->where('e.departement_id', $selectedDept);
        }

        $demandes = $query->orderBy('c.id', 'DESC')->get()->getResultArray();

        // Compte les statuts pour tous les demandes
        $allDemandes = $db->table('conges c')
            ->select('c.statut')
            ->join('employes e', 'e.id = c.employe_id')
            ->get()
            ->getResultArray();

        $pendingCount = 0;
        $approvedCount = 0;
        $refusedCount = 0;
        $cancelledCount = 0;
        foreach ($allDemandes as $demande) {
            $statut = $demande['statut'] ?? '';
            if ($statut === 'en_attente') {
                $pendingCount++;
            } elseif ($statut === 'approuvee') {
                $approvedCount++;
            } elseif ($statut === 'refusee') {
                $refusedCount++;
            } elseif ($statut === 'annulee') {
                $cancelledCount++;
            }
        }

        // Récupère les départements
        $departements = $db->table('departements')->get()->getResultArray();

        return view('rh/historique', [
            'demandes' => $demandes,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'refusedCount' => $refusedCount,
            'cancelledCount' => $cancelledCount,
            'departements' => $departements,
            'selectedStatut' => $selectedStatut,
            'selectedDept' => $selectedDept,
            'user_name' => session()->get('user_nom') ?: 'Responsable RH',
        ]);
    }

    public function soldes()
    {
        $db = \Config\Database::connect();
        $year = (int) date('Y');
        $service = new \App\Services\SoldeService();

        // Récupère tous les soldes pour l'année en cours
        $soldesRaw = $service->getAllEmployeSolde($year);

        // Enrichir les données avec les infos des employés
        $soldes = [];
        foreach ($soldesRaw as $solde) {
            $employe = $db->table('employes')
                ->select('id, prenom, nom, departement_id')
                ->where('id', $solde['employe_id'])
                ->get()
                ->getRowArray();

            if ($employe) {
                $departement = $db->table('departements')
                    ->select('nom')
                    ->where('id', $employe['departement_id'])
                    ->get()
                    ->getRowArray();

                $soldes[] = array_merge($solde, [
                    'prenom' => $employe['prenom'],
                    'nom' => $employe['nom'],
                    'departement_nom' => $departement['nom'] ?? 'N/A',
                ]);
            }
        }

        // Récupère les départements pour le filtre
        $departements = $db->table('departements')->get()->getResultArray();

        // Filtre par type de congé
        $selectedType = $this->request->getGet('type');
        if ($selectedType) {
            $soldes = array_filter($soldes, function($s) use ($selectedType) {
                return strtolower(str_replace(' ', '', $s['type_conge_'])) === strtolower(str_replace(' ', '', $selectedType));
            });
        }

        // Récupère les types de congés uniques
        $types = array_values(array_unique(array_column($soldes, 'type_conge_')));

        return view('rh/soldes', [
            'soldes' => $soldes,
            'types' => $types,
            'selectedType' => $selectedType,
            'departements' => $departements,
            'year' => $year,
            'user_name' => session()->get('user_nom') ?: 'Responsable RH',
        ]);
    }

    public function editSolde(int $id)
    {
        $db = \Config\Database::connect();
        $soldeModel = new \App\Models\SoldeModel();

        // Récupère le solde
        $solde = $soldeModel->find($id);
        if (!$solde) {
            return redirect()->to('rh/soldes')->with('error', 'Solde non trouvé.');
        }

        // Récupère les informations de l'employé
        $employe = $db->table('employes')
            ->select('id, prenom, nom, departement_id')
            ->where('id', $solde['employe_id'])
            ->get()
            ->getRowArray();

        // Récupère le département
        $departement = $db->table('departements')
            ->select('nom')
            ->where('id', $employe['departement_id'])
            ->get()
            ->getRowArray();

        // Récupère le type de congé
        $typeConge = $db->table('types_conge')
            ->select('id, libelle')
            ->where('id', $solde['type_conge_id'])
            ->get()
            ->getRowArray();

        return view('rh/edit_solde', [
            'solde' => $solde,
            'employe' => $employe,
            'departement' => $departement,
            'typeConge' => $typeConge,
            'user_name' => session()->get('user_nom') ?: 'Responsable RH',
        ]);
    }

    public function updateSolde(int $id)
    {
        $soldeModel = new \App\Models\SoldeModel();
        
        $joursAttribues = (int) $this->request->getPost('jours_attribues');

        $solde = $soldeModel->find($id);
        if (!$solde) {
            return redirect()->back()->with('error', 'Solde non trouvé.');
        }

        // Validation basique
        if ($joursAttribues < 0) {
            return redirect()->back()->with('error', 'Les jours attribués ne peuvent pas être négatifs.');
        }

        // Met à jour le solde
        $soldeModel->update($id, [
            'jours_attribues' => $joursAttribues,
        ]);

        return redirect()->to('rh/soldes')->with('success', 'Solde mis à jour avec succès.');
    }
}
