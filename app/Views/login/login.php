<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= esc($title ?? 'Connexion') ?> | TechMada RH</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>" />
</head>
<body>

<div class="auth-page geo-bg">
  <div class="auth-split">

    <!-- ── Panneau gauche ── -->
    <div class="auth-left">
      <div class="auth-left-content">
        <p class="auth-left-brand">
          TechMada RH
          <span>Gestion des congés</span>
        </p>
        <p class="auth-left-text">
          <strong>Bienvenue sur votre espace RH.</strong>
          Gérez vos demandes de congés, consultez votre solde et
          suivez l'état de vos demandes en temps réel.
        </p>
      </div>

      <div class="auth-roles">
        <div class="roles-label">Comptes de démonstration</div>

        <div class="role-pill" data-email="admin@techmada.mg" data-pass="admin123">
          <i class="bi bi-shield-check"></i>
          <div>
            <div class="role-pill-name">Administrateur</div>
            <div class="role-pill-cred">admin@techmada.mg · admin123</div>
          </div>
        </div>

        <div class="role-pill" data-email="rh@techmada.mg" data-pass="rh123">
          <i class="bi bi-person-check"></i>
          <div>
            <div class="role-pill-name">Responsable RH</div>
            <div class="role-pill-cred">rh@techmada.mg · rh123</div>
          </div>
        </div>

        <div class="role-pill" data-email="employe@techmada.mg" data-pass="emp123">
          <i class="bi bi-person"></i>
          <div>
            <div class="role-pill-name">Employé</div>
            <div class="role-pill-cred">employe@techmada.mg · emp123</div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Panneau droit ── -->
    <div class="auth-right">
      <p class="auth-title">Connexion</p>
      <p class="auth-sub">Entrez vos identifiants pour accéder à votre espace.</p>

      <!-- Flashdata CI4 (affichée seulement si présente) -->
      <?php if (session()->getFlashdata('error')): ?>
      <div class="flash flash-error">
        <i class="bi bi-exclamation-circle-fill"></i>
        <?= session()->getFlashdata('error') ?>
      </div>
      <?php endif; ?>

      <!-- Version démo (commentez le bloc PHP ci-dessus en développement statique) -->
      <!--
      <div class="flash flash-error" id="flash-error" style="display:none">
        <i class="bi bi-exclamation-circle-fill"></i>
        <span id="flash-error-msg">Identifiants incorrects. Veuillez réessayer.</span>
      </div>
      -->

      <form id="login-form" action="/auth/login" method="POST" novalidate autocomplete="off">
        <?= csrf_field() /* CI4 CSRF token — retirez si HTML statique */ ?>

        <div class="f-group">
          <label class="f-label" for="email">Adresse email</label>
          <input
            type="email"
            id="email"
            name="email"
            class="f-input"
            placeholder="vous@techmada.mg"
            autocomplete="off"
            autocapitalize="off"
            autocorrect="off"
            spellcheck="false"
            required
          />
        </div>

        <div class="f-group">
          <label class="f-label" for="password">Mot de passe</label>
          <div class="f-password-wrap">
            <input
              type="password"
              id="password"
              name="password"
              class="f-input"
              placeholder="••••••••"
              autocomplete="new-password"
              required
            />
            <button type="button" class="toggle-pass" id="toggle-pass" aria-label="Afficher/masquer le mot de passe">
              <i class="bi bi-eye" id="eye-icon"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-primary">
          Se connecter <i class="bi bi-arrow-right-short"></i>
        </button>
      </form>
    </div>

  </div>
</div>

<script src="<?= base_url('assets/js/login.js') ?>"></script>
</body>
</html>
