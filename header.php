<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Life Organizer</title>
        <link rel="stylesheet" href="css/styles.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link rel="icon" href="assets/logo.png" sizes="16x16" type="image/png">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    </head>
    <body>
        <!-- NavBar-->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="dashboard.php">
            <img src="assets/logo.png" width="30" height="30" class="d-inline-block align-top" alt="">
            Life Organizer
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item <?= ($_SESSION['page'] == 'dashboard') ? "active" : ""?>">
                        <a class="nav-link" href="dashboard.php">Home</a>
                    </li>
                    <li class="nav-item <?= ($_SESSION['page'] == 'habits') ? "active" : ""?>">
                        <a class="nav-link" href="habits.php">Habits</a>
                    </li>
                    <li class="nav-item <?= ($_SESSION['page'] == 'tasks') ? "active" : ""?>">
                        <a class="nav-link" href="tasks.php">Tasks</a>
                    </li>
                    <li class="nav-item <?= ($_SESSION['page'] == 'goals') ? "active" : ""?>">
                        <a class="nav-link" href="goals.php">Goals</a>
                    </li>
                    <li class="nav-item <?= ($_SESSION['page'] == 'notes') ? "active" : ""?>">
                        <a class="nav-link" href="notes.php">Notes</a>
                    </li>
                    <li class="nav-item <?= ($_SESSION['page'] == 'friends') ? "active" : ""?>">
                        <a class="nav-link" href="friends.php">Friends</a>
                    </li>
                    <li class="nav-item <?= ($_SESSION['page'] == 'challenges') ? "active" : ""?>">
                        <a class="nav-link" href="challenges.php">Challenges</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Settings
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <span class="dropdown-item"><b>Your ID: </b><?= $_SESSION['id'] ?></span>
                            <div class="dropdown-divider"></div>
                            <form action="logout.php" method="POST">
                                <button type="submit" class="dropdown-item" href="#">Log Out</button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="py-3 px-3 border">
            <div class="row">
                <div class="col-lg-6 my-1">
                    <?= $_SESSION['name']?>  
                    <span class="badge bg-green">Level <?= $level ?> - <?= $label ?></span>
                    <span class="badge bg-purple text-white"><?= $xp ?> XP </span> 
                </div>
                <div class="col-lg-6 mt-2">
                    <div class="progress">
                        <div class="progress-bar bg-purple" role="progressbar" style="width: <?= $progress ?>%" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"><?= round($progress) ?>%</div>
                    </div>
                </div>
            </div>
        </div> 