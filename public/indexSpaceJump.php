<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=1024, initial-scale=1.0">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link href='//fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="css/mains.css">

    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    
    <script type="text/javascript" src="javascript/script.js"></script>

    <title> Space Jump </title>

</head>

<body>

    <div id="headerTitle" class="header-title">

        <h1>Space Jump</h1>

    </div>

    <div id="attempt" class="centered">

        <form id="gameForm" method="post" action="SpaceJump.php">

        <div id="dataForEntry" class="information">
            <label for="boardSize">Board Size:</label> 
            <input type="number" name="boardSize" id="boardSize" min="6" max="10"> 
            <br>
            <label for="userName1">Player 1 Name:</label> 
            <input type="text" name="userName1" id="userName1"> 
            <br>
            <label for="userName2">Player 2 Name:</label> 
            <input type="text" name="userName2" id="userName2"> 
            <br>
        </div>
        <div class="fill">
            <button id="startGame" class="fill">Start Game</button><br>

            <label for="submit"></label>

            <input type="submit" value="Roll" id="roll" class="fill">

            <button id="startNewGame" class="fill">Start a New Game</button>
        </div>
        </form>

    </div>

    <div id="message" class="container centered">Before Sending...

</div>

<div id="board">
</div>

    <!-- Create the placeholder to communicate the result of the turn -->

    <div class='centered container' id="currentScores">
        <label id="playerOneScore" class="player1Score">Player 1: SCORE</label>
        <label id="playerTwoScore" class="player2Score">Player 2: SCORE</label>
    </div>
    <div id="resetGame">
    <form id="resetForm" method="post" action="reset-session.php">
        <div class="centered">

            <button id="restart" class="fill">Reset Game</button>

        </div>

    </form>
    </div>

</body>

</html>