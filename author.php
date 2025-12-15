<?php

session_start();

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'author'){

   header('Location : login.php');
   exit;
}
?>

<h1>Dashboard Admin</h1>
<p>Bienvenue</p>

<a href="logout.php">Déconnexion</a>

