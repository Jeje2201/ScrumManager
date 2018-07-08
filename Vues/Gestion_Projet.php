  <body class="fixed-nav sticky-footer" id="page-top">
    <div class="content-wrapper">
      <div class="container">

        <div class="mb-3">
          <div class="form-row">
            <div class="col-md-3">
              <button type="button" id="modal_button" class="btn btn-info">Créer un projet</button>

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
          <label>Nom</label>
          <input class="form-control" name="Nom" id="Nom" type="text"placeholder="Nom..">
        </div>

        <div class="form-group">
          <label>Type</label>
          <div id="listeTypeProjet"></div>
        </div>

        <div class="form-group">
          <label>Icone (50x50)</label>
          <div id='ListImage'></div>
          <br>
          <img id="IconPreview" src='Assets/Image/Projets/inconnue.png' width="35" height="35">
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

    RemplirListTypeTypeProjet('listeTypeProjet')

    RemplirListImages('ListImage');

    fetchUser();

    function fetchUser() 
    {
      var action = "Load";
      $.ajax({
       url : "Modele/ActionGestionProjet.php", 
       method:"POST", 
       data:{action:action}, 
       success:function(data){
        $('#result').html(data); 
      }
    });
    }

    function RemplirListImages(Div)
    {
      var action = "LoadPictures";
      $.ajax({
       url : "Modele/ActionGestionProjet.php", 
       method:"POST", 
       data:{action:action}, 
       success:function(data){
        $('#'+Div+'').html(data); 
      }
    });

    }

    $('#ListImage').change(function(){
      $("#IconPreview").attr("src", 'Assets/Image/Projets/'+$('#ToutesLesImages').val()+'.png');
   });

    $('#modal_button').click(function(){
      $('#customerModal').modal('show'); 
      $('.modal-title').text("Ajouter un projet"); 
      $('#action').val('Ajouter'); 
      $('#Nom').val('');
    });

    $('#action').click(function(){

      var Nom = $('#Nom').val();
      var TypeProjet = $('#TypeProjet').val();
      var fileName = $('#ToutesLesImages').val();
      var action = $('#action').val();
      var id = $('#id').val();

      if(Nom != '' && TypeProjet != '' && fileName != '') 
      {
       $.ajax({
        url : "Modele/ActionGestionProjet.php",    
        method:"POST", 
        data:{id:id, Nom:Nom, TypeProjet:TypeProjet, fileName:fileName,  action:action},

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
   })



    $(document).on('click', '.update', function(){
      var id = $(this).attr("id"); 
      var action = "Select";
      $.ajax({
       url : "Modele/ActionGestionProjet.php",   
       method:"POST",    
       data:{id:id, action:action},
       dataType:"json",   
       success:function(data){
        $('#customerModal').modal('show');   
        $('.modal-title').text("Mettre à jour"); 
        $('#action').val("Update");  
        
        $('#id').val(id); 
        $('#Nom').val(data.Nom);
        $('#TypeProjet').val(data.TypeProjet);
        $('#IconeName').val(data.cheminIcone);
        $('#IconPreview').attr('src', 'Assets/Image/Projets/'+data.cheminIcone+'.png');
      }
    });
    });

    $(document).on('click', '.delete', function(){
      var id = $(this).attr("id"); 
      if(confirm("Es-tu sûr de vouloir supprimer cet(te) employé(e) ?")) 
      {
       var action = "Delete"; 
       $.ajax({
        url : "Modele/ActionGestionProjet.php",    
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