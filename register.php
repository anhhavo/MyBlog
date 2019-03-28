<?php
session_start();
require_once 'connection.php';
require_once 'string.php';

/**
 * CSRF token taken from stackoverflow
 * https://stackoverflow.com/questions/6287903/how-to-properly-add-csrf-token-using-php
 */

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// only execute the following block of code if the request method is post
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if($_POST['token'] != $_SESSION['token']) {
        //stop execution if the posted token does not match the one in session
        die('Token mismatch');
    }

    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    /**
     * password hashing method taken from official php website
     * http://php.net/manual/en/function.password-hash.php
     */
    $password = password_hash($password, PASSWORD_DEFAULT);

    //check that this user is not already registered

    $check_if_user_exists = "SELECT * from users where email = '$email' or username='$username'";

    $result = mysqli_query($con, $check_if_user_exists);

    $rows = mysqli_num_rows($result);

    if($rows < 1) {

        $insert_query = "INSERT INTO users (`name`, `email`, `username`, `password`) VALUES ('$name', '$email', '$username', '$password')";

        if($con->query($insert_query) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $insert_query . "<br>" . $con->error;
        }

        $message = 'User created successfully!';
        $class   = 'alert-success';
        $con->close();
    } else {
        $message = 'User already registered, please login';
        $class   = 'alert-danger';
    }

}
require_once 'main.php';
?>

<div class="container mt-5">

    <h4>Register</h4>
    <hr/>
    <?php if(isset($message)) { ?>
        <div class="alert <?php echo $class; ?>"><?php echo $message; ?></div>
    <?php } ?>
    <form action="" method="POST">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control">
            <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control">
        </div>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" class="form-control">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-success" value="Register">
            Already have an account ? <a href="/login.php">Login Here</a>
        </div>
    </form>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
</body>
</html>
