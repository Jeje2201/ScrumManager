  <body class="fixed-nav sticky-footer" id="page-top">
    <div class="content-wrapper">
      <div class="container">

        <div class="mb-3">
          <div class="form-row">
            <div class="col-md-3">
              <button type="button" id="modal_button" class="btn btn-info">Créer une interférance</button>

            </div>
          </div>
          <br />

          <input class="form-control" id="BarreDeRecherche" type="text" placeholder="Rechercher..">

          <div id="result" class="table-responsive table-striped table-hover"> 

          </div>
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

        <div class="form-group">
          <label>Prénom</label>
          <input class="form-control" name="Prenom" id="Prenom" type="text" placeholder="Jackouille">
        </div>

        <div class="form-group">
          <label>Nom</label>
          <input class="form-control" name="Nom" id="Nom" type="text"placeholder="LaFripouille">
        </div>

        <div class="form-group">
        <label>Type</label>
        <select class="form-control" id="TypeEmploye" name="TypeEmploye">
          <?php
          $result = $conn->query("select id, nom from typeemploye order by nom");
          while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $nom = $row['nom']; 
            echo '<option value="'.$id.'"> '.$nom.' </option>';
          }
          ?>
        </select>
        </div>

        <div>
          <label>Actif</label>
          <input id="Actif" type="checkbox" checked>
        </div>

      </div>
      <div class="modal-footer">
        <input type="hidden" name="id" id="id" />
        <input type="submit" name="action" id="action" class="btn btn-success" />
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<script>

  $(document).ready(function(){

    BarreDeRecherche('BarreDeRecherche','result');

    fetchUser(); 

    function fetchUser() 
    {
      var action = "Load";
      $.ajax({
       url : "Modele/ActionInterferance.php", 
       method:"POST", 
       data:{action:action}, 
       success:function(data){
        $('#result').html(data); 
      }
    });
    }

    $('#modal_button').click(function(){
      $('#customerModal').modal('show'); 
      $('.modal-title').text("Ajouter un employé"); 
      $('#action').val('Ajouter'); 
      $('#Prenom').val('');
      $('#Nom').val('');
      $('#Actif').prop( "checked", true ); 
    });

    $('#action').click(function(){
      var Nom_Employe = $('#Nom').val();
      var Prenom_Employe = $('#Prenom').val();
      var Type_Employe = $('#TypeEmploye').val();
      if (document.getElementById("Actif").checked == true){
        var Actif = 1;
      } else {
        var Actif = 0;
      }

      var Initial = Prenom_Employe.charAt(0)+Nom_Employe.charAt(0);
      var action = $('#action').val();
      var id = $('#id').val();

      if(Nom_Employe != '' && Prenom_Employe != '' && Type_Employe != '') 
      {
       $.ajax({
        url : "Modele/ActionInterferance.php",    
        method:"POST",     
        data:{id:id, Nom_Employe:Nom_Employe, Prenom_Employe:Prenom_Employe, Actif:Actif, Initial:Initial, Type_Employe:Type_Employe, action:action}, 
        success:function(data){
         BootstrapAlert(data);
         console.log(data);
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

    $(document).on('click', '.update', function(){
      var id = $(this).attr("id"); 
      var action = "Select";
      $.ajax({
       url : "Modele/ActionInterferance.php",   
       method:"POST",    
       data:{id:id, action:action},
       dataType:"json",   
       success:function(data){
        $('#customerModal').modal('show');   
        $('.modal-title').text("Mettre à jour"); 
        $('#action').val("Update");  

        if(data.Actif ==1)
          $('#Actif').prop('checked', true);
        else
          $('#Actif').prop('checked', false);
        
        $('#id').val(id); 
        $('#Prenom').val(data.Prenom);  
        $('#Nom').val(data.Nom); 
        $('#TypeEmploye').val(data.TypeEmploye);

      }
    });
    });

    $(document).on('click', '.delete', function(){
      var id = $(this).attr("id"); 
      if(confirm("Es-tu sûr de vouloir supprimer cet(te) employé(e) ?")) 
      {
       var action = "Delete"; 
       $.ajax({
        url : "Modele/ActionInterferance.php",    
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
</script>