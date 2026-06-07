<?php
// require 'checksession.php';
require 'dbconnection.php';
require 'navbar.php';

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion des voitures</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
    <style>
        .car-card { border: 1px solid #ddd; margin: 10px; padding: 10px; display: inline-block; width: 300px; }
        .car-card img { width: 100%; height: 200px; object-fit: cover; }
        .available { color: green; font-weight: bold; }
        .rented { color: orange; font-weight: bold; }
        .maintenance { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Gestion des voitures</h2>
    
    <!-- Formulaire de recherche -->
    <form method="GET" style="margin-bottom: 20px;">
        <input type="text" name="brand" placeholder="Marque" value="<?php echo $search_brand; ?>">
        <input type="text" name="model" placeholder="Modèle" value="<?php echo $search_model; ?>">
        <select name="status">
            <option value="all">Tous les statuts</option>
            <option value="available" <?php echo $search_status=='available'?'selected':''; ?>>Disponible</option>
            <option value="rented" <?php echo $search_status=='rented'?'selected':''; ?>>Louée</option>
            <option value="maintenance" <?php echo $search_status=='maintenance'?'selected':''; ?>>Maintenance</option>
        </select>
        <input type="submit" value="Rechercher">
        <a href="allcars.php"><button type="button">Réinitialiser</button></a>
    </form>

    <!-- <?php if($_SESSION['role'] == 'AD'): ?>
                <a href="addcar.php" class="btn-add">+ Ajouter une voiture</a>
    <?php endif; ?> -->
    <?php if($totalCars > 0): ?>
        <div class="cars-grid">
            <?php while($car = mysqli_fetch_array($result)): ?>
                <div class="car-card">
                    <?php if($car['photo']): ?>
                        <img src="<?php echo $car['photo']; ?>" alt="<?php echo $car['brand_name'] . ' ' . $car['model']; ?>">
                    <?php else: ?>
                        <img src="images/car-default.jpg" alt="No image">
                    <?php endif; ?>
                    
                    <h3><?php echo $car['brand_name'] . ' ' . $car['model']; ?></h3>
                    <p>Année: <?php echo $car['year']; ?></p>
                    <p>Plaque: <?php echo $car['license_plate']; ?></p>
                    <p>Prix: <?php echo $car['price_per_day']; ?> MAD/jour</p>
                    <p>Places: <?php echo $car['seats']; ?></p>
                    <p>Transmission: <?php echo $car['transmission'] == 'manual' ? 'Manuelle' : 'Automatique'; ?></p>
                    <p class="<?php echo $car['status']; ?>">
                        Statut: <?php 
                            switch($car['status']) {
                                case 'available': echo 'Disponible'; break;
                                case 'rented': echo 'Louée'; break;
                                case 'maintenance': echo 'En maintenance'; break;
                            }
                        ?>
                    </p>
                    
                    <div class="actions">
                        <a href="showcar.php?id=<?php echo $car['id']; ?>">👁️ Voir</a>
                        <a href="editcar.php?id=<?php echo $car['id']; ?>">✏️ Modifier</a>
                        <a href="deletecar.php?id=<?php echo $car['id']; ?>" onclick="return confirm('Supprimer cette voiture ?')">🗑️ Supprimer</a>
                        <!-- <?php if($_SESSION['role'] == 'AD'): ?>
                        <?php endif; ?> -->
                        <?php if($car['status'] == 'available'): ?>
                            <a href="addrental.php?car_id=<?php echo $car['id']; ?>" class="rent-btn">📍 Louer</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <p>Total: <?php echo $totalCars; ?> voiture(s)</p>
    <?php else: ?>
        <p>Aucune voiture trouvée.</p>
    <?php endif; ?>
    
    <a href="dashboard.php" class="back-btn">← Retour au tableau de bord</a>
</body>
</html>

<?php mysqli_close($connection); ?>