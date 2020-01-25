   <?php
    session_start();
    require_once('../Modele/Configs.php');

    if (isset($_POST["action"])) {

      if ($_POST["action"] == "getObjectifs") {
        $statement = $connection->prepare("SELECT O.id as id,
        O.objectif,
        S.couleur,
        P.nom AS projet,
        CONCAT(employe.prenom,'&nbsp;', employe.initial) AS Employe,
        S.nom AS etat
        FROM objectif O
        INNER JOIN statutobjectif S
          ON S.id = O.id_StatutObjectif
        INNER JOIN projet P
          ON P.id = O.id_Projet
        INNER JOIN employe
          ON employe.id = O.id_Employe
        WHERE O.id_Sprint = '".$_POST["idAffiche"]."'
        ORDER BY Employe, P.nom, O.id");
          $statement->execute();
          $result = $statement->fetchAll();
          $resultat = [];

        foreach ($result as $row) {

          $MonTest = [];

          $MonTest['id'] = $row['id'];
          $MonTest['objectif'] = $row['objectif'];
          $MonTest['couleur'] = $row['couleur'];
          $MonTest['projet'] = $row['projet'];
          $MonTest['Employe'] = $row['Employe'];
          $MonTest['Etat'] = $row['etat'];

          $resultat[] = $MonTest;
        }

        print json_encode($resultat);
      }

      if ($_POST["action"] == "putEtatObjectif") {
        $statement = $connection->prepare(
          "UPDATE objectif 
          SET id_StatutObjectif = :EtatObjectif 
          WHERE id = :id
          "
        );
        $result = $statement->execute(
          array(
            ':EtatObjectif' => $_POST["EtatObjectif"],
            ':id' => $_POST["id"]
          )
        );
        if (!empty($result))
          print true;
        else
          print_r($statement->errorInfo());
      }
      
      if ($_POST["action"] == "Créer") {
        $statement = $connection->prepare("
        INSERT INTO objectif (id_Sprint, id_Projet, id_Employe, objectif, id_StatutObjectif) 
        VALUES (:id_Sprint, :id_Projet, :id_Employe, :objectif, :id_StatutObjectif)
        ");
        $result = $statement->execute(
          array(
            ':id_Sprint' => $_POST["idSprint"],
            ':id_Projet' => $_POST["idProjet"],
            ':id_Employe' => $_POST["idEmploye"],
            ':objectif' => $_POST["LabelObjectif"],
            ':id_StatutObjectif' => 5
          )
        );
        if (!empty($result))
          print 'Objectif "'.$_POST["LabelObjectif"].'" créé';
        else
          print_r($statement->errorInfo());
      }

      if ($_POST["action"] == "getObjectif") {
        $output = array();
        $statement = $connection->prepare(
          "SELECT * FROM objectif 
        WHERE id = '" . $_POST["id"] . "' 
        LIMIT 1"
        );
        $statement->execute();
        $result = $statement->fetch();

        print json_encode($result);
      }

      if ($_POST["action"] == "putObjectif") {
        $statement = $connection->prepare(
          "UPDATE objectif 
        SET objectif = :LabelObjectif, id_Sprint = :id_Sprint, id_Projet = :id_Projet, id_Employe = :id_Employe
        WHERE id = :id
        "
        );
        $result = $statement->execute(
          array(
            ':LabelObjectif' => $_POST["LabelObjectif"],
            ':id_Sprint' => $_POST["idSprint"],
            ':id_Projet' => $_POST["idProjet"],
            ':id_Employe' => $_POST["idEmploye"],
            ':id' => $_POST["id"]
          )
        );
        if (!empty($result))
          print true;
        else
          print_r($statement->errorInfo());
      }

      if ($_POST["action"] == "dellObjectif") {
        $statement = $connection->prepare(
          "DELETE FROM objectif
        WHERE id = :id"
        );
        $result = $statement->execute(
          array(
            ':id' => $_POST["id"]
          )
        );
        if ($statement->rowCount() > 0)
          print true;
        else
          print_r($statement->errorInfo());
      }
    }

    ?> 