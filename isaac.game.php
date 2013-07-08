<?php
 /**
  *------
  * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
  * Isaac implementation : © <Your name here> <Your email address here>
  *
  * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
  * See http://en.boardgamearena.com/#!doc/Studio for more information.
  * -----
  *
  * isaac.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */


require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );


class Isaac extends Table
{
	function Isaac( )
	{
        

        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();self::initGameStateLabels( array(
            "game_state" => 10,  //FIX - thought I might make use of this, but don't need for now
            //    "my_second_global_variable" => 11,
            //      ...
            //    "my_first_game_variant" => 100,
            //    "my_second_game_variant" => 101,
            //      ...
        ) );

        // If you are using a tie breaker in your game (using "player_score_aux"), you must describe here
        // the formula used to compute "player_score_aux". This description will be used as a tooltip to explain
        // the tie breaker to the players.
        // Note: if you are NOT using any tie breaker, leave the line commented.
        //
        $this->tie_breaker_description = self::_("Tie breaker points are sum length of unused pieces from placement stage");
	}

    protected function getGameName( )
    {
        return "isaac";
    }

    /*
        setupNewGame:

        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {
        $sql = "DELETE FROM player WHERE 1 ";
        self::DbQuery( $sql );

        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        //$default_colors = array( "ff0000", "008000", "0000ff", "ffa500", "773300" );
		$default_colors = array( "ffffff", "000000" );

        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES ";
        $values = array();
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."')";
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
        self::reloadPlayersBasicInfos();

        /************ Init the game board *****/

        $sql = "INSERT INTO board (board_x,board_y) VALUES ";
        $sql_values = array();
        for( $x=1; $x<=10; $x++ )
        {
            for( $y=1; $y<=10; $y++ )
            {

				//init board positions?

                $sql_values[] = "('$x','$y')";
            }
        }
        $sql .= implode( $sql_values, ',' );
        self::DbQuery( $sql );

        /************ Start the game initialization *****/

        // Init global values with their initial values
        self::setGameStateInitialValue( 'game_state', 0 );

        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        //self::initStat( 'table', 'table_teststat1', 0 );    // Init a table statistics
        //self::initStat( 'player', 'player_teststat1', 0 );  // Init a player statistics (for all players)

        // TODO: setup the initial game situation here


        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }

    /*
        getAllDatas:

        Gather all informations about current game situation (visible by the current player).

        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array( 'players' => array() );

        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
	
        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );

        // TODO: Gather all information about current game situation (visible by player $current_player_id).

	$result['board'] = self::getObjectListFromDB( "SELECT board_x x, board_y y, piece_info piece_info, head head
                                                       FROM board
                                                       WHERE head = 1 and removed = 0" );
	
	
	//FIX? - move this to function and call similar to possibleMoves on enteringState - remove other counter refresh,checkDisplayPiece code in js
	$result['countPiece'] = self::getCountPiece();
	
	//$result['canMove'] = array(1,1); //whether the player has any pieces left they can play
	
	$result['gameState'] = 	self::getGameStateValue( 'game_state' );
	
        return $result;
    }

    /*
        getGameProgression:

        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).

        This method is called each time we are in a game state with the "updateGameProgression" property set to true
        (see states.inc.php)
    */
    function getGameProgression()
    {
        // TODO: compute and return the game progression

        return 0;
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////

    /*
        In this space, you can put any utility methods useful for your game logic
    */
    
    // Get the list of returned disc when "player" we play at this place ("x", "y"),
    //  or a void array if no disc is returned (invalid move)
    /*
    function getTurnedOverDiscs( $x, $y, $board )
    {
        $turnedOverDiscs = array();
        
        if( $board[ $x ][ $y ] === null ) // If there is already a disc on this place, this can't be a valid move
        {
            // For each directions...
            $directions = array(
                array( -1,-1 ), array( -1,0 ), array( -1, 1 ), array( 0, -1),
                array( 0,1 ), array( 1,-1), array( 1,0 ), array( 1, 1 )
            );
            
            foreach( $directions as $direction )
            {
                // Starting from the square we want to place a disc...
                $current_x = $x;
                $current_y = $y;
                $bContinue = true;
                $mayBeTurnedOver = array();

                while( $bContinue )
                {
                    // Go to the next square in this direction
                    $current_x += $direction[0];
                    $current_y += $direction[1];
                    
                    if( $current_x<1 || $current_x>8 || $current_y<1 || $current_y>8 )
                        $bContinue = false; // Out of the board => stop here for this direction
                    else if( $board[ $current_x ][ $current_y ] === null )
                        $bContinue = false; // An empty square => stop here for this direction
                    else if( $board[ $current_x ][ $current_y ] != $player )
                    {
                        // There is a disc from our opponent on this square
                        // => add it to the list of the "may be turned over", and continue on this direction
                        $mayBeTurnedOver[] = array( 'x' => $current_x, 'y' => $current_y );
                    }
                    else if( $board[ $current_x ][ $current_y ] == $player )
                    {
                        // This is one of our disc
                        
                        if( count( $mayBeTurnedOver ) == 0 )
                        {
                            // There is no disc to be turned over between our 2 discs => stop here for this direction
                            $bContinue = false;
                        }
                        else
                        {
                            // We found some disc to be turned over between our 2 discs
                            // => add them to the result and stop here for this direction
                            $turnedOverDiscs = array_merge( $turnedOverDiscs, $mayBeTurnedOver );
                            $bContinue = false;
                        }
                    }
                }
            }
        }
        
        return $turnedOverDiscs;
    }
    */
    
    // Get the complete board with a double associative array
    function getBoard()
    {
        return self::getDoubleKeyCollectionFromDB( "SELECT board_x x, board_y y, piece_info piece_info FROM board", true );
    }
    
    // Get the list of possible moves (x => y => true)
    function getPossibleMoves( $player_id ) {  //FIX - no need for $player_id since board applies same to both players
        $result = array();
        
        $board = self::getBoard();
        
	//print_r($board);
	//echo $board['1']['1']."debug\n";
	//echo $board[5][3]."debug\n";
	
	for ($orient=1; $orient<=2; $orient++)
	{
	for ($z=1; $z<=5; $z++)
	{
	$pieceLimit = 8; //default for pieceType == 1
	if ($z == 2) { $pieceLimit = 7; }
	if ($z == 3) { $pieceLimit = 6; }	
	if ($z == 4) { $pieceLimit = 5; }
	if ($z == 5) { $pieceLimit = 4; }
	
	$xLimit = 10;
	$yLimit = 10;
	
	if ($orient == 1) { $yLimit = $pieceLimit; }
	if ($orient == 2) { $xLimit = $pieceLimit; }
	
	//test
	$xLimit = 4;
	$yLimit = 4;	
	
	//echo "$xLimit:$yLimit";
	
        for( $x=1; $x<=$xLimit; $x++ )
        {
            for( $y=1; $y<=$yLimit; $y++ )
            {
                //$returned = self::getTurnedOverDiscs( $x, $y, $board );
                //if( count( $returned ) == 0 )
		//echo "$board[$x][$y][0]\n";
		$flag_not_possible = False;
		/*
		if ($board[$x][$y])
		$pieceInfo = explode("_", $board[$x][$y]);
		echo ":$pieceInfo[0]";
		$pieceOrient = $pieceInfo[1];
		echo ":$pieceOrient";
		*/
		
		if ($orient == 1) { //vertical
		if (($z == 1) and ($board[$x][$y] != "" or $board[$x][$y+1] != "" or $board[$x][$y+2] != "")
		    or ($z == 2) and ($board[$x][$y] != "" or $board[$x][$y+1] != "" or $board[$x][$y+2] != "" or $board[$x][$y+3] != "")
		    or ($z == 3) and ($board[$x][$y] != "" or $board[$x][$y+1] != "" or $board[$x][$y+2] != "" or $board[$x][$y+3] != "" or $board[$x][$y+4] != "")
		    or ($z == 4) and ($board[$x][$y] != "" or $board[$x][$y+1] != "" or $board[$x][$y+2] != "" or $board[$x][$y+3] != "" or $board[$x][$y+4] != "" or $board[$x][$y+5] != "")
		    or ($z == 5) and ($board[$x][$y] != "" or $board[$x][$y+1] != "" or $board[$x][$y+2] != "" or $board[$x][$y+3] != "" or $board[$x][$y+4] != "" or $board[$x][$y+5] != "" or $board[$x][$y+6] != "")		    
		)
                {
                    $flag_not_possible = True;
                }
		} //if 
		
		if ($orient == 2) { //horizontal
			
		//if (($z == 1) and ($x == 1) and ($y == 2)) { echo "ok!"; }
		//if (($z == 1) and ($x == 1) and ($y == 2) and ($board[$x][$y] != "" or $board[$x+1][$y] != "" or $board[$x+2][$y] != "")) { echo $x."-".$y."-".$board[$x][$y].":".$board[$x+1][$y].":".$board[$x+2][$y]; }
			
		if (($z == 1) and ($board[$x][$y] != "" or $board[$x+1][$y] != "" or $board[$x+2][$y] != "")
		    or ($z == 2) and ($board[$x][$y] != "" or $board[$x+1][$y] != "" or $board[$x+2][$y] != "" or $board[$x+3][$y] != "")
		    or ($z == 3) and ($board[$x][$y] != "" or $board[$x+1][$y] != "" or $board[$x+2][$y] != "" or $board[$x+3][$y] != "" or $board[$x+4][$y] != "")
		    or ($z == 4) and ($board[$x][$y] != "" or $board[$x+1][$y] != "" or $board[$x+2][$y] != "" or $board[$x+3][$y] != "" or $board[$x+4][$y] != "" or $board[$x+5][$y] != "")
		    or ($z == 5) and ($board[$x][$y] != "" or $board[$x+1][$y] != "" or $board[$x+2][$y] != "" or $board[$x+3][$y] != "" or $board[$x+4][$y] != "" or $board[$x+5][$y] != "" or $board[$x+6][$y] != "")		    
		)
                {
		    //if (($z == 1) and ($x == 1) and ($y == 2)) { echo $x."-".$y; }
                    $flag_not_possible = True;
                }
		} //if 	
		
		if ($flag_not_possible) {
			//not a possible move	
		}
                else
                {
                    // Okay => set this coordinate to "true"
                    if( ! isset( $result[$orient] ) )
                        $result[$orient] = array();
                        
                    $result[$orient][$z][$x][$y] = true;
		    //$result[$x][$y] = true;
                }
            }  //for $y
        }  //for $x
         
	} //for $z
	} //for $orient
        return $result;
    }

    // Get piece counts left
    function getCountPiece() {
	
	$count = array();
	
	for ($i = 0; $i <=1; $i++) {
	  $count[$i] = array();
	
	  $color = 'B';
	  if ($i == 1) {$color = 'W';}
	  	  
	  for ($j = 1; $j <= 5; $j++) {	

	  $lookup = $j."_%_".$color;
	  $sql = "SELECT count(*) FROM board WHERE piece_info like '$lookup' and head = 1";
	  //echo $sql;
	  $rowCount = self::getUniqueValueFromDB( $sql );
	  //echo ":".$numRows."\n";
	  
	  //$startCount == number of each pieceType initially
	  if ($j == 1) { $startCount = 5; }
	  if ($j == 2) { $startCount = 4; }
	  if ($j == 3) { $startCount = 3; }
	  if ($j == 4) { $startCount = 2; }
	  if ($j == 5) { $startCount = 1; }	  
	  
	  $count[$i][$j] = $startCount - $rowCount;  

	  }
	}
	
	//putting dummy null at element 0 since referencing pieces 1 to 5
	
	$piece0 = array(null,$count[0][1],$count[0][2],$count[0][3],$count[0][4],$count[0][5]); //black
	$piece1 = array(null,$count[1][1],$count[1][2],$count[1][3],$count[1][4],$count[1][5]); //white
	
	//$result = array($piece0,$piece1);
	return array($piece0,$piece1);
    }
    
    // Get whether pieces have any moves left
    function getMovePiece() {
    
    	$movePiece = array(null,0,0,0,0,0);

	$move = self::getPossibleMoves( self::getActivePlayerId() );
		
	for ($orient = 1; $orient <= 2; $orient++ ) {
	  for ($pieceType = 1; $pieceType <= 5; $pieceType++ ) {
	    for ($x = 1; $x <= 10; $x++) {
	      for ($y = 1; $y <= 10; $y++) {
		if ( (isset($move[$orient][$pieceType][$x][$y])) ) {
		  
		  $movePiece[$pieceType] = 1;
		  break 2;
		}
	      }
	    }
	  }
	}
	
	//print_r($movePiece);
	//echo $move[1][1][1][1];
	
	return $movePiece;
    }

    function getPossibleMovesRemove()
    {
        $result = array();
	
	$marked_pieces = '0'; //0 is default square piece_id, not taken
	
	$sql = "SELECT player_score,player_color,player_min_remove_piecetype FROM player";
	$array_scores = self::getObjectListFromDB( $sql, $bUniqueValue=false );
	
	//foreach $scores - translate x,y - select piece_id, concat piece_id list
        foreach( $array_scores as $score) {		
	    //echo $score['player_score'].":".$score['player_color'].":";
	    if ($score['player_score'] != 0) {
			
		$x = $score['player_score'] % 10;
		$y = intval($score['player_score'] / 10);
		
		if ($x == 0) {  //FIX - correct elsewhere?
		  $x = 10;
		  $y--;
		}

		//default black
		$calc_x = 11 - $x;
		$calc_y = $y + 1;
		
		if ($score['player_color'] == 'ffffff') {
		  $calc_x = $x;
		  $calc_y = 10 - $y;
		}
	    
		$sql = "SELECT piece_id FROM board WHERE board_x = $calc_x and board_y = $calc_y";
		//echo $sql;
		$piece_id = self::getUniqueValueFromDB( $sql );
		if ($piece_id == "") { $piece_id = 0; } //default for unplayed squares that are null throughout
		$marked_pieces .= ",$piece_id";
	    }
	    
	    //pieces selected in ascending order
	    if ($score['player_color'] == '000000') { $black_min_piecetype = $score['player_min_remove_piecetype']; }
	    else { $white_min_piecetype = $score['player_min_remove_piecetype']; }
	    
        }

	
	for ($idxPlayer = 0; $idxPlayer <= 1; $idxPlayer++ ) {
          
	  $color = 'B';
	  $min_piecetype = $black_min_piecetype;
	  if ($idxPlayer == 1) {$color = 'W'; $min_piecetype = $white_min_piecetype; }
	  $result[$idxPlayer] = self::getObjectListFromDB( "SELECT board_x x, board_y y
                                                       FROM board
                                                       WHERE piece_info like '%$color' and head = 1 and removed = 0 and piece_id not in($marked_pieces) and piecetype >= $min_piecetype" );
	
	  //$x = 5; $y = 6;
	  
	  // Okay => set this coordinate to "true"
	  /*
	  if( ! isset( $result[$idxPlayer] ) ) {
	    $result[$idxPlayer] = array();
	    
	  $result[$idxPlayer][$x][$y] = true;
	  }
	  */
	}

	return $result;
    }
  
    function getPossibleMovesScore()
    {
        $result = array();
	
	$player_id = self::getActivePlayerId();
	
	//$x = 7; $y = 2; //test
	
	//check/remove other player marker from score marker option
	$sql = "SELECT player_score,player_color FROM player WHERE player_id <> $player_id";  //note: only works for 2 player game
	//echo $sql;
	$opponent = self::getObjectFromDB( $sql );
	
	$x = $opponent['player_score'] % 10;
	$y = intval($opponent['player_score'] / 10);
	
	if ($x == 0) {
	  $x = 10;
	  $y--;
	}

	//default black
	$calc_x = 11 - $x;
	$calc_y = $y + 1;
	
	if ($opponent['player_color'] == 'ffffff') {
	  $calc_x = $x;
	  $calc_y = 10 - $y;
	}	
	//echo $calc_x.":".$calc_y;
	
	$sql = "SELECT player_score FROM player WHERE player_id = $player_id";
	//echo $sql;
	$current_score = self::getUniqueValueFromDB( $sql ); 

	$sql = "SELECT player_score_potential FROM player WHERE player_id = $player_id";
	//echo $sql;
	$potential_score = self::getUniqueValueFromDB( $sql );
	//echo ":$current_score:$potential_score:";
	
	//skip scoring if zero score or no change in score
	if ($current_score == 0 and $potential_score == 0) { return; }
	if (($current_score != 0) and ($current_score == $potential_score)) { return; }	

	//$current_score++; //FIX? zero a possible move?
	
	$current_x = $current_score % 10;
	$current_y = intval($current_score / 10);

	if ($current_x == 0) {
	  $current_x = 10;
	  $current_y--;
	}
	
	$x = $potential_score % 10;
	$y = intval($potential_score / 10);

	if ($x == 0) {
	  $x = 10;
	  $y--;
	}	
	//echo ":$x:$y:";
	
	$sql = "SELECT player_color FROM player WHERE player_id = $player_id";
	//echo $sql;
	$color = self::getUniqueValueFromDB( $sql );
	
	//white=============================
	if ($color == 'ffffff') {	
	$y_start = 10 - $current_y;
	
	$y_limit = 10-$y;
	$x_given = $x;
	for ($y = $y_start; $y >= $y_limit; $y-- ) {
	    $x_limit = 10;
	    $x_begin = 1;
	    if ($y == $y_limit) { $x_limit = $x_given; }
	    if ($y == $y_start) { $x_begin = $current_x; }
	    
	    for ($x = $x_begin; $x <= $x_limit; $x++ ) {
		
		//skip other score marker
		if ($x == $calc_x and $y == $calc_y) { continue; }
		
		// Okay => set this coordinate to "true"
		if( ! isset( $result[$x] ) )
		    $result[$x] = array();
		     
		$result[$x][$y] = true;	
	    } //for $x
	} //for $y
	}
	else { //black=============================
	$y_start = $current_y+1;
	
	$y_limit = $y+1;
	$x_given = 11-$x;
	for ($y = $y_start; $y <= $y_limit; $y++ ) {
	    $x_limit = 1;
	    $x_begin = 10;
	    if ($y == $y_limit) { $x_limit = $x_given; }
	    if ($y == $y_start) { $x_begin = 11-$current_x; }
	    
	    for ($x = $x_begin; $x >= $x_limit; $x-- ) {
	
		//skip other score marker
		if ($x == $calc_x and $y == $calc_y) { continue; }
		
		// Okay => set this coordinate to "true"
		if( ! isset( $result[$x] ) )
		    $result[$x] = array();
		     
		$result[$x][$y] = true;		
	    } //for $x
	} //for $y		
	}
	
	
	return $result;
    }
    
    //function setupScore() {
    //}
	
//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
////////////

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in isaac.action.php)
    */

    /*

    Example:

    function playCard( $card_id )
    {
        // Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        self::checkAction( 'playCard' );

        $player_id = self::getActivePlayerId();

        // Add your game logic to play a card there
        ...

        // Notify all players about the card played
        self::notifyAllPlayers( "cardPlayed", clienttranslate( '${player_name} played ${card_name}', array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'card_name' => $card_name,
            'card_id' => $card_id
        ) );

    }

    */

    function playDisc( $x, $y, $pieceType, $orient )
    {
    //throw new feException( "pieceType $pieceType" );

        // Check that this player is active and that this action is possible at this moment
        self::checkAction( 'playDisc' );

        $player_id = self::getActivePlayerId();

	$possible_moves = self::getPossibleMoves($player_id);
	if ($orient == 'V') { $idxOrient = 1; }
	if ($orient == 'H') { $idxOrient = 2; }
	
	if( isset( $possible_moves[$idxOrient][$pieceType][$x][$y] ) ) { //validate move
		
            // Notify
            self::notifyAllPlayers( "playDisc", clienttranslate( '${player_name} plays a piece' ), array(
                'player_id' => $player_id,
                'player_name' => self::getActivePlayerName(),
                'x' => $x,
                'y' => $y,
                'pieceType' => $pieceType,
                'orient' => $orient		
            ) );

            // Then, go to the next state
            //$this->gamestate->nextState( 'playDisc' );
        }
        else
            throw new feException( "Impossible move" );
    }

    function endTurn($x,$y,$pieceType,$color,$orient) {
	
        // Check that this player is active and that this action is possible at this moment
        self::checkAction( 'endTurn' );

	$player_id = self::getActivePlayerId();
	
	$possible_moves = self::getPossibleMoves($player_id);
	if ($orient == 'V') { $idxOrient = 1; }
	if ($orient == 'H') { $idxOrient = 2; }	

	if( isset( $possible_moves[$idxOrient][$pieceType][$x][$y] ) ) { //validate move	
	
	// Notify	
	self::notifyAllPlayers( "endTurn", clienttranslate( '${player_name} plays a piece' ), array(
               'player_id' => $player_id,
               'player_name' => self::getActivePlayerName(),
	) );	
	

	$piece_info = $pieceType."_".$orient."_".$color;
	
	$sql = "SELECT MAX(piece_id) from board";
	
	//piece_id allows us to associate several squares as part of one piece
	$max_piece_id = self::getUniqueValueFromDB( $sql );
	$max_piece_id++;
	
	$sql = "UPDATE board SET piece_info='$piece_info',head=1, piece_id = $max_piece_id, piecetype = $pieceType
			WHERE board_x = $x and board_y = $y;";
	
	self::DbQuery( $sql );
	
	if ($orient == 'V') {
	for ($extra = $y+1; $extra <= $y+$pieceType+1; $extra++) {		
		$sql = "UPDATE board SET piece_info='$piece_info', piece_id = $max_piece_id
			WHERE board_x = $x and board_y = $extra;";
		self::DbQuery( $sql );				
	}
	}
	
	if ($orient == 'H') {
	for ($extra = $x+1; $extra <= $x+$pieceType+1; $extra++) {		
		$sql = "UPDATE board SET piece_info='$piece_info', piece_id = $max_piece_id
			WHERE board_x = $extra and board_y = $y;";
		self::DbQuery( $sql );				
	}
	}		
	


        // Then, go to the next state
	//$this->gamestate->nextState( 'playDisc' ); //default
	
	$movePiece = self::getMovePiece();
	//echo $movePiece[1];

	$countPiece = self::getCountPiece();

	//flag player good(canMove=1) if player has pieces in available moves	
	$canMove = array(0,0);
	
	for ($idxColor = 0; $idxColor <= 1; $idxColor++) {
	  for ($pieceType = 1; $pieceType <= 5; $pieceType++) {
	    if ( ($movePiece[$pieceType] == 1) && ($countPiece[$idxColor][$pieceType] > 0) ) { $canMove[$idxColor] = 1; }
	  }
	}
	//$canMove = array(0,1); //test - no white move
	
	$idxColor = 0; //default black
	//if ($color == 'W') { $idxColor = 1; }	
	if ($color == 'B') { $idxColor = 1; }	//FIX - corrected?
	
	//echo $idxColor.":".$canMove[$idxColor];
	
	if ($canMove[0] == 0 && $canMove[1] == 0) {
	  //$this->gamestate->nextState( 'nextRemoveTurn' ); //move to scoring stage if no move for either player
	  //$this->gamestate->nextState( 'endGame' ); //move to scoring stage if no move for either player		
	  $this->gamestate->nextState( 'nextTurnRemove' );	  
	}
	else if ($canMove[$idxColor] == 0) {		
	  $this->gamestate->nextState( 'nextTurn' ); //stay on same player if other player has no move  //FIX - don't think this works, need self-ref like remove cases
	}
	else {
	  $this->gamestate->nextState( 'playDisc' ); //regular next player move
	}
	
	}
        else 
            throw new feException( "Impossible move" );

    }
    
    /* problem with auto-cycle
    function passTurn()
    {
	$this->gamestate->nextState( 'playDisc' );
    }
    */

    function removePiece($x, $y) {
 
         // Check that this player is active and that this action is possible at this moment
        self::checkAction( 'removePiece' );
 
 	$player_id = self::getCurrentPlayerId();
	
	$sql = "SELECT player_color FROM player WHERE player_id = $player_id";
        //echo $sql;
        $player_color = self::getUniqueValueFromDB( $sql );
	$idxPlayer = 0; //default black
	if ($player_color == 'ffffff') {$idxPlayer = 1; }
 
	$possible_removes = self::getPossibleMovesRemove();
	
	//echo $idxPlayer.":".$x.":".$y.":";
	$move_found = false;
	foreach ($possible_removes[$idxPlayer] as $square) {
	    //echo count($possible_removes[$idxPlayer]).":".$square.":";
	    //if( isset( $square[$x][$y] ) ) { $move_found = true; echo $x.":".$y.":".$square[$x][$y].":";}
	    //print_r($square);
	    if( $square['x'] == $x and $square['y'] == $y ) { $move_found = true;} 		    
	}
	if ($move_found) {
	//if( isset( $possible_removes[$idxPlayer][$x][$y] ) ) { //validate move
 
	$sql = "SELECT piece_info FROM board WHERE board_x = $x and board_y = $y";
        //echo $sql;
        $piece_info = self::getUniqueValueFromDB( $sql );
 
	$piece_info_parts = explode("_", $piece_info);
	
	//get piece base score
	$base_pts = $piece_info_parts[0];
	if ($base_pts == 5) { $base_pts = 6;} //longest piece counts for an extra point
 
        //select id of piece
        $sql = "SELECT piece_id FROM board WHERE board_x = $x and board_y = $y";
        //echo $sql;
        $piece_id = self::getUniqueValueFromDB( $sql );
	
	$orient = $piece_info_parts[1];
	
	$cond_board = "board_x = $x";
	if ($orient == 'H') { $cond_board = "board_y = $y"; }
	
	//calc number of other pieces intersecting
	$sql = "SELECT count(distinct piece_id) FROM board WHERE $cond_board and piece_id <> 0"; //0 is default for piece_id so must exclude it
        //echo $sql;
        $pieces_intersect = self::getUniqueValueFromDB( $sql ) - 1; //get pieces in column or row minus the removed piece
	//echo $pieces_intersect;
	
	//calc number of score markers intersecting
	$markers_intersect = 0;
		
	//below section copied from getPossibleMovesRemove
	$sql = "SELECT player_score,player_color FROM player";
	$array_scores = self::getObjectListFromDB( $sql, $bUniqueValue=false );
	
	//foreach $scores - translate x,y - select piece_id, concat piece_id list
        foreach( $array_scores as $score) {		
	    //echo $score['player_score'].":".$score['player_color'].":";
	    if ($score['player_score'] != 0) {
		
		
		$x_score = $score['player_score'] % 10;
		$y_score = intval($score['player_score'] / 10);
		
		if ($x_score == 0) { 
		  $x_score = 10;
		  $y_score--;
		}

		//default black
		$calc_x = 11 - $x_score;
		$calc_y = $y_score + 1;
		
		if ($score['player_color'] == 'ffffff') {
		  $calc_x = $x_score;
		  $calc_y = 10 - $y_score;
		}
	    
		if ($orient == 'H' and $y == $calc_y) { $markers_intersect++; }
		if ($orient == 'V' and $x == $calc_x) { $markers_intersect++; }		

	    }
	    
        }
    
	$sql = "SELECT player_color FROM player WHERE player_id = $player_id";
	//echo $sql;
	$color = self::getUniqueValueFromDB( $sql );
	
	$sql = "SELECT player_score FROM player WHERE player_id = $player_id";
	//echo $sql;
	$score = self::getUniqueValueFromDB( $sql );
	

	//calc score
	//echo ":$base_pts:$pieces_intersect:$markers_intersect";
	$bonus = pow(2,$markers_intersect);
	$bonus_str = "";
	if ($bonus > 1) { $bonus_str = "x $bonus bonus"; }
	
	$remove_score = $base_pts * $pieces_intersect  * $bonus; 
	$score += $remove_score; 	
	
	//set possible score in score_aux for score marker choice
	$sql = "UPDATE player set player_score_potential = $score WHERE player_id = $player_id";
	//echo $sql;
	self::DbQuery( $sql );
	
    
	//clear squares with that id
        //$sql = "DELETE FROM board WHERE piece_id = $piece_id";
        $sql = "UPDATE board set removed = 1 WHERE piece_id = $piece_id";	
        //echo $sql;
        self::DbQuery( $sql );
 
 	//remove pieces in ascending order
        $sql = "UPDATE player set player_min_remove_piecetype = $piece_info_parts[0] where player_id = $player_id";
        //echo $sql;
        self::DbQuery( $sql );
 
	// Notify
	self::notifyAllPlayers( "removePiece", clienttranslate( '${player_name} removes a ${base_pts} point(s) piece and may get ${base_pts} x ${pieces_intersect} intersects ${bonus_str} = ${remove_score} points' ), array(
	    'player_id' => $player_id,
	    'player_name' => self::getActivePlayerName(),
	    'x' => $x,
	    'y' => $y,
	    'base_pts' => $base_pts,
	    'pieces_intersect' => $pieces_intersect,
	    'bonus_str' => $bonus_str,
	    'remove_score' => $remove_score
	) );
 
	//echo "$remove_score";

	//set player score to max if >= 100 and end game
	if ($score >= 100) {
	    //set possible score in score_aux for score marker choice
	    $sql = "UPDATE player set player_score = $score WHERE player_id = $player_id";
	    //echo $sql;
	    self::DbQuery( $sql );
	    
 	    $this->gamestate->nextState( 'endGame' );
	}	
	else if ($remove_score != 0) { 
 	    $this->gamestate->nextState( 'scoreMarker' );
	}
	else {	
	
		$result = self::getPossibleMovesRemove();
		//echo count($result[1]).":".count($result[0]);
		
		//no remove pieces left for either player - endgame
		if ((count($result[0]) == 0) and (count($result[1]) == 0)) { $this->gamestate->nextState( 'endGame' ); } 
		
		
		//player checks for no removes left
		else if ($color == 'ffffff' and count($result[0]) == 0) { $this->gamestate->nextState( 'removePiece' ); }  //FIX? - working? - add color match condition, final score condition
		else if ($color == '000000' and count($result[1]) == 0) { $this->gamestate->nextState( 'removePiece' ); }
		
		//skip scoring if no score
		else { 
		    $this->gamestate->nextState( 'pass' );
		}
	}
	
	}
	else
	    throw new feException( "Impossible move" );
    }    
    
    function scoreMarker($x, $y, $player_id) {
 
        // Check that this player is active and that this action is possible at this moment
        self::checkAction( 'scoreMarker' );

	$possible_scores = self::getPossibleMovesScore();
	
	if( isset( $possible_scores[$x][$y] ) ) { //validate move	
	
	$sql = "SELECT player_color FROM player WHERE player_id = $player_id";
	//echo $sql;
	$color = self::getUniqueValueFromDB( $sql );	

	//different scoring positions since players running opposite directions
	//black player default
	$score = (($y-1)*10) + (10-$x+1);
	
	//white player	
	if ($color == 'ffffff') {
	    $score = ((10-$y)*10) + $x;
	}
	
	$sql = "SELECT player_score from player WHERE player_id = $player_id";
	//echo $sql;
	$old_score = self::getUniqueValueFromDB( $sql );
	$points_scored = $score - $old_score;
	
	$sql = "UPDATE player set player_score = $score WHERE player_id = $player_id";
	//echo $sql;
	self::DbQuery( $sql );

	// Notify
	self::notifyAllPlayers( "scoreMarker", clienttranslate( '${player_name} scores ${points_scored} point(s)' ), array(
	    'player_id' => $player_id,
	    'player_name' => self::getActivePlayerName(),
	    'x' => $x,
	    'y' => $y,
	    'points_scored' => $points_scored
	) );
	
	$newScores = self::getCollectionFromDb( "SELECT player_id, player_score FROM player", true );
	self::notifyAllPlayers( "newScores", "", array(
            "scores" => $newScores
        ) );    
 
  
 	$result = self::getPossibleMovesRemove();
	//echo count($result[0]).":".$result[1];
	
	//score limit reach - endgame
    	if ($score >= 100) { //FIX - test value - final >= 100
	    $this->gamestate->nextState( 'endGame' ); }
	else {
		
		$result = self::getPossibleMovesRemove();
		//echo count($result[1]).":".count($result[0]);
		
		//no remove pieces left for either player - endgame
		if ((count($result[0]) == 0) and (count($result[1]) == 0)) { $this->gamestate->nextState( 'endGame' ); } 
		
		
		//player checks for no removes left
		else if ($color == 'ffffff' and count($result[0]) == 0) { $this->gamestate->nextState( 'removePiece' ); }  //FIX? - working? - add color match condition, final score condition
		else if ($color == '000000' and count($result[1]) == 0) { $this->gamestate->nextState( 'removePiece' ); }
		
		else { 
		    $this->gamestate->nextState( 'pass' );
	        }
	}
	
	}
	else
	    throw new feException( "Impossible move" );	
	
	
    }
    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    /*

    Example for game state "MyGameState":

    function argMyGameState()
    {
        // Get some values from the current game situation in database...

        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }
    */

//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */

    /*

    Example for game state "MyGameState":

    function stMyGameState()
    {
        // Do some stuff ...

        // (very often) go to another gamestate
        $this->gamestate->nextState( 'some_gamestate_transition' );
    }
    */

    function argPlayerTurn()
    {
        return array(
            'possibleMoves' => self::getPossibleMoves( self::getActivePlayerId() ),
	    'movePiece' => self::getMovePiece()
        );
    }

    function argPlayerTurnRemove()
    {
	//echo "argPlayerTurnRemove";
        return array(
            'possibleMovesRemove' => self::getPossibleMovesRemove()
        );
    }    

    function argPlayerScoreMarker()
    {
	//echo "debug1";
        return array(
            'possibleMovesScore' => self::getPossibleMovesScore()
        );
    }      
    
    function stNextPlayer()
    {
        // Activate next player
        $player_id = self::activeNextPlayer();

        self::giveExtraTime( $player_id );
        $this->gamestate->nextState( 'nextTurn' );
	//$this->gamestate->nextState( 'sameTurn' );

    }

    function stNextPlayerRemove()
    {
        // Activate next player
        $player_id = self::activeNextPlayer();

        self::giveExtraTime( $player_id );
        $this->gamestate->nextState( 'nextTurnRemove' );
	//$this->gamestate->nextState( 'sameTurn' );

    }

    function actionSetupScore()
    {
	self::setGameStateInitialValue( 'game_state', 1 );
	
	$countPiece = self::getCountPiece();
	
	//tiebreaker is sum length of unused pieces
	$black_tie_score = $countPiece[0][1] + $countPiece[0][2]*2 + $countPiece[0][3]*3 + $countPiece[0][4]*4 + $countPiece[0][5]*5;
	$white_tie_score = $countPiece[1][1] + $countPiece[1][2]*2 + $countPiece[0][3]*3 + $countPiece[0][4]*4 + $countPiece[0][5]*5; 
	
	$sql = "UPDATE player set player_score_aux = $black_tie_score where player_color = '000000'";
	self::DbQuery( $sql );
	$sql = "UPDATE player set player_score_aux = $white_tie_score where player_color = 'ffffff'";
	self::DbQuery( $sql );	
	
	//set both players to 1 point scoring position
		
	$sql = "UPDATE player set player_score = 1";
	self::DbQuery( $sql );
	
	$sql = "SELECT player_id,player_name,player_color FROM player";
	$array_player_info = self::getObjectListFromDB( $sql, $bUniqueValue=false );
	
        foreach( $array_player_info as $player_info) {			
	
	//default black
	$x = 10;
	$y = 1;
	
	//white
	if ($player_info['player_color'] == 'ffffff') { $x = 1; $y = 10; }
	
	// Notify
	self::notifyAllPlayers( "scoreMarker", clienttranslate( '${player_name} scores' ), array(
	    'player_id' => $player_info['player_id'],
	    'player_name' => $player_info['player_name'],
	    'x' => $x,
	    'y' => $y	
	) );
	}
	
	$newScores = self::getCollectionFromDb( "SELECT player_id, player_score FROM player", true );
	self::notifyAllPlayers( "newScores", "", array(
            "scores" => $newScores
        ) );    	
	
        $this->gamestate->nextState( 'nextTurnRemove' );
	//$this->gamestate->nextState( 'sameTurn' );

    }
    
    /*
    function stSamePlayer()
    {
        // Activate next player
        //$player_id = self::activeNextPlayer();

        self::giveExtraTime( $player_id );
        //test $this->gamestate->nextState( 'nextTurn' );
	$this->gamestate->nextState( 'sameTurn' );

    }
    */

//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:

        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
    */

    function zombieTurn( $state, $active_player )
    {
    	$statename = $state['name'];

        if (substr($statename, 0, 6) == "player") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState( "zombiePass" );
                break;
            }

            return;
        }

        if (substr($statename, 0, 11) == "multiplayer") {
            // Make sure player is in a non blocking status for role turn
            $sql = "
                UPDATE  player
                SET     player_is_multiactive = 0
                WHERE   player_id = $active_player
            ";
            self::DbQuery( $sql );

            $this->gamestate->updateMultiactiveOrNextState( '' );
            return;
        }

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }
}
