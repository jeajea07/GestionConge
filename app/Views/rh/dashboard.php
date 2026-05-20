<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Demandes à traiter | TechMada RH</title>
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
        <a href="<?= base_url('rh/dashboard') ?>" class="active">
          <i class="bi bi-inbox"></i> Demandes a traiter
          <span class="nav-badge alert"><?= esc((string) $pendingCount) ?></span>
        </a>
      </li>
      <li><a href="<?= base_url('rh/historique') ?>"><i class="bi bi-archive"></i> Historique</a></li>
      <li><a href="<?= base_url('rh/soldes') ?>"><i class="bi bi-people"></i> Soldes employes</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-blue">RH</div>
        <div><div class="user-name"><?= esc($user_name) ?></div><div class="user-role">Responsable RH</div></div>
        <a href="<?= base_url('auth/logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Demandes a traiter</div>
        <div class="topbar-breadcrumb"><a href="<?= base_url('rh/dashboard') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Demandes</div>
      </div>
      <div class="topbar-actions">
        <span style="font-size:.8rem;color:var(--muted);background:var(--warn-bg);border:1px solid var(--warn-br);border-radius:6px;padding:5px 10px;display:flex;align-items:center;gap:5px;color:var(--warn)">
          <i class="bi bi-hourglass-split"></i> <?= esc((string) $pendingCount) ?> en attente
        </span>
      </div>
    </div>

    <div class="content">
      <?php if (session()->getFlashdata('success')): ?>
        <div class="flash flash-success">
          <i class="bi bi-check-circle-fill"></i>
          <?= esc(session()->getFlashdata('success')) ?>
        </div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-error">
          <i class="bi bi-exclamation-circle-fill"></i>
          <?= esc(session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>

      <div class="data-card">
        <div class="data-card-head"><h3>Toutes les demandes</h3></div>
        
        <!-- Filtres -->
        <div style="display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap;padding:0 1.25rem;margin-top:1rem">
          <a href="<?= base_url('rh/dashboard') ?>" class="btn-filter <?php if (!isset($_GET['statut'])) echo 'active'; ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid <?php if (!isset($_GET['statut'])) echo 'var(--forest)'; else echo 'var(--border)'; ?>;background:<?php if (!isset($_GET['statut'])) echo 'var(--forest)'; else echo 'var(--white)'; ?>;color:<?php if (!isset($_GET['statut'])) echo 'var(--white)'; else echo 'var(--muted)'; ?>;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center">Tous (<?= esc((string) count($demandes)) ?>)</a>
          <a href="<?= base_url('rh/dashboard?statut=en_attente') ?>" class="btn-filter <?php if (isset($_GET['statut']) && $_GET['statut'] === 'en_attente') echo 'active'; ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid <?php if (isset($_GET['statut']) && $_GET['statut'] === 'en_attente') echo 'var(--forest)'; else echo 'var(--border)'; ?>;background:<?php if (isset($_GET['statut']) && $_GET['statut'] === 'en_attente') echo 'var(--forest)'; else echo 'var(--white)'; ?>;color:<?php if (isset($_GET['statut']) && $_GET['statut'] === 'en_attente') echo 'var(--white)'; else echo 'var(--muted)'; ?>;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center">En attente (<?= esc((string) $pendingCount) ?>)</a>
          <a href="<?= base_url('rh/dashboard?statut=approuvee') ?>" class="btn-filter <?php if (isset($_GET['statut']) && $_GET['statut'] === 'approuvee') echo 'active'; ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid <?php if (isset($_GET['statut']) && $_GET['statut'] === 'approuvee') echo 'var(--forest)'; else echo 'var(--border)'; ?>;background:<?php if (isset($_GET['statut']) && $_GET['statut'] === 'approuvee') echo 'var(--forest)'; else echo 'var(--white)'; ?>;color:<?php if (isset($_GET['statut']) && $_GET['statut'] === 'approuvee') echo 'var(--white)'; else echo 'var(--muted)'; ?>;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center">Approuvees (<?= esc((string) ($approvedCount ?? 0)) ?>)</a>
          <a href="<?= base_url('rh/dashboard?statut=refusee') ?>" class="btn-filter <?php if (isset($_GET['statut']) && $_GET['statut'] === 'refusee') echo 'active'; ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid <?php if (isset($_GET['statut']) && $_GET['statut'] === 'refusee') echo 'var(--forest)'; else echo 'var(--border)'; ?>;background:<?php if (isset($_GET['statut']) && $_GET['statut'] === 'refusee') echo 'var(--forest)'; else echo 'var(--white)'; ?>;color:<?php if (isset($_GET['statut']) && $_GET['statut'] === 'refusee') echo 'var(--white)'; else echo 'var(--muted)'; ?>;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center">Refusees (<?= esc((string) ($refusedCount ?? 0)) ?>)</a>
          <select class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto;margin-left:auto" onchange="if(this.value) window.location='<?= base_url('rh/dashboard?dept=') ?>' + this.value">
            <option value="">Tous les departements</option>
            <?php foreach ($departements ?? [] as $dept): ?>
              <option value="<?= esc((string) $dept['id']) ?>"><?= esc($dept['nom']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <table class="tbl">
          <thead>
            <tr><th>Employe</th><th>Type</th><th>Periode</th><th>Duree</th><th>Solde dispo</th><th>Statut</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php if (! empty($demandes)): ?>
              <?php foreach ($demandes as $demande): ?>
                <?php
                  $initials = strtoupper(substr($demande['prenom'] ?? '?', 0, 1) . substr($demande['nom'] ?? '?', 0, 1));
                  $statut = $demande['statut'] ?? 'en_attente';
                  $statutClass = match ($statut) {
                    'approuvee' => 's-approuvee',
                    'refusee' => 's-refusee',
                    'annulee' => 's-annulee',
                    default => 's-attente'
                  };
                  $typeLabel = $demande['type_libelle'] ?? 'Conge';
                  $typeKey = strtolower((string) $typeLabel);
                  $typeClass = 't-annuel';
                  if (strpos($typeKey, 'malad') !== false) {
                    $typeClass = 't-maladie';
                  } elseif (strpos($typeKey, 'special') !== false) {
                    $typeClass = 't-special';
                  } elseif (strpos($typeKey, 'sans') !== false) {
                    $typeClass = 't-sans-solde';
                  }
                  $attribues = (int) ($demande['jours_attribues'] ?? 0);
                  $pris = (int) ($demande['jours_pris'] ?? 0);
                  $restant = $attribues - $pris;
                  $isInsufficient = $restant < ($demande['nb_jours'] ?? 0);
                  $dateDebut = ! empty($demande['date_debut']) ? date('d/m/Y H:i', strtotime($demande['date_debut'])) : '-';
                  $dateFin = ! empty($demande['date_fin']) ? date('d/m/Y H:i', strtotime($demande['date_fin'])) : '-';
                ?>
                <tr>
                  <td>
                    <div class="profile-row">
                      <div class="avatar av-green" style="width:32px;height:32px;font-size:.7rem"><?= esc($initials) ?></div>
                      <div class="profile-info">
                        <div class="pname"><?= esc(($demande['prenom'] ?? '') . ' ' . ($demande['nom'] ?? '')) ?></div>
                        <div class="pdept"><?= esc($demande['departement_nom'] ?? '-') ?></div>
                      </div>
                    </div>
                  </td>
                  <td><span class="type-badge <?= esc($typeClass) ?>"><?= esc($typeLabel) ?></span></td>
                  <td class="td-muted" style="font-size:.8rem"><?= esc($dateDebut) ?> – <?= esc($dateFin) ?></td>
                  <td class="td-mono"><?= esc((string) ($demande['nb_jours'] ?? 0)) ?> j</td>
                  <td>
                    <span style="font-family:'DM Mono',monospace;font-size:.82rem;<?= $isInsufficient ? 'color:var(--warn);font-weight:500' : 'color:var(--success);font-weight:500' ?>"><?= esc((string) $restant) ?> j</span>
                    <span style="font-size:.72rem;color:<?= $isInsufficient ? 'var(--danger)' : 'var(--muted)' ?>"><?= $isInsufficient ? ' ⚠ insuffisant' : ' dispo' ?></span>
                  </td>
                  <td><span class="statut <?= esc($statutClass) ?>"><?= esc(str_replace('_', ' ', $statut)) ?></span></td>
                  <td>
                    <?php if ($statut === 'en_attente'): ?>
                      <div class="action-btns">
                        <a href="<?= base_url('rh/dashboard?action=approve&id=' . $demande['id']) ?>" class="btn-sm btn-approve" onclick="return confirm('Confirmez l\'approbation ?');"><i class="bi bi-check-lg"></i> Approuver</a>
                        <a href="<?= base_url('rh/dashboard?action=refuse&id=' . $demande['id']) ?>" class="btn-sm btn-refuse"><i class="bi bi-x-lg"></i> Refuser</a>
                      </div>
                    <?php else: ?>
                      <span class="td-muted" style="font-size:.75rem">Traitee</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="td-muted">Aucune demande trouvee.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Carte de confirmation -->
      <?php if ($selectedDemand && $action === 'approve'): ?>
        <?php
          $selectedDateDebut = ! empty($selectedDemand['date_debut']) ? date('d/m/Y H:i', strtotime($selectedDemand['date_debut'])) : '-';
          $selectedDateFin = ! empty($selectedDemand['date_fin']) ? date('d/m/Y H:i', strtotime($selectedDemand['date_fin'])) : '-';
        ?>
        <div style="margin-top:1.5rem">
          <div class="form-section" style="border-color:var(--success-br);background:var(--success-bg)">
            <h3 style="color:var(--success)"><i class="bi bi-check-circle"></i> Confirmer l'approbation — <?= esc(($selectedDemand['prenom'] ?? '') . ' ' . ($selectedDemand['nom'] ?? '')) ?></h3>
            <div style="font-size:.875rem;color:var(--ink);margin-bottom:1rem">
              Demande de <strong><?= esc((string) ($selectedDemand['nb_jours'] ?? 0)) ?> jours</strong> du <?= esc($selectedDateDebut) ?> au <?= esc($selectedDateFin) ?> · Type : <?= esc($selectedDemand['type_libelle'] ?? 'Congé') ?>
            </div>
            <form action="<?= base_url('rh/conges/approve/' . $selectedDemand['id']) ?>" method="post">
              <?= csrf_field() ?>
              <div class="f-group">
                <label class="f-label">Commentaire pour l'employé (optionnel)</label>
                <textarea class="f-textarea" name="commentaire_rh" placeholder="Votre commentaire ici..."></textarea>
              </div>
              <div class="form-actions">
                <button class="btn-sm btn-approve" style="padding:9px 16px;font-size:.875rem" type="submit"><i class="bi bi-check-lg"></i> Confirmer l'approbation</button>
                <a href="<?= base_url('rh/dashboard') ?>" class="btn-secondary"><i class="bi bi-arrow-left"></i> Annuler</a>
              </div>
            </form>
          </div>
        </div>
      <?php elseif ($selectedDemand && $action === 'refuse'): ?>
        <?php
          $selectedDateDebut = ! empty($selectedDemand['date_debut']) ? date('d/m/Y H:i', strtotime($selectedDemand['date_debut'])) : '-';
          $selectedDateFin = ! empty($selectedDemand['date_fin']) ? date('d/m/Y H:i', strtotime($selectedDemand['date_fin'])) : '-';
        ?>
        <div style="margin-top:1.5rem">
          <div class="form-section" style="border-color:var(--danger-br);background:var(--danger-bg)">
            <h3 style="color:var(--danger)"><i class="bi bi-x-circle"></i> Confirmer le refus — <?= esc(($selectedDemand['prenom'] ?? '') . ' ' . ($selectedDemand['nom'] ?? '')) ?></h3>
            <div style="font-size:.875rem;color:var(--ink);margin-bottom:1rem">
              Demande de <strong><?= esc((string) ($selectedDemand['nb_jours'] ?? 0)) ?> jours</strong> du <?= esc($selectedDateDebut) ?> au <?= esc($selectedDateFin) ?> · Type : <?= esc($selectedDemand['type_libelle'] ?? 'Congé') ?><br>
              <span style="font-size:.8rem;color:var(--muted)">Solde disponible : <?= esc((string) (($selectedDemand['jours_attribues'] ?? 0) - ($selectedDemand['jours_pris'] ?? 0))) ?> jours</span>
            </div>
            <form action="<?= base_url('rh/conges/refuse/' . $selectedDemand['id']) ?>" method="post">
              <?= csrf_field() ?>
              <div class="f-group">
                <label class="f-label">Commentaire pour l'employé (optionnel)</label>
                <textarea class="f-textarea" name="commentaire_rh" placeholder="Ex: Solde insuffisant, veuillez contacter les RH."></textarea>
              </div>
              <div class="form-actions">
                <button class="btn-sm btn-refuse" style="padding:9px 16px;font-size:.875rem" type="submit"><i class="bi bi-x-lg"></i> Confirmer le refus</button>
                <a href="<?= base_url('rh/dashboard') ?>" class="btn-secondary"><i class="bi bi-arrow-left"></i> Annuler</a>
              </div>
            </form>
          </div>
        </div>
      <?php endif; ?>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc(date('Y')) ?> <span>TechMada RH</span></div>
  </div>

</div>
</section>
</body>
</html>
