
  <body class="fixed-nav sticky-footer" id="page-top">
    <div class="content-wrapper">
      <div class="container">

        <div class="mb-3">
          <div class="form-row">
            <div class="col-md-2">
              <!-- <a href="javascript:demoFromHTML()">WoW</a>  -->
              <select class="form-control"  id="numeroSprint" name="numeroSprint">
                <?php
                $result = $conn->query("select id, numero from sprint order by numero desc");

                while ($row = $result->fetch_assoc()) {
                  unset($id, $numero);
                  $id = $row['id'];
                  $numero = $row['numero']; 
                  echo '<option value="'.$id.'"> ' .$numero. ' </option>';
                }
                ?> 
              </select>
            </div>
            <div class="col-md-5">
              <button type="button" id="modal_button" class="btn btn-info">Créer un objectif</button>
            </div>
            <div class="col-md-5">
              <button type="button" id="Bouttonretrospective" class="btn btn-info">Créer une rétrospective</button>
            </div>

          </div>
          <br />

          <input class="form-control" id="BarreDeRecherche" type="text" placeholder="Rechercher..">

          <h3> Objectif(s) </h3>

          <div id="result" class="table-responsive"></div>

          <h3> Rétrospective(s) </h3>

          <div id="retrospective" class="table-responsive"></div>

        </div>
      </div>
    </body>
    </html>

    <div id="customerModal" class="modal fade">
     <div class="modal-dialog">
      <div class="modal-content">
       <div class="modal-header">
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <label>Projet</label>
        <select class="form-control"  id="projetId" name="projetId">
          <?php
          $result = $conn->query("select id, nom from projet order by nom");

          while ($row = $result->fetch_assoc()) {
            unset($id, $nom);
            $id = $row['id'];
            $nom = $row['nom']; 
            echo '<option value="'.$id.'"> ' .$nom. ' </option>';
          }
          ?>
        </select>
        <br />
        <label>Objectif</label>
        <input class="form-control" name="LabelObjectif" id="LabelObjectif" type="text" placeholder="Je suis un objectif.." >
        <br />
         <label>Etat</label>
        <form class="form-control" name="EtatObjectif" id="EtatObjectif">
          <?php
          $result = $conn->query("select id, nom from statutobjectif order by nom");
          while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $nom = $row['nom']; 
            echo '<input type="radio" name="Etat" id="EtatNum'.$id.'" value="'.$id.'"> '.$nom.'<br>';
          }
          ?>
        </form>
      </div>
      <div class="modal-footer">
        <input type="hidden" name="id" id="id" />
        <input type="submit" name="action" id="action" class="btn btn-success" />
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>


    <div id="Modalretrospective" class="modal fade">
     <div class="modal-dialog">
      <div class="modal-content">
       <div class="modal-header">
        <h4 class="modal-title">Remarque</h4>
      </div>
      <div class="modal-body">
        <input class="form-control" name="Labelretrospective" id="Labelretrospective" type="text" placeholder="Je suis une rétrospective.." >
      </div>
      <div class="modal-footer">
        <input type="submit" name="CreerRetrospective" id="CreerRetrospective" class="btn btn-success" />
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<script>

  $(document).ready(function(){

    $("#BarreDeRecherche").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#myTable tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });

     fetchUser(); 

    function fetchUser() 
    {
      var idAffiche = $('#numeroSprint').val();
      var action = "Load";
      $.ajax({
       url : "Modele/ActionObjectifs.php", 
       method:"POST", 
       data:{action:action, idAffiche:idAffiche}, 
       success:function(data){
        $('#result').html(data); 
      }
    });

      var action = "retrospective";
      $.ajax({
       url : "Modele/ActionObjectifs.php", 
       method:"POST", 
       data:{action:action}, 
       success:function(data){
        $('#retrospective').html(data); 
      }
    });

    }

    $('#numeroSprint').change(function(){
      fetchUser();
      $('td:nth-child(4),th:nth-child(4)').hide();
    });

    $('#modal_button').click(function(){
      $('#customerModal').modal('show'); 
      $('.modal-title').text("Créer un objectif"); 
      $('#action').val('Créer');
      $('#LabelObjectif').val("");
      $('#EtatNum5').prop("checked", true);
    });

    $('#Bouttonretrospective').click(function(){
      $('#Modalretrospective').modal('show'); 
      $('.modal-title').text("Créer une rétrospective"); 
      $('#Labelretrospective').val("");
      $('#EtatNum5').prop("checked", true);
    });

    $('#action').click(function(){
      var idSprint = $('#numeroSprint').val();
      var idProjet = $('#projetId').val();
      var LabelObjectif = $('#LabelObjectif').val();
      var EtatObjectif = $('input[name=Etat]:checked', '#EtatObjectif').val()
      var id = $('#id').val();
      var action = $('#action').val();  
      if(idSprint != '' && LabelObjectif != ''  && EtatObjectif != '' && idProjet != '') 
      {
       $.ajax({
        url : "Modele/ActionObjectifs.php",    
        method:"POST",     
        data:{id:id, idSprint:idSprint, LabelObjectif:LabelObjectif, EtatObjectif:EtatObjectif, idProjet:idProjet, action:action}, 
        success:function(data){
         BootstrapAlert(data);
         $('#customerModal').modal('hide'); 
         fetchUser();    
       }
     });
     }
     else
     {
       alert("Tous les champs doivent être plein."); 
     }
   });

      $('#CreerRetrospective').click(function(){

      var Labelretrospective = $('#Labelretrospective').val();
      var action = "CréerRetrospective";  
      if(Labelretrospective != '') 
      {
       $.ajax({
        url : "Modele/ActionObjectifs.php",    
        method:"POST",     
        data:{Labelretrospective:Labelretrospective, action:action}, 
        success:function(data){
         BootstrapAlert(data);
         $('#Modalretrospective').modal('hide'); 
         fetchUser();    
       }
     });
     }
     else
     {
       alert("Tous les champs doivent être plein."); 
     }
   });

    $(document).on('click', '.success', function(){
      var id = $(this).attr("id"); 
      var action = "retrospectiveFini";   
      $.ajax({
       url : "Modele/ActionObjectifs.php",   
       method:"POST",
       data:{id:id, action:action},
       success:function(data){
        BootstrapAlert(data);
        fetchUser();
      }
    });
    });

    $(document).on('click', '.update', function(){
      var id = $(this).attr("id"); 
      var action = "Select";   
      $.ajax({
       url : "Modele/ActionObjectifs.php",   
       method:"POST",    
       data:{id:id, action:action},
       dataType:"json",   
       success:function(data){
        $('#customerModal').modal('show');   
        $('.modal-title').text("Editer objectif"); 
        $('#action').val("Changer");
        $('#id').val(id); 
        $('#projetId').val(data.id_Projet);
        $('#LabelObjectif').val(data.objectif);
        $('#EtatNum'+data.id_StatutObjectif).prop("checked", true);
      }
    });
    });

    $(document).on('click', '.delete', function(){
      var id = $(this).attr("id"); 
      if(confirm("Es-tu sûr de vouloir supprimer ce sprint?")) 
      {
       var action = "Delete"; 
       $.ajax({
        url : "Modele/ActionObjectifs.php",    
        method:"POST",     
        data:{id:id, action:action}, 
        success:function(data)
        {
         fetchUser();    
         BootstrapAlert(data);
       }
     })
     }
     else  
     {
       return false; 
     }
   });
  });

  function demoFromHTML() {
    
        var pdf = new jsPDF('p', 'pt', 'letter');
        // source can be HTML-formatted string, or a reference
        // to an actual DOM element from which the text will be scraped.
        source = $('#result')[0];

        // we support special element handlers. Register them with jQuery-style 
        // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
        // There is no support for any other type of selectors 
        // (class, of compound) at this time.
        specialElementHandlers = {
            // element with id of "bypass" - jQuery style selector
            '#bypassme': function (element, renderer) {
                // true = "handled elsewhere, bypass text extraction"
                return true
            }
        };
        margins = {
            top: 80,
            bottom: 60,
            left: 40,
            width: 522
        };
        // all coords and widths are in jsPDF instance's declared units
        // 'inches' in this case
        pdf.fromHTML(
            source, // HTML string or DOM elem ref.
            margins.left, // x coord
            margins.top, { // y coord
                'width': margins.width, // max width of content on PDF
                'elementHandlers': specialElementHandlers
            },

            function (dispose) {
                // dispose: object with X, Y of the last line add to the PDF 
                //          this allow the insertion of new lines after html
                pdf.save('Test.pdf');
            }, margins
        );
    }
</script>