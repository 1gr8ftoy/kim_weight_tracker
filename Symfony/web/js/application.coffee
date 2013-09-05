jQuery(document).ready ->
  $("input.date_picker").datepicker dateFormat: "yy-mm-dd"
  $("#flash_alert").addClass("ui-state-error").addClass "ui-corner-bottom"
  $("ul#goals_list").menu()
  $(document).ajaxError (e, xhr, options) ->
    $(location).attr "href", "/login"  if "401" is xhr.responseText