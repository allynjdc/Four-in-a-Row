<?php

$gameField = array();
$Tcol = 7;
$Trow = 6;
$grid;
$currentPlayer;

function newGame() {
  $GLOBALS['grid'] = prepareField();
  printGrid();
}

function prepareField() {
	for($i = 0; $i < $GLOBALS['Trow']; $i++) {
		$GLOBALS['gameField'][$i] = array();
		for($j = 0; $j < $GLOBALS['Tcol']; $j++) {
			$GLOBALS['gameField'][$i][$j] = -1;	
		}	
	}
	return $GLOBALS['gameField'];
}

function printGrid() {
	for($i = 0; $i<$GLOBALS['Trow']; $i++) {
			for($j = 0; $j < $GLOBALS['Tcol']; $j++) {
				echo " ".$GLOBALS['grid'][$i][$j];
			}	
			echo "<br/>";
	}
}

function dropPiece($player, $target_col) {
	for($i = 5; $i >= 0; $i--) {
		if($GLOBALS['grid'][$i][$target_col] === -1) {
			$GLOBALS['grid'][$i][$target_col]= $player;
      echo "<br/><br/>".$GLOBALS['grid'][$i][$target_col]."<br/><br/>";
			if(checkForVictory($i,$target_col)) {
        echo "Player ".$player." won!";
        newGame();
				return "victory";			
			} else {
				return "continue";
			}
			break;
		}
	}
} 

function checkForVictory($row,$col) {
  if(getAdj($row,$col,0,1)+getAdj($row,$col,0,-1) > 2){ //horizontal
		return true;
  } else if(getAdj($row,$col,1,0) > 2){ //vertical
    return true;
  } else if(getAdj($row,$col,-1,1)+getAdj($row,$col,1,-1) > 2){ //diagonal "\"
    return true;
  } else if(getAdj($row,$col,1,1)+getAdj($row,$col,-1,-1) > 2){ //diagonal "/"
    return true;
  } else {
    return false;
  }
}

function getAdj($row,$col,$row_inc,$col_inc) {
  if(cellVal($row,$col) == cellVal($row+$row_inc,$col+$col_inc)) {
    return 1+getAdj($row+$row_inc,$col+$col_inc,$row_inc,$col_inc);
  } else {
    return 0;
  }
}

function cellVal($row,$col) {
  if($row < 0 || $row > 5 || $col < 0 || $col > 6) {
    return -1;
  } else {
    return $GLOBALS['grid'][$row][$col];
  }
}

//$grid = prepareField();
//printGrid();
newGame();
echo "--------------<br/>";

// dropPiece(1, 6);
// dropPiece(1, 5);
// dropPiece(2, 4);
// dropPiece(1, 3);
// dropPiece(1, 3);
// dropPiece(2, 4);
// dropPiece(2, 4);
// dropPiece(2, 4);

$host = 'localhost';
$port = '10000';
$null = NULL;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($socket, 0, $port);
socket_listen($socket);

$clients = array($socket);

while (true) {
  $changed = $clients;
  socket_select($changed, $null, $null, 0, 10); //returns the socket resources in $changed array
  
  //check for new socket
  if (in_array($socket, $changed)) {
    $socket_new = socket_accept($socket);
    $clients[] = $socket_new;
    
    $header = socket_read($socket_new, 1024);
    perform_handshaking($header, $socket_new, $host, $port);
    
    socket_getpeername($socket_new, $ip);
    $response = mask(json_encode(array('type'=>'system', 'message'=>$ip.' connected')));
    send_message($response); //notify all users about new connection
    
    //make room for new socket
    $found_socket = array_search($socket, $changed);
    unset($changed[$found_socket]);
  }
  
  //loop through all connected sockets
  foreach ($changed as $changed_socket) { 
    
    //check for any incomming data
    while(socket_recv($changed_socket, $buf, 1024, 0) >= 1)
    {
      $received_data = unmask($buf); //unmask data
      $player_data = json_decode($received_data); 
      $player_num = $player_data->player_num;
      $player_col = $player_data->player_col;
		
		  $game_status = dropPiece($player_num, $player_col);		
		
      //prepare data to be sent to client
      $response_text = mask(json_encode(array('type'=>'msg','player_num'=>$player_num, 'player_col'=>$player_col, 'game_status'=>$game_status)));
      send_message($response_text); //send data
      break 2; //exist this loop
    }
    
    $buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
    if ($buf === false) { // check disconnected client
      // remove client for $clients array
      $found_socket = array_search($changed_socket, $clients);
      socket_getpeername($changed_socket, $ip);
      unset($clients[$found_socket]);
      
      //notify all users about disconnected connection
      $response = mask(json_encode(array('type'=>'system', 'message'=>$ip.' disconnected')));
      send_message($response);
    }
  }
}
// close the listening socket
socket_close($socket);
function send_message($msg) {
  global $clients;
  foreach($clients as $changed_socket) {
    @socket_write($changed_socket,$msg,strlen($msg));
  }
  return true;
}
//Unmask incoming framed message
function unmask($text) {
  $length = ord($text[1]) & 127;
  if($length == 126) {
    $masks = substr($text, 4, 4);
    $data = substr($text, 8);
  } elseif($length == 127) {
    $masks = substr($text, 10, 4);
    $data = substr($text, 14);
  } else {
    $masks = substr($text, 2, 4);
    $data = substr($text, 6);
  }
  $text = "";
  for ($i = 0; $i < strlen($data); ++$i) {
    $text .= $data[$i] ^ $masks[$i%4];
  }
  return $text;
}
//Encode message for transfer to client.
function mask($text) {
  $b1 = 0x80 | (0x1 & 0x0f);
  $length = strlen($text);
  
  if($length <= 125) {
    $header = pack('CC', $b1, $length);
  } elseif($length > 125 && $length < 65536) {
    $header = pack('CCn', $b1, 126, $length);
  } elseif($length >= 65536){
    $header = pack('CCNN', $b1, 127, $length);
  }
  return $header.$text;
}
//handshake new client.
function perform_handshaking($receved_header,$client_conn, $host, $port) {
  $headers = array();
  $lines = preg_split("/\r\n/", $receved_header);
  foreach($lines as $line) {
    $line = chop($line);
    if(preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
      $headers[$matches[1]] = $matches[2];
    }
  }

  $secKey = $headers['Sec-WebSocket-Key'];
  $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
  //hand shaking header
  $upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
  "Upgrade: websocket\r\n" .
  "Connection: Upgrade\r\n" .
  "WebSocket-Origin: $host\r\n" .
  "WebSocket-Location: ws://$host:$port/demo/shout.php\r\n".
  "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
  socket_write($client_conn,$upgrade,strlen($upgrade));
}

?>
