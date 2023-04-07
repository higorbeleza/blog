<?php 

@include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}else{
    $user_id = '';
}

if(isset($_GET['author'])) {
    $author = $_GET['author'];
}else {
    $author = '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search page</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
<?php include 'components/user_header.php'; ?>

<section class="authors">
    <h1 class="heading">authors</h1>

    <div class="box-container">
        <?php 
            $select_authors = $conn->prepare("SELECT * FROM `admin` ");
            $select_authors->execute();
            if($select_authors->rowCount() > 0) {
                while($fetch_author = $select_authors->fetch(PDO::FETCH_ASSOC)) {

                $admin_id = $fetch_author['id'];

                $count_admin_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ? AND status = ?");
                $count_admin_posts->execute([$admin_id, 'active']);
                $total_admin_posts = $count_admin_posts->rowCount();

                $count_admin_likes = $conn->prepare("SELECT * FROM `likes` WHERE admin_id = ?");
                $count_admin_likes->execute([$admin_id]);
                $total_admin_likes = $count_admin_likes->rowCount();

                $count_admin_comments = $conn->prepare("SELECT * FROM `comments` WHERE admin_id = ?");
                $count_admin_comments->execute([$admin_id]);
                $total_admin_comments = $count_admin_comments->rowCount();
        ?>
        <div class="box">
            <p>authors : <span><?= $fetch_author['name']; ?></span></p>
            <p>total posts : <span><?= $total_admin_posts; ?></span></p>
            <p>total likes : <span><?= $total_admin_likes; ?></span></p>
            <p>total comments : <span><?= $total_admin_comments; ?></span></p>
            <a href="author_posts.php?author=<?= $fetch_author['name']; ?>" class="btn">view posts</a>
        </div>
        <?php
                }
            }else {
                echo '<p class="empty">no authors found!</p>';
            }
        ?>
    </div>
</section>

<?php include 'components/user_footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>