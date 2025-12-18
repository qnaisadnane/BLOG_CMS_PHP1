<?php
session_start();
require_once 'database.php';

$success = '';
$error = '';


if(isset($_POST['add_author'])){
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if(!empty($username) && !empty($name) && !empty($email) && !empty($password)){
        // Hasher le mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO utilisateur (username, name, email, password, role, datecreation) VALUES (?, ?, ?, ?, 'author', NOW())");
        if($stmt->execute([$username, $name, $email, $hashed_password])){
            $success = "Auteur ajouté avec succès !";
        } else {
            $error = "Erreur lors de l'ajout de l'auteur .";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}


if(isset($_POST['update_author'])){
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if(!empty($username) && !empty($name) && !empty($email)){
        if(!empty($password)){
            // Si nouveau mot de passe fourni
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE utilisateur SET name = ?, email = ?, password = ? WHERE username = ? AND role = 'author'");
            $stmt->execute([$name, $email, $hashed_password, $username]);
        } else {
            // Sans changer le mot de passe
            $stmt = $conn->prepare("UPDATE utilisateur SET name = ?, email = ? WHERE username = ? AND role = 'author'");
            $stmt->execute([$name, $email, $username]);
        }
        $success = "Auteur modifié avec succès !";
    }
}


if(isset($_GET['delete_author'])){
    $username = $_GET['delete_author'];
    $stmt = $conn->prepare("DELETE FROM utilisateur WHERE username = ? AND role = 'author'");
    if($stmt->execute([$username])){
        header("Location: admin.php?success=author_deleted");
        exit;
    }
}

$stmt = $conn->query("SELECT * FROM utilisateur WHERE role = 'author' ORDER BY datecreation DESC");
$auteurs = $stmt->fetchAll();

$author_to_edit = null;
if(isset($_GET['edit_author'])){
    $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE username = ? AND role = 'author'");
    $stmt->execute([$_GET['edit_author']]);
    $author_to_edit = $stmt->fetch();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilisateur Admin - BlogCMS</title>
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
                        <h1 class="text-2xl font-bold text-gray-800">Admin</h1>
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


        <div class="mt-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">
                <i class="fas fa-pen-fancy text-blue-600 mr-2"></i>Gestion des Auteurs 
            </h2>

            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Formulaire -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">
                            <i class="fas fa-<?= $author_to_edit ? 'edit' : 'user-plus' ?> mr-2"></i>
                            <?= $author_to_edit ? 'Modifier' : 'Ajouter' ?> un Auteur
                        </h3>
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Username </label>
                                <input type="text" name="username" required maxlength="30"
                                       value="<?= $author_to_edit ? htmlspecialchars($author_to_edit['username']) : '' ?>"
                                       <?= $author_to_edit ? 'readonly' : '' ?>
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="Ex: jean_123">
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Nom </label>
                                <input type="text" name="name" required maxlength="30"
                                       value="<?= $author_to_edit ? htmlspecialchars($author_to_edit['name']) : '' ?>"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="Jean Dupont">
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Email </label>
                                <input type="email" name="email" required maxlength="30"
                                       value="<?= $author_to_edit ? htmlspecialchars($author_to_edit['email']) : '' ?>"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="jean@example.com">
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">
                                    Mot de passe <?= $author_to_edit ? '(laisser vide pour ne pas changer)' : '*' ?>
                                </label>
                                <input type="password" name="password" <?= $author_to_edit ? '' : 'required' ?>
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="Mot de passe">
                            </div>
                            
                            <?php if($author_to_edit): ?>
                                <button type="submit" name="update_author"
                                        class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition mb-2">
                                    <i class="fas fa-save mr-2"></i>Modifier
                                </button>
                                <a href="admin.php" class="block w-full bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700 transition text-center">
                                    <i class="fas fa-times mr-2"></i>Annuler
                                </a>
                            <?php else: ?>
                                <button type="submit" name="add_author"
                                        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-plus mr-2"></i>Ajouter
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Liste -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 p-4">
                            <h3 class="text-xl font-bold text-white">Tous les auteurs (<?= count($auteurs) ?>)</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b-2">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Username</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nom</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php if(empty($auteurs)): ?>
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                                Aucun auteur enregistre
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($auteurs as $auteur): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($auteur['username']) ?></span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="text-sm text-gray-900"><?= htmlspecialchars($auteur['name']) ?></span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="text-sm text-gray-600"><?= htmlspecialchars($auteur['email']) ?></span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-600"><?= date('d/m/Y', strtotime($auteur['datecreation'])) ?></span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex justify-center space-x-2">
                                                    <a href="?edit_author=<?= $auteur['username'] ?>" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?delete_author=<?= $auteur['username'] ?>"
                                                       onclick="return confirm('Supprimer cet auteur ?')"
                                                       class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </main>
</body>
</html>