<?php
session_start();
require_once 'database.php';

$success = '';
$error = '';

$stmt = $conn->query("SELECT * FROM article WHERE status = 'published'");
$articles = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $author_name = $_POST['author_name'];
    $contenu = $_POST['contenu'];
    $id_article = $_POST['id_article'];
    
    if (!empty($author_name) && !empty($contenu) && $id_article > 0) {
        $stmt = $conn->prepare("INSERT INTO commentaire (contenu, author_name, id_article) VALUES (?, ?, ?)");
        if ($stmt->execute([$contenu, $author_name, $id_article])) {
            $success = "Comment created ";
        } else {
            $error = "Erreur";
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
    <title>BlogCMS - Votre Blog Professionnel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
    <div class="flex items-center space-x-4">
        <i class="fas fa-pen-fancy text-3xl text-blue-600"></i>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Author</h1>
            <p class="text-sm text-gray-600">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></p>
        </div>
    </div>
    
    <!-- Lien "Article" ajouté au centre -->
    <div class="flex justify-center">
        <a href="author.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-house mr-2"></i>Home
        </a>
    </div>
    
    <div class="flex items-center space-x-4">
        <a href="logout.php" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-red-700 transition">
            <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
    </div>
</div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-5xl font-bold mb-4">Welcome to BlogCMS</h2>
            <p class="text-xl mb-8">Discover Beautifuls Articles And Share Your Comment</p>
            
        </div>
    </section>

    <!-- Messages notification -->
    <?php if (!empty($success)): ?>
    <div class="container mx-auto px-4 mt-6">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
    <div class="container mx-auto px-4 mt-6">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Articles Section -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <h3 class="text-3xl font-bold text-center mb-8 text-gray-800">All Articles</h3>
            
           
            <?php if (!empty($articles)): ?>
                <div class="space-y-8">
                    <?php foreach($articles as $article): ?>
                        
                        <?php
                        $stmt_comments = $conn->prepare("SELECT * FROM commentaire WHERE id_article = ? ORDER BY date_creation DESC");
                        $stmt_comments->execute([$article['id_article']]);
                        $comments = $stmt_comments->fetchAll();
                        ?>

                        <article class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <!-- header articles -->
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-8 text-white">
                                <h2 class="text-3xl font-bold mb-2"><?= htmlspecialchars($article['title']) ?></h2>
                                <div class="flex items-center space-x-6 text-sm">
                                    <span><i class="far fa-user mr-2"></i><?= htmlspecialchars($article['username']) ?></span>
                                    <span><i class="far fa-calendar mr-2"></i><?= date('d/m/Y', strtotime($article['date_creation'])) ?></span>
                                    <span><i class="far fa-eye mr-2"></i><?= $article['view_count'] ?> vues</span>
                                    <span><i class="far fa-comments mr-2"></i><?= count($comments) ?> commentaire(s)</span>
                                </div>
                            </div>

                            <!-- content -->
                            <div class="p-8">
                                <div class="text-gray-700 text-lg leading-relaxed mb-6">
                                    <?= (htmlspecialchars($article['content'])) ?>
                                </div>

                                <!-- comment -->
                                <div class="border-t pt-6 mt-6">
                                    <h3 class="text-2xl font-bold text-gray-800 mb-6">
                                        <i class="far fa-comments mr-2"></i>Commentaires 
                                    </h3>

                                    <!-- add comment form -->
                                    <div class="bg-gray-50 p-6 rounded-lg mb-6">
                                        <h4 class="text-lg font-semibold mb-4">Ajouter un commentaire</h4>
                                        <form method="POST" action="">
                                            <input type="hidden" name="id_article" value="<?= $article['id_article'] ?>">
                                            
                                            <div class="mb-4">
                                                <label class="block text-gray-700 mb-2">Votre nom</label>
                                                <input type="text" name="author_name" required 
                                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                       placeholder="Entrez votre nom">
                                            </div>
                                            
                                            <div class="mb-4">
                                                <label class="block text-gray-700 mb-2">Commentaire</label>
                                                <textarea name="contenu" required rows="4"
                                                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                          placeholder="Votre commentaire..."></textarea>
                                            </div>
                                            
                                            <button type="submit" name="add_comment" 
                                                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                                                <i class="fas fa-paper-plane mr-2"></i>Publier le commentaire
                                            </button>
                                        </form>
                                    </div>
                                
                                    <?php if (empty($comments)): ?>
                                        <p class="text-gray-500 text-center py-8">
                                            <i class="far fa-comment-slash text-4xl mb-3 block"></i>
                                            Aucun commentaire. Soyez le premier à commenter !
                                        </p>
                                    <?php else: ?>
                                        <div class="space-y-4">
                                            <?php foreach($comments as $comment): ?>
                                                <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-blue-500">
                                                    <div class="flex items-start">
                                                        <div class="bg-blue-600 text-white w-10 h-10 rounded-full flex items-center justify-center mr-3">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                        <div class="flex-1">
                                                            <div class="flex items-center justify-between mb-2">
                                                                <h5 class="font-semibold text-gray-800"><?= htmlspecialchars($comment['author_name']) ?></h5>
                                                                <span class="text-sm text-gray-500">
                                                                    <i class="far fa-clock mr-1"></i>
                                                                    <?= date('d/m/Y à H:i', strtotime($comment['date_creation'])) ?>
                                                                </span>
                                                            </div>
                                                            <p class="text-gray-700"><?= nl2br(htmlspecialchars($comment['contenu'])) ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <div class="flex justify-center space-x-6 mb-4">
                <a href="#" class="hover:text-blue-400 transition"><i class="fab fa-facebook text-2xl"></i></a>
                <a href="#" class="hover:text-blue-400 transition"><i class="fab fa-twitter text-2xl"></i></a>
                <a href="#" class="hover:text-blue-400 transition"><i class="fab fa-instagram text-2xl"></i></a>
                <a href="#" class="hover:text-blue-400 transition"><i class="fab fa-linkedin text-2xl"></i></a>
            </div>
            <p class="text-gray-400">&copy; 2024 BlogCMS. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>