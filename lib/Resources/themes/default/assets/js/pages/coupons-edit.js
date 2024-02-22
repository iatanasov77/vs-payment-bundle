require ( '../../css/datetime.css' );
import { VsPath } from '@/js/includes/fos_js_routes.js';

$( function()
{
    $( '#btnGenerateCode' ).on( 'click', function ( e )
    {
        $.ajax({
            type: 'GET',
            url: VsPath( 'vs_payment_generate_coupon_code' ),
            success: function ( data )
            {
                if ( data['status'] == 'ok' ) {
                    $( '#coupon_form_code' ).val( data['code'] );
                } else {
                    alert( 'ERROR !!!' );
                }
            }, 
            error: function( XMLHttpRequest, textStatus, errorThrown )
            {
                alert( 'SYSTEM ERROR !!!' );
            }
        });
    });
});
