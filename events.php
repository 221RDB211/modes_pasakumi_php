<?php
session_start();

// Pievienojam datubāzes savienojumu
$connection = mysqli_connect("localhost", "root", "", "fashion_sense");
if (!$connection) {
    die("Datubāzes savienojuma kļūda: " . mysqli_connect_error());
}

// Ja lietotājs nav pieteicies, novirzām uz login lapu
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Pievienot komentāru
if (isset($_POST['comment']) && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $comment = mysqli_real_escape_string($connection, $_POST['comment']);
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    // Pievienojam komentāru datubāzē
    $query = "INSERT INTO comments (event_id, user_id, username, comment) 
              VALUES ('$event_id', '$user_id', '$username', '$comment')";
    mysqli_query($connection, $query);
}

// Dzēst komentāru
if (isset($_POST['delete_comment']) && isset($_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];
    $user_id = $_SESSION['user_id'];

    // Pārbaudām, vai lietotājs var dzēst šo komentāru (pieder viņam)
    $query = "SELECT * FROM comments WHERE id = '$comment_id' AND user_id = '$user_id'";
    $result = mysqli_query($connection, $query);
    
    if (mysqli_num_rows($result) > 0) {
        // Dzēst komentāru no datubāzes
        $query = "DELETE FROM comments WHERE id = '$comment_id'";
        mysqli_query($connection, $query);
    }
}

// Pārbaudām, vai ir meklēšanas frāze
$search_query = "";
if (isset($_GET['search'])) {
    $search_term = mysqli_real_escape_string($connection, $_GET['search']);
    $search_query = "WHERE name LIKE '%$search_term%'";
}

// Iegūstam pasākumus no datubāzes ar meklēšanu
$query = "SELECT * FROM events $search_query";
$events_result = mysqli_query($connection, $query);
$events = mysqli_fetch_all($events_result, MYSQLI_ASSOC);

// Iegūstam komentārus no datubāzes
function get_comments($event_id) {
    global $connection;
    $query = "SELECT * FROM comments WHERE event_id = '$event_id' ORDER BY created_at DESC";
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasākumi | Fashion Sense</title>
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

        .event {
            display: flex;
            margin-bottom: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .event img {
            max-width: 200px;
            height: auto;
            border-radius: 8px;
            margin-right: 20px;
        }

        .event h3 {
            margin: 0;
            color: #333;
        }

        .event p {
            color: #666;
        }

        .purchase-button {
            padding: 15px;
            background-color: purple;
            color: white;
            text-align: center;
            margin-top: 10px;
            display: block;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
            width: 200px;
            text-align: center;
        }

        .purchase-button:hover {
            background-color: darkviolet;
        }

        .comments-section {
            margin-top: 20px;
        }

        .comment {
            background-color: #f9f9f9;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .comment p {
            margin: 0;
        }

        .comment form {
            display: inline;
        }

        .delete-button {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .delete-button:hover {
            background-color: darkred;
        }

        .comment-form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-top: 10px;
            margin-bottom: 10px;
            resize: vertical;
        }

        .comment-form button {
            padding: 10px 15px;
            background-color: purple;
            color: white;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .comment-form button:hover {
            background-color: darkviolet;
        }

        .search-form {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        .search-form input {
            padding: 10px;
            width: 250px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .search-form button {
            padding: 10px 15px;
            background-color: purple;
            color: white;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: darkviolet;
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
    <h1>Modes Pasākumi</h1>

    <!-- Meklēšanas forma -->
    <div class="search-form">
        <form action="events.php" method="GET">
            <input type="text" name="search" placeholder="Meklēt pasākumus..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <button type="submit">Meklēt</button>
        </form>
    </div>

    <?php foreach ($events as $event): ?>
        <div class="event">
            <img src="<?php echo $event['image']; ?>" alt="Event Image">
            <div>
                <h3><?php echo $event['name']; ?></h3>
                <p><strong>Datums:</strong> <?php echo $event['date']; ?></p>
                <p><?php echo $event['description']; ?></p>
                
                <!-- Cena -->
                <p><strong>Biļešu cena:</strong> €<?php echo number_format($event['ticket_price'], 2); ?></p>

                <!-- Pirkšanas poga -->
                <a href="purchase.php?event_id=<?php echo $event['id']; ?>&price=<?php echo $event['ticket_price']; ?>" 
                   style="padding: 15px; background-color: purple; color: white; text-align: center; margin-top: 10px; display: inline-block; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 18px; width: 200px; text-align: center;">
                   Pirkt biļetes
                </a>
            </div>
        </div>

        <!-- Komentāru sadaļa -->
        <div class="comments-section">
            <h3>Komentāri</h3>

            <!-- Komentāru forma -->
            <form action="events.php" method="POST" class="comment-form">
                <textarea name="comment" rows="4" placeholder="Pievienot komentāru..."></textarea>
                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                <button type="submit">Pievienot komentāru</button>
            </form>

            <!-- Komentāru parādīšana -->
            <?php $comments = get_comments($event['id']); ?>
            <?php if ($comments): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <p><strong><?php echo $comment['username']; ?>:</strong> <?php echo $comment['comment']; ?></p>

                        <!-- Dzēšanas poga -->
                        <?php if ($_SESSION['user_id'] == $comment['user_id']): ?>
                            <form action="events.php" method="POST" style="display: inline;">
                                <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                <button type="submit" name="delete_comment" class="delete-button">Dzēst</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nav komentāru.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
