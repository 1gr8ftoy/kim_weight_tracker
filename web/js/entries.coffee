@bmi_hint_html = "" + "BMI Categories:\n" + "Underweight: < 18.5\n" + "Normal weight: 18.5 - 24.9\n" + "Overweight: 25 - 29.9\n" + "Obesity: BMI of 30 or greater"

@showEntries = (page) ->
  page = (if (page?) then page else 1)
  $("div#entries_tables").fadeOut ->
    $("div#loading").fadeIn ->
      url = Routing.generate("b_conway_tracker_entry_view")
      data =
        start_date: $("form#entries_filter input#start_date").val()
        end_date: $("form#entries_filter input#end_date").val()
        all: $("form#entries_filter input#show_all_entries").is(":checked")
        page: page

      $.get(url, data, (html) ->
        try
          if html.indexOf("login_form") > -1
            window.location = window.location
          else
            $("div#entries_tables").html html
            $("div#loading").fadeOut ->
              $("div#entries_tables").fadeIn()

        catch e
          alert "Error loading entries"
          $("div#loading").fadeOut ->
            $("div#entries_tables").fadeIn()

      ).fail ->
        alert "Error loading entries"
        $("div#loading").fadeOut ->
          $("div#entries_tables").fadeIn()

@clearEntrySearchForm = ->
  $("form#entries_filter input[type=\"text\"]").each ->
    $(this).val ""

  $("form#entries_filter").submit()

$(document).ready ->
  jQuery("#bmi-hint").attr "title", bmi_hint_html
  jQuery("#show_all_entries").change ->
    jQuery(this).closest("form").submit()

  $("form#entries_filter").submit (event) ->
    page = (if (page?) then page else 1)
    showEntries page
    event.preventDefault()
    false