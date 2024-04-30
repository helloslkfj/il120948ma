function loadpost(id) {
    var link = "postpage.php" + "?id=" + id.toString();
    window.location.href = link;
}

function mapMultiSelectToObject(selector) {
    var object = $(selector).map(function() {
        return $(this).val();
    });
    return object;
}

function add0sInPlaceOfNothing(string) {
    var arr = convertStringtoArray(string);

    for (let i=0; i<arr.length; i++) {
        if (arr[i] == "") {
            arr[i] = "0";
        }
    }

    var starr = convertObjectorArrayToString(arr);
    return starr;
}

function convertObjectorArrayToString(object) { // MOVE THESE FUNCTIONS TO MAIN.JS FILE SO THEY CAN BE USED IN ANY FILE
    var st = "";

    for (let i=0; i<object.length; i++) {
        if(i == 0) {
            st += object[i].toString();
        }
        else {
            st += ","+object[i].toString();
        }
    }

    return st;
}

function convertStringtoArray(string) {
    var arr = string.split(",");
    return arr;
}

function convertNodeListtoArray(nodelist) {
    arr = new Array();
    for (let i =0; i<nodelist.length; i++) {
        arr.push(nodelist[i]);
    }

    return arr;
}

function setAttributes(element, attributes) {
    for (let i=0; i<attributes.length; i++) {
        element.setAttribute(attributes[i][0], attributes[i][1]);
    }
}

function addClasses(element, classarray) {
    for (let i=0; i<classarray.length; i++) {
        element.classList.add(classarray[i]);
    }
}

function divAppend(parent, times, classes) {
    for (let i=0; i<times;i++) {
        var div = document.createElement('div');
        addClasses(div, classes);
        parent.append(div);
    }
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

function appendElement(parent, classes, attributes, message, element) {
    var el = document.createElement(element);
    addClasses(el, classes);
    if(attributes != "nothing") {
        setAttributes(el, attributes);
    }
    if (message != "") {
        el.innerHTML = message;
    }
    parent.append(el);
    return el;
}

function triggerMultInput(selector) {
    var inputs = $(selector).map(function() {
        return $(this);
    }).get();

    for(let i=0; i<inputs.length; i++) {
        inputs[i].keyup();
    }
}

function triggerAlertforUnload(e) {
    e=e || window.event;

    if(e){ 
        e.returnValue= 'Sure?';
    }

    return 'Sure?';
}

function reload() {
    location.reload();
}

function reload1() {
    window.removeEventListener('beforeunload', triggerAlertforUnload); 
    location.reload();
}

function donothing() {
    //nothing
}

function redirectToPostaPosting() {
    window.location.replace("dashboard.php?page=post2");
}

function truncate(string, length) {
    if(string.length > length) {
        return string.slice(0, length) + '.....';
    }
    else {
        return string;
    }
}