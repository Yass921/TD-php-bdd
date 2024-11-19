<?php
// Inclure le fichier de configuration de la base de données
require_once '../database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Récupérez les données du formulaire et nettoyez les entrées
        $id_auteur = htmlspecialchars($_POST['id_auteur'], ENT_QUOTES, 'UTF-8');
        $nouveau_prenom = htmlspecialchars($_POST['nouveau_prenom'], ENT_QUOTES, 'UTF-8');
        $nouveau_nom = htmlspecialchars($_POST['nouveau_nom'], ENT_QUOTES, 'UTF-8');

        // Vérifiez que l'ID de l'auteur est un entier
        if (!filter_var($id_auteur, FILTER_VALIDATE_INT)) {
            throw new Exception("ID de l'auteur invalide.");
        }

        // Requête SQL pour mettre à jour le nom et le prénom de l'auteur
        $requete = "UPDATE AUTEUR SET prenom_auteur = :prenom, nom_auteur = :nom WHERE id_auteur = :id";
        $stmt = $db->prepare($requete);
        $stmt->bindParam(':id', $id_auteur, PDO::PARAM_INT);
        $stmt->bindParam(':prenom', $nouveau_prenom, PDO::PARAM_STR);
        $stmt->bindParam(':nom', $nouveau_nom, PDO::PARAM_STR);

        // Exécutez la requête
        $stmt->execute();

        // Fermez la fenêtre pop-up et actualisez la liste des auteurs dans la fenêtre parente
        echo "<script>window.close(); window.opener.location.reload();</script>";
    } catch (PDOException $e) {
        // Gérer les erreurs de mise à jour de manière sécurisée
        die("Erreur lors de la mise à jour de l'auteur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    } catch (Exception $e) {
        // Gérer les autres erreurs
        die("Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
} else {
    // Récupérez l'ID de l'auteur à modifier depuis l'URL et nettoyez l'entrée
    $id_auteur = htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8');

    // Vérifiez que l'ID de l'auteur est un entier
    if (!filter_var($id_auteur, FILTER_VALIDATE_INT)) {
        die("ID de l'auteur invalide.");
    }

    // Récupérez les informations actuelles de l'auteur
    try {
        $requete_info_auteur = "SELECT prenom_auteur, nom_auteur FROM AUTEUR WHERE id_auteur = :id";
        $stmt_info_auteur = $db->prepare($requete_info_auteur);
        $stmt_info_auteur->bindParam(':id', $id_auteur, PDO::PARAM_INT);
        $stmt_info_auteur->execute();
        $info_auteur = $stmt_info_auteur->fetch(PDO::FETCH_ASSOC);

        // Vérifiez si l'auteur existe
        if (!$info_auteur) {
            die("Aucun auteur trouvé avec cet ID.");
        }
    } catch (PDOException $e) {
        die("Erreur lors de la récupération des informations de l'auteur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier Auteur</title>
</head>
<body>
    <h1>Modifier l'auteur</h1>
    <form method="post" action="modifier-auteur.php">
        <!-- Champ caché pour l'ID de l'auteur -->
        <input type="hidden" name="id_auteur" value="<?= htmlspecialchars($id_auteur, ENT_QUOTES, 'UTF-8') ?>">
        
        <label for="nouveau_prenom">Nouveau prénom :</label>
        <input type="text" name="nouveau_prenom" id="nouveau_prenom" value="<?= htmlspecialchars($info_auteur['prenom_auteur'], ENT_QUOTES, 'UTF-8') ?>" required><br>

        <label for="nouveau_nom">Nouveau nom :</label>
        <input type="text" name="nouveau_nom" id="nouveau_nom" value="<?= htmlspecialchars($info_auteur['nom_auteur'], ENT_QUOTES, 'UTF-8') ?>" required><br>

        <input type="submit" value="Modifier">
    </form>
</body>
</html>