<?php
require_once 'icon_helper.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: authForm.php?auth=nonAuth');
    exit();
}

$fullname = $_SESSION['fullname'] ?? $_SESSION['user'];
$role = $_SESSION['role'] ?? 'US';
$isAdmin = ($role === 'AD');
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');

function navbar_link_class($currentPage, $pages)
{
    return in_array($currentPage, (array) $pages, true) ? 'navbar-link active' : 'navbar-link';
}
?>

<link rel="stylesheet" href="cssfiles/navbar.css">

<nav class="navbar">
    <div class="navbar-container">
        <a href="dashboard.php" class="navbar-logo">
            <?= ui_icon('car') ?> Location de voitures
        </a>

        <button class="mobile-menu-btn" type="button" onclick="toggleMobileMenu()" aria-label="Ouvrir le menu">
            <?= ui_icon('bars-3') ?>
        </button>

        <div class="navbar-menu" id="navbarMenu">
            <?php if ($isAdmin): ?>
                <a href="dashboard.php" class="<?= navbar_link_class($currentPage, ['dashboard.php']) ?>">
                    <?= ui_icon('chart-bar') ?> Tableau de bord
                </a>
            <?php endif; ?>

            <div class="navbar-item dropdown">
                <a href="#" class="<?= navbar_link_class($currentPage, ['allcars.php', 'addcar.php', 'editcar.php', 'showcar.php']) ?>">
                    <?= ui_icon('car') ?> Voitures <?= ui_icon('chevron-down') ?>
                </a>
                <div class="dropdown-menu">
                    <a href="allcars.php"><?= ui_icon('clipboard') ?> Toutes les voitures</a>
                    <?php if ($isAdmin): ?>
                        <a href="addcar.php"><?= ui_icon('add') ?> Ajouter une voiture</a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($isAdmin): ?>
                <div class="navbar-item dropdown">
                    <a href="#" class="<?= navbar_link_class($currentPage, ['allrentals.php', 'addrental.php']) ?>">
                        <?= ui_icon('calendar') ?> Locations <?= ui_icon('chevron-down') ?>
                    </a>
                    <div class="dropdown-menu">
                        <a href="allrentals.php"><?= ui_icon('clipboard') ?> Toutes les locations</a>
                    </div>
                </div>
            <?php endif; ?>

            <span class="navbar-link navbar-user-badge">
                <?= ui_icon('user') ?> <?= htmlspecialchars($fullname . ' - ' . ($isAdmin ? 'Administrateur' : 'Utilisateur'), ENT_QUOTES, 'UTF-8') ?>
            </span>

            <a href="logout.php" class="navbar-link logout-link">
                <?= ui_icon('logout') ?> Deconnexion
            </a>
        </div>
    </div>
</nav>

<script>
function toggleMobileMenu() {
    document.getElementById('navbarMenu').classList.toggle('active');
}
</script>