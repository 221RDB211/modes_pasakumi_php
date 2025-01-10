<?php
session_start();

// Pārbauda, vai lietotājs ir pieteicies
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$connection = mysqli_connect("localhost", "root", "", "fashion_sense");
if (!$connection) {
    die("Datubāzes savienojuma kļūda: " . mysqli_connect_error());
}

if (isset($_POST['delete'])) {
    $image_id = $_POST['delete'];
    $user_id = $_SESSION['user_id'];

    // Iegūstam attēla informāciju no datubāzes
    $query = "SELECT * FROM gallery WHERE id = '$image_id'";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) > 0) {
        $image = mysqli_fetch_assoc($result);

        // Pārbaudām, vai tas ir lietotājs, kas augšupielādējis šo attēlu
        if ($image['user_id'] == $user_id) {
            // Dzēšam attēlu no datubāzes
            $deleteQuery = "DELETE FROM gallery WHERE id = '$image_id'";
            if (mysqli_query($connection, $deleteQuery)) {
                // Dzēšam attēlu no servera
                if (unlink($image['image_url'])) {
                    echo "Attēls veiksmīgi izdzēsts.";
                } else {
                    echo "Kļūda dzēšot attēlu no servera.";
                }
            } else {
                echo "Kļūda dzēšot attēlu no datubāzes.";
            }
        } else {
            echo "Jums nav atļaujas dzēst šo attēlu.";
        }
    } else {
        echo "Attēls nav atrasts.";
    }
}

// Novirza atpakaļ uz galerijas lapu
header("Location: gallerija.php");
exit();
?>
