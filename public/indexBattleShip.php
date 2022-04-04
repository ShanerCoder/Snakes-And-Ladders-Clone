<!DOCTYPE html> 

<html lang="en"> 

  <head> 

    <meta charset="UTF-8"> 

    <meta name="viewport" content="width=1024, initial-scale=1.0"> 

    <meta http-equiv="X-UA-Compatible" content="ie=edge"> 

    <link href='//fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'> 

    <link rel="stylesheet" href="css/mains.css"> 

  </head> 

  <body> 

    <div class="container centered"> 

      <h1>Battleships</h1> 

    </div> 

      <h1>Turn</h1> 

        <div id="attempt"> 

          <form id="myForm" method="post" action="battleship.php"> 

            <label for="userName">Name:</label> 

            <input type="text" name="userName" id="userName"> 

            <button id="startGame">Start Game</button><br> 

            <label for="row">X Coordinate:</label> 

            <input type="row" name="row" id="row" size="3"> 

            <label for="col">Y Coordinate:</label> 

            <input type="col" name="col" id="col" size="3"><br> 

            <label for="submit"></label> 

            <input type="submit" value="Fire" id="fire"> 

            <div id="newGame"> 

              <button id="startGame">Start a New Game</button> 

            </div> 

          </form> 

          <form id="myResetForm" method="post" action="reset-session.php"> 

            <button id="resetGame">Reset Game</button> 
          
          </form>

        </div> 


    <!-- Create the placeholder for the Board to update from the success of AJAX --> 

    <div id="board" class="container centered">... 

    </div> 

    <!-- Create the placeholder to communicate the result of the turn --> 

    <div id="message" class="container centered">Before Sending... 

    </div> 


    <!-- Set up the jQuery and note that the http/https is omitted to allow for either protocol --> 

    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script> 

    <script> 

      $(document).ready(function() { 

        // Initialise the screen to wait for the player name 

        $("#startGame").hide(); 

        $("#newGame").hide(); 

        $("#row").hide(); 

        $("#col").hide(); 

        $("#fire").hide(); 

        $("#resetGame").hide(); 

        $("#userName").keypress(function(){ 

          $("#startGame").show(); 

        }); 

        // Send the initial session startup request to place a ship on the board 

        $("#startGame").on("click dblclick", function(evt){ 

          evt.preventDefault(); 

          $("#startGame").hide(); 

          $("#newGame").hide(); 

          $("#row").show(); 

          $("#col").show(); 

          $("#fire").show(); 

          $('#myForm').submit(); 

        }); 

        // Allow the player to click on the grid of 'fog' and reveal if it is a hit or water 

        $("#board").on("click", "span", function(){ 

          point = $(this).attr("id"); 

          res = point.split("-"); 

          $("#row").val(res[0]); 

          $("#col").val(res[1]); 

          $('#myForm').submit(); 

        }); 


        $('#myForm').submit(function(evt) { 

          evt.preventDefault(); 

          // Collect from the form the URL and current data values for the row and column 

          var form_url = $(this).attr("action"); 

          var form_data = $(this).serialize(); 

          console.log(form_data);  

          $.ajax({ 

            url: form_url, 

            type: "POST", 

            data: { 

              data: form_data 

            }, 

            // Assume PHP will generate JSON to update the web page without refresh 

            dataType: "json", 

            success: function (returnData) { 

              console.dir(returnData); 

              // Reset the board to display the result in row and columns 

              $("#board").html(""); 

              var board = ""; 

              $.each( returnData.board, function( row, block ){ 

                board= board+"<div class='row'>"; 

                //console.log("row"+row); 

                $.each( block, function( pos, cell ){ 

                  //console.log("cell"+cell); 

                  board= board + "<span class='cell pointer' id='"+row+"-"+pos+"'>"+cell+"</span>"; 

                }); 

                board= board+"</div>"; 

              }); 

              $("#board").html(board); 
 

              // Checking the status and if not 'PLAY' then allow for a new game 

              $("#message").html(returnData.message); 

              if (returnData.status != "play") { 

                $("#newGame").show(); 
                $("#resetGame").hide(); 


              } 

              else { 

                $("#newGame").hide(); 
                $("#resetGame").show(); 

              } 

            }, 

            error: function (res){ 

              console.dir(res); 

              $("#message").html("Not Good!"); 

            } 

          }); 

        });  


$('#myResetForm').submit(function(evt) { 

evt.preventDefault(); 

$.ajax({ 

  cache: false,

  url: "reset-session.php",

  type: "POST", 

  success: function (returnData) { 


    $("#board").html(""); 

    var board = ""; 

    $("#newGame").hide(); 
    $("#resetGame").hide(); 

  }, 

  error: function (res){ 

    console.dir(res); 

    $("#message").html("Not Good!"); 

  } 

}); 

});  


      }); // end ready 

    </script> 

  </body> 

</html> 