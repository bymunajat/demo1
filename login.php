<?php
session_start();

// Kalau sudah login, langsung ke dashboard
if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

// Proses login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // DEMO ACCOUNT (hardcoded)
    if ($email === "admin@example.com" && $password === "admin123") {
        $_SESSION['admin'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0f172a] min-h-screen flex items-center justify-center text-gray-200">

<div class="w-full max-w-md bg-[#020617] border border-gray-800 rounded-xl p-8">
    <h1 class="text-2xl font-bold text-green-500 text-center">Admin Login</h1>
    <p class="text-sm text-gray-400 text-center mt-2">Demo Access</p>

    <?php if ($error): ?>
        <div class="bg-red-500/10 text-red-400 text-sm p-3 rounded mt-4">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="post" class="space-y-4 mt-6">
        <div>
            <label class="text-sm text-gray-400">Email</label>
            <input type="email" name="email" required
                class="w-full px-4 py-2 rounded bg-[#0f172a] border border-gray-700">
        </div>

        <div>
            <label class="text-sm text-gray-400">Password</label>
            <input type="password" name="password" required
                class="w-full px-4 py-2 rounded bg-[#0f172a] border border-gray-700">
        </div>

        <button class="w-full bg-green-600 hover:bg-green-500 py-2 rounded">
            Sign In
        </button>
    </form>

    <p class="text-xs text-gray-500 mt-6 text-center">
        Demo: admin@example.com / admin123
    </p>
</div>

</body>
</html>
