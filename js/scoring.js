var matchNo = null;
var winningTeamId = null, losingTeamId = null;
var playerScores = {};
var matchObj = null;
var teams = [];
var fieldingCounts = {};
var playerTeams = {};
var playerNames = {};
var fantasyTeams = {};
var fantasyPlayers = {};
var jackpotScore = 0;
var scoringDetails = {};
var MoMId = null;
var hattrickId = null;

window.matchDataReady = function () {
    $(".control").show();
};

function onScoring(iplMatch) {
    matchObj = iplMatch;
    setTeamsAndPlayers();

    var team1Opt = $("<option value = '" + teams[0] + "'>" + matchObj.matchInfo.teams[0].team.fullName + "</option>");
    var team2Opt = $("<option value = '" + teams[1] + "'>" + matchObj.matchInfo.teams[1].team.fullName + "</option>");
    var teamSelect = $("#teams");
    teamSelect.append(team1Opt);
    teamSelect.append(team2Opt);
    for(var i in playerTeams) {
        var option1 = $("<option value = '" + i + "'>" + playerNames[i] + "</option>");
        var option2 = $("<option value = '" + i + "'>" + playerNames[i] + "</option>");
        var MoMSelect = $("#MoM");
        MoMSelect.append(option1);
        var hattrickSelect = $("#hattrick");
        hattrickSelect.append(option2);
    }
    window.matchDataReady();
}

function getMiscData() {
    MoMId = $("#MoM").val();
    hattrickId = $("#hattrick").val();
    winningTeamId = $("#teams").val();
}

function calculateAndDisplayScores() {
    getMiscData();
    database.ref("rules")
        .once("value")
        .then(function (data) {
            rules = data.val;
            calculateScores();
            displayScores();
        });
}

function printPlayerScore(playerId) {
    var string = "";
    string += ("<b>" + playerNames[playerId] + "</b><br>");
    var scoringDetail = scoringDetails[playerId];
    for(var i in scoringDetail) {
        string += (scoringDetail[i] + "<br>");
    }
    string += "Total: " + playerScores[playerId] + "<br>";
    string += "<br>";
    var p = document.createElement("p");
    p.innerHTML = string;
    document.body.appendChild(p);
}

function displayScores() {
    for (var playerId in playerScores) {
        console.log(playerNames[playerId] + "( " + playerId + " )" + " : " + playerScores[playerId]);
        printPlayerScore(playerId);
        jackpotScore += playerScores[playerId];
    }
    console.log("JP: " + jackpotScore);
    var p = document.createElement("p");
    p.innerHTML = "JP score: " + (jackpotScore) + "<br>";
    document.body.appendChild(p);
}

var rules = {
    runs: {
        basic: 0,
        ranges: [{
            from: -1,
            to: -1,
            points: 0,
            onlyOnOut: false,
            excludedRoles: []
        }]
    },
    sr: {
        ranges: [{
            from: -1,
            to: -1,
            points: 0,
            onlyOnOut: false,
            excludedRoles: []
        }]
    },
    boundaries: {
        ranges: [{
            from: -1,
            to: -1,
            points: 0,
            onlyOnOut: false,
            excludedRoles: []
        }]
    },
    notOutPoints: 0,
    wickets: {
        basic: 0,
        ranges: [{
            from: -1,
            to: -1,
            points: 0,
            onlyOnOut: false,
            excludedRoles: []
        }]
    },
    maidens: {
        basic: 0
    },
    economy: {
        ranges: [{
            from: -1,
            to: -1,
            points: 0,
            onlyOnOut: false,
            excludedRoles: []
        }]
    },
    fielding: {
        basic: 0,
        ranges: [{
            from: -1,
            to: -1,
            points: 0,
            onlyOnOut: false,
            excludedRoles: []
        }]
    },
    winningTeam: 0,
    losingTeam: 0,
    MoM: 0,
    hattrick: 0
};

function applyBasicRule(rule, value) {
    return value * rule.basic;
}

function isApplicable(rule, value, reverse) {
    if (reverse) {
        if ((value <= range.to && (range.from === undefined || value > range.to))) {
            return true;
        }
        if (value > range.from && range.to === undefined) {
            return true;
        }
    } else {
        if (value >= ranges.from && (range.to === undefined || value < ranges.to)) {
            return true;
        }
    }
    return false;
}

function applyRangeRule(rule, value, role, notOut, reverse) {
    var ranges = rule.ranges;
    for (var i = 0; i < ranges.length; i++) {
        var range = ranges[i];
        if (isApplicable(rule, value, reverse)) {
            if (range.onlyOnOut && !notOut) {
                continue;
            }
            if (role && range.excludedRoles && range.excludedRoles[role]) {
                continue;
            }
            return ranges.points;
        }
    }
    return 0;
}

function BattingScoring(runs, balls, sr, boundaries, notOut, role) {
    var points = 0, descriptions = [];
    function addPointsForRuns() {
        var runsBasicPoints = applyBasicRule(rules.runs, runs);
        var runsBonus = applyRangeRule(rules.runs, runs, role, notOut);

        var runsPoints = runsBasicPoints + runsBonus;

        if (runsBonus != 0) {
            descriptions.push("Runs: " + runs + " - Points: " + (runsPoints) + " (" + runsBasicPoints + " + " + runsBonus + ")");
        } else {
            descriptions.push("Runs: " + runs + " - Points: " + runs);
        }
        points += runsPoints;
    }

    function addPointsForSr() {
        var srBonus = applyRangeRule(roles.sr, sr, role, notOut);
        if (srBonus != 0) {
            descriptions.push("Batting Strike Rate: " + sr + " - Points: " + srBonus);
        }
        points += srBonus;
    }

    function addPointsForBoundaries() {
        var bBonus = applyRangeRule(rules.boundaries, boundaries);
        if (bBonus != 0) {
            descriptions.push("Fours and Sixes: " + boundaries + " - Points: " + bBonus);
        }
        points += bBonus;
    }

    function addPointsForNotOut() {
        var notOutPoints = 0;
        if (notOut) {
            notOutPoints += rules.notOutPoints;
            descriptions.push("Not Out - Points: " + notOutPoints);
        }
        points += notOutPoints;
    }

    this.getResult = function () {
        addPointsForRuns();
        addPointsForSr();
        addPointsForBoundaries();
        addPointsForNotOut();
        return {
            points: parseFloat(points),
            descriptions: descriptions
        };
    }
}

function addDescriptions(playerId, descriptions) {
    for (var i = 0; i < descriptions.length; i++) {
        scoringDetails[playerId].push(descriptions[i]);
    }
}

function BowlingScoring(wickets, dots, maidens, economy) {
    var points = 0, descriptions = [];

    function addPointsForWickets() {
        var wicketsBasicPoints = applyBasicRule(rules.wickets, wickets);
        var wBonus = applyRangeRule(rules.wickets, wickets);

        var wicketsPoints = wicketsBasicPoints + wBonus;

        if (wBonus != 0) {
            descriptions.push("Wickets: " + wickets + " - Points: " + (wicketsPoints) + " (" + (wicketsBasicPoints) + " + " + wBonus + ")");
        } else {
            descriptions.push("Wickets: " + wickets + " - Points: " + (wicketsBasicPoints));
        }

        points += wicketsPoints;
    }

    function addPointsForDots() {
        var dotsBasicPoints = applyBasicRule(rules.dots, dots),
            dotsBonus = applyRangeRule(rules.dots, dots);

        var dotsPoints = dotsBasicPoints + dotsBonus;

        if (dotsBonus != 0) {
            descriptions.push("Dots: " + dots + " - Points: " + (dotsPoints) + " (" + (dotsBasicPoints) + " + " + dotsBonus + ")");
        } else {
            descriptions.push("Dots: " + dots + " - Points: " + (dotsBasicPoints));
        }

        points += dotsPoints;
    }

    function addPointsForMaidens() {
        var maidensBonus = applyBasicRule(rule.maidens, maidens);
        if (maidensBonus > 0) {
            descriptions.push("Maidens: " + maidens + " - Points: " + (maidensBonus));
        }
        points += maidensBonus;
    }

    function addPointsForEconomy() {
        var eBonus = applyRangeRule(rule.economy, economy, null, false, true);

        if (eBonus != 0) {
            descriptions.push("Economy: " + economy + " - Points: " + eBonus);
        }

        points += eBonus;
    }

    this.getResult = function () {
        addPointsForWickets();
        addPointsForDots();
        addPointsForMaidens();
        addPointsForEconomy();
        return {
            points: parseFloat(points),
            descriptions: descriptions
        }
    }
}

function FieldingScoring(fieldingCount) {
    var points = 0, descriptions = [];
    function addPointsForFielding() {
        var fieldingBasicPoints = applyBasicRule(rules.fielding, fieldingCount),
            fBonus = applyRangeRule(rules.fielding, fieldingCount);

        var fieldingPoints = fieldingBasicPoints + fBonus;

        if (fieldingPoints != 0) {
            if (fBonus != 0) {
                descriptions.push("Catches / Run Outs / Stumpings: " + fieldingCount + " - Points: " + (fieldingPoints) + " (" + (fieldingBasicPoints) + " + " + fBonus + ")");
            } else {
                descriptions.push("Catches / Run Outs / Stumpings: " + fieldingCount + " - Points: " + fieldingPoints);
            }
        }
        points += fieldingPoints;
    }
    this.getResult = function () {
        addPointsForFielding();
        return {
            points: parseFloat(points),
            descriptions: descriptions
        };
    }
}

function MatchResultScoring(teamId, winningTeamId) {
    var points = 0, descriptions = [];
    function addPointsForWinLoss() {
        var winLossPoints = 0;
        if (teamId == winningTeamId) {
            winLossPoints += rules.winningTeam;
            descriptions.push("Winning team - Points: " + winLossPoints);
        } else if (teamId == losingTeamId) {
            winLossPoints -= rules.losingTeam;
            descriptions.push("Losing team - Points: " + winLossPoints);
        }
        points += winLossPoints;
    }

    this.getResult = function () {
        addPointsForWinLoss();
        return {
            points: parseFloat(points),
            descriptions: descriptions
        };
    }
}

function MoMScoring() {
    var points = 0, descriptions = [];
    function addPointsForMoM() {
        points = rules.MoM;
        descriptions.push("Man of the Match - Points: 100");
    }

    this.getResult = function () {
        addPointsForMoM();
        return {
            points: parseFloat(points),
            descriptions: descriptions
        };
    }
}

function HattrickScoring() {
    var points = 0, descriptions = [];
    function addPointsForHattrick() {
        points = rules.hattrick;
        descriptions.push("Hattrick - Points: 200");
    }

    this.getResult = function () {
        addPointsForHattrick();
        return {
            points: parseFloat(points),
            descriptions: descriptions
        };
    }
}

function calculateScores() {
    var innings = matchObj.innings;
    for (var i in innings) {
        var scorecard = innings[i].scorecard;
        var battingStats = scorecard.battingStats;
        var bowlingStats = scorecard.bowlingStats;
        for (var j in battingStats) {
            var battingStat = battingStats[j];
            var playerId = battingStat.playerId;
            var balls = battingStat.b || 0;
            var runs = battingStat.r || 0;
            var sr = battingStat.sr || 0;
            var boundaries = (battingStat["4s"] || 0) + (battingStat["6s"] || 0);
            var mod = battingStat.mod;
            var notOut = (mod == undefined) || (mod.out === false);

            if (fantasyPlayers[playerId] === undefined) {
                fantasyPlayers[playerId] = {};
            }

            var battingScoring = new BattingScoring(runs, balls, sr, boundaries, notOut, fantasyPlayers[playerId].role);
            var battingResult = battingScoring.getResult();
            console.log(playerNames[playerId], battingResult.points);
            playerScores[playerId] += battingResult.points;
            addDescriptions(playerId, battingResult.descriptions);

            //add fielding counts from dismissals
            if (mod && mod.additionalPlayerIds) {
                for (var k in mod.additionalPlayerIds) {
                    fieldingCounts[mod.additionalPlayerIds[k]] = fieldingCounts[mod.additionalPlayerIds[k]] || 0;
                    fieldingCounts[mod.additionalPlayerIds[k]]++;
                }
            }
        }
        for (var j in bowlingStats) {
            var bowlingStat = bowlingStats[j];
            var playerId = bowlingStat.playerId;

            var wickets = bowlingStat.w || 0;
            var dots = bowlingStat.d || 0;
            var maidens = bowlingStat.maid || 0;
            var economy = bowlingStat.e;

            var bowlingScoring = new BowlingScoring(wickets, dots, maidens, economy);
            var bowlingResult = bowlingScoring.getResult();
            console.log(playerNames[playerId], bowlingResult.points);
            playerScores[playerId] += bowlingResult.points;
            addDescriptions(playerId, bowlingResult.descriptions);
        }
    }

    for(var playerId in fieldingCounts) {
        var fieldingCount = fieldingCounts[playerId] || 0;
        var fieldingScoring = new FieldingScoring(fieldingCount);
        var fieldingResult = fieldingScoring.getResult();
        playerScores[playerId] += fieldingResult.points;
        addDescriptions(playerId, fieldingResult.descriptions);
    }

    //winning or losing team
    for(var playerId in playerScores) {
        var teamId = playerTeams[playerId];
        var matchResultScoring = new MatchResultScoring(teamId, winningTeamId);
        var matchResultResult = matchResultScoring.getResult();
        playerScores[playerId].points += matchResultResult.points;
        addDescriptions(playerId, matchResultResult.descriptions);
    }

    if (MoMId) {
        var moMScoring = new MoMScoring();
        var moMResult = moMScoring.getResult();
        playerScores[MoMId] += moMResult.points;
        addDescriptions(MoMId, moMResult.descriptions);
    }
    if (hattrickId) {
        var hattrickScoring = new HattrickScoring();
        var hattrickResult = hattrickScoring.getResult();
        playerScores[hattrickId] += hattrickResult.points;
        addDescriptions(hattrickId, hattrickResult.descriptions);
    }
}

function setTeamsAndPlayers() {
    var matchTeams = matchObj.matchInfo.teams;
    for(var i in matchTeams) {
        var matchTeam = matchTeams[i];
        teams.push(matchTeam.team.id);
        var players = matchTeam.players;
        for(var j in players) {
            scoringDetails[players[j].id] = [];
            playerScores[players[j].id] = 0;
            playerTeams[players[j].id] = matchTeam.team.id;
            playerNames[players[j].id] = players[j].fullName;
        }
    }
}

function getMatchData(matchId) {
    console.log(matchId);
    var matchNoString = matchId.toString();
    if (matchNoString.length == 1) {
        matchNoString = "0" + "" + matchNoString;
    }
    var head = document.getElementsByTagName("head")[0];
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.async = true;
    script.src = "https://datacdn.iplt20.com/dynamic/data/core/cricket/2012/ipl2020/ipl2020-" + matchNoString + "/scoring.js";
    document.getElementsByTagName('head')[0].appendChild(script);
}

getFirebaseData();

function getFirebaseData() {
    getNextMatchId()
}

function getNextMatchId() {
    database.ref('tournaments/1/nextMatch')
        .once('value')
        .then(function (data) {
            matchNo = data.val();
            getFantasyTeams();
        });
}

function getFantasyTeams() {
    database.ref('fantasyTeams')
        .once('value')
        .then(function (data) {
            fantasyTeams = data.val();
            getFantasyPlayers();
        });
}

function getFantasyPlayers() {
    database.ref('players')
        .once('value')
        .then(function (data) {
            fantasyPlayers = data.val();
            window.scoringReady();
        });
}