///////////////////////////////////////////////////////
// From Payum
// ===========
// https://github.com/Payum/Stripe/blob/master/Resources/views/Action/obtain_js_token.html.twig
//////////////////////////////////////////////////////

function PrepairPaymentForm()
{
    $( '#payment-form' ).attr( 'action', $( '#credit_card_form_captureUrl' ).val() );
    
    $( '#StripePaymentForm_Number' ).val( $( '#credit_card_form_number' ).val() );
    $( '#StripePaymentForm_Cvc' ).val( $( '#credit_card_form_cvv' ).val() );
    $( '#StripePaymentForm_ExpMonth' ).val( $( '#credit_card_form_ccmonth' ).val() );
    $( '#StripePaymentForm_ExpYear' ).val( $( '#credit_card_form_ccyear' ).val() );
    
    $( '#payment-form' ).submit();
}

var stripeResponseHandler = function( status, response ) {
    var $form = $( '#payment-form' );

    if (response.error) {
        // Show the errors on the form
        $form.find( '.payment-errors' ).text( response.error.message );
        $form.find( 'button' ).prop( 'disabled', false );
    } else {
        // token contains id, last4, and card type
        var token = response.id;
        // Insert the token into the form so it gets submitted to the server
        $form.append( $('<input type="hidden" name="stripeToken" />').val(token ) );
        // and re-submit
        $form.get(0).submit();
    }
};

export function SubmitPaymentForm( e )
{
    e.preventDefault();
    
    var publishableKey  = $( '#credit_card_form_captureUrl' ).attr( 'data-capturekey' );
    var $form           = $( this );

    // This identifies your website in the createToken call below
    Stripe.setPublishableKey( publishableKey );
    Stripe.card.createToken( $form, stripeResponseHandler );
    
    // Prevent the form from submitting with the default action
    return false;
}

export function SubmitCreditCardForm( e )
{
    e.preventDefault();
    
    // Disable the submit button to prevent repeated clicks
    $( '#btnPaymentPay' ).prop( 'disabled', true );
    
    PrepairPaymentForm();
    
    // Prevent the form from submitting with the default action
    return false;
}
    