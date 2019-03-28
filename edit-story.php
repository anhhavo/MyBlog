<?php
session_start();
error_reporting(E_ALL);
require_once 'connection.php';
require_once 'string.php';

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $_SESSION['token'] = bin2hex(random_bytes(32));

    //check that user is logged in
    if(isset($_SESSION['user'])) {
        $post_id = $_GET['id'];
        $stmt    = $con->prepare("SELECT id, title, body, user_id, created_at, updated_at from stories where id='$post_id'");
        //execute the query
        $stmt->execute();
        // Bind the results
        $stmt->bind_result($id, $title, $body, $user_id, $created_at, $updated_at);

        $result = $stmt->fetch();
        //check that the post actually belongs to this user

        if($_SESSION['user']['id'] != $user_id) {
            $_SESSION['message'] = 'You are not authorized to take that action';
            $_SESSION['class']   = 'alert-danger';
            header("Location: /");
            exit;
        }
    }

}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if($_POST['token'] != $_SESSION['token']) {
        //stop execution if the posted token does not match the one in session
        die('Token mismatch');
    }

    $title = $_POST['title'];
    $body  = $_POST['body'];
    if(isset($title) && isset($body)) {
        $post_id = $_GET['id'];

        $query  = mysqli_query($con, "select id, user_id from stories where id='$post_id'");
        $result = mysqli_fetch_assoc($query);

        $user_id = $result['user_id'];
        if($_SESSION['user']['id'] != $user_id) {
            $_SESSION['message'] = 'You are not authorized to take that action';
            $_SESSION['class']   = 'alert-danger';
            header("Location: /");
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $time    = date('Y-m-d H:i:s');

        $update_query = $con->prepare("UPDATE stories SET title=?, body=? WHERE id=?");
        $update_query->bind_param('ssi', $title, $body, $post_id);
        $update_query->execute();

        $update_query->close();
        $title               = $new_title;
        $body                = $new_body;
        $_SESSION['message'] = 'Post edited successfully';
        $_SESSION['class']   = 'alert-success';
        header("Location: /edit-story.php?id=" . $post_id); // go to user account page
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
    <form action="/edit-story.php?id=<?php echo $post_id; ?>" method="POST">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" name="title" value="<?php echo $title; ?>">
            <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
        </div>


        <div class="form-group">
            <label for="body">Body</label>
            <textarea class="form-control" name="body"><?php echo $body; ?></textarea>
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-success" value="Submit">
        </div>
    </form>

</div>
