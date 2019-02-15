<?php 
/** Sign Up */
function create_user($conn, $name, $email, $password) {
    $user = get_user_id($conn, $email);
    if(empty($user)) {
        $sql = "INSERT INTO users(name, email, password, level, xp) values('$name', '$email', '$password', 1, 0);";
        $conn->exec($sql);
        $id = get_user_id($conn, $email)[0]['id'];
        $sql = "INSERT INTO earned(user_id, challenge_id) values('$id', 1);";
        $conn->exec($sql);
        $_SESSION['error'] = "";
        $_SESSION['success'] = "You're now signed up! Please log in!";
        return true;
    } else {
        $_SESSION['error'] = "There is already a user created with this email!";
        return false;
    }
}

function reset_alerts() {
    $_SESSION['error'] = "";
    $_SESSION['success'] = "";  
}

function get_user_id($conn, $email) {
    $sql = "SELECT id FROM users WHERE email='$email';";
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/** Dashboard Functions */
function get_user_level($conn, $user_id) {
    $sql = "SELECT level FROM users WHERE id='$user_id';";
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function get_user_xp($conn, $user_id) {
    $sql = "SELECT xp FROM users WHERE id='$user_id';";
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function get_challenge_label($conn, $user_id) {
    $sql = "SELECT * FROM challenges WHERE id IN(SELECT max(challenge_id) FROM earned GROUP BY user_id HAVING user_id = '$user_id');";
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function calculate_progress($level, $xp) {
    if($level == 1) {
        $percentage = $xp / 100;
    } else if($level == 2) {
        $percentage = $xp / 250;
    } else if($level == 3) {
        $percentage = $xp / 400;
    } else if($level == 4) {
        $percentage = $xp / 600;
    } else if($level == 5) {
        $percentage = $xp / 900;
    } else if($level == 6) {
        $percentage = $xp / 1400;
    } else if($level == 7) {
        $percentage = $xp / 2000;
    } else if($level == 8) {
        $percentage = $xp / 3000;
    } else if($level == 9) {
        $percentage = $xp / 4500;
    } else {
        $percentage = 0;
    } 

    return $percentage * 100;
}

function get_daily_habits($conn, $user_id) {
    $today = date('Y-m-d');
    $sql = "SELECT * FROM habits WHERE user_id='$user_id' AND id NOT IN(SELECT habit_id FROM habit_log WHERE date_completed = '$today');";
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function get_recent_notes($conn, $user_id) {
    $sql = "SELECT * FROM notes WHERE user_id='$user_id' ORDER BY date_created LIMIT 3;";
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC); 
}

function get_todays_tasks($conn, $user_id) {
    $sql = "SELECT * FROM tasks WHERE user_id='$user_id' AND is_complete='f' ORDER BY deadline;";
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function get_current_goals($conn, $user_id) {
    $sql = "SELECT * FROM goals WHERE user_id='$user_id' AND is_complete='f' ORDER BY deadline;";
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function log_habit($conn, $user_id, $habit_id) {
    if(empty(check_habit_logged($conn, $habit_id))) {
        $today = date('Y-m-d');
        $sql = "INSERT INTO habit_log(user_id, habit_id, date_completed) values('$user_id', '$habit_id', '$today')";
        $conn->exec($sql);
    }
}

/** Habit Functions */
function get_all_habits($conn, $user_id) {
    $sql = "SELECT * FROM habits WHERE user_id='$user_id'";
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function check_habit_logged($conn, $habit_id) {
    $today = date('Y-m-d');
    $sql = "SELECT * FROM habit_log WHERE habit_id='$habit_id' AND date_completed='$today'";
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function insert_habit($conn, $user_id, $habit_name) {
    $sql = "INSERT INTO habits(user_id, name) values('$user_id', '$habit_name')";
    $conn->exec($sql);
}

function update_habit($conn, $habit_id, $habit_name) {
    $sql = "UPDATE habits SET name='$habit_name' WHERE id='$habit_id';";
    $conn->exec($sql);
}

function delete_habit($conn, $habit_id) {
    $sql = "DELETE FROM habits WHERE id='$habit_id'"; 
    $conn->exec($sql);
}

function habit_streak($conn, $habit_id) {
    $today = new DateTime('now');
    $yesterday = new DateTime($today->format('Y-m-d'));
    date_sub($yesterday,date_interval_create_from_date_string("1 day"));

    $habit_log = get_habit_log($conn, $habit_id);
    $consecutive_days = 0;

    if(!empty($habit_log)) {
        $first_date = new DateTime($habit_log[0]['date_completed']);

        if($first_date->format('Y-m-d') == $today->format('Y-m-d')) {
            $consecutive_days = 1;
            if(count($habit_log) > 1) {
                $streak_broken = false;
                $i = 1;
                while($streak_broken == false || $i < count($habit_log)) {
                    $first = new DateTime($habit_log[$i - 1]['date_completed']);
                    $second = new DateTime($habit_log[$i]['date_completed']);
                    if(date_sub($first,date_interval_create_from_date_string("1 day")) == $second) {
                        $consecutive_days++;
                    } else {
                        $streak_broken = true;
                    }
                    $i++;
                }
            }   
        }        
    } 

    return $consecutive_days;
}

function get_habit_log($conn, $habit_id) {
    $sql = "SELECT * FROM habit_log WHERE habit_id='$habit_id' ORDER BY date_completed DESC;";
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/** Task Functions */
function get_all_tasks($conn, $user_id) {
    $sql = "SELECT * FROM tasks WHERE user_id='$user_id' ORDER BY is_complete, deadline;";
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function get_goal_tasks($conn, $goal_id) {
    $sql = "SELECT * FROM tasks WHERE goal_id='$goal_id' ORDER BY is_complete, deadline;";
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function mark_task_complete($conn, $task_id) {
    $today = date('Y-m-d');
    $sql = "UPDATE tasks SET is_complete='t', date_completed='$today' WHERE id='$task_id'"; 
    $conn->exec($sql);
}

function insert_task($conn, $user_id, $task_name, $task_deadline, $goal_id) {
    if($goal_id == "NULL") {
        $sql = "INSERT INTO tasks(user_id, body, priority, is_complete, deadline) values('$user_id', '$task_name', '1', 'f', '$task_deadline')";
    } else {
        $sql = "INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline) values('$user_id', '$goal_id', '$task_name', '1', 'f', '$task_deadline')";
    }
    
    $conn->exec($sql);
}

function update_task($conn, $task_id, $task_name, $task_deadline, $goal_id) {
    if($goal_id == "NULL") {
        $sql = "UPDATE tasks SET body='$task_name', deadline='$task_deadline', goal_id=NULL WHERE id='$task_id'";
    } else {
        $sql = "UPDATE tasks SET body='$task_name', deadline='$task_deadline', goal_id='$goal_id' WHERE id='$task_id'";
    }
    
    $conn->exec($sql);
}

function delete_task($conn, $task_id) {
    $sql = "DELETE FROM tasks WHERE id='$task_id'"; 
    $conn->exec($sql);
}

/** Goal Functions  */
function get_all_goals($conn, $user_id) {
    $sql = "SELECT * FROM goals WHERE user_id='$user_id' ORDER BY date_created;";
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function get_incomplete_goals($conn, $user_id) {
    $sql = "SELECT * FROM goals WHERE user_id='$user_id' AND is_complete='f' ORDER BY date_created;";
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function get_complete_goals($conn, $user_id) {
    $sql = "SELECT * FROM goals WHERE user_id='$user_id' AND is_complete='t' ORDER BY date_created;";
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function mark_goal_complete($conn, $goal_id) {
    $today = date('Y-m-d');
    $sql = "UPDATE goals SET is_complete='t', date_completed='$today' WHERE id='$goal_id'"; 
    $conn->exec($sql);
}
 
function mark_goal_incomplete($conn, $goal_id) {
    $today = date('Y-m-d');
    $sql = "UPDATE goals SET is_complete='f', date_completed=NULL WHERE id='$goal_id'"; 
    $conn->exec($sql);
}

function insert_goal($conn, $user_id, $goal_name, $goal_desc, $goal_deadline) {
    $sql = "INSERT INTO goals(user_id, name, body, is_complete, deadline) values('$user_id', '$goal_name', '$goal_desc', 'f', '$goal_deadline')";
    $conn->exec($sql);
}

function update_goal($conn, $goal_id, $goal_name, $goal_desc, $goal_deadline) {
    $sql = "UPDATE goals SET name='$goal_name', body='$goal_desc', deadline='$goal_deadline' WHERE id='$goal_id';";
    $conn->exec($sql);
}

function delete_goal($conn, $goal_id) {
    $sql = "DELETE FROM goals WHERE id='$goal_id'"; 
    $conn->exec($sql);
}

/** Notes Functions */
function get_all_notes($conn, $user_id) {
    $sql = "SELECT * FROM notes WHERE user_id='$user_id' ORDER BY date_created;";
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function insert_note($conn, $user_id, $note_title, $note_body) {
    $sql = "INSERT INTO notes(user_id, title, body) values('$user_id', '$note_title', '$note_body')";
    $conn->exec($sql);
}

function update_note($conn, $note_id, $note_title, $note_body) {
    $sql = "UPDATE notes SET title='$note_title', body='$note_body' WHERE id='$note_id';";
    $conn->exec($sql);
}

function delete_note($conn, $note_id) {
    $sql = "DELETE FROM notes WHERE id='$note_id'"; 
    $conn->exec($sql);
}

/** Friend Functions */
function get_friends($conn, $user_id) {
    $sql = "SELECT * FROM users WHERE id IN((SELECT friend_one FROM friends WHERE friend_two='$user_id') UNION (SELECT friend_two FROM friends WHERE friend_one='$user_id'));";  
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function remove_friend($conn, $friend_id, $user_id) {
    $sql = "DELETE FROM friends WHERE (friend_one='$friend_id' AND friend_two='$user_id') OR (friend_one='$user_id' AND friend_two='$friend_id')"; 
    $conn->exec($sql);
}

function add_friend($conn, $friend_id, $user_id) {
    $sql = "INSERT INTO friends(friend_one, friend_two) values('$friend_id', '$user_id')";
    $conn->exec($sql);
}

/** Challenges Functions */
function get_completed_challenges($conn, $user_id) {
    $sql = "SELECT * FROM challenges WHERE id IN(SELECT challenge_id FROM earned WHERE user_id = '$user_id');"; 
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function get_incomplete_challenges($conn, $user_id) {
    $sql = "SELECT * FROM challenges WHERE id NOT IN(SELECT challenge_id FROM earned WHERE user_id = '$user_id');"; 
    $statement = $conn->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

?>