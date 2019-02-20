   <?php
  session_start();
  require_once('../Modele/Configs.php');

  if (isset($_POST["action"])) {

    if ($_POST["action"] == "LoadObjectif") {
      $numero = $_POST["idAffiche"];
      $statement = $connection->prepare("SELECT objectif.id as id,
      objectif.objectif as objectif,
      statutobjectif.couleur as couleur,
      projet.nom as projet,
      CONCAT(employe.prenom,' ', employe.initial) as Employe,
      statutobjectif.nom as etat
      FROM objectif
      INNER JOIN statutobjectif on statutobjectif.id = objectif.id_StatutObjectif
      INNER JOIN projet on projet.id = objectif.id_Projet
      INNER JOIN employe on employe.id = objectif.id_Employe
      Where objectif.id_Sprint = $numero
      ORDER BY projet.nom, employe.prenom, objectif.id");
      $statement->execute();
      $result = $statement->fetchAll();
      $output = '';
      $output .= '
      <table class="table table-sm table-striped table-bordered" id="datatable" >
      <thead class="thead-light">
      <tr>
      <th>N°</th>
      <th>État</th>
      <th>Projet</th>
      <th>Ressource</th>
      <th>Objectif</th>';
      if ($_SESSION['TypeUtilisateur'] == 'ScrumMaster')
        $output .= '<th colspan="2"><center>Changer État</center></th>';
      $output .= '
      </tr>
      </thead>
      <tbody id="myTable">
      ';
      if ($statement->rowCount() > 0) {
        $num = 0;
        foreach ($result as $row) {
          $num += 1;
          $output .= '
        <tr >
        
        <td>' . $num . '</td>
        <td style="background-color: ' . $row["couleur"] . '; color: white; font-weight: bold;">' . $row["etat"] . '</td>
        <td>' . $row["projet"] . '</td>
        <td>' . $row["Employe"] . '</td>
        <td>' . $row["objectif"] . '</td>';
          if ($_SESSION['TypeUtilisateur'] == 'ScrumMaster')
            $output .= '
        <td><center><div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" id="' . $row["id"] . '" class="btn btn-success 1"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i></button>
        <button type="button" id="' . $row["id"] . '" class="btn btn-warning 2"><i class="fa fa-hourglass-half" aria-hidden="true"></i></button>
        <button type="button" id="' . $row["id"] . '" class="btn btn-danger 3"><i class="fa fa-thumbs-o-down" aria-hidden="true"></i></button>
        <button type="button" id="' . $row["id"] . '" class="btn btn-dark 4"><i class="fa fa-times" aria-hidden="true"></i></button>
        <button type="button" id="' . $row["id"] . '" class="btn btn-primary 5"><i class="fa fa-question" aria-hidden="true"></i></button>
        </div></center></td>
        
        <td>
        <center>
        <button type="button" id="' . $row["id"] . '" class="btn btn-warning GetObjectif"><i class="fa fa-pencil" aria-hidden="true"></i></button>
        </center>
        </td>
        </tr>
        ';

        }
      } else {
        $output .= '
     <tr>
     <td align="center" colspan="10">Pas de données</td>
     </tr>
     ';
      }
      $output .= '</tbody></table>';
      echo $output;
    }

    if ($_POST["action"] == "LoadMail") {
      $numero = $_POST["idAffiche"];
      $statement = $connection->prepare("SELECT objectif.id as id,
      objectif.objectif as objectif,
      statutobjectif.couleur as couleur,
      projet.nom as projet,
      statutobjectif.nom as etat
      FROM objectif
      INNER JOIN statutobjectif on statutobjectif.id = objectif.id_StatutObjectif
      INNER JOIN projet on projet.id = objectif.id_Projet
      Where objectif.id_Sprint = $numero
      ORDER BY projet.nom");
      $statement->execute();
      $result = $statement->fetchAll();
      $output = 'Rétrospective:';

      $projet = '';

      if ($statement->rowCount() > 0) {
        foreach ($result as $row) {

          if ($projet != $row["projet"]) {
            $projet = $row["projet"];
            $output .= '<br> <br><u>' . $row["projet"] . '</u>: <br>' . '- ' . $row["objectif"] . ' (<span class="Mail'.$row["etat"] .'">' . $row["etat"] . '</span>)';
          } else {
            $output .= '<br>- ' . $row["objectif"] . ' (<span class="Mail'.$row["etat"] .'">' . $row["etat"] . '</span>)';
          }
        }
      }

      $output .= '<br> <br> Remarques: <br>';

      $statement = $connection->prepare("SELECT label as label FROM `retrospective` where retrospective.DateFini IS NULL");
      $statement->execute();
      $result = $statement->fetchAll();

      if ($statement->rowCount() > 0) {
        foreach ($result as $row) {
          $output .= '- ' . $row["label"] . '<br>';
        }
      }

      echo ($output);
    }

    if ($_POST["action"] == "LoadRemarque") {
      $statement = $connection->prepare("SELECT id as id, DateCreation as DateCreation, Label as Label
        FROM retrospective
        WHERE DateFini IS NULL
        ORDER BY DateCreation desc
        ");
      $statement->execute();
      $result = $statement->fetchAll();
      $output = '';
      $output .= '
      <table class="table table-sm table-striped table-bordered" id="datatable" width="100%" cellspacing="0">
      <thead class="thead-light">
      <tr>
      <th>Création</th>
      <th>Remarque</th>';
      if ($_SESSION['TypeUtilisateur'] == 'ScrumMaster')
        $output .= '<th width=""><center>Changer État</center></th>';

      $output .= ' </tr>
      </thead>
      <tbody id="myTable">
      ';
      if ($statement->rowCount() > 0) {
        foreach ($result as $row) {
          $output .= '
        <tr>
        <td>' . date("d/m/Y", strtotime($row["DateCreation"])) . '</td>
        <td>' . $row["Label"] . '</td>';
          if ($_SESSION['TypeUtilisateur'] == 'ScrumMaster')
            $output .= '
          <td>
          <center>
          
          <button type="button" id="' . $row["id"] . '" class="btn btn-warning GetRemarque"><i class="fa fa-pencil" aria-hidden="true"></i></button>
          <button type="button" id="' . $row["id"] . '" class="btn btn-success ValiderRemarque"><i class="fa fa-check" aria-hidden="true"></i></button>
          </center>
          </td>';
          $output .= '</tr>
        ';
        }
      } else {
        $output .= '
     <tr>
     <td align="center" colspan="10">Pas de données</td>
     </tr>
     ';
      }
      $output .= '</tbody></table>';
      echo $output;
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
        echo '✓';
      else
        print_r($statement->errorInfo());
    }

    if ($_POST["action"] == "Créer Retrospective") {
      $statement = $connection->prepare("
   INSERT INTO retrospective (Label, DateCreation) 
   VALUES (:Label, NOW())
   ");
      $result = $statement->execute(
        array(
          ':Label' => $_POST["Labelretrospective"]
        )
      );
      if (!empty($result))
        echo '✓';
      else
        print_r($statement->errorInfo());
    }

    if ($_POST["action"] == "SelectObjectif") {
      $output = array();
      $statement = $connection->prepare(
        "SELECT * FROM objectif 
   WHERE id = '" . $_POST["id"] . "' 
   LIMIT 1"
      );
      $statement->execute();
      $result = $statement->fetchAll();
      foreach ($result as $row) {
        $output["id_Projet"] = $row["id_Projet"];
        $output["id_Employe"] = $row["id_Employe"];
        $output["objectif"] = $row["objectif"];
        $output["id_StatutObjectif"] = $row["id_StatutObjectif"];
      }
      echo json_encode($output);
    }

    if ($_POST["action"] == "SelectRemarque") {
      $output = array();
      $statement = $connection->prepare(
        "SELECT * FROM retrospective 
   WHERE id = '" . $_POST["id"] . "' 
   LIMIT 1"
      );
      $statement->execute();
      $result = $statement->fetch();

      $output["labelremarque"] = $result["Label"];

      echo json_encode($output);
    }

    if ($_POST["action"] == "Changer Objectif") {
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
        echo '✓';
      else
        print_r($statement->errorInfo());
    }

    if ($_POST["action"] == "Changer Remarque") {
      $statement = $connection->prepare(
        "UPDATE retrospective
   SET Label = :LabelRemarque
   WHERE id = :id
   "
      );
      $result = $statement->execute(
        array(
          ':LabelRemarque' => $_POST["Labelretrospective"],
          ':id' => $_POST["id"]
        )
      );
      if (!empty($result))
        echo '✓';
      else
        print_r($statement->errorInfo());
    }

    if ($_POST["action"] == "ChangerEtat") {
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
        echo '✓';
      else
        print_r($statement->errorInfo());
    }

    if ($_POST["action"] == "retrospectiveFini") {
      $statement = $connection->prepare(
        "UPDATE retrospective 
   SET DateFini = NOW()
   WHERE id = :id
   "
      );
      $result = $statement->execute(
        array(
          ':id' => $_POST["id"]
        )
      );
      if (!empty($result))
        echo '✓';
      else
        print_r($statement->errorInfo());
    }

    if ($_POST["action"] == "Delete") {
      $statement = $connection->prepare(
        "DELETE FROM objectif WHERE id = :id"
      );
      $result = $statement->execute(
        array(
          ':id' => $_POST["id"]
        )
      );
      if (!empty($result))
        echo '✓';
      else
        print_r($statement->errorInfo());
    }

  }

  ?>
