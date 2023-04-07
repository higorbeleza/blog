<?php 

@include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}else{
    $user_id = '';
}

if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);

    $password = sha1($_POST['password']);
    $password = filter_var($password, FILTER_SANITIZE_STRING);

    $cpass = sha1($_POST['cpassword']);
    $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

    $select_users = $conn->prepare("SELECT * FROM `users` WHERE email = ? ");
    $select_users->execute([$email]);

    if($select_users->rowCount() > 0) {
        $message[] = 'email already taken!';
    }else {
        if($password != $cpass) {
            $message[] = 'confirm password not matched!';
        }else {
            $insert_user = $conn->prepare("INSERT INTO `users` (name, email, password) VALUES(?,?,?) ");
            $insert_user->execute([$name, $email, $cpass]);
            $fetch_users = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
            $fetch_users->execute([$email, $cpass]);
            $row = $fetch_users->fetch(PDO::FETCH_ASSOC);
            
            if($fetch_users->rowCount() > 0) {
                $_SESSION['user_id'] = $row['id'];
                header('Location: home.php');
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
<?php include 'components/user_header.php'; ?>

<section class="form-container">
    <form action="" method="POST">
        <h3>register now</h3>

        <input class="box" type="text" name="name" placeholder="enter your name" require maxlength="50">
        <input class="box" type="email" name="email" placeholder="enter your email" require maxlength="50">
        <input class="box" type="password" name="password" placeholder="enter your password" require maxlength="50">
        <input class="box" type="password" name="cpassword" placeholder="confirm your password" require maxlength="50">
        <input type="submit" value="register now" name="submit" class="btn">
    </form>
</section>

<?php include 'components/user_footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>