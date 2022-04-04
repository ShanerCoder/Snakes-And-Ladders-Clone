<?php

session_start(); 

$test = false;
$test2 = false;
$maxBoardLength = 10;

if ($test == true) {

    $_SESSION=[]; 

    session_destroy();  // start the game fresh 

}

function check_if_pos_exists($position, $boardSize) {
    if ($position < 0 || $position > ($boardSize * $boardSize)) return false;
    else return true;
}

function generate_random_wormhole_link_position($wormholePosition, $boardSize) {
    return rand($wormholePosition+(floor($boardSize/2)), $wormholePosition + ($boardSize*3));
}

function generate_random_blackhole_link_position($blackholePosition, $boardSize) {
    return rand($blackholePosition-(floor($boardSize/2)), $blackholePosition - ($boardSize*3));
}

function random_dice_roll(): int
{
    return rand(1,6);
}

function getPlayerSpace($row, $col)
{
    return ($row*6 + $col) - 6;
}

function findNextPlayerTurn($currentPlayerTurn): int
{
    switch ($currentPlayerTurn) {
        case 1:
            return 2;
        default:
            return 1;
    }
}

function checkHoleSpaces($game_data, $player, $diceRoll): array
{
    $wormholePositions = $game_data['wormhole_positions'][0];
    $blackholePositions = $game_data['blackhole_positions'][0];
    if (in_array($game_data[$player.'_space'], $wormholePositions) == true)
    {
        $indexOfSpace = array_search($game_data[$player.'_space'], $game_data['wormhole_positions'][0]);
        $game_data['wormhole_positions'][1][$indexOfSpace] = true;
        $game_data[$player.'_space'] = $game_data['wormhole_linked_positions'][$indexOfSpace];
        $game_data['message'] = $game_data[$player.'_name']." rolled ".$diceRoll." this turn and landed on a wormhole! They are currently on space ".$game_data[$player.'_space'];
    }
    else if (in_array($game_data[$player.'_space'], $blackholePositions) == true)
    {
        $indexOfSpace = array_search($game_data[$player.'_space'], $game_data['blackhole_positions'][0]);
        $game_data['blackhole_positions'][1][$indexOfSpace] = true;
        $game_data[$player.'_space'] = $game_data['blackhole_linked_positions'][$indexOfSpace];
        $game_data['message'] = $game_data[$player.'_name']." rolled ".$diceRoll." this turn and landed on a blackhole! They are currently on space ".$game_data[$player.'_space'];
    }
    else {
        $game_data['message'] = $game_data[$player.'_name']." rolled ".$diceRoll." this turn. They are currently on space ".$game_data[$player.'_space'];
    }
    return $game_data;
}

function calculateRoll($game_data) {
    $player = "player_one";
    $boardSize = $game_data['board_size'];
    if ($game_data['player_turn'] == 2) $player = "player_two";
    $diceRoll = random_dice_roll();
    if ($game_data[$player.'_space'] + $diceRoll > ($boardSize * $boardSize))
        {
            $game_data['message'] = $game_data[$player.'_name']." rolled ".$diceRoll." this turn. They are currently on space ".$game_data[$player.'_space']." and did not move.";
            $game_data['player_turn'] = findNextPlayerTurn($game_data['player_turn']);
            return $game_data;
        }
        $game_data[$player.'_space'] += $diceRoll;
        if ($game_data[$player.'_space'] == ($boardSize * $boardSize))
        {
            $game_data['message'] = $game_data[$player.'_name']." rolled ".$diceRoll." this turn. They are currently on space ".$game_data[$player.'_space']." and won the game!";
            $game_data['status']="finish"; 
            return $game_data;
        }
        $game_data = checkHoleSpaces($game_data, $player, $diceRoll);
        $game_data['player_turn'] = findNextPlayerTurn($game_data['player_turn']);
        return $game_data;
}

function generateHoles($boardSize, $numberOfHoles, $occupiedPositions1 = array(0), $occupiedPositions2 = array(0)): array
{
    $holeArray = array(array());
    $holePositions = array();
    $holeVisiblity = array();
    $numberOfHolesAdded = 0;
    $numberOfRemainingHoles = $numberOfHoles;
    while ($numberOfRemainingHoles > 0) {
        $randomPosition = rand($boardSize-2, $boardSize*($boardSize-1));
        if (in_array($randomPosition, $holePositions) == null
            && in_array($randomPosition, $occupiedPositions1) == null
            && in_array($randomPosition, $occupiedPositions2) == null
            && check_if_pos_exists($randomPosition, $boardSize)) 
            {
                $holePositions[$numberOfHolesAdded] = $randomPosition;
                $holeVisiblity[$numberOfHolesAdded] = false;
                $numberOfRemainingHoles--;
                $numberOfHolesAdded++;
            }
    }
    $holeArray[0] = $holePositions;
    $holeArray[1] = $holeVisiblity;
    return $holeArray;
}

function generateHoleLinks($boardSize, $numberOfHoles, $isWormhole, $positionsToLink, $occupiedPositions1 = array(0), $occupiedPositions2 = array(0)): array
{
    $holeLinkPositions = array();
    $numberOfHoleLinksAdded = 0;
    $numberOfRemainingHoles = $numberOfHoles;
    while ($numberOfRemainingHoles > 0) {
        $randomPosition = 0;
        switch($isWormhole) {
            case true:
                $randomPosition = generate_random_wormhole_link_position($positionsToLink[$numberOfHoleLinksAdded],$boardSize);
                break;
            case false:
                $randomPosition = generate_random_blackhole_link_position($positionsToLink[$numberOfHoleLinksAdded],$boardSize);
                break;
        }
        if (in_array($randomPosition, $holeLinkPositions) == null
            && in_array($randomPosition, $positionsToLink) == null
            && in_array($randomPosition, $occupiedPositions1) == null
            && in_array($randomPosition, $occupiedPositions2) == null
            && check_if_pos_exists($randomPosition, $boardSize)) {
                $holeLinkPositions[$numberOfHoleLinksAdded] = $randomPosition;
                $numberOfRemainingHoles--;
                $numberOfHoleLinksAdded++;
            }
    }
    return $holeLinkPositions;
}

/** Receive user input.  Data from the form has been serialised, so we need to parse it */ 

parse_str($_POST["data"], $roll); 


if(!isset($_SESSION['game_data']) && $test2 != true){ //Starting up the game

    $game_data = [ 

        'player_one_space'  => 1, 

        'player_one_name' => $roll["userName1"], 

        'player_two_space'  => 1, 

        'player_two_name' => $roll["userName2"], 

        'player_turn'     => 1, 

        'board_size'      => ($roll["boardSize"] > $maxBoardLength ? $maxBoardLength : $roll["boardSize"]),

        'wormhole_positions' => array(array()),

        'wormhole_linked_positions' => array(),

        'blackhole_positions' => array(array()),

        'blackhole_linked_positions' => array(),

        'message'   => $roll["userName1"].'\'s turn!', 

        'status'    => 'play'

    ];

    $boardSize = $game_data['board_size'];
    $numberOfWormholes = floor($boardSize/2);
    $numberOfBlackholes = floor($boardSize/3);

    $game_data['wormhole_positions'] = generateHoles($boardSize, $numberOfWormholes);
    $wormholePositions = $game_data['wormhole_positions'][0];

    $game_data['wormhole_linked_positions'] = generateHoleLinks($boardSize, $numberOfWormholes, true, $wormholePositions);
    $wormholeLinkedPositions = $game_data['wormhole_linked_positions'];

    $game_data['blackhole_positions'] = generateHoles($boardSize, $numberOfBlackholes, $wormholePositions, $wormholeLinkedPositions);
    $blackholePositions = $game_data['blackhole_positions'][0];

    $game_data['blackhole_linked_positions'] = generateHoleLinks($boardSize, $numberOfBlackholes, false, $blackholePositions, $wormholePositions, $wormholeLinkedPositions);
    $_SESSION['game_data'] = $game_data;

} 
else if (!isset($_SESSION['game_data'])) {
    $game_data = [ 

        'player_one_space'  => 1, 

        'player_one_name' => 'shane1',

        'player_two_space'  => 1, 

        'player_two_name' => 'shane2', 

        'player_turn'     => 1, 

        'board_size'      => 6,

        'wormhole_positions' => array(array()),

        'wormhole_linked_positions' => array(),

        'blackhole_positions' => array(array()),

        'blackhole_linked_positions' => array(),

        'message'   => 'shane1\'s turn!', 

        'status'    => 'play',

        'debug'     => array(),

        'debug2'    => array(),

        'debug3'    => array(array())

    ];

    $boardSize = $game_data['board_size'];
    $numberOfWormholes = floor($boardSize/2);
    $numberOfBlackholes = floor($boardSize/3);

    $game_data['wormhole_positions'] = generateHoles($boardSize, $numberOfWormholes);
    $wormholePositions = $game_data['wormhole_positions'][0];

    $game_data['wormhole_linked_positions'] = generateHoleLinks($boardSize, $numberOfWormholes, true, $wormholePositions);
    $wormholeLinkedPositions = $game_data['wormhole_linked_positions'];
    
    $game_data['blackhole_positions'] = generateHoles($boardSize, $numberOfBlackholes, $wormholePositions, $wormholeLinkedPositions);
    $blackholePositions = $game_data['blackhole_positions'][0];

    $game_data['blackhole_linked_positions'] = generateHoleLinks($boardSize, $numberOfBlackholes, false, $blackholePositions, $wormholePositions, $wormholeLinkedPositions);
    $_SESSION['game_data'] = $game_data;
}

else 
{ 
    $game_data = $_SESSION['game_data']; 
    $game_data = calculateRoll($game_data);
    $_SESSION['game_data']=$game_data; 
} 

/** Here we return the game state to our front end, the UI for update */ 

header('Access-Control-Allow-Origin: *'); 

header('Content-type: application/json'); 

echo json_encode($game_data); 

/** Finally, if the game has finished, we need to clear up the Session 

 * to prepare for a new game 

 */ 

//$game_data['status'] = "won"; 

if($game_data['status'] != 'play'){ 

    $_SESSION=[]; 

    session_destroy();  // start the game fresh 

} 