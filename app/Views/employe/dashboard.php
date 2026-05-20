<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tableau de bord | TechMada RH</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>" />
</head>

<body>
  <section id="page-dashboard-employe">
    <div class="app-wrap">
      <aside class="sidebar">
        <div class="sidebar-brand">
          <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
          <div class="sidebar-brand-name">TechMada RH<span>Espace employe</span></div>
        </div>
        <div class="sidebar-section">Menu</div>
        <ul class="sidebar-nav">
          <li><a href="<?= base_url('employe/dashboard') ?>" class="active"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
          <li><a href="<?= base_url('employe/demande') ?>"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
          <li><a href="<?= base_url('employe/conges') ?>"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
          <li><a href="<?= base_url('employe/calendar') ?>"><i class="bi bi-calendar3"></i> Calendrier</a></li>
          <li><a href="#"><i class="bi bi-person"></i> Mon profil</a></li>
        </ul>
        <div class="sidebar-user">
          <div class="s-user-row">
            <div class="avatar av-green">
              <?= esc(strtoupper(substr((string) session()->get('user_nom'), 0, 1))) ?>
            </div>
            <div>
              <div class="user-name"><?= esc(session()->get('user_nom') ?? 'Employe') ?></div>
              <div class="user-role">Employe</div>
            </div>
            <a href="<?= base_url('auth/logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Deconnexion"><i class="bi bi-box-arrow-right"></i></a>
          </div>
        </div>
      </aside>

      <div class="main">
        <div class="topbar">
          <div>
            <div class="topbar-title">Tableau de bord</div>
            <div class="topbar-breadcrumb">Accueil</div>
          </div>
          <div class="topbar-actions">
            <a href="<?= base_url('employe/demande') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
              <i class="bi bi-plus-lg"></i> Nouvelle demande
            </a>
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

          <?php
          $annual = null;
          foreach ($soldes as $solde) {
            $label = strtolower((string) ($solde['type_conge_'] ?? $solde['type_conge'] ?? ''));
            if (strpos($label, 'annuel') !== false) {
              $annual = $solde;
              break;
            }
          }
          $annualRestant = $annual['jour_restant'] ?? 0;
          $annualAttribues = $annual['jour_attribues'] ?? 0;
          ?>

          <div class="metrics">
            <div class="metric">
              <div class="metric-top">
                <div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div>
              </div>
              <div class="metric-val"><?= esc((string) $count_conge_en_attente) ?></div>
              <div class="metric-label">En attente</div>
            </div>
            <div class="metric">
              <div class="metric-top">
                <div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div>
              </div>
              <div class="metric-val"><?= esc((string) $count_conge_approuvee) ?></div>
              <div class="metric-label">Approuvees</div>
            </div>
            <div class="metric">
              <div class="metric-top">
                <div class="metric-icon mi-forest"><i class="bi bi-calendar-check"></i></div>
              </div>
              <div class="metric-val"><?= esc((string) $annualRestant) ?></div>
              <div class="metric-label">Jours restants</div>
              <div class="metric-sub">sur <?= esc((string) $annualAttribues) ?> cette annee</div>
            </div>
            <div class="metric">
              <div class="metric-top">
                <div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div>
              </div>
              <div class="metric-val"><?= esc((string) $count_conge_refusee) ?></div>
              <div class="metric-label">Refusees</div>
            </div>
          </div>

          <div class="data-card">
            <div class="data-card-head">
              <h3>Mes soldes de conges — <?= esc(date('Y')) ?></h3>
            </div>
            <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
              <?php if (! empty($soldes)): ?>
                <?php foreach ($soldes as $solde): ?>
                  <?php
                  $libelle = (string) ($solde['type_conge_'] ?? $solde['type_conge'] ?? 'Conge');
                  $attribues = (int) ($solde['jour_attribues'] ?? 0);
                  $pris = (int) ($solde['jour_pris'] ?? 0);
                  $restant = (int) ($solde['jour_restant'] ?? ($attribues - $pris));
                  $percent = $attribues > 0 ? (int) round(($restant / $attribues) * 100) : 0;
                  $fillClass = $percent <= 20 ? 'danger' : ($percent <= 40 ? 'warn' : '');
                  ?>
                  <div class="solde-card" style="margin:0">
                    <div class="solde-header">
                      <span class="solde-type"><?= esc($libelle) ?></span>
                      <span class="solde-nums"><strong><?= esc((string) $restant) ?></strong> / <?= esc((string) $attribues) ?> j</span>
                    </div>
                    <div class="solde-bar">
                      <div class="solde-fill <?= esc($fillClass) ?>" style="width:<?= esc((string) $percent) ?>%"></div>
                    </div>
                    <div class="solde-label"><?= esc((string) $restant) ?> jours restants · <?= esc((string) $pris) ?> pris</div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="empty"><i class="bi bi-emoji-frown"></i>
                  <p>Aucun solde disponible pour le moment.</p>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <div class="data-card">
            <div class="data-card-head">
              <h3>Mes historiques de demandes de conges</h3>
            </div>
            <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
              <?php if (! empty($demande_par_type_conge)): ?>
                <?php foreach ($demande_par_type_conge as $typeId => $demandes): ?>
                  <?php
                  $libelle = (string) ($type_map[$typeId]['libelle'] ?? 'Conge');
                  $count = count($demandes);
                  ?>
                  <div class="solde-card" style="margin:0">
                    <div class="solde-header">
                      <span class="solde-type"><?= esc($libelle) ?></span>
                      <span class="solde-nums"><?= esc((string) $count) ?> demande(s)</span>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="empty"><i class="bi bi-emoji-frown"></i>
                  <p>Aucun solde disponible pour le moment.</p>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <div class="data-card">
            <div class="data-card-head">
              <h3>Mes dernieres demandes</h3>
              <a href="<?= base_url('employe/conges') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout →</a>
            </div>
            <table class="tbl">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>Du</th>
                  <th>Au</th>
                  <th>Duree</th>
                  <th>Statut</th>
                  <th>Action</th>
                </tr>
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
                    $dateDebut = ! empty($conge['date_debut']) ? date('d/m/Y H:i', strtotime($conge['date_debut'])) : '-';
                    $dateFin = ! empty($conge['date_fin']) ? date('d/m/Y H:i', strtotime($conge['date_fin'])) : '-';
                    ?>
                    <tr>
                      <td><span class="type-badge <?= esc($typeClass) ?>"><?= esc($typeLabel) ?></span></td>
                      <td class="td-muted"><?= esc($dateDebut) ?></td>
                      <td class="td-muted"><?= esc($dateFin) ?></td>
                      <td class="td-mono"><?= esc((string) ($conge['nb_jours'] ?? 0)) ?> j</td>
                      <td><span class="statut <?= esc($statutClass) ?>"><?= esc(str_replace('_', ' ', $statut)) ?></span></td>
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
                  <tr>
                    <td colspan="6" class="td-muted">Aucune demande recente.</td>
                  </tr>
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
