<?php
// require 'checksession.php';
require 'dbconnection.php';

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

// Locations terminées ce mois
$query = "SELECT COUNT(*) as total FROM rentals 
          WHERE status = 'completed' 
          AND MONTH(return_date) = MONTH(CURDATE()) 
          AND YEAR(return_date) = YEAR(CURDATE())";
$result = mysqli_query($connection, $query);
$stats['completed_this_month'] = mysqli_fetch_array($result)['total'];

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

// Nombre total de clients
$query = "SELECT COUNT(*) as total FROM customers";
$result = mysqli_query($connection, $query);
$stats['total_customers'] = mysqli_fetch_array($result)['total'];

// Taux d'occupation (voitures louées / total voitures)
$stats['occupancy_rate'] = $stats['total_cars'] > 0 
    ? round(($stats['rented_cars'] / $stats['total_cars']) * 100, 1) 
    : 0;

// ========== TOP 5 DES VOITURES LES PLUS LOUÉES ==========
$query = "SELECT b.name as brand_name, c.model, c.license_plate, c.photo, COUNT(r.id) as rental_count
          FROM rentals r
          JOIN cars c ON r.car_id = c.id
          JOIN brands b ON c.brand_id = b.id
          GROUP BY c.id
          ORDER BY rental_count DESC
          LIMIT 5";
$top_cars = mysqli_query($connection, $query);

// ========== LOCATIONS RÉCENTES ==========
$query = "SELECT r.*, c.license_plate, c.model, c.photo,
          b.name as brand_name,
          cu.firstname, cu.lastname, cu.phone
          FROM rentals r
          JOIN cars c ON r.car_id = c.id
          JOIN brands b ON c.brand_id = b.id
          JOIN customers cu ON r.customer_id = cu.id
          ORDER BY r.created_at DESC
          LIMIT 5";
$recent_rentals = mysqli_query($connection, $query);

// ========== LOCATIONS À VENIR (prochaines 7 jours) ==========
$query = "SELECT r.*, c.license_plate, c.model,
          b.name as brand_name,
          cu.firstname, cu.lastname
          FROM rentals r
          JOIN cars c ON r.car_id = c.id
          JOIN brands b ON c.brand_id = b.id
          JOIN customers cu ON r.customer_id = cu.id
          WHERE r.start_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
          AND r.status IN ('confirmed', 'pending')
          ORDER BY r.start_date ASC
          LIMIT 5";
$upcoming_rentals = mysqli_query($connection, $query);

// ========== STATISTIQUES PAR MARQUE ==========
$query = "SELECT b.name, COUNT(c.id) as car_count
          FROM brands b
          LEFT JOIN cars c ON b.id = c.brand_id
          GROUP BY b.id
          ORDER BY car_count DESC
          LIMIT 5";
$brand_stats = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Car Rental System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styles spécifiques au dashboard */
        .welcome-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: var(--radius-lg);
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .welcome-text h1 {
            color: white;
            margin-bottom: 0.25rem;
        }
        
        .welcome-text p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .date-badge {
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-size: 0.875rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            transition: var(--transition);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        
        .stat-info .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
        }
        
        .stat-info .stat-label {
            color: var(--gray);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.3;
        }
        
        .stat-card.primary .stat-value { color: var(--primary); }
        .stat-card.success .stat-value { color: var(--success); }
        .stat-card.warning .stat-value { color: var(--warning); }
        .stat-card.danger .stat-value { color: var(--danger); }
        .stat-card.info .stat-value { color: var(--info); }
        
        .section {
            background: white;
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .section-header h2 {
            margin-bottom: 0;
            font-size: 1.25rem;
        }
        
        .section-header a {
            font-size: 0.875rem;
            color: var(--primary);
        }
        
        .top-car-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            border-bottom: 1px solid var(--light-dark);
        }
        
        .top-car-item:last-child {
            border-bottom: none;
        }
        
        .top-car-rank {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            width: 40px;
        }
        
        .top-car-info {
            flex: 1;
        }
        
        .top-car-name {
            font-weight: 600;
        }
        
        .top-car-plate {
            font-size: 0.75rem;
            color: var(--gray);
        }
        
        .top-car-count {
            font-weight: 600;
            color: var(--success);
        }
        
        .rental-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            border-bottom: 1px solid var(--light-dark);
        }
        
        .rental-item:last-child {
            border-bottom: none;
        }
        
        .rental-info {
            flex: 1;
        }
        
        .rental-customer {
            font-weight: 600;
        }
        
        .rental-car {
            font-size: 0.875rem;
            color: var(--gray);
        }
        
        .rental-dates {
            font-size: 0.75rem;
            color: var(--gray-light);
        }
        
        .rental-status {
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .brand-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            border-bottom: 1px solid var(--light-dark);
        }
        
        .brand-name {
            font-weight: 500;
        }
        
        .brand-count {
            background: var(--light);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--primary);
        }
        
        .quick-action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .quick-action-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: var(--light);
            border-radius: var(--radius);
            text-decoration: none;
            color: var(--dark);
            transition: var(--transition);
        }
        
        .quick-action-btn:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }
        
        .quick-action-btn .action-icon {
            font-size: 1.5rem;
        }
        
        .progress-bar {
            margin-top: 0.5rem;
            background: var(--light-dark);
            border-radius: 10px;
            overflow: hidden;
            height: 8px;
        }
        
        .progress-fill {
            background: var(--success);
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        
        .occupancy-text {
            font-size: 0.75rem;
            color: var(--gray);
            margin-top: 0.5rem;
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

<!-- Welcome Section -->
<div class="welcome-section">
    <div class="welcome-text">
        <h1>Bonjour, <?php echo htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['user']); ?> !</h1>
        <p>Bienvenue dans votre espace de gestion de location de voitures</p>
    </div>
    <div class="date-badge">
        📅 <?php echo date('l d F Y', strtotime('now')); ?>
    </div>
</div>

<!-- Statistics Grid -->
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-info">
            <div class="stat-value"><?php echo $stats['total_cars']; ?></div>
            <div class="stat-label">Total Véhicules</div>
        </div>
        <div class="stat-icon">🚗</div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-info">
            <div class="stat-value"><?php echo $stats['available_cars']; ?></div>
            <div class="stat-label">Véhicules Disponibles</div>
        </div>
        <div class="stat-icon">✅</div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-info">
            <div class="stat-value"><?php echo $stats['rented_cars']; ?></div>
            <div class="stat-label">Véhicules Loués</div>
        </div>
        <div class="stat-icon">📍</div>
    </div>
    
    <div class="stat-card danger">
        <div class="stat-info">
            <div class="stat-value"><?php echo $stats['maintenance_cars']; ?></div>
            <div class="stat-label">En Maintenance</div>
        </div>
        <div class="stat-icon">🔧</div>
    </div>
    
    <div class="stat-card info">
        <div class="stat-info">
            <div class="stat-value"><?php echo $stats['active_rentals']; ?></div>
            <div class="stat-label">Locations Actives</div>
        </div>
        <div class="stat-icon">📋</div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-info">
            <div class="stat-value"><?php echo $stats['total_customers']; ?></div>
            <div class="stat-label">Clients Enregistrés</div>
        </div>
        <div class="stat-icon">👥</div>
    </div>
</div>

<!-- Revenue & Occupancy Stats -->
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-info">
            <div class="stat-value"><?php echo number_format($stats['revenue_today'], 0, ',', ' '); ?> MAD</div>
            <div class="stat-label">Chiffre d'affaires Aujourd'hui</div>
        </div>
        <div class="stat-icon">💰</div>
    </div>
    
    <div class="stat-card info">
        <div class="stat-info">
            <div class="stat-value"><?php echo number_format($stats['revenue_month'], 0, ',', ' '); ?> MAD</div>
            <div class="stat-label">Chiffre d'affaires du Mois</div>
        </div>
        <div class="stat-icon">📊</div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-info">
            <div class="stat-value"><?php echo number_format($stats['total_revenue'], 0, ',', ' '); ?> MAD</div>
            <div class="stat-label">Chiffre d'affaires Total</div>
        </div>
        <div class="stat-icon">🏆</div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-info">
            <div class="stat-value"><?php echo $stats['occupancy_rate']; ?>%</div>
            <div class="stat-label">Taux d'Occupation</div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $stats['occupancy_rate']; ?>%"></div>
            </div>
            <div class="occupancy-text"><?php echo $stats['rented_cars']; ?> / <?php echo $stats['total_cars']; ?> véhicules loués</div>
        </div>
        <div class="stat-icon">📈</div>
    </div>
</div>

<!-- Two Columns Layout -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    
    <!-- Top 5 Most Rented Cars -->
    <div class="section">
        <div class="section-header">
            <h2>🏆 Top 5 des voitures les plus louées</h2>
            <a href="allcars.php">Voir tout →</a>
        </div>
        <?php if(mysqli_num_rows($top_cars) > 0): ?>
            <?php $rank = 1; while($car = mysqli_fetch_array($top_cars)): ?>
                <div class="top-car-item">
                    <div class="top-car-rank">#<?php echo $rank++; ?></div>
                    <div class="top-car-info">
                        <div class="top-car-name"><?php echo $car['brand_name'] . ' ' . $car['model']; ?></div>
                        <div class="top-car-plate"><?php echo $car['license_plate']; ?></div>
                    </div>
                    <div class="top-car-count"><?php echo $car['rental_count']; ?> locations</div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center; color: var(--gray);">Aucune donnée disponible</p>
        <?php endif; ?>
    </div>
    
    <!-- Brand Statistics -->
    <div class="section">
        <div class="section-header">
            <h2>🏭 Parc par marque</h2>
            <a href="allcars.php">Voir tout →</a>
        </div>
        <?php if(mysqli_num_rows($brand_stats) > 0): ?>
            <?php while($brand = mysqli_fetch_array($brand_stats)): ?>
                <div class="brand-item">
                    <span class="brand-name"><?php echo $brand['name']; ?></span>
                    <span class="brand-count"><?php echo $brand['car_count']; ?> véhicules</span>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center; color: var(--gray);">Aucune donnée disponible</p>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Rentals -->
<div class="section">
    <div class="section-header">
        <h2>📋 Dernières locations</h2>
        <a href="allrentals.php">Voir toutes →</a>
    </div>
    <?php if(mysqli_num_rows($recent_rentals) > 0): ?>
        <div style="overflow-x: auto;">
            <table class="data-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Véhicule</th>
                        <th>Période</th>
                        <th>Total</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($rental = mysqli_fetch_array($recent_rentals)): ?>
                        <tr>
                            <td>
                                <?php echo $rental['firstname'] . ' ' . $rental['lastname']; ?><br>
                                <small style="color: var(--gray);"><?php echo $rental['phone']; ?></small>
                            </td>
                            <td>
                                <?php echo $rental['brand_name'] . ' ' . $rental['model']; ?><br>
                                <small style="color: var(--gray);"><?php echo $rental['license_plate']; ?></small>
                            </td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($rental['start_date'])); ?><br>
                                <small>→ <?php echo date('d/m/Y', strtotime($rental['end_date'])); ?></small>
                            </td>
                            <td><?php echo number_format($rental['total_price'], 0, ',', ' '); ?> MAD</td>
                            <td>
                                <span class="status-badge status-<?php echo $rental['status']; ?>">
                                    <?php 
                                        switch($rental['status']) {
                                            case 'pending': echo 'En attente'; break;
                                            case 'confirmed': echo 'Confirmée'; break;
                                            case 'active': echo 'Active'; break;
                                            case 'completed': echo 'Terminée'; break;
                                            case 'cancelled': echo 'Annulée'; break;
                                        }
                                    ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: var(--gray);">Aucune location récente</p>
    <?php endif; ?>
</div>

<!-- Upcoming Rentals -->
<div class="section">
    <div class="section-header">
        <h2>📅 Locations à venir (7 prochains jours)</h2>
        <a href="allrentals.php?status=confirmed">Voir tout →</a>
    </div>
    <?php if(mysqli_num_rows($upcoming_rentals) > 0): ?>
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            <?php while($rental = mysqli_fetch_array($upcoming_rentals)): ?>
                <div class="rental-item">
                    <div class="rental-info">
                        <div class="rental-customer">👤 <?php echo $rental['firstname'] . ' ' . $rental['lastname']; ?></div>
                        <div class="rental-car">🚗 <?php echo $rental['brand_name'] . ' ' . $rental['model']; ?> - <?php echo $rental['license_plate']; ?></div>
                        <div class="rental-dates">📅 Du <?php echo date('d/m/Y', strtotime($rental['start_date'])); ?> au <?php echo date('d/m/Y', strtotime($rental['end_date'])); ?></div>
                    </div>
                    <div>
                        <span class="rental-status status-<?php echo $rental['status']; ?>">
                            <?php echo $rental['status'] == 'confirmed' ? 'Confirmée' : 'En attente'; ?>
                        </span>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: var(--gray);">Aucune location à venir</p>
    <?php endif; ?>
</div>

<!-- Quick Actions -->
<div class="section">
    <div class="section-header">
        <h2>⚡ Actions rapides</h2>
    </div>
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

</body>
</html>

<?php mysqli_close($connection); ?>