<?php
require 'checksession.php';
require 'dbconnection.php';
require 'navbar.php';


$car_id = isset($_GET['car_id']) ? $_GET['car_id'] : null;

// Récupérer la voiture si un ID est fourni
$car = null;
if($car_id) {
    $car_query = "SELECT c.*, b.name as brand_name 
                  FROM cars c 
                  LEFT JOIN brands b ON c.brand_id = b.id 
                  WHERE c.id = $car_id AND c.status = 'available'";
    $car_result = mysqli_query($connection, $car_query);
    $car = mysqli_fetch_array($car_result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle location</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
    <style>
        .form-container { max-width: 600px; margin: 20px auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .car-info {
            background: #e8f4f8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .total-price {
            font-size: 24px;
            color: #28a745;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }
        .btn-submit { background: #28a745; color: white; padding: 12px; font-size: 18px; }
        .error { color: red; font-size: 14px; margin-top: 5px; }
    </style>
    <script>
        function calculateTotal() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const pricePerDay = <?php echo $car ? $car['price_per_day'] : 0; ?>;
            
            if(startDate && endDate && pricePerDay > 0) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                
                if(diffDays > 0) {
                    const total = diffDays * pricePerDay;
                    document.getElementById('total_days').innerHTML = diffDays;
                    document.getElementById('total_price_display').innerHTML = total.toFixed(2) + ' MAD';
                    document.getElementById('total_price').value = total;
                }
            }
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Nouvelle location</h2>
        
        <?php if($car): ?>
        <div class="car-info">
            <h3>Voiture sélectionnée</h3>
            <p><strong><?php echo $car['brand_name'] . ' ' . $car['model']; ?></strong><br>
            Plaque: <?php echo $car['license_plate']; ?><br>
            Prix: <?php echo $car['price_per_day']; ?> MAD/jour</p>
        </div>
        <?php endif; ?>
        
        <form action="saverental.php" method="post" oninput="calculateTotal()">
            <input type="hidden" name="car_id" value="<?php echo $car_id; ?>">
            
            <!-- Section Client -->
            <h3>Informations client</h3>
            
            <div class="form-group">
                <label>Type de client</label>
                <select name="customer_type" id="customer_type" onchange="toggleCustomerForm()">
                    <option value="existing">Client existant</option>
                    <option value="new">Nouveau client</option>
                </select>
            </div>
            
            <div id="existing_customer_div">
                <div class="form-group">
                    <label>Email du client</label>
                    <input type="email" name="customer_email" id="customer_email" 
                           placeholder="Entrez l'email du client">
                </div>
            </div>
            
            <div id="new_customer_div" style="display: none;">
                <div class="form-group">
                    <label>Prénom *</label>
                    <input type="text" name="firstname" id="firstname">
                </div>
                
                <div class="form-group">
                    <label>Nom *</label>
                    <input type="text" name="lastname" id="lastname">
                </div>
                
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" id="email">
                </div>
                
                <div class="form-group">
                    <label>Téléphone *</label>
                    <input type="tel" name="phone" id="phone">
                </div>
                
                <div class="form-group">
                    <label>Adresse</label>
                    <textarea name="address" id="address" rows="2"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Permis de conduire *</label>
                    <input type="text" name="driver_license" id="driver_license">
                </div>
            </div>
            
            <!-- Section Location -->
            <h3>Période de location</h3>
            
            <div class="form-group">
                <label>Date de début *</label>
                <input type="date" name="start_date" id="start_date" required 
                       min="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="form-group">
                <label>Date de fin *</label>
                <input type="date" name="end_date" id="end_date" required 
                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
            </div>
            
            <div class="total-price">
                <p>Durée: <span id="total_days">0</span> jour(s)</p>
                <p>Total à payer: <span id="total_price_display">0 MAD</span></p>
                <input type="hidden" name="total_price" id="total_price">
            </div>
            
            <button type="submit" class="btn-submit">Confirmer la location</button>
        </form>
    </div>
    
    <script>
        function toggleCustomerForm() {
            const type = document.getElementById('customer_type').value;
            const existingDiv = document.getElementById('existing_customer_div');
            const newDiv = document.getElementById('new_customer_div');
            
            if(type === 'existing') {
                existingDiv.style.display = 'block';
                newDiv.style.display = 'none';
            } else {
                existingDiv.style.display = 'none';
                newDiv.style.display = 'block';
            }
        }
    </script>
</body>
</html>

<?php mysqli_close($connection); ?>