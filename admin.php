<?php
require_once 'app/helpers.php';
my_session_start('widemarkets');
$error = [
    'password' => ''
    ];
if (isset($_POST['submit'])) {
    if (isset($_POST['token']) && isset($_SESSION['token']) && $_POST['token'] == $_SESSION['token']) {
        $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
        //no point in encryption here but spec asked for encrytion and not to save on db.
        $pw_encryted = password_hash('widemarkets',PASSWORD_BCRYPT);
        if(password_verify($password, $pw_encryted)){
            $_SESSION['admin'] = true;
            header('location:dashboard.php');
            exit;
        }
        else{
            $error['password'] = 'Wrong password';
        }
    }
}
$token = csrf_token();
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin</title>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <h1> Admin login</h1>
        <form action="" method="POST">
            <input type="hidden" name="token" value="<?= $token; ?>">
            <label for="password">Enter password: </label>
            <input type="password" name="password" id="password">
            <span class="error" ><?= $error['password']; ?></span>
            <input type="submit" name="submit">
        </form>
    </body>
</html>

