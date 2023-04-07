<?php 

@include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)) {
    header("Location: admin_login.php");
}

if(isset($_POST['publish'])) {
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $title = $_POST['title'];
    $title = filter_var($title, FILTER_SANITIZE_STRING);

    $content = $_POST['content'];
    $content = filter_var($content, FILTER_SANITIZE_STRING);
    
    $category = $_POST['category'];
    $category = filter_var($category, FILTER_SANITIZE_STRING);

    $status = 'active';

    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/'.$image;

    $select_image = $conn->prepare("SELECT * FROM `posts` WHERE image = ? AND admin_id = ?");
    $select_image->execute([$image, $admin_id]);

    if(isset($image)) {
        if($select_image->rowCount() > 0 AND $image != '') {
            $message[] = 'image name repeated';
        }elseif($image_size > 2000000) {
            $message[] = 'image size is too large';
        }else {
            move_uploaded_file($image_tmp_name, $image_folder);
        }
    }else {
        $image = '';
    }

    if($select_image->rowCount() > 0 AND $image != '') {
        $message[] = 'please rename your image';
    }else {
        $insert_post = $conn->prepare("INSERT INTO `posts` (admin_id, name, title, content, category, image, status) VALUES(?,?,?,?,?,?,?)");
        $insert_post->execute([$admin_id, $name, $title, $content, $category, $image, $status]);
        $message[] = 'post published!';
    }
}

if(isset($_POST['draft'])) {
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $title = $_POST['title'];
    $title = filter_var($title, FILTER_SANITIZE_STRING);

    $content = $_POST['content'];
    $content = filter_var($content, FILTER_SANITIZE_STRING);
    
    $category = $_POST['category'];
    $category = filter_var($category, FILTER_SANITIZE_STRING);

    $status = 'deactive';

    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/'.$image;

    $select_image = $conn->prepare("SELECT * FROM `posts` WHERE image = ? AND admin_id = ?");
    $select_image->execute([$image, $admin_id]);

    if(isset($image)) {
        if($select_image->rowCount() > 0 AND $image != '') {
            $message[] = 'image name repeated';
        }elseif($image_size > 2000000) {
            $message[] = 'image size is too large';
        }else {
            move_uploaded_file($image_tmp_name, $image_folder);
        }
    }else {
        $image = '';
    }

    if($select_image->rowCount() > 0 AND $image != '') {
        $message[] = 'please rename your image';
    }else {
        $insert_post = $conn->prepare("INSERT INTO `posts` (admin_id, name, title, content, category, image, status) VALUES(?,?,?,?,?,?,?)");
        $insert_post->execute([$admin_id, $name, $title, $content, $category, $image, $status]);
        $message[] = 'draft saved!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add post</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
    
<!-- HEADER START -->
<?php include '../components/admin_header.php'; ?>
<!-- HEADER END -->

<section class="post-editor">
    <h1 class="heading">add post</h1>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="name" value="<?=$fetch_profile['name'];?>">
        <p>post title <span>*</span></p>
        <input type="text" name="title" require placeholder="add post title" maxlength="1000" class="box">

        <p>post content <span>*</span></p>
        <textarea name="content" class="box" require maxlength="10000" placeholder="write your content..." id="" cols="30" rows="10"></textarea>

        <p>post category <span>*</span></p>
        <select name="category" class="box" require id="">
            <option value="" disabled selected>-- select post category</option>
            <option value="nature">nature</option>
            <option value="nature">education</option>
            <option value="nature">pets and animals</option>
            <option value="nature">technology</option>
            <option value="nature">fashion</option>
            <option value="nature">entertainment</option>
            <option value="nature">movies</option>
            <option value="nature">gaming</option>
            <option value="nature">music</option>
            <option value="nature">sports</option>
            <option value="nature">news</option>
            <option value="nature">travel</option>
            <option value="nature">comedy</option>
            <option value="nature">design and development</option>
            <option value="nature">food and drinks</option>
            <option value="nature">lifestyle</option>
            <option value="nature">personal</option>
            <option value="nature">health and fitness</option>
            <option value="nature">business</option>
            <option value="nature">shopping</option>
            <option value="nature">animations</option>
        </select>
        <p>post image <span>*</span></p>
        <input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
        <div class="flex-btn">
            <input type="submit" value="publish post" name="publish" class="btn">
            <input type="submit" value="save draft" name="draft" class="option-btn">
        </div>
    </form>
</section>

<!-- custom js file link -->
<script src="../js/admin_script.js"></script>
</body> 
</html>