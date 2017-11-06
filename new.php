<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>New Reservation</title>
    	<link type="text/css" rel="stylesheet" href="media/layout.css" />    
        <script src="js/jquery-1.11.2.min.js" type="text/javascript"></script>
        <script src="js/angular.min.js" type="text/javascript"></script>
        <script src="js/daypilot/daypilot-all.min.js" type="text/javascript"></script>
    </head>
    <body>
        <?php
            // check the input
            is_numeric($_GET['resource']) or die("invalid URL");
            
            require_once '_db.php';
            
            $rooms = $db->query('SELECT * FROM rooms');
            
            $resource = $_GET['resource'];
            $start = (new DateTime($_GET['start']))->format("Y-m-d\\TH:i:s");
            $end = (new DateTime($_GET['end']))->format("Y-m-d\\TH:i:s");
        ?>
        
        <div ng-app="main" ng-controller="NewReservationController" style="padding:10px">
			<!-- Fix for ENI-8 -->
			<!-- Uncomment the below line -->
            <!-- New Room Reservation -->
			<!-- comment the below line as the Fix for ENI-8-->
            <h1>New Reservation</h1>
            <div>Name: </div>
            <div><input type="text" id="name" name="name" ng-model="reservation.name" /></div>
            <div>Start:</div>
            <div><input type="text" id="start" name="start" ng-model="reservation.start" date-format="d/M/yyyy" /> <span ng-show="!reservation.start">Invalid date</span></div>
            <div>End:</div>
            <div><input type="text" id="end" name="end" ng-model="reservation.end" date-format="d/M/yyyy" /> <span ng-show="!reservation.end">Invalid date</span></div>
            <div>Room:</div>
            <div>
                <select id="room" name="room" ng-model="reservation.room">
                    <?php 
                        foreach ($rooms as $room) {
                            $id = $room['id'];
                            $name = $room['name'];
                            print "<option value='$id'>$name</option>";
                        }
                    ?>
                </select>
                
            </div>
            <div class="space"><input type="submit" value="Save" ng-click="save()" /> 
			<!-- Fix for ENI-6 -->
			<!-- Uncomment the below line -->
			<!-- <button type="cancel" ng-click="cancel()">Cancel</button> -->
			<!-- comment the below line as the Fix for ENI-6 -->
			<a href="" id="cancel" ng-click="cancel()">Cancel</a> 
			</div>
        </div>
        
        <script type="text/javascript">
            
        var app = angular.module('main', ['daypilot']).controller('NewReservationController', function($scope, $timeout, $http) {
            $scope.reservation = {
                name: '',
                start: '<?php print $start ?>',  // use ISO format for the model
                end: '<?php print $end ?>',      // use ISO format for the model
                room: <?php print $resource ?>
            };
            $scope.delete = function() {
                $http.post("backend_delete.php", $scope.reservation).success(function(data) {
                    DayPilot.Modal.close(data);
                });   
            };
            $scope.save = function() {
                $http.post("backend_create.php", $scope.reservation).success(function(data) {
                    DayPilot.Modal.close(data);
                });
            };
            $scope.cancel = function() {
                DayPilot.Modal.close();
            };
            
            $("#name").focus();
        });
        
        
        app.directive('dateFormat', function() {
            return { restrict: 'A',
              require: 'ngModel',
              link: function(scope, element, attrs, ngModel) {
                if(ngModel) {
                    // parse the input value using the format string, pass the normalized ISO8601 value to the model
                    // unparseable value returns null
                    ngModel.$parsers.push(function (value) {
                        var d = DayPilot.Date.parse(value, attrs.dateFormat); 
                        return d && d.toString();
                    });
                    // display the date in the specified format
                    // null value will be returned as null
                    ngModel.$formatters.push(function (value) {
                        return value && new DayPilot.Date(value).toString(attrs.dateFormat);
                    });
                }
              }
            };
        });

        </script>
    </body>
</html>
