<?php 

@include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}else{
    $user_id = '';
}

if(isset($_GET['post_id'])) {
    $get_id = $_GET['post_id'];
}else {
    $get_id = '';
}

@include 'components/like_post.php';

if(isset($_POST['add_comment'])) {
    $admin_id = $_POST['admin_id'];
    $admin_id = filter_var($admin_id, FILTER_SANITIZE_STRING);

    $user_name = $_POST['user_name'];
    $user_name = filter_var($user_name, FILTER_SANITIZE_STRING);

    $comment = $_POST['comment'];
    $comment = filter_var($comment, FILTER_SANITIZE_STRING);

    $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ? 
    AND admin_id = ? 
    AND user_id = ? 
    AND user_name = ? 
    AND comment = ? ");
    $verify_comment->execute([$get_id, $admin_id, $user_id, $user_name, $comment]);

    if($verify_comment->rowCount() > 0) {
        $message[] = 'comment already added!';
    }else {
        $insert_comment = $conn->prepare("INSERT INTO `comments`(post_id, admin_id, user_id, user_name, comment) VALUES(?,?,?,?,?) ");
        $insert_comment->execute([$get_id, $admin_id, $user_id, $user_name, $comment]);
        $message[] = 'new comment added!';
    }

}

if(isset($_POST['delete_comment'])) {
    $delete_comment_id = $_POST['comment_id'];
    $delete_comment_id = filter_var($delete_comment_id, FILTER_SANITIZE_STRING);
    $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ? ");
    $delete_comment->execute([$delete_comment_id]);
    $message[] = 'comment deleted sucessfully!';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>read post</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
<?php include 'components/user_header.php'; ?>

<section class="read-post">
    <h1 class="heading">read post</h1>

        <?php
        

            
            $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE id = ? AND status = ?");
            $select_posts->execute([$get_id, 'active']);
            if($select_posts->rowCount() > 0) {
                while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
                    $post_id = $fetch_posts['id'];
                    
                    $count_post_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
                    $count_post_comments->execute([$post_id]);
                    $total_post_comments = $count_post_comments->rowCount();

                    $count_post_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ?");
                    $count_post_likes->execute([$post_id]);
                    $total_post_likes = $count_post_likes->rowCount();

                    $confirm_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND post_id = ?");
                    $confirm_likes->execute([$user_id, $post_id]);
        ?>
        <form action="" method="POST" class="box">
            <input type="hidden" name="post_id" value="<?= $post_id; ?>">
            <input type="hidden" name="admin_id" value="<?= $fetch_posts['admin_id']; ?>">

            <div class="admin">
                <i class="fas fa-user"></i>
                <div class="admin-info">
                    <a href="author_posts.php?author=<?= $fetch_posts['name']; ?>"><?= $fetch_posts['name']; ?></a>
                    <div><?= $fetch_posts['date']; ?></div>
                </div>
            </div>
            <div class="title"><?= $fetch_posts['title'];?></div>
            <?php
                if($fetch_posts['image'] != '') {
            ?>
                <img class="image" src="uploaded_img/<?= $fetch_posts['image']; ?>" alt="">
            <?php
                }
            ?>
            
            <div class="content"><?= $fetch_posts['content'];?></div>
            <div class="icons">
                <a href="view_post.php?post_id=<?= $post_id; ?>"><i class="fas fa-comment"></i><span><?= $total_post_comments; ?></span></a>
                <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if($confirm_likes->rowCount() > 0) {echo 'color:var(--red);';} ?>"></i><span><?= $total_post_likes; ?></span></button>
            </div>
            <a class="category" href="category.php?category=<?= $fetch_posts['category'] ?>"><i class="fas fa-tag"></i><span><?= $fetch_posts['category']; ?></span></a>
        </form>
        <?php 
                }
            }else {
                echo '<p class="empty">no post found!</p>';
            }
        ?>
</section>

<section class="comments" style="padding-top: 0;">
    <p class="comment-title">add comment</p>
    <?php 
        if($user_id != '') {
            $select_admin_id = $conn->prepare("SELECT * FROM `posts` WHERE id = ? AND status = ?");
            $select_admin_id->execute([$get_id, 'active']);
            $fetch_admin_id = $select_admin_id->fetch(PDO::FETCH_ASSOC);
            
            
    ?>

    <form action="" method="POST" class="add-comment">
        <input type="hidden" name="admin_id" value="<?= $fetch_admin_id['admin_id']; ?>">
        <input type="hidden" name="user_name" value="<?= $fetch_profile['name']; ?>">
        <p><i class="fas fa-user"></i><a href="update.php"><?= $fetch_profile['name']; ?></a></p>
        <textarea name="comment" class="comment-box" required maxlength="1000" placeholder="write your comment..." cols="30" rows="10"></textarea>
        <input type="submit" value="add comment" name="add_comment" class="inline-btn">
    </form>

    <?php 
        }else {

        
    ?>

        <div class="add-comment">
            <p>login to add or edit comments</p>
            <div class="flex-btn">
                <a href="login.php" class="inline-option-btn">login</a>
                <a href="register.php" class="inline-option-btn">register</a>
            </div>
        </div>
        
    <?php 
        }
    ?>
    <p class="comment-title">user comments</p>
    <div class="show-comments">
        <?php 
            $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ? ");
            $select_comments->execute([$get_id]);
            if($select_comments->rowCount() > 0) {
                while($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)) {
                    
                
        ?>

        <div class="user-comments" <?php if($fetch_comments['user_id'] == $user_id) {echo 'style="order: -1;" ';}; ?> >
            <div class="user">
                <i class="fas fa-user"></i>
                <div class="user-info">
                    <p><?= $fetch_comments['user_name']; ?></p>
                    <div><?= $fetch_comments['date']; ?></div>
                </div>
            </div>
            <div class="comment-box"><?= $fetch_comments['comment'] ?></div>
            <?php 
                if($fetch_comments['user_id'] == $user_id) {
                    
                 
            ?>
                <form action="" method="POST" class="flex-btn">
                    <input type="hidden" name="comment_id" value="<?= $fetch_comments['id'] ?>">
                    <!-- <input type="submit" value="edit comment" class="inline-option-btn" name="edit_comment"> -->
                    <input type="submit" value="delete comment" class="inline-delete-btn" name="delete_comment" onclick="return confirm('delte this comment?');">
                </form>
            <?php 
                }
            ?>
        </div>
        
        <?php 
            }
        }else {
            echo '<p class="empty">no comments added!</p>';
        }
        ?>
    </div>
</section>

<?php include 'components/user_footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>