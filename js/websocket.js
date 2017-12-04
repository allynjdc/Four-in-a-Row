$(document).ready(function(){
    //create a new WebSocket object.
    var wsUri = "ws://10.55.11.213:10000/127_FinalProject/game_server.php";   
    websocket = new WebSocket(wsUri); 
    
    websocket.onopen = function(ev) { // connection is open 
        $('#message_box').append("<div class=\"system_msg\">Connected!</div>"); //notify user
    }
    $('.board button').click(function(e) { 
        var selectedCol = $(this).closest('tr').find('td').index($(this).closest('td'));
        var playerNum = $('#playerNum').val(); //get player number
        
        if(playerNum == ""){
            alert("Choose a player number!");
            return;
        }
     
        $('#playerNum').prop("disabled",true);
        
        //prepare json data
        var msg = {
            player_num: playerNum,
            player_col: selectedCol,
        };
        //convert and send data to server
        websocket.send(JSON.stringify(msg));
    });
    
    //#### Message received from server?
    websocket.onmessage = function(ev) {
        var msg = JSON.parse(ev.data); //PHP sends Json data
		var type = msg.type;
        var playerNum = msg.player_num; 
        var gameGrid = msg.grid;
        var gameStatus = msg.game_status;
        var systemMsg = msg.message;      

        if(type == 'msg') {
            console.log(gameGrid);
            printBoard(gameGrid);
        }

        if(type == 'system') {
            $('#message_box').append("<div class=\"system_msg\">"+systemMsg+"</div>");
        }

        if(gameStatus=="victory"){   
                alert("player "+playerNum+" wins!");
        }
        
        $('#playerCol').val(''); //reset text
        
        var objDiv = document.getElementById("message_box");
        objDiv.scrollTop = objDiv.scrollHeight;
    };
    
    websocket.onerror   = function(ev){$('#message_box').append("<div class=\"system_error\">Error Occurred - "+ev.data+"</div>");}; 
    websocket.onclose   = function(ev){$('#message_box').append("<div class=\"system_msg\">Connection Closed</div>");}; 
});

function printBoard(gameGrid) {
    // Loop through the board, and add classes to each cell for the
    // appropriate colors.
    for (var y = 0; y <= 5; y++) {
        for (var x = 0; x <= 6; x++) {
            if (gameGrid[y][x] != -1) {
                var cell = $("tr:eq(" + y + ")").find('td').eq(x);
                var color = (gameGrid[y][x] == 1)? "yellow": "red";
                cell.children('button').addClass(color);
            }
        }
    }
}