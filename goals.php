<?php
session_start();

$_SESSION['page'] = "goals";

require_once('db_config.php');
require_once('helpers.php');

if(!isset($_SESSION['name'])) {
    header('Location:login.php');
} else {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if($_POST['goal_name'] && $_POST['goal_desc'] && $_POST['goal_deadline']) {
            $goal_name = htmlspecialchars(str_replace("'", "''",$_POST['goal_name']));
            $goal_desc = htmlspecialchars(str_replace("'", "''",$_POST['goal_desc']));
            $goal_deadline = htmlspecialchars($_POST['goal_deadline']);
            insert_goal($conn, $_SESSION['id'], $goal_name, $goal_desc, $goal_deadline);
        }
        if($_POST['goal_id']) {
            $goal_id = htmlspecialchars($_POST['goal_id']);
            if(!empty($_POST['edit_goal'])) {
                if($_POST['edit_goal'] == 'mark_complete') {
                    mark_goal_complete($conn, $goal_id); 
                } else {
                    mark_goal_incomplete($conn, $goal_id); 
                }
            } else {
                delete_goal($conn, $goal_id);
            }
        }
        if($_POST['edit_id']) {
            $goal_id = htmlspecialchars($_POST['edit_id']);
            $goal_name = htmlspecialchars(str_replace("'", "''",$_POST['edit_name']));
            $goal_desc = htmlspecialchars(str_replace("'", "''",$_POST['edit_desc']));
            $goal_deadline = htmlspecialchars($_POST['edit_deadline']);
            update_goal($conn, $goal_id, $goal_name, $goal_desc, $goal_deadline);
        }
    }
    $level = get_user_level($conn, $_SESSION['id'])[0]['level'];
    $xp = get_user_xp($conn, $_SESSION['id'])[0]['xp'];
    $progress = calculate_progress($level, $xp); 
    $label = get_challenge_label($conn, $_SESSION['id'])[0]['name']; 
}

require_once('header.php');

$incomplete_goals  = get_incomplete_goals($conn, $_SESSION['id']);
$complete_goals    = get_complete_goals($conn, $_SESSION['id']);

if(!empty($incomplete_goals)) {
    foreach($incomplete_goals as &$goal) {
        $goal['tasks'] = get_goal_tasks($conn, $goal['id']);
    }
}

if(!empty($complete_goals)) {
    foreach($complete_goals as &$goal) {
        $goal['tasks'] = get_goal_tasks($conn, $goal['id']);
    }
}
?>
<div class="container col-lg-12 mt-4">
    <!-- Include Alerts-->
    <h1 class="text-center">Goals</h1>
    <hr>
    <div class="row">
        <!-- Goals Card -->
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header">
                    Goals 
                </div>
                <div class="card-body">
                    <?php if(!empty($incomplete_goals) || !empty($complete_goals)): ?>
                        <h3>Incomplete Goals</h3>
                        <hr>
                        <?php if(!empty($incomplete_goals)): ?>
                            <?php foreach($incomplete_goals as $goal): ?>
                                <div class="mb-4">
                                    <h5><?= $goal['name'] ?></h5>
                                    <p class="card-text">
                                        <?= $goal['body'] ?>
                                        <div class="mt-1">
                                            <span class="badge bg-purple text-white">Deadline: <?= $goal['deadline'] ?></span>
                                        </div>
                                        <?php if(!empty($goal['tasks'])) : ?>
                                        <h6 class="mt-2">Tasks</h6>
                                            <?php foreach($goal['tasks'] as $task):?>
                                                <div class="badge <?= ($task['is_complete'] != 't') ? 'badge-danger': 'btn-green' ?>"><?= $task['body'] ?></div> <br>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </p>
                                    <form class="d-inline" action="goals.php" method="POST" onsubmit="return confirm('Are you sure you want to mark this goal complete?')">
                                        <input name="goal_id" value="<?= $goal['id'] ?>" type="hidden">
                                        <input name="edit_goal" value="mark_complete" type="hidden">
                                        <button type="submit" class="btn btn-green">Mark Complete</button> 
                                    </form>      
                                    <button class="btn btn-purple text-white" data-toggle="modal" data-target="#modal<?= $goal['id']?>">Edit</button>          
                                    <form class="d-inline" action="goals.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this goal?')">
                                        <input name="goal_id" value="<?= $goal['id'] ?>" type="hidden">
                                        <button type="submit" class="btn btn-secondary">Delete</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <h5>- No Incomplete Goals</h5>
                        <?php endif;?>
                        <hr>
                        <h3>Complete Goals</h3>
                        <hr>
                        <?php if(!empty($complete_goals)): ?>
                            <?php foreach($complete_goals as $goal): ?>
                                <div>
                                    <h5><?= $goal['name'] ?></h5>
                                    <p class="card-text">
                                        <?= $goal['body'] ?>
                                        <div class="mt-1">
                                            <span class="badge bg-purple text-white">Deadline: <?= $goal['deadline'] ?></span>
                                        </div>
                                        <?php if(!empty($goal['tasks'])) : ?>
                                        <h6 class="mt-2">Tasks</h6>
                                            <?php foreach($goal['tasks'] as $task):?>
                                                <div class="badge <?= ($task['is_complete'] != 't') ? 'badge-danger': 'btn-green' ?>"><?= $task['body'] ?></div> <br>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </p>
                                    <form class="d-inline" action="goals.php" method="POST" onsubmit="return confirm('Are you sure you want to mark this goal complete?')">
                                        <input name="goal_id" value="<?= $goal['id'] ?>" type="hidden">
                                        <input name="edit_goal" value="mark_incomplete" type="hidden">
                                        <button type="submit" class="btn btn-green">Mark Incomplete</button> 
                                    </form>
                                    <form class="d-inline" action="goals.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this goal?')">
                                        <input name="goal_id" value="<?= $goal['id'] ?>" type="hidden">
                                        <button type="submit" class="btn btn-secondary">Delete</button>
                                    </form>
                                </div>
                                <hr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <h5>- No Complete Goals</h5>
                        <?php endif; ?>
                    <?php else : ?>
                        <h5>No Goals Created</h5>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Add a Goal -->
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header">
                    Add a Goal
                </div>
                <div class="card-body">
                    <form action="goals.php" method="POST" class="form my-2 my-lg-0">
                        <div class="form-group">
                            <label for="goal_name">Goal</label>
                            <input id="goal_name" name="goal_name" class="form-control mr-sm-2" type="text" placeholder="Name" required>
                        </div>
                        <div class="form-group">
                            <label for="goal_desc">Description</label>
                            <textarea id="goal_desc" name="goal_desc" class="form-control" placeholder="Enter Description Here..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="goal_deadline">Deadline</label>
                            <input id="goal_deadline" name="goal_deadline" class="form-control mr-sm-2" type="date" placeholder="Deadline" required>
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

<?php if(!empty($incomplete_goals)): ?>
    <?php foreach($incomplete_goals as $goal) : ?>
    <div class="modal fade" id="modal<?= $goal['id']?>" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Goal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" class="form my-2 my-lg-0">
                    <input type="hidden" name="edit_id" value="<?= $goal['id'] ?>">
                    <div class="form-group">
                        <input name="edit_name" class="form-control mr-sm-2" type="text" placeholder="Note Title" required value="<?= $goal['name']?>">                       
                    </div>
                    <div class="form-group">
                        <textarea name="edit_desc" id="note-body" class="form-control" placeholder="Note Body" required><?= $goal['body']?></textarea>                  
                    </div>
                    <div class="form-group">
                        <input name="edit_deadline" class="form-control mr-sm-2" type="date" placeholder="Deadline" value="<?= $goal['deadline']?>" required>
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
