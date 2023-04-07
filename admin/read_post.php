<?php 

@include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)) {
    header("Location: admin_login.php");
}

if(!isset($_GET['post_id'])) {    
    header('Location: view_posts.php');
}else {
    $get_id = $_GET['post_id'];
}

if(isset($_POST['delete'])) {
    $delete_id = $_POST['post_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
    $select_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
    $select_image->execute([$delete_id]);
    $fetch_image = $select_image->fetch(PDO::FETCH_ASSOC);
    if($fetch_image['image'] != '') {
        unlink('../uploaded_img/'.$fetch_image['image']);
    }
    $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
    $delete_comments->execute([$delete_id]);

    $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE post_id = ?");
    $delete_likes->execute([$delete_id]);

    $delete_posts = $conn->prepare("DELETE FROM `posts` WHERE id = ?");
    $delete_posts->execute([$delete_id]);

    $message[] = 'post deleted sucessfull!';
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
    <title>Read page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
    
<!-- HEADER START -->
<?php include '../components/admin_header.php'; ?>
<!-- HEADER END -->

<section class="read-post">

<h1 class="heading">read post</h1>

<?php 
    $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE id = ? AND admin_id = ?");
    $select_posts->execute([$get_id, $admin_id]);
    if($select_posts->rowCount() > 0) {
        while($fetch_post = $select_posts->fetch(PDO::FETCH_ASSOC)) {
            $post_id = $fetch_post['id'];

            $count_post_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
            $count_post_comments->execute([$post_id]);
            $total_post_comments = $count_post_comments->rowCount();

            $count_post_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ?");
            $count_post_likes->execute([$post_id]);
            $total_post_likes = $count_post_likes->rowCount();
    ?>
    <form action="" method="POST" class="box">
        <div class="status" style="background: <?php if($fetch_post['status'] == 'active'){echo 'limegreen';}else{echo 'coral';} ?>;"><?= $fetch_post['status'] ?></div>
        <?php 
            if($fetch_post['image'] != '') {

            
        ?>
        <img src="../uploaded_img/<?= $fetch_post['image'] ?>" alt="" class="image">
        <?php
            }
        ?>

        <h1 class="post-title"><?= $fetch_post['title'] ?></h1>
        <div class="post-content"><?= $fetch_post['content'] ?></div>
        <div class="icons">
            <div><i class="fas fa-comment"></i><span><?= $total_post_comments; ?></span></div>
            <div><i class="fas fa-heart"></i><span><?= $total_post_likes; ?></span></div>
        </div>
        <div class="flex-btn">
            <a href="edit_post.php?post_id=<?= $post_id; ?>" class="option-btn">edit</a>
            <button type="submit" name="delete" onclick="return confirm('delete this post?')" class="delete-btn">delete</button>
        </div>
        <div class="post-category"><i class="fas fa-tag"></i><span><?= $fetch_post['category']; ?></span></div>
    </form>
    <?php 
        }
    }else {
        echo '<p class="empty">no posts added yet!</p>';
    }
    ?>
</section>

<div class="comments">
    <p class="comment-title">post comments</p>
    <div class="box-container">
        <?php
            $select_commet = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
            $select_commet->execute([$get_id]);
            if($select_commet->rowCount() > 0) {
                while($fetch_commets = $select_commet->fetch(PDO::FETCH_ASSOC)) {

                
        ?>
        <div class="box">
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