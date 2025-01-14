<?php
session_start();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db.php'; // Ensure you are connected to the database

    // Manual Login
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $captcha = $_POST['captcha'];
        $remember = isset($_POST['remember']); // Check "Remember Me" checkbox

        // Verify CAPTCHA
        if ($captcha !== $_SESSION["captcha_code"]) {
            $error = "Kode CAPTCHA salah!";
        } else {
            // Check username in the database
            $query = "SELECT * FROM users WHERE username = '$username'";
            $result = mysqli_query($conn, $query);
            $user = mysqli_fetch_assoc($result);

            // Check if user exists
            if ($user) {
                // Check if the user is banned
                if ($user['banned_until'] && new DateTime() < new DateTime($user['banned_until'])) {
                    $error = "Silahkan Untuk Mengganti Password, coba lagi setelah 15 menit";
                } else {
                    // Check password
                    if (password_verify($password, $user['password'])) {
                        // Reset failed attempts on successful login
                        $query = "UPDATE users SET failed_attempts = 0, banned_until = NULL WHERE username = '$username'";
                        mysqli_query($conn, $query);

                        $_SESSION['username'] = $username;

                        // Create cookies if "Remember Me" is checked
                        if ($remember) {
                            setcookie('username', $username, time() + (86400 * 30), '/'); // Cookie valid for 30 days
                            setcookie('session_id', session_id(), time() + (86400 * 30), '/');
                        }

                        header("Refresh: 0; url=index.php"); // Redirect after 2 seconds
                    } else {
                        // Increment failed attempts
                        $failed_attempts = $user['failed_attempts'] + 1;

                        // Check if failed attempts exceed limit
                        if ($failed_attempts >= 5) {
                            // Ban the user for 15 minutes
                            $banned_until = (new DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s');
                            $query = "UPDATE users SET failed_attempts = $failed_attempts, banned_until = '$banned_until' WHERE username = '$username'";
                            mysqli_query($conn, $query);
                            $error = "Silahkan coba lagi selama 15 menit karena terlalu banyak percobaan login yang gagal.";
                        } else {
                            // Update failed attempts
                            $query = "UPDATE users SET failed_attempts = $failed_attempts WHERE username = '$username'";
                            mysqli_query($conn, $query);
                            $error = "Username atau password salah! Percobaan gagal: $failed_attempts";
                        }
                    }
                }
            } else {
                $error = "Username atau password salah!";
            }
        }
    }

    // Google Login handling remains unchanged
    if (isset($_POST['id_token'])) {
        include 'google-verify.php'; // File to verify token
        $id_token = $_POST['id_token'];
        $googleUser  = verifyGoogleToken($id_token); // Verify Google token

        if ($googleUser) {
            $email = $googleUser['email'];
            $name = $googleUser['name'];

            // Check if user already exists in the database
            $query = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                $_SESSION['username'] = $email;
            } else {
                // Add new user to the database if not exists
                $query = "INSERT INTO users (username, email) VALUES ('$name', '$email')";
                mysqli_query($conn, $query);
                $_SESSION['username'] = $email;
            }

            $success = "Login berhasil! Selamat datang, " . htmlspecialchars($name) . ".";
            header("Refresh: 2; url=index.php");
        } else {
            $error = "Login dengan Google gagal!";
        }
    }
}

// Auto-login using cookies
if (isset($_COOKIE['username']) && isset($_COOKIE['session_id'])) {
    session_id($_COOKIE['session_id']);
    session_start();
    $_SESSION['username'] = $_COOKIE['username'];
    header("Location: index.php"); // Redirect directly to the main page
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style/login.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>

<body>
    <div class="form">
        <h1 class="login-title">Login</h1>
        <form method="POST" action="">
            <input type="text" name="username" class="login-input" placeholder="Username" required><br>
            <input type="password" id="password" name="password" class="login-input" placeholder="Password" required>
            <div class="password-toggle">
                <input type="checkbox" id="show-password-checkbox">
                <label for="show-password-checkbox">Tampilkan Password</label>
            </div>
            <img src="captcha.php" alt="CAPTCHA" /><br>
            <input type="text" name="captcha" class="login-input" placeholder="Kode CAPTCHA" required><br>
            <label>
                <input type="checkbox" name="remember"> Ingat Saya
            </label><br>
            <input type="submit" value="Login" class="login-button">
        </form>
        <hr>
        <div id="g_id_onload" data-client_id="YOUR_GOOGLE_CLIENT_ID" data-context="signin" data-ux_mode="popup" data-login_uri="" data-auto_prompt="false">
        </div>

        <div class="g_id_signin" data-type="standard" data-shape="rectangular" data-theme="outline" data-text="signin_with" data-size="large" data-logo_alignment="center">
        </div>

        <?php if ($error) : ?>
            <p class="error" style="color: red; text-align: center;"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if ($success) : ?>
            <p class="success" style="color: green; text-align: center;"><?php echo $success; ?></p>
        <?php endif; ?>
        <p class="link">Lupa password? <a href="reset_saja.php">Klik di sini</a></p>
        <p class="link">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>

    <script>
        function handleCredentialResponse(response) {
            const id_token = response.credential;

            // Send token to server for processing
            fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_token: id_token
                    })
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'index.php';
                    } else {
                        alert('Login gagal!');
                    }
                }).catch(err => console.error(err));
        }

        const passwordInput = document.getElementById('password');
        const showPasswordCheckbox = document.getElementById('show-password-checkbox');

        showPasswordCheckbox.addEventListener('change', function() {
            if (this.checked) {
                passwordInput.type = 'text'; // Show password
            } else {
                passwordInput.type = 'password'; // Hide password
            }
        });
    </script>
</body>
</html>