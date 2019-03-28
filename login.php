<?php
session_start();
require_once 'connection.php';
require_once 'string.php';

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// only execute the following block of code if the request method is post
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if($_POST['token'] != $_SESSION['token']) {
        //stop execution if the posted token does not match the one in session
        die('Token mismatch');
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $check_if_user_exists = "SELECT * from users where username = '$username'";

    $result = mysqli_query($con, $check_if_user_exists);

    $result = mysqli_fetch_assoc($result);

    if($result) {
        $hash = $result['password'];

        if(password_verify($password, $hash)) {
            $_SESSION['user'] = ['id' => $result['id'], 'name' => $result['name'], 'email' => $result['email'], 'username' => $result['username']];
            header("Location: /");
        } else {
            $message = 'Invalid username or password';
            $class   = 'alert-danger';
        }
    } else {
        $message = 'Invalid username or password';
        $class   = 'alert-danger';
    }
}
require_once 'main.php';
?>

<div class="container mt-5">

    <h4>Login</h4>
    <hr/>

    <?php if(isset($message)) { ?>
        <div class="alert <?php echo $class; ?>"><?php echo $message; ?></div>
    <?php } ?>
    <form action="" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" class="form-control">
            <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-success" value="Login">
            Don't have an account ? <a href="/register.php">Register Here</a>
        </div>
    </form>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
</body>
</html>
