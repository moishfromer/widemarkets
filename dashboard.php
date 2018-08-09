<?php
require_once 'app/helpers.php';
my_session_start('widemarkets');

if(!isset($_SESSION['admin'])){
    header('location:admin.php');
    exit;
}

$users = [];
$link = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PW, MYSQL_DB);
mysqli_query($link, "SET NAMES utf8");
$query = "SELECT * FROM users";
$result = mysqli_query($link, $query);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Dashboard</title>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <h1> Welcome to dashboard</h1>
         <?php if ($users): ?>
        <table> 
            <thead>
                <tr> 
                    <th>First name</th>
                    <th>Last name</th>
                    <th>Email </th>
                    <th>Phone number</th>
                    <th>country</th>
                    <th>Address</th>
                    <th>IP</th>
                    <th>Date</th>
                </tr>
            </thead>
            <?php foreach ($users as $user): ?>
            <tr> 
                <td> <?=$user['first_name']?></td>
                <td> <?=$user['last_name']?></td>
                <td> <?=$user['email']?></td>
                <td> <?=$user['number']?></td>
                <td> <?=$user['country']?></td>
                <td> <?=$user['address']?></td>
                <td> <?=$user['ip']?></td>
                <td> <?=$user['date']?></td>
            <?php endforeach; ?>
            </tr>
        </table>
    <?php endif; ?>
    </body>
</html>

