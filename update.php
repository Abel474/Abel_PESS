<?php
$btnSearchClicked = isset($_POST['btnSearch']);
$car = null;
$statuses = [];

if($btnSearchClicked == true) {
    require_once 'db.php';
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
    $carId = $_POST['patrolCarId'];
    $sql = "SELECT * FROM patrolcar WHERE patrolcar_id = '" . $carId . "'";
    $result = $conn->query($sql);
    if($row = $result->fetch_assoc() ) {
        $id = $row['patrolcar_id'];
        $statusId = $row['patrolcar_status_id'];
        $car = ['id' => $id, 'statusId' => $statusId];
    }
    
    $sql = "SELECT * FROM patrolcar_status";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        $id = $row['patrolcar_status_id'];
        $desc = $row['patrolcar_status_desc'];
        $status = ['id'=>$id,'desc'=>$desc];
        array_push($statuses,$status);
    }
    $conn->close();
}

$btnUpdateClicked = isset($_POST['btnUpdate']);
if($btnUpdateClicked == true) {
    require_once 'db.php';
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
    $carId = $_POST['patrolCarId'];
    $newStatusId = $_POST['carStatus'];
    $sql = "UPDATE patrolcar SET patrolcar_status_id = " . $newStatusId . " where patrolcar_id='" . $carId . "'";
    $updateSuccess = $conn->query($sql);
    if($updateSuccess === FALSE) {
        echo 'Error:' . $sql . "<br>" . $conn->error;
    }
    
    if($newStatusId == '4') {
        $sql = "UPDATE dispatch SET time_arrived = NOW() WHERE time_arrived is NULL AND patrolcar_id='" . $carId . "'";
        $updateSuccess = $conn->query($sql);
        if($updateSuccess === FALSE) {
            echo 'Error:' . $sql . "<br>" . $conn->error;
        }
    }
    else if($newStatusId == '3') {
        $sql = "UPDATE dispatch SET time_completed = NOW() WHERE time_completed is NULL AND patrolcar_id='" . $carId . "'";
        //echo 'sql:' . $sql;
        $updateSuccess = $conn->query($sql);
        if($updateSuccess === FALSE) {
            echo 'Error:' . $sql . "<br>" . $conn->error;
        }
    }
    if($updateSuccess == true) {
        header("Location: search.php");
    }
    $conn->close();
}

?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Log Call
    </title>
    <link href="css/bootstrap-4.3.1.css" rel="stylesheet" type="text/css">
    <style type="text/css">
    </style>
  </head>
  <body>
    <div class="container" style="width: 930px">
      <header>
        <img src="images/banner.jpg" width="900" height="200" alt="" />
      </header>
      <?php
        require_once 'nav.php';
        ?>
      <section style="margin-top: 20px">
        <form action="update.php" method="post">
           <?php
           if($car != null) {
            	echo '<div class="form-group row">';
                echo '<label for="patrolCarId" class="col-sm-4 col-form-label">
                  Car Number
                </label>';
                echo '<div class="col-sm-8">';
                echo $car['id'];
                echo '<input type="hidden" class="form-control" id="patrolCarId"
                         name="patrolCarId" value="' . $car['id'] . '">';
                echo '</div>';
                echo '</div>';
                
                echo '<div class="form-group row">';
                echo '<label for="carStatus" class="col-sm-4 col-form-label">
                  Patrol Car Status
                </label>';
                echo '<div class="col-sm-8">';
                echo ' <select id="carStatus" class="form-control" name="carStatus">';
                $totalStatus = count($statuses);
                for($i = 0; $i<$totalStatus;$i++ ) {
                    $status = $statuses[$i];
                    $selected = "";
                    if($status['id'] == $car['statusId']) {
                        $selected = ' selected="selected"';
                    }
                    echo "<option value='" . $status['id'] . "'" .
                         $selected . ">" . 
                         $status['desc'] .
                         "</option>";
                    
                }
                echo " </select>";
                echo '</div>';
                echo '</div>';
           }
           else {
               echo '<div class="form-group row">';
               echo '<div class="col-sm-12">';
               echo 'No records found';
               echo '</div>';
               echo '</div>';
           }
          ?>
          <div class="form-group row">
            <div class="col-sm-4">
            </div>
            <div class="col-sm-8">
              <input class="btn btn-primary" name="btnUpdate" type="submit"
                     value="Update">
            </div>
          </div>
        </form>
      </section>
      <footer 
      		class="page-footer font-small blue pt-4 
      			   footer-copyright text-center py-3">
            © 2020 Copyright: 
            <a href="https://www.ite.edu.sg"> ITE </a>
      </footer>
    </div>
    <script type="text/javascript" src="js/jquery-3.3.1.min.js">
    </script>
    <script type="text/javascript" src="js/popper.min.js">
    </script>
    <script type="text/javascript" src="js/bootstrap-4.3.1.js">
    </script>
  </body>
</html>
