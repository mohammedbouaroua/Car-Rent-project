<?php
require 'checksession.php';
require 'dbconnection.php';
require 'navbar.php';


$id = $_GET['id'];

$query = "SELECT c.*, b.name as brand_name 
          FROM cars c 
          LEFT JOIN brands b ON c.brand_id = b.id 
          WHERE c.id = $id";
$result = mysqli_query($connection, $query);
$car = mysqli_fetch_array($result);

if(!$car) {
    header('Location: allcars.php');
    exit();
}

// // Vérifier si la voiture est actuellement louée
// $rental_query = "SELECT r.*, cu.firstname, cu.lastname, cu.phone 
//                  FROM rentals r 
//                  JOIN customers cu ON r.customer_id = cu.id 
//                  WHERE r.car_id = $id 
//                  AND r.status IN ('pending', 'active')
//                  ORDER BY r.created_at DESC LIMIT 1";
// $rental_result = mysqli_query($connection, $rental_query);
// $current_rental = mysqli_fetch_array($rental_result);
// ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Détails de la voiture</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
    <style>
        .car-detail {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .car-detail img {
            max-width: 100%;
            border-radius: 10px;
        }
        .info-row {
            margin: 15px 0;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: bold;
            width: 200px;
            display: inline-block;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .status-available { background: #d4edda; color: #155724; }
        .status-rented { background: #fff3cd; color: #856404; }
        .status-maintenance { background: #f8d7da; color: #721c24; }
        .action-buttons {
            margin-top: 20px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-primary { background: #007bff; color: white; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="car-detail">
        <h2><?php echo $car['brand_name'] . ' ' . $car['model']; ?></h2>
        
        <?php if($car['photo']): ?>
            <img src="<?php echo $car['photo']; ?>" alt="<?php echo $car['brand_name'] . ' ' . $car['model']; ?>">
        <?php else: ?>
            <img src="images/car-default.jpg" alt="No image">
        <?php endif; ?>
        
        <div class="info-row">
            <span class="info-label">Marque :</span>
            <?php echo $car['brand_name']; ?>
        </div>
        
        <div class="info-row">
            <span class="info-label">Modèle :</span>
            <?php echo $car['model']; ?>
        </div>
        
        <div class="info-row">
            <span class="info-label">Année :</span>
            <?php echo $car['year']; ?>
        </div>
        
        <div class="info-row">
            <span class="info-label">Plaque d'immatriculation :</span>
            <?php echo $car['license_plate']; ?>
        </div>
        
        <div class="info-row">
            <span class="info-label">Prix par jour :</span>
            <?php echo $car['price_per_day']; ?> MAD
        </div>
        
        <div class="info-row">
            <span class="info-label">Nombre de places :</span>
            <?php echo $car['seats']; ?>
        </div>
        
        <div class="info-row">
            <span class="info-label">Transmission :</span>
            <?php echo ($car['transmission'] == 'manual') ? 'Manuelle' : 'Automatique'; ?>
        </div>
        
        <div class="info-row">
            <span class="info-label">Carburant :</span>
            <?php 
                switch($car['fuel_type']) {
                    case 'petrol': echo 'Essence'; break;
                    case 'diesel': echo 'Diesel'; break;
                    case 'electric': echo 'Électrique'; break;
                    case 'hybrid': echo 'Hybride'; break;
                }
            ?>
        </div>
        
        <div class="info-row">
            <span class="info-label">Statut :</span>
            <span class="status-badge status-<?php echo $car['status']; ?>">
                <?php 
                    switch($car['status']) {
                        case 'available': echo 'Disponible'; break;
                        case 'rented': echo 'Louée'; break;
                        case 'maintenance': echo 'En maintenance'; break;
                    }
                ?>
            </span>
        </div>
        
        <?php if($car['description']): ?>
        <div class="info-row">
            <span class="info-label">Description :</span>
            <?php echo nl2br(htmlspecialchars($car['description'])); ?>
        </div>
        <?php endif; ?>
        
        <!-- <?php if($current_rental): ?>
        <div class="info-row" style="background: #fff3cd;">
            <span class="info-label">Location en cours :</span>
            Client : <?php echo $current_rental['firstname'] . ' ' . $current_rental['lastname']; ?><br>
            Téléphone : <?php echo $current_rental['phone']; ?><br>
            Période : du <?php echo $current_rental['start_date']; ?> au <?php echo $current_rental['end_date']; ?>
        </div>
        <?php endif; ?> -->
        
        <div class="action-buttons">
            <a href="allcars.php" class="btn btn-secondary">← Retour</a>
            <!-- <?php if($_SESSION['role'] == 'AD'): ?>
                <a href="editcar.php?id=<?php echo $car['id']; ?>" class="btn btn-warning">✏️ Modifier</a>
            <?php endif; ?> -->
            <?php if($car['status'] == 'available'): ?>
                <a href="addrental.php?car_id=<?php echo $car['id']; ?>" class="btn btn-primary">📍 Louer cette voiture</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php mysqli_close($connection); ?>