function giveEmptyData() {
    var emptydata = new FormData();
    return emptydata;
}

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

function sendAJAXRequest2(url, data, func, vardata) {
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
            func(response, vardata);
        }
    });
}

//This errorHandle function will send the value of the input element to the specified url and then get the response from the server based on var name and 
// add it to the element where the response should be shown (id for error); this function helps with asynchronous error handling
function errorHandle(url, dataobject, idforerror) {
    $.post(url, dataobject, function(data, status) {
        $(idforerror).html(data);
    });
}

function keyUpAllElements(elementarray) {
    for (let i=0;i<elementarray.length;i++) {
        $(elementarray[i]).trigger('keyup');
    }

    return;
}

function createDataObject(elements, varnames) {
    var dataobject = {};

    for (i=0; i<elements.length; i++) {
        dataobject[varnames[i]] = $(elements[i]).val();
    }

    return dataobject;
}

function createFormDataObject(elements, varnames) {
    var formdataobject = new FormData();

    for (i=0; i<elements.length; i++) {
        formdataobject.append(varnames[i], $(elements[i]).val());
    }

    return formdataobject;
}

function reLoadandErrorHandle(input, varname) {
    if(input == 'true') {
        location.reload();
    }
    else {
        $(varname).html(input);
    }
}

function reLoad(input) {
    if(input == 'true') {
        location.reload();
    }
}