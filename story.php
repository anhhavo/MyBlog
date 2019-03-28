<?php
session_start();
error_reporting(E_ALL);
require_once 'connection.php';
require_once 'string.php';

if(isset($_GET['id'])) {
    $post_id = $_GET['id'];

    $stmt = $con->prepare("SELECT id, title, body, user_id, created_at, updated_at from stories where id='$post_id'");
    //execute the query
    $stmt->execute();
    // Bind the results
    $stmt->bind_result($id, $title, $body, $user_id, $created_at, $updated_at);
}
require_once 'main.php';
?>

<div class="container mt-5">
    <h4><?php echo $welcome_text; ?></h4>
    <?php echo $welcome_subline; ?>

    <hr/>
    <div class="col-12 mt-4 mb-5">
        <?php
        while($stmt->fetch()) { ?>
            <h3><?php echo $title; ?></h3>
            <hr/>
            <p>
                <?php echo $body; ?>
            </p>
            <?php if($user_id == $_SESSION['user']['id']) { ?>
                <a href="/edit-story.php?id=<?php echo $id; ?>" class="btn btn-warning">Edit Story</a>
                <a href="/delete-story.php?id=<?php echo $id; ?>" class="btn btn-danger">Delete Story</a>
            <?php } ?>
        <?php } ?>
    </div>


    <hr/>
    <h4>Comments</h4>
    <?php
    $query = "SELECT story_comments.id, story_comments.comment, story_comments.user_id, users.name from story_comments join users on story_comments.user_id = users.id where story_id='$id'";


    if($result = mysqli_query($con, $query)) {
        // Fetch one and one row
        while($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="alert alert-secondary">
                <?php echo $row['comment']; ?>
                <small class="text-muted">
                    by <?php echo $row['name']; ?>
                    <?php if(isset($_SESSION['user']) && $_SESSION['user']['id'] == $row['user_id']) { ?>
                        <a href="/delete-comment.php?id=<?php echo $row['id']; ?>"><i class="fa fa-times"></i>
                            Delete</a>
                        <a href="/edit-comment.php?id=<?php echo $row['id']; ?>"><i class="fa fa-pencil"></i> Edit</a>
                    <?php } ?>
                </small>
            </div>
        <?php }
        // Free result set
        mysqli_free_result($result);
    } ?>
    <hr/>

    <div class="col-12 mt-5">
        <?php if(isset($_SESSION['user'])) { ?>
            <form action="/post-comment.php?post_id=<?php echo $post_id; ?>" method="POST">
                <div class="form-group">
                    <label for="comment">Comment</label>
                    <input type="text" class="form-control" name="comment">
                </div>

                <div class="form-group">
                    <input type="submit" class="btn btn-success" value="Save">
                </div>
            </form>
        <?php } ?>
    </div>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
</body>
</html>

