<?php
session_start();
include "db.php";

$message = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($res->num_rows > 0){
        $user = $res->fetch_assoc();
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo "<script>window.location='index.php';</script>";
            exit();
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "Email not found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login</title>
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
<h2>Login</h2>
<?php if($message) echo "<p class='error'>$message</p>"; ?>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Login</button>
<p>Don't have an account? <a href="signup.php">Signup</a></p>
</form>

</body>
</html>
