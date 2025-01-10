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

// Iegūstam visus attēlus no galerijas tabulas, pievienojot arī lietotāju vārdu
$query = "SELECT gallery.*, users.username FROM gallery JOIN users ON gallery.user_id = users.id";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Kļūda iegūstot datus: " . mysqli_error($connection));
}

$images = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galerija | Fashion Sense</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
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

        .gallery {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .gallery-item {
            margin: 15px;
            text-align: center;
        }

        .gallery-item img {
            max-width: 200px;
            height: auto;
            border-radius: 8px;
        }

        .thumbs-button {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
        }

        .thumbs-button:hover {
            color: purple;
        }

        .upload-button {
            background-color: purple;
            color: white;
            padding: 10px 20px;
            border: none;
            font-size: 18px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .upload-button:hover {
            background-color: #4b0082;
        }

        .gallery-item {
            width: 300px;
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
    <h1>Galerija</h1>

    <!-- Poga augšupielādēt attēlu -->
    <div class="upload-form">
        <a href="upload.php">
            <button class="upload-button">Pievienot attēlu</button>
        </a>
    </div>

    <!-- Attēlu galerija -->
    <div class="gallery">
        <?php foreach ($images as $image): ?>
            <div class="gallery-item">
                <img src="<?php echo $image['image_url']; ?>" alt="Image">
                <p>Publicēja lietotājs: <?php echo $image['username']; ?></p>
                
                <!-- Dzēšanas poga -->
                <?php if ($_SESSION['user_id'] == $image['user_id']): ?>
                    <form action="delete_image.php" method="POST">
                        <button type="submit" name="delete" value="<?php echo $image['id']; ?>" class="thumbs-button">&#10006; Dzēst</button>
                    </form>
                <?php endif; ?>

                <!-- Balsot formas -->
                <div>
                    <!-- Thumbs Up/Down pogas, kad lietotājs vēl nav balsojis -->
                    <?php
                    $user_id = $_SESSION['user_id'];
                    $image_id = $image['id'];
                    $query_vote = "SELECT * FROM votes WHERE user_id = '$user_id' AND image_id = '$image_id'";
                    $vote_result = mysqli_query($connection, $query_vote);
                    $user_vote = mysqli_fetch_assoc($vote_result);
                    ?>
                    
                    <!-- Thumbs Up -->
                    <?php if (!$user_vote || ($user_vote['vote_type'] != 'thumbs_down')): ?>
                        <button class="thumbs-button" id="thumbs_up_<?php echo $image['id']; ?>" onclick="vote('thumbs_up', <?php echo $image['id']; ?>)">👍 <?php echo $image['thumbs_up']; ?></button>
                    <?php else: ?>
                        <button class="thumbs-button" disabled>👍 <?php echo $image['thumbs_up']; ?></button>
                    <?php endif; ?>

                    <!-- Thumbs Down -->
                    <?php if (!$user_vote || ($user_vote['vote_type'] != 'thumbs_up')): ?>
                        <button class="thumbs-button" id="thumbs_down_<?php echo $image['id']; ?>" onclick="vote('thumbs_down', <?php echo $image['id']; ?>)">👎 <?php echo $image['thumbs_down']; ?></button>
                    <?php else: ?>
                        <button class="thumbs-button" disabled>👎 <?php echo $image['thumbs_down']; ?></button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- AJAX balsošanas skripts -->
<script>
    function vote(type, image_id) {
        const xhttp = new XMLHttpRequest();
        xhttp.open("POST", "vote.php", true);
        xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                // Kad balsošana ir pabeigta, atjauninām attiecīgo pogu skaitu
                const response = JSON.parse(this.responseText);
                if (type == "thumbs_up") {
                    document.querySelector(`#thumbs_up_${image_id}`).innerText = `👍 ${response.thumbs_up}`;
                    document.querySelector(`#thumbs_up_${image_id}`).disabled = true;
                } else if (type == "thumbs_down") {
                    document.querySelector(`#thumbs_down_${image_id}`).innerText = `👎 ${response.thumbs_down}`;
                    document.querySelector(`#thumbs_down_${image_id}`).disabled = true;
                }
            }
        };
        xhttp.send("type=" + type + "&image_id=" + image_id);
    }
</script>

</body>
</html>
