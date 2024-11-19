require( 'scrollable-tabs-bootstrap-5/dist/scrollable-tabs.css' );
require( 'scrollable-tabs-bootstrap-5/dist/scrollable-tabs.js' );

$( function ()
{
    $( '.btnShowPaymentMethods' ).on( 'click', function()
    {
        $.ajax({
            type: "GET",
            url: $( this ).attr( 'data-url' ),
            success: function( response )
            {
                if ( response.status == 'ok' ) {
                    $( '#ModalBodyShowCustomerPaymentMethods > div.card-body' ).html( response.data );
                } else {
                    $( '#ModalBodyShowCustomerPaymentMethods > div.card-body' ).html( '' );
                }
                
                /** Bootstrap 5 Modal Toggle */
                const myModal = new bootstrap.Modal( '#customer-payment-methods-modal', {
                    keyboard: false
                });
                myModal.show( $( '#customer-payment-methods-modal' ).get( 0 ) );
            },
            error: function()
            {
                alert( "SYSTEM ERROR!!!" );
            }
        });
    });
});