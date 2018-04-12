<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Room</title>
    	<link type="text/css" rel="stylesheet" href="media/layout.css" />
        <script src="js/jquery-1.11.2.min.js" type="text/javascript"></script>
        <script src="js/angular.min.js" type="text/javascript"></script>
        <script src="js/daypilot/daypilot-all.min.js" type="text/javascript"></script>
    </head>
    <body>
        <?php
            // check the input
            is_numeric($_GET['id']) or die("invalid URL");

            require_once '_db.php';

            $stmt = $db->prepare('SELECT * FROM rooms WHERE id = :id');
            $stmt->bindParam(':id', $_GET['id']);
            $stmt->execute();
            $room = $stmt->fetch();
        ?>
        <div ng-app="main" ng-controller="EditRoomController" style="padding:10px">
            <input type="hidden" name="id" value="<?php print $_GET['id'] ?>" />
            <h1>Edit Room</h1>
            <div>Name: </div>
            <div><input type="text" id="name" name="name" ng-model="room.name" /></div>
            <div>Capacity:</div>
            <div>
                <select id="capacity" name="capacity" ng-model="room.capacity" >
                    <?php
                        $options = array(1, 2, 4);
                        foreach ($options as $option) {
                            $selected = $option == $room['capacity'] ? ' selected="selected"' : '';
                            $id = $option;
                            $name = $option;
                            print "<option value='$id' $selected>$name</option>";
                        }
                    ?>
                </select>
            </div>
            <div>Status:</div>
            <div>
                <select id="status" name="status" ng-model="room.status" >
                    <?php					
						// Fix for ENI-22. UNCOMMENT THE BELOW LINE
						// $options = array("Ready", "Cleanup", "Dirty", "Occupied", "Reserved"); 
                        
						// Comment only the below line while finxing ENI-22
						// Update for ENI-46 
                        //$options = array("Ready", "Cleanup", "Dirty","Occupied","Reserved");
						$options = array("Ready", "Cleanup", "Dirty");
						foreach ($options as $option) {
                            $selected = $option == $room['status'] ? ' selected="selected"' : '';
                            $id = $option;
                            $name = $option;
                            print "<option value='$id' $selected>$name</option>";
                        }
                    ?>
                </select>
            </div>


            <div class="space"><input type="submit" value="Save" ng-click="save()" /> 
				<!-- Fix for ENI-26 -->
				<!-- Uncomment the below line -->
				<button type="cancel" ng-click="cancel()">Cancel</button> 
				<!-- Comment the below line while fixing ENI-26-->
				<!-- <a href="" ng-click="cancel()">Cancel</a> -->
			</div> 
        </div>

        <script type="text/javascript">
        var app = angular.module('main', ['daypilot']).controller('EditRoomController', function($scope, $timeout, $http) {
            $scope.room = {
                id: <?php print $room['id'] ?>,
                name: '<?php print $room['name'] ?>',
                capacity: <?php print $room['capacity'] ?>,
                status: '<?php print $room['status'] ?>'
            };
            $scope.save = function() {
                $http.post("backend_room_update.php", $scope.room).success(function(data) {
                    DayPilot.Modal.close(data);
                });
            };
            $scope.cancel = function() {
                DayPilot.Modal.close();
            };

            $("#name").focus();
        });

        </script>
    </body>
</html>
