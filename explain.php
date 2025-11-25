<?php
// Database configuration
$servername = 'db';
$dbname = 'password_demo';
$username = 'root';
$password = 'notSecureChangeMe';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failure: " . $conn->connect_error);
}

// Get form data
$email = $_POST['email'] ?? '';
$plainPassword = $_POST['password'] ?? '';

// Security processing steps
$pepper = "MySecretPepper2024!"; // Secret pepper
$salt = bin2hex(random_bytes(16)); // Generate random salt
$pepperedPassword = $plainPassword . $pepper;
$saltedPepperedPassword = $pepperedPassword . $salt;

// Strong hashing (Argon2ID)
$argon2Hash = password_hash($saltedPepperedPassword, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,
    'time_cost' => 4,
    'threads' => 3
]);

// Store in database
$stmt = $conn->prepare("INSERT INTO users (email, password_hash, salt) VALUES (?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("sss", $email, $argon2Hash, $salt);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "User stored successfully.";
    } else {
        echo "Failed to store user.";
    }
    $stmt->close();
} else {
    echo "Database error: " . $conn->error;
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Security Process - Step by Step</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .content {
            padding: 30px;
        }
        
        .step {
            margin-bottom: 30px;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .step:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
        }
        
        .step-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .step-number {
            display: inline-block;
            background: #667eea;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            font-weight: bold;
            margin-right: 15px;
        }
        
        .step-title {
            font-size: 1.4em;
            color: #333;
            display: inline;
        }
        
        .step-content {
            padding: 20px;
        }
        
        .code-block {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            word-break: break-all;
            margin: 10px 0;
            overflow-x: auto;
        }
        
        .result {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            color: #155724;
        }
        
        .warning {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            color: #721c24;
        }
        
        .security-comparison {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        
        .security-bad {
            background: #f8d7da;
            border: 2px solid #dc3545;
            border-radius: 10px;
            padding: 20px;
        }
        
        .security-good {
            background: #d4edda;
            border: 2px solid #28a745;
            border-radius: 10px;
            padding: 20px;
        }
        
        .back-btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
            transition: transform 0.2s ease;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔒 Password Security Process</h1>
            <p>Step-by-step demonstration of secure password handling</p>
        </div>
        
        <div class="content">
            <!-- Step 1: Original Password -->
            <div class="step">
                <div class="step-header">
                    <span class="step-number">1</span>
                    <span class="step-title">Original Password (Plain Text)</span>
                </div>
                <div class="step-content">
                    <p><strong>User Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <div class="code-block"><?php echo htmlspecialchars($plainPassword); ?></div>
                    <div class="warning">
                        <strong>⚠️ Security Risk:</strong> Never store passwords in plain text! Anyone with database access can see all passwords.
                    </div>
                </div>
            </div>

            <!-- Step 2: Peppering -->
            <div class="step">
                <div class="step-header">
                    <span class="step-number">2</span>
                    <span class="step-title">Add Pepper (Secret Key)</span>
                </div>
                <div class="step-content">
                    <p><strong>Pepper:</strong> A secret string stored securely on the server (not in database)</p>
                    <div class="code-block">Pepper: <?php echo htmlspecialchars($pepper); ?></div>
                    <p><strong>Password + Pepper:</strong></p>
                    <div class="code-block"><?php echo htmlspecialchars($pepperedPassword); ?></div>
                    <div class="result">
                        <strong>✅ Security Benefit:</strong> Even if database is compromised, attacker needs the pepper to crack passwords.
                    </div>
                </div>
            </div>

            <!-- Step 3: Salting -->
            <div class="step">
                <div class="step-header">
                    <span class="step-number">3</span>
                    <span class="step-title">Add Salt (Random Value)</span>
                </div>
                <div class="step-content">
                    <p><strong>Salt:</strong> A unique random value generated for each password</p>
                    <div class="code-block">Salt: <?php echo htmlspecialchars($salt); ?></div>
                    <p><strong>Password + Pepper + Salt:</strong></p>
                    <div class="code-block"><?php echo htmlspecialchars($saltedPepperedPassword); ?></div>
                    <div class="result">
                        <strong>✅ Security Benefit:</strong> Prevents rainbow table attacks and ensures identical passwords have different hashes.
                    </div>
                </div>
            </div>

            <!-- Step 4: Hashing Comparison -->
            <div class="step">
                <div class="step-header">
                    <span class="step-number">4</span>
                    <span class="step-title">Hashing Algorithms Comparison</span>
                </div>
                <div class="step-content">
                    <div class="security-comparison">
                        <div class="security-bad">
                            <h3>❌ MD5 (Insecure)</h3>
                            <p><strong>Algorithm:</strong> MD5</p>
                            <p><strong>Speed:</strong> Very Fast (Bad for passwords)</p>
                            <p><strong>Hash Length:</strong> 32 characters</p>
                            <div class="code-block"><?php echo $md5Hash; ?></div>
                            <p><strong>Problems:</strong></p>
                            <ul>
                                <li>Too fast - enables brute force attacks</li>
                                <li>Cryptographically broken</li>
                                <li>Vulnerable to collision attacks</li>
                            </ul>
                        </div>
                        
                        <div class="security-good">
                            <h3>✅ Argon2ID (Secure)</h3>
                            <p><strong>Algorithm:</strong> Argon2ID</p>
                            <p><strong>Speed:</strong> Deliberately Slow</p>
                            <p><strong>Memory Cost:</strong> 64MB</p>
                            <div class="code-block" style="font-size: 0.8em;"><?php echo $argon2Hash; ?></div>
                            <p><strong>Advantages:</strong></p>
                            <ul>
                                <li>Memory-hard function</li>
                                <li>Configurable time/memory costs</li>
                                <li>Resistant to GPU/ASIC attacks</li>
                                <li>Winner of password hashing competition</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 5: Database Storage -->
            <div class="step">
                <div class="step-header">
                    <span class="step-number">5</span>
                    <span class="step-title">Database Storage</span>
                </div>
                <div class="step-content">
                    <?php if ($dbStored): ?>
                        <div class="result">
                            <strong>✅ Successfully stored in database!</strong>
                        </div>
                        <p><strong>Stored Data:</strong></p>
                        <ul>
                            <li><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></li>
                            <li><strong>Password Hash (Argon2ID):</strong> <div class="code-block" style="font-size: 0.8em;"><?php echo $argon2Hash; ?></div></li>
                            <li><strong>Salt:</strong> <div class="code-block"><?php echo htmlspecialchars($salt); ?></div></li>
                        </ul>
                        <div class="result">
                            <strong>🔒 Security Achievement:</strong> Original password is completely unrecoverable from stored data!
                        </div>
                    <?php else: ?>
                        <div class="warning">
                            <strong>❌ Database Error:</strong> <?php echo htmlspecialchars($dbError); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <a href="signup.php" class="back-btn">← Back to Sign Up</a>
        </div>
    </div>
</body>
</html>