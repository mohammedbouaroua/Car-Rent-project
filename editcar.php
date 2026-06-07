<!DOCTYPE html>
<?php
// require 'checksession.php';
// if($_SESSION['role'] != 'AD') {
//     header('Location: authForm.php?auth=role');
//     exit();
// }

require 'dbconnection.php';
require 'navbar.php';

$car_id = $_GET['id'];
$query = "SELECT c.*, b.name as brand_name 
          FROM cars c 
          LEFT JOIN brands b ON c.brand_id = b.id 
          WHERE c.id = $car_id";
$result = mysqli_query($connection, $query);
$car = mysqli_fetch_array($result);

if(!$car) {
    header('location: allcars.php');
    exit();
}
?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modifier la voiture</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
    <div class="form-container">
        <h2>Modifier la voiture</h2>
        
        <form action="updatecar.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $car['id']; ?>">
            
            <div class="form-group">
                <label>Marque</label>
                <select name="brand_id" required>
                    <?php
                    $brands = mysqli_query($connection, "SELECT id, name FROM brands ORDER BY name");
                    while($brand = mysqli_fetch_array($brands)) {
                        $selected = ($brand['id'] == $car['brand_id']) ? 'selected' : '';
                        echo "<option value='{$brand['id']}' $selected>{$brand['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Modèle</label>
                <input type="text" name="model" value="<?php echo $car['model']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Année</label>
                <input type="number" name="year" value="<?php echo $car['year']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Plaque d'immatriculation</label>
                <input type="text" name="license_plate" value="<?php echo $car['license_plate']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Prix par jour (MAD)</label>
                <input type="number" step="0.01" name="price_per_day" value="<?php echo $car['price_per_day']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Nombre de places</label>
                <input type="number" name="seats" value="<?php echo $car['seats']; ?>" min="2" max="9">
            </div>
            
            <div class="form-group">
                <label>Transmission</label>
                <label><input type="radio" name="transmission" value="manual" <?php echo $car['transmission']=='manual'?'checked':''; ?>> Manuelle</label>
                <label><input type="radio" name="transmission" value="automatic" <?php echo $car['transmission']=='automatic'?'checked':''; ?>> Automatique</label>
            </div>
            
            <div class="form-group">
                <label>Carburant</label>
                <select name="fuel_type">
                    <option value="petrol" <?php echo $car['fuel_type']=='petrol'?'selected':''; ?>>Essence</option>
                    <option value="diesel" <?php echo $car['fuel_type']=='diesel'?'selected':''; ?>>Diesel</option>
                    <option value="electric" <?php echo $car['fuel_type']=='electric'?'selected':''; ?>>Électrique</option>
                    <option value="hybrid" <?php echo $car['fuel_type']=='hybrid'?'selected':''; ?>>Hybride</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Statut</label>
                <select name="status">
                    <option value="available" <?php echo $car['status']=='available'?'selected':''; ?>>Disponible</option>
                    <option value="maintenance" <?php echo $car['status']=='maintenance'?'selected':''; ?>>Maintenance</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Photo actuelle</label>
                <?php if($car['photo']): ?>
                    <img src="<?php echo $car['photo']; ?>" width="100">
                <?php endif; ?>
                <input type="file" name="photo" accept="image/*">
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4"><?php echo $car['description']; ?></textarea>
            </div>
            
            <button type="submit">Mettre à jour</button>
        </form>
    </div>
</body>
</html>

<?php mysqli_close($connection); ?>