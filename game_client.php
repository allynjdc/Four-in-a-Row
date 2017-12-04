<html>
  <head>
    <meta charset="UTF-8">
    <title>137 Connect Four</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/table.css">
    <link rel="stylesheet" type="text/css" href="css/interface_design.css">
    <link rel="stylesheet" type="text/css" href="css/animate.css">
    <script
			  src="http://code.jquery.com/jquery-3.2.1.min.js"
			  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
			  crossorigin="anonymous"></script>
    <script src="js/websocket.js"></script>
    <script src="jquery.min.js"></script>
  </head>
  <body>
    <div class="wrapper">
      <div class="info animated bounceInDown">
        <h1 id="title">Four-in-a-Row</h1>
        <p class="desc">To win the game, pick your player number then take turns placing your colors to match four of the same color in a row! </p>
        <!-- Sets Who Player is Playing As-->
        <label for="playerNum">Player Number:</label>
        <input type="number" name="playerNum" id="playerNum" min="1" max="2" />
      </div>
      <!-- Table for the grid-->
      <table class="board">
        <tbody>
          <?php #loops to create a 6x7 gird
            for($x = 0; $x <= 5; $x++){
              echo "<tr>";
                for($y = 0; $y <= 6; $y++){
                    echo '<td><button type="button"></button></td>';
                }
              echo "</tr>";
            }
            ?>
          </tbody>
        </table>
        <div class="message_box" id="message_box">Test</div>
      </div>
    </body>
    <div style="position: relative">
      <p style="position: fixed; bottom: 0; width:100%; text-align: center; font-size: 14px;">Created by the Allyn, Kyne, & Jemwel in Fulfillment of CMSC 137</p>
    </div>
</html>
