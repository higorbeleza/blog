<?php 

if(isset($_POST['like_post'])) {
    if($user_id != '') {
        $post_id = $_POST['post_id'];
        $post_id = filter_var($post_id, FILTER_SANITIZE_STRING);

        $admin_id = $_POST['admin_id'];
        $admin_id = filter_var($admin_id, FILTER_SANITIZE_STRING);

        $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ? AND user_id = ? ");
        $select_likes->execute([$post_id, $user_id]);

        if($select_likes->rowCount() > 0) {
            $remove_likes = $conn->prepare("DELETE FROM `likes` WHERE post_id = ? AND user_id = ?");
            $remove_likes->execute([$post_id, $user_id]);
            $message[] = 'removed from likes!';
        }else {
            $add_likes = $conn->prepare("INSERT INTO `likes` (user_id, admin_id, post_id) VALUES(?, ?, ?)");
            $add_likes->execute([$user_id, $admin_id, $post_id]);
            $message[] = 'added to likes!';
        }
    }else {
        $message[] = 'please login first!';
    }
}

?>