   <?php

session_start();
require_once('Configs.php');

    if (isset($_POST["action"])) {

      if ($_POST["action"] == "RemplirTableau") {
        $numero = $_POST["idAffiche"];
        $statement = $connection->prepare("SELECT *
        FROM tache
        inner JOIN user ON tache_fk_user = user_pk
        INNER JOIN projet ON  tache_fk_projet = projet_pk
        WHERE tache_fk_sprint = $numero
        ORDER BY tache_pk DESC");
          $statement->execute();
          $result = $statement->fetchAll();
          $output = '';
          $output .= '
        <table class="table table-sm table-striped table-bordered" id="datatable" width="100%" cellspacing="0">
        <thead class="thead-light">
        <tr>
        <th>Ressource</th>
        <th>Projet</th>
        <th>Label</th>
        <th>Heures</th>
        <th class="text-center">Fini</th>
        <th class="text-center">Action</th>
        </tr>
        </thead>
        <tbody id="myTable">
        ';
        if ($statement->rowCount() > 0) {
          foreach ($result as $row) {
            $output .= '
        <tr>
        <td>' . $row["user_prenom"] . ' ' . $row["user_nom"] . '</td>
        <td>' . $row["projet_nom"] . '</td>
        <td>' . $row["tache_label"] . '</td>
        <td>' . $row["tache_heure"] . '</td>';
        if ($row["tache_done"] == null)
          $output .= '<td></td>';
        else
          $output .= '<td>' . date("d/m/Y", strtotime($row["tache_done"])) . '</td>';

          $output .='<td><div class="btn-group d-flex" role="group" ><button  id="' . $row["tache_pk"] . '" class="btn btn-warning update"><i class="fa fa-pencil" aria-hidden="true"></i></button><button  id="' . $row["tache_pk"] . '" class="btn btn-danger delete"><i class="fa fa-trash" aria-hidden="true"></i></button></div></td>';
          }
        } else {
          $output .= '
          <tr>
          <td align="center" colspan="10">Pas de données</td>
          </tr>
          ';
        }
        $output .= '</tbody></table>';
        print $output;
      }

      if ($_POST["action"] == "RemplirTableauPerso") {
        $numero = $_POST["idAffiche"];

        $statement = $connection->prepare("SELECT *
        FROM tache
        inner JOIN user ON tache_fk_user = user_pk
        INNER JOIN projet ON  tache_fk_projet = projet_pk
        WHERE tache_fk_sprint = $numero
        AND user_pk = " . $_SESSION['user']['id'] . "
        ORDER BY tache_pk DESC");
          $statement->execute();
          $result = $statement->fetchAll();
          $output = '';
          $output .= '
        <table class="table table-sm table-striped table-bordered" id="datatable" width="100%" cellspacing="0">
        <thead class="thead-light">
        <tr>
        <th>Projet</th>
        <th>Label</th>
        <th>Heures</th>
        <th class="text-center">Fini</th>
        <th class="text-center">Action</th>
        </tr>
        </thead>
        <tbody id="myTable">
        ';
        if ($statement->rowCount() > 0) {
          foreach ($result as $row) {
            $output .= '
        <tr>
        <td>' . $row["projet_nom"] . '</td>
        <td>' . $row["tache_label"] . '</td>
        <td>' . $row["tache_heure"] . '</td>';
        if ($row["tache_done"] == null)
          $output .= '<td></td>';
        else
          $output .= '<td>' . date("d/m/Y", strtotime($row["tache_done"])) . '</td>';

          $output .='<td><div class="btn-group d-flex" role="group" ><button  id="' . $row["tache_pk"] . '" class="btn btn-warning update"><i class="fa fa-pencil" aria-hidden="true"></i></button><button  id="' . $row["tache_pk"] . '" class="btn btn-danger delete"><i class="fa fa-trash" aria-hidden="true"></i></button></div></td>';
          }
        } else {
          $output .= '
          <tr>
          <td align="center" colspan="10">Pas de données</td>
          </tr>
          ';
        }
        $output .= '</tbody></table>';
        print $output;
      }

      if ($_POST["action"] == "RessourcePivotalPerso") {
        $output = array();
        $statement = $connection->prepare(
          "SELECT user_pk, user_apiPivotal
          FROM user
          WHERE user_pk = " . $_SESSION['user']['id']);
        $statement->execute();
        $result = $statement->fetch();

        print json_encode($result);
      }

      if ($_POST["action"] == "RemplirTableauRessources") {
        $numero = $_POST["idAffiche"];

        $statement = $connection->prepare("SELECT * from 

        (
          SELECT sum(tache_heure) AS Planifié,
          sprint_attribuable - sum(tache_heure) AS Planifiable,
          user_prenom,
          user_initial,
          tache_fk_user
          FROM tache
          inner JOIN user ON user_pk = tache_fk_user
          inner join sprint on tache_fk_sprint = sprint_pk
          WHERE sprint_pk = $numero
          GROUP BY tache_fk_user 

          union 

          select 0,  (
              SELECT sprint_attribuable
              FROM sprint
              WHERE sprint_pk = $numero
            ), user_prenom, user_initial, user_pk from user where user_pk not in (
          select tache_fk_user from tache where tache_fk_sprint = $numero group by tache_fk_user)
          and user_doesPlanification = 1
          order by Planifié desc, user_prenom
        ) as t1

        left join 

        (
          select count(*) as NbObjectif, 
          retrospective_objectif_fk_user as user_pk
          from retrospective_objectif
          where retrospective_objectif_fk_sprint = $numero
          GROUP by retrospective_objectif_fk_user

          UNION
          
          select 0,
          user.user_pk
          from user
          where user.user_pk not in (
            select retrospective_objectif_fk_user
            from retrospective_objectif
            where retrospective_objectif_fk_sprint = $numero
            GROUP by retrospective_objectif_fk_user
          )
          and user.user_doesPlanification = 1
        ) as t2
        
        on t1.tache_fk_user = t2.user_pk

        order by t1.Planifié desc, t1.user_prenom asc
        ");
          $statement->execute();
          $result = $statement->fetchAll();
          $output = '';
          $output .= '
        <table class="table table-sm table-bordered" id="datatable" width="100%" cellspacing="0">
        <thead class="thead-light">
        <tr>
        <th>Ressource</th>
        <th>Planifié (Dispo)</th>
        <th>Objectif</th>
        </tr>
        </thead>
        <tbody id="myTable">
        ';
        if ($statement->rowCount() > 0) {
          foreach ($result as $row) {

            if ($row["Planifiable"] <= 0){
              $CouleurPlanif = "bg-success";
            }
            else{
              $CouleurPlanif = "bg-danger";
            }

            if ($row["NbObjectif"] >= 2){
              $CouleurObj = "bg-success";
            }
            else{
              $CouleurObj = "bg-danger";
            }

            $output .= '
            <tr>
              <td>' . $row["user_prenom"] . ' ' . $row["user_initial"] . '</td>
              <td class="text-white ' . $CouleurPlanif . '">' . $row["Planifié"] . ' (' . $row["Planifiable"] . ')</td>
              <td class="text-center text-white ' . $CouleurObj . '">' . $row["NbObjectif"] . '</td>
            </tr>';
          }
        } else {
          $output .= '
        <tr>
        <td align="center" colspan="10">Pas de données</td>
        </tr>
        ';
        }
        $output .= '</tbody></table>';
        print $output;
      }

      //Créer une tache depuis la méthode x+x+x
      if ($_POST["action"] == "Attribuer") {
        $TableauHeurePlanifie = $_POST["NombreHeure"];
        $statement = $connection->prepare("
        INSERT INTO tache (tache_heure, tache_fk_sprint, tache_fk_user, tache_fk_projet, tache_label) 
        VALUES (:NombreHeure, :idSprint, :idEmploye, :idProjet, :Label)
        ");
        for ($i = 0; $i < count($TableauHeurePlanifie); $i++) {

          $result = $statement->execute(
            array(
              ':NombreHeure' => intval($TableauHeurePlanifie[$i]),
              ':idSprint' => $_POST["idSprint"],
              ':Label' => "Label inconnue",
              ':idEmploye' => $_POST["idEmploye"],
              ':idProjet' => $_POST["idProjet"]
            )
          );
        }
        if (!empty($result))
          print true;
        else
          print_r($statement->errorInfo());
      }

      //Créer une tache depuis l'api
      if ($_POST["action"] == "CreerTacheApi") {
        $ListeTache = $_POST["ListeTaches"];
        $statement = $connection->prepare("
        INSERT INTO tache (tache_heure, tache_fk_sprint, tache_fk_user, tache_fk_projet, tache_label, tache_done, tache_pivotal_id_Task, tache_pivotal_id_Story, tache_pivotal_id_Project, tache_pivotal_Label_Story) 
        VALUES (:NombreHeure, :idSprint, :idEmploye, :idProjet, :Label, :Done, :pivotal_id_Task, :pivotal_id_Story, :pivotal_id_Project, :pivotal_Label_Story)
        ");
        for ($i = 2; $i < count($ListeTache); $i++) {

          if ($ListeTache[$i]['done'] == null)
            $ListeTache[$i]['done'] = null;

          $result = $statement->execute(
            array(
              ':NombreHeure' => intval($ListeTache[$i]['heure']),
              ':idSprint' => $_POST["Sprint"],
              ':Label' => $ListeTache[$i]['label'],
              ':idEmploye' => $_POST["Employe"],
              ':idProjet' => $_POST["Projet"],
              ':Done' => $ListeTache[$i]['done'],
              ':pivotal_id_Task' => $ListeTache[$i]['TaskId'],
              ':pivotal_id_Story' => $ListeTache[$i]['StoryId'],
              ':pivotal_id_Project' => $ListeTache[$i]['ProjectId'],
              ':pivotal_Label_Story' => $ListeTache[$i]['StoryLabel']
            )
          );
        }
        if (!empty($result))
          print true;
        else
          print_r($statement->errorInfo());
      }

      //Creer des taches de type scrum planing 
      if ($_POST["action"] == "AttributionScrumPlaning") {
        $TableauEmploye = $_POST["idEmploye"];
        $numero = $_POST["idSprint"];
        $statement = $connection->prepare("INSERT INTO tache (tache_heure, tache_fk_sprint, tache_fk_user, tache_fk_projet, tache_type, tache_label) 
        VALUES (:NombreHeure, :idSprint, :idEmploye, (select projet_pk FROM projet WHERE projet_nom like 'ScrumPlanning'), :TypeTache, :LabelTache)");
        for ($i = 0; $i < count($TableauEmploye); $i++) {

          $result = $statement->execute(
            array(
              ':NombreHeure' => intval($_POST["NombreHeure"]),
              ':idSprint' => intval($_POST["idSprint"]),
              ':idEmploye' => intval($TableauEmploye[$i]),
              ':TypeTache' => $_POST["idTypeTache"],
              ':LabelTache' => $_POST["idTypeTache"]
            )
          );
          if (!empty($result))
            print true;
          else
            print_r($statement->errorInfo());
        }
      }

      if ($_POST["action"] == "NbHeureRestantePlanifiable") {
        $output = array();
        $idSprint = $_POST["Sprint"];
        $idRessource = $_POST["Employe"];
        $statement = $connection->prepare(
          "SELECT
        sprint_attribuable - sum(tache_heure) as Attribuable,
        (select sprint_attribuable from sprint where sprint_pk = $idSprint) as AttribuablePD
        FROM tache
        INNER JOIN sprint on sprint_pk = tache_fk_sprint
        where tache_fk_sprint = $idSprint
        and tache_fk_user = $idRessource"
        );
        $statement->execute();
        $result = $statement->fetch();

        if ($result["Attribuable"] == null)
          $output["Attribuable"] = intval($result["AttribuablePD"]);
        else
          $output["Attribuable"] = intval($result["Attribuable"]);

        print json_encode($output);
      }
    }

    ?> 