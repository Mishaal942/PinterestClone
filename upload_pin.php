<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    echo "<script>window.location='login.php';</script>";
    exit();
}

$message = "";

if(isset($_POST['upload_pin'])){
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = $_POST['category'];
    $board_id = !empty($_POST['board_id']) ? $_POST['board_id'] : NULL;

    if(isset($_FILES['image']) && $_FILES['image']['error']==0){
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = basename($_FILES['image']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];

        if(in_array($file_ext, $allowed)){
            $upload_dir = "uploads/";
            if(!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $new_name = time() . "_" . uniqid() . "." . $file_ext;
            $target_file = $upload_dir . $new_name;

            if(move_uploaded_file($file_tmp, $target_file)){
                $stmt = $conn->prepare("INSERT INTO pins(user_id, board_id, title, description, image, category) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iissss", $_SESSION['user_id'], $board_id, $title, $description, $target_file, $category);
                if($stmt->execute()){
                    $message = "✅ Pin uploaded successfully!";
                } else {
                    $message = "❌ Database error: ".$stmt->error;
                }
                $stmt->close();
            } else {
                $message = "❌ Failed to move uploaded file!";
            }
        } else {
            $message = "❌ Invalid file type!";
        }
    } else {
        $message = "❌ Please select an image!";
    }
}

// Fetch boards
$boards_result = $conn->query("SELECT * FROM boards WHERE user_id='".$_SESSION['user_id']."'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Upload Pin</title>
<style>
    body{
        font-family: 'Segoe UI', sans-serif;
        background: #f8f8f8;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
    .upload-container{
        width: 100%;
        max-width: 450px;
        background: #fff;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        border-radius: 12px;
    }
    h2{
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }
    input[type="text"],
    textarea,
    select,
    input[type="file"]{
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        background: #fafafa;
        transition: 0.3s;
    }
    input:focus,
    textarea:focus,
    select:focus{
        outline: none;
        border-color: #e60023;
        background: #fff;
    }
    textarea{
        resize: none;
        height: 100px;
    }
    button{
        width: 100%;
        padding: 12px;
        background: #e60023;
        border: none;
        border-radius: 8px;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
    }
    button:hover{
        background: #c3001d;
    }
    .message-success{
        color: #009e30;
        font-size: 15px;
        text-align: center;
        margin-bottom: 10px;
    }
    .message-error{
        color: #e60023;
        font-size: 15px;
        text-align: center;
        margin-bottom: 10px;
    }
</style>
</head>
<body>

<div class="upload-container">
    <h2>Upload Pin</h2>

    <?php 
    if($message){
        if(strpos($message,'✅') !== false) {
            echo "<p class='message-success'>$message</p>";
        } else {
            echo "<p class='message-error'>$message</p>";
        }
    }
    ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Enter Pin Title" required>
        
        <textarea name="description" placeholder="Enter Description" required></textarea>

        <select name="category" required>
            <option value="">Select Category</option>
            <option>Fashion</option>
            <option>Art</option>
            <option>Food</option>
            <option>Travel</option>
            <option>DIY</option>
        </select>

        <select name="board_id" required>
            <option value="">-- Select Board --</option>
            <?php 
            if($boards_result->num_rows > 0){
                while($board = $boards_result->fetch_assoc()){
                    echo "<option value='".$board['id']."'>".$board['board_name']."</option>";
                }
            } else {
                echo "<option value=''>No boards available</option>";
            }
            ?>
        </select>

        <input type="file" name="image" accept="image/*" required>
        <button type="submit" name="upload_pin">Upload Pin</button>
    </form>
</div>

</body>
</html>
