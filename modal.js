const app_version = navigator.appVersion;

console.log( navigator );

jQuery(document).ready(function() {

    showModal(app_version);

    ajaxSaveBroswerFingerPrint(app_version);
    
});

function displayModal() {

    console.log('displayModal');

    
    jQuery.ajax({

        url: '/wordpress/wp-json/modal-api/v1/modal',

        type: "GET",

        success: function(response) {

            console.log(response);

            let content = response['content'];

            let associate_url = response['associate_url'];

            let display = response['display'];

            jQuery('#author-content').val(content);

            jQuery('#associate-url').val(associate_url);

        }

        });

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