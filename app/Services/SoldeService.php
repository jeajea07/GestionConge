<?php

namespace App\Services;

// Import des Models
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;

use DateTime;

class SoldeService
{
    protected $soldeModel;
    protected $typeCongeModel;

    public function __construct()
    {
        $this->soldeModel = new SoldeModel();
        $this->typeCongeModel = new TypeCongeModel();
    }

    public function calculerJourRestant(int $employeId, int $typeCongeId, int $annee): int{
        $solde = $this->soldeModel->where('employe_id', $employeId)
                                  ->where('type_conge_id', $typeCongeId)
                                  ->where('annee', $annee)
                                  ->first();

        $restant = $solde['jour_attribues'] - $solde['jour_pris'];      

        return $restant;
    }

    public function getSoldeRestantByEmployeIdAndByTypeCongeId(int $employeId, int $typeCongeId, int $annee){
        $solde = $this->soldeModel->where('employe_id', $employeId)
                                  ->where('type_conge_id', $typeCongeId)
                                  ->where('annee', $annee)
                                  ->first();

        $typeConge = $this->typeCongeModel->find($typeCongeId);

        if(!$solde){
            return null;
        }

        $restant = $solde['jour_attribues'] - $solde['jour_pris'];      

        return [
            'employe_id' => $employeId,
            'type_conge_' => $typeConge['libelle'],
            'annee' => $annee,
            'jour_attribues' => $solde['jour_attribues'],
            'jour_pris' => $solde['jour_pris'],
            'jour_restant' => $restant
        ];
    }

    public function getAllSoldeRestantByEmployeIdAndByTypeCongeId(int $employeId, int $annee){
        $typeConge = $this->typeCongeModel->findAll();

        $result = [];
        foreach($typeConge as $type){
            $solde = $this->getSoldeRestantByEmployeIdAndByTypeCongeId($employeId, $type['id'], $annee);
            if($solde){
                $result[] = $solde;
            }
        }
        return $result;
    }

    public function getAllEmployeSolde(int $annee){
        $solde = $this->soldeModel->where('annee', $annee)->findAll();

        $result = [];
        foreach($solde as $s){
            $typeConge = $this->typeCongeModel->find($s['type_conge_id']);
            $result[] = [
                'employe_id' => $s['employe_id'],
                'type_conge_' => $typeConge['libelle'],
                'annee' => $s['annee'],
                'jour_attribues' => $s['jour_attribues'],
                'jour_pris' => $s['jour_pris'],
                'jour_restant' => $s['jour_attribues'] - $s['jour_pris']
            ];
        }
    }

}