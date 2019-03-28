<?php

session_start();
require_once 'connection.php';
require_once 'string.php';
error_reporting(E_ALL);

if(isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    //check if user is logged in
    if(isset($_SESSION['user'])) {

        $comment = $_POST['comment'];
        $user_id = $_SESSION['user']['id'];

        $time         = date('Y-m-d H:i:s');
        $insert_query = "INSERT INTO story_comments (`comment`, `story_id`, `user_id`, `created_at`, `updated_at`) VALUES ('$comment', '$post_id', '$user_id', '$time', '$time')";

        if($con->query($insert_query) === TRUE) {
            $_SESSION['message'] = 'Commented successfully';
            $_SESSION['class']   = 'alert-success';
        } else {
            $_SESSION['message'] = $con->error;
            $_SESSION['class']   = 'alert-danger';
        }
    }

    header("Location: /story.php?id=" . $post_id);
}
