<?php
session_start();
require_once 'database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (!empty($username) && !empty($password)) {
        // Récupérer l'utilisateur
        $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user) {
            // DEBUG: Afficher le hash stocké (à retirer en production)
            // echo "Hash dans DB: " . $user['password'] . "<br>";
            // echo "Password entré: " . $password . "<br>";
            
            // Vérifier le mot de passe
            // Le hash dans votre DB est: $2y$10$EixZaYVK1fsbw1ZfbX3OXePaWxn96p36WQoeG6Lruj3vjPGga31lW
            // Ce hash correspond au mot de passe: "secret"
            
            if (password_verify($password, $user['password'])) {
                // Vérifier le rôle (seulement admin et editor/author)
                if (in_array($user['role'], ['admin', 'editor', 'author'])) {
                    // Connexion réussie
                    $_SESSION['user_id'] = $user['username'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    // Redirection selon le rôle
                    if ($user['role'] === 'admin' || $user['role'] === 'editor') {
                        header('Location: admin.php');
                        exit;
                    } else {
                        header('Location: author.php');
                        exit;
                    }
                } else {
                    $error = "Accès refusé. Seuls les administrateurs et auteurs peuvent se connecter.";
                }
            } else {
                $error = "Nom d'utilisateur ou mot de passe incorrect.";
            }
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - BlogCMS</title>
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
                <p class="text-white/80">Connectez-vous à votre espace</p>
            </div>

            <!-- Formulaire de connexion -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
                    <i class="fas fa-sign-in-alt mr-2 text-blue-600"></i>Connexion
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
                            <i class="fas fa-user mr-2 text-blue-600"></i>Nom d'utilisateur
                        </label>
                        <input type="text" name="username" required 
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="Entrez votre nom d'utilisateur"
                               value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-lock mr-2 text-blue-600"></i>Mot de passe
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required 
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                   placeholder="Entrez votre mot de passe">
                            <button type="button" onclick="togglePassword()" 
                                    class="absolute right-3 top-3 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="index.php" class="text-blue-600 hover:text-blue-800 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Retour à l'accueil
                    </a>
                </div>

                <!-- Informations de test -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 font-semibold mb-3 text-center">
                        <i class="fas fa-info-circle mr-2"></i>Comptes de test disponibles :
                    </p>
                    <div class="space-y-2 text-sm">
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <p class="font-semibold text-blue-800">👑 Administrateur</p>
                            <p class="text-gray-700">Username: <span class="font-mono bg-white px-2 py-1 rounded">admin_123</span></p>
                            <p class="text-gray-700">Password: <span class="font-mono bg-white px-2 py-1 rounded">secret</span></p>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-lg">
                            <p class="font-semibold text-purple-800">✍️ Auteur</p>
                            <p class="text-gray-700">Username: <span class="font-mono bg-white px-2 py-1 rounded">marie_47</span></p>
                            <p class="text-gray-700">Password: <span class="font-mono bg-white px-2 py-1 rounded">secret</span></p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-3 text-center">
                        Note: Les abonnés (subscribers) ne peuvent pas se connecter
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>