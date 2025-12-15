<?php
session_start();
require_once 'database.php';

// Vérifier si l'utilisateur est connecté et est auteur
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['author', 'editor'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// === CREATE - Ajouter un article ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_article'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $status = $_POST['status'];
    $id_categorie = intval($_POST['id_categorie']);
    
    if (!empty($title) && !empty($content) && $id_categorie > 0) {
        $stmt = $conn->prepare("INSERT INTO Article (title, content, status, username, id_categorie) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $content, $status, $_SESSION['user_id'], $id_categorie])) {
            $success = "Article créé avec succès !";
        } else {
            $error = "Erreur lors de la création de l'article.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

// === UPDATE - Modifier un article ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_article'])) {
    $id_article = intval($_POST['id_article']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $status = $_POST['status'];
    $id_categorie = intval($_POST['id_categorie']);
    
    if (!empty($title) && !empty($content) && $id_categorie > 0) {
        $stmt = $conn->prepare("UPDATE Article SET title = ?, content = ?, status = ?, id_categorie = ? WHERE id_article = ? AND username = ?");
        if ($stmt->execute([$title, $content, $status, $id_categorie, $id_article, $_SESSION['user_id']])) {
            $success = "Article modifié avec succès !";
        } else {
            $error = "Erreur lors de la modification de l'article.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

// === DELETE - Supprimer un article ===
if (isset($_GET['delete'])) {
    $id_article = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM Article WHERE id_article = ? AND username = ?");
    if ($stmt->execute([$id_article, $_SESSION['user_id']])) {
        $success = "Article supprimé avec succès !";
    } else {
        $error = "Erreur lors de la suppression de l'article.";
    }
}

// === READ - Récupérer les articles de l'auteur ===
$stmt = $conn->prepare("SELECT * FROM Article WHERE username = ? ORDER BY date_creation DESC");
$stmt->execute([$_SESSION['user_id']]);
$articles = $stmt->fetchAll();

// Récupérer les catégories
$categories = $conn->query("SELECT * FROM categorie ORDER BY nom_categorie")->fetchAll();

// Récupérer l'article à modifier si demandé
$article_to_edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM Article WHERE id_article = ? AND username = ?");
    $stmt->execute([intval($_GET['edit']), $_SESSION['user_id']]);
    $article_to_edit = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Auteur - BlogCMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-md">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-pen-fancy text-3xl text-blue-600"></i>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Espace Auteur</h1>
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
        <!-- Messages -->
        <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- Statistiques de l'auteur -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm mb-1">Mes Articles</p>
                        <p class="text-4xl font-bold"><?= count($articles) ?></p>
                    </div>
                    <i class="fas fa-newspaper text-5xl opacity-30"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm mb-1">Publiés</p>
                        <p class="text-4xl font-bold">
                            <?= count(array_filter($articles, fn($a) => $a['status'] === 'published')) ?>
                        </p>
                    </div>
                    <i class="fas fa-check-circle text-5xl opacity-30"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm mb-1">Total Vues</p>
                        <p class="text-4xl font-bold">
                            <?= array_sum(array_column($articles, 'view_count')) ?>
                        </p>
                    </div>
                    <i class="fas fa-eye text-5xl opacity-30"></i>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Formulaire CREATE/UPDATE -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-4">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-<?= $article_to_edit ? 'edit' : 'plus-circle' ?> mr-2"></i>
                        <?= $article_to_edit ? 'Modifier l\'Article' : 'Nouvel Article' ?>
                    </h2>

                    <form method="POST" action="">
                        <?php if ($article_to_edit): ?>
                            <input type="hidden" name="id_article" value="<?= $article_to_edit['id_article'] ?>">
                        <?php endif; ?>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Titre *</label>
                            <input type="text" name="title" required maxlength="50"
                                   value="<?= $article_to_edit ? htmlspecialchars($article_to_edit['title']) : '' ?>"
                                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Titre de l'article">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Contenu *</label>
                            <textarea name="content" required rows="8"
                                      class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Contenu de l'article..."><?= $article_to_edit ? htmlspecialchars($article_to_edit['content']) : '' ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Catégorie *</label>
                            <select name="id_categorie" required
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionnez une catégorie</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id_categorie'] ?>"
                                            <?= ($article_to_edit && $article_to_edit['id_categorie'] == $cat['id_categorie']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nom_categorie']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2">Statut *</label>
                            <select name="status" required
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="draft" <?= ($article_to_edit && $article_to_edit['status'] === 'draft') ? 'selected' : '' ?>>
                                    Brouillon
                                </option>
                                <option value="published" <?= ($article_to_edit && $article_to_edit['status'] === 'published') ? 'selected' : '' ?>>
                                    Publié
                                </option>
                                <option value="archived" <?= ($article_to_edit && $article_to_edit['status'] === 'archived') ? 'selected' : '' ?>>
                                    Archivé
                                </option>
                            </select>
                        </div>

                        <div class="flex space-x-2">
                            <?php if ($article_to_edit): ?>
                                <button type="submit" name="update_article"
                                        class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                                    <i class="fas fa-save mr-2"></i>Modifier
                                </button>
                                <a href="author.php"
                                   class="flex-1 bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700 transition text-center">
                                    <i class="fas fa-times mr-2"></i>Annuler
                                </a>
                            <?php else: ?>
                                <button type="submit" name="create_article"
                                        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-plus mr-2"></i>Créer l'Article
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des articles READ -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-list mr-2"></i>Mes Articles (<?= count($articles) ?>)
                    </h2>

                    <?php if (empty($articles)): ?>
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-inbox text-6xl mb-4"></i>
                            <p class="text-lg">Aucun article pour le moment</p>
                            <p class="text-sm">Créez votre premier article pour commencer !</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach($articles as $article): ?>
                                <div class="border rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-xl font-bold text-gray-800 mb-2">
                                                <?= htmlspecialchars($article['title']) ?>
                                            </h3>
                                            <p class="text-gray-600 mb-3 line-clamp-2">
                                                <?= htmlspecialchars(substr($article['content'], 0, 150)) ?>...
                                            </p>
                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                <span>
                                                    <i class="far fa-calendar mr-1"></i>
                                                    <?= date('d/m/Y', strtotime($article['date_creation'])) ?>
                                                </span>
                                                <span>
                                                    <i class="far fa-eye mr-1"></i>
                                                    <?= $article['view_count'] ?> vues
                                                </span>
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                    <?php
                                                        echo $article['status'] === 'published' ? 'bg-green-100 text-green-800' : 
                                                             ($article['status'] === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800');
                                                    ?>">
                                                    <?= ucfirst($article['status']) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col space-y-2 ml-4">
                                            <a href="?edit=<?= $article['id_article'] ?>"
                                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-center">
                                                <i class="fas fa-edit mr-1"></i>Modifier
                                            </a>
                                            <a href="?delete=<?= $article['id_article'] ?>"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')"
                                               class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-center">
                                                <i class="fas fa-trash mr-1"></i>Supprimer
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>