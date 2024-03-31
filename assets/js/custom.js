jQuery(function ($) {
    var currentDomain = window.location.origin;
    $('#ajax_form').on('submit', function (el) {
        el.preventDefault();
        $.post(ajaxurl, {action: "my_ajax_form", chatbot_id: el.target.chatbot_id.value }, function (val) {
            window.location.href = currentDomain+"/wp-admin/plugins.php";
        })
    })

})