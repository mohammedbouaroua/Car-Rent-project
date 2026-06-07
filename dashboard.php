<?php
require 'checksession.php';
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
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Welcome Section */
        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .welcome-text h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .date-badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }
        
        .stat-label {
            color: #666;
            font-size: 13px;
            margin-top: 5px;
        }
        
        .stat-icon {
            font-size: 40px;
            opacity: 0.3;
        }
        
        .stat-card.primary .stat-value { color: #667eea; }
        .stat-card.success .stat-value { color: #28a745; }
        .stat-card.warning .stat-value { color: #ffc107; }
        .stat-card.danger .stat-value { color: #dc3545; }
        .stat-card.info .stat-value { color: #17a2b8; }
        
        /* Quick Actions */
        .section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .section h2 {
            margin-bottom: 15px;
            font-size: 18px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        
        .quick-action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .quick-action-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
        }
        
        .quick-action-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        .action-icon {
            font-size: 24px;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .quick-action-buttons {
                grid-template-columns: 1fr;
            }
            .welcome-section {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
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