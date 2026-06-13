<?php
require 'checksession.php';
require 'dbconnection.php';
require 'navbar.php';
require_once 'icon_helper.php';

$car_id = isset($_GET['car_id']) ? $_GET['car_id'] : null;
$car = null;

if ($car_id) {
    $car_query = "SELECT c.*, b.name as brand_name
                  FROM cars c
                  LEFT JOIN brands b ON c.brand_id = b.id
                  WHERE c.id = $car_id AND c.status = 'available'";
    $car_result = mysqli_query($connection, $car_query);
    $car = mysqli_fetch_array($car_result);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle location</title>
    <link rel="stylesheet" type="text/css" href="cssfiles/addrental.css"/>
    <style>
        .error {
            background: #fee;
            color: #c33;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
    </style>
    <script>
        function calculateTotal() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const pricePerDay = <?php echo $car ? (float) $car['price_per_day'] : 0; ?>;

            if (startDate && endDate && pricePerDay > 0) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

                if (diffDays > 0) {
                    const total = diffDays * pricePerDay;
                    document.getElementById('total_days').innerHTML = diffDays;
                    document.getElementById('total_price_display').innerHTML = total.toFixed(2) + ' MAD';
                    document.getElementById('total_price').value = total;
                }
            }
        }

        function toggleCustomerForm() {
            const type = document.getElementById('customer_type').value;
            const existingDiv = document.getElementById('existing_customer_div');
            const newDiv = document.getElementById('new_customer_div');

            if (type === 'existing') {
                existingDiv.style.display = 'block';
                newDiv.style.display = 'none';
            } else {
                existingDiv.style.display = 'none';
                newDiv.style.display = 'block';
            }
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h2><?= ui_icon('calendar') ?> Nouvelle location</h2>

        <?php if (isset($_GET['error'])): ?>
            <?php if ($_GET['error'] === 'customer_not_found'): ?>
                <div class="error"><?= ui_icon('x-circle') ?> Client non trouve</div>
            <?php elseif ($_GET['error'] === 'customer_insert_failed'): ?>
                <div class="error"><?= ui_icon('x-circle') ?> Echec de l'insertion du client</div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($car): ?>
            <div class="car-info">
                <h3>Voiture selectionnee</h3>
                <p><strong><?= htmlspecialchars($car['brand_name'] . ' ' . $car['model'], ENT_QUOTES, 'UTF-8') ?></strong><br>
                Plaque: <?= htmlspecialchars($car['license_plate'], ENT_QUOTES, 'UTF-8') ?><br>
                Prix: <?= number_format($car['price_per_day'], 0, ',', ' ') ?> MAD/jour</p>
            </div>
        <?php endif; ?>

        <form action="saverental.php" method="post" oninput="calculateTotal()">
            <input type="hidden" name="car_id" value="<?= htmlspecialchars((string) $car_id, ENT_QUOTES, 'UTF-8') ?>">

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
                    <input type="email" name="customer_email" id="customer_email" placeholder="Entrez l'email du client">
                </div>
            </div>

            <div id="new_customer_div" style="display: none;">
                <div class="form-group">
                    <label>Prenom *</label>
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
                    <label>Telephone *</label>
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

            <h3>Periode de location</h3>

            <div class="form-group">
                <label>Date de debut *</label>
                <input type="date" name="start_date" id="start_date" required min="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label>Date de fin *</label>
                <input type="date" name="end_date" id="end_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
            </div>

            <div class="total-price">
                <p>Duree: <span id="total_days">0</span> jour(s)</p>
                <p>Total a payer: <span id="total_price_display">0 MAD</span></p>
                <input type="hidden" name="total_price" id="total_price">
            </div>

            <button type="submit" class="btn-submit">Confirmer la location</button>
        </form>
    </div>
</body>
</html>

<?php mysqli_close($connection); ?>