<?php
session_start();

$_SESSION['page'] = "notes";

require_once('db_config.php');
require_once('helpers.php');

if(!isset($_SESSION['name'])) {
    header('Location:login.php');
} else {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if($_POST['note_title'] && $_POST['note_body']) {
            $note_title = htmlspecialchars(str_replace("'", "''",$_POST['note_title']));
            $note_body = htmlspecialchars(str_replace("'", "''",$_POST['note_body']));
            insert_note($conn, $_SESSION['id'], $note_title, $note_body);
        }
        if($_POST['note_id']) {
            $note_id = htmlspecialchars($_POST['note_id']);
            delete_note($conn, $note_id);
        }
        if($_POST['edit_id']) {
            $note_id = htmlspecialchars($_POST['edit_id']);
            $note_title = htmlspecialchars(str_replace("'", "''",$_POST['edit_title']));
            $note_body = htmlspecialchars(str_replace("'", "''",$_POST['edit_body']));
            update_note($conn, $note_id, $note_title, $note_body);
        }
    }
    $level = get_user_level($conn, $_SESSION['id'])[0]['level'];
    $xp = get_user_xp($conn, $_SESSION['id'])[0]['xp'];
    $progress = calculate_progress($level, $xp); 
    $label = get_challenge_label($conn, $_SESSION['id'])[0]['name']; 
}

require_once('header.php');

$notes  = get_all_notes($conn, $_SESSION['id']);
?>

<div class="container col-lg-12 mt-4">
    <!-- Include Alerts-->
    <h1 class="text-center">Notes </h1>
    <hr>
    <div class="row">
        <!-- Notes Card -->
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header">
                    Notes
                </div>
                <div class="card-body">
                    <?php if(!empty($notes)): ?>
                        <?php foreach($notes as $note): ?>
                            <div>
                                <h5><?= $note['title'] ?></h5>
                                <small class="text-bold"><i>Created on <?= $note['date_created'] ?></i></small>
                                <p class="card-text"><?= $note['body'] ?></p>
                                <button class="btn btn-purple text-white" data-toggle="modal" data-target="#modal<?= $note['id']?>">Edit</button>
                                <form class="d-inline" action="notes.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this note?')">
                                    <input name="note_id" value="<?= $note['id'] ?>" type="hidden">
                                    <button type="submit" class="btn btn-secondary">Delete</button>
                                </form>
                            </div>
                            <hr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <h5>No Notes Created</h5>
                    <?php endif;?>
                </div>
            </div>
        </div>
        
        <!-- Add a Note -->
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header">
                    Add a Note
                </div>
                <div class="card-body">
                    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" class="form my-2 my-lg-0">
                        <div class="form-group">
                            <label for="note_title">Title</label>
                            <input id="note_title" name="note_title" class="form-control mr-sm-2" type="text" placeholder="Note Title" required>                       
                        </div>
                        <div class="form-group">
                            <label for="note_body">Note</label>
                            <textarea id="note_body" name="note_body" id="note-body" class="form-control" placeholder="Enter Note Here..." required></textarea>                  
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

<?php if(!empty($notes)): ?>
    <?php foreach($notes as $note) : ?>
    <div class="modal fade" id="modal<?= $note['id']?>" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Note</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" class="form my-2 my-lg-0">
                    <input type="hidden" name="edit_id" value="<?= $note['id'] ?>">
                    <div class="form-group">
                        <input name="edit_title" class="form-control mr-sm-2" type="text" placeholder="Note Title" required value="<?= $note['title']?>">                       
                    </div>
                    <div class="form-group">
                        <textarea name="edit_body" id="note-body" class="form-control" placeholder="Note Body" required><?= $note['body']?></textarea>                  
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