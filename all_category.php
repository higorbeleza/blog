<?php 

@include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}else{
    $user_id = '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>all categories</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
<?php include 'components/user_header.php'; ?>

<section class="categories">
    <h1 class="heading">all categories</h1>

    <div class="box-container">
        <div class="box"><span>01</span><a href="category.php?category=education">education</a></div>
        <div class="box"><span>02</span><a href="category.php?category=business">business</a></div>
        <div class="box"><span>03</span><a href="category.php?category=news">news</a></div>
        <div class="box"><span>04</span><a href="category.php?category=gaming">gaming</a></div>
        <div class="box"><span>05</span><a href="category.php?category=sports">sports</a></div>
        <div class="box"><span>06</span><a href="category.php?category=design">design</a></div>
        <div class="box"><span>07</span><a href="category.php?category=technology">technology</a></div>
    </div>
</section>

<?php include 'components/user_footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>