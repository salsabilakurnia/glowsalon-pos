<?php
session_start();
include 'koneksi.php';

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Bypass aman untuk demo / cek database
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['user'] = $username;
        header("Location: kasir.php");
        exit();
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - GlowSalon POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 flex min-h-screen items-center justify-center p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl">
        <h2 class="text-center text-3xl font-bold text-pink-600">GlowSalon POS</h2>
        <p class="mb-6 text-center text-sm text-gray-500">Silakan Login Terlebih Dahulu</p>
        
        <?php if ($error): ?>
            <div class="mb-4 rounded-lg bg-red-100 p-3 text-sm text-red-600 font-semibold text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium">Username</label>
                <input type="text" name="username" required class="w-full rounded-lg border p-2 focus:outline-pink-500" value="admin">
            </div>
            <div>
                <label class="block text-sm font-medium">Password</label>
                <input type="password" name="password" required class="w-full rounded-lg border p-2 focus:outline-pink-500" value="admin123">
            </div>
            <button type="submit" name="login" class="w-full rounded-lg bg-pink-500 py-2.5 font-semibold text-white hover:bg-pink-600">Masuk Sistem</button>
        </form>
    </div>
</body>
</html>