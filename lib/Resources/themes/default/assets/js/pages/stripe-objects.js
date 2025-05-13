require( '@/js/includes/resource-delete.js' );

require( 'bootstrap-icons/font/bootstrap-icons.css' );
require( 'move-js/index.js' );

require( 'scrolling-tabs-bootstrap-5/dist/scrollable-tabs.min.css' );
require( 'scrolling-tabs-bootstrap-5/dist/scrollable-tabs.min.js' );

import { initWebhookEndpointForm } from '../includes/stripe_object_form.js';

$( function ()
{
    $( '.btnShowStripeObject' ).on( 'click', function()
    {
        let url     = $( this ).attr( 'data-url' );
        let formId  = $( this ).attr( 'data-formId' );
        
        $.ajax({
            type: "GET",
            url: url,
            success: function( response )
            {
                if ( response.status == 'ok' ) {
                    $( '#modalContentStripeObject' ).html( response.data );
                    
                    if ( formId == 'WebhookEndpoint' ) {
                        initWebhookEndpointForm();
                    }
                } else {
                    $( '#modalContentStripeObject' ).html( '' );
                }
                
                /** Bootstrap 5 Modal Toggle */
                const myModal = new bootstrap.Modal( '#stripe-object-modal', {
                    keyboard: false
                });
                myModal.show( $( '#stripe-object-modal' ).get( 0 ) );
            },
            error: function()
            {
                alert( "SYSTEM ERROR!!!" );
            }
        });
    });
    
    $( '#modalContentStripeObject' ).on( 'click', 'button.StripeObjectSubmit', function() {
        $( '#modalContentStripeObject' ).find( 'form' ).first().submit();
    });
});