<?php 

@include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)) {
    header("Location: admin_login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users accounts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
    
<!-- HEADER START -->
<?php include '../components/admin_header.php'; ?>
<!-- HEADER END -->

<section class="accounts">
    <h1 class="heading">users accounts</h1>

    <div class="box-container">
        <?php 
            $select_account = $conn->prepare("SELECT * FROM `users` ");
            $select_account->execute();
            if($select_account->rowCount() > 0) {
                while($fetch_account = $select_account->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <div class="box">
            <p>id : <span><?= $fetch_account['id']; ?></span></p>
            <p>username : <span><?= $fetch_account['name']; ?></span></p>
            <p>user email : <span><?= $fetch_account['email']; ?></span></p>
                
        </div>
        <?php 
            }
            }else {
                echo '<p class="empty">no accounts found!</p>';
            }
        ?>
    </div>
</section>

<!-- custom js file link -->
<script src="../js/admin_script.js"></script>
</body> 
</html>