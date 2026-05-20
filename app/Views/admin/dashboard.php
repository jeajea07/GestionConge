<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> | TechMada RH</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body>
<section id="page-dashboard-admin">
<div class="app-wrap">
  <aside class="sidebar">
    <div>
      <div class="sidebar-brand">
        <div class="sidebar-logo-icon"><i class="bi bi-shield-check"></i></div>
        <div class="sidebar-brand-name">TechMada RH<span>Administration</span></div>
      </div>
      <div class="sidebar-section">Gestion</div>
      <ul class="sidebar-nav">
        <li><a href="<?= base_url('admin/dashboard') ?>" class="active"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
        <li><a href="<?= base_url('admin/employes') ?>"><i class="bi bi-people"></i> Employés</a></li>
      </ul>
    </div>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar avatar-admin">AD</div>
        <div>
          <div class="user-name"><?= esc($user_name) ?></div>
          <div class="user-role">Admin système</div>
        </div>
        <a href="<?= base_url('auth/logout') ?>" class="logout-link"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Vue d'ensemble</div>
        <div class="topbar-breadcrumb">Administration</div>
      </div>
      <div class="topbar-actions">
        <a href="<?= base_url('admin/employes') ?>" class="btn-forest btn-sm-inline"><i class="bi bi-person-plus"></i> Ajouter un employé</a>
      </div>
    </div>

    <div class="content">
      <div class="metrics">
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-people"></i></div></div>
          <div class="metric-val"><?= esc((string) $metrics['activeEmployees']) ?></div>
          <div class="metric-label">Employés actifs</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
          <div class="metric-val"><?= esc((string) $metrics['pendingRequests']) ?></div>
          <div class="metric-label">Demandes en attente</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-calendar-check"></i></div></div>
          <div class="metric-val"><?= esc((string) $metrics['approvedThisMonth']) ?></div>
          <div class="metric-label">Approuvées ce mois</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-blue"><i class="bi bi-building"></i></div></div>
          <div class="metric-val"><?= esc((string) $metrics['departments']) ?></div>
          <div class="metric-label">Départements</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-person-slash"></i></div></div>
          <div class="metric-val"><?= esc((string) $metrics['absentToday']) ?></div>
          <div class="metric-label">Absents aujourd'hui</div>
        </div>
      </div>

      <div class="data-card" style="padding: 24px;">
        <h3>Congés par mois — <?= date('Y') ?></h3>
        <canvas id="congeChartMois" height="100"></canvas>
      </div>

      <div class="data-card" style="padding: 24px;">
        <h3>Congés par jour — <?= date('Y') ?></h3>
        <div style="max-width: 400px; margin: 0 auto;">
            <canvas id="congeChartJours"></canvas>
        </div>
      </div>

      <div class="dashboard-grid">
        <div class="data-card no-gap">
          <div class="data-card-head">
            <h3>Demandes récentes</h3>
            <a href="#" class="card-link">Tout voir <i class="bi bi-arrow-right"></i></a>
          </div>
          <table class="tbl">
            <thead>
              <tr><th>Employé</th><th>Type</th><th>Durée</th><th>Statut</th></tr>
            </thead>
            <tbody>
              <?php if (! empty($recentRequests)): ?>
                <?php foreach ($recentRequests as $request): ?>
                  <?php
                    $initials = strtoupper(substr($request['nom'] ?? '?', 0, 1) . substr($request['prenom'] ?? '?', 0, 1));
                    $statusClass = ($request['statut'] ?? '') === 'approuvee' ? 's-approuvee' : 's-attente';
                    $typeClass = stripos((string) ($request['libelle'] ?? ''), 'malad') !== false ? 't-maladie' : 't-annuel';
                  ?>
                  <tr>
                    <td>
                      <div class="mini-profile">
                        <div class="avatar av-green avatar-xs"><?= esc($initials) ?></div>
                        <span class="td-name"><?= esc(($request['prenom'] ?? '') . ' ' . ($request['nom'] ?? '')) ?></span>
                      </div>
                    </td>
                    <td><span class="type-badge <?= esc($typeClass) ?>"><?= esc($request['libelle'] ?? 'Conge') ?></span></td>
                    <td class="td-mono"><?= esc((string) ($request['nb_jours'] ?? 0)) ?> j</td>
                    <td><span class="statut <?= esc($statusClass) ?>"><?= esc(str_replace('_', ' ', $request['statut'] ?? '')) ?></span></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="4" class="empty-cell">Aucune demande récente pour le moment.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="side-stack">
          <div class="data-card no-gap">
            <div class="data-card-head"><h3><i class="bi bi-person-slash"></i> Absents aujourd'hui</h3></div>
            <div class="absence-list">
              <?php if (! empty($absentsToday)): ?>
                <?php foreach ($absentsToday as $absence): ?>
                  <?php $initials = strtoupper(substr($absence['nom'] ?? '?', 0, 1) . substr($absence['prenom'] ?? '?', 0, 1)); ?>
                  <div class="absence-item">
                    <div class="avatar av-green avatar-sm"><?= esc($initials) ?></div>
                    <div>
                      <div class="absence-name"><?= esc(($absence['prenom'] ?? '') . ' ' . ($absence['nom'] ?? '')) ?></div>
                      <div class="absence-meta"><?= esc($absence['libelle'] ?? 'Congé') ?> · retour <?= esc($absence['date_fin'] ?? '-') ?></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="empty-note">Aucun absent aujourd'hui.</div>
              <?php endif; ?>
            </div>
          </div>
          <div class="flash flash-warn compact-flash">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span><?= esc((string) $criticalBalances) ?> employé(s) ont un solde critique (≤ 2 jours). <a href="<?= base_url('admin/employes') ?>">Voir les soldes →</a></span>
          </div>
        </div>
      </div>
    </div>

    <div class="footer-app"><i class="bi bi-c-circle"></i> 2026 <span>TechMada RH</span></div>
  </div>
</div>
</section>

<script src="<?= base_url('assets/js/chart.min.js') ?>"></script>
<script>
  const congeParMois = <?= json_encode($congeParMois) ?>;
  const congeParJours = <?= json_encode($congeParJours) ?>;
</script>

<script src="<?= base_url('assets/js/dashboard.js') ?>"></script>

</body>
</html>
