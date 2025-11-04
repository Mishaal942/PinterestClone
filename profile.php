<?php
session_start();
include "db.php";
if(!isset($_SESSION['user_id'])){
    echo "<script>window.location='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id='$user_id'")->fetch_assoc();
$pins = $conn->query("SELECT * FROM pins WHERE user_id='$user_id' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Profile</title>
<style>
body{font-family:Arial,sans-serif;background:#f5f5f5;}
header{background:#e60023;color:white;padding:15px;text-align:center;}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:15px;padding:20px;}
.pin{background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);}
.pin img{width:100%;display:block;}
.pin h3{margin:10px;padding:0 10px;}
.pin p{padding:0 10px 10px;}
a{color:inherit;text-decoration:none;}
</style>
</head>
<body>

<header>
<h1><?= $user['username'] ?>'s Profile</h1>
<a href="index.php" style="color:white;margin-right:10px;">Home</a>
<a href="upload_pin.php" style="color:white;margin-right:10px;">Upload Pin</a>
<a href="logout.php" style="color:white;">Logout</a>
</header>

<div class="grid">
<?php while($pin = $pins->fetch_assoc()): ?>
<div class="pin">
    <img src="<?= $pin['image'] ?>" alt="<?= $pin['title'] ?>">
    <h3><?= $pin['title'] ?></h3>
    <p><?= $pin['description'] ?></p>
</div>
<?php endwhile; ?>
</div>

</body>
</html>
