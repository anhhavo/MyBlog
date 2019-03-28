<?php
session_start();
require_once 'connection.php';
require_once 'string.php';

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if($_POST['token'] != $_SESSION['token']) {
        //stop execution if the posted token does not match the one in session
        die('Token mismatch');
    }

    $title = $_POST['title'];
    $body  = $_POST['body'];

    if(isset($title) && isset($body)) {
        $user_id = $_SESSION['user']['id'];
        $stmt    = $con->prepare("insert into stories (title,body, user_id, created_at, updated_at) values (?,?,?,?,?)");
        if(!$stmt) {
            printf("Query Prep Failed: %s\n", $con->error);
            exit;
        } else {
            $time = date('Y-m-d H:i:s');
            $stmt->bind_param('sssss', $title, $body, $user_id, $time, $time);
            $stmt->execute();

            $stmt->close();
            header("Location: /"); // go to user account page
            exit;
        }
    }
}
require_once 'main.php';

?>

<div class="container mt-5">

    <form action="/new-story.php" method="POST">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" name="title">
            <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
        </div>


        <div class="form-group">
            <label for="body">Body</label>
            <textarea class="form-control" name="body"></textarea>
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-success" value="Submit">
        </div>
    </form>
</div>
