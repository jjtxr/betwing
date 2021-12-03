function startTime(d) {
    setInterval(function() {
        d.setSeconds(d.getSeconds() + 1);
        $('#txt').text((d.getHours() +':' + checkTime(d.getMinutes()) + ':' + checkTime(d.getSeconds())));
    }, 1000);
}
function checkTime(i) {
    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
    return i;
}