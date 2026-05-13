/**
 * TechMada RH — login.js
 * Comportements UI de la page de connexion
 */

document.addEventListener('DOMContentLoaded', () => {

  // ── Éléments ──────────────────────────────────────────────
  const form       = document.getElementById('login-form');
  const emailInput = document.getElementById('email');
  const passInput  = document.getElementById('password');
  const toggleBtn  = document.getElementById('toggle-pass');
  const eyeIcon    = document.getElementById('eye-icon');
  const submitBtn  = form?.querySelector('button[type="submit"]');

  // Vider les champs au chargement pour éviter l'autoremplissage navigateur.
  if (emailInput) emailInput.value = '';
  if (passInput) passInput.value = '';

  // ── 1. Afficher / masquer le mot de passe ─────────────────
  if (toggleBtn && passInput && eyeIcon) {
    toggleBtn.addEventListener('click', () => {
      const isHidden = passInput.type === 'password';
      passInput.type = isHidden ? 'text' : 'password';
      eyeIcon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
      toggleBtn.setAttribute('aria-label',
        isHidden ? 'Masquer le mot de passe' : 'Afficher le mot de passe'
      );
    });
  }

  // ── 2. Validation légère côté client ──────────────────────
  if (form) {
    form.addEventListener('submit', (e) => {
      let valid = true;

      // Email
      if (!emailInput?.value.trim() || !emailInput.value.includes('@')) {
        emailInput?.classList.add('error');
        valid = false;
      } else {
        emailInput?.classList.remove('error');
      }

      // Mot de passe
      if (!passInput?.value.trim()) {
        passInput?.classList.add('error');
        valid = false;
      } else {
        passInput?.classList.remove('error');
      }

      if (!valid) {
        e.preventDefault();
        return;
      }

      // ── État chargement ──
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Connexion…';
      }
    });

    // Retirer la classe error à la saisie
    [emailInput, passInput].forEach(input => {
      input?.addEventListener('input', () => input.classList.remove('error'));
    });
  }

  // ── 3. Auto-dismiss du flash après 5 s ────────────────────
  const flash = document.querySelector('.flash-error');
  if (flash) {
    setTimeout(() => {
      flash.style.transition = 'opacity .4s ease';
      flash.style.opacity = '0';
      setTimeout(() => flash.remove(), 400);
    }, 5000);
  }

});
