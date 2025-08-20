require ( '@vankosoft/jquery-duplicate-fields/jquery.duplicateFields.js' );

$( function()
{
    $( '.attributesContainer' ).duplicateFields({
        btnRemoveSelector: ".btnRemoveField",
        btnAddSelector:    ".btnAddField"
    });
});
 