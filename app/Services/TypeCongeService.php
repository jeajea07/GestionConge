<?php

namespace App\Services;

// Import des Models
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;

use DateTime;

class TypeCongeService
{
    protected $soldeModel;
    protected $typeCongeModel;

    public function __construct()
    {
        $this->soldeModel = new SoldeModel();
        $this->typeCongeModel = new TypeCongeModel();
    }

    public function getAllTypeConge()
    {
        return $this->typeCongeModel->findAll();
    }

}