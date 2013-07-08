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
 * isaac.action.php
 *
 * Isaac main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/isaac/isaac/myAction.html", ...)
 *
 */


  class action_isaac extends APP_GameAction
  {
    // Constructor: please do not modify
   	public function __default()
  	{
  	    if( self::isArg( 'notifwindow') )
  	    {
            $this->view = "common_notifwindow";
  	        $this->viewArgs['table'] = self::getArg( "table", AT_posint, true );
  	    }
  	    else
  	    {
            $this->view = "isaac_isaac";
            self::trace( "Complete reinitialization of board game" );
      }
  	}

  	// TODO: defines your action entry points there


    /*

    Example:

    public function myAction()
    {
        self::setAjaxMode();

        // Retrieve arguments
        // Note: these arguments correspond to what has been sent through the javascript "ajaxcall" method
        $arg1 = self::getArg( "myArgument1", AT_posint, true );
        $arg2 = self::getArg( "myArgument2", AT_posint, true );

        // Then, call the appropriate method in your game logic, like "playCard" or "myAction"
        $this->game->myAction( $arg1, $arg2 );

        self::ajaxResponse( );
    }

    */

    public function playDisc() {
	self::setAjaxMode();

	$x = self::getArg( "x", AT_posint, true );
	$y = self::getArg( "y", AT_posint, true );
	$pieceType = self::getArg( "pieceType", AT_posint, true );
	$orient = self::getArg( "orient", AT_alphanum, true );
	
	$result = $this->game->playDisc( $x, $y, $pieceType, $orient );
	self::ajaxResponse( );
    }

    public function endTurn() {
	self::setAjaxMode();

	$x = self::getArg( "x", AT_posint, true );
	$y = self::getArg( "y", AT_posint, true );
	$pieceType = self::getArg( "pieceType", AT_posint, true );
	$color = self::getArg( "color", AT_alphanum, true );
	$orient = self::getArg( "orient", AT_alphanum, true );

	$result = $this->game->endTurn($x, $y, $pieceType, $color, $orient);
	self::ajaxResponse( );
    }

    public function removePiece() {
	self::setAjaxMode();

	$x = self::getArg( "x", AT_posint, true );
	$y = self::getArg( "y", AT_posint, true );
	
	$result = $this->game->removePiece( $x, $y );
	self::ajaxResponse( );
    }    

    public function scoreMarker() {
	self::setAjaxMode();

	$x = self::getArg( "x", AT_posint, true );
	$y = self::getArg( "y", AT_posint, true );
	$player_id = self::getArg( "player_id", AT_posint, true );
	
	$result = $this->game->scoreMarker( $x, $y, $player_id );
	self::ajaxResponse( );
    }    
    
    /* tried, but auto-cycle
    public function passTurn()
	    {
	        self::setAjaxMode();

	        $result = $this->game->passTurn();
	        self::ajaxResponse( );
    }
    */
    
  }


