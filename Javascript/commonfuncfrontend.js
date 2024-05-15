function doNothing (response){
    //do nothing
}

function sendAJAXRequest(url, data, func) {
    $.ajax({
        url: url,
        dataType: 'text',
        cache: false,
        contentType: false,
        processData: false,
        data: data,
        type: 'post',
        success: function(response) {
            console.log(response);
            func(response);
        }
    });
}