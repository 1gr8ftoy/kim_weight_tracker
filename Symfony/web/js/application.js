// Generated by CoffeeScript 1.6.3
(function() {
  jQuery(document).ready(function() {
    $("input.date_picker").datepicker({
      dateFormat: "yy-mm-dd"
    });
    $("#flash_alert").addClass("ui-state-error").addClass("ui-corner-bottom");
    $("ul#goals_list").menu();
    return $(document).ajaxError(function(e, xhr, options) {
      if ("401" === xhr.responseText) {
        return $(location).attr("href", "/login");
      }
    });
  });

}).call(this);
