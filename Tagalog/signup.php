<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alalaypasig_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['name']);
    $email = trim($_POST['email']);
    $verifyemail = trim($_POST['verifyemail']);
    $psw = $_POST['psw'];
    $agree = isset($_POST['agree']);

    // Validation
    if (empty($fullname) || empty($email) || empty($verifyemail) || empty($psw)) {
        $message = "Kinakailangan ang lahat ng field.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Maling format ng email.";
    } elseif ($email !== $verifyemail) {
        $message = "Hindi magkatugma ang mga email.";
    } elseif (strlen($psw) < 6) {
        $message = "Ang password ay dapat hindi bababa sa 6 na character.";
    } elseif (!$agree) {
        $message = "Dapat kang sumang-ayon sa mga tuntunin.";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = "Mayroon nang email na ito.";
        } else {
            // Hash password and insert
            $hashed_psw = password_hash($psw, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fullname, $email, $hashed_psw);
            if ($stmt->execute()) {
                $message = "Matagumpay na nilikha ang account!";
            } else {
                $message = "Error sa paglikha ng account.";
            }
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
    <title>Mag-sign up</title>
    <style>
        /* All the CSS from the original signup.html remains unchanged */
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
        .signup {
            text-align: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            width: 100%;
        }
        h1 {
            margin-top: 0;
            font-size: 2.5rem;
            color: #333;
        }
        .image-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        .image-container img {
            max-width: 100%;
            height: auto;
        }
        .form {
            max-width: 700px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form label {
            display: block;
            margin-top: 25px;
            margin-bottom: 8px;
            text-align: left;
            font-size: 18px;
            color: #333;
            font-weight: bold;
        }
        input[type=text], input[type=password], input[type=email] {
            width: 100%;
            height: 60px;
            padding: 12px 20px;
            display: inline-block;
            border: 1px solid #7689D7;
            box-sizing: border-box;
            border-radius: 4px;
            font-size: 16px;
        }
        input[type="checkbox"] {
            transform: scale(1.5);
            margin-right: 10px;
        }
        .agree {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            font-size: 16px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
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
        }
        button:hover {
            background-color: #5a6fc2;
        }
        .signs {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 35px;
            font-size: 16px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        .signs p {
            margin: 0;
        }
        a {
            color: #7689D7;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        
        /* Input box styles */
        .input-box {
            position: relative;
            width: 100%;
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
        
        /* Desktop styles */
        @media (min-width: 1025px) {
            .signup {
                padding: 30px;
            }
            .image-container img {
                width: 350px;
                height: auto;
            }
            h1 {
                font-size: 2.3rem;
            }
        }
        
        /* Tablet landscape styles */
        @media (max-width: 1024px) and (min-width: 769px) {
            .signup {
                padding: 15px;
            }
            .image-container img {
                width: 300px;
                height: auto;
            }
            h1 {
                font-size: 2.2rem;
            }
            input[type=text], input[type=password], input[type=email] {
                height: 55px;
            }
            .input-box img {
                width: 22px;
                height: 22px;
                right: 12px;
            }
        }
        
        /* Tablet portrait styles */
        @media (max-width: 768px) and (min-width: 481px) {
            .signup {
                padding: 10px;
            }
            .image-container {
                flex-direction: column;
                gap: 10px;
            }
            .image-container img {
                width: 250px;
                height: auto;
            }
            h1 {
                font-size: 1.8rem;
                margin: 0;
            }
            input[type=text], input[type=password], input[type=email] {
                height: 50px;
                font-size: 16px;
                padding-right: 45px; 
            }
            .input-box img {
                width: 22px;
                height: 22px;
                right: 12px;
            }
            .form label {
                text-align: center;
                font-size: 16px;
            }
            .agree {
                justify-content: center;
                text-align: center;
            }
            .signs {
                flex-direction: column;
                gap: 20px;
                margin-top: 25px;
            }
            button {
                width: 140px;
                height: 45px;
                font-size: 18px;
            }
        }

        @media (max-width: 600px) {
            .signup {
                padding: 8px;
            }
            .image-container img {
                width: 200px;
            }
            h1 {
                font-size: 1.6rem;
            }
            input[type=text], input[type=password], input[type=email] {
                height: 48px;
                padding-right: 42px;
            }
            .input-box img {
                width: 20px;
                height: 20px;
                right: 10px;
            }
        }
        
        @media (max-width: 480px) {
            .signup {
                padding: 5px;
            }
            .image-container {
                flex-direction: column;
                gap: 5px;
            }
            .image-container img {
                width: 180px;
                height: auto;
            }
            h1 {
                font-size: 1.5rem;
            }
            input[type=text], input[type=password], input[type=email] {
                height: 45px;
                padding: 10px 15px;
                font-size: 14px;
                padding-right: 40px; 
            }
            .input-box img {
                width: 20px;
                height: 20px;
                right: 10px;
            }
            .agree {
                font-size: 14px;
                flex-direction: row;
                align-items: flex-start;
            }
            .signs {
                flex-direction: column;
                gap: 15px;
                margin-top: 25px;
                font-size: 14px;
            }
            button {
                width: 130px;
                height: 42px;
                font-size: 16px;
            }
            .form label {
                text-align: center;
                font-size: 16px;
            }
        }
        
        @media (max-width: 360px) {
            .signup {
                padding: 5px;
            }
            input[type=text], input[type=password], input[type=email] {
                padding-right: 38px;
            }
            .input-box img {
                width: 18px;
                height: 18px;
                right: 8px;
            }
            .agree {
                font-size: 13px;
            }
        }
    </style>
</head>

<body>
  <div class="signup">

    <div class="image-container">
        <img src="Pictures/logo.png" width="400" height="300" alt="Alalay-Pasig Logo">
    </div>

    <h1>ALALAY-PASIG</h1>
    <?php if ($message): ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php endif; ?>
 <form action="signup.php" method="POST">  
    <div class="form">
        <div class="form-group">
            <label for="name"><b>Buong Pangalan</b></label>
            <input type="text" placeholder="Ilagay ang Buong Pangalan" name="name" required>
        </div>

        <div class="form-group">
            <label for="email"><b>Email</b></label>
            <input type="email" placeholder="Ilagay ang Email" name="email" required>
        </div>

        <div class="form-group">
            <label for="verifyemail"><b>I-verify ang Email</b></label>
            <input type="email" placeholder="Ilagay muli ang Email" name="verifyemail" required>
        </div>

        <div class="form-group">
            <label for="psw"><b>Password</b></label>
            <div class="input-box">
                <input type="password" placeholder="Ilagay ang Password" name="psw" required id="password">
                <img src="Pictures/eye-close.png" id="eyeicon" alt="Toggle Password Visibility">
            </div>
        </div>
    </div>

    <div class="agree">
        <input type="checkbox" id="agree" name="agree" value="agree" required>
        <label for="agree">Binasa ko at tinatanggap ang 
            <a href="../English/navbar/Terms of Services-notlogin.html">Mga Tuntunin ng Serbisyo</a> at 
            <a href="../English/navbar/Privacy and Policy-notlogin.html">Patakaran sa Privacy</a>.
        </label>
    </div>

    <div class="signs">
        <button type="submit">Mag-sign up</button>
        <p>Mayroon nang account? <a href="login.php" class="login">Mag-log in</a></p>
    </div>
    
  </div>
</form> 
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
