/**
 * MANUAL
 *============================================================
 * https://stripe.com/docs/payments/accept-a-payment-charges
 */
import {loadStripe} from '@stripe/stripe-js';

// Custom styling can be passed to options when creating an Element.
const style = {
    base: {
        // Add your base input styles here. For example:
        fontSize: '16px',
        color: '#32325d',
    },
};

const stripeTokenHandler = ( token ) => {
    console.log( token );
    
    // Insert the token ID into the form so it gets submitted to the server
    const form          = document.getElementById( 'payment-form' );
    const hiddenInput   = document.createElement( 'input' );
    
    hiddenInput.setAttribute( 'type', 'hidden' );
    hiddenInput.setAttribute( 'name', 'stripeToken' );
    hiddenInput.setAttribute( 'value', token.id );
    form.appendChild( hiddenInput );
    
    // Submit the form
    form.submit();
}

export async function StripeCard()
{
    var publishableKey  = $( '#credit_card_form_captureUrl' ).attr( 'data-capturekey' );
    
    var stripe          = await loadStripe( publishableKey  );
    var elements        = stripe.elements();
        
    var card            = elements.create( 'card', {
        hidePostalCode : true
    });
    card.mount( '#card-element' );
    
    var form = document.getElementById( 'payment-form' );
    form.addEventListener('submit', async ( event ) => {
        event.preventDefault();
        
        const {token, error} = await stripe.createToken( card );
        
        if ( error ) {
          // Inform the customer that there was an error.
          const errorElement        = document.getElementById( 'card-errors' );
          errorElement.textContent  = error.message;
        } else {
          // Send the token to your server.
          stripeTokenHandler( token );
        }
    });
}
    