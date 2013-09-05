var bmi_hint_html =  '' +
'BMI Categories:\n' +
'Underweight: < 18.5\n' +
'Normal weight: 18.5 - 24.9\n' +
'Overweight: 25 - 29.9\n' +
'Obesity: BMI of 30 or greater';

$(document).ready(function() {
    jQuery("#bmi-hint").attr("title", bmi_hint_html);

    jQuery("#show_all_entries").change(function() {
        jQuery(this).closest("form").submit();
    });

    $("form#entries_filter").submit(function (event) {
        var page = (page != null) ? page : 1;

        showEntries(page);

        event.preventDefault();
        return false;
    });
});

function showEntries(page) {
    page = (page != null) ? page : 1;

    $('div#entries_tables').fadeOut(function(){
        $('div#loading').fadeIn(function() {
            var url = Routing.generate('b_conway_tracker_entry_view');
            var data = {
                start_date: $("form#entries_filter input#start_date").val(),
                end_date: $("form#entries_filter input#end_date").val(),
                all: $("form#entries_filter input#show_all_entries").is(':checked'),
                page: page
            };

            $.get(url, data,
                function(html) {
                    try {
                        if( html.indexOf( "login_form" ) > -1 ) {
                            window.location = window.location;
                        } else {
                            $('div#entries_tables').html(html);

                            $('div#loading').fadeOut(function() {
                                $('div#entries_tables').fadeIn();
                            });
                        }
                    } catch (e) {
                        alert("Error loading entries");

                        $('div#loading').fadeOut(function() {
                            $('div#entries_tables').fadeIn();
                        });
                    }
                }
            )
                .fail(function() {
                    alert("Error loading entries");

                    $('div#loading').fadeOut(function() {
                        $('div#entries_tables').fadeIn();
                    });
                });
        });
    });
}

function clearEntrySearchForm() {
    $('form#entries_filter input[type="text"]').each(function () {
        $(this).val('');
    });

    $("form#entries_filter").submit();
}