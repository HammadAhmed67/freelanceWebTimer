function startTime(){
    var startButton = document.getElementById('time_log_start');
    startButton.disabled = true;
    var startTimeField = document.getElementById('time_log_startTime');
    const currentDate = new Date();
    const formattedDate = currentDate.toISOString().slice(0, 19).replace('T', ' ');
    const formattedDateTime = formattedDate.slice(0, 10) + ' ' + formattedDate.slice(11, 19);
    startTimeField.value = formattedDateTime;
    var stopButton = document.getElementById('time_log_stop');
    stopButton.disabled = false;
}

function stopTime(){
    var stopTimeField = document.getElementById('time_log_endTime');
    const currentDate = new Date();
    currentDate.setHours(currentDate.getHours() + 8);
    const formattedDate = currentDate.toISOString().slice(0, 19).replace('T', ' ');
    const formattedDateTime = formattedDate.slice(0, 10) + ' ' + formattedDate.slice(11, 19);
    stopTimeField.value = formattedDateTime;
    var durationField = document.getElementById('time_log_duration');
    var startTimeField = document.getElementById('time_log_startTime');
    const datetimeString1 = startTimeField.value;
    const datetime1 = new Date(datetimeString1);
    const datetime2 =  new Date(formattedDateTime);
    const differenceInMilliseconds = datetime2 - datetime1;
    const differenceInSeconds = differenceInMilliseconds / 1000;
    const minutes = differenceInSeconds / 60;
    const hours = minutes / 60;
    durationField.value = hours.toFixed(2);

}
function editTime(){
    var startTimeField = document.getElementById('time_log_startTime');
    var stopTimeField = document.getElementById('time_log_endTime');
    var durationField = document.getElementById('time_log_duration');
    const datetime1 = new Date(startTimeField.value);
    const datetime2 = new Date(stopTimeField.value);
    const differenceInMilliseconds = datetime2 - datetime1;
    const differenceInSeconds = differenceInMilliseconds / 1000;
    const minutes = differenceInSeconds / 60;
    const hours = minutes / 60;
    durationField.value = hours.toFixed(2);;

}