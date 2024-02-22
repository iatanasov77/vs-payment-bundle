require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );
// Need copy of: jquery-easyui/images/*

require( 'jquery-easyui-extensions/EasyuiCombobox.css' );
import { EasyuiCombobox } from 'jquery-easyui-extensions/EasyuiCombobox.js';

$( function()
{
    let associationsSelector    = "#customer_group_form_customers";
    EasyuiCombobox( $( associationsSelector ), {
        required: false,
        multiple: true,
        checkboxId: "customer_groups",
        values: null,
        getValuesFrom: 'select-box',
        debug: false
    });
});
