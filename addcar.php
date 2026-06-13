<?php
require 'checksession.php';
require_once 'icon_helper.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'AD') {
    header('Location: authForm.php?auth=access_denied');
}

include 'navbar.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une voiture</title>
    <link rel="stylesheet" type="text/css" href="cssfiles/addcar.css"/>
</head>
<body>
    <div class="form-container">
        <h2><?= ui_icon('add') ?> Ajouter une nouvelle voiture</h2>

        <form action="addcar_action.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="brand_id" class="required">Marque</label>
                <select id="brand_id" name="brand_id" required>
                    <option value="">Selectionner une marque</option>
                    <?php
                    require 'dbconnection.php';
                    $query = "SELECT id, name FROM brands ORDER BY name";
                    $result = mysqli_query($connection, $query);
                    if (!$result) {
                        die('SQL Error: ' . mysqli_error($connection));
                    }
                    while ($brand = mysqli_fetch_array($result)) {
                        echo "<option value='{$brand['id']}'>{$brand['name']}</option>";
                    }
                    mysqli_close($connection);
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="model" class="required">Modele</label>
                <input type="text" id="model" name="model" required placeholder="Ex: Clio, Megane, Serie 3" maxlength="50">
            </div>

            <div class="form-group">
                <label for="year" class="required">Annee</label>
                <input type="number" id="year" name="year" required min="1990" max="<?php echo date('Y') + 1; ?>" placeholder="Ex: 2022">
            </div>

            <div class="form-group">
                <label for="license_plate" class="required">Plaque d'immatriculation</label>
                <input type="text" id="license_plate" name="license_plate" required placeholder="Ex: AB-123-CD" maxlength="20">
            </div>

            <div class="form-group">
                <label for="price_per_day" class="required">Prix par jour (MAD)</label>
                <input type="number" id="price_per_day" name="price_per_day" required step="0.01" min="0" placeholder="0.00">
            </div>

            <div class="form-group">
                <label for="seats">Nombre de places</label>
                <input type="number" id="seats" name="seats" min="2" max="9" value="5">
            </div>

            <div class="form-group">
                <label class="required">Transmission</label>
                <label><input type="radio" name="transmission" value="manual" checked> Manuelle</label>
                <label><input type="radio" name="transmission" value="automatic"> Automatique</label>
            </div>

            <div class="form-group">
                <label class="required">Carburant</label>
                <select name="fuel_type">
                    <option value="petrol">Essence</option>
                    <option value="diesel">Diesel</option>
                    <option value="electric">Electrique</option>
                    <option value="hybrid">Hybride</option>
                </select>
            </div>

            <div class="form-group">
                <label>Statut</label>
                <select name="status">
                    <option value="available">Disponible</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>

            <div class="form-group">
                <label for="photo">Photo de la voiture</label>
                <input type="file" id="photo" name="photo" accept="image/png, image/jpeg, image/jpg, image/gif">
                <div class="hint">Formats acceptes: PNG, JPG, JPEG. Max: 5MB</div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Caracteristiques, options, etc." rows="4"></textarea>
            </div>

            <button type="submit" class="submit-btn">Ajouter la voiture</button>
        </form>
    </div>
    <div class="button-container">
        <a href="allcars.php" class="show"><?= ui_icon('arrow-left') ?> Voir toutes les voitures</a>
    </div>
</body>
</html>