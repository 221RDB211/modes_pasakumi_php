<?php
session_start();
$connection = mysqli_connect("localhost", "root", "", "fashion_sense");
if (!$connection) {
    die("Datubāzes savienojuma kļūda: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Pārbaude, vai lietotājvārds jau eksistē
    $stmt = $connection->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $error_message = "Lietotājvārds jau tiek izmantots.";
    } else {
        // Lietotāja reģistrācija
        $stmt = $connection->prepare('INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)');
        if ($stmt->execute([$username, $password_hash, 'registered'])) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['username'] = $username;
            header("Location: index.php");  // Pārvirza uz sākumlapa pēc veiksmīgas reģistrēšanās
            exit();
        } else {
            $error_message = "Reģistrācija neizdevās.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reģistrēties | Fashion Sense</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }

        .register-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .register-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .register-container button {
            width: 100%;
            padding: 10px;
            background-color: purple;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .register-container button:hover {
            background-color: darkviolet;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Reģistrēties</h2>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST">
            Lietotājvārds: <input type="text" name="username" required>
            Parole: <input type="password" name="password" required>
            <button type="submit">Reģistrēties</button>
        </form>

        <p>Jau ir konts? <a href="login.php">Pieteikties</a></p>
    </div>
</body>
</html>
