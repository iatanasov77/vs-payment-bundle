
export function SubmitPayumCreditCardForm( e )
{
    // This Form Should be submitted with the default action
    //return true;

    e.preventDefault();
    submitForm( 'payum_credit_card_form' );
    
    // Prevent the form from submitting with the default action
    return false;
}

function submitForm( formId )
{
    let formData    = new FormData( document.getElementById( formId ) );
    //alert('EHO');
    
    $.ajax({
        type: "POST",
        url: $( '#' + formId ).attr( 'action' ),
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function( response, textStatus, jqXHR )
        {
            //alert( response.data.paymentPrepareUrl );
            //alert( response.data.gatewayFactory );
            
            //alert( jqXHR.status );
            console.log( 'DEBUG PAYUM CREDIT CARD SUBMIT.' );
            console.log( response );
            document.location   = document.location;
        },
        error: function()
        {
            alert( "SYSTEM ERROR!!!" );
        }
    });
}
