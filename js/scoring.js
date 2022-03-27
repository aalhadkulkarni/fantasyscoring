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
var iplTeams = {};
var iplPlayers = {};
var jackpotScore = 0;
var scoringDetails = {};
var MoMId = null;
var hattrickId = null;
var matchFromDB = {};
var playersInDb = {};
var teamsInDb = {};
var newPlayers = {};
var playerNameMap = {};

window.matchDataReady = function () {
    $(".control").show();
};

function onActualScoring(iplMatch) {
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
            rules = data.val();
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
    jackpotScore = round(jackpotScore);
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
        }],
        multipliers: {}
    },
    sr: {
        ranges: [{
            from: -1,
            to: -1,
            points: 0,
            onlyOnOut: false,
            excludedRoles: []
        }],
        inactive: 1
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

function applyBasicRule(rule, value, multipliers) {
    value = parseFloat(value);
    rule.basic = parseFloat(rule.basic);
    let basic = value * rule.basic;
    multipliers = multipliers || {};
    rule.multipliers = rule.multipliers || [];
    for (let i = 0; i < rule.multipliers.length; i++) {
        let multiplier = rule.multipliers[i];
        if (!isNaN(multipliers[multiplier])) {
            basic *= multipliers[multiplier];
        }
    }
    basic = round(basic);
    return basic;
}

function isApplicable(range, value, reverse) {
    if (reverse) {
        if (range.to === undefined) {
            return value > range.from;
        }
        if (range.from === undefined) {
            return value <= range.to;
        }
        return value > range.from && value <= range.to;
    } else {
        if (range.to === undefined) {
            return value >= range.from;
        }
        return value >= range.from && value < range.to;
    }
    return false;
}

function applyRangeRule(rule, value, role, notOut, reverse) {
    if (!rule.inactive) {
        value = parseFloat(value);
        if (!isNaN(value)) {
            var ranges = rule.ranges;
            for (var i = 0; i < ranges.length; i++) {
                var range = ranges[i];
                if (isApplicable(range, value, reverse)) {
                    if (range.onlyOnOut && notOut) {
                        continue;
                    }
                    if (role && range.excludedRoles && range.excludedRoles[role]) {
                        continue;
                    }
                    return range.points;
                }
            }
        }
    }
    return 0;
}

function BattingScoring(runs, balls, sr, boundaries, notOut, role) {
    var points = 0, descriptions = [];
    function addPointsForRuns() {
        let srMultiplier = round(sr, 4);
        var runsBasicPoints = applyBasicRule(rules.runs, runs, {
            sr: srMultiplier
        });
        var runsBonus = applyRangeRule(rules.runs, runs, role, notOut);

        var runsPoints = runsBasicPoints + runsBonus;

        descriptions.push("Runs: " + runs + " - Points: " + (runsBasicPoints) + " (" + runs + " x " + srMultiplier + ")");
        if (runsBonus != 0) {
            descriptions.push("Runs bonus: " + runsBonus);
        }
        points += runsPoints;
    }

    function addPointsForSr() {
        var srBonus = applyRangeRule(rules.sr, sr, role, notOut || (runs == 0 && !notOut));
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
        var maidensBonus = applyBasicRule(rules.maidens, maidens);
        if (maidensBonus > 0) {
            descriptions.push("Maidens: " + maidens + " - Points: " + (maidensBonus));
        }
        points += maidensBonus;
    }

    function addPointsForEconomy() {
        var eBonus = applyRangeRule(rules.economy, economy, null, false, true);

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
            winLossPoints = rules.winningTeam;
            descriptions.push("Winning team - Points: " + winLossPoints);
        } else if (winningTeamId && teamId != winningTeamId) {
            winLossPoints = rules.losingTeam;
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
        console.log("POINTS", points);
        descriptions.push("Hattrick - Points: " + points);
    }

    this.getResult = function () {
        addPointsForHattrick();
        return {
            points: parseFloat(points),
            descriptions: descriptions
        };
    }
}

let modifiedMatchObj = {
    innings: [],
    matchInfo: {
        teams: [],
    },
};

function onScoring(data) {
    if (data.Innings1) {
        addInnings(data. Innings1);
        fetchInnings(2)
    } else if (data.Innings2) {
        addInnings(data.Innings2);
        onActualScoring(modifiedMatchObj);
    }
}

function fetchInnings(inningsNo) {
    let script = document.createElement("script");
    script.src = "https://ipl-stats-sports-mechanic.s3.ap-south-1.amazonaws.com/ipl/feeds/" + (matchNo + 455) + "-Innings" + inningsNo + ".js";
    document.body.appendChild(script);
}

function addInnings(iplInnings) {
    let teamId,
        teamPlayers = [];

    function getPlayerId(iplPlayerId) {
        return iplPlayers[iplPlayerId];
    }

    function getTeamId(iplTeamId) {
        return iplTeams[iplTeamId];
    }

    function getIPLPlayerId(playerName) {
        return playerNameMap[playerName];
    }

    function isCaught(desc) {
        return desc[0] == "c";
    }

    function getCatcher(outDesc) {
        let parts = outDesc.split(" b ");
        return parts[0].replace("c ", "");
    }

    function isStumped(desc) {
        return desc[0] == "st";
    }

    function getKeeper(outDesc) {
        let parts = outDesc.split(" b ");
        return parts[0].replace("st ", "");
    }

    function isRunOut(desc) {
        return desc[0] == "run" && desc[1] == "out";
    }

    function getFielder(outDesc) {
        return outDesc.replace("run out ", "").replace("(", "").replace(")", "");
    }

    let battingStats = [],
        bowlingStats = [];

    let battingCard = iplInnings.BattingCard;
    for (let i = 0; i < battingCard.length; i++) {
        let iplBatsmanStats = battingCard[i];
        let playerId = getPlayerId(iplBatsmanStats.PlayerID);
        teamId = getTeamId(iplBatsmanStats.TeamID);
        teamPlayers.push({
            id: playerId,
            fullName: iplBatsmanStats.PlayerName,
            shortName: iplBatsmanStats.PlayerShortName,
        });

        let r = iplBatsmanStats.Runs,
            b = iplBatsmanStats.Balls,
            sr = iplBatsmanStats.StrikeRate,
            fours = iplBatsmanStats.Fours,
            sixes = iplBatsmanStats.Sixes,
            outDesc = iplBatsmanStats.outDesc,
            notOut = outDesc == "not out";
        let fielderName = "";
        let mod = {};
        if (notOut) {
            mod.out = false;
        } else if (!notOut && outDesc != "") {
            mod.out = true;
            let desc = outDesc.split(" ");
            if (isCaught(desc)) {
                fielderName = getCatcher(outDesc);
            } else if (isStumped(desc)) {
                fielderName = getKeeper(outDesc);
            } else if (isRunOut(desc)) {
                fielderName = getFielder(outDesc);
            }
            battingStat.mod = {

            };
            if (fielderName.length > 0) {
                mod.additionalPlayerIds = [getPlayerId(getIPLPlayerId(fielderName))];
            }
        } else {
            continue;
        }
        battingsStats.push({
            "playerId": playerId,
            "b": b,
            "r": r,
            "sr": sr,
            "4s": fours,
            "6s": sixes,
            "mod": mod,
        });
    }

    let bowlingCard = iplInnings.BowlingCard;
    for (let i = 0; i < bowlingCard.length; i++) {
        let iplBowlerStats = bowlingCard[i],
            playerId = getPlayerId(iplBowlerStats.PlayerID);
            w = iplBowlerStats.Wickets.,
            d = iplBowlerStats.DotBalls,
            maid = iplBowlerStats.Maidens,
            e = iplBowlerStats.Economy;
        bowlingStats.push({
            "w": w,
            "d": d,
            "maid": maid,
            "e": e,
        });
    }

    modifiedMatchObj.innings.push({
        scorecard: {
            battingsStats: battingStats,
            bowlingStats: bowlingStats,
        },
    });
    modifiedMatchObj.matchInfo.teams.push({
        players: teamPlayers,
        team: {
            id: teamId,
            fullName: fantasyTeams[teamId].fullName,
        }
    });
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
            var sr = battingStat.sr;
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

    console.log("HERE1");
    console.log(playerScores);
    //winning or losing team
    for(var playerId in playerScores) {
        var teamId = playerTeams[playerId];
        var matchResultScoring = new MatchResultScoring(teamId, winningTeamId);
        var matchResultResult = matchResultScoring.getResult();
        playerScores[playerId] += matchResultResult.points;
        playerScores[playerId] = round(playerScores[playerId]);
        addDescriptions(playerId, matchResultResult.descriptions);
    }

    if (MoMId) {
        var moMScoring = new MoMScoring();
        var moMResult = moMScoring.getResult();
        playerScores[MoMId] += moMResult.points;
        playerScores[MoMId] = round(playerScores[MoMId]);
        addDescriptions(MoMId, moMResult.descriptions);
    }
    if (hattrickId) {
        var hattrickScoring = new HattrickScoring();
        var hattrickResult = hattrickScoring.getResult();
        playerScores[hattrickId] += hattrickResult.points;
        playerScores[hattrickId] = round(playerScores[hattrickId]);
        addDescriptions(hattrickId, hattrickResult.descriptions);
    }
}

function round(num, digits) {
    digits = digits || 2;
    let x = 1;
    for (let i = 0; i < digits; i++) {
        x *= 10;
    }
    return Math.round(num * 100) / x;
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

function getMatchData(matchNo) {
    console.log(matchNo);
    // var matchNoString = matchId.toString();
    // if (matchNoString.length == 1) {
    //     matchNoString = "0" + "" + matchNoString;
    // }
    // var head = document.getElementsByTagName("head")[0];
    // var script = document.createElement('script');
    // script.type = 'text/javascript';
    // script.async = true;
    // script.src = "https://datacdn.iplt20.com/dynamic/data/core/cricket/2012/ipl2021/ipl2021-" + matchNoString + "/scoring.js";
    // document.getElementsByTagName('head')[0].appendChild(script);
    //
    // if (window.location.href.indexOf("testjsrm") !== -1) {
    //     getMatchFromId(32212 + parseInt(matchNo) - 1);
    // } else {
    //     getMatchFromDB(matchNo, function(matchFromDb) {
    //         getMatchFromId(matchFromDb.scoringId);
    //     });
    // }

    // function getMatchFromId(matchId) {
    //     $.get("https://cricketapi.platform.iplt20.com//fixtures/" + matchId + "/scoring").done(function (data) {
    //         onScoring(data);
    //     });
    // }

    //https://ipl-stats-sports-mechanic.s3.ap-south-1.amazonaws.com/ipl/feeds/456-Innings1.js?onScoring=_jqjsp&_1648321460797=
    fetchInnings(1);
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

function getMatchFromDB(matchId, callback) {
    database.ref('matches/' + matchId)
        .once('value')
        .then(function (data) {
            matchFromDB = data.val();
            callback(matchFromDB);
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
            setIplPlayers();
        });
}

function getTeams() {
    function setIplTeams() {
        for (let i in teamsInDb) {
            iplTeams[teamsInDb[i].newId] = i;
        }
    }
    database.ref("teams")
        .once('value')
        .then(function (data) {
            teamsInDb = data.val();
            setIplTeams();
            getPlayers();
        });
}

function getPlayers() {
    function setIplPlayers() {
        for (let i in playersInDb) {
            iplPlayers[playersInDb[i].newId] = i;
        }
    }
    database.ref("players")
        .once('value')
        .then(function (data) {
            playersInDb = data.val();
            setIplPlayers();
            getNewPlayers();
            window.scoringReady();
        });
}

function getNewPlayers() {
    function setPlayerNameMap() {
        for (let i in newPlayers) {
            playerNameMap[newPlayers[i]] = i;
        }
    }
    database.ref("newPlayers")
        .once('value')
        .then(function (data) {
            newPlayers = data.val();
            setPlayerNameMap();
            window.scoringReady();
        });
}
