<?php

session_start();

require_once('db_config.php');
require_once('helpers.php');

if(isset($_SESSION['name'])) {
    header('Location:dashboard.php'); 
} else {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if($_POST['user_email'] && $_POST['user_password']) {
            $email = htmlspecialchars($_POST['user_email']);
            $password = htmlspecialchars($_POST['user_password']);
            $sql = "select * from users where email='$email' and password='$password';";
            $statement = $conn->prepare($sql);
            $statement->execute();
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            if(!empty($row)) {
                $_SESSION['id'] = $row['id'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['password'] = $row['password'];
                $_SESSION['level'] = $row['level'];
                $_SESSION['xp'] = $row['xp'];
                $_SESSION['avatar_url'] = $row['avatar_url'];
                header('Location:dashboard.php'); 
            } else {
                $_SESSION['error'] = "Invalid Email or Password";
                header('Location:login.php');
            }
            print_r($row);
        } else { 
            header('Location:login.php');
        }
    }
}


?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Life Organizer - Login</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link rel="icon" href="assets/logo.png" sizes="16x16" type="image/png">
        <link rel="stylesheet" href="css/styles.css">
    </head>
    <body>
        <!-- NavBar-->
        <nav class="navbar navbar-light bg-light">
            <a class="navbar-brand" href="login.php">
            <img src="assets/logo.png" width="30" height="30" class="d-inline-block align-top" alt="Logo">
            Life Organizer
            </a>
        </nav>

        <!-- Log In Form -->
        <div class="container col-lg-6 mt-4">
            <h1 class="text-center">Log In</h1>
            <?php require_once('alerts.php'); ?>
            <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST">
                <div class="form-group">
                    <label for="login_email">Email Address</label>
                    <input name="user_email" type="email" class="form-control" id="login_email" aria-describedby="emailHelp" placeholder="Enter email" required>
                </div>
                <div class="form-group">
                    <label for="login_password">Password</label>
                    <input name="user_password" type="password" class="form-control" id="login_password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-green text-dark">Sign In</button>
                <a href="signup.php" class="btn btn-purple text-white">Sign Up</a>
            </form>
        </div>


<?php require_once('footer.php'); ?>