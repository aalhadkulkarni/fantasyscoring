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
    <script src="js/updatelb.js"></script>
</head>
<body>

<script>
    window.scoringReady = function () {
        getMatchData(matchNo);
    }
</script>


<select id="MoM" class = "control">
    <option value disabled selected>Select MoM</option>
</select>

<select id="hattrick" class = "control">
    <option value disabled selected>Select Hattrick</option>
</select>

<select id="teams" class = "control">
    <option value disabled selected>Select Winning Team</option>
</select>

<button onclick="calculateAndDisplayScores()" class = "control">Calculate and display scores</button>

<button onclick="updateScoresInDb()" class = "control">Save scores in db</button>

<script>
    $(".control").hide();
</script>

</body>
</html>