<?php

namespace App\Controllers;

use App\Services\CongeService;
use App\Services\SoldeService;

class DashboardController extends BaseController
{
    public function index(){
        $congeService = new CongeService();
        $soldeService = new SoldeService();

        $employeId = session()->get('employe_id');
        $annee = date('Y');

        $conges = $congeService->get3derniersCongeByEmployeId($employeId);
        $conge_en_attente = $congeService->getAllCongeByEmployeIdAndByStatut($employeId, 'en_attente');
        $conge_approuvee = $congeService->getAllCongeByEmployeIdAndByStatut($employeId, 'approuvee');
        $conge_refusee = $congeService->getAllCongeByEmployeIdAndByStatut($employeId, 'refusee');
        $soldes = $soldeService->getAllSoldeRestantByEmployeIdAndByTypeCongeId($employeId, $annee);


        return view('employe/dashboard', [
            'conges' => $conges,
            'count_conge_en_attente' => count($conge_en_attente),
            'count_conge_approuvee' => count($conge_approuvee),
            'count_conge_refusee' => count($conge_refusee),
            'soldes' => $soldes
        ]);
    }

    public function showFormDemandeConge(){
        return view('employe/demande_conge');
    }

}
