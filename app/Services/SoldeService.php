<?php

namespace App\Services;

// Import des Models
use App\Models\SoldeModel;

use DateTime;

class SoldeService
{
    protected $soldeModel;

    public function __construct()
    {
        $this->soldeModel = new SoldeModel();
    }

    public function calculerJourRestant(int $employeId, int $typeCongeId, int $annee): int{
        $solde = $this->soldeModel->where('employe_id', $employeId)
                                  ->where('type_conge_id', $typeCongeId)
                                  ->where('annee', $annee)
                                  ->first();

        $restant = $solde['jour_attribues'] - $solde['jour_pris'];      
                
        return $restant;
    }
}