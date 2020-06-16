<?php
$callerName = $_POST['callerName'];
$contactNo = $_POST['contactNo'];
$locationOfIncident = $_POST['locationOfIncident'];
$typeOfIncident = $_POST['typeOfIncident'];
$descriptionOfincident = $_POST['descriptionOfIncident'];

$sql = 'SELECT patrolcar_id,patrolcar_status_desc FROM patrolcar INNER JOIN patrolcar_status ON patrolcar.patrolcar_status_id = patrolcar_status.patrolcar_status_id';
$cars = [];
require_once 'db.php';
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $id = $row['patrolcar_id'];
    $status = $row['patrolcar_status_desc'];
    
    $car = [
        'id' => $id,
        "status" => $status
    ];
    array_push($cars, $car);
}

$btnDispatchClicked = isset($_POST['btnDispatch']);

if ($btnDispatchClicked == true) {
    $isCarSelected = isset($_POST["cbCarSelection"]);
    $incident_status_id = 1;
    if ($isCarSelected == true) {
        $incident_status_id = 2;
    } else {
        $incident_status_id = 1;
    }
    $sql = "INSERT INTO incident(caller_name,incident_desc,incident_location,incident_status_id,incident_type_id)
                VALUES(
                " . "'" . $callerName . "'" . "," . "'" . $descriptionOfincident . "'" . "," . "'" . $locationOfIncident . "'" . "," . "'" . $incident_status_id . "'" . "," . "'" . $typeOfIncident . "'" . ")";
    echo '<br>';
    echo 'sql:' . $sql;
    $result = $conn->query($sql);
    
    echo '<br>';
    
    $insertIncidentSuccess = false;
    if ($result == true) {
        $insertIncidentSuccess = true;
    } else {
        echo "Error:" . $sql . "<br>" . $conn->error;
    }
    
    $incidentId = mysqli_insert_id($conn);
    
    $insertDispatchSuccess = false;
    $updateStatus = false;
    
    if ($isCarSelected == true) {
        $patrolCarDispatched = $_POST["cbCarSelection"];
        $numOfPatrolcarDispatched = count($patrolCarDispatched);
        
        for ($i = 0; $i < $numOfPatrolcarDispatched; $i ++) {
            $carId = $patrolCarDispatched[$i];
            $sql = "UPDATE patrolcar set patrolcar_status_id=1 where patrolcar_id='" . $carId . "'";
            // echo 'sql: ' . $sql;
            // echo '<br>';
            $updateStatus = $conn->query($sql);
            if ($updateStatus == false) {
                echo "Error:" . $sql . "<br>" . $conn->error;
                echo '<br>';
            } else {
                // echo 'Update on patrol car: ' . $carId . ' is success!';
                // echo '<br>';
            }
            
            $sql = "INSERT INTO dispatch(incident_id,patrolcar_id,time_dispatched)
                        VALUES(" . $incidentId . ',' . "'" . $carId . "'" . ",NOW())";
            // echo 'sql: ' . $sql;
            $insertDispatchSuccess = $conn->query($sql);
            if ($insertDispatchSuccess == false) {
                echo "Error:" . $sql . "<br>" . $conn->error;
                echo '<br>';
            }
        }
    }
    
    $conn->close();
    if ($insertIncidentSuccess == true && $updateStatus == true && $insertDispatchSuccess == true) {
        header("Location: logcall.php");
    }
}
?>
<!doctype html>
<html>

<head>
<meta charset="utf-8">
<title>Dispatch</title>
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
			<form action="dispatch.php" method="post">
				<div class="form-group row">
					<label for="callerName" class="col-sm-4 col-form-label">Caller's
						Name </label>
					<div class="col-sm-8">
						<span id="callerName">
                     		<?php echo $callerName;?>
                     		<input type="hidden" name="callerName"
							id="callerName" value="<?php echo $callerName;?>">
						</span>
					</div>
				</div>

				<div class="form-group row">
					<label for="contactNo" class="col-sm-4 col-form-label"> Contact No:
					</label>
					<div class="col-sm-8">
						<span id="contactNo">
                     		<?php echo $contactNo;?>
                     		<input type="hidden" name="contactNo"
							id="contactNo" value="<?php echo $contactNo;?>">
						</span>
					</div>
				</div>

				<div class="form-group row">
					<label for="locationOfIncident" class="col-sm-4 col-form-label">
						Location of Incident: </label>
					<div class="col-sm-8">
						<span id="locationOfIncident">
                     		<?php echo $locationOfIncident;?>
                     			<input type="hidden" name="locationOfIncident"
							id="locationOfIncident" value="<?php echo $locationOfIncident;?>">
						</span>
					</div>
				</div>

				<div class="form-group row">
					<label for="typeOfIncident" class="col-sm-4 col-form-label"> Type
						of Incident: </label>
					<div class="col-sm-8">
						<span id="typeOfIncident">
                     		<?php echo $typeOfIncident;?>
                     		<input type="hidden" name="typeOfIncident"
							id="typeOfIncident" value="<?php echo $typeOfIncident;?>">
						</span>
					</div>
				</div>

				<div class="form-group row">
					<label for="descriptionOfIncident" class="col-sm-4 col-form-label">
						Description of Incident: </label>
					<div class="col-sm-8">
						<span id="descriptionOfIncident">
                     		<?php echo $descriptionOfincident;?>
                     		<input type="hidden" name="descriptionOfIncident"
							id="descriptionOfIncident"
							value="<?php echo $descriptionOfincident;?>">
						</span>
					</div>
				</div>

				<div class="form-group row">
					<label for="patrolCars" class="col-sm-4 col-form-label"> Choose a
						Patrol Car </label>
					<div class="col-sm-8">
						<table id="patrolCars" class="table table-striped">
							<tbody>
								<tr>
									<th>Car Number</th>
									<th>Status</th>
									<th></th>
								</tr>
                     			<?php
                        for ($i = 0; $i < count($cars); $i ++) {
                            $car = $cars[$i];
                            echo '<tr>';
                            echo '<td>' . $car['id'] . '</td>';
                            echo '<td>' . $car['status'] . '</td>';
                            echo '<td>' . '<input name="cbCarSelection[]" type="checkbox"
                                              value="' . $car['id'] . '">';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                     		</tbody>
						</table>
					</div>
				</div>

				<div class="form-group row">

					<div class="col-sm-4"></div>

					<div class="col-sm-8" style="text-align: center">
						<input type="submit" name="btnDispatch" id="btnDispatch"
							value="Dispatch" class="btn btn-primary">
					</div>
				</div>

			</form>
		</section>



	</div>
</body>

</html>
