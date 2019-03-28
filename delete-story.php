<?php
session_start();
require_once 'connection.php';
require_once 'string.php';

if(isset($_GET['id'])) {
    $post_id = $_GET['id'];

    //check if user is logged in
    if(isset($_SESSION['user'])) {
        $stmt = $con->prepare("SELECT id, title, body, user_id, created_at, updated_at from posts where id='$post_id'");
        //execute the query
        $stmt->execute();
        // Bind the results
        $stmt->bind_result($id, $title, $body, $user_id, $created_at, $updated_at);

        while($stmt->fetch()) {
            $userId = $user_id;
        }
        if($_SESSION['user']['id'] == $user_id) {
            $stmt = $con->prepare("DELETE FROM stories WHERE id = ?");
            $stmt->bind_param('i', $post_id);
            $stmt->execute();
            $stmt->close();

            $_SESSION['message'] = 'Post deleted successfully';
            $_SESSION['class']   = 'alert-success';
        } else {
            $_SESSION['message'] = 'You are not authorized to take that action';
            $_SESSION['class']   = 'alert-danger';
        }

        header("Location: /");
    } else {
        $_SESSION['message'] = 'You are not authorized to take that action';
        $_SESSION['class']   = 'alert-danger';
        header("Location: /");
    }
}
require_once 'main.php';