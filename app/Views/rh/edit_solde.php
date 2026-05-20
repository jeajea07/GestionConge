<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Modifier Solde | TechMada RH</title>
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
        <div class="topbar-title">Modifier solde</div>
        <div class="topbar-breadcrumb"><a href="<?= base_url('rh/dashboard') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> <a href="<?= base_url('rh/soldes') ?>">Soldes</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Modifier</div>
      </div>
    </div>

    <div class="content">
      <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-error">
          <i class="bi bi-exclamation-circle-fill"></i>
          <?= esc(session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>

      <div class="form-section">
        <h3><i class="bi bi-pencil-square"></i> Modifier le solde</h3>
        
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid var(--border)">
          <div>
            <div style="font-size:.8rem;color:var(--muted);margin-bottom:.5rem">Employé</div>
            <div style="font-size:1rem;font-weight:500"><?= esc(($employe['prenom'] ?? '') . ' ' . ($employe['nom'] ?? '')) ?></div>
            <div style="font-size:.85rem;color:var(--muted);margin-top:.25rem"><?= esc($departement['nom'] ?? '-') ?></div>
          </div>
          <div>
            <div style="font-size:.8rem;color:var(--muted);margin-bottom:.5rem">Type de congé</div>
            <div style="font-size:1rem;font-weight:500"><?= esc($typeConge['libelle'] ?? '-') ?></div>
            <div style="font-size:.85rem;color:var(--muted);margin-top:.25rem">Année <?= esc((string) ($solde['annee'] ?? date('Y'))) ?></div>
          </div>
        </div>

        <form action="<?= base_url('rh/soldes/update/' . $solde['id']) ?>" method="post">
          <?= csrf_field() ?>
          
          <div class="f-group">
            <label class="f-label">Jours attribués</label>
            <input type="number" class="f-control" name="jours_attribues" value="<?= esc((string) $solde['jours_attribues']) ?>" min="0" max="365" required />
            <div style="font-size:.75rem;color:var(--muted);margin-top:.5rem">Total de jours alloués pour cette année</div>
          </div>

          <div class="f-group" style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border)">
            <div style="font-size:.85rem;color:var(--muted);margin-bottom:.5rem">Jours pris (lecture seule)</div>
            <div style="font-size:1.1rem;font-weight:500;color:var(--warn)"><?= esc((string) $solde['jours_pris']) ?> jours</div>
          </div>

          <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--border);display:flex;gap:1rem;align-items:center">
            <button type="submit" class="btn-sm btn-approve"><i class="bi bi-check-lg"></i> Enregistrer les modifications</button>
            <a href="<?= base_url('rh/soldes') ?>" class="btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
          </div>
        </form>
      </div>
    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc(date('Y')) ?> <span>TechMada RH</span></div>
  </div>

</div>
</section>
</body>
</html>
