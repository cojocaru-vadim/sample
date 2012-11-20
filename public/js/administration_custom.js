
$().ready(function() {
    $.validator.setDefaults({
        submitHandler: function(e) {
            $.jGrowl("Form was successfully submitted.", { theme: 'success' });
            $(e).parent().parent().fadeOut();
            v.resetForm();
            v2.resetForm();
            v3.resetForm();
        }
    });
    var v = $("#create-user-form").validate();
    jQuery("#reset").click(function() {
        v.resetForm();
        $.jGrowl("User was not created!", { theme: 'error' });
    });
    var v2 = $("#write-message-form").validate();
    jQuery("#reset2").click(function() {
        v2.resetForm();
        $.jGrowl("Message was not sent.", { theme: 'error' });
    });
    var v3 = $("#create-folder-form").validate();
    jQuery("#reset3").click(function() {
        v3.resetForm();
        $.jGrowl("Folder was not created!", { theme: 'error' });
    });
    var validateform = $("#validate-form").validate();
    $("#reset-validate-form").click(function() {
        validateform.resetForm();
        $.jGrowl("Blogpost was not created.", { theme: 'error' });
    });
   var validatelogin = $("#login-form").validate({
        invalidHandler: function(form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                var message = errors == 1
                    ? 'You missed 1 field. It has been highlighted.'
                    : 'You missed ' + errors + ' fields. They have been highlighted.';
                $('#login-form').removeAlertBoxes();
                $('#login-form').alertBox(message, {type: 'error'});

            } else {
                $('#login-form').removeAlertBoxes();
            }
        },
        submitHandler: function(form) {
            //$('#login-form').ajaxForm();
            //console.log(form);
                var queryString = $('#login-form').formSerialize();
                $.ajax({
                type: "POST",
                url: "/administration/login/ajax-login",
                data: queryString,
                success: function(msg){
                    msg = $.parseJSON(msg);
                    if(msg.state == false){
                        $('#login-form').alertBox(msg.message, {type: 'error'});
                    }else{
                        window.location = '/administration';
                        $.jGrowl("Form was successfully submitted.", { theme: 'success' });
                        $(e).parent().parent().fadeOut();

                    }
                }
                });
            return false;
        }

    });
    jQuery("#reset-login").click(function() {
        validatelogin.resetForm();
    });
    $("#datepicker").datepicker();
    $('#table-example').dataTable();
    $('#graph-data').visualize({type: 'line', height: 250}).appendTo('#tab-line').trigger('visualizeRefresh');
    $('#graph-data').visualize({type: 'area', height: 250}).appendTo('#tab-area').trigger('visualizeRefresh');
    $('#graph-data').visualize({type: 'pie', height: 250}).appendTo('#tab-pie').trigger('visualizeRefresh');
    $('#graph-data').visualize({type: 'bar', height: 250}).appendTo('#tab-bar').trigger('visualizeRefresh');
    $("#specify-a-unique-tab-name").createTabs();
    $("#tab-graph").createTabs();
    $("#tab-panel-1").createTabs();
    $("#tab-panel-2").createTabs();
    $('#slider').sliderNav();
    $('#notification-success').click(function() {
        $.jGrowl("Hey, I'm a <strong>success</strong> message. :-)<br>I want to say you something...", { theme: 'success' });
    });
    $('#notification-error').click(function() {
        $.jGrowl("Hey, I'm a <strong>error</strong> message. :-)<br>I want to say you something...", { theme: 'error' });
    });
    $('#notification-information').click(function() {
        $.jGrowl("Hey, I'm a <strong>information</strong> message. :-)<br>I want to say you something...", { theme: 'information' });
    });
    $('#notification-warning').click(function() {
        $.jGrowl("Hey, I'm a <strong>warning</strong> message. :-)<br>I want to say you something...", { theme: 'warning' });
    });
    $('#notification-saved').click(function() {
        $.jGrowl("Hey, I'm a <strong>saved</strong> message. :-)<br>I want to say you something...", { theme: 'saved' });
    });
    $("select, input:checkbox, input:text, input:password, input:radio, input:file, textarea").uniform();
});