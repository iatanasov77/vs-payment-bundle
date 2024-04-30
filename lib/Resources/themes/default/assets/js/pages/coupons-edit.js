require ( '../../css/datetime.css' );
import { VsPath } from '@/js/includes/fos_js_routes.js';

$( function()
{
    $( '#btnGenerateCode' ).on( 'click', function ( e )
    {
        let promotionId = $( this ).attr( 'data-promotionId' );
        
        $.ajax({
            type: 'GET',
            url: VsPath( 'vs_payment_generate_coupon_code', { 'promotionId': promotionId } ),
            success: function ( data )
            {
                if ( data['status'] == 'ok' ) {
                    //$( '#coupon_form_code' ).val( data['code'] );
                    let redirectUrl     = VsPath( 'vs_payment_promotion_coupon_update', { 'promotionId': promotionId, 'id': data['couponId'] } );
                    document.location   = redirectUrl;
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
