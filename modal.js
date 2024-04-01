const app_version = navigator.appVersion;
const user_agent = navigator.userAgent;

console.log( navigator );
ajaxSaveBroswerFingerPrint(app_version);

jQuery(document).ready(function() {
    
}); 

function ajaxSaveBroswerFingerPrint (app_version, user_agent) {
    //console.log( app_version );

    jQuery.ajax ( {

        url: '/wordpress/wp-admin/admin-ajax.php',

        type: 'POST',

        data: {

            action: 'save_browser_fingerprint',

            app_version: app_version,
            user_agent: user_agent
        },

        success: function ( response ) {

            console.log( response );

        }

    } );

}