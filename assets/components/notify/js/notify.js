
/* This function is triggered when the start button is clicked. 
   Placeholders below are set in the snippet properties
*/

/*$(document).ready(function () {
    start();
});*/

$(document).ready(function (event) {
    $('#pb_button').click(function () {

    /* If no action selected, submit (reload) form */
    if (($('#nf_notify').prop('checked') == false)
            && ($('#nf_send_test_email').prop('checked') == false)
            && ($('#nf_send_tweet').prop('checked') == false)) {
       $('#nf_form').submit();
        return true;
    }

    $('#nf_results').empty();
    $('#nf_results').hide();

    /* One or more actions selected. Run Processor */
    /* start the notify-process snippet, ignore the return value
     * this needs to be at the top so the process snippet
     * can write to the file and this ajax call can complete
     * before the second ajax call tries to read the file
     */

    var connectorUrl = "http://localhost/addons/assets/components/notify/connector.php";
    $.ajax({
        type: "POST",
        data: {
            'action': 'mgr/nfsend',
            'send_bulk' : $("#nf_notify").prop('checked') == true,
            'single_id': $("#nf_test_email_address").val(),
            'email_subject': $("#nf_email_subject").val(),
            'email_text': $("#nf_email_text").val(),
            'groups': $("#nf_groups").val(),
            'tags': $("#nf_tags").val(),
            'require_all_tags': $("#nf_require_all_tags").prop('checked') == true,
            'single': $("#nf_send_test_email").prop('checked') == true,
            'send_tweet': $("#nf_send_tweet").prop('checked') == true,
            'tweet_text': $("#nf_tweet_text").val()
        },
        dataType: "json",
        cache: false,
        url: connectorUrl,
        success: function(data) {
           data['errors'].forEach(function (err, i) {
                console.log("Error: " + err);
               $('<br /><span class="nf_error">' + err + '</span>').appendTo("#nf_results")


            });
           data['successMessages'].forEach(function (msg, i) {
               console.log("Success: " + msg);
               $('<br /><span class="nf_success">' + msg + '</span>').appendTo("#nf_results")
           });
            $("#results").slideDown("slow");
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
           alert("Status: " + textStatus);
           alert("Error: " + errorThrown);
        }
    });

    if  ($("#nf_notify").prop('checked') == true) {
        var url = "http://localhost/addons/notify-status.html";

        $("#pb_progressbar").progressbar({
            value: 0,
            max: 100
        });

        $("#pb_button").hide();
        $("#pb_progressOuter").slideDown("slow");




        /* set the timer that checks the status.php file for progress reports */
        var timer = setInterval(function(){
        $.ajax({
            type: "POST",
            url: url,
            cache: false,
            data: {},
            dataType:"json",
            //crossDomain: true,

            /* update the progress bar and text messages if the file changes */
            success: function(data){
                if (data.percent >= 100) {
                    clearInterval(timer);
                    $("#pb_progressOuter").hide("slow");
                    $("#pb_button").show();
                    /* Clear for future runs */
                    data.percent = 0;
                    data.text1="";
                    data.text2="";

                }
                $("#pb_progressbar").progressbar("value",data.percent);
                $("#pb_percent").text(data.percent);
                $("#pb_text2").text(data.text2);
                $("#pb_text1").text(data.text1);

            },
        error : function (x, d, e) {
              if (x.status == -5) {
                  alert("You are offline!! Please Check Your Network.");
              } else {
                  if (x.status == 404) {
                      alert("Requested URL not found");
                  } else {
                      if (x.status == 500) {
                          alert("Internal Server Error.");
                      } else {
                          if (e == "parsererror") {
                              alert("Error: Parsing JSON Request failed.");
                          } else {
                              if (e == "timeout") {
                                  alert("Request Time out.");
                              } else {
                                  alert("Unknown Error: " + x.responseText);
                              }
                          }
                      }
                  }
              }
          }
       });
       },800);
    }
    return false;
    })

});
