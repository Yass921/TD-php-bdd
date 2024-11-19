<?php
// Inclure le fichier de configuration de la base de données
require_once '../database.php';

// Initialiser une variable pour afficher les messages
$message = '';

// Vérifiez si la méthode de requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyer et valider les données de formulaire
    $titre_oeuvre = htmlspecialchars($_POST['titre_oeuvre'], ENT_QUOTES, 'UTF-8');
    $id_auteur = htmlspecialchars($_POST['id_auteur'], ENT_QUOTES, 'UTF-8');

    // Vérifier si l'œuvre existe déjà en fonction du titre et de l'année
    $oeuvreExiste = false;
    try {
        // Requête SQL pour vérifier l'existence de l'œuvre
        $sql = "SELECT id_oeuvre FROM OEUVRE WHERE titre_oeuvre = :titre_oeuvre";
        $stmt = $db->prepare($sql);

        // Lier les paramètres avec les valeurs nettoyées
        $stmt->bindParam(':titre_oeuvre', $titre_oeuvre, PDO::PARAM_STR);
        $stmt->execute();

        // Si l'œuvre existe déjà
        if ($stmt->rowCount() > 0) {
            $oeuvreExiste = true;
        }
    } catch (PDOException $e) {
        // Gérer les erreurs de base de données et sécuriser le message d'erreur
        $message = "Erreur lors de la vérification de l'œuvre : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }

    if (!$oeuvreExiste) {
        // L'œuvre n'existe pas, donc on peut l'ajouter
        try {
            // Préparez la requête d'insertion avec des paramètres liés
            $insertOeuvreSql = "INSERT INTO OEUVRE (titre_oeuvre, id_auteur) VALUES (:titre_oeuvre, :id_auteur)";
            $stmt = $db->prepare($insertOeuvreSql);

            // Lier les paramètres
            $stmt->bindParam(':titre_oeuvre', $titre_oeuvre, PDO::PARAM_STR);
            $stmt->bindParam(':id_auteur', $id_auteur, PDO::PARAM_INT);
            $stmt->execute();

            // Message de succès
            $message = "Nouvelle œuvre ajoutée avec succès !";
            // Actualiser la liste des œuvres dans la fenêtre parente
            echo "<script>window.opener.location.reload();</script>";
        } catch (PDOException $e) {
            // Gérer les erreurs d'insertion et sécuriser le message d'erreur
            $message = "Erreur lors de l'ajout de l'œuvre : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    } else {
        // L'œuvre existe déjà
        $message = "Cette œuvre existe déjà en base de données.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter une nouvelle œuvre</title>
</head>
<body>
    <h1>Ajouter une nouvelle œuvre</h1>
    <!-- Affichage du message d'état -->
    <p><?php echo $message; ?></p>

    <!-- Formulaire d'ajout d'une nouvelle œuvre -->
    <form method="POST" action="ajouter-oeuvre.php">
        <label for="titre_oeuvre">Titre de l'œuvre:</label>
        <input type="text" id="titre_oeuvre" name="titre_oeuvre" required><br>
        
   
        
        <label for="id_auteur">Auteur (sélectionnez l'auteur par son ID):</label>
        <input type="number" id="id_auteur" name="id_auteur" required><br>

        <!-- Bouton pour soumettre le formulaire -->
        <input type="submit" value="Ajouter">
    </form>
</body>
</html>
