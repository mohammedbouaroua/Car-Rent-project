<?php
require 'checksession.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'AD') {
    header("Location: authForm.php?auth=access_denied");
}
require 'dbconnection.php';
require 'navbar.php';


// ========== STATISTIQUES GÉNÉRALES ==========

// Nombre total de voitures
$query = "SELECT COUNT(*) as total FROM cars";
$result = mysqli_query($connection, $query);
$stats['total_cars'] = mysqli_fetch_array($result)['total'];

// Voitures disponibles
$query = "SELECT COUNT(*) as total FROM cars WHERE status = 'available'";
$result = mysqli_query($connection, $query);
$stats['available_cars'] = mysqli_fetch_array($result)['total'];

// Voitures louées
$query = "SELECT COUNT(*) as total FROM cars WHERE status = 'rented'";
$result = mysqli_query($connection, $query);
$stats['rented_cars'] = mysqli_fetch_array($result)['total'];

// Voitures en maintenance
$query = "SELECT COUNT(*) as total FROM cars WHERE status = 'maintenance'";
$result = mysqli_query($connection, $query);
$stats['maintenance_cars'] = mysqli_fetch_array($result)['total'];

// Locations actives
$query = "SELECT COUNT(*) as total FROM rentals WHERE status IN ('confirmed', 'active')";
$result = mysqli_query($connection, $query);
$stats['active_rentals'] = mysqli_fetch_array($result)['total'];

// Nombre total de clients
$query = "SELECT COUNT(*) as total FROM customers";
$result = mysqli_query($connection, $query);
$stats['total_customers'] = mysqli_fetch_array($result)['total'];

// Chiffre d'affaires total
$query = "SELECT SUM(total_price) as total FROM rentals WHERE status = 'completed'";
$result = mysqli_query($connection, $query);
$stats['total_revenue'] = mysqli_fetch_array($result)['total'] ?? 0;

// Chiffre d'affaires du mois
$query = "SELECT SUM(total_price) as total FROM rentals 
          WHERE status = 'completed' 
          AND MONTH(return_date) = MONTH(CURDATE()) 
          AND YEAR(return_date) = YEAR(CURDATE())";
$result = mysqli_query($connection, $query);
$stats['revenue_month'] = mysqli_fetch_array($result)['total'] ?? 0;

// Chiffre d'affaires du jour
$query = "SELECT SUM(total_price) as total FROM rentals 
          WHERE status = 'completed' 
          AND DATE(return_date) = CURDATE()";
$result = mysqli_query($connection, $query);
$stats['revenue_today'] = mysqli_fetch_array($result)['total'] ?? 0;

// print_r($stats);
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
        
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-text">
                <h1>Bonjour, <?php echo htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['user']); ?> !</h1>
                <p>Bienvenue dans votre espace de gestion</p>
            </div>
            <div class="date-badge">
                📅 <?php echo date('l d F Y'); ?>
            </div>
        </div>
        
        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div>
                    <div class="stat-value"><?php echo $stats['total_cars']; ?></div>
                    <div class="stat-label">Total Véhicules</div>
                </div>
                <div class="stat-icon">🚗</div>
            </div>
            
            <div class="stat-card success">
                <div>
                    <div class="stat-value"><?php echo $stats['available_cars']; ?></div>
                    <div class="stat-label">Voitures Disponibles</div>
                </div>
                <div class="stat-icon">✅</div>
            </div>
            
            <div class="stat-card warning">
                <div>
                    <div class="stat-value"><?php echo $stats['rented_cars']; ?></div>
                    <div class="stat-label">Voitures Louées</div>
                </div>
                <div class="stat-icon">📍</div>
            </div>
            
            <div class="stat-card danger">
                <div>
                    <div class="stat-value"><?php echo $stats['maintenance_cars']; ?></div>
                    <div class="stat-label">Voitures en Maintenance</div>
                </div>
                <div class="stat-icon">🔧</div>
            </div>
            
            <div class="stat-card info">
                <div>
                    <div class="stat-value"><?php echo $stats['active_rentals']; ?></div>
                    <div class="stat-label">Locations Actives</div>
                </div>
                <div class="stat-icon">📋</div>
            </div>
            
            <div class="stat-card success">
                <div>
                    <div class="stat-value"><?php echo $stats['total_customers']; ?></div>
                    <div class="stat-label">Clients Enregistrés</div>
                </div>
                <div class="stat-icon">👥</div>
            </div>
        </div>
        
        <!-- Revenue Stats -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div>
                    <div class="stat-value"><?php echo number_format($stats['revenue_today'], 0, ',', ' '); ?> MAD</div>
                    <div class="stat-label">Chiffre d'affaires Aujourd'hui</div>
                </div>
                <div class="stat-icon">💰</div>
            </div>
            
            <div class="stat-card info">
                <div>
                    <div class="stat-value"><?php echo number_format($stats['revenue_month'], 0, ',', ' '); ?> MAD</div>
                    <div class="stat-label">Chiffre d'affaires du Mois</div>
                </div>
                <div class="stat-icon">📊</div>
            </div>
            
            <div class="stat-card success">
                <div>
                    <div class="stat-value"><?php echo number_format($stats['total_revenue'], 0, ',', ' '); ?> MAD</div>
                    <div class="stat-label">Chiffre d'affaires Total</div>
                </div>
                <div class="stat-icon">🏆</div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="section">
            <h2>⚡ Actions rapides</h2>
            <div class="quick-action-buttons">
                <a href="addcar.php" class="quick-action-btn">
                    <span class="action-icon">➕</span>
                    <span>Ajouter une voiture</span>
                </a>
                <a href="addrental.php" class="quick-action-btn">
                    <span class="action-icon">📍</span>
                    <span>Nouvelle location</span>
                </a>
                <a href="allcars.php" class="quick-action-btn">
                    <span class="action-icon">🚗</span>
                    <span>Gérer les véhicules</span>
                </a>
                <a href="allrentals.php" class="quick-action-btn">
                    <span class="action-icon">📋</span>
                    <span>Gérer les locations</span>
                </a>
                <?php if($_SESSION['role'] == 'AD'): ?>
                    <a href="users.php" class="quick-action-btn">
                        <span class="action-icon">👥</span>
                        <span>Gérer les utilisateurs</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</body>
</html>

<?php mysqli_close($connection); ?>