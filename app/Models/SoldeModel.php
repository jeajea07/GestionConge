<?php

namespace App\Models;

use CodeIgniter\Model;

class SoldeModel extends Model
{
    protected $table = 'solde';
    protected $primaryKey = 'id';
    protected $allowedFields = ['employe_id', 'type_conge_id', 'annee', 'jour_attribues', 'jour_pris'];
}