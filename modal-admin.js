jQuery(document).ready(function() {

   console.log('admin modal js loaded');

   ajaxSaveAuthorContent ( ajaxurl )
    
});

function ajaxSaveAuthorContent ( ajaxurl ) {

    jQuery('#save-author-content').click ( function ( e ) {
        
        e.preventDefault();

        let content = jQuery('#author-content').val();

        let associate_url = jQuery('#associate-url').val();

        console.log( content );

        console.log( associate_url );

        jQuery.ajax ( {

            url: ajaxurl,

            type: 'POST',

            data: {

                action: 'save_author_content',

                content: content,

                associate_url: associate_url

            },

            success: function ( response ) {

                jQuery ( '#author-content-message' ).html( response );

            }

        } );

    } );

}
