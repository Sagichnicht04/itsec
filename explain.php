<?php
// Database configuration
$servername = 'db';
$dbname = 'password_demo';
$username = 'root';
$password = 'notSecureChangeMe';


$sha1Password = strtoupper(sha1($_POST['password'])); // HIBP expects uppercase SHA-1
$prefix = substr($sha1Password, 0, 5);       // First 5 chars
$suffix = substr($sha1Password, 5);          // Remaining chars
$url = "https://api.pwnedpasswords.com/range/" . $prefix;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
$lines = explode("\n", $result);
$found = false;

foreach ($lines as $line) {
    list($hashSuffix, $count) = explode(":", $line);
    if ($hashSuffix === $suffix) {
        $found = true;
        break;
    }
}


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
$pepper = "Louvre"; // Secret pepper
$salt = bin2hex(random_bytes(16)); // Generate random salt
$pepperedPassword = $plainPassword . $pepper;
$saltedPepperedPassword = $pepperedPassword . $salt;


$md5_start = microtime(true); 
$md5Hash = md5($saltedPepperedPassword);
$md5_time = (microtime(true) - $md5_start) * 1000;

usleep(100000);

$sha1_start = microtime(true); 
$sha1_hash = hash('sha1', $saltedPepperedPassword);
$sha1_time = (microtime(true) - $sha1_start) * 1000;

usleep(100000);

$sha256_start = microtime(true); 
$sha256_hash = hash('sha256', $saltedPepperedPassword);
$sha256_time = (microtime(true) - $sha256_start) * 1000;

usleep(100000);

$sha512_start = microtime(true); 
$sha512_hash = hash('sha512', $saltedPepperedPassword);
$sha512_time = (microtime(true) - $sha512_start) * 1000;

usleep(100000);

// Strong hashing (Argon2ID)
$argon2_start = microtime(true); 
$argon2Hash = password_hash($saltedPepperedPassword, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,
    'time_cost' => 4,
    'threads' => 3
]);
$argon2_time = (microtime(true) - $argon2_start) * 1000;


// Store in database
$stmt = $conn->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
if ($stmt) {
    $stmt->bind_param("ss", $email, $argon2Hash);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
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
            background: linear-gradient(135deg, #2196F3 0%, #00BCD4 100%);
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
            background: linear-gradient(135deg, #2196F3 0%, #00BCD4 100%);
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
            border-color: #2196F3;
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.1);
        }
        
        .step-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .step-number {
            display: inline-block;
            background: #2196F3;
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
            background: linear-gradient(135deg, #2196F3 0%, #00BCD4 100%);
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
            <h1>Password Security Process</h1>
            <p>Step-by-step demonstration of secure password handling</p>
        </div>
        
<!-- Step 1: Original Password -->
            <div class="step">
                <div class="step-header">
                    <span class="step-number">1</span>
                    <span class="step-title">Original Password (Plain Text)</span>
                </div>
                <div class="step-content">
                    <p><strong>User Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <div class="code-block"><?php echo htmlspecialchars($plainPassword); ?></div>
                    <div class="result">
                        HTTPS: Only client and server can see the password in plain text. 
                    </div>
                </div>
            </div>

        <div class="content">
            <div class="step">
                <div class="step-header">
                    <span class="step-number">2</span>
                    <span class="step-title">Breach Check</span>
                </div>
                <div class="step-content">
                    <div class="code-block"><?php echo "Clear Text: ".htmlspecialchars($plainPassword); ?></div>
                    <div class="code-block"><?php echo "Sha1 hash: ".$sha1Password; ?></div>
                    <div class="code-block"><?php echo "Sha1 prefix: ".$prefix; ?></div>
                    <div class="code-block"> API response: <br> <?php 
                        $temp = 0;
                        foreach ($lines as $line) {
                            $temp = $temp+1;
                            if($temp < 5){
                                echo $prefix . $line."<br>";
                            }
                            else{
                                if(str_contains($line, $suffix)){
                                    echo $prefix . $line."<br>";
                                }
                            }
                        }
                        echo "...";
                    ?></div>
                     <?php 
                        if (!$found) {
                            echo '<div class="result">
                                Password not found in breaches. Safe to use.
                            </div>';
                        }
                        else{
                            echo '<div class="warning">
                                Password breached. Do not use.
                            </div>';
                        }
                    ?>
                </div>
            </div>

            

            <!-- Step 2: Peppering -->
            <div class="step">
                <div class="step-header">
                    <span class="step-number">3</span>
                    <span class="step-title">Add Pepper (Secret Key)</span>
                </div>
                <div class="step-content">
                    <p><strong>Pepper:</strong> A secret string stored securely on the server (not in database)</p>
                    <div class="code-block">Pepper: <?php echo htmlspecialchars($pepper); ?></div>
                    <p><strong>Password + Pepper:</strong></p>
                    <div class="code-block"><?php echo htmlspecialchars($pepperedPassword); ?></div>
                    <div class="result">
                        Even if database is leaked, attacker needs the pepper to crack passwords.
                    </div>
                </div>
            </div>

            <!-- Step 3: Salting -->
            <div class="step">
                <div class="step-header">
                    <span class="step-number">4</span>
                    <span class="step-title">Add Salt (Random Value)</span>
                </div>
                <div class="step-content">
                    <p><strong>Salt:</strong> A unique random value generated for each password</p>
                    <div class="code-block">Salt: <?php echo htmlspecialchars($salt); ?></div>
                    <p><strong>Password + Pepper + Salt:</strong></p>
                    <div class="code-block"><?php echo htmlspecialchars($saltedPepperedPassword); ?></div>
                    <div class="result">
                        Ensures identical passwords have different hashes.
                    </div>
                </div>
            </div>

            <!-- Step 4: Hashing Comparison -->
            <div class="step">
                <div class="step-header">
                    <span class="step-number">5</span>
                    <span class="step-title">Hashing Algorithms Comparison</span>
                </div>
                <div class="step-content">
                    <div class="security-comparison">
                        <div class="security-bad">
                            <h3>MD5</h3>
                            <p><strong>Speed in ms:</strong> <?php echo $md5_time; ?></p>
                            <div class="code-block"><?php echo $md5Hash; ?></div>
                        </div>
                        
                        <div class="security-bad">
                            <h3>SHA-256</h3>
                            <p><strong>Speed in ms:</strong> <?php echo $sha256_time; ?></p>
                            <div class="code-block"><?php echo $sha256_hash; ?></div>
                        </div>
                    </div>
                    <div class="security-comparison">
                        <div class="security-bad">
                            <h3>SHA-512</h3>
                            <!-- Gleich schnell oder schneller wie 256, da auf 64-Bit Systeme ausgelegt. SHA256 nur auf 32 Bit -->
                            <p><strong>Speed in ms:</strong> <?php echo $sha512_time; ?></p>
                            <div class="code-block"><?php echo $sha512_hash; ?></div>
                        </div>
                        
                        <div class="security-good">
                            <h3>Argon2ID</h3>
                            <p><strong>Speed in ms:</strong> <?php echo $argon2_time; ?></p>
                            <div class="code-block" style="font-size: 0.8em;"><?php echo $argon2Hash; ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 5: Database Storage -->
            <div class="step">
                <div class="step-header">
                    <span class="step-number">6</span>
                    <span class="step-title">Result</span>
                </div>
                <div class="step-content">
                    <p><strong>Data:</strong></p>
                    <div class="code-block">
                        <ul>
                            <li><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></li>
                            <li><strong>Password Hash:</strong><?php echo $argon2Hash; ?>/li>
                        </ul>
                    </div>
                </div>
            </div>

            <a onclick="showSteps()" class="back-btn", id="next">Next</a>
        </div>
    </div>

    <script>
    var step = 0

    function showSteps(){
        if(step == 6){
            document.location.href = "http://" + window.location.host.replace("8081","8080")
        }
        step = step + 1
        const divs = document.querySelectorAll('.step');

        // Loop through divs starting from index 3 (elements later than index 2)
        for (let i = step; i < divs.length; i++) {
            divs[i].setAttribute("hidden","hidden")
        }
        for (let i = 0; i < step; i++) {
            divs[i].removeAttribute("hidden")
        }  
        window.scrollBy({
            top: 700,      // Amount to scroll vertically
            behavior: 'smooth' // Smooth scrolling
        });
        if(step == 6){
            document.getElementById("next").innerHTML = "Save to database and view in PHPMyAdmin"
        }
    }
    showSteps()
    </script>

</body>
</html>