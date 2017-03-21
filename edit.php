<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Event</title>
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
            
            $stmt = $db->prepare('SELECT * FROM reservations WHERE id = :id');
            $stmt->bindParam(':id', $_GET['id']);
            $stmt->execute();
            $reservation = $stmt->fetch();
            
            $rooms = $db->query('SELECT * FROM rooms');
        ?>
        <div ng-app="main" ng-controller="EditReservationController" style="padding:10px">
            
            <h1>Edit Reservation</h1>
            <div>Start:</div>
            <div><input type="text" id="start" name="start" ng-model="reservation.start" date-format="d/M/yyyy" /></div>
            <div>End:</div>
            <div><input type="text" id="end" name="end" ng-model="reservation.end" date-format="d/M/yyyy" /></div>
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
            <div>Name: </div>
            <div><input type="text" id="name" name="name" ng-model="reservation.name" /></div>
            <div>Status:</div>
            <div>
                <select id="status" name="status" ng-model="reservation.status">
                    <?php 
                        $options = array("New", "Confirmed", "Arrived", "CheckedOut");
                        foreach ($options as $option) {
                            $id = $option;
                            $name = $option;
                            print "<option value='$id'>$name</option>";
                        }
                    ?>
                </select>                
            </div>
            <div>Paid:</div>
            <div>
                <select id="paid" name="paid" ng-model="reservation.paid">
                    <?php 
                        $options = array(0, 50, 100);
                        foreach ($options as $option) {
                            $id = $option;
                            $name = $option."%";
                            print "<option value='$id'>$name</option>";
                        }
                    ?>
                </select>
                
            </div>
            
            <div class="space"><input type="submit" value="Save" ng-click="save()" /> <a href="" id="cancel" ng-click="cancel()">Cancel</a></div>
        </div>
        
        <script type="text/javascript">
        var app = angular.module('main', ['daypilot']).controller('EditReservationController', function($scope, $timeout, $http) {
            $scope.reservation = {
                id: <?php print $reservation['id'] ?>,
                name: '<?php print $reservation['name'] ?>',
                start: '<?php print $reservation['start'] ?>',  // use ISO format for the model
                end: '<?php print $reservation['end'] ?>',      // use ISO format for the model
                room: <?php print $reservation['room_id'] ?>,
                status: '<?php print $reservation['status'] ?>',
                paid: <?php print $reservation['paid'] ?>
            };
            $scope.save = function() {
                $http.post("backend_update.php", $scope.reservation).success(function(data) {
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
