<?php
session_start();
error_reporting(E_ALL);
require_once 'connection.php';
require_once 'string.php';

if(isset($_GET['query'])) {
    $query     = $_GET['query'];
    $sql_query = "select *, users.name from stories join users on stories.user_id = users.id where title like '%$query%'";

    $result = $con->query($sql_query);

}
require_once 'main.php';
?>

<div class="container mt-5">
    <?php
    if(isset($result)) { ?>
        <h4>Search Results for <?php echo $query; ?></h4>
        <?php
        while($row = $result->fetch_assoc()) { ?>
            <div class="alert alert-secondary mt-3">
                <a href="/story.php?id=<?php echo $row['id']; ?>"><h4><?php echo $row['title']; ?></h4></a>
                By <?php echo $row['name']; ?> at <?php echo $row['created_at']; ?>
            </div>
        <?php }
    } ?>
</div>