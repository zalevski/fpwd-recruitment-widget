jQuery('#fpwd_widget_form').on('submit', function (event) {
    event.preventDefault()
    let order_id = jQuery('input[name=order_id]').val()
    let message = jQuery('#fpwd_response')
    jQuery.ajax({
        method: 'POST',
        url: ajax_object.ajax_url,
        data: {
            action: 'check_payment',
            data: {
                order_id: order_id
            },
        }
    }).done(function (data) {
        if (data.success == false) {
            message.text('Nie znaleziono zamówienia');
        } else {
            data = JSON.parse(data)
            if (data.accounting_date != null) {
                message.text(`Płatność w wysokości ${data.amount} ${data.currency} została zaksięgowana`)
            } else {
                message.text(`Zamówienie o numerze ${data.order_id} nie zostało opłacone.`)
            }
        }
    }).fail(function () {
        message.text('Wystąpił błąd serwera');
    });
})