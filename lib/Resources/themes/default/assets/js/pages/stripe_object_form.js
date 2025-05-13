require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );

import { EasyuiCombobox } from 'jquery-easyui-extensions/EasyuiCombobox.js';

export function initWebhookEndpointForm()
{
    let selectedEvents  = JSON.parse( $( '#webhook_endpoint_form_selectedEvents').val() );
    alert( selectedEvents );
    EasyuiCombobox( $( '#webhook_endpoint_form_enabled_events' ), {
        required: true,
        multiple: true,
        checkboxId: "enabled_events",
        values: selectedEvents
    });
}
