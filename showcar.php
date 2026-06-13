<?php
require 'checksession.php';
require 'dbconnection.php';
require 'navbar.php';
require_once 'icon_helper.php';

$id = $_GET['id'];

$query = "SELECT c.*, b.name as brand_name
          FROM cars c
          LEFT JOIN brands b ON c.brand_id = b.id
          WHERE c.id = $id";
$result = mysqli_query($connection, $query);
$car = mysqli_fetch_array($result);

if (!$car) {
    header('Location: allcars.php');
    exit();
}

$car_name = trim(($car['brand_name'] ?? '') . ' ' . ($car['model'] ?? ''));
$fuel_label = 'Essence';
switch ($car['fuel_type']) {
    case 'diesel':
        $fuel_label = 'Diesel';
        break;
    case 'electric':
        $fuel_label = 'Electrique';
        break;
    case 'hybrid':
        $fuel_label = 'Hybride';
        break;
}

$status_label = 'Disponible';
$status_icon = 'check-circle';
switch ($car['status']) {
    case 'rented':
        $status_label = 'Louee';
        $status_icon = 'map-pin';
        break;
    case 'maintenance':
        $status_label = 'En maintenance';
        $status_icon = 'wrench';
        break;
}

$photo_path = (!empty($car['photo']) && file_exists($car['photo'])) ? $car['photo'] : 'photos/car-default.jpg';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details de la voiture</title>
    <link rel="stylesheet" type="text/css" href="cssfiles/showcar.css"/>
</head>
<body>
    <div class="car-detail">
        <div class="car-detail-grid">
            <div class="car-image-panel">
                <h2><?= ui_icon('car') ?> <?= htmlspecialchars($car_name, ENT_QUOTES, 'UTF-8') ?></h2>
                <img src="<?= htmlspecialchars($photo_path, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($car_name, ENT_QUOTES, 'UTF-8') ?>">
                <div class="status-row">
                    <span class="status-badge status-<?= $car['status'] ?>">
                        <?= ui_icon($status_icon) ?> <?= $status_label ?>
                    </span>
                    <span class="price-chip"><?= ui_icon('wallet') ?> <?= number_format($car['price_per_day'], 0, ',', ' ') ?> MAD / jour</span>
                </div>
            </div>

            <div class="car-info-panel">
                <div class="info-row">
                    <span class="info-label"><?= ui_icon('factory') ?> Marque</span>
                    <span class="info-value"><?= htmlspecialchars($car['brand_name'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label"><?= ui_icon('car') ?> Modele</span>
                    <span class="info-value"><?= htmlspecialchars($car['model'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label"><?= ui_icon('calendar') ?> Annee</span>
                    <span class="info-value"><?= (int) $car['year'] ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label"><?= ui_icon('clipboard') ?> Plaque d'immatriculation</span>
                    <span class="info-value"><?= htmlspecialchars($car['license_plate'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label"><?= ui_icon('users') ?> Nombre de places</span>
                    <span class="info-value"><?= (int) $car['seats'] ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label"><?= ui_icon('settings') ?> Transmission</span>
                    <span class="info-value"><?= $car['transmission'] == 'manual' ? 'Manuelle' : 'Automatique' ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label"><?= ui_icon('fuel') ?> Carburant</span>
                    <span class="info-value"><?= $fuel_label ?></span>
                </div>

                <?php if (!empty($car['description'])): ?>
                    <div class="info-row description-row">
                        <span class="info-label"><?= ui_icon('pencil-square') ?> Description</span>
                        <div class="info-value description-text"><?= nl2br(htmlspecialchars($car['description'], ENT_QUOTES, 'UTF-8')) ?></div>
                    </div>
                <?php endif; ?>

                <div class="action-buttons">
                    <a href="allcars.php" class="btn btn-secondary"><?= ui_icon('arrow-left') ?> Retour</a>
                    <?php if ($car['status'] == 'available'): ?>
                        <a href="addrental.php?car_id=<?= $car['id'] ?>" class="btn btn-primary"><?= ui_icon('map-pin') ?> Louer cette voiture</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php mysqli_close($connection); ?>