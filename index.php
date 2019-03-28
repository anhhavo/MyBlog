<?php
session_start();
require_once 'connection.php';
require_once 'string.php';

$get_posts = "Select stories.id, stories.title, stories.body, stories.created_at, stories.user_id, users.name from stories join users on stories.user_id = users.id limit 10";

$result = mysqli_query($con, $get_posts);
require_once 'main.php';
?>

<div class="container mt-5">
    <h4><?php echo $welcome_text; ?></h4>
    <?php echo $welcome_subline; ?>

    <?php
    if(isset($_SESSION['message'])) { ?>
        <div class="alert <?php echo $_SESSION['class']; ?>"><?php echo $_SESSION['message']; ?></div>
    <?php }
    unset($_SESSION['message']);
    unset($_SESSION['class']);
    while($row = mysqli_fetch_assoc($result)) { ?>
        <div class="alert alert-secondary mt-3">
            <a href="/story.php?id=<?php echo $row['id']; ?>"><h4><?php echo $row['title']; ?></h4></a>
            By <?php echo $row['name']; ?> at <?php echo $row['created_at']; ?>
        </div>
    <?php } ?>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
</body>
</html>
