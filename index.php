<?php
if(!isset($_SERVER['https'])){
    header('location:http://' . $_SERVER['HTTP_HOST'] . '/wideMarkets');
}
require_once 'app/helpers.php';
my_session_start('widemarkets');
//set errors to empty on first load.
$error = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'number' => '',
    'country' => '',
    'address' => '',
];

if (isset($_POST['submit'])) {
    if (isset($_POST['token']) && isset($_SESSION['token']) && $_POST['token'] == $_SESSION['token']) {
        //clean from xss attacks
        $first_name = trim(filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING));
        $last_name = trim(filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING));
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
        $number = trim(filter_input(INPUT_POST, 'number', FILTER_SANITIZE_STRING));
        $country = trim(filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING));
        $address = trim(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING));
        $ip = trim(filter_input(INPUT_POST, 'ip', FILTER_SANITIZE_STRING));
        $country_from_ip = trim(filter_input(INPUT_POST, 'country_from_ip', FILTER_SANITIZE_STRING));

        $valid = true;
        //server side validation
        if (!$first_name) {
            $error['first_name'] = '* First Name required';
            $valid = false;
        }
        if (!$last_name) {
            $error['last_name'] = '* Last Name required';
            $valid = false;
        }
        if (!$email) {
            $error['email'] = '* Enter valid email';
            $valid = false;
        } else {
            //check if email already exists
            $link = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PW, MYSQL_DB);
            $email = mysqli_real_escape_string($link, $email);
            $query = "SELECT email FROM users WHERE email = '$email'";
            $result = mysqli_query($link, $query);
            if ($result && mysqli_num_rows($result) == 1) {
                $error['email'] = '* email already exists';
                $valid = false;
            }
        }
        if (!$number || !preg_match('/^[\d-\s]{9,15}$/', $number)) {
            $error['number'] = '* A valid phone number is required';
            $valid = false;
        }
        if ($valid) {
            //clean from sql injection
            $first_name = mysqli_real_escape_string($link, $first_name);
            $last_name = mysqli_real_escape_string($link, $last_name);
            $number = mysqli_real_escape_string($link, $number);
            $country = mysqli_real_escape_string($link, $country);
            $address = mysqli_real_escape_string($link, $address);
            $ip = mysqli_real_escape_string($link, $ip);
            $country_from_ip = mysqli_real_escape_string($link, $country_from_ip);

            mysqli_query($link, 'SET NAMES utf8');
            $query = "INSERT INTO users VALUES(DEFAULT,'$first_name','$last_name','$email','$number','$country','$address'"
                    . ",'$ip','$country_from_ip',CURDATE())";
            $result = mysqli_query($link, $query);
            if ($result && mysqli_affected_rows($link) == 1) {
                header('location:thanks.php');
                exit;
            }
        }
    }
}
$token = csrf_token();
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Wide markets</title>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <h1> Welcome to Wide markets</h1>
        <form action="" method="POST" id="form" novalidate >
            <input type="hidden" name="token" value="<?= $token; ?>">
            <label for="first_name">First Name:</label>
            <br>
            <input type="text" id="first_name" name="first_name" value="<?= old('first_name'); ?>">
            <br>
            <span class="error" id="first_name-error"><?= $error['first_name']; ?></span>
            <br>
            <label for="last_name">Last Name:</label>
            <br>
            <input type="text" id="last_name" name="last_name" value="<?= old('last_name'); ?>">
            <br>
            <span class="error" id="last_name-error"><?= $error['last_name']; ?></span>
            <br>
            <label for="email">Email:</label>
            <br>
            <input type="email" id="email" name="email" value="<?= old('email'); ?>" >
            <br>
            <span class="error" id="email-error"><?= $error['email']; ?></span>
            <br>
            <label for="number">Phone Number:</label>
            <br>
            <input type="tel" id="number" name="number" value="<?= old('number'); ?>">
            <br>
            <span class="error" id="number-error"><?= $error['number']; ?></span>
            <br>
            <label for="country">Country:</label>
            <br>
            <input type="text" id="country" name="country" value="<?= old('country'); ?>">
            <br>
            <span class="error" id="country-error"><?= $error['country']; ?></span>
            <br>
            <label for="address">Address:</label>
            <br>
            <input type="text" id="address" name="address" value="<?= old('address'); ?>">
            <br>
            <span class="error" id="address-error"><?= $error['address']; ?></span>
            <br>
            <input type="hidden" name="ip" id="ip">
            <input type="hidden" name="country_from_ip" id="country_from_ip">
            <input type="submit" value="submit" name="submit">
        </form>
        <script>
            var request = new XMLHttpRequest();
            request.open('Get', '//ip-api.com/json');
            request.send();
            request.onreadystatechange = function () {
                if (request.readyState === 4) {
                    if (request.status === 200) {
                        var result = JSON.parse(request.responseText);
                        document.getElementById('ip').value = result.query;
                        document.getElementById('country_from_ip').value = result.country;
                    }
                }
            };
            var form = document.getElementById('form');
            form.addEventListener("submit", function (e) {
                var errors = document.querySelectorAll('.error');
                for (let i = 0; i < errors.length; i++) {
                    errors[i].innerHTML = '';
                }
                //between 9 to 15 characters containing digits whitespace or dashes.
                var numberRegex = /^[\d-\s]{9,15}$/;

                var emailRegex = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i;
                //at least 2 characters
                var first_nameRegex = /^[a-zא-ת]+[a-zא-ת]+(\s[a-zא-ת]+)*$/i;
                var last_nameRegex = /^[a-zא-ת]+[a-zא-ת]+(\s[a-zא-ת]+)*$/i;
                //not necessary. just to make the loop run smoothly
                var countryRegex = /^.+$/;
                var addressRegex = /^.+$/;

                var valid = true;
                var fields = form.getElementsByTagName('input');

                //dont loop over the hidden values
                for (let i = 1; i < fields.length - 3; i++) {

                    var errorField = document.getElementById(fields[i].name + '-error');
                    var inputField = document.getElementById(fields[i].name);
                    var regex = fields[i].name + 'Regex';
                    if (!fields[i].value.trim()) {
                        errorField.innerHTML = '* ' + fields[i].name + ' is required';
                        valid = false;
                    } else if (!eval(regex).test(fields[i].value)) {
                        console.log('jfkd');
                        errorField.innerHTML = '* A valid ' + fields[i].name + ' is required';
                        valid = false;
                    }

                }

                if (!valid) {
                    e.preventDefault();
                }
            });
            


        </script>
    </body>
</html>