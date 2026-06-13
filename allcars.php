<?php
require 'checksession.php';
require 'dbconnection.php';
require 'navbar.php';
require 'editstatusrentals.php';
require_once 'icon_helper.php';

$search_brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$search_model = isset($_GET['model']) ? $_GET['model'] : '';
$search_status = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT c.*, b.name as brand_name
          FROM cars c
          LEFT JOIN brands b ON c.brand_id = b.id
          WHERE 1=1";

if (!empty($search_brand)) {
    $query .= " AND b.name LIKE '%$search_brand%'";
}
if (!empty($search_model)) {
    $query .= " AND c.model LIKE '%$search_model%'";
}
if (!empty($search_status) && $search_status != 'all') {
    $query .= " AND c.status = '$search_status'";
}

$query .= " ORDER BY c.brand_id, c.model";
$result = mysqli_query($connection, $query);
$totalCars = mysqli_num_rows($result);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des voitures - Car Rental</title>
    <link rel="stylesheet" type="text/css" href="cssfiles/allcars.css"/>
</head>
<body>
    <div class="cars-container">
        <?php if ($_SESSION['role'] == 'AD'): ?>
            <div class="page-header">
                <h2><?= ui_icon('car') ?> Gestion des vehicules</h2>
            </div>

            <div class="stats-bar">
                <div class="stat-chip available">
                    <span class="count"><?= (int) $stats['available'] ?></span>
                    <span class="label">Disponibles</span>
                </div>
                <div class="stat-chip rented">
                    <span class="count"><?= (int) $stats['rented'] ?></span>
                    <span class="label">Louees</span>
                </div>
                <div class="stat-chip maintenance">
                    <span class="count"><?= (int) $stats['maintenance'] ?></span>
                    <span class="label">Maintenance</span>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="success"><?= ui_icon('check-circle') ?> Location ajoutee avec succes</div>
        <?php endif; ?>

        <div class="search-form">
            <form method="GET">
                <div class="search-filters">
                    <div class="filter-group">
                        <label><?= ui_icon('factory') ?> Marque</label>
                        <input type="text" name="brand" placeholder="Ex: Toyota, BMW..." value="<?= htmlspecialchars($search_brand, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="filter-group">
                        <label><?= ui_icon('car') ?> Modele</label>
                        <input type="text" name="model" placeholder="Ex: Clio, Serie 3..." value="<?= htmlspecialchars($search_model, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="filter-group">
                        <label><?= ui_icon('chart-bar') ?> Statut</label>
                        <select name="status">
                            <option value="all">Tous</option>
                            <option value="available" <?= $search_status == 'available' ? 'selected' : '' ?>>Disponible</option>
                            <option value="rented" <?= $search_status == 'rented' ? 'selected' : '' ?>>Louee</option>
                            <option value="maintenance" <?= $search_status == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                        </select>
                    </div>
                    <div class="search-actions">
                        <button type="submit" class="btn-search"><?= ui_icon('search') ?> Rechercher</button>
                        <a href="allcars.php" class="btn-reset"><?= ui_icon('refresh-cw') ?> Reinitialiser</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="action-bar">
            <?php if ($_SESSION['role'] == 'AD'): ?>
                <a href="addcar.php" class="btn-add"><?= ui_icon('add') ?> Ajouter une voiture</a>
            <?php endif; ?>
            <div class="total-count">
                <?= ui_icon('chart-bar') ?> <?= $totalCars ?> vehicule(s) trouve(s)
            </div>
        </div>

        <?php if ($totalCars > 0): ?>
            <div class="cars-grid">
                <?php while ($car = mysqli_fetch_array($result)): ?>
                    <div class="car-card">
                        <div class="car-image">
                            <?php if ($car['photo'] && file_exists($car['photo'])): ?>
                                <img src="<?= htmlspecialchars($car['photo'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($car['brand_name'] . ' ' . $car['model'], ENT_QUOTES, 'UTF-8') ?>">
                            <?php else: ?>
                                <img src="photos/car-default.jpg" alt="No image">
                            <?php endif; ?>
                            <span class="car-status status-<?= $car['status'] ?>">
                                <?php
                                switch ($car['status']) {
                                    case 'available':
                                        echo ui_icon('check-circle') . ' Disponible';
                                        break;
                                    case 'rented':
                                        echo ui_icon('map-pin') . ' Louee';
                                        break;
                                    case 'maintenance':
                                        echo ui_icon('wrench') . ' Maintenance';
                                        break;
                                }
                                ?>
                            </span>
                        </div>
                        <div class="car-info">
                            <div class="car-title"><?= htmlspecialchars($car['brand_name'] . ' ' . $car['model'], ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="car-subtitle"><?= (int) $car['year'] ?> • <?= htmlspecialchars($car['license_plate'], ENT_QUOTES, 'UTF-8') ?></div>

                            <div class="car-details">
                                <span class="detail-item"><?= ui_icon('users') ?> <?= (int) $car['seats'] ?> places</span>
                                <span class="detail-item"><?= ui_icon('settings') ?> <?= $car['transmission'] == 'manual' ? 'Manuelle' : 'Automatique' ?></span>
                                <span class="detail-item"><?= ui_icon('fuel') ?>
                                    <?php
                                    switch ($car['fuel_type']) {
                                        case 'petrol':
                                            echo 'Essence';
                                            break;
                                        case 'diesel':
                                            echo 'Diesel';
                                            break;
                                        case 'electric':
                                            echo 'Electrique';
                                            break;
                                        case 'hybrid':
                                            echo 'Hybride';
                                            break;
                                    }
                                    ?>
                                </span>
                            </div>

                            <div class="car-price">
                                <span class="price-value"><?= number_format($car['price_per_day'], 0, ',', ' ') ?> MAD</span>
                                <span class="price-unit">/jour</span>
                            </div>

                            <div class="car-actions">
                                <a href="showcar.php?id=<?= $car['id'] ?>" class="btn-view"><?= ui_icon('view') ?> Voir</a>
                                <?php if ($_SESSION['role'] == 'AD'): ?>
                                    <a href="editcar.php?id=<?= $car['id'] ?>" class="btn-edit"><?= ui_icon('edit') ?> Modifier</a>
                                    <a href="deletecar.php?id=<?= $car['id'] ?>" class="btn-delete" onclick="return confirm('Supprimer cette voiture ?')"><?= ui_icon('trash') ?> Supprimer</a>
                                <?php endif; ?>
                                <?php if ($car['status'] == 'available'): ?>
                                    <a href="addrental.php?car_id=<?= $car['id'] ?>" class="btn-rent"><?= ui_icon('map-pin') ?> Louer</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <?php if ($_SESSION['role'] == 'AD'): ?>
                <a href="dashboard.php" class="back-btn"><?= ui_icon('arrow-left') ?> Retour au tableau de bord</a>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon"><?= ui_icon('car-search') ?></div>
                <h3>Aucune voiture trouvee</h3>
                <?php if ($_SESSION['role'] == 'AD'): ?>
                    <a href="addcar.php" class="btn-add" style="margin-top: 15px;"><?= ui_icon('add') ?> Ajouter une voiture</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php mysqli_close($connection); ?>