function processFantasyLeagueScores() {
    for (var fantasyTeamId in fantasyTeams) {
        var fantasyTeam = fantasyTeams[fantasyTeamId];
        var prevmatch = matchNo - 1;
        if (prevmatch % 5 == 0) {
            fantasyTeam.gwScore = 0;
        }
        fantasyTeam.playerPoints = fantasyTeam.playerPoints || {};
        var scoringPlayers = fantasyTeam.scoringPlayers;
        var applicableMatchId = 0;
        for(var matchId in scoringPlayers) {
            var mid = parseInt(matchId);
            if (mid>applicableMatchId && mid<=matchNo) {
                if (fantasyTeam.leagueId == 2 && matchNo <= 56) {
                    var curGwM1 = (matchNo - matchNo % 8) + 1;
                    if (curGwM1 > matchNo) {
                        curGwM1 -= 8;
                    }
                    if (mid <= curGwM1) {
                        applicableMatchId = mid;
                    }
                } else {
                    applicableMatchId = mid;
                }
            }
        }
        console.log(fantasyTeam.leagueId, applicableMatchId);
        var playerList = scoringPlayers[applicableMatchId];
        for(var i=0; i<playerList.length; i++) {
            var curPlayerId = playerList[i];
            var curPlayerPoints = playerScores[curPlayerId] || 0;
            if (i==0) {
                if (fantasyTeam.tcMatchId == matchNo && fantasyTeam.leagueId == 1) {
                    curPlayerPoints *= 3;
                } else {
                    curPlayerPoints *= 2;
                }
            } else if (i==1) {
                curPlayerPoints *= 1.5;
            }
            curPlayerPoints = round(curPlayerPoints);
            fantasyTeam.playerPoints[curPlayerId] = fantasyTeam.playerPoints[curPlayerId] || 0;
            fantasyTeam.playerPoints[curPlayerId] += curPlayerPoints;
            fantasyTeam.playerPoints[curPlayerId] = round(fantasyTeam.playerPoints[curPlayerId]);
            fantasyTeam.points += curPlayerPoints;
            fantasyTeam.points = round(fantasyTeam.points);
            fantasyTeam.gwScore += curPlayerPoints;
            fantasyTeam.gwScore = round(fantasyTeam.gwScore);
        }
        fantasyTeam.matchStates = fantasyTeam.matchStates || {};
        fantasyTeam.matchStates[matchNo] = fantasyTeam.points
    }

    for(var playerId in playerScores) {
        if (!fantasyPlayers[playerId] || !fantasyPlayers[playerId].name) {
            continue;
        }
        playerScores[playerId] = round(playerScores[playerId]);
        fantasyPlayers[playerId].scores = fantasyPlayers[playerId].scores || {};
        fantasyPlayers[playerId].scores[matchNo] = playerScores[playerId];
        fantasyPlayers[playerId].points = fantasyPlayers[playerId].points || 0;
        fantasyPlayers[playerId].points += playerScores[playerId];
        fantasyPlayers[playerId].points = round(fantasyPlayers[playerId].points);
    }
}

function updateScoresInDb() {
    takeBackup();
    processFantasyLeagueScores();
    $(".control").hide();
    console.log(fantasyPlayers);
    console.log(fantasyTeams);

    database.ref("matches/" + matchNo + "/totalPointsScored").set(jackpotScore);

    let filteredPlayers = {};
    for (let id in fantasyPlayers) {
        if (fantasyPlayers[id] && fantasyPlayers[id].id == id) {
            filteredPlayers[id] = fantasyPlayers[id];
        }
    }
    console.log(filteredPlayers);
    database.ref("players").set(filteredPlayers);

    database.ref("fantasyTeams").set(fantasyTeams);

    database.ref("tournaments/1/nextMatch").set(matchNo+1);
    alert("Score updated, please check lb");
}