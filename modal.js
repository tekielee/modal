const app_version = navigator.appVersion;

console.log( navigator );

jQuery(document).ready(function() {

    showModal(app_version);

    ajaxSaveBroswerFingerPrint(app_version);
    
});

function displayModal() {

    console.log('displayModal');
    alert('displayModal'); 


}

function showModal(app_version) {

    console.log('/wordpress/wp-json/modal-api/v1/browser-inf/?app_version=' + app_version);

    jQuery.ajax({

        url: '/wordpress/wp-json/modal-api/v1/browser-inf/?app_version=' + app_version,

        type: "GET",

        success: function(response) {

          console.log(response['count']);

          if (parseInt(response['count']) > 0) {

            displayModal();

          }

        }

      });

}

function ajaxSaveBroswerFingerPrint (app_version) {
    //console.log( app_version );

    jQuery.ajax ( {

        url: '/wordpress/wp-admin/admin-ajax.php',

        type: 'POST',

        data: {

            action: 'save_browser_fingerprint',

            app_version: app_version
        },

        success: function ( response ) {

            console.log( response );

        }

    } );

}