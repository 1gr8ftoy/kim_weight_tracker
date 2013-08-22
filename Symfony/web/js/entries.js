var bmi_hint_html =  '' +
'BMI Categories:\n' +
'Underweight: < 18.5\n' +
'Normal weight: 18.5 - 24.9\n' +
'Overweight: 25 - 29.9\n' +
'Obesity: BMI of 30 or greater';

$(document).ready(function() {
    jQuery("#bmi-hint").attr("title", bmi_hint_html);

    jQuery("#entries_with_weights_only").change(function() {
        jQuery(this).closest("form").submit();
    });

    $("form#entries_filter").submit(function (event) {
        $('div#entries_tables').fadeOut(function(){
            $('div#loading').fadeIn();
        });

        $.get(Routing.generate('b_conway_tracker_entry_view'), {
                start_date: $("form#entries_filter input#start_date").val(),
                end_date: $("form#entries_filter input#end_date").val(),
                all: $("form#entries_filter input#entries_with_weights_only").is(':checked')
            },
            function(html) {
                $('div#entries_tables').html(html);
            }
        )
            .fail(function() {
                alert("Error loading entries");
            })
            .always(function() {
                $('div#loading').fadeOut(function() {
                    $('div#entries_tables').fadeIn();
                });
            });

        event.preventDefault();
        return false;
    });
});

function clearEntrySearchForm() {
    $('form#entries_filter input[type="text"]').each(function () {
        $(this).val('');
    });

    $("form#entries_filter").submit();
}