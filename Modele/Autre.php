   <?php

    session_start();
    require_once('Configs.php');

    if (isset($_POST["action"])) {

      //Load les fiches de temps de tout le monde dans la gestion des fiches de temps
      if ($_POST["action"] == "UrgentNotification") {
        $toto = [];
        $toto['Message'] = $Urgent_Notification[0];
        $toto['Type'] = $Urgent_Notification[1];
        print json_encode($toto);
      }

      if ($_POST["action"] == "getApiPivotalKey") {
        print $PivotalKey;
      }
    }
    ?> 