// Generated by CoffeeScript 1.6.3
(function() {
  this.bmi_hint_html = "" + "BMI Categories:\n" + "Underweight: < 18.5\n" + "Normal weight: 18.5 - 24.9\n" + "Overweight: 25 - 29.9\n" + "Obesity: BMI of 30 or greater";

  this.showEntries = function(page) {
    page = ((page != null) ? page : 1);
    return $("div#entries_tables").fadeOut(function() {
      return $("div#loading").fadeIn(function() {
        var data, url;
        url = Routing.generate("b_conway_tracker_entry_view");
        data = {
          start_date: $("form#entries_filter input#start_date").val(),
          end_date: $("form#entries_filter input#end_date").val(),
          all: $("form#entries_filter input#show_all_entries").is(":checked"),
          page: page
        };
        return $.get(url, data, function(html) {
          var e;
          try {
            if (html.indexOf("login_form") > -1) {
              return window.location = window.location;
            } else {
              $("div#entries_tables").html(html);
              return $("div#loading").fadeOut(function() {
                return $("div#entries_tables").fadeIn();
              });
            }
          } catch (_error) {
            e = _error;
            alert("Error loading entries");
            return $("div#loading").fadeOut(function() {
              return $("div#entries_tables").fadeIn();
            });
          }
        }).fail(function() {
          alert("Error loading entries");
          return $("div#loading").fadeOut(function() {
            return $("div#entries_tables").fadeIn();
          });
        });
      });
    });
  };

  this.clearEntrySearchForm = function() {
    $("form#entries_filter input[type=\"text\"]").each(function() {
      return $(this).val("");
    });
    return $("form#entries_filter").submit();
  };

  $(document).ready(function() {
    jQuery("#bmi-hint").attr("title", bmi_hint_html);
    jQuery("#show_all_entries").change(function() {
      return jQuery(this).closest("form").submit();
    });
    return $("form#entries_filter").submit(function(event) {
      var page;
      page = ((page != null) ? page : 1);
      showEntries(page);
      event.preventDefault();
      return false;
    });
  });

}).call(this);
