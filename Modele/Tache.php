    <?php

    require_once('../Modele/Configs.php');

    //Si jamais le texte d'une tache risque de casser l'html qui ressort
    function PreviewText($input)
    {
      $Problem = array("<", ">");
      $input = str_replace($Problem, " ", $input);

      return $input;
    }

    if (isset($_POST["action"])) {

      //Fonction pour afficher les cards a DESCendre et DESCendues
      if ($_POST["action"] == "AfficherCards") {
        $Test = new stdClass;

        $idEmploye = $_POST["idEmploye"];
        $numero = $_POST["idAffiche"];

        if ($_POST["idEmploye"] == "ToutLeMonde")
          $Requete1 = "AND A.id_Employe in (select id FROM employe)";
        else
          $Requete1 = "AND A.id_Employe = $idEmploye";

        $statement = $connection->prepare("SELECT A.id,
          A.Label,
          A.heure,
          P.nom AS projet,
          P.Logo,
          E.Initial,
          E.couleur,
          E.prenom AS E_Prenom,
          E.nom AS E_Nom,
          E.Pseudo AS E_Pseudo
          FROM attribution A
          INNER JOIN employe E
            ON E.id = A.id_Employe
          INNER JOIN projet P
            ON P.id = A.id_Projet
          INNER JOIN sprint S
            ON S.id = A.id_Sprint
          WHERE A.id_Sprint =  $numero
          AND A.Done IS NULL " . $Requete1 .
          " ORDER BY E.prenom");

        $statement->execute();
        $result = $statement->fetchAll();
        $output1 = '';
        if ($statement->rowCount() > 0) {
          foreach ($result AS $row) {
            $output1 .= '
      <div class="card BOUGEMOI" id="' . $row["id"] . '" onclick="DeplaceToi(this)">
        <img class="LogoProjet" src="Assets/Image/Projets/' . $row["Logo"] . '">
        <div style="margin-left:7px;">
          <div class="BarreLateralCard" style="background-color:' . $row["couleur"] . ';"></div>
            <i class="fa fa-user-o" aria-hidden="true"></i> ' . $row["E_Pseudo"] . ' (' . $row["Initial"] . ')<br>
            <div class="SpecialHr"></div>
            <i class="fa fa-file-o" aria-hidden="true"></i> ' . $row["projet"] . '<br>
            <div class="SpecialHr"></div>
            <i class="fa fa-tag" aria-hidden="true"></i> ' . PreviewText($row["Label"]) . ' (' . $row["heure"] . ')
        </div>
      </div>';
          }
        } else {
          $output1 .= 'Pas de tâche';
        }

        $statement = $connection->prepare("SELECT A.id, A.Label, A.heure, P.nom AS projet, P.Logo, E.Initial, E.couleur, E.prenom, E.nom AS E_Nom, E.Pseudo
        FROM attribution A
        INNER JOIN employe E ON E.id = A.id_Employe
        INNER JOIN projet P ON P.id = A.id_Projet
        INNER JOIN sprint S ON S.id = A.id_Sprint
        WHERE A.id_Sprint =  $numero
        AND A.Done IS NOT NULL "  . $Requete1 .
          " ORDER BY E.prenom");

        $statement->execute();
        $result = $statement->fetchAll();
        $output2 = '';
        if ($statement->rowCount() > 0) {
          foreach ($result AS $row) {

            $output2 .= '
<div class="card PASTOUCHE">
  <img class="LogoProjet" src="Assets/Image/Projets/' . $row["Logo"] . '">
  <div style="margin-left:7px;">
    <div class="BarreLateralCard" style="background-color:' . $row["couleur"] . ';"></div>
      <i class="fa fa-user-o" aria-hidden="true"></i> ' . $row["Pseudo"] . ' (' . $row["Initial"] . ')<br>
      <div class="SpecialHr"></div>
      <i class="fa fa-file-o" aria-hidden="true"></i> ' . $row["projet"] . '<br>
      <div class="SpecialHr"></div>
      <i class="fa fa-tag" aria-hidden="true"></i> ' . PreviewText($row["Label"]) . ' (' . $row["heure"] . ')
  </div>
</div>';

          }
        } else {
          $output2 .= 'Pas de tâche';
        }
        $Test->Attribution = $output1;
        $Test->Descendue = $output2;

        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');

        echo json_encode($Test);

      }

      //Afficher la liste d'employes qui ont des taches
      if ($_POST["action"] == "LoadListEmployes") {
        $Test = new stdClass;

        $numero = $_POST["idAffiche"];

        $statement = $connection->prepare("SELECT DISTINCT
        (
          select E.prenom
          FROM employe E
          WHERE E.id = A.id_Employe
        ) AS Prenom,
        (
          select E.nom
          FROM employe E
          WHERE E.id = A.id_Employe
        ) AS Nom,
        A.id_Employe AS id
        FROM attribution A
        WHERE A.id_Sprint = $numero
        ORDER BY (
          select E.prenom
          FROM employe E
          WHERE E.id = A.id_Employe
        )
  ");

        $statement->execute();
        $result = $statement->fetchAll();
        $output2 = '<select class="form-control"  id="numeroEmploye" name="numeroEmploye">
              <option value="ToutLeMonde">Tout le monde</option>';
        if ($statement->rowCount() > 0) {
          foreach ($result AS $row) {

            $output2 .= '<option value="' . $row["id"] . '"> ' . $row["Prenom"] . ' ' . $row["Nom"] . ' </option>';

          }

          $output2 .= '</select>';
        }
        $Test->Attribution = $output2;

        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');

        echo json_encode($Test);

      }

      //Ressortir la date min et max du sprint
      if ($_POST["action"] == "DateMinMax") {

        $idAffiche = $_POST["idAffiche"];

        $statement = $connection->prepare(
          $sql = "SELECT dateDebut,
          dateFin
          FROM sprint S
          WHERE S.id = $idAffiche"
        );
        $statement->execute();
        $result = $statement->fetch();

          $DateMin[] = date("d-m-Y", strtotime($result["dateDebut"]));
          $DateMax[] = date("d-m-Y", strtotime($result["dateFin"]));

        $array['DateMin'] = $DateMin;
        $array['DateMax'] = $DateMax;

        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');
        echo json_encode($array);

      }

      //Valider une tache, soit lui donner une date de validation
      if ($_POST["action"] == "Descendre") {

        $IdAttribue = $_POST["IdAttribue"];

        for ($i = 0; $i < sizeof($IdAttribue); $i++) {

          $statement = $connection->prepare("UPDATE attribution
          set Done = :done
          WHERE id = :id");

          $result = $statement->execute(
            array(
              ':done' => $_POST["LeJourDeDescente"],
              ':id' => $IdAttribue[$i]
            )
          );
        }
        if (!empty($result))
          echo '✓';
        else
          print_r($statement->errorInfo());
      }

    }

    ?>