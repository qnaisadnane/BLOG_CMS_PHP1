<?php
session_start();
require_once 'database.php';

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// === STATISTIQUES AVEC REQUÊTES SQL ===

// Nombre total d'articles

// === STATISTIQUES AVEC REQUÊTES SQL ===

// Nombre total d'articles
$total_articles = $conn->query("SELECT COUNT(*) as total FROM Article")->fetch()['total'];

// Nombre d'articles publiés
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
$total_views = $conn->query("SELECT SUM(view_count) as total FROM Article")->fetch()['total'] ?? 0;

// Moyenne des vues par article
$avg_views = $conn->query("SELECT AVG(view_count) as average FROM Article")->fetch()['average'] ?? 0;

// Maximum de vues
$max_views = $conn->query("SELECT MAX(view_count) as maximum FROM Article")->fetch()['maximum'] ?? 0;

// Minimum de vues
$min_views = $conn->query("SELECT MIN(view_count) as minimum FROM Article")->fetch()['minimum'] ?? 0;

// Article le plus vu
$most_viewed = $conn->query("SELECT * FROM Article ORDER BY view_count DESC LIMIT 1")->fetch();

// Article le plus récent
$latest_article = $conn->query("SELECT * FROM Article ORDER BY date_creation DESC LIMIT 1")->fetch();

// Moyenne de commentaires par article
$avg_comments = $conn->query("
    SELECT AVG(comment_count) as average FROM (
        SELECT COUNT(*) as comment_count FROM commentaire GROUP BY id_article
    ) as subquery
")->fetch()['average'] ?? 0;

// Articles par catégorie
$articles_by_category = $conn->query("
    SELECT c.nom_categorie, COUNT(a.id_article) as total
    FROM categorie c
    LEFT JOIN Article a ON c.id_categorie = a.id_categorie
    GROUP BY c.id_categorie, c.nom_categorie
    ORDER BY total DESC
")->fetchAll();

// Articles par auteur
$articles_by_author = $conn->query("
    SELECT username, COUNT(*) as total
    FROM Article
    GROUP BY username
    ORDER BY total DESC
    LIMIT 5
")->fetchAll();

// Statistiques par statut
$stats_by_status = $conn->query("
    SELECT status, COUNT(*) as total, SUM(view_count) as total_views
    FROM Article
    GROUP BY status
")->fetchAll();

// Derniers commentaires
$recent_comments = $conn->query("
    SELECT * FROM commentaire
    ORDER BY date_creation DESC
    LIMIT 5
")->fetchAll();
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
                        <p class="text-sm text-gray-600">Bienvenue, <?= htmlspecialchars($_SESSION['user_name']) ?></p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-gray-600 hover:text-blue-600 transition">
                        <i class="fas fa-home mr-2"></i>Accueil
                    </a>
                    <a href="logout.php" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-4 py-8">
        <!-- Statistiques principales -->
        <h2 class="text-3xl font-bold text-gray-800 mb-6">📊 Statistiques Générales</h2>
        
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

            <!-- Articles Publiés -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm mb-1">Publiés</p>
                        <p class="text-4xl font-bold"><?= $articles_published ?></p>
                    </div>
                    <i class="fas fa-check-circle text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Brouillons -->
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm mb-1">Brouillons</p>
                        <p class="text-4xl font-bold"><?= $articles_draft ?></p>
                    </div>
                    <i class="fas fa-edit text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Total Commentaires -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm mb-1">Commentaires</p>
                        <p class="text-4xl font-bold"><?= $total_comments ?></p>
                    </div>
                    <i class="fas fa-comments text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Total Utilisateurs -->
            <div class="bg-gradient-to-br from-pink-500 to-pink-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-pink-100 text-sm mb-1">Utilisateurs</p>
                        <p class="text-4xl font-bold"><?= $total_users ?></p>
                    </div>
                    <i class="fas fa-users text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Total Catégories -->
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-indigo-100 text-sm mb-1">Catégories</p>
                        <p class="text-4xl font-bold"><?= $total_categories ?></p>
                    </div>
                    <i class="fas fa-folder text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Total Vues (SUM) -->
            <div class="bg-gradient-to-br from-red-500 to-red-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm mb-1">Total Vues (SUM)</p>
                        <p class="text-4xl font-bold"><?= number_format($total_views) ?></p>
                    </div>
                    <i class="fas fa-eye text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Moyenne Vues (AVG) -->
            <div class="bg-gradient-to-br from-teal-500 to-teal-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-teal-100 text-sm mb-1">Moy. Vues (AVG)</p>
                        <p class="text-4xl font-bold"><?= number_format($avg_views, 1) ?></p>
                    </div>
                    <i class="fas fa-chart-line text-5xl opacity-30"></i>
                </div>
            </div>
        </div>

        <!-- Statistiques MIN/MAX -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-arrow-up text-green-600 mr-2"></i>Max Vues (MAX)
                </h3>
                <p class="text-3xl font-bold text-green-600"><?= number_format($max_views) ?></p>
                <?php if ($most_viewed): ?>
                    <p class="text-sm text-gray-600 mt-2">Article: <?= htmlspecialchars($most_viewed['title']) ?></p>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-arrow-down text-orange-600 mr-2"></i>Min Vues (MIN)
                </h3>
                <p class="text-3xl font-bold text-orange-600"><?= number_format($min_views) ?></p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-comment-dots text-purple-600 mr-2"></i>Moy. Commentaires (AVG)
                </h3>
                <p class="text-3xl font-bold text-purple-600"><?= number_format($avg_comments, 1) ?></p>
            </div>
        </div>

        <!-- Articles par Catégorie -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-folder-open text-blue-600 mr-2"></i>Articles par Catégorie (GROUP BY)
                </h3>
                <div class="space-y-3">
                    <?php foreach($articles_by_category as $cat): ?>
                        <div class="flex items-center justify-between border-b pb-2">
                            <span class="text-gray-700"><?= htmlspecialchars($cat['nom_categorie']) ?></span>
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                <?= $cat['total'] ?> articles
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Top Auteurs -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-trophy text-yellow-600 mr-2"></i>Top 5 Auteurs (GROUP BY + LIMIT)
                </h3>
                <div class="space-y-3">
                    <?php foreach($articles_by_author as $index => $author): ?>
                        <div class="flex items-center justify-between border-b pb-2">
                            <div class="flex items-center">
                                <span class="bg-yellow-500 text-white w-8 h-8 rounded-full flex items-center justify-center mr-3 font-bold">
                                    <?= $index + 1 ?>
                                </span>
                                <span class="text-gray-700"><?= htmlspecialchars($author['username']) ?></span>
                            </div>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                                <?= $author['total'] ?> articles
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Statistiques par Statut -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-pie text-indigo-600 mr-2"></i>Statistiques par Statut (GROUP BY)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php foreach($stats_by_status as $stat): ?>
                    <div class="border rounded-lg p-4">
                        <p class="text-gray-600 text-sm mb-2"><?= htmlspecialchars(ucfirst($stat['status'])) ?></p>
                        <p class="text-2xl font-bold text-gray-800"><?= $stat['total'] ?> articles</p>
                        <p class="text-sm text-gray-500 mt-1"><?= number_format($stat['total_views']) ?> vues totales</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Derniers Commentaires -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-comment-alt text-purple-600 mr-2"></i>Derniers Commentaires (ORDER BY + LIMIT)
            </h3>
            <div class="space-y-3">
                <?php foreach($recent_comments as $comment): ?>
                    <div class="border-l-4 border-purple-500 bg-gray-50 p-4 rounded-r-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-800"><?= htmlspecialchars($comment['author_name']) ?></span>
                            <span class="text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($comment['date_creation'])) ?></span>
                        </div>
                        <p class="text-gray-700 text-sm"><?= htmlspecialchars(substr($comment['contenu'], 0, 100)) ?>...</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</body>
</html>->query("SELECT COUNT(*) as total FROM Article")->fetch()['total'];

// Nombre d'articles publiés
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
$total_views = $conn->query("SELECT SUM(view_count) as total FROM Article")->fetch()['total'] ?? 0;

// Moyenne des vues par article
$avg_views = $conn->query("SELECT AVG(view_count) as average FROM Article")->fetch()['average'] ?? 0;

// Maximum de vues
$max_views = $conn->query("SELECT MAX(view_count) as maximum FROM Article")->fetch()['maximum'] ?? 0;

// Minimum de vues
$min_views = $conn->query("SELECT MIN(view_count) as minimum FROM Article")->fetch()['minimum'] ?? 0;

// Article le plus vu
$most_viewed = $conn->query("SELECT * FROM Article ORDER BY view_count DESC LIMIT 1")->fetch();

// Article le plus récent
$latest_article = $conn->query("SELECT * FROM Article ORDER BY date_creation DESC LIMIT 1")->fetch();

// Moyenne de commentaires par article
$avg_comments = $conn->query("
    SELECT AVG(comment_count) as average FROM (
        SELECT COUNT(*) as comment_count FROM commentaire GROUP BY id_article
    ) as subquery
")->fetch()['average'] ?? 0;

// Articles par catégorie
$articles_by_category = $conn->query("
    SELECT c.nom_categorie, COUNT(a.id_article) as total
    FROM categorie c
    LEFT JOIN Article a ON c.id_categorie = a.id_categorie
    GROUP BY c.id_categorie, c.nom_categorie
    ORDER BY total DESC
")->fetchAll();

// Articles par auteur
$articles_by_author = $conn->query("
    SELECT username, COUNT(*) as total
    FROM Article
    GROUP BY username
    ORDER BY total DESC
    LIMIT 5
")->fetchAll();

// Statistiques par statut
$stats_by_status = $conn->query("
    SELECT status, COUNT(*) as total, SUM(view_count) as total_views
    FROM Article
    GROUP BY status
")->fetchAll();

// Derniers commentaires
$recent_comments = $conn->query("
    SELECT * FROM commentaire
    ORDER BY date_creation DESC
    LIMIT 5
")->fetchAll();
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
                        <p class="text-sm text-gray-600">Bienvenue, <?= htmlspecialchars($_SESSION['user_name']) ?></p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-gray-600 hover:text-blue-600 transition">
                        <i class="fas fa-home mr-2"></i>Accueil
                    </a>
                    <a href="logout.php" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-4 py-8">
        <!-- Statistiques principales -->
        <h2 class="text-3xl font-bold text-gray-800 mb-6">📊 Statistiques Générales</h2>
        
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

            <!-- Articles Publiés -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm mb-1">Publiés</p>
                        <p class="text-4xl font-bold"><?= $articles_published ?></p>
                    </div>
                    <i class="fas fa-check-circle text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Brouillons -->
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm mb-1">Brouillons</p>
                        <p class="text-4xl font-bold"><?= $articles_draft ?></p>
                    </div>
                    <i class="fas fa-edit text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Total Commentaires -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm mb-1">Commentaires</p>
                        <p class="text-4xl font-bold"><?= $total_comments ?></p>
                    </div>
                    <i class="fas fa-comments text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Total Utilisateurs -->
            <div class="bg-gradient-to-br from-pink-500 to-pink-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-pink-100 text-sm mb-1">Utilisateurs</p>
                        <p class="text-4xl font-bold"><?= $total_users ?></p>
                    </div>
                    <i class="fas fa-users text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Total Catégories -->
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-indigo-100 text-sm mb-1">Catégories</p>
                        <p class="text-4xl font-bold"><?= $total_categories ?></p>
                    </div>
                    <i class="fas fa-folder text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Total Vues (SUM) -->
            <div class="bg-gradient-to-br from-red-500 to-red-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm mb-1">Total Vues (SUM)</p>
                        <p class="text-4xl font-bold"><?= number_format($total_views) ?></p>
                    </div>
                    <i class="fas fa-eye text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Moyenne Vues (AVG) -->
            <div class="bg-gradient-to-br from-teal-500 to-teal-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-teal-100 text-sm mb-1">Moy. Vues (AVG)</p>
                        <p class="text-4xl font-bold"><?= number_format($avg_views, 1) ?></p>
                    </div>
                    <i class="fas fa-chart-line text-5xl opacity-30"></i>
                </div>
            </div>
        </div>

        <!-- Statistiques MIN/MAX -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-arrow-up text-green-600 mr-2"></i>Max Vues (MAX)
                </h3>
                <p class="text-3xl font-bold text-green-600"><?= number_format($max_views) ?></p>
                <?php if ($most_viewed): ?>
                    <p class="text-sm text-gray-600 mt-2">Article: <?= htmlspecialchars($most_viewed['title']) ?></p>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-arrow-down text-orange-600 mr-2"></i>Min Vues (MIN)
                </h3>
                <p class="text-3xl font-bold text-orange-600"><?= number_format($min_views) ?></p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-comment-dots text-purple-600 mr-2"></i>Moy. Commentaires (AVG)
                </h3>
                <p class="text-3xl font-bold text-purple-600"><?= number_format($avg_comments, 1) ?></p>
            </div>
        </div>

        <!-- Articles par Catégorie -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-folder-open text-blue-600 mr-2"></i>Articles par Catégorie (GROUP BY)
                </h3>
                <div class="space-y-3">
                    <?php foreach($articles_by_category as $cat): ?>
                        <div class="flex items-center justify-between border-b pb-2">
                            <span class="text-gray-700"><?= htmlspecialchars($cat['nom_categorie']) ?></span>
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                <?= $cat['total'] ?> articles
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Top Auteurs -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-trophy text-yellow-600 mr-2"></i>Top 5 Auteurs (GROUP BY + LIMIT)
                </h3>
                <div class="space-y-3">
                    <?php foreach($articles_by_author as $index => $author): ?>
                        <div class="flex items-center justify-between border-b pb-2">
                            <div class="flex items-center">
                                <span class="bg-yellow-500 text-white w-8 h-8 rounded-full flex items-center justify-center mr-3 font-bold">
                                    <?= $index + 1 ?>
                                </span>
                                <span class="text-gray-700"><?= htmlspecialchars($author['username']) ?></span>
                            </div>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                                <?= $author['total'] ?> articles
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Statistiques par Statut -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-pie text-indigo-600 mr-2"></i>Statistiques par Statut (GROUP BY)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php foreach($stats_by_status as $stat): ?>
                    <div class="border rounded-lg p-4">
                        <p class="text-gray-600 text-sm mb-2"><?= htmlspecialchars(ucfirst($stat['status'])) ?></p>
                        <p class="text-2xl font-bold text-gray-800"><?= $stat['total'] ?> articles</p>
                        <p class="text-sm text-gray-500 mt-1"><?= number_format($stat['total_views']) ?> vues totales</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Derniers Commentaires -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-comment-alt text-purple-600 mr-2"></i>Derniers Commentaires (ORDER BY + LIMIT)
            </h3>
            <div class="space-y-3">
                <?php foreach($recent_comments as $comment): ?>
                    <div class="border-l-4 border-purple-500 bg-gray-50 p-4 rounded-r-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-800"><?= htmlspecialchars($comment['author_name']) ?></span>
                            <span class="text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($comment['date_creation'])) ?></span>
                        </div>
                        <p class="text-gray-700 text-sm"><?= htmlspecialchars(substr($comment['contenu'], 0, 100)) ?>...</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</body>
</html>