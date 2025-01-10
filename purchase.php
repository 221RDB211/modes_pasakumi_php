<?php
session_start();

// Pārbaudām, vai pasākuma ID un cena ir norādīta
if (!isset($_GET['event_id']) || !isset($_GET['price'])) {
    echo "Pasākums netika atrasts vai cena nav norādīta.";
    exit();
}

$event_id = $_GET['event_id'];
$price = $_GET['price'];

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

// Apstrādāt kartes informāciju pēc formas nosūtīšanas
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $card_number = $_POST['card_number'];
    $expiry_date = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];
    $quantity = $_POST['quantity'];

    // Piemērs, kā saglabāt pirkuma informāciju datubāzē vai veikt apstrādi
    // Šeit jāintegrē maksājumu apstrādātāji kā Stripe, PayPal u.c. (iespējams, varētu izveidot simulāciju)

    // Simulēts veiksmīgs maksājums
    echo "Jūsu " . htmlspecialchars($quantity) . " biļete(s) tika iegādātas veiksmīgi par kopējo summu: €" . htmlspecialchars($price * $quantity);

    // Pārvirzām uz citu lapu, piemēram, uz paldies lapu (var pievienot header(), ja nepieciešams)
    // header("Location: success.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pirkt biļeti | Fashion Sense</title>
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

        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 auto;
        }

        .form-container h2 {
            margin-bottom: 20px;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-container button {
            padding: 10px;
            background-color: purple;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container button:hover {
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
    <h1>Ievadiet kartes informāciju</h1>

    <div class="form-container">
        <h2>Kartes dati</h2>
        <form method="POST">
            <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event_id); ?>" required>
            <input type="hidden" name="price" value="<?php echo htmlspecialchars($price); ?>" required>
            <input type="number" name="quantity" min="1" max="10" placeholder="Biļešu daudzums" required>
            <input type="text" name="card_number" placeholder="Kartes numurs" required>
            <input type="text" name="expiry_date" placeholder="Derīguma termiņš (MM/YY)" required>
            <input type="text" name="cvv" placeholder="CVV" required>
            <button type="submit">Apstiprināt maksājumu</button>
        </form>
    </div>
</div>

</body>
</html>
