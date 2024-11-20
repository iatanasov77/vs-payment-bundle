require( 'bootstrap-icons/font/bootstrap-icons.css' );
require( 'move-js/index.js' );

require( 'scrolling-tabs-bootstrap-5/dist/scrollable-tabs.min.css' );
require( 'scrolling-tabs-bootstrap-5/dist/scrollable-tabs.min.js' );

$( function ()
{
    $( '.btnShowStripeObject' ).on( 'click', function()
    {
        $.ajax({
            type: "GET",
            url: $( this ).attr( 'data-url' ),
            success: function( response )
            {
                if ( response.status == 'ok' ) {
                    $( '#modalContentStripeObject' ).html( response.data );
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
        //alert( 'Submit Clicked' );
        $( '#modalContentStripeObject' ).find( 'form' ).first().submit();
    });
});