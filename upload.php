<?php
session_start();

// Pārbauda, vai lietotājs ir pieteicies
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Savienojums ar datubāzi
$connection = mysqli_connect("localhost", "root", "", "fashion_sense");
if (!$connection) {
    die("Datubāzes savienojuma kļūda: " . mysqli_connect_error());
}

// Ja ir nosūtīts attēls
if (isset($_FILES['image'])) {
    $image = $_FILES['image'];
    
    // Pārbauda vai nav kļūdas augšupielādē
    if ($image['error'] === 0) {
        // Nosaka mērķa direktoriju
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($image['name']);
        
        // Pārbauda, vai fails jau eksistē
        if (file_exists($targetFile)) {
            echo "Attēls jau eksistē.";
        } else {
            // Pārbauda vai faila tips ir atļauts
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($image['type'], $allowedTypes)) {
                // Pārvieto failu uz galamērķi
                if (move_uploaded_file($image['tmp_name'], $targetFile)) {
                    // Ievieto attēlu datubāzē
                    $user_id = $_SESSION['user_id'];
                    $image_url = $targetFile;

                    $query = "INSERT INTO gallery (user_id, image_url) VALUES ('$user_id', '$image_url')";
                    if (mysqli_query($connection, $query)) {
                        echo "Attēls veiksmīgi augšupielādēts.";
                        header("Location: gallerija.php"); // Pāradresē uz galeriju
                        exit();
                    } else {
                        echo "Kļūda pievienojot attēlu datubāzē.";
                    }
                } else {
                    echo "Attēla augšupielāde neizdevās.";
                }
            } else {
                echo "Nepareizs attēla formāts. Atļautie formāti: JPEG, PNG, GIF.";
            }
        }
    } else {
        echo "Attēla augšupielādes laikā radās kļūda.";
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pievienot attēlu | Fashion Sense</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: purple;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
        }

        .header .logo img {
            height: 80px;
        }

        .header .navigation a {
            color: white;
            text-decoration: none;
            padding: 10px;
        }

        .header .navigation a:hover {
            background-color: #4b0082;
            border-radius: 5px;
        }

        .main-content {
            padding: 20px;
        }

        .upload-form {
            text-align: center;
            margin-top: 30px;
        }

        .upload-form input[type="file"] {
            margin: 10px;
        }

        .upload-form button {
            background-color: purple;
            color: white;
            padding: 10px 20px;
            border: none;
            font-size: 18px;
            cursor: pointer;
        }

        .upload-form button:hover {
            background-color: #4b0082;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div class="logo">
        <img src="logo.png" alt="Fashion Sense Logo">
    </div>
    <div class="navigation">
        <a href="index.php">Sākumlapa</a>
        <a href="events.php">Pasākumi</a>
        <a href="gallerija.php">Galerija</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php">Izrakstīties</a>
        <?php else: ?>
            <a href="login.php">Pieteikties</a>
            <a href="register.php">Reģistrēties</a>
        <?php endif; ?>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <h1>Pievienot attēlu</h1>

    <!-- Attēla augšupielādes forma -->
    <div class="upload-form">
        <form action="upload.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="image" required>
            <button type="submit">Augšupielādēt attēlu</button>
        </form>
    </div>
</div>

</body>
</html>