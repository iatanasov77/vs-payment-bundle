require ( 'jquery-duplicate-fields/jquery.duplicateFields.js' );

$( function()
{
    $( '.RulesContainer' ).duplicateFields({
        btnRemoveSelector: ".btnRemoveField",
        btnAddSelector:    ".btnAddField",
        onCreate: function( newElement ) {
            let collectionIndex = $( '.RulesContainer' ).children().length;
            newElement.find( '.RuleConfigurationContainer' ).attr( 'data-form-collection-index', collectionIndex );
        }
    });
    
    $( '.RulesContainer' ).on( 'change', '.RuleType', function( e ) {
        updateRulesItem( e );
    });
    
    $( '.ActionsContainer' ).duplicateFields({
        btnRemoveSelector: ".btnRemoveField",
        btnAddSelector:    ".btnAddField",
        onCreate: function( newElement ) {
            let collectionIndex = $( '.ActionsContainer' ).children().length;
            newElement.find( '.ActionConfigurationContainer' ).attr( 'data-form-collection-index', collectionIndex );
        }
    });
    
    $( '.ActionsContainer' ).on( 'change', '.ActionType', function( e ) {
        updateActionsItem( e );
    });
});

/**
 * Get from: Sylius/Bundle/UiBundle/Resources/private/js/sylius-form-collection.js
 */
function updateRulesItem( event ) {
    event.preventDefault();
    
    const $element      = $( event.currentTarget );
    const value         = $element.val();
    if ( ! value ) {
        return;
    }
    
    const $container    = $element.closest( '.RuleContainer' ).find( '.RuleConfigurationContainer' );
    const index         = $container.data( 'form-collection-index' );

    let $prototype  = $( '#RulePrototypes' ).find( 'div#' + value + ' :input' ); // .find( 'input' )
    let inputHTML   = $prototype[0].outerHTML;
    inputHTML       = inputHTML.replaceAll( '__name__', index );
    
    let prototype   = $( inputHTML, {'class': 'form-control'} ).addClass( 'form-control' );
    //alert( inputHTML );
    
    $container.html( prototype );
}

function updateActionsItem( event ) {
    event.preventDefault();
    
    const $element      = $( event.currentTarget );
    const value         = $element.val();
    if ( ! value ) {
        return;
    }
    
    const $container    = $element.closest( '.ActionContainer' ).find( '.ActionConfigurationContainer' );
    const index         = $container.data( 'form-collection-index' );

    let $prototype  = $( '#ActionPrototypes' ).find( 'div#' + value + ' :input' ); // .find( 'input' )
    let inputHTML   = $prototype[0].outerHTML;
    inputHTML       = inputHTML.replaceAll( '__name__', index );
    
    let prototype   = $( inputHTML, {'class': 'form-control'} ).addClass( 'form-control' );
    //alert( inputHTML );
    
    $container.html( prototype );
}
 