/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
        */
        
        
 /* Isaac implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * isaac.js
 *
 * Isaac user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */



define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter"
],



function (dojo, declare) {
    return declare("bgagame.isaac", ebg.core.gamegui, {
        constructor: function(){
            console.log('isaac constructor');
              
            // Here, you can init the global variables of your user interface
            // Example:
            // this.myGlobalValue = 0;


            this.globPieceType = null;
            this.globOrient = 'V'; //FIX? - store last orientation?	    
            this.globLastTokenId = null;
            this.globX = null;
            this.globY = null;
	    this.globEnterArgs = null;
	    
	    this.globCountPiece = []; 
	    this.globMovePiece = [];
	    this.globIdxPlayer = null; //0 = black, 1 = white
	    this.globPlayerColor = null;
	    //this.globConnections = [];
	    //this.globConnections = new Array();
	    this.globGameState = null; //FIX - temp till figure out disconnect issue
        },
        
       
        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
	*/
	
        setup: function( gamedatas )
        {
            console.log( "Starting game setup" );
	    //alert("gamedatas");
	    
	    //rotate board for black player perspective
	    //FIX - change view.php for black player instead
	    /*
	    if (gamedatas.players[this.player_id].color == '000000') {
		
	    var transform;
	    dojo.forEach(
		['transform', 'WebkitTransform', 'msTransform',
		 'MozTransform', 'OTransform'],
		function (name) {
		    if (typeof dojo.body().style[name] != 'undefined') {
			transform = name;
		    }
		});
    
	    var node = dojo.byId("board");
    
	    var animation = new dojo.Animation({
		curve: [0, 180],
		onAnimate: function (v) {
		    node.style[transform] = 'rotate(' + v + 'deg)';
		}
	    }).play();
	    }
	    */
	    
	    for( var i in gamedatas.board )
            {
                var square = gamedatas.board[i];
		
                if( square.head == 1 )
                {
		    //alert(square.x+":"+square.y+":"+this.player_id);
		    var piece_args = square.piece_info.split('_'); 
                    this.addTokenOnBoard( square.x, square.y, this.player_id, piece_args[0], piece_args[2], piece_args[1] );
                }
            }
	    
   
            //alert("debug0:"+this.player_id)

            
            // Setting up player boards
            //for( var player_id in gamedatas.players )
            //{
                //var player = gamedatas.players[player_id];
                         
                // TODO: Setting up players boards if needed
                //this.addTokenOnBoard( 2, 2, player_id, 1 );
                //alert("debug1:"+this.player_id)
                //alert("debug1a:"+this.gamedatas.players[ player_id ].color)
                
                /*
                	    dojo.place( this.format_block( 'jstpl_piece', {
				x_y: '2_2',
				color: this.gamedatas.players[ player_id ].color,
				piecetype: 1
			    } ) , 'pieces' );
		
			    //this.placeOnObject( 'piece_2_2', 'overall_player_board_'+player );
	    		    this.slideToObject( 'piece_2_2', 'square_piece_2_2' ).play();
                */
                //this.addPiece( 1, 2, player_id, 1 );
                //this.addPiece( 2, 3, player_id, 2 );
                //this.addPiece( 3, 3, player_id, 3 );
                //this.addPiece( 4, 4, player_id, 4 );
                //this.addPiece( 5, 4, player_id, 5 );                
            //}
            
            // TODO: Set up your game interface here, according to "gamedatas"
            //var active_player_id = this.getActivePlayerId()
            //alert("debug2:"+this.player_id)
            //this.addTokenOnBoard( 2, 2, this.player_id, 1 ); //testing

	    //this.globPlayerColor = this.gamedatas.players[this.player_id].color;
	    
	    //default black, unless white
	    //FIX - add this.globPlayerColor and set onEnter
	    this.globPlayerColor = gamedatas.players[this.player_id].color;	    
	    var playerColor = gamedatas.players[this.player_id].color;  //FIX - how getting color here? not defined in getAllDatas
	    idxPlayer = 0; //black
	    if (playerColor == 'ffffff') { idxPlayer = 1; } //white
	    this.globIdxPlayer = idxPlayer;
	    
	    for ( var pieceType = 1; pieceType <= 5; pieceType++ ) {
		
	    var initOffset = 3;
	    if (pieceType == 2) { initOffset = 4; }
	    if (pieceType == 3) { initOffset = 4; }
	    if (pieceType == 4) { initOffset = 5; }
	    if (pieceType == 5) { initOffset = 5; }	    
		
	    this.globCountPiece[pieceType] = gamedatas.countPiece[idxPlayer][pieceType];
	    //this.globMovePiece[pieceType] = gamedatas.movePiece[pieceType];
	    dojo.byId("square_piece_"+pieceType+"_1").innerHTML = dojo.replace("x"+this.globCountPiece[pieceType]);
	    
	    if (this.globCountPiece[pieceType] > 0) {
		this.addPiece( pieceType, initOffset, this.player_id, pieceType );
		//alert("movePiece:"+pieceType+":"+this.globMovePiece[pieceType]);
		//if (this.globMovePiece[pieceType] == 0) { dojo.addClass( 'piecetype_'+pieceType, 'noMove' ); }
	    }
	    
	    } //for pieceType
	    
	    
	    //this.addPiece( 2, 4, this.player_id, 2 );
	    //this.addPiece( 3, 4, this.player_id, 3 );
	    //this.addPiece( 4, 5, this.player_id, 4 );
	    //this.addPiece( 5, 5, this.player_id, 5 );
	    
	    dojo.byId("square_piece_1_8").innerHTML = dojo.replace(this.globOrient);
	    
                 
 	    dojo.query( '.square' ).connect( 'onmouseover', this, 'onHoverPlace' );
	    dojo.query( '.square' ).connect( 'onclick', this, 'onPlayDisc' );

	    dojo.query( '.token' ).connect( 'onclick', this, 'removeToken' );
	    
	    /*
	    var nodeList = dojo.query( '.piece' );
	    
	    nodes = Array.prototype.slice.call(nodeList,0); 

		// nodes is an array now.
		nodes.forEach(function(node){ 
	      
		     // do your stuff here.
		     dojo.connect(node, 'onclick', this, 'changePiece' );
	      
		});
	    */
	    
	    //connections.forEach(dojo.connect( 'onclick', this, 'changePiece' ));
	    
 	    dojo.query( '.piece' ).connect( 'onclick', this, 'changePiece' );
	    
	    //this.globConnections = dojo.query('.piece').map( function( zenode ) { return dojo.connect( zenode, "onclick", this, 'changePiece'); });
	    
	    //dojo.forEach(globConnections, function(conn) { dojo.disconnect(conn); });
	    
	    //dojo.query( '.piece' ).connect( 'onclick', this, 'doNothing' );
	    

	    dojo.query( '#square_piece_1_8' ).connect( 'onclick', this, 'changeOrient' );
	    
	    //var node = dojo.byId("#square_piece_1_8");
	   //  dojo.query('.piece').forEach(function(node){
	//	dojo.disconnect(node.innerHTML);
	  //  });
	    
	    //dojo.forEach(connections, dojo.disconnect);
	    
	   // var handle = dojo.connect( node, 'onclick', this, 'changePiece' );
	   
 		//dojo.disconnect(handle);
 
 
	    //dojo.query( '.token' ).connect( 'onclick', this, 'onToken' );  
 
 	    //if (this.globGameState != "Place") {
		
	    for (var i in gamedatas.players) {
	    //if (i == this.player_id) {
	    
	    var player_score = gamedatas.players[i].score
	    //var player_id = gamedatas.players[i].id
	    //player_score = 14; //test
	    
	    if (player_score != 0) {	
	    
	    //player_score++; //FIX?
	    
	    //default white
	    var x = player_score % 10;
	    var y = 10 - Math.floor(player_score / 10);

	    if (x == 0) {
	      x = 10;
	      y--;
	    }
	   
	   //if black
	    if (gamedatas.players[i].color == '000000') {
	        x = 11 - x;
		y = Math.floor(player_score / 10) + 1;
	    }
	   
	    //x = 5; y = 6; //test
	    //alert(gamedatas.players[i].id);
	    //alert(player_score+":"+gamedatas.players[i].color+":"+x+":"+y);
	   
	    this.moveScoreMarker(x,y,gamedatas.players[i].id);
	    /*
	    dojo.place( this.format_block( 'jstpl_score', {
		color: gamedatas.players[i].color,
		player_id: gamedatas.players[i].id
	    } ) , 'scores' );
	    
	    this.placeOnObject( 'score_marker_'+gamedatas.players[i].id, 'overall_player_board_'+this.player_id );
	    this.slideToObject( 'score_marker_'+gamedatas.players[i].id, 'square_'+x+'_'+y ).play();
	    */
	    
	    } //if player_score != 0
	    //} //if this.player_id
	    } //for players
	    //} // if globGameState != "Place" 	    
	    // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

	    //alert("Leaving setup");
            console.log( "Ending game setup" );
        },
       

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
 
            console.log( 'Entering state: '+stateName );
            
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Show some HTML block at this game state
                dojo.style( 'my_html_block_id', 'display', 'block' );
                
                break;
           */
           
            case 'playerTurn':
		//alert("playerTurn");
		/* tried this but kept getting into auto-cycling
		if (this.globPlayerColor == 'ffffff') {
		    this.ajaxcall( "/isaac/isaac/passTurn.html", {
		    }, this, function( result ) {} );
		}
		*/

 	        if (this.getActivePlayerId() != this.player_id) {
	            return;
	        }		
		
		this.checkDisplayPiece();
                this.updatePossibleMoves( args.args.possibleMoves );
		this.updateMovePiece( args.args.movePiece );
		this.globEnterArgs = args;
		this.globGameState = "Place";		

                break;

            case 'setupScore':
		//alert("setupScore");
		//if (dojo.exists("token_test")) {
		//    dojo.destroy("token_test");
		//}
		
		dojo.query( '.token' ).connect( 'onclick', this, 'removeToken' );
		
                break;		
		
            case 'playerTurnRemove':
 	        if (this.getActivePlayerId() != this.player_id) {
	            return;
	        }		
		
		//alert("playerTurnRemove - Enter");
		//this.checkDisplayPiece();
                this.updatePossibleMovesRemove( args.args.possibleMovesRemove );
		//this.updateMovePiece( args.args.movePiece );
		this.globEnterArgs = args;
		
	        this.globGameState = "Remove";
				
		//dojo.query( '.piece' ).disconnect;
		
		//if (dojo.exists("token_test")) {
		//    dojo.destroy("token_test");
		//}
		
                break;

            case 'playerScoreMarker':
 	        if (this.getActivePlayerId() != this.player_id) {
	            return;
	        }		
	    
		//alert("playerScoreMarker");
                this.updatePossibleMovesScore( args.args.possibleMovesScore ); 
		this.globEnterArgs = args;
		
		this.globGameState = "Score";
				
		//dojo.query( '.piece' ).disconnect;
		
                break;		
	    
            case 'dummy':
                break;
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {
 	    if (this.getActivePlayerId() != this.player_id) {
	        return;
	    }	    
	    
            console.log( 'Leaving state: '+stateName );
            
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Hide the HTML block we are displaying only during this game state
                dojo.style( 'my_html_block_id', 'display', 'none' );
                
                break;
           */
	    
            case 'playerTurn':
		//alert("leaving playerTurn"); 
		this.turnClear();
		
           	if (dojo.exists("token_test")) {
		    dojo.destroy("token_test");
		}
		
		break;

            case 'playerTurnRemove':
		//alert("playerTurnRemove - Leave");		
		this.turnClear();
		
                break;

            case 'playerScoreMarker':
		this.turnClear();
		
		break;
		
            case 'dummy':
                break;
            }               
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args )
        {
            console.log( 'onUpdateActionButtons: '+stateName );
                      
            if( this.isCurrentPlayerActive() )
            {            
                switch( stateName )
                {
/*               
                 Example:
 
                 case 'myGameState':
                    
                    // Add 3 action buttons in the action status bar:
                    
                    this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' ); 
                    this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' ); 
                    this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' ); 
                    break;
*/


                 case 'playerTurn':
                 
		    if (dojo.exists("token_test")) {
		        dojo.destroy("token_test");
		    }
		 
                    this.globLastTokenId = null;  //FIX? move to onEnteringState?
                    
                    // Add action buttons in the action status bar:
                    
                    //this.addActionButton( 'action_endTurn', _('End my turn'), 'endTurn' ); 

                    break;


                }
            }
        },        

        ///////////////////////////////////////////////////
        //// Utility methods
        
        /*
        
            Here, you can defines some utility methods that you can use everywhere in your javascript
            script.
        
        */

	addTokenOnBoard: function( x, y, player, piecetype, color, orient )
	{
	
	    //this.globPieceType = piecetype; //other player notify set
	    //this.globX = x;
	    //this.globY = y;
	    
	    piece_offset = 0;

	    if (piecetype == 1) { piece_offset = 1; }
	    if (piecetype == 2) { piece_offset = 2; if (orient == 'H') { piece_offset = 1; } }
	    if (piecetype == 3) { piece_offset = 2; }
	    if (piecetype == 4) { piece_offset = 3; if (orient == 'H') { piece_offset = 2; } }
	    if (piecetype == 5) { piece_offset = 3; }
	    
	    x_offset = x;
	    y_offset = y;
	    if (orient == 'H') {
		x_offset = parseInt(x)+piece_offset;
	    }
	    else { //orient == 'V'
		y_offset = parseInt(y)+piece_offset;
	    }
	
	    //take the player color(default) or the set color
	    var token_color = this.gamedatas.players[ player ].color;
	    if (color != null) {
		token_color = '000000';
		if (color == "W") {
		    token_color = 'ffffff';
		}
	    }
	    
	    //if (color == null && this.globLastTokenId != null) {
	    //  dojo.destroy(this.globLastTokenId);
	    //}
	   
	    dojo.place( this.format_block( 'jstpl_token', {
		x_y: x+'_'+y,
		color: token_color,
		piecetype: piecetype
	    } ) , 'tokens' );

	    //alert("debug3:"+x+":"+y+":"+player+":"+x_offset.toString()+":"+y_offset.toString()+":"+orient);
	    var tokenId = "token_"+x+"_"+y;
	    //this.globLastTokenId = tokenId;
	    this.placeOnObject( tokenId, 'overall_player_board_'+player );
	    
	    //this.slideToObject( tokenId, 'square_'+x_offset.toString()+'_'+y_offset.toString() ).play();
	    this.placeOnObject( tokenId, 'square_'+x_offset.toString()+'_'+y_offset.toString() );
	    //alert("debug4");
	    
	    if (orient == 'H') {
    
		// The following code to determine the transform style property name
		// is adapted from:
		// http://www.zachstronaut.com/posts/2009/02/17/animate-css-transforms-firefox-webkit.html
		var transform;
		dojo.forEach(
		    ['transform', 'WebkitTransform', 'msTransform',
		     'MozTransform', 'OTransform'],
		    function (name) {
			if (typeof dojo.body().style[name] != 'undefined') {
			    transform = name;
			}
		    });
	
		var node = dojo.byId("token_"+x+"_"+y);
	
		var animation = new dojo.Animation({
		    curve: [0, 90],
		    onAnimate: function (v) {
			node.style[transform] = 'rotate(' + v + 'deg)';
		    }
		}).play();
		
		//dojo.connect(node, 'onclick', animation, 'play');
	    } //if (orient == 'H')
	    
	    /*
	    new ContentPane({
		content:"<p>x4</p>",
		style:"height:125px"
	     }, "square_piece_1_1");
	    
	    dojo.byId("output").innerHTML = dojo.replace(
	    "Hello, {0} {2} AKA {3}!",
	    ["Robert", "X", "Cringely", "Bob"]
	    );
	    */
	    //dojo.byId("square_piece_1_1").innerHTML = dojo.replace("x4");
	    
        },
        
	addPiece: function( x, y, player, piecetype )
	{
	    dojo.place( this.format_block( 'jstpl_piece', {
		color: this.gamedatas.players[ player ].color,
		piecetype: piecetype
	    } ) , 'pieces' );

	    this.placeOnObject( 'piecetype_'+piecetype, 'overall_player_board_'+player );
	    this.slideToObject( 'piecetype_'+piecetype, 'square_piece_'+x+'_'+y ).play();
        },        

        ///////////////////////////////////////////////////
        //// Player's action
        
        /*
        
            Here, you are defining methods to handle player's action (ex: results of mouse click on 
            game objects).
            
            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server
        
        */
        
        /* Example:
        
        onMyMethodToCall1: function( evt )
        {
            console.log( 'onMyMethodToCall1' );
            
            // Preventing default browser reaction
            dojo.stopEvent( evt );

            // Check that this action is possible (see "possibleactions" in states.inc.php)
            if( ! this.checkAction( 'myAction' ) )
            {   return; }

            this.ajaxcall( "/isaac/isaac/myAction.html", { 
                                                                    lock: true, 
                                                                    myArgument1: arg1, 
                                                                    myArgument2: arg2,
                                                                    ...
                                                                 }, 
                         this, function( result ) {
                            
                            // What to do after the server call if it succeeded
                            // (most of the time: nothing)
                            
                         }, function( is_error) {

                            // What to do after the server call in anyway (success or failure)
                            // (most of the time: nothing)

                         } );        
        },        
        
        */

	onPlayDisc: function( evt )
        {
            // Stop this event propagation
            dojo.stopEvent( evt );

	    //alert("gameState:"+this.globGameState);
	    if (this.globGameState == "Remove") { this.removePieceSquare(evt); return; } //FIX
	    if (this.globGameState == "Score") { this.scoreMarker(evt); return; } //FIX
	    
            // Get the cliqued square x and y
            // Note: square id format is "square_X_Y"
            var coords = evt.currentTarget.id.split('_');
            var x = coords[1];
            var y = coords[2];

            if( ! dojo.hasClass( 'square_'+x+'_'+y, 'possibleMove' ) )
            {
                // This is not a possible move => the click does nothing
                return ;
            }
            //alert ("debug2");

            if( this.checkAction( 'playDisc' ) )    // Check that this action is possible at this moment
            {
		//alert(x+":"+y+":"+this.globPieceType+":"+this.globOrient)
                this.ajaxcall( "/isaac/isaac/playDisc.html", {
                    x:x,
                    y:y,
                    pieceType:this.globPieceType,
		    orient:this.globOrient
                }, this, function( result ) {} );
		
	        this.endTurn();		
            }            
            

        },

	onHoverPlace: function( evt )
        {
            // Stop this event propagation
            dojo.stopEvent( evt );

	    //alert("gameState:"+this.globGameState);
	    if (this.globGameState != "Place") { return; } //FIX
	    
            // Get the cliqued square x and y
            // Note: square id format is "square_X_Y"
            var coords = evt.currentTarget.id.split('_');
            var x = coords[1];
            var y = coords[2];

            if( ! dojo.hasClass( 'square_'+x+'_'+y, 'possibleMove' ) )
            {
                // This is not a possible move => the click does nothing
                return ;
            }
            //alert ("debug2");

	    //this.globPieceType = piecetype; //other player notify set
	    piecetype = this.globPieceType;
	    orient = this.globOrient;
	    color = this.globPlayerColor;
	    player = this.player_id;
	    this.globX = x;
	    this.globY = y;
	    
	    piece_offset = 0;

	    if (piecetype == 1) { piece_offset = 1; }
	    if (piecetype == 2) { piece_offset = 2; if (orient == 'H') { piece_offset = 1; } }
	    if (piecetype == 3) { piece_offset = 2; }
	    if (piecetype == 4) { piece_offset = 3; if (orient == 'H') { piece_offset = 2; } }
	    if (piecetype == 5) { piece_offset = 3; }
	    
	    x_offset = x;
	    y_offset = y;
	    if (orient == 'H') {
		x_offset = parseInt(x)+piece_offset;
	    }
	    else { //orient == 'V'
		y_offset = parseInt(y)+piece_offset;
	    }
	
	    //take the player color(default) or the set color
	    /*
	    var token_color = this.gamedatas.players[ player ].color;
	    if (color != null) {
		token_color = '000000';
		if (color == "W") {
		    token_color = 'ffffff';
		}
	    }
	    */
	    
	    //if (color == null && this.globLastTokenId != null) {
	    //  dojo.destroy(this.globLastTokenId);
	    //}

	    var tokenId = 'token_test';
	    this.globLastTokenId = tokenId;
	   
	    //return;
	
	    if (!(dojo.exists(tokenId))) {
	    
	    dojo.place( this.format_block( 'jstpl_token_test', {
		color: color,
		piecetype: piecetype
	    } ) , 'tokens' );

	    //alert("debug3:"+x+":"+y+":"+player+":"+x_offset.toString()+":"+y_offset.toString()+":"+orient);
	    //alert("debug3:"+x+":"+y+":"+player+":"+x_offset+":"+y_offset+":"+orient);

	    this.placeOnObject( tokenId, 'overall_player_board_'+player );
	    //this.placeOnObject( tokenId, 'board_actual' );
	    
	    if (orient == 'H') {
    
		// The following code to determine the transform style property name
		// is adapted from:
		// http://www.zachstronaut.com/posts/2009/02/17/animate-css-transforms-firefox-webkit.html
		var transform;
		dojo.forEach(
		    ['transform', 'WebkitTransform', 'msTransform',
		     'MozTransform', 'OTransform'],
		    function (name) {
			if (typeof dojo.body().style[name] != 'undefined') {
			    transform = name;
			}
		    });
	
		var node = dojo.byId('token_test');
	
		var animation = new dojo.Animation({
		    curve: [0, 90],
		    onAnimate: function (v) {
			node.style[transform] = 'rotate(' + v + 'deg)';
		    }
		}).play();
		
		//dojo.connect(node, 'onclick', animation, 'play');
	    } //if (orient == 'H')
	    
	    }
	    
	    this.slideToObject( tokenId, 'square_'+x_offset.toString()+'_'+y_offset.toString() ).play();
	    //this.slideToObject( tokenId, 'square_'+x_offset+'_'+y_offset ).play();	    
	    //alert("debug4");
    
        },

	doNothing: function( evt )
        {
	  //do nothing  
	},

	removeToken: function( evt )
        {
            // Stop this event propagation
            dojo.stopEvent( evt );

            //var coords = evt.currentTarget.id.split('_');
            //var x = coords[1];
            //var y = coords[2];	    
	    
	    //alert("gameState:"+this.globGameState);
	    if (this.globGameState != "Remove") { return; } //FIX
	    
            var coords = evt.currentTarget.id.split('_');
            var x = coords[1];
            var y = coords[2];
	    
	    this.removePiece(x,y);	    
	},
	
	removePieceSquare: function( evt )
        {
	    // Get the cliqued square x and y
            // Note: square id format is "square_X_Y"
            var coords = evt.currentTarget.id.split('_');
            var x = coords[1];
            var y = coords[2];
	    
	    this.removePiece(x,y);
	},
	
	removePiece: function( x,y )
        {	    
	    
            if( ! dojo.hasClass( 'square_'+x+'_'+y, 'possibleMove' ) )
            {
                // This is not a possible move => the click does nothing
                return ;
            }
            
            if( this.checkAction( 'removePiece' ) )    // Check that this action is possible at this moment
            {            

                this.ajaxcall( "/isaac/isaac/removePiece.html", {
                    x:x,
                    y:y
                }, this, function( result ) {} );
		
 	        this.destroyToken(x,y); 	
            }
            
	},
	
	destroyToken: function( x, y )
        {
	    /*
	    var anim = dojo.fx.chain( [
		dojo.fadeOut( { node: 'token_'+x+'_'+y, 
				    onEnd: function( node ) {
					dojo.destroy(node);
                                    }
		} )
		
	    ] ); // end of dojo.fx.chain

	    anim.play();
	    */
	    
	    //dojo.destroy('token_'+x+'_'+y);
	    if (dojo.exists('token_'+x+'_'+y)) {
	        this.fadeOutAndDestroy('token_'+x+'_'+y);
	    }
	},

	moveScoreMarker: function( x, y, player )
        {
	    if (!(dojo.exists("score_marker_"+player))) {
	    //alert("scoreMarker");
		
	    dojo.place( this.format_block( 'jstpl_score', {
		color: this.gamedatas.players[ player ].color,
		player_id: player
	    } ) , 'scores' );
	    
	    //alert("debug1");
	    this.placeOnObject( 'score_marker_'+player, 'overall_player_board_'+this.player_id );
	    }
	    
	    //alert("debug2:"+player+":"+x+":"+y);	    	    
	    this.slideToObject( 'score_marker_'+player, 'square_'+x+'_'+y ).play();
	    //alert("debug3");
	},
	
	scoreMarker: function( evt )
        {
	    // Get the cliqued square x and y
            // Note: square id format is "square_X_Y"
            var coords = evt.currentTarget.id.split('_');
            var x = coords[1];
            var y = coords[2];

            if( ! dojo.hasClass( 'square_'+x+'_'+y, 'possibleScore' ) )
            {
                // This is not a possible move => the click does nothing
                return ;
            }
            
            if( this.checkAction( 'scoreMarker' ) )    // Check that this action is possible at this moment
            {            
	        this.moveScoreMarker(x,y,this.player_id);

                this.ajaxcall( "/isaac/isaac/scoreMarker.html", {
                    x:x,
                    y:y,
		    player_id: this.player_id
                }, this, function( result ) {} );
            }
            
	},
	
	changePiece: function( evt )
        {
            // Stop this event propagation
            dojo.stopEvent( evt );

	    if (this.globGameState != "Place") { return; } //FIX	    
	    
            // Get the cliqued square x and y
            // Note: square id format is "square_X_Y"
            var coords = evt.currentTarget.id.split('_');
            var pieceType = coords[1];
	    
	    //return if no moves for this piece
	    if (this.globMovePiece[pieceType] == 0) { return; }
	    
	    dojo.query("#square_piece_"+this.globPieceType+"_1").replaceClass("square_piece", "square_piece_select");
	    
            this.globPieceType = coords[1];
            
            //alert("piecetype:"+pieceType);
            
	    dojo.query("#square_piece_"+pieceType+"_1").replaceClass("square_piece_select", "square_piece");
	    
	    if (this.globLastTokenId != null) {
	      dojo.destroy(this.globLastTokenId);
	    }
	    
            this.updatePossibleMoves( this.globEnterArgs.args.possibleMoves );
        },
        
	changeOrient: function( evt )
        {
            // Stop this event propagation
            dojo.stopEvent( evt );

	    if (this.globGameState != "Place") { return; } //FIX	    
	    
            // Get the cliqued square x and y
            // Note: square id format is "square_X_Y"
            //alert("id:"+evt.currentTarget.id);	    
            //var coords = evt.currentTarget.id.split('_');
            //var piecetype = coords[1];
            //this.globPieceType = coords[1];

	    //toggle V/H
	    if (this.globOrient == 'V') { this.globOrient = 'H'; }
	    else {this.globOrient = 'V';}

	    dojo.byId("square_piece_1_8").innerHTML = dojo.replace(this.globOrient);
            
            //alert("orient:"+this.globOrient);
            
	    //remove any test pieces
	    if (this.globLastTokenId != null) {
	      dojo.destroy(this.globLastTokenId);
	    }
	    
	    this.updatePossibleMoves( this.globEnterArgs.args.possibleMoves );
        },
		
	endTurn: function()
        {
	    
	    this.globLastTokenId = null;
		
            var orient = this.globOrient; 
            
            var color = 'B';
            if (this.gamedatas.players[ this.player_id ].color == 'ffffff') { color = 'W'; }

            if( this.checkAction( 'endTurn' ) )    // Check that this action is possible at this moment
            {
		
		this.ajaxcall( "/isaac/isaac/endTurn.html", {
			    x:this.globX,
			    y:this.globY,
			    pieceType:this.globPieceType,
			    orient:orient,
			    color:color
		}, this, function( result ) {} );
			
		//note this section needs to follow after recording the played piece since globPieceType may be set to null if no pieces left
    
		for ( var pieceType = 1; pieceType <= 5; pieceType++ ) {
		    if (this.globPieceType == pieceType) {
			this.globCountPiece[pieceType]--;
			dojo.byId("square_piece_"+pieceType+"_1").innerHTML = dojo.replace("x"+this.globCountPiece[pieceType]);
			
		    }
		} //for pieceType
	    
	    }

	    //var n = dojo.byId("token_test");
	    //dojo.attr(n, "id", "token_"+this.globX+"_"+this.globY);
	    //dojo.attr(n, "class", "token");

	    //this.addTokenOnBoard( this.globX, this.globY, this.player_id, notif.args.pieceType,null,notif.args.orient );
	    
        },

        checkDisplayPiece: function()
        {
	   for ( var pieceType = 1; pieceType <= 5; pieceType++ ) {
	    if (this.globCountPiece[pieceType] == 0) {
 
	      if (dojo.byId("piecetype_"+pieceType) != null) {
		 dojo.destroy("piecetype_"+pieceType);
	      }
	      if (this.globPieceType == pieceType) {
		 this.globPieceType = null;
		 //this.updatePossibleMoves( this.globEnterArgs.args.possibleMoves );
	      }
	    }
	   } //for pieceType

	},
	
        updatePossibleMoves: function( possibleMoves )
        {
            // Remove current possible moves
	    this.addTooltipToClass( 'possibleMove', '', _('') );
            dojo.query( '.possibleMove' ).removeClass( 'possibleMove' );

	    //alert("debugPossible");
	    for (var orient in possibleMoves )
	    {
	    var orientIndex = 1;
	    if (this.globOrient == 'H') { orientIndex = 2; }
	    //alert("orientIndex:"+orientIndex+":globOrient:"+this.globOrient)
	    if (orient == orientIndex) {
	    for (var z in possibleMoves[orient] )
	    {
	    //alert("z_hey:"+z+":globPieceType:"+this.globPieceType)
	    //if (parseInt(z) == this.globPieceType.toString()) {
	    if (z == this.globPieceType) {		
	    //alert("z_true:"+z)
	    
            for( var x in possibleMoves[orient][z] )
            {
                for( var y in possibleMoves[orient][z][x] )
                {
		    
		    // x,y,z is a possible move
                    dojo.addClass( 'square_'+x+'_'+y, 'possibleMove' );
		    //alert("debugPossible:"+x+"_"+y);
		    
                } //for y        
            } //for x
	    
	    } //if z
	    } //for z
	    
	    } //if orient
	    } //for orient
	    
            this.addTooltipToClass( 'possibleMove', '', _('Place a piece here') );
        },	


	
        updatePossibleMovesRemove: function( possibleMovesRemove )
        {
	    //alert("updatePossibleMovesRemove");
            // Remove current possible moves
	    this.addTooltipToClass( 'possibleMove', '', _('') );
            dojo.query( '.possibleMove' ).removeClass( 'possibleMove' );
	    
	    this.addTooltipToClass( 'possibleScore', '', _('') );
            dojo.query( '.possibleScore' ).removeClass( 'possibleScore' );	    

	    for (var idxPlayer in possibleMovesRemove ) {
		
	    if (idxPlayer == this.globIdxPlayer) {		
	    //alert("idxPlayer_true:"+idxPlayer)
	    
            for (var square in possibleMovesRemove[idxPlayer] )
            {
		this_square = possibleMovesRemove[idxPlayer][square];	
	    
                //for( var y in possibleMovesRemove[idxPlayer][x] )
                //{
		    
		    // x,y,z is a possible move
                    dojo.addClass( 'square_'+this_square.x+'_'+this_square.y, 'possibleMove' );
		    //alert("debugPossible:"+x+"_"+y);
		    
                //} //for y        
            } //for square
	    
	    } //if idxPlayer
	    } //for idxPlayer
	       
            this.addTooltipToClass( 'possibleMove', '', _('Remove piece here') );
        },
	
        updatePossibleMovesScore: function( possibleMovesScore ) {
	    //alert("updatePossibleMovesScore");
            // Remove current possible moves
	    this.addTooltipToClass( 'possibleMove', '', _('') );
            dojo.query( '.possibleMove' ).removeClass( 'possibleMove' );
	    //alert("debug4");
	    
            for (var x in possibleMovesScore ) {
		for (var y in possibleMovesScore[x]) {
	
		//alert("debugPossible:"+x+"_"+y);		    
		// x,y is a possible move
		dojo.addClass( 'square_'+x+'_'+y, 'possibleScore' );
		}
            } //for square

            this.addTooltipToClass( 'possibleScore', '', _('Choose score here') );

        },		
	
	//this function currently just exists to reset the test play tokens before end turn
	resetGlob: function()
        {
	    this.globLastTokenId = null;
	    //this.globPieceType = null;
	    //alert("globReset");
	},
	
	turnClear: function()
        {
	    this.globGameState = "";
	    
	    this.addTooltipToClass( 'possibleMove', '', _('') );
            dojo.query( '.possibleMove' ).removeClass( 'possibleMove' );
	    
	    this.addTooltipToClass( 'possibleScore', '', _('') );
            dojo.query( '.possibleScore' ).removeClass( 'possibleScore' );	
	},	
	
        updateMovePiece: function( movePiece )
        {	
	    for ( var pieceType = 1; pieceType <= 5; pieceType++ ) {
		
		//this.globCountPiece[pieceType] = gamedatas.countPiece[idxPlayer][pieceType];
		this.globMovePiece[pieceType] = movePiece[pieceType];
		//dojo.byId("square_piece_"+pieceType+"_1").innerHTML = dojo.replace("x"+this.globCountPiece[pieceType]);
		
		if (this.globCountPiece[pieceType] > 0) {
		    //this.addPiece( pieceType, initOffset, this.player_id, pieceType );
		    //alert("movePiece:"+pieceType+":"+this.globMovePiece[pieceType]);
		    if (this.globMovePiece[pieceType] == 0) { dojo.addClass( 'piecetype_'+pieceType, 'noMove' ); }
		}
	    }
       },		    
	    
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your isaac.game.php file.
        
        */
        setupNotifications: function()
        {
            console.log( 'notifications subscriptions setup' );
            
            // TODO: here, associate your game notifications with local methods
            
            // Example 1: standard notification handling
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            
            // Example 2: standard notification handling + tell the user interface to wait
            //            during 3 seconds after calling the method in order to let the players
            //            see what is happening in the game.
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
            // 
            
            dojo.subscribe( 'playDisc', this, "notif_playDisc" );
            this.notifqueue.setSynchronous( 'playDisc', 500 );
	    
	    dojo.subscribe( 'endTurn', this, "notif_endTurn" );
            this.notifqueue.setSynchronous( 'endTurn', 500 );

            dojo.subscribe( 'removePiece', this, "notif_removePiece" );
            this.notifqueue.setSynchronous( 'removePiece', 500 );
            
	    dojo.subscribe( 'newScores', this, "notif_newScores" );
 
            dojo.subscribe( 'scoreMarker', this, "notif_scoreMarker" );
            this.notifqueue.setSynchronous( 'scoreMarker', 500 );
            
        },  
        
        // TODO: from this point and below, you can write your game notifications handling methods
        
        /*
        Example:
        
        notif_cardPlayed: function( notif )
        {
            console.log( 'notif_cardPlayed' );
            console.log( notif );
            
            // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call
            
            // TODO: play the card in the user interface.
        },    
        
        */

        notif_playDisc: function( notif )
        {
        
            console.log( 'notif_playDisc' );
	    console.log( notif );

            // Remove current possible moves (makes the board more clear)
            //dojo.query( '.possibleMove' ).removeClass( 'possibleMove' );        
        
            this.addTokenOnBoard( notif.args.x, notif.args.y, notif.args.player_id, notif.args.pieceType,null,notif.args.orient );
        },

        notif_endTurn: function( notif )
        {
        
            console.log( 'notif_endTurn' );
	    console.log( notif );

            this.resetGlob();
        },

        notif_removePiece: function( notif )
        {
        
            console.log( 'notif_removePiece' );
	    console.log( notif );

            // Remove current possible moves (makes the board more clear)
            //dojo.query( '.possibleMove' ).removeClass( 'possibleMove' );        
        
            this.destroyToken( notif.args.x, notif.args.y );
        },
	
        notif_newScores: function( notif )
        {
            for( var player_id in notif.args.scores )
            {
                var newScore = notif.args.scores[ player_id ];
                this.scoreCtrl[ player_id ].toValue( newScore );
            }
        },
	
        notif_scoreMarker: function( notif )
        {
	    this.moveScoreMarker( notif.args.x, notif.args.y, notif.args.player_id );
        },	

        

   });             
});
