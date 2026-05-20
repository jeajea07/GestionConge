<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Soldes Employés | TechMada RH</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>" />
</head>
<body>
<section id="page-liste-rh">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-person-check"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace responsable</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <li>
        <a href="<?= base_url('rh/dashboard') ?>">
          <i class="bi bi-inbox"></i> Demandes a traiter
          <span class="nav-badge alert">0</span>
        </a>
      </li>
      <li><a href="<?= base_url('rh/historique') ?>"><i class="bi bi-archive"></i> Historique</a></li>
      <li><a href="<?= base_url('rh/soldes') ?>" class="active"><i class="bi bi-people"></i> Soldes employes</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-blue">RH</div>
        <div><div class="user-name"><?= esc($user_name ?? 'Responsable RH') ?></div><div class="user-role">Responsable RH</div></div>
        <a href="<?= base_url('auth/logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Soldes des employes</div>
        <div class="topbar-breadcrumb"><a href="<?= base_url('rh/dashboard') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Soldes</div>
      </div>
      <div class="topbar-actions">
        <span style="font-size:.8rem;color:var(--muted);background:var(--info-bg);border:1px solid var(--info-br);border-radius:6px;padding:5px 10px;display:flex;align-items:center;gap:5px;color:var(--info)">
          <i class="bi bi-calendar-event"></i> Année <?= esc((string) $year) ?>
        </span>
      </div>
    </div>

    <div class="content">
      <div class="data-card">
        <div class="data-card-head"><h3>Soldes de conges</h3></div>
        
        <!-- Filtres -->
        <div style="display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap;padding:0 1.25rem;margin-top:1rem">
          <a href="<?= base_url('rh/soldes') ?>" class="btn-filter <?php if (!$selectedType) echo 'active'; ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid <?php if (!$selectedType) echo 'var(--forest)'; else echo 'var(--border)'; ?>;background:<?php if (!$selectedType) echo 'var(--forest)'; else echo 'var(--white)'; ?>;color:<?php if (!$selectedType) echo 'var(--white)'; else echo 'var(--muted)'; ?>;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center">Tous les types (<?= esc((string) count($soldes)) ?>)</a>
          <?php foreach ($types as $type): ?>
            <a href="<?= base_url('rh/soldes?type=' . urlencode($type)) ?>" class="btn-filter <?php if ($selectedType === $type) echo 'active'; ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid <?php if ($selectedType === $type) echo 'var(--forest)'; else echo 'var(--border)'; ?>;background:<?php if ($selectedType === $type) echo 'var(--forest)'; else echo 'var(--white)'; ?>;color:<?php if ($selectedType === $type) echo 'var(--white)'; else echo 'var(--muted)'; ?>;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center">
              <?= esc($type) ?> 
              (<?= esc((string) count(array_filter($soldes, function($s) use ($type) { return $s['type_conge_'] === $type; }))) ?>)
            </a>
          <?php endforeach; ?>
        </div>

        <table class="tbl">
          <thead>
            <tr><th>Employe</th><th>Departement</th><th>Type</th><th>Attribues</th><th>Pris</th><th>Restant</th><th>% Utilisation</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php if (!empty($soldes)): ?>
              <?php foreach ($soldes as $solde): ?>
                <?php
                  $initials = strtoupper(substr($solde['prenom'] ?? '?', 0, 1) . substr($solde['nom'] ?? '?', 0, 1));
                  $attribues = (int) $solde['jour_attribues'];
                  $pris = (int) $solde['jour_pris'];
                  $restant = $solde['jour_restant'];
                  $pourcentage = $attribues > 0 ? round(($pris / $attribues) * 100) : 0;
                  
                  // Déterminer la couleur en fonction du pourcentage
                  $percentageColor = 'var(--success)';
                  if ($pourcentage >= 80) {
                    $percentageColor = 'var(--danger)';
                  } elseif ($pourcentage >= 60) {
                    $percentageColor = 'var(--warn)';
                  }
                ?>
                <tr>
                  <td>
                    <div class="profile-row">
                      <div class="avatar av-green" style="width:32px;height:32px;font-size:.7rem"><?= esc($initials) ?></div>
                      <div class="profile-info">
                        <div class="pname"><?= esc(($solde['prenom'] ?? '') . ' ' . ($solde['nom'] ?? '')) ?></div>
                        <div class="pdept"><?= esc($solde['departement_nom'] ?? '-') ?></div>
                      </div>
                    </div>
                  </td>
                  <td class="td-muted" style="font-size:.8rem"><?= esc($solde['departement_nom'] ?? '-') ?></td>
                  <td><span class="type-badge t-annuel"><?= esc($solde['type_conge_']) ?></span></td>
                  <td class="td-mono" style="font-family:'DM Mono',monospace;font-size:.82rem"><?= esc((string) $attribues) ?> j</td>
                  <td class="td-mono" style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--warn)"><?= esc((string) $pris) ?> j</td>
                  <td class="td-mono" style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--success);font-weight:500"><?= esc((string) $restant) ?> j</td>
                  <td>
                    <div style="display:flex;align-items:center;gap:8px">
                      <div style="width:60px;height:6px;background:var(--border);border-radius:3px;overflow:hidden">
                        <div style="height:100%;width:<?= $pourcentage ?>%;background:<?= $percentageColor ?>;transition:width 0.3s"></div>
                      </div>
                      <span style="font-size:.8rem;color:<?= $percentageColor ?>;font-weight:500;min-width:35px"><?= esc((string) $pourcentage) ?>%</span>
                    </div>
                  </td>
                  <td>
                    <a href="<?= base_url('rh/soldes/edit/' . $solde['id']) ?>" class="btn-sm btn-approve"><i class="bi bi-pencil"></i> Modifier</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="8" class="td-muted">Aucun solde trouve.</td></tr>
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
