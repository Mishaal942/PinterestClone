<?php
session_start();
include "db.php";

// Redirect if not logged in
if(!isset($_SESSION['user_id'])){
    echo "<script>window.location='login.php';</script>";
    exit();
}

// Fetch pins with total likes and total comments
$pins = $conn->query("
    SELECT p.*, 
           (SELECT COUNT(*) FROM pin_likes pl WHERE pl.pin_id=p.id) AS total_likes,
           (SELECT COUNT(*) FROM pin_comments pc WHERE pc.pin_id=p.id) AS total_comments
    FROM pins p
    ORDER BY p.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Pinterest Clone</title>
<style>
body{font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:0;}
header{background:#e60023;color:white;padding:15px;text-align:center;}
a{color:white;text-decoration:none;margin:0 10px;}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:15px;padding:20px;}
.pin{background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);}
.pin img{width:100%;display:block;}
.pin-content{padding:10px;text-align:left;}
.pin h3{margin:0 0 5px;}
.pin p{margin:0 0 10px;font-size:14px;color:#555;}
.pin-stats{font-size:14px;color:#e60023;margin-top:5px;}
.pin a.details-link{text-decoration:none;color:#e60023;font-weight:bold;}
</style>
</head>
<body>

<header>
<h1>Pinterest Clone</h1>
<a href="profile.php">Profile</a>
<a href="upload_pin.php">Upload Pin</a>
<a href="logout.php">Logout</a>
</header>

<div class="grid">
<?php while($pin = $pins->fetch_assoc()): ?>
<div class="pin">
    <img src="<?= $pin['image'] ?>" alt="<?= $pin['title'] ?>">
    <div class="pin-content">
        <h3><?= $pin['title'] ?></h3>
        <p><?= $pin['category'] ?></p>
        <div class="pin-stats">
            ❤️ <?= $pin['total_likes'] ?> &nbsp; | &nbsp; 💬 <?= $pin['total_comments'] ?>
        </div>
        <a class="details-link" href="pin_details.php?id=<?= $pin['id'] ?>">View Details</a>
    </div>
</div>
<?php endwhile; ?>
</div>

</body>
</html>
