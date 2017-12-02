<html>
  <head>
    <meta charset="UTF-8">
    <title>137 Connect Four</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/mystyle.css">
    <script
			  src="http://code.jquery.com/jquery-3.2.1.min.js"
			  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
			  crossorigin="anonymous"></script>
    <script src="js/interface.js"></script>
  </head>
  <body>
    <div class="wrapper">
      <div class="info">
        <h1 class="title">Four-in-a-Row</h1>
        <p class="desc">To play four in a row, you have to take turns placing your colors to match in a row! </p>
      </div>
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
      </div>
    </body>

  <footer class="wrapper">
    <p id="gemz">Created by the Allyn, Kyne, & Jemwel in Fulfillment of CMSC 137</p>
  </footer>
</html>
