{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- Isaac implementation : © <Your name here> <Your email address here>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------

    isaac_isaac.tpl
    
    This is the HTML template of your game.
    
    Everything you are writing in this file will be displayed in the HTML page of your game user interface,
    in the "main game zone" of the screen.
    
    You can use in this template:
    _ variables, with the format {MY_VARIABLE_ELEMENT}.
    _ HTML block, with the BEGIN/END format
    
    See your "view" PHP file to check how to set variables and control blocks
-->

<script type="text/javascript">

// Templates

var jstpl_token='<div class="token tokencolor_${color}_${piecetype}" id="token_${x_y}"></div>';

var jstpl_token_test='<div class="token_test tokencolor_${color}_${piecetype}" id="token_test"></div>';

var jstpl_piece='<div class="piece piececolor_${color}_${piecetype}" id="piecetype_${piecetype}"></div>';

var jstpl_score='<div class="scoreMarker scorecolor_${color}" id="score_marker_${player_id}"></div>';

</script>

<div id="board">

    <div id="tokens">
    </div>
    <div id="scores">
    </div>
 
    <!-- BEGIN square -->
    <div id="square_{X}_{Y}" class="square" style="left: {LEFT}px; top: {TOP}px;"></div>
    <!-- END square -->
    


    <div id="area_pieces">

    <!-- BEGIN square_piece -->
    <div id="square_piece_{X}_{Y}" class="square_piece" style="left: {LEFT}px; top: {TOP}px;"></div>
    <!-- END square_piece -->

    <div id="pieces">
    </div>
    
    </div>
</div>

{OVERALL_GAME_FOOTER}
