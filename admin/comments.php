<?php 

@include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)) {
    header("Location: admin_login.php");
}

if(isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment_id'];
    $comment_id = filter_var($comment_id, FILTER_SANITIZE_STRING);
    $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ? ");
    $delete_comment->execute([$comment_id]);
    $message[] = 'commet deleted!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users comments</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
    
<!-- HEADER START -->
<?php include '../components/admin_header.php'; ?>
<!-- HEADER END -->

<div class="comments">
    <h1 class="heading">all comments</h1>

    <p class="comment-title">post comments</p>
    <div class="box-container">
        <?php
            $select_commet = $conn->prepare("SELECT * FROM `comments` WHERE admin_id = ?");
            $select_commet->execute([$admin_id]);
            if($select_commet->rowCount() > 0) {
                while($fetch_commets = $select_commet->fetch(PDO::FETCH_ASSOC)) {

                
        ?>
        <div class="box">
            <?php 
                $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE id = ? ");
                $select_posts->execute([$fetch_commets['post_id']]);
                while($fetch_post = $select_posts->fetch(PDO::FETCH_ASSOC) ) {

                
            ?>
            <div class="post-title"><span>from: </span><p><?= $fetch_post['title'] ?></p><a href="read_post.php?post_id=<?= $fetch_post['id'] ?>">read post</a></div>
            <?php 
                }
            ?>
            <div class="user">
                <i class="fas fa-user"></i>
                <div class="user-info">
                    <span><?= $fetch_commets['user_name']; ?></span>
                    <div><?= $fetch_commets['date']; ?></div>
                </div>
            </div>
            <div class="text"><?= $fetch_commets['comment']; ?></div>
            <form action="" class="icons" method="POST">
                <input type="hidden" name="comment_id" value="<?= $fetch_commets['id']; ?>">
                <button type="submit" name="delete_comment" onclick="return confirm('delete this comment?')" class="inline-delete-btn">delete comment</button>
            </form>
        </div>  
        <?php 
            }
            }else {
                echo '<p class="empty">no comments added yet!</p>';
            }
        ?>
    </div>
</div>

<!-- custom js file link -->
<script src="../js/admin_script.js"></script>
</body> 
</html>