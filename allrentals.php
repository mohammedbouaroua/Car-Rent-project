<?php
require 'checksession.php';
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
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .page-header h2 {
            margin: 0;
            color: #333;
        }
        
        .filter-bar {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }
        
        .filter-group {
            flex: 1;
            min-width: 180px;
        }
        
        .filter-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #666;
            margin-bottom: 5px;
        }
        
        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .filter-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-search, .btn-reset {
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .btn-search {
            background: #667eea;
            color: white;
        }
        
        .btn-search:hover {
            background: #5a67d8;
        }
        
        .btn-reset {
            background: #6c757d;
            color: white;
        }
        
        .btn-reset:hover {
            background: #5a6268;
        }
        
        .rentals-table {
            background: white;
            border-radius: 10px;
            overflow-x: auto;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #555;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background: #cff4fc;
            color: #055160;
        }
        
        .status-active {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-completed {
            background: #d1e7dd;
            color: #0a3622;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .total-count {
            margin-top: 15px;
            padding: 10px;
            text-align: right;
            font-weight: bold;
            color: #666;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px;
            color: #999;
        }
        
        .btn-add {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-add:hover {
            background: #218838;
        }
        
        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
            }
            
            .filter-actions {
                width: 100%;
            }
            
            .btn-search, .btn-reset {
                flex: 1;
            }
            
            th, td {
                padding: 8px 10px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        
        <!-- Page Header -->
        <div class="page-header">
            <h2>📋 Gestion des locations</h2>
        </div>
        
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
                        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>En attente</option>
                        <option value="confirmed" <?php echo $status_filter == 'confirmed' ? 'selected' : ''; ?>>Confirmée</option>
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
                                            case 'pending': echo '⏳ En attente'; break;
                                            case 'confirmed': echo '✅ Confirmée'; break;
                                            case 'active': echo '🚗 Active'; break;
                                            case 'completed': echo '✔️ Terminée'; break;
                                            case 'cancelled': echo '❌ Annulée'; break;
                                            default: echo $rental['status'];
                                        }
                                    ?>
                                </span>
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