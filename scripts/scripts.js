$(document).ready(function() {
    $("#btnSearch").click(function() {
        if (ValidBotBoot()) {
            $.ajax({
                url: "proccess_search_prefix.php",
                type: "post",
                async: false,
                data: {prefix: $("#txtPrefix").val()},
                success: function(result)
                {
                    $("#SearchResultArea").html(result);

                    $("#btnSave").click(function() {
                        $.post("proccess_change_policy.php", $("#frmChange").serialize(),
                                function(result) {
                                    $("#ExcuteResultArea").html(result);
                                });
                    });
                    
                    $("#btnShow").click(function() {
                        $.post("proccess_show_policy.php", $("#frmChange").serialize(),
                                function(result) {
                                    $("#ShowResultArea").html(result);
                                });
                    });
                }
            });
        }
    });
});

var a = Math.ceil(Math.random() * 10);
var b = Math.ceil(Math.random() * 10);
var c = a + b;
function DrawBotBoot()
{
    document.write(a + " + " + b + " = ");
    document.write("<input id='BotBootInput' type='text' maxlength='2' size='2'/>");
}
function ValidBotBoot() {
    var d = document.getElementById('BotBootInput').value;
    if (d == c) {
        document.getElementById('BotBootInput').value = null;
        return true;
    }
    else {
        alert("Câu trả lời chưa chính xác.\nThao tác không thực hiện được. :(");
    }
    return false;

}

// this function create an Array that contains the JS code of every <script> tag in parameter
// then apply the eval() to execute the code in every script collected
function parseScript(strcode) {
    var scripts = new Array();         // Array which will store the script's code

    // Strip out tags
    while (strcode.indexOf("<script") > -1 || strcode.indexOf("</script") > -1) {
        var s = strcode.indexOf("<script");
        var s_e = strcode.indexOf(">", s);
        var e = strcode.indexOf("</script", s);
        var e_e = strcode.indexOf(">", e);

        // Add to scripts array
        scripts.push(strcode.substring(s_e + 1, e));
        // Strip from strcode
        strcode = strcode.substring(0, s) + strcode.substring(e_e + 1);
    }

    // Loop through every script collected and eval it
    for (var i = 0; i < scripts.length; i++) {
        try {
            eval(scripts[i]);
        }
        catch (ex) {
            // do what you want here when a script fails
        }
    }
}

function log_out(B) {
    var A = document.getElementsByTagName("html")[0];
    A.style.filter = "progid:DXImageTransform.Microsoft.BasicImage(grayscale=1)";
    if (confirm(B)) {
        return true
    } else {
        A.style.filter = "";
        return false
    }
}