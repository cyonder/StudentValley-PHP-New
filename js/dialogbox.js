function freeze_account(id){
    $.ajax({
        type: "POST",
        url: "/lib/freeze-account.php",
        data: "id=" + id
    });
}

function disconnect_connection(id){
    $.ajax({
       type: "POST",
        url: "/lib/disconnect-connection.php",
        data: "id=" + id
    });
}

function custom_alertbox(){
    this.render = function(dialog){
        var window_height = window.innerHeight;
        var window_width = window.innerWidth;
        var dialogbox_overlay = document.getElementById("dialogbox-overlay");
        var dialogbox = document.getElementById("dialogbox");
        dialogbox_overlay.style.display = "block";
        dialogbox_overlay.style.height = window_height + "px";
        dialogbox.style.left = (window_width / 2) - (550 * .5) + "px";
        dialogbox.style.top = "100px";
        dialogbox.style.display = "block";
        document.getElementById("dialogbox-head").innerHTML = "Congratulations!";
        document.getElementById("dialogbox-body").innerHTML = dialog;
        document.getElementById("dialogbox-foot").innerHTML = "<button onclick=Alert.ok() class=button-4>Okay</button>";
    }
    this.ok = function(){
        document.getElementById("dialogbox-overlay").style.display = "none";
        document.getElementById("dialogbox").style.display = "none";
        window.location.replace("http://studentvalley.org/login");
    }
}
var Alert = new custom_alertbox();

function custom_confirmbox(){
    this.render = function(title, dialog, operation, id){
        var window_height = window.innerHeight;
        var window_width = window.innerWidth;
        var dialogbox_overlay = document.getElementById("dialogbox-overlay");
        var dialogbox = document.getElementById("dialogbox");
        dialogbox_overlay.style.display = "block";
        dialogbox_overlay.style.height = window_height + "px";
        dialogbox.style.left = (window_width / 2) - (550 * .5) + "px";
        dialogbox.style.top = "100px";
        dialogbox.style.display = "block";
        document.getElementById("dialogbox-head").innerHTML = title;
        document.getElementById("dialogbox-body").innerHTML = dialog;
        document.getElementById("dialogbox-foot").innerHTML = "<button onclick=Confirm.yes(\'"+ operation + "\'," + id +") class=button-4>Yes</button> <button onclick=Confirm.no() class=button-4>No</button>";
    }
    this.yes = function(operation, id){
        if(operation == "Freeze"){
            freeze_account(id);
            window.location.replace("http://studentvalley.org");
        }else if(operation == "Disconnect"){
            disconnect_connection(id);
            //document.location.href = '/profile/'+id;
        }
        document.getElementById("dialogbox-overlay").style.display = "none";
        document.getElementById("dialogbox").style.display = "none";
    }
    this.no = function(){
        document.getElementById("dialogbox-overlay").style.display = "none";
        document.getElementById("dialogbox").style.display = "none";
    }
}
var Confirm = new custom_confirmbox();