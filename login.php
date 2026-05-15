<?php
// =============================================
// login.php - Prijava i registracija
// =============================================

require_once 'includes/auth.php';

// Ako je već prijavljen, idi na index
if (jePrijavljen()) {
    header('Location: index.php');
    exit;
}

$greska = '';
$uspjeh = '';
$tab = $_GET['tab'] ?? 'prijava'; // 'prijava' ili 'registracija'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['akcija']) && $_POST['akcija'] === 'registracija') {
        // --- REGISTRACIJA ---
        $tab = 'registracija';
        $ime     = trim($_POST['korisnicko_ime'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $loz     = $_POST['lozinka'] ?? '';
        $loz2    = $_POST['lozinka2'] ?? '';

        if ($loz !== $loz2) {
            $greska = 'Lozinke se ne podudaraju.';
        } else {
            $rez = registrirajKorisnika($ime, $email, $loz);
            if (isset($rez['ok'])) {
                $uspjeh = 'Registracija uspješna! Možete se prijaviti.';
                $tab = 'prijava';
            } else {
                $greska = $rez['greska'];
            }
        }
    } else {
        // --- PRIJAVA ---
        $ime = trim($_POST['korisnicko_ime'] ?? '');
        $loz = $_POST['lozinka'] ?? '';
        $rez = prijaviKorisnika($ime, $loz);
        if (isset($rez['ok'])) {
            header('Location: index.php');
            exit;
        } else {
            $greska = $rez['greska'];
        }
    }
}

// Poruka iz redirecta
if (isset($_GET['poruka']) && $_GET['poruka'] === 'morate_se_prijaviti') {
    $greska = 'Morate se prijaviti da biste pristupili toj stranici.';
}

$pageTitle    = 'Prijava | Netflix Filmovi';
$pageSubtitle = 'Prijava';
require_once 'includes/header.php';
?>

<div class="auth-wrapper fade-in">
  <div class="auth-box">
    <h2 class="auth-title">
      <?= $tab === 'registracija' ? '📋 Registracija' : '🔐 Prijava' ?>
    </h2>

    <!-- Tab navigacija -->
    <div class="auth-tabs">
      <a href="login.php?tab=prijava" class="auth-tab <?= $tab === 'prijava' ? 'active' : '' ?>">Prijava</a>
      <a href="login.php?tab=registracija" class="auth-tab <?= $tab === 'registracija' ? 'active' : '' ?>">Registracija</a>
    </div>

    <?php if ($greska): ?>
      <div class="alert alert-error">⚠️ <?= htmlspecialchars($greska) ?></div>
    <?php endif; ?>
    <?php if ($uspjeh): ?>
      <div class="alert alert-success">✅ <?= htmlspecialchars($uspjeh) ?></div>
    <?php endif; ?>

    <?php if ($tab === 'registracija'): ?>
    <!-- FORMA: REGISTRACIJA -->
    <form method="POST" action="login.php?tab=registracija" class="auth-form" novalidate>
      <input type="hidden" name="akcija" value="registracija">

      <div class="form-group">
        <label for="reg-ime">Korisničko ime *</label>
        <input type="text" id="reg-ime" name="korisnicko_ime"
               value="<?= htmlspecialchars($_POST['korisnicko_ime'] ?? '') ?>"
               minlength="3" maxlength="50" required placeholder="npr. filmofil123">
        <small>3–50 znakova</small>
      </div>

      <div class="form-group">
        <label for="reg-email">Email *</label>
        <input type="email" id="reg-email" name="email"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               required placeholder="vas@email.com">
      </div>

      <div class="form-group">
        <label for="reg-loz">Lozinka *</label>
        <input type="password" id="reg-loz" name="lozinka"
               minlength="6" required placeholder="najmanje 6 znakova">
      </div>

      <div class="form-group">
        <label for="reg-loz2">Potvrdi lozinku *</label>
        <input type="password" id="reg-loz2" name="lozinka2"
               minlength="6" required placeholder="ponovi lozinku">
      </div>

      <button type="submit" class="btn-primary" style="width:100%;margin-top:.5rem;">
        Registriraj se
      </button>
    </form>

    <?php else: ?>
    <!-- FORMA: PRIJAVA -->
    <form method="POST" action="login.php" class="auth-form">
      <div class="form-group">
        <label for="log-ime">Korisničko ime</label>
        <input type="text" id="log-ime" name="korisnicko_ime"
               value="<?= htmlspecialchars($_POST['korisnicko_ime'] ?? '') ?>"
               required placeholder="Vaše korisničko ime">
      </div>

      <div class="form-group">
        <label for="log-loz">Lozinka</label>
        <input type="password" id="log-loz" name="lozinka" required placeholder="Vaša lozinka">
      </div>

      <button type="submit" class="btn-primary" style="width:100%;margin-top:.5rem;">
        Prijavi se
      </button>
    </form>

    <div class="auth-hint">
      <strong>Demo:</strong> korisnik <code>admin</code> / lozinka <code>admin123</code>
    </div>
    <?php endif; ?>

  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
