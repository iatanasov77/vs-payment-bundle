

export function SubmitPayumCreditCardForm( e )
{
    e.preventDefault();
    
    let formId      = 'payum_credit_card_form';
    let formData    = new FormData( document.getElementById( formId ) );
    
    $.ajax({
        type: "POST",
        url: $( '#' + formId ).attr( 'action' ),
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function( response )
        {
            //alert( response.data.paymentPrepareUrl );
            //alert( response.data.gatewayFactory );
            document.location   = document.location;
        },
        error: function()
        {
            alert( "SYSTEM ERROR!!!" );
        }
    });
    
    // Prevent the form from submitting with the default action
    return false;
}
