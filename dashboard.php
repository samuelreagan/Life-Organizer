<?php
session_start();

$_SESSION['page'] = "dashboard";

require_once('db_config.php');
require_once('helpers.php');

if(!isset($_SESSION['name'])) {
    $_SESSION['error'] = "You don't have access to this page!";
    header('Location:login.php');
} else {
    reset_alerts();
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(!empty($_POST['task_id'])) {
            $task_id = htmlspecialchars($_POST['task_id']);
            mark_task_complete($conn, $task_id);
        }
        if(!empty($_POST['goal_id'])) {
            $goal_id = htmlspecialchars($_POST['goal_id']);
            mark_goal_complete($conn, $goal_id);
        }
        if(!empty($_POST['habit_id'])) {
            $habit_id = htmlspecialchars($_POST['habit_id']);
            log_habit($conn, $_SESSION['id'], $habit_id);
        }
    }
    $level = get_user_level($conn, $_SESSION['id'])[0]['level'];
    $xp = get_user_xp($conn, $_SESSION['id'])[0]['xp'];
    $progress = calculate_progress($level, $xp); 
    $label = get_challenge_label($conn, $_SESSION['id'])[0]['name']; 
}

require_once('header.php');

$habits = get_daily_habits($conn, $_SESSION['id']);
$notes  = get_recent_notes($conn, $_SESSION['id']);
$tasks  = get_todays_tasks($conn, $_SESSION['id']);
$goals  = get_current_goals($conn, $_SESSION['id']);
?>

<div class="container col-lg-12 mt-4">
    <!-- Include Alerts-->
    <h1 class="text-center">Dashboard</h1>
    <h6 class="text-center"><?= date('m/d/Y');?> </h6>
    <hr>
    <!-- Row 1 -->
    <div class="card-deck">
        <!-- Tasks Card -->
        <div class="card">
            <div class="card-header">
                Today's Tasks
            </div>
            <div class="card-body">
                <?php if(!empty($tasks)) : ?>
                    <div class="table-responsive-lg">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tasks</th>
                                    <th scope="col">Deadline</th>
                                    <th class="text-center">Check Complete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($tasks as $task): ?>
                                    <tr>
                                        <td><?= $task['body'] ?></td>
                                        <td><?= $task['deadline'] ?></td>
                                        <td class="text-center">
                                            <form action="dashboard.php" method="POST" onsubmit="return confirm('Are you sure you want to mark this task complete?')">
                                                <input name="task_id" type="hidden" value="<?= $task['id']?>">
                                                <input type="checkbox" onchange="this.form.submit();"> 
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?> 
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                        <h5>Nothing to do!</h5>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <a href="tasks.php" class="btn btn-green text-dark">View/Edit Tasks</a>
            </div> 
        </div>

        <!-- Habits Card -->
        <div class="card">
            <div class="card-header">
                Log Your Completed Habits for Today
            </div>
            <div class="card-body">
                <?php if(!empty($habits)) : ?>
                    <div class="table-responsive-lg">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Habit</th>
                                    <th class="text-center" scope="col">Check Complete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($habits as $habit): ?>
                                    <tr>
                                        <td><?= $habit['name'] ?></td>
                                        <td class="text-center">
                                            <form action="dashboard.php" method="POST" onsubmit="return confirm('Are you sure you want to log this habit today?')">
                                                <input name="habit_id" type="hidden" value="<?= $habit['id']?>">
                                                <input type="checkbox" onchange="this.form.submit();"> 
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <h5>You don't have any habits to track today!</h5>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <a href="habits.php" class="btn btn-green text-dark">View/Edit Habits</a>
            </div>
        </div>
    </div> 
        
    <br>

    <!-- Row 2 -->
    <div class="card-deck">
        <!-- Goals Card -->
        <div class="card">
            <div class="card-header">
                Current Goals
            </div>
            <div class="card-body">
                <?php if(!empty($goals)) : ?>
                    <div class="table-responsive-lg">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Goal</th>
                                    <th scope="col">Deadline</th>
                                    <th class="text-center" scope="col">Check Complete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($goals as $goal): ?>
                                    <tr>
                                        <td><?= $goal['name'] ?></td>
                                        <td><?= $goal['deadline'] ?></td>
                                        <td class="text-center">
                                            <form action="dashboard.php" method="POST" onsubmit="return confirm('Are you sure you want to mark this goal complete?')">
                                                <input name="goal_id" type="hidden" value="<?= $goal['id']?>">
                                                <input type="checkbox" onchange="this.form.submit();"> 
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?> 
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <h5>No Active Goals</h5>
                <?php endif;   ?>
            </div>
            <div class="card-footer">
                <a href="goals.php" class="btn btn-green text-dark">View/Edit Goals</a>
            </div>
        </div>

        <!-- Note Card -->
        <div class="card">
            <div class="card-header">
                Notes <small>(Recently updated)</small>
            </div>
            <div class="card-body">
                <?php if(!empty($notes)): ?>
                    <?php foreach($notes as $note): ?>
                        <div>
                            <h5><?= $note['title'] ?></h5>
                            <small class="text-bold"><i>Created on <?= $note['date_created'] ?></i></small>
                            <p class="card-text"><?= $note['body'] ?></p>
                        </div>
                        <hr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <h5>No Notes Created</h5>
                <?php endif;?>
            </div>
            <div class="card-footer">
                <a href="notes.php" class="btn btn-green text-dark">View/Edit Notes</a>
            </div>
        </div>
    </div>
    <br>
</div>
<?php require_once('footer.php'); ?>