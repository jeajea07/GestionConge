<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nouvelle demande | TechMada RH</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>" />
</head>
<body>
<section id="page-form-conge">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employe</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="<?= base_url('employe/dashboard') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="<?= base_url('employe/demande') ?>" class="active"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="<?= base_url('employe/conges') ?>"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
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
        <div class="topbar-title">Nouvelle demande de conge</div>
        <div class="topbar-breadcrumb">
          <a href="<?= base_url('employe/dashboard') ?>">Accueil</a>
          <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Nouvelle demande
        </div>
      </div>
    </div>

    <div class="content">

      <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start" class="form-layout">
        <div>
          <?php if (session()->has('error')): ?>
            <div class="flash flash-error" style="margin-bottom:1.5rem">
              <i class="bi bi-exclamation-circle-fill"></i>
              <span><?= esc(session()->getFlashdata('error')) ?></span>
            </div>
          <?php endif; ?>
          <?php if (session()->has('success')): ?>
            <div class="flash flash-success" style="margin-bottom:1.5rem">
              <i class="bi bi-check-circle-fill"></i>
              <span><?= esc(session()->getFlashdata('success')) ?></span>
            </div>
          <?php endif; ?>
          
          <div class="form-section">
            <h3>Details de la demande</h3>

            <form action="<?= base_url('employe/demande') ?>" method="post">
              <?= csrf_field() ?>
              <div class="f-group" style="margin-bottom:1rem">
                <label class="f-label">Type de conge <span style="color:var(--danger)">*</span></label>
                <select class="f-select" name="type_conge_id" required>
                  <option value="">-- Choisir un type --</option>
                  <?php foreach ($type_conge as $type): ?>
                    <option value="<?= esc((string) $type['id']) ?>"><?= esc($type['libelle']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-grid-2" style="margin-bottom:1rem">
                <div class="f-group">
                  <label class="f-label">Date de debut <span style="color:var(--danger)">*</span></label>
                  <input type="date" class="f-input" name="date_debut" required />
                </div>
                <div class="f-group">
                  <label class="f-label">Date de fin <span style="color:var(--danger)">*</span></label>
                  <input type="date" class="f-input" name="date_fin" required />
                </div>
              </div>

              <div class="f-group" style="margin-bottom:1rem">
                <label class="f-label">Motif (optionnel)</label>
                <textarea class="f-textarea" name="motif" placeholder="Precisez le motif de votre demande si necessaire..."></textarea>
                <div class="f-hint">Le motif est visible par le responsable RH.</div>
              </div>

              <div class="form-actions">
                <button class="btn-forest" type="submit"><i class="bi bi-send"></i> Soumettre la demande</button>
                <a href="<?= base_url('employe/dashboard') ?>" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
              </div>
            </form>
          </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:1rem">
          <div class="data-card" style="margin:0">
            <div class="data-card-head"><h3><i class="bi bi-piggy-bank" style="color:var(--forest);margin-right:5px"></i>Vos soldes actuels</h3></div>
            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.75rem">
              <?php if (! empty($soldes)): ?>
                <?php foreach ($soldes as $solde): ?>
                  <?php
                    $libelle = (string) ($solde['type_conge_'] ?? $solde['type_conge'] ?? 'Conge');
                    $attribues = (int) ($solde['jour_attribues'] ?? 0);
                    $pris = (int) ($solde['jour_pris'] ?? 0);
                    $restant = (int) ($solde['jour_restant'] ?? ($attribues - $pris));
                    $percent = $attribues > 0 ? (int) round(($restant / $attribues) * 100) : 0;
                    $fillClass = $percent <= 20 ? 'warn' : '';
                  ?>
                  <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                      <span style="font-size:.8rem;color:var(--ink)"><?= esc($libelle) ?></span>
                      <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--forest);font-weight:500"><?= esc((string) $restant) ?> j</span>
                    </div>
                    <div class="solde-bar"><div class="solde-fill <?= esc($fillClass) ?>" style="width:<?= esc((string) $percent) ?>%"></div></div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="empty"><i class="bi bi-emoji-frown"></i><p>Aucun solde disponible.</p></div>
              <?php endif; ?>
            </div>
          </div>
          <div class="flash flash-info" style="margin:0">
            <i class="bi bi-info-circle-fill"></i>
            <span style="font-size:.8rem">Le solde est deduit uniquement a l'approbation de votre responsable.</span>
          </div>
          <div style="background:var(--cream);border:1px solid var(--border);border-radius:8px;padding:.85rem 1rem">
            <div style="font-size:.78rem;font-weight:500;color:var(--ink);margin-bottom:.5rem"><i class="bi bi-clipboard-check" style="color:var(--forest);margin-right:5px"></i>Rappel des regles</div>
            <ul style="margin:0;padding-left:1rem;font-size:.75rem;color:var(--muted);line-height:1.7">
              <li>Preavis minimum : 48h avant la date de debut</li>
              <li>Pas de chevauchement avec une demande en cours</li>
              <li>Solde insuffisant = demande refusee automatiquement</li>
            </ul>
          </div>
        </div>

      </div>
    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc(date('Y')) ?> <span>TechMada RH</span></div>
  </div>

</div>
</section>
</body>
</html>
