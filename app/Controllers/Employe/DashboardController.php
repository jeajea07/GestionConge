<?php

namespace App\Controllers\Employe;

use App\Services\CongeService;
use App\Services\SoldeService;
use App\Services\TypeCongeService;
use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index(){
        $congeService = new CongeService();
        $soldeService = new SoldeService();
        $typeCongeService = new TypeCongeService();

        $employeId = session()->get('user_id') ?? session()->get('employe_id');
        $annee = date('Y');

        $conges = $congeService->get3derniersCongeByEmployeId($employeId);
        $conge_en_attente = $congeService->getAllCongeByEmployeIdAndByStatut($employeId, 'en_attente');
        $conge_approuvee = $congeService->getAllCongeByEmployeIdAndByStatut($employeId, 'approuvee');
        $conge_refusee = $congeService->getAllCongeByEmployeIdAndByStatut($employeId, 'refusee');
        $soldes = $soldeService->getAllSoldeRestantByEmployeIdAndByTypeCongeId($employeId, $annee);
        $types = $typeCongeService->getAllTypeConge();
        $typeMap = [];
        foreach ($types as $type) {
            $typeMap[$type['id']] = $type;
        }

        $type_conge = $typeCongeService->getAllTypeConge();
        $demande_par_type_conge = [];
        foreach ($type_conge as $type) {
            $demande_par_type_conge[$type['id']] = $congeService->getAllCongeByEmployeIdAndByTypeCongeId($employeId, $type['id']);
        }

        return view('employe/dashboard', [
            'conges' => $conges,
            'count_conge_en_attente' => count($conge_en_attente),
            'count_conge_approuvee' => count($conge_approuvee),
            'count_conge_refusee' => count($conge_refusee),
            'demande_par_type_conge' => $demande_par_type_conge,
            'soldes' => $soldes,
            'type_map' => $typeMap
        ]);
    }

    public function showFormDemandeConge(){
        $soldeService = new SoldeService();
        $typeCongeService = new TypeCongeService();

        $employeId = session()->get('user_id') ?? session()->get('employe_id');
        $annee = date('Y');

        $soldes = $soldeService->getAllSoldeRestantByEmployeIdAndByTypeCongeId($employeId, $annee);
        $typeConge = $typeCongeService->getAllTypeConge();


        return view('employe/demande_conge',
        [
            'soldes' => $soldes,
            'type_conge' => $typeConge
        ]);
    }

    public function storeDemandeConge(){
        $congeService = new CongeService();

        $employeId = session()->get('user_id') ?? session()->get('employe_id');
        $typeCongeId = (int) $this->request->getPost('type_conge_id');
        $dateDebut = $this->request->getPost('date_debut');
        $dateFin = $this->request->getPost('date_fin');
        $motif = $this->request->getPost('motif') ?? '';

        if (!$typeCongeId || !$dateDebut || !$dateFin) {
            return redirect()->back()->with('error', 'Veuillez remplir tous les champs obligatoires.');
        }

        try {
            $dateDebutObj = new \DateTime($dateDebut);
            $dateFinObj = new \DateTime($dateFin);
            $interval = $dateDebutObj->diff($dateFinObj);
            $nbJours = $interval->days + 1;

            $congeService->demanderConge($employeId, $typeCongeId, $dateDebutObj, $dateFinObj, $nbJours, $motif, '', $employeId);

            return redirect()->to('employe/conges')->with('success', 'Votre demande de congé a été enregistrée.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function showHistoriqueConge(){
        $congeService = new CongeService();
        $typeCongeService = new TypeCongeService();

        $liste_statut = ['en_attente', 'approuvee', 'refusee', 'annulee'];

        $employeId = session()->get('user_id') ?? session()->get('employe_id');
        $selectedStatut = $this->request->getGet('statut');

        if ($selectedStatut && in_array($selectedStatut, $liste_statut)) {
            $conges = $congeService->getAllCongeByEmployeIdAndByStatut($employeId, $selectedStatut);
        } else {
            $conges = $congeService->getAllCongeByEmployeId($employeId);
        }

        $types = $typeCongeService->getAllTypeConge();
        $typeMap = [];
        foreach ($types as $type) {
            $typeMap[$type['id']] = $type;
        }

        return view('employe/historique_conge', [
            'conges' => $conges,
            'liste_statut' => $liste_statut,
            'type_map' => $typeMap
        ]);
    }

    public function cancelDemandeConge($congeId){
        $congeService = new CongeService();
        $employeId = session()->get('user_id') ?? session()->get('employe_id');

        try {
            $conge = $congeService->getCongeById($congeId);
            
            if (!$conge) {
                return redirect()->back()->with('error', 'Demande introuvable.');
            }

            if ($conge['employe_id'] != $employeId) {
                return redirect()->back()->with('error', 'Vous n\'avez pas le droit d\'annuler cette demande.');
            }

            if (!in_array($conge['statut'], ['en_attente', 'approuvee'])) {
                return redirect()->back()->with('error', 'Cette demande ne peut pas être annulée.');
            }

            $congeService->annulerDemandeConge($congeId);
            
            return redirect()->to('employe/conges')->with('success', 'Votre demande de congé a été annulée.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

}
