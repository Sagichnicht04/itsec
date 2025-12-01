<?php

// Server name must be localhost
$servername = "db";

// In my case, user name will be root
$username = "root";

// Password is empty
$password = "notSecureChangeMe";

// Creating a connection
$conn = new mysqli($servername, 
            $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failure: " 
        . $conn->connect_error);
} 
 
// Creating a database named geekdata
$sql = "CREATE DATABASE IF NOT EXISTS password_demo";
if ($conn->query($sql) === TRUE) {
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select database
$conn->select_db("password_demo");

// Create table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Closing connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Security Demo - Sign Up</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2196F3 0%, #00BCD4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #333;
            font-size: 2.2em;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
            font-size: 1.1em;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 1.1em;
        }
        
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s ease;
        }
        
        input[type="email"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #2196F3;
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
        }
        
        .password-display {
            margin-top: 10px;
            padding: 12px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            color: #495057;
            word-break: break-all;
            min-height: 40px;
        }
        
        .password-label {
            font-size: 0.9em;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #2196F3 0%, #00BCD4 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.2em;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(33, 150, 243, 0.3);
        }
        
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            color: #856404;
            font-size: 0.95em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Sign Up</h1>
            <p>Welcome to our <b>Super Secure Service</b></p>
        </div>
        
        <div class="warning">
            This demo shows passwords in plain text. Never do this in production!
        </div>
        
        <form method="POST" action="explain.php" id="form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <label for="passwordrepeat">Repeat Password</label>
                <input type="password" id="passwordrepeat" name="passwordrepeat" required>
                
                <div class="password-label">Password in clear text:</div>
                <div class="password-display" id="passwordDisplay"></div>
            </div>

            <div class="warning" id="error" hidden>
            </div>
            
            <button type="submit" class="submit-btn">Sign Up</button>
        </form>
    </div>
    
    <script>
        document.getElementById('password').addEventListener('input', function() {
            document.getElementById("error").textContent = ""
            document.getElementById("error").setAttribute("hidden","hidden")
            const passwordDisplay = document.getElementById('passwordDisplay');
            const password = this.value;
            passwordDisplay.textContent = password || '';
        });

        document.getElementById('form').addEventListener('submit', function(e) {
        e.preventDefault(); // prevent form submission
        
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('passwordrepeat').value;
        const errorMsg = document.getElementById("error")
        errorMsg.removeAttribute("hidden")

        // Password policy
        const minLength = 8;

        // Check password match
        if (password !== confirmPassword) {
            errorMsg.textContent = "Passwords do not match.";
            return;
        }

        // Check password policy
        if (password.length < minLength) {
            errorMsg.textContent = `Password must be at least ${minLength} characters long.`;
            return;
        }

        // If all checks pass, submit the form
        errorMsg.textContent = "";
        this.submit();
    });



    </script>
</body>
</html>

