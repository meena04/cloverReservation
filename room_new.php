<!DOCTYPE html>
<!-- Code Tested by Meena Sriram -->
<html>
    <head>
        <meta charset="UTF-8">
        <title>New Room</title>
    	<link type="text/css" rel="stylesheet" href="media/layout.css" />
        <script src="js/jquery-1.11.2.min.js" type="text/javascript"></script>
        <script src="js/angular.min.js" type="text/javascript"></script>
        <script src="js/daypilot/daypilot-all.min.js" type="text/javascript"></script>
    </head>
    <body>
        <div ng-app="main" ng-controller="NewRoomController" style="padding:10px">
            <h1>New Room</h1>
            <div>Name: </div>
            <div><input type="text" id="name" name="name" value="" ng-model="room.name" /></div>
            <div>Capacity:</div>
            <div>
                <select id="capacity" name="capacity" ng-model="room.capacity">
                    <option value='1'>1</option>
                    <option value='2'>2</option>
                    <option value='4'>4</option>
                </select>
            </div>
			
            
            <div class="space"><input type="submit" value="Save" ng-click="save()" /> 
			<!-- Fix for ENI-7 -->
			<!-- Uncomment the below line -->
			 <button type="cancel" ng-click="cancel()">Cancel</button> 
			<!-- comment the below line as the Fix for ENI-7-->
			<!-- <a href="" ng-click="cancel()">Cancel</a> -->
			</div>
        </div>

        <script type="text/javascript">


        var app = angular.module('main', ['daypilot']).controller('NewRoomController', function($scope, $timeout, $http) {
            $scope.room = {
                name: '',
                capacity: 2
            };
            $scope.save = function() {
                $http.post("backend_room_create.php", $scope.room).success(function(data) {
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
