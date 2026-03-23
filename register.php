<?php
$msgs = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = trim($_POST["fname"] ?? '');
    $lname = trim($_POST["lname"] ?? '');
    $user  = trim($_POST["username"] ?? '');
    $pass  = $_POST["password"] ?? '';

    if ($fname === '') {
        $msgs[] = "Invalid first name";
    }

    if ($lname === '') {
        $msgs[] = "Invalid last name";
    }

    if ($user === '') {
        $msgs[] = "Invalid username or no username given";
    }

    if ($pass === '') {
        $msgs[] = "No password given";
    }

    if (count($msgs) === 0) {
        require("includes/db_connect.php");

        if ($dbHandler) {
            try {
                $hashedPass = password_hash($pass, PASSWORD_BCRYPT);
                $defaultRoleId = 2;

                $stmt = $dbHandler->prepare("
                    INSERT INTO users (username, first_name, last_name, password, role_id)
                    VALUES (:username, :fname, :lname, :hashedpass, :role_id)
                ");

                $stmt->bindParam(':username', $user, PDO::PARAM_STR);
                $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
                $stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
                $stmt->bindParam(':hashedpass', $hashedPass, PDO::PARAM_STR);
                $stmt->bindParam(':role_id', $defaultRoleId, PDO::PARAM_INT);

                $stmt->execute();

                header("Location: log_in.php");
                exit;
            } catch (PDOException $e) {
                if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                    $msgs[] = "<p>Username already exists.</p>";
                } else {
                    $msgs[] = "<p>Something went wrong. Please try again.</p>";
                }
            }
        } else {
            $msgs[] = "<p>Database connection failed.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an account</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <div class="main-box">
        <img src="images/nopfp_icon.png" alt="no pfp icon">

        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
            <div class="name-box">
                <input type="text" id="fname" name="fname" placeholder="First name" value="<?= htmlspecialchars($fname ?? '') ?>">
                <input type="text" id="lname" name="lname" placeholder="Last name" value="<?= htmlspecialchars($lname ?? '') ?>">
            </div>

            <input type="text" id="username" name="username" placeholder="Username" value="<?= htmlspecialchars($user ?? '') ?>">
            <input type="password" id="password" name="password" placeholder="Password">

            <input class="button" type="submit" value="Create an account">
        </form>
            <?php
            echo "<div class='error-box'>";
            if (count($msgs) > 0) {
                foreach ($msgs as $msg) {
                    echo "<p>$msg</p>";
                }
            }
            echo "</div>";
            ?>
    </div>
</body>
</html>