create table departement (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom text,
    description text
);

create table employe (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom text,
    prenom text,
    email text unique,
    password_hash text,
    role text default 'employe',
    departement_id INTEGER,
    date_embauche date,
    actif boolean,
    foreign key (departement_id) references departement(id)
);

create table types_conge (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle text,
    jours_annuels INTEGER,
    deductible boolean
);

create table solde (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employe_id INTEGER,
    type_conge_id INTEGER,
    annee INTEGER,
    jours_attribues INTEGER,
    jours_pris INTEGER,
    foreign key (employe_id) references employe(id),
    foreign key (type_conge_id) references types_conge(id)
);

create table conge (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employe_id INTEGER,
    type_conge_id INTEGER,
    date_debut date,
    date_fin date,
    nb_jours INTEGER,
    motif text,
    statut default 'en_attente',
    commentaire_rh text,
    created_at datetime default current_timestamp,
    traite_par INTEGER,
    foreign key (employe_id) references employe(id),
    foreign key (type_conge_id) references types_conge(id),
    foreign key (traite_par) references employe(id)
);