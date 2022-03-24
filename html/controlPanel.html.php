<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scoring</title>

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <script src="js/jquery.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/4.12.1/firebase.js"></script>
    <script src="js/firebaseinit.js"></script>
    <script src="js/updatelb.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <style>
        .table-responsive
        {
            max-height: 300px;
        }
    </style>
</head>
<body>

<script>
    takeBackup();
    let globalTeams = {}, teams = {};
    let type, name, startMatchId, matchCount;
    let done = 0;
    let matches = {};

    function refreshTournament() {
        getMultiDataFromFirebase(["tournaments/1", "matches"], function (tournament, _matches) {
            matches = _matches;
            type = tournament.type;
            name = tournament.name;
            startMatchId = tournament.startMatchId;
            matchCount = tournament.matchCount;

            console.log(matches);
            console.log(type);
            console.log(name);
            console.log(startMatchId);
            console.log(matchCount);
            startTournament();
            alert("Matches refreshed. Check site for updated data.");
        });
    }

    function startLeague() {
        let transfers = parseInt($("#transfers").val());
        if (isNaN(transfers)) {
            alert("Number taak re chutya");
            return;
        }
        if (!confirm("This will reset all transfers to " + transfers + " and disable league joining. Are you sure you want to continue?")) {
            return;
        }
        getDataFromFirebase("fantasyTeams", function (fantasyTeams) {
            for (let id in fantasyTeams) {
                fantasyTeams[id].transfersRemaining = transfers;
                fantasyTeams[id].ccRemaining = transfers;
                fantasyTeams[id].vccRemaining = transfers;
            }
            database.ref("fantasyTeams").set(fantasyTeams);
            database.ref("leagueJoiningCodes/JSRM").set("-1");
            console.log(fantasyTeams);
            alert("All transfers set to " + transfers)
        });
    }

    function startTournament() {
        type = type || $("#tournament_type").val();
        name = name || $("#tournament_name").val();
        startMatchId = parseInt(startMatchId || $("#start_match_id").val());
        matchCount = parseInt(matchCount || $("#match_count").val());

        for (let i = 0; i < matchCount; i++) {
            getMatchData(startMatchId + i);
        }
    }

    function getMatchData(matchId) {
        console.log(matchId);
//         $.get("https://cricketapi.platform.iplt20.com//fixtures/" + matchId + "/scoring").done(function (data) {
//             processMatchData(matchId, data);
//         });
        processMatchData(matchId, {
            matchIndo: {
                teams: {[
                    id: 545,
                        fullName: 'TBC',
                        abbreviation: 'TBC',
                        type: 'IPL',
                ], [
                    id: 545,
                        
                        fullName: 'TBC',
                        abbreviation: 'TBC',
                        type: 'IPL',
                ]},
                matchDate: '2022-03-26T19:30:00+0530'
            };
        });
    }

    function processMatchData(scoringId, match) {
        let matchInfo = match.matchInfo,
            matchDate = matchInfo.matchDate,
            teams = matchInfo.teams;
        addTeam(teams[0].team);
        addTeam(teams[1].team);

        let matchId = scoringId - startMatchId + 1;
        matches[matchId] = matches[matchId] || {
            id: matchId,
            startTime: matchDate,
            team1Id: teams[0].team.id,
            team2Id: teams[1].team.id,
            tournamentId: 1,
            scoringId: scoringId,
        };
        matches[matchId].team1Id = teams[0].team.id;
        matches[matchId].team2Id = teams[1].team.id;
        done++;

        if (done === matchCount) {
            alert("New tournament added");
            finish();
        }
    }

    function finish() {
        let tournamentTeams = [];
        for (let id in teams) {
            tournamentTeams.push(id);
        }
        let tournamentMatches = [];
        for (let id in matches) {
            tournamentMatches.push(id);
        }
        let tournament = {
            id: 1,
            name: name,
            type: type,
            startDate: matches[1].startTime.substr(0, 10),
            teams: tournamentTeams,
            nextMatch: 1,
            format: "T20",
            matches: tournamentMatches,
            startMatchId: startMatchId,
            matchCount: matchCount,
        };

        console.log(tournament);
        database.ref("tournaments/1").set(tournament);
        database.ref("matches").set(matches);
    }

    function addTeam(team) {
        let teamObj = {
            id: team.id,
            fullName: team.fullName,
            type: type,
            shortName: team.abbreviation,
        };
        if (!globalTeams[team.id]) {
            globalTeams[team.id] = teamObj;
            database.ref("teams/" + team.id).set(globalTeams[team.id]);
            database.ref("teams_global/" + team.id).set(globalTeams[team.id]);
        }
        if (!teams[team.id]) {
            teams[team.id] = teamObj;
        }
    }

    getMultiDataFromFirebase(["teams_global"], function (_globalTeams) {
        globalTeams = _globalTeams;
    });
</script>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info"></div>
        </div>
    </div>
</div>
<div class="container">

    <div class="row">
        <div class="col-sm-3">

        </div>
        <div class="col-sm-6">
            <div class="panel panel-info">
                <div class="panel panel-heading">
                    Enter tournament data <br/>
                    <h4>This will overwrite any existing tournament</h4>
                </div>
                <div class="panel panel-body">
                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th>Tournament type</th>
                            <th>Tournament name</th>
                            <th>Start match id</th>
                            <th>Number of matches</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <select id="tournament_type" class="form-control" name="tournament_type">
                                    <option name="intl">International</option>
                                    <option name="ipl">IPL</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="tournament_name" class="form-control" name="tournament_name" />
                            </td>
                            <td>
                                <input type="text" id="start_match_id" class="form-control" name="start_match_id" />
                            </td>
                            <td>
                                <input type="text" id="match_count" class="form-control" name="match_count" />
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4">
                                <input onclick="startTournament()" type="button" name="submit" value="Start tournament" class="form-control btn-info" />
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-3">

        </div>
    </div>

</div>

<div class="container">

    <div class="row">
        <div class="col-sm-3">

        </div>
        <div class="col-sm-6">
            <div class="panel panel-info">
                <div class="panel panel-heading">
                    Refresh existing tournament <br/>
                    To get data of new matches - to be performed at the end of each round
                </div>
                <div class="panel panel-body">
                    <button class="btn btn-info form-control" onclick="refreshTournament()">Refresh</button>
                </div>
            </div>
        </div>
        <div class="col-sm-3">

        </div>
    </div>

</div>

<div class="container">

    <div class="row">
        <div class="col-sm-3">

        </div>
        <div class="col-sm-6">
            <div class="panel panel-info">
                <div class="panel panel-heading">
                    Enter number of transfers, cc, vvc <br/>
                    All teams's transfers will be reset to this number
                </div>
                <div class="panel panel-body">
                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th>
                                <input type="text" id="transfers" class="form-control" />
                            </th>
                            <th>
                                <input onclick="startLeague()" type="button" value="Start league" class="form-control btn-info" />
                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-3">

        </div>
    </div>

</div>
</body>
</html>
