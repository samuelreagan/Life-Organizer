<?php
session_start();

$_SESSION['page'] = "tasks";  

require_once('db_config.php');
require_once('helpers.php');

if(!isset($_SESSION['name'])) {
    header('Location:login.php');
} else {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if($_POST['task_body'] && $_POST['task_deadline'] && $_POST['goal']) {
            $task_body = htmlspecialchars(str_replace("'", "''",$_POST['task_body']));
            $task_deadline = htmlspecialchars($_POST['task_deadline']);
            $goal = htmlspecialchars($_POST['goal']);
            insert_task($conn, $_SESSION['id'], $task_body, $task_deadline, $goal);
        }
        if($_POST['task_id']) {
            $task_id = htmlspecialchars($_POST['task_id']);
            delete_task($conn, $task_id);
        }
        if($_POST['edit_id']) {
            $task_id = htmlspecialchars($_POST['edit_id']);
            $task_body = htmlspecialchars(str_replace("'", "''",$_POST['edit_body']));
            $task_deadline = htmlspecialchars($_POST['edit_deadline']);
            $goal = htmlspecialchars($_POST['edit_goal']);
            update_task($conn, $task_id, $task_body, $task_deadline, $goal);
        }
    }
    $level = get_user_level($conn, $_SESSION['id'])[0]['level'];
    $xp = get_user_xp($conn, $_SESSION['id'])[0]['xp'];
    $progress = calculate_progress($level, $xp); 
    $label = get_challenge_label($conn, $_SESSION['id'])[0]['name']; 
}

require_once('header.php');

$tasks  = get_all_tasks($conn, $_SESSION['id']);
$goals  = get_current_goals($conn, $_SESSION['id']);
?>
<div class="container col-lg-12 mt-4">
    <!-- Include Alerts-->
    <h1 class="text-center">Tasks</h1>
    <hr>
    <div class="row">
        <!-- Tasks Card -->
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header">
                    Tasks
                </div>
                <div class="card-body">
                    <?php if(!empty($tasks)) : ?>
                        <div class="table-responsive-lg">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Complete</th>
                                        <th>Deadline</th>
                                        <th class="text-center">Edit</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($tasks as $task) : ?>
                                        <tr>
                                            <td><?= $task['body']?></td>
                                            <td><?= ($task['is_complete'] == 't') ? "Yes" : "No"?></td>
                                            <td><?= $task['deadline']?></td>
                                            <td class="text-center pointer" data-toggle="modal" data-target="#modal<?= $task['id']?>"><i class="far fa-edit"></i></td>
                                            <td class="text-center">
                                                <form class="mb-0" action="tasks.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?')">
                                                    <input name="task_id" value="<?= $task['id'] ?>" type="hidden">
                                                    <button type="submit" class="btn"><i class="fa fa-times delete-btn"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <h5>No Tasks Created</h5>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Add a Task -->
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header">
                    Add a Task
                </div>
                <div class="card-body">
                    <form action="tasks.php" method="POST" class="form my-2 my-lg-0">
                        <div class="form-group">
                            <label for="task_body">Task</label>
                            <input id="task_body" name="task_body" class="form-control mr-sm-2" type="text" placeholder="Name">
                        </div>
                        <div class="form-group">
                            <label for="task_deadline">Deadline</label>
                            <input id="task_deadline" name="task_deadline" class="form-control mr-sm-2" type="date" placeholder="Deadline">
                        </div>
                        <?php if(!empty($goals)) : ?>
                            <div class="form-group">
                            <label for="goal_select">Goal</label>
                                <select class="form-control" name="goal" id="goal_select">
                                    <?php foreach($goals as $goal) : ?>
                                        <option value="NULL">None</option>
                                        <option value="<?= $goal['id'] ?>"><?= $goal['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="goal" value="NULL">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <button class="btn btn-green my-2 my-sm-0" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>                 
    </div>
</div>

<?php if(!empty($tasks)): ?>
    <?php foreach($tasks as $task) : ?>
    <div class="modal fade" id="modal<?= $task['id']?>" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" class="form my-2 my-lg-0">
                    <input type="hidden" name="edit_id" value="<?= $task['id'] ?>">
                    <div class="form-group">
                        <input name="edit_body" class="form-control mr-sm-2" type="text" placeholder="Task Title" required value="<?= $task['body']?>">                       
                    </div>

                    <?php if(!empty($goals)) : ?>
                        <div class="form-group">
                            <select class="form-control" name="edit_goal" id="edit_goal" required>
                                <?php foreach($goals as $goal) : ?>
                                    <option value="NULL">None</option>
                                    <option <?=($goal['id'] == $task['goal_id']) ? "selected" : "" ?> value="<?= $goal['id'] ?>"><?= $goal['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="goal" value="NULL">
                    <?php endif; ?>

                    <div class="form-group">
                        <input name="edit_deadline" class="form-control mr-sm-2" type="date" placeholder="Deadline" value="<?= $task['deadline']?>" required>
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