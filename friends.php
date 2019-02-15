<?php
session_start();

$_SESSION['page'] = "friends";

require_once('db_config.php');
require_once('helpers.php');

if(!isset($_SESSION['name'])) {
    header('Location:login.php');
} else {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if($_POST['id_friend']) {
            if($_POST['id_friend'] != $_SESSION['id']) {
                $friend_id = htmlspecialchars($_POST['id_friend']);
                add_friend($conn, $friend_id, $_SESSION['id']);
            }
        }
        if($_POST['friend_id']) {
            $friend_id = htmlspecialchars($_POST['friend_id']);
            remove_friend($conn, $friend_id, $_SESSION['id']);
        }
    }
    $level = get_user_level($conn, $_SESSION['id'])[0]['level'];
    $xp = get_user_xp($conn, $_SESSION['id'])[0]['xp'];
    $progress = calculate_progress($level, $xp); 
    $label = get_challenge_label($conn, $_SESSION['id'])[0]['name']; 
}

require_once('header.php');

$friends  = get_friends($conn, $_SESSION['id']);
?>

<div class="container col-lg-12 mt-4">
    <!-- Include Alerts-->
    <h1 class="text-center">Friends</h1>
    <hr>
    <div class="row">
        <!-- Notes Card -->
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header">
                    Friends 
                </div>
                <div class="card-body">
                    <?php if(!empty($friends)) : ?>
                        <div class="table-respnsive-lg">
                            <table class="table">
                                <thead>
                                    <tr>
                                    <th>ID</th>
                                        <th>Name</th>
                                        <th>Level</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>  
                                    <?php foreach($friends as $friend) : ?>
                                        <tr>
                                            <td><?= $friend['id'] ?></td>
                                            <td><?= $friend['name'] ?></td>
                                            <td><?= $friend['level'] ?></td>
                                            <td>
                                            <form action="friends.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this friend?')">
                                                <input name="friend_id" value="<?= $friend['id'] ?>" type="hidden">
                                                <button type="submit" class="btn"><i class="fa fa-times delete-btn"></i></button>
                                            </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <h5>No Friends</h5> 
                    <?php endif; ?> 
                </div>
            </div>
        </div>
        
        <!-- Add a Note -->
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header">
                    Add a Friend
                </div>
                <div class="card-body">
                    <form action="friends.php" method="POST" class="form my-2 my-lg-0">
                        <div class="form-group">
                            <label for="friend_id">Friend ID</label>
                            <input id="friend_id" name="id_friend" class="form-control mr-sm-2" type="text" placeholder="ID #">                       
                        </div>
                        <div class="form-group">
                            <button class="btn btn-green my-2 my-sm-0" type="submit">Add Friend</button>   
                        </div>
                        <div>
                            <b>Note:</b> The only way to obtain a friends ID is by having them personally give the number to you. Your 
                            ID number is located under Edit Settings in the navigation menu.
                        </div>
                    </form>
                </div>
            </div>
        </div>                 
    </div>
</div>

<?php require_once('footer.php'); ?>