<?php

namespace App\Services;

// Import des Models
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;
use App\Models\CongeModel;
use App\Services\SoldeService;

use DateTime;

class CongeService
{
    protected $soldeModel;
    protected $typeCongeModel;
    protected $congeModel;
    protected $employeModel;
    protected $soldeService;

    public function __construct()
    {
        $this->soldeModel = new SoldeModel();
        $this->typeCongeModel = new TypeCongeModel();
        $this->congeModel = new CongeModel();
        $this->soldeService = new SoldeService();
    }

    // Employee

    public function canSendDemandeConge(int $employeId, int $typeCongeId, int $annee, int $nb_jours): bool{
        $solde = $this->soldeModel->where('employe_id', $employeId)
                                  ->where('type_conge_id', $typeCongeId)
                                  ->where('annee', $annee)
                                  ->first();

        if(!$solde){
            return false;
        }

        $chevauchement = $this->verifyChevauchementConge($employeId, new DateTime(), new DateTime());

        if($chevauchement){
            return false;
        }

        $restant = $this->soldeService->calculerJourRestant($employeId, $typeCongeId, $annee);
                
        return $restant >= $nb_jours;
    }

    public function verifyChevauchementConge(int $employeId, DateTime $dateDebut, DateTime $dateFin): bool{
        $conges = $this->congeModel->where('employe_id', $employeId)
                                    ->findAll();

        foreach($conges as $conge){
            if(($dateDebut >= new DateTime($conge['date_debut']) && $dateDebut <= new DateTime($conge['date_fin'])) ||
               ($dateFin >= new DateTime($conge['date_debut']) && $dateFin <= new DateTime($conge['date_fin']))){
                return true;
            }
        }

        return false;
    }

    public function demanderConge(int $employeId, int $typeCongeId, DateTime $dateDebut, DateTime $dateFin, int $nb_jours, String $motif, String $commentaire_rh, int $traite_par){
        if(!$this->canSendDemandeConge($employeId, $typeCongeId, (int)$dateDebut->format('Y'), $nb_jours)){
            throw new \Exception("Votre demande de conge n'est pas valide.");
        }

        $this->congeModel->insert([
            'employe_id' => $employeId,
            'type_conge_id' => $typeCongeId,
            'date_debut' => $dateDebut->format('Y-m-d'),
            'date_fin' => $dateFin->format('Y-m-d'),
            'nb_jours' => $nb_jours,
            'motif' => $motif,
            'commentaire_rh' => $commentaire_rh,
            'traite_par' => $traite_par
        ]);
    }

    public function updateDemandeConge(int $congeId, int $typeCongeId, DateTime $dateDebut, DateTime $dateFin, int $nb_jours, String $motif){
        $this->congeModel->update($congeId, [
            'type_conge_id' => $typeCongeId,
            'date_debut' => $dateDebut->format('Y-m-d'),
            'date_fin' => $dateFin->format('Y-m-d'),
            'nb_jours' => $nb_jours,
            'motif' => $motif
        ]);
    }

    public function getAllCongeByEmployeId(int $employeId){
        $conges = $this->congeModel->where('employe_id', $employeId)
                                    ->findAll();
        return $conges;
    }

    // RH
    public function traiterDemandeConge(int $congeId, string $statut, string $commentaire_rh, int $traite_par){
        $this->congeModel->update($congeId, [
            'statut' => $statut,
            'commentaire_rh' => $commentaire_rh,
            'traite_par' => $traite_par
        ]);
    }


}