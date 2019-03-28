<?php

session_start();
require_once 'connection.php';
require_once 'string.php';


if(isset($_GET['id'])) {
    $comment_id = $_GET['id'];

    //check if user is logged in
    if(isset($_SESSION['user'])) {


        $stmt = $con->prepare("SELECT id, user_id, story_id from story_comments where id='$comment_id'");
        //execute the query
        $stmt->execute();
        // Bind the results
        $stmt->bind_result($id, $user_id, $story_id);

        while($stmt->fetch()) {
            $userId  = $user_id;
            $storyId = $story_id;
        }
        if($_SESSION['user']['id'] == $user_id) {
            $stmt = $con->prepare("DELETE FROM story_comments WHERE id = ?");
            $stmt->bind_param('i', $comment_id);
            $stmt->execute();
            $stmt->close();

            $_SESSION['message'] = 'Comment deleted successfully';
            $_SESSION['class']   = 'alert-success';
        } else {
            $_SESSION['message'] = 'You are not authorized to take that action';
            $_SESSION['class']   = 'alert-danger';
        }

        header("Location: /story.php?id=" . $storyId);

    } else {
        $_SESSION['message'] = 'You are not authorized to take that action';
        $_SESSION['class']   = 'alert-danger';
        header("Location: /");
    }
}