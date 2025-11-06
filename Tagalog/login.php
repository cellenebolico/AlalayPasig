<?php
session_start();
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alalaypasig_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
$message = "";
$password_error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $psw = $_POST['psw'];

    if (empty($email) || empty($psw)) {
        $message = "Email at password ay kailangan";
    } else {
        $stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $fullname, $hashed_psw);
            $stmt->fetch();
            if (password_verify($psw, $hashed_psw)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['fullname'] = $fullname;
                header("Location: navbar/homepage.html"); // Redirect to a dashboard or home page after login
                exit();
            } else {
                $password_error = "Mali ang Password.";
            }
        } else {
            $message = "Email ay hindi mahanap.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mag-log in</title>

    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login {
            text-align: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            width: 100%;
        }
        .image-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .image-container img {
            max-width: 100%;
            height: auto;
        }
        .login hr {
            width: 80%;
            max-width: 700px;
            margin: 24px auto 40px auto;
            border: 1px solid #aaa;
        }
        .login-form {
            max-width: 700px;
            margin: 0 auto;
        }
        input[type=text], input[type=password], input[type=email] {
            width: 100%;
            max-width: 700px;
            height: 60px;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #7689D7;
            box-sizing: border-box;
            border-radius: 4px;
            font-size: 16px;
        }
        label {
            display: block;
            margin-top: 25px;
            text-align: left;
            font-size: 18px;
            color: #333;
            font-weight: bold;
        }
        button {
            font-size: 20px;
            width: 120px;
            height: 40px;
            background-color: #7689D7;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            display: block;
            margin: 20px auto 0 auto;
        }
        button:hover {
            background-color: #5a6fc2;
        }
        .forgot {
            display: block;
            text-align: left;
            text-decoration: none;
            color: #7689D7;
            margin-top: 10px;
        }
        .forgot:hover {
            text-decoration: underline;
        }
        .links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
            margin-top: 40px;
            color: black;
        }
        .links a {
            text-decoration: none;
            color: #333;
        }
        .links a:hover {
            text-decoration: underline;
            color: #7689D7;
        }
        .reserved {
            color: #666;
        }
        .sign, .sign2 {
            text-decoration: none;
            color: #7689D7;
            font-weight: bold;
        }
        .sign:hover, .sign2:hover {
            text-decoration: underline;
        }
        
        .input-box {
            position: relative;
            display: flex;
            align-items: center;
            max-width: 700px;
            margin: 0 auto;
        }
        .input-box input {
            padding-right: 50px;
            width: 100%;
        }
        .input-box img {
            position: absolute;
            right: 15px;
            width: 25px;
            height: 25px;
            cursor: pointer;
            transition: transform 0.2s ease;
            top: 50%;
            transform: translateY(-50%);
        }
        .input-box img:hover {
            transform: translateY(-50%) scale(1.1);
        }
        .error-message {
            color: red;
            text-align: left;
            margin-top: 5px;
            font-size: 14px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        @media (min-width: 1025px) {
            .login {
                padding: 30px;
            }
            .image-container img {
                width: 180px;
                margin-right: -20px;
                margin-left: -90px;
                height: auto;
            }
            h1 {
                font-size: 2.2rem;
            }
        }
        
        @media (max-width: 1024px) and (min-width: 769px) {
            .login {
                padding: 15px;
            }
            .image-container {
                margin-top: 20px;
            }
            .image-container img {
                width: 140px;
                height: auto;
            }
            .login hr {
                width: 90%;
                margin-bottom: 40px;
            }
            input[type=text], input[type=password], input[type=email] {
                max-width: 500px;
                height: 50px;
            }
            .login-form {
                max-width: 500px;
            }
            .input-box {
                max-width: 500px;
            }
            .input-box img {
                width: 22px;
                height: 22px;
                right: 12px;
            }
            .links {
                gap: 20px;
                margin-top: 30px;
            }
        }
        
        @media (max-width: 768px) and (min-width: 481px) {
            .login {
                padding: 10px;
            }
            .image-container {
                flex-direction: column;
                gap: 10px;
                margin-top: 10px;
            }
            .image-container img {
                width: 120px;
                height: auto;
            }
            h1 {
                font-size: 1.8rem;
                margin: 0;
            }
            .login hr {
                width: 95%;
                margin: 20px auto 30px auto;
            }
            input[type=text], input[type=password], input[type=email] {
                width: 100%;
                max-width: 400px;
                height: 50px;
                font-size: 16px;
                padding-right: 45px; 
            }
            .input-box {
                max-width: 400px;
                width: 100%;
            }
            .input-box img {
                width: 22px;
                height: 22px;
                right: 12px;
            }
            label {
                text-align: center;
                font-size: 16px;
            }
            .forgot {
                text-align: center;
            }
            .links {
                gap: 15px;
                margin-top: 25px;
            }
            button {
                width: 140px;
                height: 45px;
                font-size: 18px;
            }
        }

        @media (max-width: 600px) {
            .login {
                padding: 8px;
            }
            .image-container img {
                width: 110px;
            }
            h1 {
                font-size: 1.6rem;
            }
            input[type=text], input[type=password], input[type=email] {
                max-width: 350px;
                height: 48px;
                padding-right: 42px;
            }
            .input-box {
                max-width: 350px;
            }
            .input-box img {
                width: 20px;
                height: 20px;
                right: 10px;
            }
        }
        
        @media (max-width: 480px) {
            .login {
                padding: 5px;
            }
            .image-container {
                flex-direction: column;
                gap: 5px;
            }
            .image-container img {
                width: 100px;
                height: auto;
            }
            h1 {
                font-size: 1.5rem;
            }
            h3, h4 {
                font-size: 1rem;
            }
            .login hr {
                margin: 15px auto 25px auto;
            }
            input[type=text], input[type=password], input[type=email] {
                height: 45px;
                padding: 10px 15px;
                width: 100%;
                max-width: 350px;
                font-size: 14px;
                padding-right: 40px; 
            }
            .input-box {
                width: 100%;
                max-width: 350px;
            }
            .input-box img {
                width: 20px;
                height: 20px;
                right: 10px;
            }
            .links {
                flex-direction: column;
                gap: 10px;
                margin-top: 20px;
                font-size: 14px;
            }
            button {
                width: 130px;
                height: 42px;
                font-size: 16px;
            }
            label {
                text-align: center;
                font-size: 16px;
            }
            .forgot {
                text-align: center;
                font-size: 14px;
            }
        }
        
        @media (max-width: 360px) {
            .login {
                padding: 5px;
            }
            input[type=text], input[type=password], input[type=email] {
                max-width: 300px;
                padding-right: 38px;
            }
            .input-box {
                max-width: 300px;
            }
            .input-box img {
                width: 18px;
                height: 18px;
                right: 8px;
            }
        }
    </style>
</head>
<body>
<div class="login">
<div class="image-container">
        <img src="Pictures/logo.png" width="180" height="140">
        <h1>ALALAY-PASIG</h1>
    </div>

<h3>Mag-log in sa AlalayPasig</h3>
<h4>Bago sa AlalayPasig? <a href="signup.php" class="sign">Mag-sign up ngayon</a></h4>

<hr>
<form action="login.php" method="POST">
    <div class="login-form">
    <label for="email"><b>Email</b></label>
        <input type="text" placeholder="Ilagay ang Email" name="email" required>

        <label for="psw"><b>Password</b></label>

        <div class="input-box">
        <input type="password" placeholder="Ilagay ang Password" name="psw" required id="password">
        <img src="Pictures/eye-close.png" id="eyeicon">
        </div>

        <?php if ($password_error): ?>
            <p class="error-message"><?php echo $password_error; ?></p>
        <?php endif; ?>
        <?php if ($message): ?>
            <p class="error-message"><?php echo $message; ?></p>
        <?php endif; ?>
        

        <a href="#" class="forgot">nakalimutan ang password?</a>

        <button type="submit">Mag-log in</button>

        <p>Wala pa bang account? <a href="signup.php" class="sign2">Mag-sign up</a></p>

        <div class="links">
        <a href="">Tulong</a>
        <a href="../English/navbar/contacts-notlogin.html">Makipag-ugnayan</a>
        <a href="../English/navbar/Terms of Services-notlogin.html">Mga Tuntunin ng Serbisyo</a>
        <a href="../English/navbar/Privacy and Policy-notlogin.html">Patakaran sa Privacy</a>
        <a href="" class="reserved">©2025 AlalayPasig. Lahat ng karapatan ay nakalaan</a>
        </div>      
        
        </div>

    </div>
</form>
<br>

<script>

    let eyeicon = document.getElementById("eyeicon");
    let password = document.getElementById("password");

    eyeicon.onclick = function(){
        if(password.type == "password"){
            password.type = "text";
            eyeicon.src = "Pictures/eye-open.png";
        }else{
            password.type = "password";
            eyeicon.src = "Pictures/eye-close.png";
        }
    }
</script>
  
</body>
</html>
