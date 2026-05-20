<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Calendrier | TechMada RH</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>" />
  <style>
    .calendar-wrap {
      padding: 1rem 1.25rem 1.25rem;
    }

    #calendar {
      min-height: 680px;
    }

    .fc {
      --fc-border-color: var(--border);
      --fc-page-bg-color: var(--white);
      --fc-neutral-bg-color: var(--cream);
      --fc-today-bg-color: rgba(95, 168, 118, .14);
      --fc-event-bg-color: var(--forest);
      --fc-event-border-color: var(--forest);
      --fc-event-text-color: var(--white);
      color: var(--ink);
      font-family: 'DM Sans', sans-serif;
      font-size: .86rem;
    }

    .fc .fc-toolbar-title {
      color: var(--ink);
      font-family: 'Playfair Display', serif;
      font-size: 1.25rem;
      font-weight: 700;
    }

    .fc .fc-button {
      background: var(--white);
      border: 1.5px solid var(--border);
      border-radius: 7px;
      color: var(--muted);
      font-family: 'DM Sans', sans-serif;
      font-size: .8rem;
      font-weight: 500;
      padding: .38rem .7rem;
      text-transform: none;
    }

    .fc .fc-button:hover,
    .fc .fc-button:focus {
      background: var(--mint);
      border-color: var(--forest);
      box-shadow: none;
      color: var(--forest);
    }

    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active {
      background: var(--forest);
      border-color: var(--forest);
      color: var(--white);
    }

    .fc .fc-col-header-cell-cushion,
    .fc .fc-daygrid-day-number {
      color: var(--ink);
      text-decoration: none;
    }

    .fc .fc-col-header-cell-cushion {
      color: var(--muted);
      font-size: .72rem;
      font-weight: 600;
      letter-spacing: .06em;
      text-transform: uppercase;
    }

    .fc .fc-event {
      border-radius: 6px;
      font-size: .75rem;
      padding: 2px 4px;
    }

    .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
      color: var(--forest);
      font-weight: 700;
    }

    @media(max-width:760px) {
      #calendar {
        min-height: 560px;
      }

      .fc .fc-toolbar {
        align-items: flex-start;
        flex-direction: column;
        gap: .75rem;
      }

      .fc .fc-toolbar-title {
        font-size: 1.05rem;
      }
    }
  </style>
</head>

<body>
  <section id="page-calendar-employe">
    <div class="app-wrap">
      <aside class="sidebar">
        <div class="sidebar-brand">
          <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
          <div class="sidebar-brand-name">TechMada RH<span>Espace employe</span></div>
        </div>
        <div class="sidebar-section">Menu</div>
        <ul class="sidebar-nav">
          <li><a href="<?= base_url('employe/dashboard') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
          <li><a href="<?= base_url('employe/demande') ?>"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
          <li><a href="<?= base_url('employe/conges') ?>"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
          <li><a href="<?= base_url('employe/calendar') ?>" class="active"><i class="bi bi-calendar-week"></i> Calendrier</a></li>
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
            <div class="topbar-title">Calendrier des conges</div>
            <div class="topbar-breadcrumb">
              <a href="<?= base_url('employe/dashboard') ?>">Accueil</a>
              <i class="bi bi-chevron-right" style="font-size:.6rem"></i>
              Calendrier
            </div>
          </div>
          <div class="topbar-actions">
            <a href="<?= base_url('employe/demande') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
              <i class="bi bi-plus-lg"></i> Nouvelle demande
            </a>
          </div>
        </div>

        <div class="content">
          <div class="data-card">
            <div class="data-card-head">
              <h3>Mes conges approuves</h3>
              <a href="<?= base_url('employe/conges') ?>" class="btn-secondary" style="padding:6px 11px;font-size:.78rem">
                <i class="bi bi-list-ul"></i> Mes demandes
              </a>
            </div>
            <div class="calendar-wrap">
              <div id="calendar"></div>
            </div>
          </div>
        </div>

        <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc(date('Y')) ?> <span>TechMada RH</span></div>
      </div>
    </div>
  </section>

  <script src="<?= base_url('assets/js/index.global.min.js.js') ?>"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const calendarEl = document.getElementById('calendar');
      const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        height: 'auto',
        firstDay: 1,
        buttonText: {
          today: 'Aujourd hui',
          month: 'Mois',
          week: 'Semaine',
          day: 'Jour'
        },
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: <?= json_encode($events) ?>
      });

      calendar.render();
    });
  </script>
</body>

</html>
