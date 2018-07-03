  <body class="fixed-nav sticky-footer" id="page-top">
    <div class="content-wrapper">
      <div class="container">

        <div class="mb-3">
          <div class="form-row">
            <div class="col-sm-3">
              <div id="ListSrint"></div>
            </div>
            <div class="col-md-3">
              <button type="button" id="modal_button" class="btn btn-info">Créer une interférance</button>

            </div>
          </div>
          <br />

          <input class="form-control" id="BarreDeRecherche" type="text" placeholder="Rechercher..">

          <div id="result" class="table-responsive table-hover"> 

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
          <label>Nombre heures</label>
      <input class="form-control" name="nbheure" id="nbheure" type="number" min="1" value="1">
        </div>

        <div class="form-group">
          <label>Employe</label>
         <div id="ListEmploye"></div>
        </div>

        <div class="form-group">
          <label>Projet</label>
          <div name="ListProjet" id="ListProjet"> </div>
        </div>

        <div class="form-group">
          <label>Type</label>
          <div name="ListTypeInterferance" id="ListTypeInterferance"> </div>
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

    RemplirListSprint('ListSrint');

    BarreDeRecherche('BarreDeRecherche','result');

    RemplirListEmployeActif('ListEmploye');

    RemplirListProjet('ListProjet');

    RemplirListTypeInterferance('ListTypeInterferance')

    RemplirTableau(); 

    function RemplirTableau() 
    {

      var idAffiche = $('#numeroSprint').val();
      var action = "Load";
      $.ajax({
       url : "Modele/ActionInterferance.php", 
       method:"POST", 
       data:{action:action, idAffiche:idAffiche}, 
       success:function(data){
        $('#result').html(data); 
      }
    });
    }

    $('#ListSrint').change(function(){
      RemplirTableau();
    });

    $('#modal_button').click(function(){
      $('#customerModal').modal('show'); 
      $('.modal-title').text("Ajouter une interférance"); 
      $('#action').val('Ajouter'); 
    });

    $('#action').click(function(){
      var idAffichee = $("#numeroSprint").val();
      var NbHeure = $('#nbheure').val();
      var Employe = $('#employeId').val();
      var Projet = $('#projetId').val();
      var TypeInterferance = $('#typeinterference').val();
      var action = $('#action').val();
      var id = $('#id').val();

      if(NbHeure != '') 
      {
       $.ajax({
        url : "Modele/ActionInterferance.php",    
        method:"POST",     
        data:{id:id, NbHeure:NbHeure, Employe:Employe, Projet:Projet, TypeInterferance:TypeInterferance, idAffichee,idAffichee, action:action}, 
        success:function(data){
         BootstrapAlert(data);
         console.log(data);
         $('#customerModal').modal('hide'); 
         RemplirTableau();
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
        
        $('#id').val(id); 
        $('#nbheure').val(data.Heure);  
        $('#employeId').val(data.Employe); 
        $('#projetId').val(data.Projet);
        $('#typeinterference').val(data.TypeInterferance);

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
         RemplirTableau();    
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