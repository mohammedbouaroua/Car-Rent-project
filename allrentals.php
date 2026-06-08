<?php
require 'checksession.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'AD') {
    header("Location: authForm.php?auth=access_denied");
}
require 'navbar.php';
require 'dbconnection.php';

// Filtres
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? mysqli_real_escape_string($connection, $_GET['search']) : '';

// Requête avec JOINs
$query = "SELECT r.*, 
          c.id as car_id, c.model, c.license_plate,
          b.name as brand_name,
          cu.id as customer_id, cu.firstname, cu.lastname, cu.phone
          FROM rentals r
          JOIN cars c ON r.car_id = c.id
          JOIN brands b ON c.brand_id = b.id
          JOIN customers cu ON r.customer_id = cu.id
          WHERE 1=1";

if($status_filter != 'all') {
    $query .= " AND r.status = '$status_filter'";
}

if(!empty($search)) {
    $query .= " AND (cu.firstname LIKE '%$search%' 
                 OR cu.lastname LIKE '%$search%' 
                 OR c.license_plate LIKE '%$search%'
                 OR b.name LIKE '%$search%'
                 OR c.model LIKE '%$search%')";
}

$query .= " ORDER BY r.created_at DESC";
$result = mysqli_query($connection, $query);
$total_rentals = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des locations - Car Rental</title>
    <link rel="stylesheet" href="cssfiles/allrentals.css">
    <style>.btn-cancel {
    background: #e74c3c;
    color: white;
    padding: 6px 10px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
    transition: 0.3s ease;
}

.btn-cancel:hover {
    background: #c0392b;
    transform: scale(1.05);
}

.no-action {
    color: #999;
    font-size: 14px;
}</style>
</head>
<body>
    <div class="container">
        
        <!-- Page Header -->
        <div class="page-header">
            <h2>📋 Gestion des locations</h2>
        </div>
          <?php if(isset($_GET['success'])): ?>
            <div class="success">✅ Location ajoutée avec succès</div>
          <?php endif; ?>
        <!-- Filter Bar -->
        <div class="filter-bar">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label>🔍 Rechercher</label>
                    <input type="text" name="search" placeholder="Nom, prénom, plaque..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="filter-group">
                    <label>📊 Statut</label>
                    <select name="status">
                        <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>Tous</option>
                        <option value="active" <?php echo $status_filter == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="completed" <?php echo $status_filter == 'completed' ? 'selected' : ''; ?>>Terminée</option>
                        <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Annulée</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-search">🔍 Filtrer</button>
                    <a href="allrentals.php"><button type="button" class="btn-reset">⟳ Réinitialiser</button></a>
                </div>
            </form>
        </div>
        
        <!-- Rentals Table -->
        <div class="rentals-table">
            <?php if($total_rentals > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Véhicule</th>
                            <th>Plaque</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($rental = mysqli_fetch_array($result)): ?>
                        <tr>
                            <td>#<?php echo $rental['id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($rental['firstname'] . ' ' . $rental['lastname']); ?><br>
                                <small style="color: #888;"><?php echo $rental['phone']; ?></small>
                            </td>
                            <td>
                                <?php echo $rental['brand_name'] . ' ' . $rental['model']; ?>
                            </td>
                            <td><?php echo $rental['license_plate']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($rental['start_date'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($rental['end_date'])); ?></td>
                            <td><strong><?php echo number_format($rental['total_price'], 0, ',', ' '); ?> MAD</strong></td>
                            <td>
                                <span class="status-badge status-<?php echo $rental['status']; ?>">
                                    <?php 
                                        switch($rental['status']) {
                                            case 'active': echo '🚗 Active'; break;
                                            case 'completed': echo '✔️ Terminée'; break;
                                            case 'cancelled': echo '❌ Annulée'; break;
                                            default: echo $rental['status'];
                                        }
                                    ?>
                                </span>
                            </td>
                            <td>
                                <?php if($rental['status'] == 'active'): ?>
                                    <a href="cancel_rental.php?id=<?php echo $rental['id']; ?>"
                                       onclick="return confirm('Annuler cette location ?');"
                                       class="btn-cancel">
                                       ❌ Cancel
                                    </a>
                                <?php else: ?>
                                    <span class="no-action">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="total-count">
                    📊 Total: <strong><?php echo $total_rentals; ?></strong> location(s)
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div style="font-size: 48px; margin-bottom: 10px;">📭</div>
                    <h3>Aucune location trouvée</h3>
                    <p>Essayez de modifier vos critères de recherche</p>
                    <a href="allcars.php" class="btn-add" style="margin-top: 15px;">➕ Nouvelle location</a>
                </div>
            <?php endif; ?>
        </div>
        
    </div>
</body>
</html>

<?php mysqli_close($connection); ?>