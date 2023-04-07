<?php 

@include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)) {
    header("Location: admin_login.php");
}

if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $pass = md5($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);
    $cpass = md5($_POST['cpass']);
    $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

    $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
    $select_admin->execute([$name]);

    if($select_admin->rowCount() > 0) {
        $message[] = 'username already exist';
    }else {
        if($pass != $cpass) {
            $message[] = 'confirm password not matched!';
        } else {
            $insert_admin = $conn->prepare("INSERT INTO `admin` (name, password) VALUES(?,?)");
            $insert_admin->execute([$name, $cpass]);
            $message[] = 'new admin registered!';
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
    <title>Register page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
    
<!-- HEADER START -->
<?php include '../components/admin_header.php'; ?>
<!-- HEADER END -->

<section class="form-container">
    <form action="" method="POST">
        <h3>register now</h3>
        <input type="text" require class="box" placeholder="enter your username" maxlength="20" name="name" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" require class="box" placeholder="enter your password" maxlength="20" name="pass" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" require class="box" placeholder="confirm your password" maxlength="20" name="cpass" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="submit" name="submit" class="btn" value="register now">
    </form>
</section>

<!-- custom js file link -->
<script src="../js/admin_script.js"></script>
</body> 
</html>