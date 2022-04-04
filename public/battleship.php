<?php

session_start(); 

const BOARDSIZE = 5; 

const GUESSES = 5; 


/** This function calculates  a random position */ 

function random_pos(){ 

    return rand(0,BOARDSIZE-1); 

}  

/** This helper function checks a position is valid */ 

function on_board($row,$col) : bool { 

    if(($row < 0 ) || ($row >= BOARDSIZE ) || 

    ($col < 0 ) || ($col >= BOARDSIZE) || 

    ($col == NULL) || ($row == NULL)){ 

        return false; 

    } 

    else { 

        return true; 

    } 

} 

/** Receive user input.  Data from the form has been serialised, so we need to parse it */ 

parse_str($_POST["data"], $guess); 


/** $guess is an array, containing all the data from the form.  The keys of the array 

 *  are the input names, while the value of the array at those keys are the user 

 * inputs 

 */ 


if(!isset($_SESSION['game_data2'])){ //Starting up the game 

    // Initialise board 

    /** This will be our new data structure for the game.  We will keep all  

     * the game data in an array.   */ 

    $game_data2 = [ 

        'board'     => array_fill(0, BOARDSIZE,  

                        array_fill(0,BOARDSIZE,"🌫") 

                    ), 

        'ship_row'  => random_pos(),

        'ship_col'  => random_pos(),

        'turn'      => 0, 

        'message'   => 'Try to sink my ship!  Choose some coordinates.', 

        'status'    => 'play', 

        'name'      => $guess["userName"], 

    ]; 

    /** and because this is the start of our game, we store the stucture in the  

     * session superglobal 

     */ 

    $_SESSION['game_data2'] = $game_data2; 

} 

else 

{ // Game has started previously 

    /** Now, we recover the game data from the superglobal as we have an ongoing  

     * game 

     */ 
 

    $game_data2 = $_SESSION['game_data2']; 

    //Update turn 

    $game_data2['turn']++; 


    // Validation 

    if(!on_board($guess['row'],$guess['col'])){ 

           $game_data2['message'] = "Oops, that's not even in the ocean."; 

    } 

    else // Has the players guessed our ship's position? 

    if (($guess['col'] == $game_data2['ship_col']) &&  

        ($guess['row'] == $game_data2['ship_row'])){ 

        $game_data2['board'][$guess['row']][$guess['col']] = '💥'; 

        $game_data2['status'] = "won"; 

        $game_data2['message'] =  "Congratulations! You sank my ship!"; 

    }     

    else  //Has the player guessed this position already? 

    if ($game_data2['board'][$guess['row']][$guess['col']] == '🌊') { 

        $game_data2['message'] = "You guessed that one already."; 

        // is it the last turn? 

        if($game_data2['turn'] == GUESSES) $game_data2['status']='lost'; 

    }     

    else { 

        //  Player has not guessed correctly - Update Board with player guess 

        $game_data2['message'] = "You missed my battleship!"; 

        $game_data2['board'][$guess['row']][$guess['col']] = '🌊'; 

        //is it the last turn? 

        if ($game_data2['turn'] == GUESSES){  

            // Game over = Update game board to reveal ship location 

            $game_data2['status']="lost"; 

            $game_data2['message'] .=" Game Over!"; 

            $game_data2['board'][$game_data2['ship_row']][$game_data2['ship_col']] = '⛴'; 

        } 

    } 

    // Prepare for next turn 

    $game_data2['message'] .= " This was turn ".$game_data2['turn']." of ".GUESSES; 

    //is it the last turn? 

    if($game_data2['turn'] == GUESSES) $game_data2['status']='lost'; 

    $_SESSION['game_data2']=$game_data2; 

} 

/** Here we return the game state to our front end, the UI for update */ 

header('Access-Control-Allow-Origin: *'); 

header('Content-type: application/json'); 

echo json_encode($game_data2); 

/** Finally, if the game has finished, we need to clear up the Session 

 * to prepare for a new game 

 */ 

if($game_data2['status'] != 'play'){ 

    $_SESSION=[]; 

    session_destroy();  // start the game fresh 

} 