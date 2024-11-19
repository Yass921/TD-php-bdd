<?php
// Inclusion du fichier de connexion à la base de données
require '../database.php';

if (isset($_GET['id_auteur'])) {
    try {
        // Récupérer et valider l'ID de l'auteur
        $id_auteur = htmlspecialchars($_GET['id_auteur'], ENT_QUOTES, 'UTF-8');

        // Vérifier si l'ID est valide
        if (!filter_var($id_auteur, FILTER_VALIDATE_INT)) {
            throw new Exception("ID de l'auteur invalide.");
        }

        // Commencer une transaction
        $db->beginTransaction();

        // Supprimer d'abord toutes les œuvres associées à cet auteur
        $sql_delete_oeuvres = "DELETE FROM OEUVRE WHERE id_auteur = :id_auteur";
        $stmt_delete_oeuvres = $db->prepare($sql_delete_oeuvres);
        $stmt_delete_oeuvres->bindParam(':id_auteur', $id_auteur, PDO::PARAM_INT);
        $stmt_delete_oeuvres->execute();

        // Ensuite, supprimer l'auteur lui-même
        $sql_delete_auteur = "DELETE FROM AUTEUR WHERE id_auteur = :id_auteur";
        $stmt_delete_auteur = $db->prepare($sql_delete_auteur);
        $stmt_delete_auteur->bindParam(':id_auteur', $id_auteur, PDO::PARAM_INT);
        $stmt_delete_auteur->execute();

        // Valider la transaction
        $db->commit();

        // Redirection vers la page des auteurs après la suppression
        header('Location: afficher-auteurs.php');
        exit();
    } catch (PDOException $e) {
        // En cas d'erreur, annuler la transaction et afficher un message d'erreur sécurisé
        $db->rollBack();
        die("Erreur lors de la suppression : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    } catch (Exception $e) {
        // Gérer les autres erreurs
        die("Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
} else {
    // Gestion de l'erreur si l'ID de l'auteur n'est pas défini
    die("ID de l'auteur non spécifié.");
}
?>