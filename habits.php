<?php
session_start();

$_SESSION['page'] = "habits";

require_once('db_config.php');
require_once('helpers.php');

if(!isset($_SESSION['name'])) {
    header('Location:login.php');
} else {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if($_POST['habit_name']) {
            $habit_name = htmlspecialchars(str_replace("'", "''",$_POST['habit_name']));
            insert_habit($conn, $_SESSION['id'], $habit_name);
        }
        if($_POST['habit_id']) {
            $habit_id = htmlspecialchars($_POST['habit_id']);
            delete_habit($conn, $habit_id);
        }
        if($_POST['edit_id']) {
            $habit_id = htmlspecialchars($_POST['edit_id']);
            $habit_name = htmlspecialchars(str_replace("'", "''",$_POST['edit_name']));
            update_habit($conn, $habit_id, $habit_name);
        }

    }
    $level = get_user_level($conn, $_SESSION['id'])[0]['level'];
    $xp = get_user_xp($conn, $_SESSION['id'])[0]['xp'];
    $progress = calculate_progress($level, $xp); 
    $label = get_challenge_label($conn, $_SESSION['id'])[0]['name']; 
}

require_once('header.php');

$habits  = get_all_habits($conn, $_SESSION['id']);
?>

<div class="container col-lg-12 mt-4">
    <!-- Include Alerts-->
    <h1 class="text-center">Habits</h1>
    <hr>
        <div class="row">
            <!-- Notes Card -->
            <div class="col-lg-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        Habit Streaks
                    </div>
                    <div class="card-body">
                        <?php if(!empty($habits)) : ?>      
                            <div class="table-respnsive-lg">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Habit</th>
                                            <th>Current Streak</th>
                                            <th class="text-center">Edit</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>                                   
                                        <?php foreach($habits as $habit) : ?>
                                            <tr>
                                                <td><?= $habit['name']?></td>
                                                <td><?= habit_streak($conn, $habit['id'])?> day(s)</td> 
                                                <td class="text-center pointer" data-toggle="modal" data-target="#modal<?= $habit['id']?>"><i class="far fa-edit"></i></td>
                                                <td class="text-center">
                                                    <form class="mb-0" action="habits.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this habit?')">
                                                        <input name="habit_id" value="<?= $habit['id'] ?>" type="hidden">
                                                        <button type="submit" class="btn"><i class="fa fa-times delete-btn"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>                                   
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <h5>No Habits Created</h5>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Add a Note -->
            <div class="col-lg-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        Add a Habit
                    </div>
                    <div class="card-body">
                        <form action="habits.php" method="POST" class="form my-2 my-lg-0">
                            <div class="form-group">
                                <label for="habit_name">Habit</label>
                                <input id="habit_name" name="habit_name" class="form-control mr-sm-2" type="text" placeholder="Name" required>                       
                            </div>
                            <div class="form-group">
                                <button class="btn btn-green my-2 my-sm-0" type="submit">Save</button>   
                            </div>
                        </form>
                    </div>
                </div>
            </div>                 
        </div>
</div>

<?php if(!empty($habits)): ?>
    <?php foreach($habits as $habit) : ?>
    <div class="modal fade" id="modal<?= $habit['id']?>" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Habit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" class="form my-2 my-lg-0">
                    <input type="hidden" name="edit_id" value="<?= $habit['id'] ?>">
                    <div class="form-group">
                        <input name="edit_name" class="form-control mr-sm-2" type="text" placeholder="Note Title" required value="<?= $habit['name']?>">                       
                    </div>
                    <div class="form-group">
                        <button class="btn btn-green my-2 my-sm-0" type="submit">Save</button>  
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> 
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>

    <?php endforeach; ?>
<?php endif;?>


<?php require_once('footer.php'); ?>