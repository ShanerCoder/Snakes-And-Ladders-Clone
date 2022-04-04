var playerOneSpace = 1;
var playerTwoSpace = 1;


function dataCheck() {
    let boardSize = document.getElementById("boardSize").value;
    let userName1 = document.getElementById("userName1").value;
    let userName2 = document.getElementById("userName2").value;
    if (boardSize != '' && userName1 != '' && userName2 != '' && (boardSize >= 6 && boardSize <= 10)) {
        $("#startGame").show(); 
    }
    else {
        $("#startGame").hide(); 
    }
}

function generateAscendingRow(startingRow, boardSize, currentBoard, wormholes, linkedwormholes, blackholes, linkedblackholes) {
    let board = currentBoard;
    for (let i = boardSize-1; i >= 0; i--) {
        board = board + "<div class='grid-item'>" + (startingRow - i);
        board = checkHoleSpaces(startingRow - i, board, wormholes, linkedwormholes, blackholes, linkedblackholes)
        board = checkPlayerSpaces(startingRow - i, board)
        board = board + "</div>"
    }
    return board;
}

function generateDescendingRow(startingRow, boardSize, currentBoard, wormholes, linkedwormholes, blackholes, linkedblackholes) {
    let board = currentBoard;
    for (let i = 0; i < boardSize; i++) {
        board = board + "<div class='grid-item'>" + (startingRow - i);
        board = checkHoleSpaces(startingRow - i, board, wormholes, linkedwormholes, blackholes, linkedblackholes)
        board = checkPlayerSpaces(startingRow - i, board)
        board = board + "</div>"
    }
    return board;
}

function checkPlayerSpaces(spaceNumberToCheck, currentBoard) {
    let board = currentBoard;
    if (spaceNumberToCheck == playerOneSpace && playerOneSpace==playerTwoSpace) {
        board = board + "<br><img class='playerIcon' src='../Media/red-rocketship.png' alt='PlayerOneIcon'>";
        board = board + "<img class='playerIcon player2Icon' src='../Media/blue-rocketship.png'  alt='PlayerTwoIcon'>";
    }
    else {
    if (spaceNumberToCheck == playerOneSpace) board = board + "<br><img class='playerIcon' src='../Media/red-rocketship.png' alt='PlayerOneIcon'>";
    if (spaceNumberToCheck == playerTwoSpace) board = board + "<br><img class='playerIcon player2Icon' src='../Media/blue-rocketship.png' alt='PlayerTwoIcon'>";
    }
    return board;
}

function checkHoleSpaces(currentSpace, currentBoard, wormholes, linkedwormholes, blackholes, linkedblackholes) {
    let board = currentBoard;
    indexOfWormhole = wormholes[0].indexOf(currentSpace);
    indexOfWormholeLink = linkedwormholes.indexOf(currentSpace);
    indexOfBlackhole = blackholes[0].indexOf(currentSpace);
    indexOfBlackholeLink = linkedblackholes.indexOf(currentSpace);
    if ((indexOfWormhole != -1 && wormholes[1][indexOfWormhole]) == true || (indexOfWormholeLink != -1 && wormholes[1][indexOfWormholeLink]) == true) {
        board = board + "<br><img class='holeIcon' src='../Media/wormhole.png' alt='WormholeIcon'>"
    }
    //else if (indexOfBlackhole != -1 || indexOfBlackholeLink != -1) {
    else if ((indexOfBlackhole != -1 && blackholes[1][indexOfBlackhole]) == true || (indexOfBlackholeLink != -1 && blackholes[1][indexOfBlackholeLink]) == true) {
        board = board + "<br><img class='holeIcon' src='../Media/blackhole.png' alt='BlackholeIcon'>"
    }
        return board;
}

function updateScoreLabels(playerOneName, playerTwoName) {
    document.getElementById('playerOneScore').innerHTML = playerOneName + ": " + playerOneSpace;
    document.getElementById('playerTwoScore').innerHTML = playerTwoName + ": " + playerTwoSpace;
}

$(document).ready(function() {

    let test = false;

    // Initialise the screen to wait for the player name 

    $("#userName1").keypress(function(){ 
        dataCheck();
    }); 

    $("#userName2").keypress(function(){ 
        dataCheck();
    }); 

    $("#boardSize").keypress(function(){ 
        dataCheck();
    }); 

    $("#startNewGame").hide();

    $("#roll").hide();

    if (test != true) $("#startGame").hide();

    $("#currentScores").hide();

    $("#resetGame").hide();

    $("#board").html("");

    $("#message").hide();

    $("#startGame").on("click dblclick", function(evt) {

        evt.preventDefault();

        $("#startGame").hide();

        $("#dataForEntry").hide();

        $("#startNewGame").hide();

        $("#message").show();

        $('#gameForm').submit();

    });

    $('#gameForm').submit(function(evt) {

        evt.preventDefault();

        // Collect from the form the URL and current data values for the row and column 

        var form_url = $(this).attr("action");

        var form_data = $(this).serialize();

        //console.log(form_data);

        $.ajax({

            url: form_url,

            type: "POST",

            data: {

                data: form_data

            },

            // Assume PHP will generate JSON to update the web page without refresh 

            dataType: "json",

            success: function(returnData) {

                console.dir(returnData);

                // Reset the board to display the result in row and columns 

                $("#board").html("");
                if (returnData.wormhole_positions[0][0] == returnData.wormhole_positions[0][1] || returnData.wormhole_positions[0][1] == returnData.wormhole_positions[0][2] || returnData.wormhole_positions[0][2] == returnData.wormhole_positions[0][0]) {
                    alert(returnData.wormhole_positions[0][0]);
                    alert(returnData.wormhole_positions[0][1]);
                    alert(returnData.wormhole_positions[0][2]);
                }
                //alert(returnData.wormhole_linked_positions[0]);
                //alert(returnData.wormhole_linked_positions[1]);
                //alert(returnData.wormhole_linked_positions[2]);
                let boardSize = returnData.board_size;
                let currentSpace = boardSize * boardSize;
                let board = "";
                playerOneSpace = returnData.player_one_space;
                playerTwoSpace = returnData.player_two_space;
                updateScoreLabels(returnData.player_one_name, returnData.player_two_name);

                board = board + "<div class='grid-container' style='grid-template-columns: ";
                for (let i = 0; i < boardSize; i++) {
                    board = board + "auto ";
                }
                board = board + "'>";

                if (boardSize % 2 != 0) {
                    board = generateAscendingRow(currentSpace, boardSize, board, returnData.wormhole_positions, returnData.wormhole_linked_positions, returnData.blackhole_positions, returnData.blackhole_linked_positions);
                    currentSpace -= boardSize;
                }

                while (currentSpace > 0) {
                    board = generateDescendingRow(currentSpace, boardSize, board, returnData.wormhole_positions, returnData.wormhole_linked_positions, returnData.blackhole_positions, returnData.blackhole_linked_positions);
                    currentSpace -= boardSize;
                    board = generateAscendingRow(currentSpace, boardSize, board, returnData.wormhole_positions,  returnData.wormhole_linked_positions, returnData.blackhole_positions, returnData.blackhole_linked_positions);
                    currentSpace -= boardSize;
                }

                board = board + "</div>"
                $("#board").html(board);



                // Checking the status and if not 'PLAY' then allow for a new game 

                $("#message").html(returnData.message);

                if (returnData.status != "play") {

                    $("#startNewGame").show();
                    $("#headerTitle").show();
                    $("#currentScores").hide();
                    $("#resetGame").hide();
                    $("#roll").hide();

                } else {

                    $("#startNewGame").hide();
                    $("#headerTitle").hide();
                    $("#currentScores").show();
                    $("#resetGame").show();
                    $("#roll").show();

                }

            },

            error: function(res) {

                console.dir(res);

                $("#message").html("An error has occurred! Please reset the current session.");

            }

        });

    });


    $('#resetForm').submit(function(evt) { 

        evt.preventDefault(); 
        
        $.ajax({ 
        
          cache: false,
        
          url: "reset-session.php",
        
          type: "POST", 
        
          success: function (returnData) { 
        
            

            $("#startGame").show();

            $("#dataForEntry").show();
            $("#headerTitle").show();

            $("#currentScores").hide();
            $("#roll").hide();
        
            $("#board").html("");
            $("#message").hide();

            $("#resetGame").hide(); 
        
          }, 
        
          error: function (res){ 
        
            console.dir(res); 
        
            $("#message").html("Not Good!"); 
        
          } 
        
        }); 
        
        });  

}); // end ready 