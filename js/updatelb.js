function processFantasyLeagueScores() {
    for (var fantasyTeamId in fantasyTeams) {
        var fantasyTeam = fantasyTeams[fantasyTeamId];
        var prevmatch = matchNo - 1;
        if (prevmatch % 8 == 0 || prevmatch > 56) {
            fantasyTeam.gwScore = 0;
        }
        fantasyTeam.playerPoints = fantasyTeam.playerPoints || {};
        var scoringPlayers = fantasyTeam.scoringPlayers;
        var applicableMatchId = 0;
        for(var matchId in scoringPlayers) {
            var mid = parseInt(matchId);
            if (mid>applicableMatchId && mid<=matchNo) {
                applicableMatchId = mid;
            }
        }
        console.log(applicableMatchId);
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
            fantasyTeam.playerPoints[curPlayerId] = fantasyTeam.playerPoints[curPlayerId] || 0;
            fantasyTeam.playerPoints[curPlayerId] += curPlayerPoints;
            fantasyTeam.points += curPlayerPoints;
            fantasyTeam.gwScore += curPlayerPoints;
        }
        fantasyTeam.matchStates = fantasyTeam.matchStates || {};
        fantasyTeam.matchStates[matchNo] = fantasyTeam.points
    }

    for(var playerId in playerScores) {
        if (!fantasyPlayers[playerId]) {
            continue;
        }
        fantasyPlayers[playerId].scores = fantasyPlayers[playerId].scores || {};
        fantasyPlayers[playerId].scores[matchNo] = playerScores[playerId];
        fantasyPlayers[playerId].points += playerScores[playerId];
    }
}

function updateScoresInDb() {
    processFantasyLeagueScores();
    $(".control").hide();
    console.log(fantasyPlayers);
    console.log(fantasyTeams);

    database.ref("matches/" + matchNo + "/totalPointsScored").set(jackpotScore);

    database.ref("players").set(fantasyPlayers);

    database.ref("fantasyTeams").set(fantasyTeams);

    database.ref("tournaments/1/nextMatch").set(matchNo+1);
    alert("Score updated, please check lb");
}