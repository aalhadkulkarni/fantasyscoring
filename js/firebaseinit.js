var config = {
    apiKey: 'AIzaSyD6jiFzXv1Bhq9gt_6QrNYzN7-cSTdVwZw',
    authDomain: 'fantasy-league-b5923.firebaseapp.com',
    databaseURL: 'https://fantasy-league-b5923.firebaseio.com',
    projectId: 'fantasy-league-b5923',
    storageBucket: 'gs://fantasy-league-b5923.appspot.com/'
};
firebase.initializeApp(config);
var database = firebase.database();

function getMultiDataFromFirebase(fields, callback) {
    var promises = [];
    for (var i = 0; i < fields.length; i++) {
        promises.push(getPromiseFor(fields[i]));
    }
    Promise.all(promises)
        .then(function (data) {
            var returnValues = [];
            for (var i = 0; i < data.length; i++) {
                returnValues.push(data[i].val());
            }
            callback.apply(null, returnValues)
        });

}

function getPromiseFor(field) {
    return database.ref(field).once("value");
}

function getDataFromFirebase(field, callback) {
    getMultiDataFromFirebase([field], callback);
}

function takeBackup() {
    getDataFromFirebase("/", function (allData) {
        let newBackup = {};
        for (let id in allData) {
            if (id === "backups") {
                continue;
            }
            newBackup[id] = allData[id];
        }
        let allBackups = allData.backups || [];
        let newBackups = [];
        if (allBackups.length < 3) {
            newBackups = allBackups;
        } else {
            for (let i = 1; i < allBackups.length; i++) {
                newBackups.push(allBackups[i]);
            }
        }
        newBackups.push({
            time: getTimeStamp(),
            data: newBackup
        });
        console.log(newBackups);

        function getTimeStamp() {
            let date = new Date();
            let dateArr = [
                date.getUTCDate(),
                date.getUTCMonth(),
                date.getUTCFullYear(),
            ], timeArr = [
                date.getUTCHours(),
                date.getUTCMinutes(),
                date.getUTCSeconds(),
            ];
            return dateArr.join("-") + "," + timeArr.join(":");
        }
        database.ref("backups").set(newBackups);
    });
}