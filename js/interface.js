//representation of game board
var board = [[0,0,0,0,0,0,0],
             [0,0,0,0,0,0,0],
             [0,0,0,0,0,0,0],
             [0,0,0,0,0,0,0],
             [0,0,0,0,0,0,0],
             [0,0,0,0,0,0,0]];

var currentPlayer = "green";
//Setup Game.
$(document).ready(function() {

  $('.board button').click(function(e) {
    var x_pos = $(this).closest('tr').find('td').index($(this).closest('td')); //looks for the closest td index along the tr clicked
    var y_pos = $('.board tr').index($(this).closest('tr'));// looks for the closest tr index in this case the one clicked

    y_pos = dropDown(x_pos, y_pos);

    if (posTaken(x_pos, y_pos)){
      $('.board').addClass('animated shake');
      $('.board').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
        $('.board').removeClass('animated shake');
          alert("You cant place your color there");
      });
      return;
    }

    addDiscToBoard(currentPlayer, x_pos, y_pos);
    printBoard();//prints of board

    changePlayer();//change players
  });

});

function addDiscToBoard(color, x_pos, y_pos) {
    board[y_pos][x_pos] = color;
    //board with current position will contain that color
}

function posTaken(x_pos,y_pos){
  var value = board[y_pos][x_pos];

  if (value == 0){
  //returns value that the board is not taken
    return false;
  } else{
  //returns value that the board is taken
    return true;
  }
}
function printBoard() {
    // Loop through the board, and add classes to each cell for the
    // appropriate colors.
    for (var y = 0; y <= 5; y++) {
        for (var x = 0; x <= 6; x++) {
            if (board[y][x] != 0) {
                var cell = $("tr:eq(" + y + ")").find('td').eq(x);
                cell.children('button').addClass(board[y][x]);
            }
        }
    }
}

function dropDown(x_pos, y_pos) {
    //position of y should always fall down if it doesn't then it should take the nearest space.
    for (var v = 5; v > y_pos; v--) {
        if (board[v][x_pos] == 0) {
            return v;
        }
    }
    return y_pos;
}

function changePlayer() {
    // Change the color's value of our player variable.
    if (currentPlayer == 'yellow') {
        currentPlayer = 'red';
    } else {
        currentPlayer = 'yellow';
    }

    // Update the UI.
    //$('#player').removeClass().addClass(currentPlayer).text(config[currentPlayer + "PlayerName"]);
}
