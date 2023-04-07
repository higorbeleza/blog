<?php 

@include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}else{
    $user_id = '';
    header('Location: home.php');
}

if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);

    if(!empty($name)) {
        $update_name = $conn->prepare("UPDATE `users` SET name = ? WHERE id = ?");
        $update_name->execute([$name, $user_id]);
        $message[] = 'name updated!';
    }

    if(!empty($email)) {
        $select_email = $conn->prepare("SELECT email FROM `users` WHERE email = ?");
        $select_email->execute([$email]);
        if($select_email->rowCount() > 0) {
            $message[] = 'email already taken!';
        }else {
            $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE id = ?");
            $update_email->execute([$email, $user_id]);
            $message[] = 'email updated!';
        }
    }

    $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
    $select_prev_pass = $conn->prepare("SELECT password FROM `users` WHERE id = ? ");
    $select_prev_pass->execute([$user_id]);
    $fetch_prev_pass = $select_prev_pass->fetch(PDO::FETCH_ASSOC);

    $prev_pass = $fetch_prev_pass['password'];

    $old_pass = sha1($_POST['old_pass']);
    $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);

    $new_pass = sha1($_POST['new_pass']);
    $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
    
    $c_pass = sha1($_POST['c_pass']);
    $c_pass = filter_var($c_pass, FILTER_SANITIZE_STRING);

    if($old_pass != $empty_pass) {
        if($old_pass != $prev_pass) {
            $message[] = 'old password not matched!';
        }elseif($new_pass != $c_pass) {
            $message[] = 'confirm password not matched!';
        }else {
            if($new_pass != $empty_pass) {
                $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
                $update_pass->execute([$c_pass, $user_id]);
                $message[] = 'password updated!';
            }else {
                $message[] = 'please enter your new password!';
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
    <title>update profile</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
<?php include 'components/user_header.php'; ?>

<section class="form-container">
    <form action="" method="POST">
        <h3>update profile</h3>

        <input class="box" type="text" name="name" placeholder="<?= $fetch_profile['name'] ?>" require maxlength="50">
        <input class="box" type="email" name="email" placeholder="<?= $fetch_profile['email'] ?>" require maxlength="50">
        <input class="box" type="password" name="old_pass" placeholder="enter your old password" require maxlength="50">
        <input class="box" type="password" name="new_pass" placeholder="enter your new password" require maxlength="50">
        <input class="box" type="password" name="c_pass" placeholder="confimr your new password" require maxlength="50">
        <input type="submit" value="update" name="submit" class="btn">
    </form>
</section>

<?php include 'components/user_footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>