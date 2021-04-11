<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scoring</title>

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <script src="js/jquery.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/4.12.1/firebase.js"></script>
    <script src="js/firebaseinit.js"></script>
</head>
<body>

<script>

    function resetScores(fantasyTeams, players, matches) {
        for (var i = 1; i < matches.length; i++) {
            matches[i].totalPointsScored = 0;
        }
        for (var id in fantasyTeams) {
            var fantasyTeam = fantasyTeams[id];
            if (!fantasyTeam) {
                continue;
            }
            fantasyTeam.gwScore = 0;
            fantasyTeam.points = 0;
            for (var playerId in fantasyTeam.playerPoints) {
                fantasyTeam.playerPoints[playerId] = 0;
            }
            fantasyTeam.matchStates = [];
        }
        for (var id in players) {
            var player = players[id];
            if (player) {
                player.points = 0;
                player.scores = [];
            }
        }
        console.log(fantasyTeams);
        console.log(players);
        console.log(matches);
        database.ref("fantasyTeams").set(fantasyTeams);
        database.ref("players").set(players);
        database.ref("matches").set(matches);
    }

    if (confirm("Are you sure you want to reset scores?")) {
        getMultiDataFromFirebase(["fantasyTeams", "players", "matches"], resetScores);
    }

</script>

</body>
</html>