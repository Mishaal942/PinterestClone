<?php
session_start();
include "db.php";

$message = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $check = $conn->query("SELECT * FROM users WHERE email='$email' OR username='$username'");
    if($check->num_rows > 0){
        $message = "Email or username already exists!";
    } else {
        $conn->query("INSERT INTO users(username,email,password) VALUES('$username','$email','$password')");
        echo "<script>window.location='login.php';</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Signup</title>
<style>
body{font-family:Arial,sans-serif;background:#f5f5f5;}
form{max-width:400px;margin:50px auto;background:white;padding:30px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.2);}
input{width:100%;padding:10px;margin:10px 0;border:1px solid #ccc;border-radius:5px;}
button{padding:10px 20px;background:#e60023;color:white;border:none;border-radius:5px;cursor:pointer;}
p.error{color:red;}
</style>
</head>
<body>

<form method="POST">
<h2>Signup</h2>
<?php if($message) echo "<p class='error'>$message</p>"; ?>
<input type="text" name="username" placeholder="Username" required>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Signup</button>
<p>Already have an account? <a href="login.php">Login</a></p>
</form>

</body>
</html>
