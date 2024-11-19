<!DOCTYPE html>
<html>
<head>
    <title>Liste des Oeuvres</title>
</head>
<body>
    <?php
    // Inclure le fichier de configuration de la base de données
    require_once '../database.php';

    try {
        // Requête SQL pour récupérer la liste des oeuvres avec les noms des auteurs
        $requete = "
            SELECT O.id_oeuvre, O.titre_oeuvre, A.nom_auteur
            FROM OEUVRE O
            JOIN AUTEUR A ON O.id_auteur = A.id_auteur
        ";
        $resultat = $db->query($requete);

        // Affichage de la liste des oeuvres avec les boutons "Modifier" et "Supprimer"
        echo "<h1>Liste des oeuvres</h1>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>ID</th><th>Titre_Oeuvre</th><th>Nom_Auteur</th><th>Modifier</th><th>Supprimer</th></tr>";

        while ($oeuvre = $resultat->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$oeuvre['id_oeuvre']}</td>";
            echo "<td>{$oeuvre['titre_oeuvre']}</td>";
            echo "<td>{$oeuvre['nom_auteur']}</td>"; // Afficher le nom de l'auteur
            // Bouton "Modifier" qui ouvre une fenêtre pop-up pour modifier l'oeuvre
            echo "<td><button onclick='modifieroeuvre({$oeuvre['id_oeuvre']})'>Modifier</button></td>";
            // Bouton "Supprimer" avec confirmation
            echo "<td><button onclick='confirmationSuppoeuvre({$oeuvre['id_oeuvre']})'>Supprimer</button></td>";
            echo "</tr>";
        }

        echo "</table>";
    } catch (PDOException $e) {
        die("Erreur lors de la récupération des oeuvres : " . $e->getMessage());
    }
    ?>

    <!-- Bouton pour ajouter un oeuvre dans une petite fenêtre -->
    <br>
    <button onclick="ajouteroeuvre()">Ajouter un oeuvre</button>

    <!-- Bouton pour renvoyer vers la liste des œuvres -->
    <button onclick="window.location.href='../gestion-oeuvres/afficher-oeuvres.php'">Afficher les œuvres</button>

    <!-- Scripts JavaScript pour les fenêtres pop-up et la suppression -->
    <script>
        function modifieroeuvre(id_oeuvre) {
            var popupWindow = window.open('modifier-oeuvre.php?id=' + id_oeuvre, 'Modifier oeuvre', 'width=400,height=300');
        }

        function confirmationSuppoeuvre(id_oeuvre) {
            if (confirm("Êtes-vous sûr de vouloir supprimer cet oeuvre et toutes ses œuvres associées ?")) {
                window.location.href = 'supprimer-oeuvre.php?id_oeuvre=' + id_oeuvre;
            }
        }

        function ajouteroeuvre() {
            var popupWindow = window.open('ajouter-oeuvre.php', 'Ajouter oeuvre', 'width=400,height=300');
        }
    </script>
</body>
</html>
