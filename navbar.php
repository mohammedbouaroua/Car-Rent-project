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

<style>
/* ===== NAVBAR STYLE (inchangé) ===== */
.navbar {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: white;
    padding: 0;
    margin-bottom: 30px;
    border-radius: 0 0 10px 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 100;
}

.navbar-container {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    padding: 0 20px;
}

.navbar-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 20px;
    font-weight: bold;
    color: white;
    text-decoration: none;
    padding: 15px 0;
}

.navbar-logo:hover {
    color: #667eea;
}

.navbar-menu {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}

.navbar-item {
    position: relative;
}

.navbar-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    color: #e2e8f0;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s;
    font-size: 14px;
    font-weight: 500;
}

.navbar-link:hover {
    background: rgba(102, 126, 234, 0.2);
    color: #667eea;
}

.navbar-link.active {
    background: #667eea;
    color: white;
}

.dropdown {
    position: relative;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    min-width: 200px;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
    z-index: 1000;
}

.dropdown:hover .dropdown-menu {
    opacity: 1;
    visibility: visible;
}

.dropdown-menu a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    color: #333;
    text-decoration: none;
    border-bottom: 1px solid #eee;
}

.dropdown-menu a:hover {
    background: #667eea;
    color: white;
}

.logout-link {
    color: #ef4444;
}

.logout-link:hover {
    background: rgba(239, 68, 68, 0.2);
}

.mobile-menu-btn {
    display: none;
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    padding: 10px;
}

@media (max-width: 992px) {
    .mobile-menu-btn { display: block; }

    .navbar-menu {
        display: none;
        width: 100%;
        flex-direction: column;
    }

    .navbar-menu.active {
        display: flex;
    }
}
</style>

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

            <a href="dashboard.php" class="navbar-link">
                📊 Tableau de bord
            </a>

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
                <a href="#" class="navbar-link">📅 Locations ▼</a>
                <div class="dropdown-menu">
                    <a href="allrentals.php">📋 Toutes les locations</a>
                    <a href="allcars.php">➕ Nouvelle location</a>
                </div>
            </div>

            <!-- Clients -->
            <?php if ($isAdmin): ?>
                <div class="navbar-item dropdown">
                    <a href="#" class="navbar-link">👥 Clients ▼</a>
                    <div class="dropdown-menu">
                        <a href="allcustomers.php">📋 Tous les clients</a>
                        <a href="addcustomer.php">➕ Ajouter un client</a>
                    </div>
                </div>
            <?php endif; ?>

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