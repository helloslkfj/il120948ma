function reLoadandErrorHandle(input) {
    if(input == 'true') {
        location.reload();
    }
    else {
        $("#loginerror").html(input);
    }
}