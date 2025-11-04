<?php
session_start();
include "db.php";

$search = $_GET['q'] ?? '';
$category = $_GET['category'] ?? '';

$query = "SELECT * FROM pins WHERE 1=1";
if($search) $query .= " AND title LIKE '%$search%'";
if($category) $query .= " AND category='$category'";
$pins = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
<title>Search Pins</title>
<style>
body{font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:0;}
header{background:#e60023;color:white;padding:15px;text-align:center;}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:15px;padding:20px;}
.pin{background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);}
.pin img{width:100%;display:block;}
.pin h3{margin:10px;padding:0 10px;}
.pin p{padding:0 10px 10px;}
form{max-width:500px;margin:20px auto;text-align:center;}
input, select{padding:10px;margin:5px;}
button{padding:10px 20px;background:#e60023;color:white;border:none;border-radius:5px;cursor:pointer;}
</style>
</head>
<body>

<header>
<h1>Search & Explore</h1>
<a href="index.php" style="color:white;margin-right:10px;">Home</a>
<a href="profile.php" style="color:white;">Profile</a>
</header>

<form method="GET">
<input type="text" name="q" placeholder="Search by title" value="<?= $search ?>">
<select name="category">
    <option value="">All Categories</option>
    <option <?= $category=='Fashion'?'selected':'' ?>>Fashion</option>
    <option <?= $category=='Art'?'selected':'' ?>>Art</option>
    <option <?= $category=='Food'?'selected':'' ?>>Food</option>
    <option <?= $category=='Travel'?'selected':'' ?>>Travel</option>
    <option <?= $category=='DIY'?'selected':'' ?>>DIY</option>
</select>
<button type="submit">Search</button>
</form>

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
