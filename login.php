<?php
session_start();
require_once 'database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (!empty($username) && !empty($password)) {
        // Recupere user
        $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user) {
            
            if (password_verify($password, $user['password'])) {
                if (in_array($user['role'], ['admin' , 'author'])) {
                    // Login success
                    $_SESSION['user_id'] = $user['username'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    if ($user['role'] === 'admin') {
                        header('Location: admin.php');
                        exit;
                    } else {
                        header('Location: author.php');
                        exit;
                    }
                } else {
                    $error = "only for admins and authors";
                }
            } else {
                $error = "incorrect username or password";
            }
        } else {
            $error = "incorrect username or password";
        }
    } else {
        $error = "select all fields";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BlogCMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <!-- Logo et titre -->
            <div class="text-center mb-8">
                <div class="inline-block bg-white p-4 rounded-full shadow-lg mb-4">
                    <i class="fas fa-blog text-5xl text-blue-600"></i>
                </div>
                <h1 class="text-4xl font-bold text-white mb-2">BlogCMS</h1>
            </div>

            <!-- Formulaire de Login -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
                    <i class="fas fa-sign-in-alt mr-2 text-blue-600"></i>Login
                </h2>

                <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-5">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-user mr-2 text-blue-600"></i>Username
                        </label>
                        <input type="text" name="username" required 
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="Entrer your username"
                               value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-lock mr-2 text-blue-600"></i>password
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required 
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                   placeholder="Enter your password">
                            <button type="button" 
                                    class="absolute right-3 top-3 text-gray-500 hover:text-gray-700">
                            </button>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="index.php" class="text-blue-600 hover:text-blue-800 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Home
                    </a>
                </div>

            </div>
        </div>
    </div>

</body>
</html>