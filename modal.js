ajaxSaveBroswerFingerPrint();

jQuery(document).ready(function() {
    
}); 

function ajaxSaveBroswerFingerPrint () {

    const app_version = navigator.appVersion;

    console.log( app_version );

    jQuery.ajax ( {

        url: '/wp-admin/admin-ajax.php',

        type: 'POST',

        data: {

            action: 'save_browser_fingerprint',

            app_version: app_version,
        },

        success: function ( response ) {

            console.log( response );

        }

    } );

}