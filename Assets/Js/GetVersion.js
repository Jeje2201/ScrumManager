function GetLatestVersion(Div) {

    var url = 'https://api.github.com/repos/Jeje2201/ScrumManager/releases';

    // API request pour obtenir les infos de l'utilisateur et lui hacker sa base de donnée parceque nous bah on est des hackers d'abord
    $.ajax({
        url: url,
        statusCode: {
            403: function () {
                alert("Mhm.. problème de connexion à l'api de Github..");
            }
        },
        success: function (data) {

            var info = "";
            for (var i = 0; i < data.length; i++) {

               info += '<p><b>'+ data[i].name + ' </b>( Sortie le: '+data[i].published_at+' )</p>'
               info += data[i].body
               info += ' <hr><br>'
           }

           $('#'+Div).html(info);
       }
   })
}