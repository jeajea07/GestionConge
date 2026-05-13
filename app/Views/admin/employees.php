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
<section id="page-admin-employes">
<div class="app-wrap">
  <?php
    $isEditing = ! empty($editingEmployee);
    $formAction = $isEditing ? base_url('admin/employes/update/' . $editingEmployee['id']) : base_url('admin/employes/store');
    $submitLabel = $isEditing ? "Mettre à jour l'employé" : "Créer l'employé";
  ?>
  <aside class="sidebar">
    <div>
      <div class="sidebar-brand">
        <div class="sidebar-logo-icon"><i class="bi bi-shield-check"></i></div>
        <div class="sidebar-brand-name">TechMada RH<span>Administration</span></div>
      </div>
      <ul class="sidebar-nav sidebar-nav-spaced">
        <li><a href="<?= base_url('admin/dashboard') ?>"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
        <li><a href="#"><i class="bi bi-inbox"></i> Toutes les demandes</a></li>
        <li><a href="<?= base_url('admin/employes') ?>" class="active"><i class="bi bi-people"></i> Employés</a></li>
        <li><a href="#"><i class="bi bi-building"></i> Départements</a></li>
        <li><a href="#"><i class="bi bi-tags"></i> Types de congé</a></li>
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
        <div class="topbar-title">Gestion des employés</div>
        <div class="topbar-breadcrumb"><a href="<?= base_url('admin/dashboard') ?>">Admin</a> <i class="bi bi-chevron-right"></i> Employés</div>
      </div>
      <div class="topbar-actions">
        <a href="#employee-form" class="btn-forest btn-sm-inline"><i class="bi bi-person-plus"></i> Ajouter</a>
      </div>
    </div>

    <div class="content">
      <?php if (session()->getFlashdata('error')): ?>
          <div class="flash flash-error">
              <i class="bi bi-exclamation-circle-fill"></i>
              <span><?= esc(session()->getFlashdata('error')) ?></span>
          </div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('success')): ?>
          <div class="flash flash-success">
              <i class="bi bi-check-circle-fill"></i>
              <span><?= esc(session()->getFlashdata('success')) ?></span>
          </div>
      <?php endif; ?>

      <div class="form-section" id="employee-form">
        <h3><i class="bi <?= $isEditing ? 'bi-pencil-square' : 'bi-person-plus' ?>" style="color:var(--forest);margin-right:6px"></i><?= $isEditing ? "Modifier l'employé" : "Ajouter un employé" ?></h3>
        <form action="<?= $formAction ?>" method="post">
          <?= csrf_field() ?>
          <div class="form-grid-2 form-spaced">
            <div class="f-group">
              <label class="f-label">Prénom</label>
              <input type="text" name="prenom" class="f-input" required placeholder="Jean" value="<?= esc(old('prenom', $editingEmployee['prenom'] ?? '')) ?>"/>
            </div>
            <div class="f-group">
              <label class="f-label">Nom</label>
              <input type="text" name="nom" class="f-input" required placeholder="Rakoto" value="<?= esc(old('nom', $editingEmployee['nom'] ?? '')) ?>"/>
            </div>
            <div class="f-group">
              <label class="f-label">Email</label>
              <input type="email" name="email" class="f-input" required placeholder="jean.rakoto@techmada.mg" value="<?= esc(old('email', $editingEmployee['email'] ?? '')) ?>"/>
            </div>
            <div class="f-group">
              <label class="f-label">Mot de passe initial</label>
              <input type="password" name="password" class="f-input" <?= $isEditing ? '' : 'required' ?> placeholder="<?= $isEditing ? 'Laisser vide pour conserver le mot de passe actuel' : "À communiquer à l'employé" ?>"/>
            </div>
            <div class="f-group">
              <label class="f-label">Département</label>
              <select name="departement_id" class="f-select">
                <?php foreach($departements as $dept): ?>
                  <option value="<?= $dept['id'] ?>" <?= (string) old('departement_id', $editingEmployee['departement_id'] ?? '') === (string) $dept['id'] ? 'selected' : '' ?>><?= esc($dept['nom']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="f-group">
              <label class="f-label">Rôle</label>
              <select name="role" class="f-select">
                <option value="employe" <?= old('role', $editingEmployee['role'] ?? 'employe') === 'employe' ? 'selected' : '' ?>>Employé</option>
                <option value="rh" <?= old('role', $editingEmployee['role'] ?? '') === 'rh' ? 'selected' : '' ?>>Responsable RH</option>
                <option value="admin" <?= old('role', $editingEmployee['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrateur</option>
              </select>
            </div>
            <div class="f-group">
              <label class="f-label">Date d'embauche</label>
              <input type="date" name="date_embauche" class="f-input" value="<?= esc(old('date_embauche', $editingEmployee['date_embauche'] ?? date('Y-m-d'))) ?>"/>
            </div>
          </div>
          <div class="flash flash-info">
            <i class="bi bi-info-circle-fill"></i>
            <span>Les soldes de congés seront initialisés automatiquement selon les types de congé configurés.</span>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn-forest"><i class="bi <?= $isEditing ? 'bi-check2' : 'bi-plus' ?>"></i> <?= $submitLabel ?></button>
            <button type="reset" class="btn-secondary">Réinitialiser</button>
            <?php if ($isEditing): ?>
              <a href="<?= base_url('admin/employes') ?>#employee-form" class="btn-secondary btn-linkish">Annuler</a>
            <?php endif; ?>
          </div>
        </form>
      </div>

      <div class="data-card">
        <div class="data-card-head data-card-head-row">
            <h3>Tous les employés</h3>
            <div class="filter-row">
              <input type="text" placeholder="Rechercher..." class="f-input f-input-compact">
              <select class="f-select f-select-compact" id="department-filter">
                <option value="">Tous les depts</option>
                <?php foreach ($departements as $dept): ?>
                  <option value="<?= esc($dept['nom']) ?>"><?= esc($dept['nom']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
        </div>
        <table class="tbl">
          <thead>
            <tr>
              <th>Employé</th>
              <th>Département</th>
              <th>Rôle</th>
              <th>Embauche</th>
              <th>Statut</th>
              <th>Solde annuel</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (! empty($employes)): ?>
                <?php foreach ($employes as $emp): ?>
                <?php
                  $balance = $annualBalances[$emp['id']] ?? null;
                  $initials = strtoupper(substr($emp['nom'] ?? '?', 0, 1) . substr($emp['prenom'] ?? '?', 0, 1));
                ?>
                <tr <?= $emp['actif'] == 0 ? 'class="row-inactive"' : '' ?> data-departement="<?= esc($emp['departement_nom'] ?? '') ?>">
                  <td>
                    <div class="profile-row">
                      <div class="avatar av-green avatar-sm"><?= esc($initials) ?></div>
                      <div class="profile-info">
                        <div class="pname"><?= esc($emp['nom'] . ' ' . $emp['prenom']) ?></div>
                        <div class="pdept"><?= esc($emp['email']) ?></div>
                      </div>
                    </div>
                  </td>
                  <td class="td-muted"><?= esc($emp['departement_nom'] ?? '-') ?></td>
                  <td><span class="type-badge t-<?= esc($emp['role']) ?>"><?= esc($emp['role']) ?></span></td>
                  <td class="td-muted td-mono"><?= esc($emp['date_embauche']) ?></td>
                  <td><span class="statut <?= $emp['actif'] == 1 ? 's-approuvee' : 's-annulee' ?>"><?= $emp['actif'] == 1 ? 'actif' : 'inactif' ?></span></td>
                  <td>
                    <?php if ($balance !== null): ?>
                      <span class="balance-value"><?= esc((string) $balance['restants']) ?> / <?= esc((string) $balance['attribues']) ?> j</span>
                    <?php else: ?>
                      <span class="balance-value muted">— / — j</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="action-btns">
                      <a href="<?= base_url('admin/employes?edit=' . $emp['id']) ?>#employee-form" class="btn-sm btn-edit" title="Modifier"><i class="bi bi-pencil"></i></a>
                      <?php if ((int) $emp['actif'] === 1): ?>
                        <form action="<?= base_url('admin/employes/deactivate/' . $emp['id']) ?>" method="post" class="inline-form">
                          <?= csrf_field() ?>
                          <button type="submit" class="btn-sm btn-del" title="Désactiver"><i class="bi bi-slash-circle"></i></button>
                        </form>
                      <?php else: ?>
                        <form action="<?= base_url('admin/employes/reactivate/' . $emp['id']) ?>" method="post" class="inline-form">
                          <?= csrf_field() ?>
                          <button type="submit" class="btn-sm btn-view" title="Réactiver"><i class="bi bi-arrow-counterclockwise"></i></button>
                        </form>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="empty-cell">Aucun employé trouvé.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2026 <span>TechMada RH</span></div>
  </div>
</div>
</section>

<script src="<?= base_url('assets/js/admin.js') ?>"></script>
</body>
</html>
