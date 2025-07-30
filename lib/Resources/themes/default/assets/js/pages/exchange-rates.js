require( '@@/js/includes/resource-delete.js' );
require ( 'jquery-duplicate-fields/jquery.duplicateFields.js' );

var loader      = '<span class="spinner-border flex-shrink-0" role="status"><span class="visually-hidden">Loading...</span></span>';

$( function()
{
    $( '.ExchangeRateServiceOptionsContainer' ).duplicateFields({
        btnRemoveSelector: ".btnRemoveField",
        btnAddSelector:    ".btnAddField"
    });
    
    $( '#btnGetRatio' ).on( 'click', function( e )
    {
        e.preventDefault();
        e.stopPropagation();
        $( "#GetExchangeRateRatioLoader" ).prepend( loader );
        
        var url = $( this ).attr( 'data-url' );
        $.ajax({
            type: "GET",
            url: url,
            success: function( response )
            {
                $( "#GetExchangeRateRatioLoader" ).find( 'span:first' ).remove();
                //alert( response );
                
                if ( response.status == 'ok' ) {
                    $( '#vs_exchange_rate_ratio' ).val( response.data );
                    
                } else {
                    alert( "RUNTIME ERROR!!!" );
                }
            },
            error: function()
            {
                $( "#GetExchangeRateRatioLoader" ).find( 'span:first' ).remove();
                alert( "SYSTEM ERROR!!!" );
            }
        });
    });
});
