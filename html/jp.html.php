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
    var iplTeams = {},
        matches = [];
    window.matchDataReady = function () {
        calculateAndDisplayScores();
    };

    window.scoringReady = function () {
        getIplTeams();

        function getIplTeams() {
            database.ref('teams')
                .once('value')
                .then(function (data) {
                    iplTeams = data.val();
                    getMatches();
                });
        }
    };

    function getMatches() {
        database.ref('matches')
            .once('value')
            .then(function (data) {
                matches = data.val();
                for (var i = 1; i < matches.length; i++) {
                    var match = matches[i];
                    var matchName = i + ". ";
                    matchName += iplTeams[match.team1Id].shortName + " x " + iplTeams[match.team2Id].shortName + " - ";
                    matchName += match.startTime.substr(0, 10);
                    var option = $("<option value='" + i + "'>" + matchName + "</option>");
                    $("#matches").append(option);
                }
                $("#matches").val(matchNo);
                $(".control").show();
                $("#wait").hide();
            });
    }

    function calculateJP() {
        for (let id in fantasyTeams) {
            let team = fantasyTeams[id];
            try {
                if (team.leagueId == 1) {
                    let jpScore = parseFloat(matches[team.jackpotMatchId].totalPointsScored);
                    console.log(team);
                    console.log(team.points);
                    team.points += jpScore;
                    console.log(team.points);
                }
            } catch (e) {
            }
        }
    }

    function calculateCaps() {
        for (let id in fantasyTeams) {
            let team = fantasyTeams[id];
            let orange = getCapPointsFor(team, 5443),
                purple = getCapPointsFor(team, 157);
            console.log(team, orange, purple);
            team.points += (orange + purple);
        }
    }

    function getCapPointsFor(team, playerId) {
        if (team.captain == playerId) {
            return 400;
        }
        if (team.viceCaptain == playerId) {
            return 300;
        }
        let currentPlayers = team.activePlayers;
        for (let i = 0; i < currentPlayers.length; i++) {
            if (currentPlayers[i] == playerId) {
                return 200;
            }
        }
        return 0;
    }

    function fixGW() {
        for (let id in fantasyTeams) {
            let team = fantasyTeams[id];
            let points = team.points,
                prevPoints = team.matchStates[56],
                gwPoints = (points - prevPoints);
            team.gwScore = gwPoints;
        }
    }

    function save() {
        database.ref("fantasyTeams").set(fantasyTeams);
    }
</script>

<button onclick="calculateJP()" class = "control" id="jp">Calculate JP</button>

<button onclick="calculateCaps()" class = "control" id="cap">Calculate orange and purple caps</button>

<button onclick="fixGW()" class = "control" id="gwfix">Fix GW calculation</button>

<button onclick="save()" class = "control" id="save">Save all and finish league</button>

<div id="wait">
    Please wait. System is fetching data
</div>

<script>
    $(".control").hide();
</script>

</body>
</html>