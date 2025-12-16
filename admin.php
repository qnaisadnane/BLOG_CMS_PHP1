<?php
session_start();
require_once 'database.php';

// Verifier si utilisateur est connecte et est admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Nombre total article
$total_articles = $conn->query("SELECT COUNT(*) as total FROM Article")->fetch()['total'];

// Nombre article published
$articles_published = $conn->query("SELECT COUNT(*) as total FROM Article WHERE status = 'published'")->fetch()['total'];

// Nombre d'articles en brouillon
$articles_draft = $conn->query("SELECT COUNT(*) as total FROM Article WHERE status = 'draft'")->fetch()['total'];

// Nombre total de commentaires
$total_comments = $conn->query("SELECT COUNT(*) as total FROM commentaire")->fetch()['total'];

// Nombre total d'utilisateurs
$total_users = $conn->query("SELECT COUNT(*) as total FROM utilisateur")->fetch()['total'];

// Nombre total de catégories
$total_categories = $conn->query("SELECT COUNT(*) as total FROM categorie")->fetch()['total'];

// Somme totale des vues
$total_views = $conn->query("SELECT SUM(view_count) as total FROM Article")->fetch()['total'];

// Moyenne des vues par article
$avg_views = $conn->query("SELECT AVG(view_count) as average FROM Article")->fetch()['average'];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - BlogCMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-md">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-user-shield text-3xl text-purple-600"></i>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
                        <p class="text-sm text-gray-600">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    
                    <a href="logout.php" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-4 py-8">
        <!-- dashboard -->
        <h2 class="text-3xl font-bold text-gray-800 mb-6">📊 Dashboard</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Articles -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm mb-1">Total Articles</p>
                        <p class="text-4xl font-bold"><?= $total_articles ?></p>
                    </div>
                    <i class="fas fa-newspaper text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Articles Published -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm mb-1">Published</p>
                        <p class="text-4xl font-bold"><?= $articles_published ?></p>
                    </div>
                    <i class="fas fa-check-circle text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Draft -->
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm mb-1">Draft</p>
                        <p class="text-4xl font-bold"><?= $articles_draft ?></p>
                    </div>
                    <i class="fas fa-edit text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Total Comments -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm mb-1">Comments</p>
                        <p class="text-4xl font-bold"><?= $total_comments ?></p>
                    </div>
                    <i class="fas fa-comments text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Total Users -->
            <div class="bg-gradient-to-br from-pink-500 to-pink-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-pink-100 text-sm mb-1">Users</p>
                        <p class="text-4xl font-bold"><?= $total_users ?></p>
                    </div>
                    <i class="fas fa-users text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Total Categories -->
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-indigo-100 text-sm mb-1">Categories</p>
                        <p class="text-4xl font-bold"><?= $total_categories ?></p>
                    </div>
                    <i class="fas fa-folder text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Total Views (SUM) -->
            <div class="bg-gradient-to-br from-red-500 to-red-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm mb-1">Total Views</p>
                        <p class="text-4xl font-bold"><?= $total_views ?></p>
                    </div>
                    <i class="fas fa-eye text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Average Views (AVG) -->
            <div class="bg-gradient-to-br from-teal-500 to-teal-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-teal-100 text-sm mb-1">Average Views</p>
                        <p class="text-4xl font-bold"><?= $avg_views ?></p>
                    </div>
                    <i class="fas fa-chart-line text-5xl opacity-30"></i>
                </div>
            </div>
        </div>
        </div>

    </main>
</body>
</html>