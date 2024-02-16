require ( 'jquery-duplicate-fields/jquery.duplicateFields.js' );

$( function()
{
    $( '.attributesContainer' ).duplicateFields({
        btnRemoveSelector: ".btnRemoveField",
        btnAddSelector:    ".btnAddField"
    });
    
    $( '.RuleType' ).on( 'change', function( e ) {
        updateItem( e );
    });
});

/**
 * Get from: Sylius/Bundle/UiBundle/Resources/private/js/sylius-form-collection.js
 */
function updateItem( event ) {
    event.preventDefault();
    
    const $element      = $( event.currentTarget );
    const url           = $element.data( 'form-url' );
    const value         = $element.val();
    const $container    = $element.closest( '[data-form-collection="item"]' );
    const index         = $container.data( 'form-collection-index' );
    const position      = $container.data( 'form-collection-index' );

    if ( url ) {
        $container.load( url, { id: value, position } );
    } else {
        let $prototype = this.$element.find( `[data-form-prototype="${value}"]` );
        let prototypeName = new RegExp( $prototype.data( 'subprototype-name' ), 'g' );

        let prototype = $prototype.val().replace( prototypeName, index );

        $container.replaceWith( prototype );
    }
    
    $( document ).trigger( 'collection-form-update', [$( event.currentTarget )] );
  }
 