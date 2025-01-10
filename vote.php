<?php
session_start();

// Pārbauda, vai lietotājs ir pieteicies
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array("error" => "Lietotājs nav pieteicies"));
    exit();
}

$connection = mysqli_connect("localhost", "root", "", "fashion_sense");
if (!$connection) {
    die("Datubāzes savienojuma kļūda: " . mysqli_connect_error());
}

if (isset($_POST['type']) && isset($_POST['image_id'])) {
    $user_id = $_SESSION['user_id'];
    $image_id = $_POST['image_id'];
    $type = $_POST['type'];

    // Pārbauda, vai lietotājs jau ir balsojis
    $query = "SELECT * FROM votes WHERE user_id = '$user_id' AND image_id = '$image_id'";
    $result = mysqli_query($connection, $query);
    
    // Ja lietotājs nav balsojis, ievieto balsi
    if (mysqli_num_rows($result) == 0) {
        // Pievieno balsi datubāzē
        $query = "INSERT INTO votes (user_id, image_id, vote_type) VALUES ('$user_id', '$image_id', '$type')";
        mysqli_query($connection, $query);

        // Atjauno thumbs skaitļus galerijas tabulā
        if ($type == 'thumbs_up') {
            $query = "UPDATE gallery SET thumbs_up = thumbs_up + 1 WHERE id = '$image_id'";
        } else {
            $query = "UPDATE gallery SET thumbs_down = thumbs_down + 1 WHERE id = '$image_id'";
        }
        mysqli_query($connection, $query);
    }

    // Iegūst atjaunotos balsojuma rezultātus
    $query = "SELECT thumbs_up, thumbs_down FROM gallery WHERE id = '$image_id'";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);

    echo json_encode(array("thumbs_up" => $row['thumbs_up'], "thumbs_down" => $row['thumbs_down']));
} else {
    echo json_encode(array("error" => "Nepareizi dati"));
}
?>
