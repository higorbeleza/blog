<?php 

@include '../components/connect.php';
session_start();

if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $pass = md5($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);

    $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ? AND password = ?");
    $select_admin->execute([$name, $pass]);

    if($select_admin->rowCount() > 0) {
        $fetch_admin_id = $select_admin->fetch(PDO::FETCH_ASSOC);
        $_SESSION['admin_id'] = $fetch_admin_id['id'];
        header('location:dashboard.php');
    }else {
        $message[] = 'incorrect username or password!!';
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body style="padding-left: 0">

<?php 
if(isset($message)) {
    foreach($message as $message) {
        echo '
        <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>

<section class="form-container">
    <form action="" method="POST">
        <h3>login now</h3>
        <input type="text" require class="box" placeholder="enter your username" maxlength="20" name="name" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" require class="box" placeholder="enter your password" maxlength="20" name="pass" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="submit" name="submit" class="btn" value="login now">
    </form>
</section>

<!-- custom js file link -->
<script src="../js/admin_script.js"></script>
</body> 
</html>