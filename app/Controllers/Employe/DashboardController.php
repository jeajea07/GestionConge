<?php

namespace App\Controllers\Employe;

use App\Services\CongeService;
use App\Services\SoldeService;
use App\Services\TypeCongeService;
use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    private function parseLocalDateTime(string $value): \DateTime
    {
        $date = \DateTime::createFromFormat('Y-m-d\TH:i', $value);
        $errors = \DateTime::getLastErrors();

        if (! $date || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            throw new \InvalidArgumentException('Format de date invalide.');
        }

        return $date;
    }

    private function countInclusiveLocalDays(\DateTime $dateDebut, \DateTime $dateFin): int
    {
        $debut = (clone $dateDebut)->setTime(0, 0, 0);
        $fin = (clone $dateFin)->setTime(0, 0, 0);

        return $debut->diff($fin)->days + 1;
    }

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
            return redirect()->back()
                ->withInput()
                ->with('error', 'Veuillez remplir tous les champs obligatoires.');
        }

        try {
            $dateDebutObj = $this->parseLocalDateTime($dateDebut);
            $dateFinObj = $this->parseLocalDateTime($dateFin);

            if ($dateFinObj < $dateDebutObj) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'La date de fin doit être supérieure ou égale à la date de début.');
            }

            $nbJours = $this->countInclusiveLocalDays($dateDebutObj, $dateFinObj);

            $congeService->demanderConge($employeId, $typeCongeId, $dateDebutObj, $dateFinObj, $nbJours, $motif, '', $employeId);

            return redirect()->to('employe/conges')->with('success', 'Votre demande de congé a été enregistrée.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur: ' . $e->getMessage());
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

    public function showCalendar(){
        $congeService = new CongeService();
        $employeId = session()->get('user_id') ?? session()->get('employe_id');

        $conges = $congeService->getAllCongeByEmployeIdAndByStatut($employeId, 'approuvee');

        $events = [];
        foreach ($conges as $conge) {
            $events[] = [
                'title' => 'Congé: ' . ($conge['motif'] ?? 'Sans motif'),
                'start' => (new \DateTime($conge['date_debut']))->format('Y-m-d\TH:i:s'),
                'end' => (new \DateTime($conge['date_fin']))->format('Y-m-d\TH:i:s'),
            ];
        }

        return view('employe/calendar', [
            'events' => $events
        ]);
    }

}
