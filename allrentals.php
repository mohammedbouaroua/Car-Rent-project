<?php
require 'checksession.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'AD') {
    header('Location: authForm.php?auth=access_denied');
}
require 'navbar.php';
require 'dbconnection.php';
require_once 'icon_helper.php';

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? mysqli_real_escape_string($connection, $_GET['search']) : '';

$query = "SELECT r.*,
          c.id as car_id, c.model, c.license_plate,
          b.name as brand_name,
          cu.id as customer_id, cu.firstname, cu.lastname, cu.phone
          FROM rentals r
          JOIN cars c ON r.car_id = c.id
          JOIN brands b ON c.brand_id = b.id
          JOIN customers cu ON r.customer_id = cu.id
          WHERE 1=1";

if ($status_filter != 'all') {
    $query .= " AND r.status = '$status_filter'";
}

if (!empty($search)) {
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
    <style>
        .btn-cancel {
            background: #e74c3c;
            color: white;
            padding: 6px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-cancel:hover {
            background: #c0392b;
            transform: scale(1.05);
        }

        .no-action {
            color: #999;
            font-size: 14px;
        }

        .success {
            background: #e0f8e9;
            color: #2d7a46;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h2><?= ui_icon('clipboard') ?> Gestion des locations</h2>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success"><?= ui_icon('check-circle') ?> Location ajoutee avec succes</div>
        <?php endif; ?>

        <div class="filter-bar">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label><?= ui_icon('search') ?> Rechercher</label>
                    <input type="text" name="search" placeholder="Nom, prenom, plaque..." value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="filter-group">
                    <label><?= ui_icon('chart-bar') ?> Statut</label>
                    <select name="status">
                        <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>Tous</option>
                        <option value="active" <?php echo $status_filter == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="completed" <?php echo $status_filter == 'completed' ? 'selected' : ''; ?>>Terminee</option>
                        <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Annulee</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-search"><?= ui_icon('search') ?> Filtrer</button>
                    <a href="allrentals.php" class="btn-reset"><?= ui_icon('refresh-cw') ?> Reinitialiser</a>
                </div>
            </form>
        </div>

        <div class="rentals-table">
            <?php if ($total_rentals > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Vehicule</th>
                            <th>Plaque</th>
                            <th>Date debut</th>
                            <th>Date fin</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($rental = mysqli_fetch_array($result)): ?>
                            <tr>
                                <td>#<?php echo $rental['id']; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($rental['firstname'] . ' ' . $rental['lastname'], ENT_QUOTES, 'UTF-8'); ?><br>
                                    <small style="color: #888;"><?php echo htmlspecialchars($rental['phone'], ENT_QUOTES, 'UTF-8'); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($rental['brand_name'] . ' ' . $rental['model'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($rental['license_plate'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($rental['start_date'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($rental['end_date'])); ?></td>
                                <td><strong><?php echo number_format($rental['total_price'], 0, ',', ' '); ?> MAD</strong></td>
                                <td>
                                    <span class="status-badge status-<?php echo $rental['status']; ?>">
                                        <?php
                                        switch ($rental['status']) {
                                            case 'active':
                                                echo ui_icon('car') . ' Active';
                                                break;
                                            case 'completed':
                                                echo ui_icon('check-circle') . ' Terminee';
                                                break;
                                            case 'cancelled':
                                                echo ui_icon('x-circle') . ' Annulee';
                                                break;
                                            default:
                                                echo htmlspecialchars($rental['status'], ENT_QUOTES, 'UTF-8');
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($rental['status'] == 'active'): ?>
                                        <a href="cancel_rental.php?id=<?php echo $rental['id']; ?>" onclick="return confirm('Annuler cette location ?');" class="btn-cancel">
                                            <?= ui_icon('x-circle') ?> Annuler
                                        </a>
                                    <?php else: ?>
                                        <span class="no-action">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="total-count">
                    <?= ui_icon('chart-bar') ?> Total: <strong><?php echo $total_rentals; ?></strong> location(s)
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon"><?= ui_icon('clipboard') ?></div>
                    <h3>Aucune location trouvee</h3>
                    <p>Essayez de modifier vos criteres de recherche</p>
                    <a href="allcars.php" class="btn-add" style="margin-top: 15px;"><?= ui_icon('add') ?> Nouvelle location</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php mysqli_close($connection); ?>