
/* This function is triggered when the start button is clicked. 
   Placeholders below are set in the snippet properties
*/


$(document).ready(function (event) {
    $('#pb_button').click(function () {

        /* If no action selected, submit (reload) form */
        if (($('#nf_notify').prop('checked') == false)
                && ($('#nf_send_test_email').prop('checked') == false)
                && ($('#nf_send_tweet').prop('checked') == false)) {
           $('#nf_form').submit();
            return true;
        }

        $('#nf_results').find('span').remove();
        $('#nf_results').find('br').remove();
        $('#nf_results').hide();

        /** ToDo: make this generic */
        var connectorUrl = "http://localhost/addons/assets/components/notify/connector.php";


        /* One or more actions selected */

        /* IF send_tweet is checked, call sendTweet processor */
        if ($("#nf_send_tweet").prop('checked') == true) {
            $.ajax({
                type: "POST",
                data: {
                    'action': 'mgr/nfsendtweet',
                    'tweet_text': $("#nf_tweet_text").val()
                },
                dataType: "json",
                cache: false,
                url: connectorUrl,
                success: function (data) {
                   if (data['errors'] !== null) {
                       data['errors'].forEach(function (err, i) {
                           console.log("Error: " + value);
                           $('<span class="nf_error">' + value + '</span><br />').appendTo("#nf_results")


                       });
                   }
                   if (data['successMessages'] !== null) {
                       data['successMessages'].forEach(function (msg, i) {
                           console.log("Success: " + msg);
                           $('<span class="nf_success">' + msg + '</span><br />').appendTo("#nf_results")
                       });
                   }
                   $("#nf_results").slideDown("slow");
                    var $target = $('html,body');
                    $target.animate({scrollTop: $target.height()}, 1000);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                   alert("Status: " + textStatus);
                   alert("Error: " + errorThrown);
                }


            });
        }

        /* Send single Email */
        if ($("#nf_send_test_email").prop('checked') == true) {
            $.ajax({
                type: "POST",
                data: {
                   'action': 'mgr/nfsendemail',
                   'send_bulk': false,
                   'single_id': $("#nf_test_email_address").val(),
                   'email_subject': $("#nf_email_subject").val(),
                   'email_text': $("#nf_email_text").val(),
                   'single': true
                },
                dataType: "json",
                cache: false,
                url: connectorUrl,
                success: function (data) {
                    if (data['errors'] !== null) {
                       data['errors'].forEach(function (err, i) {
                           $('<span class="nf_error">' + err + '</span><br />').appendTo("#nf_results")
                       });
                    }

                    if (data['successMessages'] !== null) {
                        data['successMessages'].forEach(function (msg, i) {
                            $('<span class="nf_success">' + msg + '</span><br />').appendTo("#nf_results")
                        });
                    }

                   $("#nf_results").slideDown("slow");
                   var $target = $('html,body');
                   $target.animate({scrollTop: $target.height()}, 1000);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                   alert("Status: " + textStatus);
                   alert("Error: " + errorThrown);
                }
                });
        }

            /* Send bulk email (with progress bar)
             *
             * Start the notify-process snippet, ignore the return value
             * this needs to be at the top so the process snippet
             * can write to the file and this ajax call can complete
             * before the second ajax call tries to read the file
             */
        if ($("#nf_notify").prop('checked') == true) {

            $.ajax({
                type: "POST",
                data: {
                    'action': 'mgr/nfsendemail',
                    'send_bulk' : true,
                    'email_subject': $("#nf_email_subject").val(),
                    'email_text': $("#nf_email_text").val(),
                    'groups': $("#nf_groups").val(),
                    'tags': $("#nf_tags").val(),
                    'require_all_tags': $("#nf_require_all_tags").prop('checked') == true,
                    'single': false
                },
                dataType: "json",
                cache: false,
                url: connectorUrl,
                success: function(data) {
                    if (data['errors'] !== null) {
                       data['errors'].forEach(function (err, i) {
                            console.log("Error: " + err);
                           $('<span class="nf_error">' + err + '</span><br />').appendTo("#nf_results")
                        });
                    }
                    if (data['successMessages'] !== null) {
                       data['successMessages'].forEach(function (msg, i) {
                           console.log("Success: " + msg);
                           $('<span class="nf_success">' + msg + '</span><br />').appendTo("#nf_results")
                       });
                    }

                   $("#nf_results").slideDown("slow");
                    var $target = $('html,body');
                    $target.animate({scrollTop: $target.height()}, 1000);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                   alert("Status: " + textStatus);
                   alert("Error: " + errorThrown);
                }
            });


            var url = "http://localhost/addons/notify-status.html";

            $("#pb_progressbar").progressbar({
                value: 0,
                max: 100
            });

            $("#pb_button").hide();
            $("#pb_progressOuter").slideDown("slow");
            var $target = $('html,body');
            $target.animate({scrollTop: $target.height()}, 1000);




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
