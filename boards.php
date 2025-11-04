<?php
session_start();
include "db.php";
if(!isset($_SESSION['user_id'])){
    echo "<script>window.location='login.php';</script>";
    exit();
}

$message = "";
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $board_name = $_POST['board_name'];
    $conn->query("INSERT INTO boards(user_id, board_name) VALUES('".$_SESSION['user_id']."','$board_name')");
    $message = "Board created successfully!";
}

$boards = $conn->query("SELECT * FROM boards WHERE user_id='".$_SESSION['user_id']."'");
?>
<!DOCTYPE html>
<html>
<head>
<title>My Boards</title>
<style>
body{font-family:Arial,sans-serif;background:#f5f5f5;}
form{max-width:400px;margin:20px auto;background:white;padding:20px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.2);}
input{width:100%;padding:10px;margin:10px 0;border:1px solid #ccc;border-radius:5px;}
button{padding:10px 20px;background:#e60023;color:white;border:none;border-radius:5px;cursor:pointer;}
p.success{color:green;text-align:center;}
ul{list-style:none;padding:0;}
li{background:white;margin:5px 0;padding:10px;border-radius:5px;box-shadow:0 1px 4px rgba(0,0,0,0.1);}
</style>
</head>
<body>

<form method="POST">
<h2>Create Board</h2>
<?php if($message) echo "<p class='success'>$message</p>"; ?>
<input type="text" name="board_name" placeholder="Board Name" required>
<button type="submit">Create</button>
</form>

<div style="max-width:400px;margin:20px auto;">
<h3>My Boards</h3>
<ul>
<?php while($board = $boards->fetch_assoc()): ?>
<li><?= $board['board_name'] ?></li>
<?php endwhile; ?>
</ul>
</div>

</body>
</html>
