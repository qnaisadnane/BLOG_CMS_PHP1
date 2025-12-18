<?php
session_start();
require_once 'database.php';

$success = '';
$error = '';


if(isset($_POST['add_category'])){
    $nom_categorie = trim($_POST['nom_categorie']);
    $description_c = trim($_POST['description_c']);
    
    if(!empty($nom_categorie) && !empty($description_c)){
        $stmt = $conn->prepare("INSERT INTO categorie (nom_categorie, description_c) VALUES (?, ?)");
        if($stmt->execute([$nom_categorie, $description_c])){
            $success = "Catégorie ajoutée avec succès !";
        } else {
            $error = "Erreur lors de l'ajout de la catégorie.";
        }
    }
}


if(isset($_POST['update_category'])){
    $id_categorie = intval($_POST['id_categorie']);
    $nom_categorie = trim($_POST['nom_categorie']);
    $description_c = trim($_POST['description_c']);
    
    if(!empty($nom_categorie) && !empty($description_c)){
        $stmt = $conn->prepare("UPDATE categorie SET nom_categorie = ?, description_c = ? WHERE id_categorie = ?");
        if($stmt->execute([$nom_categorie, $description_c, $id_categorie])){
            $success = "Catégorie modifiée avec succès !";
        } else {
            $error = "Erreur lors de la modification.";
        }
    }
}


if(isset($_GET['delete_category'])){
    $id_categorie = intval($_GET['delete_category']);
    $stmt = $conn->prepare("DELETE FROM categorie WHERE id_categorie = ?");
    if($stmt->execute([$id_categorie])){
        header("Location: admin.php?success=cat_deleted");
        exit;
    }
}

$stmt = $conn->query("SELECT * FROM categorie ORDER BY nom_categorie");
$categories = $stmt->fetchAll();

$category_to_edit = null;
if(isset($_GET['edit_category'])){
    $stmt = $conn->prepare("SELECT * FROM categorie WHERE id_categorie = ?");
    $stmt->execute([intval($_GET['edit_category'])]);
    $category_to_edit = $stmt->fetch();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorie Admin - BlogCMS</title>
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
                <i class="fas fa-folder text-indigo-600 mr-2"></i>Gestion des Catégories
            </h2>

            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Formulaire -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">
                            <i class="fas fa-<?= $category_to_edit ? 'edit' : 'plus-circle' ?> mr-2"></i>
                            <?= $category_to_edit ? 'Modifier' : 'Ajouter' ?> une Catégorie
                        </h3>
                        <form method="POST" action="">
                            <?php if($category_to_edit): ?>
                                <input type="hidden" name="id_categorie" value="<?= $category_to_edit['id_categorie'] ?>">
                            <?php endif; ?>
                            
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Nom *</label>
                                <input type="text" name="nom_categorie" required maxlength="30"
                                       value="<?= $category_to_edit ? htmlspecialchars($category_to_edit['nom_categorie']) : '' ?>"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                                       placeholder="Ex: Technologie">
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Description *</label>
                                <textarea name="description_c" required rows="4"
                                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                                          placeholder="Description de la catégorie"><?= $category_to_edit ? htmlspecialchars($category_to_edit['description_c']) : '' ?></textarea>
                            </div>
                            
                            <?php if($category_to_edit): ?>
                                <button type="submit" name="update_category"
                                        class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition mb-2">
                                    <i class="fas fa-save mr-2"></i>Modifier
                                </button>
                                <a href="admin.php" class="block w-full bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700 transition text-center">
                                    <i class="fas fa-times mr-2"></i>Annuler
                                </a>
                            <?php else: ?>
                                <button type="submit" name="add_category"
                                        class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition">
                                    <i class="fas fa-plus mr-2"></i>Ajouter
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Liste -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-4">
                            <h3 class="text-xl font-bold text-white">Toutes les catégories (<?= count($categories) ?>)</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b-2">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nom</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach($categories as $cat): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-medium"><?= $cat['id_categorie'] ?></span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($cat['nom_categorie']) ?></span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm text-gray-600"><?= htmlspecialchars(substr($cat['description_c'], 0, 50)) ?>...</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center space-x-2">
                                                <a href="?edit_category=<?= $cat['id_categorie'] ?>" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?delete_category=<?= $cat['id_categorie'] ?>" 
                                                   onclick="return confirm('Supprimer cette catégorie ?')"
                                                   class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
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