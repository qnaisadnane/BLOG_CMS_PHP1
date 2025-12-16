<?php
session_start();
require_once 'database.php';

$stmt = $conn->query("SELECT * FROM article");
$articles = $stmt->fetchAll();

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
                <div class="flex items-center space-x-2">
                    <i class="fas fa-blog text-3xl text-blue-600"></i>
                    <h1 class="text-2xl font-bold text-gray-800">BlogCMS</h1>
                </div>
                <a href="login.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
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
    <?php if (isset($success)): ?>
    <div class="container mx-auto px-4 mt-6">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
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
                        
                        <article class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <!-- header articles -->
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-8 text-white">
                                <h2 class="text-3xl font-bold mb-2"><?= htmlspecialchars($article['title']) ?></h2>
                                <div class="flex items-center space-x-6 text-sm">
                                    <span><i class="far fa-user mr-2"></i><?= htmlspecialchars($article['username']) ?></span>
                                    <span><i class="far fa-calendar mr-2"></i><?= date('d/m/Y', strtotime($article['date_creation'])) ?></span>
                                    <span><i class="far fa-eye mr-2"></i><?= $article['view_count'] ?> vues</span>
                                    
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