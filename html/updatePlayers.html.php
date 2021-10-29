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
    let tournament;
    let players, globalPlayers;
    let rowCount = 0;
    let iplTeamOptions = [], intlTeamOptions = [];
    let roleOptions = [{
        id: "Batsman",
        value: "Batsman",
        text: "Batsman"
    }, {
        id: "Bowler",
        value: "Bowler",
        text: "Bowler"
    }, {
        id: "Keeper",
        value: "Keeper",
        text: "Keeper"
    }, {
        id: "All Rounder",
        value: "All Rounder",
        text: "All Rounder"
    }];
    let yesNoOptions = [{
        id: "Yes",
        value: "Yes",
        text: "Yes",
    }, {
        id: "No",
        value: "No",
        text: "No",
    }];

    getMultiDataFromFirebase(["teams_global", "teams", "tournaments/1", "players_global", "players"],
        function (_globalTeams, _teams, _tournament, _globalPlayers, _players) {
            globalTeams = _globalTeams;
            teams = _teams;
            tournament = _tournament;
            globalPlayers = _globalPlayers;
            players = _players;

            updateTable();
    });


    function updateTable() {
        for (let id in globalTeams) {
            let team = globalTeams[id];
            let option = {
                id: "team" + id,
                value: id,
                text: team.shortName,
            };
            if (team.type === "IPL") {
                iplTeamOptions.push(option);
            } else if (team.type === "International") {
                intlTeamOptions.push(option);
            }
        }
        for (let id in globalPlayers) {
            let player = globalPlayers[id];
            addNewRow(player);
        }
    }

    function addNewRow(player) {
        rowCount++;
        let htmlId = rowCount;
        let table = $("#players tbody"),
            tr = $("<tr></tr>");
        tr.attr("id", htmlId);

        let idInput = newText("id", htmlId);
        let nameInput = newText("name", htmlId);
        let iplTeamSelect = newSelect("iplTeam", htmlId, iplTeamOptions);
        let intlTeamSelect = newSelect("intlTeam", htmlId, intlTeamOptions);
        let currentSelect = newSelect("current", htmlId, yesNoOptions);
        let roleSelect = newSelect("role", htmlId, roleOptions);
        let actionButton = newButton("action", htmlId, "Delete");
        actionButton.bind("click", function() {
            if (!confirm("This player will be permanently deleted from the system. Are you sure you want to proceed?")) {
                return;
            }
            deleteRow(htmlId);
        });

        if (player) {
            idInput.val(player.id);
            idInput.attr("disabled", true);

            nameInput.val(player.name);

            if (player.iplTeam !== undefined) {
                selectOption(iplTeamSelect, player.iplTeam);
            }

            if (player.intlTeam !== undefined) {
                selectOption(intlTeamSelect, player.intlTeam);
            }

            selectOption(roleSelect, player.role);

            selectOption(currentSelect, player.selected ? "Yes" : "No");
            actionButton = "";
        }

        tr.append(newTd(idInput));
        tr.append(newTd(nameInput));
        tr.append(newTd(iplTeamSelect));
        tr.append(newTd(intlTeamSelect));
        tr.append(newTd(currentSelect));
        tr.append(newTd(roleSelect));
        tr.append(newTd(actionButton));

        table.append(tr);
    }

    function selectOption(select, optionValue) {
        let options = select[0].options;
        for (let i = 0; i < options.length; i++) {
            if (options[i].value == optionValue) {
                options[i].selected = true;
                break;
            }
        }
    }
    function deleteRow(htmlId) {
        $("#" + htmlId).remove();
    }

    function newButton(prefix, id, text) {
        let button = $("<button></button>");
        addBasicAttributes(button, prefix, id);
        button.html(text);
        button.addClass("btn");
        button.addClass("btn-info");
        return button;
    }
    function newSelect(prefix, id, options) {
        let select = $("<select></select>");
        addBasicAttributes(select, prefix, id);
        for (let i = 0; i < options.length; i++) {
            let option = getOption(options[i]);
            select.append(option);
        }
        select.prepend("<option value='' selected='selected'></option>");
        return select;
    }
    function getOption(optionObj) {
        let option = $("<option></option>");
        option.val(optionObj.value);
        option.html(optionObj.text);
        addBasicAttributes(option, "opt", optionObj.id);
        return option;
    }
    function newText(prefix, id) {
        let text = $("<input type='text'/>");
        addBasicAttributes(text, prefix, id);
        return text;
    }

    function addBasicAttributes(element, prefix, id) {
        element.attr("id", getId(prefix, id));
        element.addClass("form-control");
    }
    function newTd(html) {
        let td = $("<td></td>");
        td.html(html);
        return td;
    }

    function updatePlayers() {
        if (!confirm("Changes will reflect directly into live fantasy site. Are you sure you want to proceed?")) {
            return;
        }
        let globalPlayersNew = {},
            playersNew = {};
        for (let i = 1; i <= rowCount; i++) {
            let row = $("#" + i);
            if (row) {
                let id = getElement("id", i).val();
                let name = getElement("name", i).val();
                let iplTeam = getElement("iplTeam", i).val();
                let intlTeam = getElement("intlTeam", i).val();
                let current = getElement("current", i).val();
                let role = getElement("role", i).val();

                let playerObj = {
                    id: id,
                    name: name,
                    iplTeam: iplTeam,
                    intlTeam: intlTeam,
                    role: role,
                };

                if (tournament.type === "International") {
                    playerObj.teamId = intlTeam;
                } else {
                    playerObj.teamId = iplTeam;
                }

                if (current === "Yes") {
                    if (playerObj.teamId === "") {
                        alert("Please select team for " + name);
                        continue;
                    }
                    playerObj.selected = 1;

                    console.log(id);
                    playersNew[id] = playersNew[id] || {};
                    for (let key in playerObj) {
                        playersNew[id][key] = playerObj[key];
                    }
                    addToTeam(id, iplTeam);
                    addToTeam(id, intlTeam);
                }
                addToGlobalTeam(id, iplTeam);
                addToGlobalTeam(id, intlTeam);
                globalPlayersNew[id] = playerObj;
            }
        }

        console.log(globalPlayersNew);
        console.log(playersNew);

        database.ref("players").set(playersNew);
        database.ref("players_global").set(globalPlayersNew);
        database.ref("teams").set(teams);
        database.ref("teams_global").set(globalTeams);

        alert("Players updated successfully. Please check the fantasy site to verify changes have reflected correctly.");
    }

    function addToGlobalTeam(playerId, teamId) {
        if (teamId !== "") {
            globalTeams[teamId].players = globalTeams[teamId].players || [];
            if (globalTeams[teamId].players.indexOf(playerId) === -1) {
                console.log("Global Teams", playerId, teamId);
                globalTeams[teamId].players.push(playerId);
            }
        }
    }

    function addToTeam(playerId, teamId) {
        if (teamId !== "" && teams[teamId]) {
            teams[teamId].players = teams[teamId].players || [];
            if (teams[teamId].players.indexOf(playerId) === -1) {
                console.log("Teams", playerId, teamId);
                teams[teamId].players.push(playerId);
            }
        }
    }

    function getElement(prefix, id) {
        return $("#" + getId(prefix, id));
    }

    function getId(prefix, id) {
        return prefix + "-" + id
    }
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

        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel panel-heading">
                    These are all the players in the system <br/>
                    You can add / edit / delete players <br/>
                    <h4>Please note that changes will not be saved until you click "Update players" button at the bottom of the page<h4>
                </div>
                <div class="panel panel-body">
                    <table class="table table-responsive" id="players">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>IPL Team</th>
                            <th>Intl Team</th>
                            <th>Active</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                        <tr>
                            <td colspan="7">
                                <input onclick="addNewRow()" type="button" name="newRow" value="Add new player" class="form-control btn-info"/>
                            </td>
                        </tr>
                            <td colspan="7">
                                <input onclick="updatePlayers()" type="button" name="submit" value="Update players" class="form-control btn-info" />
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>