<?php
session_start();

if (isset($_SESSION['user_id'])) {
    echo "Sveiki, " . htmlspecialchars($_SESSION['username']) . "! ";
    echo '<a href="logout.php">Izrakstīties</a>';
} else {
    echo '<a href="login.php">Pieteikties</a> vai <a href="register.php">Reģistrēties</a>';
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashion Sense</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: purple;
            padding: 10px 20px;
            color: white;
        }

        .logo img {
            height: 120px;
            width: auto;
        }

        .navigation {
            display: flex;
            align-items: center;
        }

        .navigation a {
            text-decoration: none;
            color: white;
            margin-left: 20px;
            font-size: 18px;
            padding: 10px 15px;
            border-radius: 5px;
            background-color: #4b0082; /* Vienota fona krāsa visām pogām */
        }

        .navigation a:hover {
            background-color: #3b0069; /* Tumšāka krāsa peles virsū */
        }

        .main-content {
            padding: 20px;
            text-align: center;
        }

        .article {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .article img {
            width: 70%;
            height: auto;
            border-radius: 8px;
        }

        .article h2 {
            margin-top: 20px;
            color: #333;
        }

        .article p {
            line-height: 1.6;
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <div class="logo">
            <img src="logo.png" alt="Fashion Sense Logo">
        </div>
        <nav class="navigation">
            <a href="index.php">Sākumlapa</a>
            <a href="events.php">Pasākumi</a>
            <a href="gallerija.php">Galerija</a> <!-- Galerijas saite -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php">Izrakstīties</a>
            <?php else: ?>
                <a href="login.php">Pieteikties</a>
                <a href="register.php">Reģistrēties</a>
            <?php endif; ?>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="article">
            <img src="logo.png" alt="Modes attēls">
            <h2>Jaunākās modes tendences 2025</h2>
            <p>
                Modes pasaule turpina pārsteigt ar radošumu un inovācijām. 2025. gads piedāvā drosmīgas krāsas, unikālas formas un ilgtspējīgus materiālus. Šajā sezonā 
                dominē minimālisms un moderns pieskāriens klasiskajam dizainam. Uzsvars tiek likts uz pašizpausmi un individualitāti. Modes pasākumi ir lieliska iespēja 
                iepazīties ar jaunākajām kolekcijām un gūt iedvesmu ikdienas stilam.
            </p>
        </div>
    </div>

</body>
</html>
