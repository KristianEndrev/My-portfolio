<?php
session_start();
$msgs = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $msgs[] = "Please fill in both username and password.";
    } else {
        require("includes/db_connect.php");

        try {
            $stmt = $dbHandler->prepare(
                "SELECT user_id, username, password, role_id 
                 FROM users 
                 WHERE username = :username"
            );

            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                $msgs[] = "Invalid username or password.";
            } else {
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id'];

                header("Location: home.php");
                exit;
            }
        } catch (PDOException $ex) {
            $msgs[] = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <link rel="stylesheet" href="css/log_in.css">
</head>
<body>
    <div class="main-box">
        <img src="images/nopfp_icon.png" alt="no pfp icon">

        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
            <input name="username" type="text" placeholder="Username" value="<?= htmlspecialchars($username ?? '') ?>">

            <input type="password" name="password" placeholder="Password">

            <input class="button" type="submit" value="Sign in">
        </form>

        <a href="register.php">Don't have an account?</a>
            <?php
            echo "<div class='error-box'>";
            foreach ($msgs as $msg) {
                echo "<p>$msg</p>";
            }
            echo "</div>";
            ?>
        </div>
    </div>
</body>
</html>