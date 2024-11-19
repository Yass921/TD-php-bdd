<?php

// Inclure le fichier de configuration de la base de données
require_once '../database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Récupérez les données du formulaire et nettoyez les entrées
        $id_oeuvre = htmlspecialchars($_POST['id_oeuvre'], ENT_QUOTES, 'UTF-8');
        $nouveau_titre_oeuvre = htmlspecialchars($_POST['nouveau_titre_oeuvre'], ENT_QUOTES, 'UTF-8');
        $nouvel_auteur = htmlspecialchars($_POST['nouvel_auteur'], ENT_QUOTES, 'UTF-8');

        // Vérifiez que l'ID de l'œuvre est un entier
        if (!filter_var($id_oeuvre, FILTER_VALIDATE_INT)) {
            throw new Exception("ID de l'œuvre invalide.");
        }

        // Requête SQL pour mettre à jour le titre de l'œuvre et l'auteur
        $requete = "UPDATE OEUVRE SET titre_oeuvre = :titre_oeuvre, id_auteur = :id_auteur WHERE id_oeuvre = :id";
        $stmt = $db->prepare($requete);
        $stmt->bindParam(':id', $id_oeuvre, PDO::PARAM_INT);
        $stmt->bindParam(':titre_oeuvre', $nouveau_titre_oeuvre, PDO::PARAM_STR);
        $stmt->bindParam(':id_auteur', $nouvel_auteur, PDO::PARAM_INT);

        // Exécutez la requête
        $stmt->execute();

        // Fermez la fenêtre pop-up et actualisez la liste des œuvres dans la fenêtre parente
        echo "<script>window.close(); window.opener.location.reload();</script>";

    } catch (PDOException $e) {
        die("Erreur lors de la mise à jour de l'œuvre : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    } catch (Exception $e) {
        die("Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }

} else {
    // Récupérez l'ID de l'œuvre à modifier depuis l'URL et nettoyez l'entrée
    $id_oeuvre = htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8');

    // Vérifiez que l'ID de l'œuvre est un entier
    if (!filter_var($id_oeuvre, FILTER_VALIDATE_INT)) {
        die("ID de l'œuvre invalide.");
    }

    // Récupérez les informations actuelles de l'œuvre
    try {
        $requete_info_oeuvre = "SELECT titre_oeuvre, id_auteur FROM OEUVRE WHERE id_oeuvre = :id";
        $stmt_info_oeuvre = $db->prepare($requete_info_oeuvre);
        $stmt_info_oeuvre->bindParam(':id', $id_oeuvre, PDO::PARAM_INT);
        $stmt_info_oeuvre->execute();
        $info_oeuvre = $stmt_info_oeuvre->fetch(PDO::FETCH_ASSOC);

        if (!$info_oeuvre) {
            die("Aucune œuvre trouvée avec cet ID.");
        }

        // Récupérer la liste des auteurs pour le menu déroulant
        $requete_auteurs = "SELECT id_auteur, nom_auteur FROM AUTEUR";
        $resultat_auteurs = $db->query($requete_auteurs);
        $auteurs = $resultat_auteurs->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Erreur lors de la récupération des informations de l'œuvre : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Œuvre</title>
</head>
<body>
    <h1>Modifier l'œuvre</h1>
    <form method="post" action="modifier-oeuvre.php">
        <!-- Champ caché pour l'ID de l'œuvre -->
        <input type="hidden" name="id_oeuvre" value="<?= htmlspecialchars($id_oeuvre, ENT_QUOTES, 'UTF-8') ?>">

        <!-- Champ pour le nouveau titre -->
        <label for="nouveau_titre_oeuvre">Nouveau titre :</label>
        <input type="text" name="nouveau_titre_oeuvre" id="nouveau_titre_oeuvre" value="<?= htmlspecialchars($info_oeuvre['titre_oeuvre'], ENT_QUOTES, 'UTF-8') ?>" required><br>

        <!-- Menu déroulant pour sélectionner un nouvel auteur -->
        <label for="nouvel_auteur">Sélectionner un auteur :</label>
        <select name="nouvel_auteur" id="nouvel_auteur" required>
            <?php foreach ($auteurs as $auteur): ?>
                <option value="<?= $auteur['id_auteur'] ?>" <?= ($auteur['id_auteur'] == $info_oeuvre['id_auteur']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($auteur['nom_auteur'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <input type="submit" value="Modifier">
    </form>
</body>
</html>
