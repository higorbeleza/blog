<?php 

@include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)) {
    header("Location: admin_login.php");
}

if(isset($_POST['delete'])) {

    $select_image = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ?");
    $select_image->execute([$admin_id]);
    $fetch_image = $select_image->fetch(PDO::FETCH_ASSOC);
    if($fetch_image['image'] != '') {
        unlink('../uploaded_img/'.$fetch_image['image']);
    }

    $delete_posts = $conn->prepare("DELETE FROM `posts` WHERE admin_id = ? ");
    $delete_posts->execute([$admin_id]);

    $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE admin_id = ? ");
    $delete_comments->execute([$admin_id]);

    $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE admin_id = ? ");
    $delete_likes->execute([$admin_id]);

    $delete_admin = $conn->prepare("DELETE FROM `admin` WHERE id = ? ");
    $delete_admin->execute([$admin_id]);
    
    header('Location: ../admin/admin_login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin accounts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
    
<!-- HEADER START -->
<?php include '../components/admin_header.php'; ?>
<!-- HEADER END -->

<section class="accounts">
    <h1 class="heading">admins accounts</h1>

    <div class="box-container">

    <div class="box" style="order: -2;">
        <p>register new admin</p>
        <a href="admin_register.php" class="option-btn">register now</a>
    </div>
        <?php 
            $select_account = $conn->prepare("SELECT * FROM `admin` ");
            $select_account->execute();
            if($select_account->rowCount() > 0) {
                while($fetch_account = $select_account->fetch(PDO::FETCH_ASSOC)) {

                $count_admin_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ?");
                $count_admin_posts->execute([$fetch_account['id']]);
                $total_admin_posts = $count_admin_posts->rowCount();
        ?>
        <div class="box" style="<?php if($fetch_account['id'] == $admin_id) {echo 'order:-1'; } ?>">
            <p>id : <span><?= $fetch_account['id']; ?></span></p>
            <p>username : <span><?= $fetch_account['name']; ?></span></p>
            <p>total posts : <span><?= $total_admin_posts; ?></span></p>
                <?php 
                    if($fetch_account['id'] == $admin_id) {

                    
                ?>
                <div class="flex-btn">
                <a href="update_profile.php" class="option-btn">update</a>
                <form action="" method="POST">
                    <button type="submit" class="delete-btn" onclick="return confirm('delte the accoutn?');" name="delete">delete</button>
                </form>
                </div>
                <?php 
                    }
                ?>
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