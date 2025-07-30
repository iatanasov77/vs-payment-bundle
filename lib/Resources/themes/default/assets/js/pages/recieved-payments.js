require( '@@/js/includes/resource-delete.js' );
require( '@@/vendor/vs_tablesortable/tablesortable.js' );
import { VsPath } from '@@/js/includes/fos_js_routes.js';

$( function()
{
    $( '#FormFilterPayments' ).on( 'submit', function( e )
    {
        e.preventDefault();
        
        let submitUrl   = VsPath( 'vs_payment_custom_payment_actions_search_payments' );
        let formData    = new FormData( $( '#FormFilterPayments' )[ 0 ] );
        
        $.ajax({
            type: "POST",
            url: submitUrl,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function( response )
            {
                if ( response.status == 'ok' ) {
                    $( '#PaymentsTableContainer' ).html( response.data );
                } else {
                    alert( 'ERROR' );
                }
            },
            error: function()
            {
                alert( "SYSTEM ERROR!!!" );
            }
        });
        
        return false;
    });
});
