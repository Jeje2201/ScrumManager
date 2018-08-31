   <?php

  require_once('../Modele/Configs.php');

  if (isset($_POST["action"])) {

    if ($_POST["action"] == "GetTotalHeuresDescenduesParEmploye") {

      $NumeroduSprint = $_POST["NumeroduSprint"];

      $statement = $connection->prepare(
        $sql = "
        SELECT 
        E.id as empid,
        E.prenom as prenom,
        E.Initial as Initial,
        sum(A.heure) as nbheureadescendre,
        ifnull( sum(HD.heure),0) as nbheuredescendu,
        sum(A.heure) - ifnull( sum(HD.heure),0) as heurerestantes
        from Employe as E
        LEFT JOIN attribution as a ON a.id_employe = e.id and a.id_Sprint = $NumeroduSprint
        LEFT JOIN heuresdescendues as hd on e.id = hd.id_Employe and hd.id_Attribution = a.id and hd.id_Sprint = $NumeroduSprint 
        WHERE
        A.heure is not null
        AND A.id_TypeTache IS NULL
        GROUP BY E.id
        order by nbheuredescendu desc, heurerestantes desc
      "
      );

      $statement->execute();
      $result = $statement->fetchAll();

      $employe = [];
      $HDescendue = [];
      $Hattribue = [];

      foreach ($result as $row) {

        $employe[] = $row['prenom'] . ' (' . $row['Initial'] . ')';
        $HDescendue[] = intval($row['nbheuredescendu']);
        $Hattribue[] = intval($row['nbheureadescendre']);

      }

      $array[] = $employe;
      $array[] = $HDescendue;
      $array[] = $Hattribue;

      $statement = $connection->prepare(
        $sql = "
        SELECT 
        P.id as projid,
        P.nom as pnom,
        sum(A.heure) as nbheureadescendre,
        ifnull( sum(HD.heure),0) as nbheuredescendu,
        sum(A.heure) - ifnull( sum(HD.heure),0) as heurerestantes
        from projet as P
        LEFT JOIN attribution as a ON a.id_Projet = p.id and a.id_Sprint = $NumeroduSprint
        LEFT JOIN heuresdescendues as hd on hd.id_Projet = p.id and hd.id_Attribution = a.id and hd.id_Sprint = $NumeroduSprint
        WHERE
        A.heure is not null
        AND A.id_TypeTache IS NULL
        GROUP BY P.id
        order by nbheuredescendu desc, heurerestantes desc
       "
      );

      $statement->execute();
      $result = $statement->fetchAll();

      $projet = [];
      $HDescendueProjet = [];
      $HattribueProjet = [];

      foreach ($result as $row) {

        $projet[] = $row['pnom'];
        $HDescendueProjet[] = intval($row['nbheuredescendu']);
        $HattribueProjet[] = intval($row['nbheureadescendre']);

      }

      $array[] = $projet;
      $array[] = $HDescendueProjet;
      $array[] = $HattribueProjet;

      $statement = $connection->prepare(
        $sql = "SELECT COUNT(objectif.id) as Nombre, statutobjectif.nom as Statut, statutobjectif.couleur as Couleur
      from objectif
      join statutobjectif on statutobjectif.id = objectif.id_StatutObjectif
      WHERE objectif.id_Sprint = $NumeroduSprint
      GROUP BY objectif.id_StatutObjectif
      ORDER BY objectif.id_StatutObjectif
      "
      );

      $statement->execute();
      $result = $statement->fetchAll();

      $Total = [];

      foreach ($result as $row) {

        $MonTest = [];

        $MonTest[] = $row['Statut'];
        $MonTest[] = intval($row['Nombre']);
        $MonTest[] = $row['Couleur'];

        $Total[] = $MonTest;

      }

      $array[] = $Total;

      $statement = $connection->prepare(
        $sql = "SELECT sum(heure) as Tot from attribution where attribution.id_Sprint = $NumeroduSprint AND attribution.id_TypeTache IS NULL"
      );
      $statement->execute();
      $result = $statement->fetch();
      $TotAttribution[] = intval($result["Tot"]);

      $array[] = $TotAttribution;

      $statement = $connection->prepare(
        $sql = "SELECT sum(heure) as Tot from heuresdescendues where heuresdescendues.id_Sprint = $NumeroduSprint"
      );
      $statement->execute();
      $result = $statement->fetch();
      $TotDescendue[] = intval($result["Tot"]);

      $array[] = $TotDescendue;

      $statement = $connection->prepare(
        $sql = "SELECT sum(heure) as heures, DateDescendu as Ladate FROM `heuresdescendues` where heuresdescendues.id_Sprint = $NumeroduSprint GROUP by DateDescendu"
      );

      $statement->execute();
      $result = $statement->fetchAll();

      $Descendues = [];
      $Date = [];


      foreach ($result as $row) {
        $Descendues[] = intval($row['heures']);
        $Date[] = date("d-m-Y", strtotime($row['Ladate']));

      }

      $array[] = $Descendues;
      $array[] = $Date;

      $statement = $connection->prepare(
        $sql = "SELECT * from sprint where sprint.id = $NumeroduSprint"
      );
      $statement->execute();
      $result = $statement->fetch();
      $FinSprint[] = $result["dateFin"];
      $DebutSprint[] = $result["dateDebut"];

      $array[] = $FinSprint;
      $array[] = $DebutSprint;

    }

    echo json_encode($array);

  }

  ?>
