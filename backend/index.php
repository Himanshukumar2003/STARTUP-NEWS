<?php
// error_reporting(0);
session_start();
include 'db.php';
$alertmsg = '';

if (isset($_POST['submit'])) {
    extract($_POST);
    $res = mysqli_query($con, "SELECT * FROM `user` WHERE `username`='$username'");
    if (mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $verify = password_verify($password, $row['password']);
        if ($verify == 1) {
            $_SESSION['user'] = $row['userid'];
            $_SESSION['type'] = 'Admin';
            header('location:dashboard.php');
        } else {
            $alertmsg = '<sl-alert variant="danger" open duration="1500" closable>
                    <span class="mdi mdi-alpha-x-circle-outline"></span>
                    Wrong Password
                  </sl-alert>';
        }
    } else {
        $alertmsg = '<sl-alert variant="danger" open duration="1500" closable>
                    <span class="mdi mdi-alpha-x-circle-outline"></span>
                    User With This Username Does Not Exists
                  </sl-alert>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AILC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=DM+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">
</head>

<body class="min-h-screen flex items-center justify-center" style="background-color: #c4a574;">

    <!-- Login Card -->
    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h2>
            <p class="text-gray-600 text-sm">Please sign in to your admin account</p>
        </div>

        <!-- Alert Message -->
        <div class="mb-4 text-center">
            <?= $alertmsg ?>
        </div>

        <form class="space-y-6" method="POST">
            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                <input type="text" id="username" name="username"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="Enter your username" required>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" id="password" name="password"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="••••••••" required>
            </div>

            <!-- Submit -->
            <button type="submit" name="submit"
                class="w-full text-white py-3 px-4 rounded-xl font-semibold text-lg shadow-lg transition-all duration-200"
                style="background-color:#c4a574">
                Sign In
            </button>
        </form>

        <p class="text-center text-gray-500 text-xs mt-6">© <?= date('Y') ?> AILC. All rights reserved.</p>
    </div>

</body>

</html>