<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mes demandes | TechMada RH</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>" />
</head>
<body>
<section id="page-mes-conges">
<div class="app-wrap">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employe</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="<?= base_url('employe/dashboard') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="<?= base_url('employe/demande') ?>"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="<?= base_url('employe/conges') ?>" class="active"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
      <li><a href="#"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green">
          <?= esc(strtoupper(substr((string) session()->get('user_nom'), 0, 1))) ?>
        </div>
        <div><div class="user-name"><?= esc(session()->get('user_nom') ?? 'Employe') ?></div><div class="user-role">Employe</div></div>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Mes demandes de conge</div>
        <div class="topbar-breadcrumb"><a href="<?= base_url('employe/dashboard') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Mes demandes</div>
      </div>
      <div class="topbar-actions">
        <a href="<?= base_url('employe/demande') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-plus-lg"></i> Nouvelle demande</a>
      </div>
    </div>

    <div class="content">
      <div class="data-card">
        <div class="data-card-head">
          <h3>Toutes mes demandes</h3>
          <div style="display:flex;gap:6px">
            <form id="filter-form" method="get" style="display:flex;gap:6px">
              <select class="f-select" name="statut" style="font-size:.8rem;padding:6px 10px;width:auto" onchange="document.getElementById('filter-form').submit();">
                <option value="">Tous les statuts</option>
                <?php $selectedStatut = isset($_GET['statut']) ? $_GET['statut'] : ''; ?>
                <?php foreach ($liste_statut as $statut): ?>
                  <option value="<?= esc($statut) ?>" <?php if ($selectedStatut === $statut) echo 'selected'; ?>><?= esc(ucfirst(str_replace('_', ' ', $statut))) ?></option>
                <?php endforeach; ?>
              </select>
            </form>
          </div>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Type</th><th>Debut</th><th>Fin</th><th>Duree</th><th>Statut</th><th>Commentaire RH</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php if (! empty($conges)): ?>
              <?php foreach ($conges as $conge): ?>
                <?php
                  $type = $type_map[$conge['type_conge_id']] ?? null;
                  $typeLabel = $type['libelle'] ?? 'Conge';
                  $typeKey = strtolower((string) $typeLabel);
                  $typeClass = 't-annuel';
                  if (strpos($typeKey, 'malad') !== false) {
                    $typeClass = 't-maladie';
                  } elseif (strpos($typeKey, 'special') !== false) {
                    $typeClass = 't-special';
                  } elseif (strpos($typeKey, 'sans') !== false) {
                    $typeClass = 't-sans-solde';
                  }
                  $statut = $conge['statut'] ?? 'en_attente';
                  $statutClass = match ($statut) {
                    'approuvee' => 's-approuvee',
                    'refusee' => 's-refusee',
                    'annulee' => 's-annulee',
                    default => 's-attente'
                  };
                ?>
                <tr>
                  <td><span class="type-badge <?= esc($typeClass) ?>"><?= esc($typeLabel) ?></span></td>
                  <td class="td-muted"><?= esc($conge['date_debut'] ?? '-') ?></td>
                  <td class="td-muted"><?= esc($conge['date_fin'] ?? '-') ?></td>
                  <td class="td-mono"><?= esc((string) ($conge['nb_jours'] ?? 0)) ?> j</td>
                  <td><span class="statut <?= esc($statutClass) ?>"><?= esc(str_replace('_', ' ', $statut)) ?></span></td>
                  <td class="td-muted" style="font-size:.78rem"><?= esc($conge['commentaire_rh'] ?? '—') ?></td>
                  <td>
                    <?php if ($statut === 'en_attente' || $statut === 'approuvee'): ?>
                      <form action="<?= base_url('employe/conges/cancel/' . $conge['id']) ?>" method="post" style="display:inline">
                        <?= csrf_field() ?>
                        <button class="btn-sm btn-cancel" type="submit" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette demande ?');"><i class="bi bi-x"></i> Annuler</button>
                      </form>
                    <?php else: ?>
                      <span class="td-muted" style="font-size:.75rem">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="td-muted">Aucune demande enregistree.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc(date('Y')) ?> <span>TechMada RH</span></div>
  </div>

</div>
</section>
</body>
</html>
