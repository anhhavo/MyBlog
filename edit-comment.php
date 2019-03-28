<?php
session_start();
error_reporting(E_ALL);
require_once 'connection.php';
require_once 'string.php';

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $_SESSION['token'] = bin2hex(random_bytes(32));

    //check that user is logged in
    if(isset($_SESSION['user'])) {
        $comment_id = $_GET['id'];
        $stmt       = $con->prepare("SELECT id, comment, story_id, user_id, created_at, updated_at from story_comments where id='$comment_id'");
        //execute the query
        $stmt->execute();
        // Bind the results
        $stmt->bind_result($id, $comment, $story_id, $user_id, $created_at, $updated_at);


        while($row = $stmt->fetch()) {
            $commentId   = $id;
            $commentBody = $comment;
            $storyId     = $story_id;
            $userId      = $user_id;
            $createdAt   = $created_at;
            $updatedAt   = $updated_at;
        }
        //check that the post actually belongs to this user

        if($_SESSION['user']['id'] != $userId) {
            $_SESSION['message'] = 'You are not authorized to take that action';
            $_SESSION['class']   = 'alert-danger';
            header("Location: /");
            exit;
        }
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if($_POST['token'] != $_SESSION['token']) {
        //stop execution if the posted token does not match the one in session
        die('Token mismatch');
    }

    $comment = $_POST['comment'];
    if(isset($comment)) {
        $comment_id = $_GET['id'];

        $query  = mysqli_query($con, "select id, story_id, user_id from story_comments where id='$comment_id'");
        $result = mysqli_fetch_assoc($query);

        $user_id  = $result['user_id'];
        $story_id = $result['story_id'];

        if($_SESSION['user']['id'] != $user_id) {
            $_SESSION['message'] = 'You are not authorized to take that action';
            $_SESSION['class']   = 'alert-danger';
            header("Location: /");
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $time    = date('Y-m-d H:i:s');

        $update_query = $con->prepare("UPDATE story_comments SET comment=? WHERE id=?");
        $update_query->bind_param('si', $comment, $comment_id);
        $update_query->execute();

        $update_query->close();
        /*$title               = $new_title;
        $body                = $new_body;*/
        $_SESSION['message'] = 'Post edited successfully';
        $_SESSION['class']   = 'alert-success';
        header("Location: /story.php?id=" . $story_id); // go to user account page
    }
}

require_once 'main.php';

?>

<div class="container mt-5">

    <?php if(isset($_SESSION['message'])) { ?>
        <div class="alert <?php echo $_SESSION['class']; ?>"><?php echo $_SESSION['message']; ?></div>
        <?php
        unset($_SESSION['message']);
        unset($_SESSION['class']);
    } ?>
    <form action="/edit-comment.php?id=<?php echo $comment_id; ?>" method="POST">
        <div class="form-group">
            <label for="title">Comment</label>
            <input type="text" class="form-control" name="comment" value="<?php echo $commentBody; ?>">
            <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-success" value="Save">
        </div>
    </form>

</div>
