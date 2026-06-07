<?php
// require 'checksession.php';
require 'dbconnection.php';

// Filtres
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Requête avec JOINs
$query = "SELECT r.*, 
          c.brand_id, c.model, c.license_plate, c.photo,
          b.name as brand_name,
          cu.firstname, cu.lastname, cu.email, cu.phone
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion des locations</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
    <style>
        .rental-table { width: 100%; border-collapse: collapse; }
        .rental-table th, .rental-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .rental-table th { background: #f2f2f2; }
        .status-pending { background: #ffc107; color: #212529; padding: 3px 8px; border-radius: 3px; }
        .status-active { background: #17a2b8; color: white; padding: 3px 8px; border-radius: 3px; }
        .status-completed { background: #28a745; color: white; padding: 3px 8px; border-radius: 3px; }
        .status-cancelled { background: #dc3545; color: white; padding: 3px 8px; border-radius: 3px; }
        .filter-bar { margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .btn-return {
            background: #007bff;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <h2>Gestion des locations</h2>
    
    <div class="filter-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Rechercher..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="status">
                <option value="all" <?php echo $status_filter=='all'?'selected':''; ?>>Tous les statuts</option>
                <option value="pending" <?php echo $status_filter=='pending'?'selected':''; ?>>En attente</option>
                <option value="confirmed" <?php echo $status_filter=='confirmed'?'selected':''; ?>>Confirmée</option>
                <option value="active" <?php echo $status_filter=='active'?'selected':''; ?>>Active</option>
                <option value="completed" <?php echo $status_filter=='completed'?'selected':''; ?>>Terminée</option>
                <option value="cancelled" <?php echo $status_filter=='cancelled'?'selected':''; ?>>Annulée</option>
            </select>
            <input type="submit" value="Filtrer">
            <a href="allrentals.php"><button type="button">Réinitialiser</button></a>
        </form>
    </div>
    
    <?php if($total_rentals > 0): ?>
        <table class="rental-table">
            <thead>
                <tr>
                    <th>Réf.</th>
                    <th>Client</th>
                    <th>Véhicule</th>
                    <th>Plaque</th>
                    <th>Début</th>
                    <th>Fin</th>
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
                        <?php echo $rental['firstname'] . ' ' . $rental['lastname']; ?><br>
                        <small><?php echo $rental['phone']; ?></small>
                    </td>
                    <td><?php echo $rental['brand_name'] . ' ' . $rental['model']; ?></td>
                    <td><?php echo $rental['license_plate']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($rental['start_date'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($rental['end_date'])); ?></td>
                    <td><?php echo $rental['total_price']; ?> MAD</td>
                    <td>
                        <span class="status-<?php echo $rental['status']; ?>">
                            <?php 
                                switch($rental['status']) {
                                    case 'pending': echo 'En attente'; break;
                                    case 'confirmed': echo 'Confirmée'; break;
                                    case 'active': echo 'Active'; break;
                                    case 'completed': echo 'Terminée'; break;
                                    case 'cancelled': echo 'Annulée'; break;
                                }
                            ?>
                        </span>
                    </td>
                    <td>
                        <a href="showrental.php?id=<?php echo $rental['id']; ?>">👁️ Détails</a>
                        <?php if($rental['status'] == 'active'): ?>
                            <a href="returncar.php?id=<?php echo $rental['id']; ?>" onclick="return confirm('Confirmer le retour ?')">✅ Retour</a>
                        <?php endif; ?>
                        <?php if($_SESSION['role'] == 'AD' && $rental['status'] != 'completed' && $rental['status'] != 'cancelled'): ?>
                            <a href="cancelrental.php?id=<?php echo $rental['id']; ?>" onclick="return confirm('Annuler cette location ?')">❌ Annuler</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <p><strong>Total : <?php echo $total_rentals; ?> location(s)</strong></p>
    <?php else: ?>
        <p>Aucune location trouvée.</p>
    <?php endif; ?>
    
    <a href="dashboard.php" class="btn-return">← Tableau de bord</a>
</body>
</html>

<?php mysqli_close($connection); ?>