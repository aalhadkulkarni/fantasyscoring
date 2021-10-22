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