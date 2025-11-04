<?php
session_start();
include "db.php";

// Redirect if not logged in
if(!isset($_SESSION['user_id'])){
    echo "<script>window.location='login.php';</script>";
    exit();
}

// Get pin ID
$pin_id = $_GET['id'] ?? 0;
$pin = $conn->query("SELECT * FROM pins WHERE id='$pin_id'")->fetch_assoc();
if(!$pin){
    echo "<script>alert('Pin not found!');window.location='index.php';</script>";
    exit();
}

// Fetch user boards for dropdown
$boards_result = $conn->query("SELECT * FROM boards WHERE user_id='".$_SESSION['user_id']."'");

// Handle Save to Board
$save_message = "";
if(isset($_POST['save_board'])){
    $board_id = $_POST['board_id'];
    if($board_id){
        $stmt = $conn->prepare("UPDATE pins SET board_id=? WHERE id=?");
        $stmt->bind_param("ii",$board_id,$pin_id);
        $stmt->execute();
        $stmt->close();
        $save_message = "Pin saved to board!";
    } else {
        $save_message = "Please select a board!";
    }
}

// Handle Likes
$liked = $conn->query("SELECT * FROM pin_likes WHERE pin_id='$pin_id' AND user_id='".$_SESSION['user_id']."'")->num_rows > 0;
$total_likes = $conn->query("SELECT COUNT(*) as total FROM pin_likes WHERE pin_id='$pin_id'")->fetch_assoc()['total'];
if(isset($_POST['like'])){
    if(!$liked){
        $conn->query("INSERT INTO pin_likes(pin_id,user_id) VALUES('$pin_id','".$_SESSION['user_id']."')");
        $liked = true;
        $total_likes++;
    }
}

// Handle comments
if(isset($_POST['comment_submit'])){
    $comment_text = trim($_POST['comment']);
    if($comment_text != ""){
        $stmt = $conn->prepare("INSERT INTO pin_comments(pin_id,user_id,comment) VALUES(?,?,?)");
        $stmt->bind_param("iis",$pin_id,$_SESSION['user_id'],$comment_text);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch comments
$comments = $conn->query("
    SELECT pc.comment, pc.created_at, u.username
    FROM pin_comments pc
    JOIN users u ON pc.user_id = u.id
    WHERE pc.pin_id='$pin_id'
    ORDER BY pc.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title><?= $pin['title'] ?></title>
<style>
body{font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:0;text-align:center;}
header{background:#e60023;color:white;padding:15px;}
a{color:white;text-decoration:none;margin:0 10px;}
img{max-width:80%;border-radius:10px;margin:20px 0;}
form{max-width:500px;margin:20px auto;background:white;padding:20px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.2);}
textarea,select{width:100%;padding:10px;margin:10px 0;border:1px solid #ccc;border-radius:5px;}
button{padding:10px 20px;background:#e60023;color:white;border:none;border-radius:5px;cursor:pointer;}
p.success{color:green;}
p.error{color:red;}
.comment{background:white;padding:10px;margin:5px 0;border-radius:5px;box-shadow:0 1px 4px rgba(0,0,0,0.1);text-align:left;}
h2,h3{margin:10px 0;}
</style>
</head>
<body>

<header>
<h1><?= $pin['title'] ?></h1>
<a href="index.php">Home</a>
<a href="profile.php">Profile</a>
<a href="logout.php">Logout</a>
</header>

<!-- Pin Image & Description -->
<img src="<?= $pin['image'] ?>" alt="<?= $pin['title'] ?>">
<p><?= $pin['description'] ?></p>

<!-- Save to Board -->
<form method="POST">
<h3>Save to Board</h3>
<?php if($save_message) echo "<p class='success'>$save_message</p>"; ?>
<select name="board_id" required>
<option value="">-- Select Board --</option>
<?php 
if($boards_result->num_rows > 0){
    while($board = $boards_result->fetch_assoc()){
        // Show selected board if pin already saved
        $selected = ($pin['board_id'] == $board['id']) ? "selected" : "";
        echo "<option value='".$board['id']."' $selected>".$board['board_name']."</option>";
    }
} else {
    echo "<option value=''>No boards available</option>";
}
?>
</select>
<button name="save_board">Save</button>
</form>

<!-- Like Button -->
<form method="POST" style="margin-top:20px;">
<button name="like">
<?= $liked ? "❤️ Liked ($total_likes)" : "🤍 Like ($total_likes)" ?>
</button>
</form>

<!-- Comments -->
<div style="max-width:500px;margin:20px auto;text-align:left;">
<h3>Comments</h3>

<!-- Add Comment Form -->
<form method="POST">
<textarea name="comment" placeholder="Add a comment..." required></textarea>
<button name="comment_submit">Post Comment</button>
</form>

<!-- Display Comments -->
<?php while($c = $comments->fetch_assoc()): ?>
<div class="comment">
<strong><?= $c['username'] ?>:</strong> <?= $c['comment'] ?><br>
<small><?= $c['created_at'] ?></small>
</div>
<?php endwhile; ?>
</div>

</body>
</html>
