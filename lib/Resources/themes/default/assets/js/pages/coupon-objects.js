const bootstrap = require( 'bootstrap' );

$( function()
{
    $( '.btnRetrieveCoupon' ).on( 'click', function( e )
    {
        e.preventDefault();
        
        var url      = $( this ).attr( 'data-url' );
        
        $.ajax({
            type: "GET",
            url: url,
            success: function( response )
            {
                $( '#modalBodyRetrieveCoupon > div.card-body' ).html( response );
                
                /** Bootstrap 5 Modal Toggle */
                const myModal = new bootstrap.Modal( '#retrieve-coupon-modal', {
                    keyboard: false
                });
                myModal.show( $( '#retrieve-coupon-modal' ).get( 0 ) );
            },
            error: function()
            {
                alert( "SYSTEM ERROR!!!" );
            }
        });
    });
});