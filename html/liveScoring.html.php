<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scoring</title>

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <script src="js/jquery.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/4.12.1/firebase.js"></script>
    <script src="js/firebaseinit.js"></script>
    <script src="js/scoring.js"></script>
</head>
<body>

<script>
    var allTeams = {};
    window.matchDataReady = function () {
        calculateAndDisplayScores();
    };

    window.scoringReady = function () {
        getAllTeams();

        function getAllTeams() {
            database.ref('teams')
                .once('value')
                .then(function (data) {
                    allTeams = data.val();
                    getMatches();
                });
        }
    };

    function getMatches() {
        database.ref('matches')
            .once('value')
            .then(function (data) {
                var matches = data.val();
                for (var i = 1; i < matches.length; i++) {
                    var match = matches[i];
                    var matchName = i + ". ";
                    matchName += allTeams[match.team1Id].shortName + " x " + allTeams[match.team2Id].shortName + " - ";
                    matchName += match.startTime.substr(0, 10);
                    var option = $("<option value='" + i + "'>" + matchName + "</option>");
                    $("#matches").append(option);
                }
                $("#matches").val(matchNo);
                $(".control").show();
                $("#wait").hide();
            });
    }

    function matchSelected() {
        $("#calc").hide();
        getMatchData($("#matches").val());
    }

</script>

<select id="matches" class = "control">
    <option value disabled selected>Select match</option>
</select>

<button onclick="matchSelected()" class = "control" id="calc">Calculate and display scores</button>

<div id="wait">
    Please wait. System is fetching data
</div>

<!--<select id="MoM" class = "control">-->
<!--    <option value disabled selected>Select MoM</option>-->
<!--</select>-->
<!---->
<!--<select id="hattrick" class = "control">-->
<!--    <option value disabled selected>Select Hattrick</option>-->
<!--</select>-->
<!---->
<!--<select id="teams" class = "control">-->
<!--    <option value disabled selected>Select Winning Team</option>-->
<!--</select>-->

<script>
    $(".control").hide();
</script>

</body>
</html>
