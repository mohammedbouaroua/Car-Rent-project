<?php
// query updating rentals status to completed if end_date has passed
    $queryUpdateRentals = "
         UPDATE rentals 
         SET status = 'completed',
         return_date = CURDATE()
         WHERE status = 'active'
         AND end_date < CURDATE();
         ";         
    mysqli_query($connection, $queryUpdateRentals);
// query updating cars status to available if rental has been completed
    $queryFreeCars = "
        UPDATE cars c
        JOIN rentals r ON c.id = r.car_id
        SET c.status = 'available'
        WHERE r.status = 'completed'
        AND c.status = 'rented'";
    mysqli_query($connection, $queryFreeCars);
?>