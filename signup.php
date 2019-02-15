<?php

session_start();

require_once('db_config.php');
require_once('helpers.php');

if(isset($_SESSION['name'])) {
    header('Location:dashboard.php'); 
} else {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if($_POST['user_name'] && $_POST['user_email'] && $_POST['user_password']) {
            $name = htmlspecialchars($_POST['user_name']);
            $email = htmlspecialchars($_POST['user_email']);
            $password = htmlspecialchars($_POST['user_password']);
            $user_created = create_user($conn, $name, $email, $password);
            if($user_created) {
                header('Location:login.php');
            } else {
                header('Location:signup.php');
            }
        } else { 
            header('Location:signup.php');
        }
    }
}


?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Life Organizer - Sign Up</title>
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
            <h1 class="text-center">Sign Up</h1>
            <?php require_once('alerts.php'); ?>
            <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST">
                <div class="form-group">
                    <label for="user_name">Name</label>
                    <input name="user_name" type="text" class="form-control" id="user_name" placeholder="Enter name" required>
                </div>
                <div class="form-group">
                    <label for="user_email">Email address</label>
                    <input name="user_email" type="email" class="form-control" id="user_email"  placeholder="Enter email" required>
                </div>
                <div class="form-group">
                    <label for="user_password">Password</label>
                    <input name="user_password" type="password" class="form-control" id="user_password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn bg-green text-dark">Sign Up</button>
                <a href="login.php" class="btn btn-secondary">Go Back</a>
            </form>
        </div>


        <!-- JavaScript-->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    </body>
</html>