<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Ness.com  - Hotel Reservation Project </title>

        <!-- demo stylesheet -->
    	<link type="text/css" rel="stylesheet" href="media/layout.css" />

        <link type="text/css" rel="stylesheet" href="icons/style.css" />

		<script>
			function myFunction()
			{
				alert("Room1 = 1000/- INR\nRoom2 = 1000/- INR\nRoom3 = 1000/- INR\nRoom4 = 2000/- INR\nRoom5 = 500/- INR\nRoom6 = 500/- INR\nRoom7 = 1000/-INR\nRoom8 = 1000/- INR\nVIP Suite = 5000/- INR");
			}
		</script>
		
        <style type="text/css">
            .icon {
                font-size: 14px;
                text-align: center;
                line-height: 14px;
                vertical-align: middle;

                cursor: pointer;
            }
            .scheduler_default_rowheader
            {
                background: -webkit-gradient(linear, left top, left bottom, from(#eeeeee), to(#dddddd));
                background: -moz-linear-gradient(top, #eeeeee 0%, #dddddd);
                background: -ms-linear-gradient(top, #eeeeee 0%, #dddddd);
                background: -webkit-linear-gradient(top, #eeeeee 0%, #dddddd);
                background: linear-gradient(top, #eeeeee 0%, #dddddd);
                filter: progid:DXImageTransform.Microsoft.gradient(startColorStr="#eeeeee", endColorStr="#dddddd");

            }
            .scheduler_default_rowheader_inner
            {
                border-right: 1px solid #ccc;
            }
            .scheduler_default_rowheadercol2
            {
                background: #ffffff;
            }
            .scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner
            {
                top: 2px;
                bottom: 2px;
                left: 2px;
                background-color: transparent;
                border-left: 5px solid #1a9d13; /* status: "free" (default), green color */
                border-right: 0px none;
            }
            .status_dirty.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner
            {
                border-left: 5px solid #ea3624; /* status: "dirty", red color */
            }
            .status_cleanup.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner
            {
                    border-left: 5px solid #f9ba25; /* status: "cleanup", orange color */
            }
			
			.button {
				display: block;
				width: 100px;
				height: 10px;
				background: #4E9CAF;
				padding: 10px;
				text-align: center;
				border-radius: 5px;
				color: white;
				font-weight: bold;
			}

        </style>

    </head>
    <body>
        <script src="js/jquery-1.11.2.min.js"></script>
        <script src="js/angular.min.js"></script>
        <script src="js/daypilot/daypilot-all.min.js"></script>

        <?php require_once '_header.php'; ?>

        <div class="main">

            <?php require_once '_navigation.php'; ?>

            <div ng-app="main" ng-controller="DemoCtrl" >

              <div style="float:left; width:160px">
                    <daypilot-navigator id="navigator" daypilot-config="navigatorConfig"></daypilot-navigator>
                </div>
                <div style="margin-left: 160px">
					<!-- Fix for ENI-24. UNCOMMENT THE BELOW LINE-->
                    <!-- <div class="space options" style="font-weight:bold"> -->
					<!-- Comment the below line while fixing ENI-24-->
                    <div class="space options"> 
                        Room Filter:
					<!-- Fix for ENI-25. UNCOMMENT THE BELOW LINE-->
					<!-- <select ng-model="roomType" style="background-color:#f2f2f2"> -->
					<!-- Comment the below line while fixing ENI-25-->
						<select ng-model="roomType">
                            <option value="0">All</option>
                            <option value="1">Single</option>
                            <option value="2">Double</option>
                            <option value="4">Family</option>
						</select>
                    </div>
					
                    <daypilot-scheduler id="scheduler" daypilot-config="schedulerConfig" daypilot-events="events" ></daypilot-scheduler>

                    <div class="space">
                        <!-- Fix for ENI-2. UNCOMMENT THE BELOW LINE-->
						<!--  <a class="button" href="" id="add-room" ng-click="addRoom()">Add room</a> -->
						
						<!-- Comment the below line while fixing ENI-2-->
						<a href="" id="add-room" ng-click="addRoom()">create room</a>
						
						<!-- Fix for ENI-4. UNCOMMENT THE BELOW 2 LINES -->
						<!-- <br> -->
						<!-- <a class="button" href="" onclick="myFunction()">Room tariff</a> -->

                    </div>

                </div>

            </div>

            <script>
                var dp;

                var app = angular.module('main', ['daypilot']).controller('DemoCtrl', function($scope, $timeout, $http) {

                    $scope.roomType = 0;

                    $scope.$watch("roomType", function() {
                        loadResources();
                    });

                    $scope.navigatorConfig = {
                        selectMode: "month",
                        showMonths: 3,
                        skipMonths: 3,
                        onTimeRangeSelected: function(args) {
                            if ($scope.scheduler.visibleStart().getDatePart() <= args.day && args.day < $scope.scheduler.visibleEnd()) {
                                $scope.scheduler.scrollTo(args.day, "fast");  // just scroll
                            }
                            else {
                                loadEvents(args.day);  // reload and scroll
                            }
                        }
                    };

                    $scope.schedulerConfig = {
                        visible: false, // will be displayed after loading the resources
                        scale: "Manual",
                        timeline: getTimeline(),
                        timeHeaders: [ { groupBy: "Month", format: "MMMM yyyy" }, { groupBy: "Day", format: "d" } ],
                        eventDeleteHandling: "Update",
                        allowEventOverlap: false,
                        cellWidthSpec: "Auto",
                        eventHeight: 50,
                        rowHeaderColumns: [
                            {title: "Room", width: 80},
                            {title: "Capacity", width: 80},
                            {title: "Status", width: 80}
                        ],
                        onBeforeResHeaderRender: function(args) {
                            var beds = function(count) {
                                return count + " bed" + (count > 1 ? "s" : "");
                            };

                            args.resource.columns[0].html = beds(args.resource.capacity);
                            args.resource.columns[1].html = args.resource.status;
                            switch (args.resource.status) {
                                case "Dirty":
                                    args.resource.cssClass = "status_dirty";
                                    break;
                                case "Cleanup":
                                    args.resource.cssClass = "status_cleanup";
                                    break;
                            }
                            args.resource.areas = [{
                                top:3,
                                right:4,
                                height:14,
                                width:14,
                                action:"JavaScript",
                                js: function(r) {
                                    var modal = new DayPilot.Modal();
                                    modal.onClosed = function(args) {
                                        loadResources();
                                    };
                                    modal.showUrl("room_edit.php?id=" + r.id);
                                },
                                v:"Hover",
                                css:"icon icon-edit",
                            }];
                        },
                        onEventMoved: function (args) {
                            $http.post("backend_move.php", {
                                id: args.e.id(),
                                newStart: args.newStart.toString(),
                                newEnd: args.newEnd.toString(),
                                newResource: args.newResource
                            }).then(function(response) {
                                dp.message(response.data.message);
                            });
                        },
                        onEventResized: function (args) {
                            $http.post("backend_resize.php", {
                                id: args.e.id(),
                                newStart: args.newStart.toString(),
                                newEnd: args.newEnd.toString()
                            }).then(function() {
                                dp.message("Resized.");
                            });
                        },
                        onEventDeleted: function(args) {
                            $http.post("backend_delete.php", {
                                id: args.e.id()
                            }).then(function() {
                                dp.message("Deleted.");
                            });
                        },
                        onTimeRangeSelected: function (args) {
                            var modal = new DayPilot.Modal();
                            modal.closed = function() {
                                dp.clearSelection();

                                // reload all events
                                var data = this.result;
                                if (data && data.result === "OK") {
                                    loadEvents();
                                }
                            };
                            modal.showUrl("new.php?start=" + args.start + "&end=" + args.end + "&resource=" + args.resource);
                        },
                        onEventClick: function(args) {
                            var modal = new DayPilot.Modal();
                            modal.closed = function() {
                                // reload all events
                                var data = this.result;
                                if (data && data.result === "OK") {
                                    loadEvents();
                                }
                            };
                            modal.showUrl("edit.php?id=" + args.e.id());
                        },
                        onBeforeEventRender: function(args) {
                            var start = new DayPilot.Date(args.data.start);
                            var end = new DayPilot.Date(args.data.end);

                            var now = new DayPilot.Date();
                            var today = new DayPilot.Date().getDatePart();
                            var status = "";

                            // customize the reservation bar color and tooltip depending on status
                            switch (args.e.status) {
                                case "New":
                                    var in2days = today.addDays(1);

                                    if (start < in2days) {
                                        args.data.barColor = 'red';
                                        status = 'Expired (not confirmed in time)';
                                    }
                                    else {
                                        args.data.barColor = 'orange';
                                        status = 'New';
                                    }
                                    break;
                                case "Confirmed":
                                    var arrivalDeadline = today.addHours(18);

                                    if (start < today || (start === today && now > arrivalDeadline)) { // must arrive before 6 pm
                                        args.data.barColor = "#f41616";  // red
                                        status = 'Late arrival';
                                    }
                                    else {
                                        args.data.barColor = "green";
                                        status = "Confirmed";
                                    }
                                    break;
                                case 'Arrived': // arrived
                                    var checkoutDeadline = today.addHours(10);

                                    if (end < today || (end === today && now > checkoutDeadline)) { // must checkout before 10 am
                                        args.data.barColor = "#f41616";  // red
                                        status = "Late checkout";
                                    }
                                    else
                                    {
                                        args.data.barColor = "#1691f4";  // blue
                                        status = "Arrived";
                                    }
                                    break;
                                case 'CheckedOut': // checked out
                                    args.data.barColor = "gray";
                                    status = "Checked out";
                                    break;
                                default:
                                    status = "Unexpected state";
                                    break;
                            }

                            // customize the reservation HTML: text, start and end dates
                            args.data.html = args.data.text + " (" + start.toString("M/d/yyyy") + " - " + end.toString("M/d/yyyy") + ")" + "<br /><span style='color:gray'>" + status + "</span>";

                            // reservation tooltip that appears on hover - displays the status text
                            args.e.toolTip = status;

                            // add a bar highlighting how much has been paid already (using an "active area")
                            var paid = args.e.paid;
                            var paidColor = "#aaaaaa";
                            args.data.areas = [
                                { bottom: 10, right: 4, html: "<div style='color:" + paidColor + "; font-size: 8pt;'>Paid: " + paid + "%</div>", v: "Visible"},
                                { left: 4, bottom: 8, right: 4, height: 2, html: "<div style='background-color:" + paidColor + "; height: 100%; width:" + paid + "%'></div>" }
                            ];

                        }
                    };

                    $scope.addRoom = function() {
                        var modal = new DayPilot.Modal();
                        modal.onClosed = function(args) {
                            loadResources();
                        };
                        modal.showUrl("room_new.php");
                    };

                    $timeout(function() {
                        dp = $scope.scheduler;  // debug
                        loadEvents(DayPilot.Date.today());
                    });

                    // loads events; switches the Scheduler visible range if "day" supplied
                    function loadEvents(day) {
                        var from = $scope.scheduler.visibleStart();
                        var to = $scope.scheduler.visibleEnd();
                        if (day) {
                            from = new DayPilot.Date(day).firstDayOfMonth();
                            to = from.addMonths(1);
                        }

                        var params = {
                            start: from.toString(),
                            end: to.toString()
                        };

                        $http.post("backend_events.php", params).then(function(response) {
                            if (day) {
                                $scope.schedulerConfig.timeline = getTimeline(day);
                                $scope.schedulerConfig.scrollTo = day;
                                $scope.schedulerConfig.scrollToAnimated = "fast";
                                $scope.schedulerConfig.scrollToPosition = "left";
                            }
                            $scope.events = response.data;
                        });
                    }

                    function loadResources() {
                        var params = {
                            capacity: $scope.roomType
                        };
                        $http.post("backend_rooms.php", params).then(function(response) {
                            $scope.schedulerConfig.resources = response.data;
                            $scope.schedulerConfig.visible = true;
                        });
                    }

                    function getTimeline(date) {
                        var date = date || DayPilot.Date.today();
                        var start = new DayPilot.Date(date).firstDayOfMonth();
                        var days = start.daysInMonth();

                        var timeline = [];

                        var checkin = 12;
                        var checkout = 12;

                        for (var i = 0; i < days; i++) {
                            var day = start.addDays(i);
                            timeline.push({start: day.addHours(checkin), end: day.addDays(1).addHours(checkout) });
                        }

                        return timeline;
                    }


                });

            </script>

        </div>
        <div class="clear">
        </div>
    </body>
</html>
