<?php
require 'checksession.php';
require 'dbconnection.php';
require 'editstatusrentals.php';
require_once 'icon_helper.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'AD') {
    header('Location: authForm.php?auth=access_denied');
}
require 'navbar.php';

$query = "SELECT COUNT(*) as total FROM cars";
$result = mysqli_query($connection, $query);
$stats['total_cars'] = mysqli_fetch_array($result)['total'];

$query = "SELECT COUNT(*) as total FROM cars WHERE status = 'available'";
$result = mysqli_query($connection, $query);
$stats['available_cars'] = mysqli_fetch_array($result)['total'];

$query = "SELECT COUNT(*) as total FROM cars WHERE status = 'rented'";
$result = mysqli_query($connection, $query);
$stats['rented_cars'] = mysqli_fetch_array($result)['total'];

$query = "SELECT COUNT(*) as total FROM cars WHERE status = 'maintenance'";
$result = mysqli_query($connection, $query);
$stats['maintenance_cars'] = mysqli_fetch_array($result)['total'];

$query = "SELECT COUNT(*) as total FROM rentals WHERE status IN ('confirmed', 'active')";
$result = mysqli_query($connection, $query);
$stats['active_rentals'] = mysqli_fetch_array($result)['total'];

$query = "SELECT COUNT(*) as total FROM customers";
$result = mysqli_query($connection, $query);
$stats['total_customers'] = mysqli_fetch_array($result)['total'];

$query = "SELECT SUM(total_price) as total FROM rentals WHERE status = 'completed'";
$result = mysqli_query($connection, $query);
$stats['total_revenue'] = mysqli_fetch_array($result)['total'] ?? 0;

$query = "SELECT SUM(total_price) as total FROM rentals
          WHERE status = 'completed'
          AND MONTH(return_date) = MONTH(CURDATE())
          AND YEAR(return_date) = YEAR(CURDATE())";
$result = mysqli_query($connection, $query);
$stats['revenue_month'] = mysqli_fetch_array($result)['total'] ?? 0;

$query = "SELECT SUM(total_price) as total FROM rentals
          WHERE status = 'completed'
          AND DATE(return_date) = CURDATE()";
$result = mysqli_query($connection, $query);
$stats['revenue_today'] = mysqli_fetch_array($result)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Car Rental System</title>
    <link rel="stylesheet" href="cssfiles/dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="welcome-section">
            <div class="welcome-text">
                <h1>Bonjour, <?php echo htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['user'], ENT_QUOTES, 'UTF-8'); ?> !</h1>
                <p>Bienvenue dans votre espace de gestion</p>
            </div>
            <div class="date-badge">
                <?= ui_icon('calendar') ?> <?php echo date('l d F Y'); ?>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card primary">
                <div>
                    <div class="stat-value"><?php echo $stats['total_cars']; ?></div>
                    <div class="stat-label">Total Vehicules</div>
                </div>
                <div class="stat-icon"><?= ui_icon('car') ?></div>
            </div>

            <div class="stat-card success">
                <div>
                    <div class="stat-value"><?php echo $stats['available_cars']; ?></div>
                    <div class="stat-label">Voitures Disponibles</div>
                </div>
                <div class="stat-icon"><?= ui_icon('check-circle') ?></div>
            </div>

            <div class="stat-card warning">
                <div>
                    <div class="stat-value"><?php echo $stats['rented_cars']; ?></div>
                    <div class="stat-label">Voitures Louees</div>
                </div>
                <div class="stat-icon"><?= ui_icon('map-pin') ?></div>
            </div>

            <div class="stat-card danger">
                <div>
                    <div class="stat-value"><?php echo $stats['maintenance_cars']; ?></div>
                    <div class="stat-label">Voitures en Maintenance</div>
                </div>
                <div class="stat-icon"><?= ui_icon('wrench') ?></div>
            </div>

            <div class="stat-card info">
                <div>
                    <div class="stat-value"><?php echo $stats['active_rentals']; ?></div>
                    <div class="stat-label">Locations Actives</div>
                </div>
                <div class="stat-icon"><?= ui_icon('clipboard') ?></div>
            </div>

            <div class="stat-card success">
                <div>
                    <div class="stat-value"><?php echo $stats['total_customers']; ?></div>
                    <div class="stat-label">Clients Enregistres</div>
                </div>
                <div class="stat-icon"><?= ui_icon('users') ?></div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card primary">
                <div>
                    <div class="stat-value"><?php echo number_format($stats['revenue_today'], 0, ',', ' '); ?> MAD</div>
                    <div class="stat-label">Chiffre d'affaires Aujourd'hui</div>
                </div>
                <div class="stat-icon"><?= ui_icon('wallet') ?></div>
            </div>

            <div class="stat-card info">
                <div>
                    <div class="stat-value"><?php echo number_format($stats['revenue_month'], 0, ',', ' '); ?> MAD</div>
                    <div class="stat-label">Chiffre d'affaires du Mois</div>
                </div>
                <div class="stat-icon"><?= ui_icon('chart-bar') ?></div>
            </div>

            <div class="stat-card success">
                <div>
                    <div class="stat-value"><?php echo number_format($stats['total_revenue'], 0, ',', ' '); ?> MAD</div>
                    <div class="stat-label">Chiffre d'affaires Total</div>
                </div>
                <div class="stat-icon"><?= ui_icon('trophy') ?></div>
            </div>
        </div>

        <div class="section">
            <h2><?= ui_icon('settings') ?> Actions rapides</h2>
            <div class="quick-action-buttons">
                <a href="addcar.php" class="quick-action-btn">
                    <span class="action-icon"><?= ui_icon('add') ?></span>
                    <span>Ajouter une voiture</span>
                </a>

                <a href="allcars.php" class="quick-action-btn">
                    <span class="action-icon"><?= ui_icon('car') ?></span>
                    <span>Gerer les vehicules</span>
                </a>
                <a href="allrentals.php" class="quick-action-btn">
                    <span class="action-icon"><?= ui_icon('clipboard') ?></span>
                    <span>Gerer les locations</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>

<?php mysqli_close($connection); ?>