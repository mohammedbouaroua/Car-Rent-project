<?php
// navbar.php - Barre de navigation principale

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: authForm.php?auth=nonAuth");
    exit();
}

$fullname = $_SESSION['fullname'] ?? $_SESSION['user'];
$role = $_SESSION['role'] ?? 'US';
$isAdmin = ($role === 'AD');
?>

<link rel="stylesheet" href="cssfiles/navbar.css">

<nav class="navbar">
    <div class="navbar-container">

        <!-- Logo -->
        <a href="dashboard.php" class="navbar-logo">
            🚗 Location de voitures
        </a>

        <!-- Mobile -->
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>

        <!-- Menu -->
        <div class="navbar-menu" id="navbarMenu">
            <?php if ($isAdmin): ?>

            <a href="dashboard.php" class="navbar-link">
                📊 Tableau de bord
            </a>
            <?php endif; ?>

            <!-- Voitures -->
            <div class="navbar-item dropdown">
                <a href="#" class="navbar-link">🚗 Voitures ▼</a>
                <div class="dropdown-menu">
                    <a href="allcars.php">📋 Toutes les voitures</a>

                    <?php if ($isAdmin): ?>
                        <a href="addcar.php">➕ Ajouter une voiture</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Locations -->
            <div class="navbar-item dropdown">
                <?php if ($isAdmin):{ ?>
                <a href="#" class="navbar-link">📅 Locations ▼</a>
                <div class="dropdown-menu">
                    <a href="allrentals.php">📋 Toutes les locations</a>
                </div>
                <?php }endif; ?>
            </div>

            <!-- User -->
            <span class="navbar-link" style="cursor: default;">
                👤 <?= htmlspecialchars($fullname . ' - ' . ($isAdmin ? 'Administrateur' : 'Utilisateur')) ?>
            </span>

            <a href="logout.php" class="navbar-link logout-link">
                🚪 Déconnexion
            </a>

        </div>
    </div>
</nav>

<script>
function toggleMobileMenu() {
    document.getElementById('navbarMenu').classList.toggle('active');
}
</script>