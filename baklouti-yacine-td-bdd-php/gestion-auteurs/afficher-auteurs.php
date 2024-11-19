<!DOCTYPE html>
<html>
<head>
    <title>Liste des Auteurs</title>
</head>
<body>
    <?php
    require_once '../database.php';

    try {
        $requete = "SELECT id_auteur, prenom_auteur, nom_auteur FROM AUTEUR";
        $resultat = $db->query($requete);
        echo "<h1>Liste des auteurs</h1>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>ID</th><th>Prénom</th><th>Nom</th><th>Modifier</th><th>Supprimer</th></tr>";

        while ($auteur = $resultat->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$auteur['id_auteur']}</td>";
            echo "<td>{$auteur['prenom_auteur']}</td>";
            echo "<td>{$auteur['nom_auteur']}</td>";
            echo "<td><button onclick='modifierAuteur({$auteur['id_auteur']})'>Modifier</button></td>";
            echo "<td><button onclick='confirmationSuppAuteur({$auteur['id_auteur']})'>Supprimer</button></td>";
            echo "</tr>";
        }

        echo "</table>";
    } catch (PDOException $e) {
        die("Erreur lors de la récupération des auteurs : " . $e->getMessage());
    }
    ?>

    <br>
    <button onclick="ajouterAuteur()">Ajouter un auteur</button>

    <button onclick="window.location.href='../gestion-oeuvres/afficher-oeuvres.php'">Afficher les œuvres</button>

    <script>
        function modifierAuteur(id_auteur) {
            var popupWindow = window.open('modifier-auteur.php?id=' + id_auteur, 'Modifier Auteur', 'width=400,height=300');
        }

        function confirmationSuppAuteur(id_auteur) {
            if (confirm("Êtes-vous sûr de vouloir supprimer cet auteur et toutes ses œuvres associées ?")) {
                window.location.href = 'supprimer-auteur.php?id_auteur=' + id_auteur;
            }
        }

        function ajouterAuteur() {
            var popupWindow = window.open('ajouter-auteur.php', 'Ajouter Auteur', 'width=400,height=300');
        }
    </script>
</body>
</html>
