/**
 * Created by henrikbygren on 2018-02-07.
 */
let ajaxRequest = new XMLHttpRequest();
let setColorRequest = new XMLHttpRequest();

let userList;

let updateTimer;

function getUsersFromServer(){
    ajaxRequest.onreadystatechange = printUsers;

    ajaxRequest.open("POST", ajax_object.ajax_url);
    ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajaxRequest.send("action=server_response&todo=get_users");
}

function printUsers(){
    if (ajaxRequest.readyState == 4 && ajaxRequest.status == 200) {
        userList = JSON.parse(ajaxRequest.responseText);

        console.log(ajaxRequest.responseText); // För att visa JSON-formatet

        let parent = document.getElementById('color-panel');

        // Rensa gamalt
        while (parent.firstChild) {
            parent.removeChild(parent.firstChild);
        }

        // Skriver ut användarna i p-taggar med u_id som ID
        for(let i = 0; i < userList.length; i++){
            let userElement = document.createElement("P");
            userElement.setAttribute("ID", userList[i].u_id);
            userElement.style.color = userList[i].color;
            userElement.innerHTML = userList[i].display_name;
            userElement.addEventListener("click", userClick);

            parent.appendChild(userElement);
        }
    }
}

function userClick(ev){
    let u_id = ev.currentTarget.getAttribute("ID");

    setColorRequest.onreadystatechange = colorCheck;

    setColorRequest.open("POST", ajax_object.ajax_url);
    setColorRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    setColorRequest.send("action=server_response&todo=set_color&u_id=" + u_id + "&color=red");
}

function colorCheck(){
    if (setColorRequest.readyState == 4 && setColorRequest.status == 200) {
        getUsersFromServer();
    }
}


function init(){
    getUsersFromServer();
    updateTimer = setInterval(getUsersFromServer, 3000);
}