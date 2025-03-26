require( '@/js/includes/resource-delete.js' );
require ( 'jquery-duplicate-fields/jquery.duplicateFields.js' );

$( function()
{
    $( '.ExchangeRateServiceOptionsContainer' ).duplicateFields({
        btnRemoveSelector: ".btnRemoveField",
        btnAddSelector:    ".btnAddField"
    });
});
