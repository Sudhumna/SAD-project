<?php
   include("connection.php");
?>
<html>
  <head>
    <style>
       .contaner{
    position: absolute;
    left: 0; 
    top: 0;
    width: 100%;
    height: 100%;
}
.hero{
    width: 100%;
    height: 100%;
    background: linear-gradient(rgba(216, 216, 206, 0.7), rgba(26, 26, 6, 0.7));
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;
}
            footer {
      background-color: rgb(198, 245, 245);
      position: fixed;
    width: 100%;
    text-align:center;
    left: 0px;
    bottom: 0px;
}  
    </style>
    <center>
    <div class="contaner">
     <div class="hero">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['name', 'votes'],
         <?php
         $sql = "SELECT * FROM user";
         $fire = mysqli_query($connect,$sql);
          while ($result = mysqli_fetch_assoc($fire)) {
            echo"['".$result['name']."',".$result['votes']."],";
          }

         ?>
        ]);

        var options = {
          title: 'Group-Wise Voting Result',
          is3D:true,
         // pieHole:0.4
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>
     </div>
     </div>
     </center>

  </head>
  <footer>
  <p>Developed by Sudhumna Phuyal</p>
   
</footer>
  <body>
    <div id="piechart" style=" width: 900px; height: 500px;"></div>
  </body>
</html>