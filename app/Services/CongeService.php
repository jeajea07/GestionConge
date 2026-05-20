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

    public function canSendDemandeConge(int $employeId, int $typeCongeId, int $annee, int $nb_jours, DateTime $dateDebut, DateTime $dateFin): bool
    {
        $solde = $this->soldeModel->where('employe_id', $employeId)
            ->where('type_conge_id', $typeCongeId)
            ->where('annee', $annee)
            ->first();

        if (!$solde) {
            return false;
        }

        $chevauchement = $this->verifyChevauchementConge($employeId, $dateDebut, $dateFin);

        if ($chevauchement) {
            throw new \Exception("Chevauchement detecte pour les dates selectionnees.");
        }

        $restant = $this->soldeService->calculerJourRestant($employeId, $typeCongeId, $annee);

        if ($restant < $nb_jours) {
            throw new \Exception("Solde insuffisant pour ce type de congé. Jours restants: $restant.");
        }

        return true;
    }

    public function verifyChevauchementConge(int $employeId, DateTime $dateDebut, DateTime $dateFin): bool
    {
        $conges = $this->congeModel->where('employe_id', $employeId)
            ->whereIn('statut', ['en_attente', 'approuvee'])
            ->findAll();

        foreach ($conges as $conge) {
            $congeDebut = new DateTime($conge['date_debut']);
            $congeFin = new DateTime($conge['date_fin']);

            if ($dateDebut <= $congeFin && $dateFin >= $congeDebut) {
                return true;
            }
        }

        return false;
    }

    public function demanderConge(int $employeId, int $typeCongeId, DateTime $dateDebut, DateTime $dateFin, int $nb_jours, String $motif, String $commentaire_rh, int $traite_par)
    {
        if ($this->canSendDemandeConge($employeId, $typeCongeId, (int)$dateDebut->format('Y'), $nb_jours, $dateDebut, $dateFin)) {
            $this->congeModel->insert([
                'employe_id' => $employeId,
                'type_conge_id' => $typeCongeId,
                'date_debut' => $dateDebut->format('Y-m-d H:i:s'),
                'date_fin' => $dateFin->format('Y-m-d H:i:s'),
                'nb_jours' => $nb_jours,
                'motif' => $motif,
                'commentaire_rh' => $commentaire_rh,
                'traite_par' => $traite_par
            ]);
        }
    }

    public function updateDemandeConge(int $congeId, int $typeCongeId, DateTime $dateDebut, DateTime $dateFin, int $nb_jours, String $motif)
    {
        $this->congeModel->update($congeId, [
            'type_conge_id' => $typeCongeId,
            'date_debut' => $dateDebut->format('Y-m-d H:i:s'),
            'date_fin' => $dateFin->format('Y-m-d H:i:s'),
            'nb_jours' => $nb_jours,
            'motif' => $motif
        ]);
    }

    public function annulerDemandeConge(int $congeId)
    {
        $conge = $this->congeModel->find($congeId);
        $last_statut = $conge['statut'];
        $statut = 'annulee';


        if ($last_statut == 'approuvee') {
            $solde = $this->soldeService->getSoldeRestantByEmployeIdAndByTypeCongeId($conge['employe_id'], $conge['type_conge_id'], (int)(new DateTime($conge['date_debut']))->format('Y'));

            $this->soldeModel->update($solde['id'], [
                'jours_pris' => $solde['jour_pris'] - $conge['nb_jours']
            ]);


            $this->congeModel->update(
                $congeId,
                [
                    'statut' => $statut
                ]
            );
        }
        $this->congeModel->update(
            $congeId,
            [
                'statut' => $statut
            ]
        );
    }


    public function getAllCongeByEmployeId(int $employeId)
    {
        $conges = $this->congeModel->where('employe_id', $employeId)
            ->findAll();
        return $conges;
    }

    public function getAllCongeByEmployeIdAndByStatut(int $employeId, String $statut)
    {
        $conges = $this->congeModel->where('employe_id', $employeId)
            ->where('statut', $statut)
            ->findAll();

        return $conges;
    }

    public function get3derniersCongeByEmployeId(int $employeId)
    {
        $conges = $this->congeModel->where('employe_id', $employeId)
            ->orderBy('date_debut', 'DESC')
            ->findAll(3);

        return $conges;
    }

    public function getAllCongeByEmployeIdAndByTypeCongeId(int $employeId, int $typeCongeId)
    {
        return $this->congeModel
            ->where('employe_id', $employeId)
            ->where('type_conge_id', $typeCongeId)
            ->findAll();
    }

    // RH
    public function traiterDemandeConge(int $congeId, string $statut, string $commentaire_rh, int $traite_par)
    {
        $this->congeModel->update($congeId, [
            'statut' => $statut,
            'commentaire_rh' => $commentaire_rh,
            'traite_par' => $traite_par,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function approuverDemandeConge(int $congeId, String $commentaire_rh, int $traite_par)
    {
        $conge = $this->congeModel->find($congeId);
        if (!$conge) {
            throw new \Exception('Demande de congé non trouvée.');
        }

        // Vérifier que le solde existe avant de traiter
        $solde = $this->soldeService->getSoldeRestantByEmployeIdAndByTypeCongeId(
            $conge['employe_id'],
            $conge['type_conge_id'],
            (int)(new DateTime($conge['date_debut']))->format('Y')
        );

        if (!$solde) {
            throw new \Exception('Solde non trouvé pour cet employé et ce type de congé.');
        }

        // Traiter la demande (changer le statut)
        $statut = 'approuvee';
        $this->traiterDemandeConge($congeId, $statut, $commentaire_rh, $traite_par);

        // Mettre à jour le solde
        $this->soldeModel->update($solde['id'], [
            'jours_pris' => $solde['jour_pris'] + $conge['nb_jours']
        ]);
    }

    public function refuserDemandeConge(int $congeId, String $commentaire_rh, int $traite_par)
    {
        $conge = $this->congeModel->find($congeId);
        if (!$conge) {
            throw new \Exception('Demande de congé non trouvée.');
        }

        $last_statut = $conge['statut'];
        $statut = 'refusee';

        if ($last_statut == 'approuvee') {
            $solde = $this->soldeService->getSoldeRestantByEmployeIdAndByTypeCongeId($conge['employe_id'], $conge['type_conge_id'], (int)(new DateTime($conge['date_debut']))->format('Y'));

            if (!$solde) {
                throw new \Exception('Solde non trouvé pour cet employé et ce type de congé.');
            }

            $this->soldeModel->update($solde['id'], [
                'jours_pris' => $solde['jour_pris'] - $conge['nb_jours']
            ]);
        }

        $this->traiterDemandeConge($congeId, $statut, $commentaire_rh, $traite_par);
    }

    public function getAllCongeByStatut(String $statut)
    {
        $conges = $this->congeModel->where('statut', $statut)
            ->findAll();
        return $conges;
    }

    public function getAllCongeByDepartementId(int $departementId)
    {
        $conges = $this->congeModel->join('employe', 'conge.employe_id = employe.id')
            ->where('employe.departement_id', $departementId)
            ->findAll();
        return $conges;
    }

    // Admin
    public function getAllConge()
    {
        $conges = $this->congeModel->findAll();
        return $conges;
    }

    public function getCongeById(int $congeId)
    {
        return $this->congeModel->find($congeId);
    }

    public function updateConge(int $congeId, array $data)
    {
        $this->congeModel->update($congeId, $data);
    }
}
