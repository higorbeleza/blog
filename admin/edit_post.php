<?php 

@include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)) {
    header("Location: admin_login.php");
}

if(!isset($_GET['post_id'])) {
    header("Location: view_posts.php");
}else {
    $get_id = $_GET['post_id'];
}

if(isset($_POST['save'])) {

    $title = $_POST['title'];
    $title = filter_var($title, FILTER_SANITIZE_STRING);

    $content = $_POST['content'];
    $content = filter_var($content, FILTER_SANITIZE_STRING);
    
    $category = $_POST['category'];
    $category = filter_var($category, FILTER_SANITIZE_STRING);

    $status = $_POST['status'];
    $status = filter_var($status, FILTER_SANITIZE_STRING);

    $update_post = $conn->prepare("UPDATE `posts` SET title = ?, content = ?, category = ?, status = ? WHERE id = ? ");
    $update_post->execute([$title, $content, $category, $status, $get_id]);

    $message[] = 'posts updated!';

    $old_img = $_POST['old_image'];
    $old_img = filter_var($old_img, FILTER_SANITIZE_STRING);
    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/'.$image;

    $select_image = $conn->prepare("SELECT * FROM `posts` WHERE image = ? AND admin_id = ?");
    $select_image->execute([$image, $admin_id]);

    if(!empty($image)) {
        if($select_image->rowCount() > 0 AND $image != '') {
            $message[] = 'please rename your image!';
        }elseif($image_size > 2000000) {
            $message[] = 'image size is too large';
        }else {
            $update_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE id = ? ");
            $update_image->execute([$image, $get_id]);
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'image updated!';

            if($old_img != $image AND $old_img != '') {
                unlink('../uploaded_img/'.$old_img);
            }
        }
    }else {
        $image = '';
    }
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

    header('Location: view_posts.php');
}

if(isset($_POST['delete_image'])) {
    $empty_image = '';

    $select_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
    $select_image->execute([$get_id]);
    $fetch_image = $select_image->fetch(PDO::FETCH_ASSOC);
    if($fetch_image['image'] != '') {
        unlink('../uploaded_img/'.$fetch_image['image']);
    }
    $unset_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE id = ? ");
    $unset_image->execute([$empty_image, $get_id]);
    $message[] = 'image deleted!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit posts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
    
<!-- HEADER START -->
<?php include '../components/admin_header.php'; ?>
<!-- HEADER END -->

<section class="post-editor">
    <h1 class="heading">edit post</h1>

    <?php 
        $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE id = ? AND admin_id = ?");
        $select_posts->execute([$get_id, $admin_id]);
        if($select_posts->rowCount() > 0) {
            while($fetch_post = $select_posts->fetch(PDO::FETCH_ASSOC)) {
                
    ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="post_id" value="<?= $fetch_post['id'];?>">
        <input type="hidden" name="old_image" value="<?= $fetch_post['image'];?>">
        <input type="hidden" name="name" value="<?=$fetch_profile['name'];?>">
        <p>post status <span>*</span></p>
        <select name="status" require class="box">
            <option value="<?=$fetch_post['status'];?>" selected><?= $fetch_post['status']; ?></option>
            <option value="active">active</option>
            <option value="deactive">deactive</option>
        </select>
        <p>post title <span>*</span></p>
        <input type="text" name="title" value="<?=$fetch_post['title'];?>" require placeholder="add post title" maxlength="1000" class="box">

        <p>post content <span>*</span></p>
        <textarea name="content" class="box" require maxlength="10000" placeholder="write your content..." id="" cols="30" rows="10"> <?=$fetch_post['content'];?> </textarea>

        <p>post category <span>*</span></p>
        <select name="category" class="box" require id="">
            <option value="" disabled selected>-- select post category</option>
            <option value="<?=$fetch_post['category'];?>" selected><?= $fetch_post['category']; ?></option>
            <option value="education">education</option>
            <option value="pets and animals">pets and animals</option>
            <option value="technology">technology</option>
            <option value="fashion">fashion</option>
            <option value="entertainment">entertainment</option>
            <option value="movies">movies</option>
            <option value="gaming">gaming</option>
            <option value="music">music</option>
            <option value="sports">sports</option>
            <option value="news">news</option>
            <option value="natural">travel</option>
            <option value="comedy">comedy</option>
            <option value="design and development">design and development</option>
            <option value="food and drinks">food and drinks</option>
            <option value="lifestyle">lifestyle</option>
            <option value="personal">personal</option>
            <option value="health and fitness">health and fitness</option>
            <option value="business">business</option>
            <option value="shopping">shopping</option>
            <option value="animation">animations</option>
        </select>
        <p>post image <span>*</span></p>
        <input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
            <?php 
                if($fetch_post['image'] != '') {

                }
            ?>  
            <img src="../uploaded_img/<?= $fetch_post['image'] ?>" alt="" class="image">
            <input type="submit" value="delete image" name="delete_image" class="delete-btn">
            <?php 

            ?>
        <div class="flex-btn">
            <input type="submit" value="save post" name="save" class="btn">
            <a href="view_posts.php" class="option-btn">go back</a>
            <button type="submit" name="delete" onclick="return confirm('delete this post?')" class="delete-btn">delete</button>
        </div>
    </form>
    <?php 
            }
        }else {
            echo '<p class="empty">no posts added yet!</p>';
        }
    ?>
</section>

<!-- custom js file link -->
<script src="../js/admin_script.js"></script>
</body> 
</html>