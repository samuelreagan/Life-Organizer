<?php
session_start();

$_SESSION['page'] = "challenges";

require_once('db_config.php');
require_once('helpers.php');

if(!isset($_SESSION['name'])) {
    header('Location:login.php');
} else {
    $level = get_user_level($conn, $_SESSION['id'])[0]['level'];
    $xp = get_user_xp($conn, $_SESSION['id'])[0]['xp'];
    $progress = calculate_progress($level, $xp); 
    $label = get_challenge_label($conn, $_SESSION['id'])[0]['name']; 
}

require_once('header.php');

$notes  = get_all_notes($conn, $_SESSION['id']);
$completed_challenges = get_completed_challenges($conn, $_SESSION['id']);
$incomplete_challenges = get_incomplete_challenges($conn, $_SESSION['id']);
?>

<div class="container col-lg-12 mt-4">
    <!-- Include Alerts-->
    <h1 class="text-center">Challenges</h1>
    <hr>
    <div class="row">
        <!-- Notes Card -->
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header">
                    Completed Challenges
                </div>
                <div class="card-body">
                    <div class="table-respnsive-lg">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($completed_challenges as $challenge) : ?>
                                    <tr>
                                        <td><?= $challenge['name'] ?></td>
                                        <td><?= $challenge['body'] ?></td>
                                    </tr>
                                <?php endforeach; ?> 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Add a Note -->
        <div class="col-lg-6  mb-3">
            <div class="card">
                <div class="card-header">
                    Incomplete Challenges
                </div>
                <div class="card-body">
                    <div class="table-respnsive-lg">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($incomplete_challenges as $challenge) : ?>
                                    <tr>
                                        <td><?= $challenge['name'] ?></td>
                                        <td><?= $challenge['body'] ?></td>
                                    </tr>
                                <?php endforeach; ?> 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>                 
    </div>
</div>
<?php require_once('footer.php'); ?>