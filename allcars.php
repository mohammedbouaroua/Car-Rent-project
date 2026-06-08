<?php
require 'checksession.php';
require 'dbconnection.php';
require 'navbar.php';
require 'editstatusrentals.php'; // Met à jour les statuts des locations et des voitures avant d'afficher la liste


// Recherche
$search_brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$search_model = isset($_GET['model']) ? $_GET['model'] : '';
$search_status = isset($_GET['status']) ? $_GET['status'] : '';

// Construction de la requête avec JOIN
$query = "SELECT c.*, b.name as brand_name 
          FROM cars c 
          LEFT JOIN brands b ON c.brand_id = b.id 
          WHERE 1=1";

if(!empty($search_brand)) {
    $query .= " AND b.name LIKE '%$search_brand%'";
}
if(!empty($search_model)) {
    $query .= " AND c.model LIKE '%$search_model%'";
}
if(!empty($search_status) && $search_status != 'all') {
    $query .= " AND c.status = '$search_status'";
}

$query .= " ORDER BY c.brand_id, c.model";
$result = mysqli_query($connection, $query);
$totalCars = mysqli_num_rows($result);

// Statistiques pour affichage
$stats_query = "SELECT 
    COUNT(CASE WHEN status = 'available' THEN 1 END) as available,
    COUNT(CASE WHEN status = 'rented' THEN 1 END) as rented,
    COUNT(CASE WHEN status = 'maintenance' THEN 1 END) as maintenance
    FROM cars";
$stats_result = mysqli_query($connection, $stats_query);
$stats = mysqli_fetch_array($stats_result);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des voitures - Car Rental</title>
    <link rel="stylesheet" type="text/css" href="cssfiles/allcars.css"/>
   <style>
        .success {
            background: #e0f8e9;
            color: #2d7a46;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="cars-container">
    <?php if($_SESSION['role'] == 'AD'): ?>
        <!-- Page Header -->
        <div class="page-header">
            <h2>🚗 Gestion des véhicules</h2>
        </div>
        <!-- Stats Bar -->
        <div class="stats-bar">
            <div class="stat-chip available">
                <span class="count"><?php echo $stats['available']; ?></span>
                <span class="label">Disponibles</span>
            </div>
            <div class="stat-chip rented">
                <span class="count"><?php echo $stats['rented']; ?></span>
                <span class="label">Louées</span>
            </div>
            <div class="stat-chip maintenance">
                <span class="count"><?php echo $stats['maintenance']; ?></span>
                <span class="label">Maintenance</span>
            </div>
        </div>
    <?php endif; ?>
        <?php if(isset($_GET['success'])): ?>
            <div class="success">✅ Location ajoutée avec succès</div>
        <?php endif; ?>
        <!-- Formulaire de recherche -->
        <div class="search-form">
            <form method="GET">
                <div class="search-filters">
                    <div class="filter-group">
                        <label>🏭 Marque</label>
                        <input type="text" name="brand" placeholder="Ex: Toyota, BMW..." value="<?php echo htmlspecialchars($search_brand); ?>">
                    </div>
                    <div class="filter-group">
                        <label>🚙 Modèle</label>
                        <input type="text" name="model" placeholder="Ex: Clio, Serie 3..." value="<?php echo htmlspecialchars($search_model); ?>">
                    </div>
                    <div class="filter-group">
                        <label>📊 Statut</label>
                        <select name="status">
                            <option value="all">Tous</option>
                            <option value="available" <?php echo $search_status=='available'?'selected':''; ?>>Disponible</option>
                            <option value="rented" <?php echo $search_status=='rented'?'selected':''; ?>>Louée</option>
                            <option value="maintenance" <?php echo $search_status=='maintenance'?'selected':''; ?>>Maintenance</option>
                        </select>
                    </div>
                    <div class="search-actions">
                        <button type="submit" class="btn-search">🔍 Rechercher</button>
                        <a href="allcars.php"><button type="button" class="btn-reset">⟳ Réinitialiser</button></a>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Action Bar -->
        <div class="action-bar">
            <?php if($_SESSION['role'] == 'AD'): ?>
                <a href="addcar.php" class="btn-add">➕ Ajouter une voiture</a>
            <?php endif; ?>
            <div class="total-count">
                📊 <?php echo $totalCars; ?> véhicule(s) trouvé(s)
            </div>
        </div>
        
        <!-- Grille des voitures -->
        <?php if($totalCars > 0): ?>
            <div class="cars-grid">
                <?php while($car = mysqli_fetch_array($result)): ?>
                    <div class="car-card">
                        <div class="car-image">
                            <?php if($car['photo'] && file_exists($car['photo'])): ?>
                                <img src="<?php echo $car['photo']; ?>" alt="<?php echo $car['brand_name'] . ' ' . $car['model']; ?>">
                            <?php else: ?>
                                <img src="photos/car-default.jpg" alt="No image">
                            <?php endif; ?>
                            <span class="car-status status-<?php echo $car['status']; ?>">
                                <?php 
                                    switch($car['status']) {
                                        case 'available': echo '✓ Disponible'; break;
                                        case 'rented': echo '📍 Louée'; break;
                                        case 'maintenance': echo '🔧 Maintenance'; break;
                                    }
                                ?>
                            </span>
                        </div>
                        <div class="car-info">
                            <div class="car-title"><?php echo $car['brand_name'] . ' ' . $car['model']; ?></div>
                            <div class="car-subtitle"><?php echo $car['year']; ?> • <?php echo $car['license_plate']; ?></div>
                            
                            <div class="car-details">
                                <span class="detail-item">👥 <?php echo $car['seats']; ?> places</span>
                                <span class="detail-item">⚙️ <?php echo $car['transmission'] == 'manual' ? 'Manuelle' : 'Automatique'; ?></span>
                                <span class="detail-item">⛽ <?php 
                                    switch($car['fuel_type']) {
                                        case 'petrol': echo 'Essence'; break;
                                        case 'diesel': echo 'Diesel'; break;
                                        case 'electric': echo 'Électrique'; break;
                                        case 'hybrid': echo 'Hybride'; break;
                                    }
                                ?></span>
                            </div>
                            
                            <div class="car-price">
                                <span class="price-value"><?php echo number_format($car['price_per_day'], 0, ',', ' '); ?> MAD</span>
                                <span class="price-unit">/jour</span>
                            </div>
                            
                            <div class="car-actions">
                                <a href="showcar.php?id=<?php echo $car['id']; ?>" class="btn-view">👁️ Voir</a>
                                <?php if($_SESSION['role'] == 'AD'): ?>
                                    <a href="editcar.php?id=<?php echo $car['id']; ?>" class="btn-edit">✏️ Modifier</a>
                                    <a href="deletecar.php?id=<?php echo $car['id']; ?>" class="btn-delete" onclick="return confirm('Supprimer cette voiture ?')">🗑️ Supprimer</a>
                                <?php endif; ?>
                                <?php if($car['status'] == 'available'): ?>
                                    <a href="addrental.php?car_id=<?php echo $car['id']; ?>" class="btn-rent">📍 Louer</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <?php if($_SESSION['role'] == 'AD'): ?>
            <a href="dashboard.php" class="back-btn">← Retour au tableau de bord</a>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">🚗🔍</div>
                <h3>Aucune voiture trouvée</h3>
                <?php if($_SESSION['role'] == 'AD'): ?>
                    <a href="addcar.php" class="btn-add" style="margin-top: 15px;">➕ Ajouter une voiture</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    </div>
</body>
</html>

<?php mysqli_close($connection); ?>