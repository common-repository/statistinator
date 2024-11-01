jQuery( document ).ready( function( $ ) {
	loading = "<img style='height:18px;' class='loading' src='images/loading.gif' />";
    
	// Analytics account autocomplete
    var data = { action: "analytics_accounts" };
    populateCall( "#statistinator_account_google_account", data ); 
    populateGProperties();
    populateGViews();

	// Analytics properties autocomplete
	$( "#statistinator_account_google_account" ).change( function() {
        populateGProperties( true );
	});

	// Analytics views autocomplete
	$( "#statistinator_account_google_property" ).change( function() {
        populateGViews( true );
	});

    //Facebook Autocomplete
    $( "#statistinator_account_facebook_name" ).autocomplete({
        minLength: 3,
        source: function ( request, response) {
            var query = $( "#statistinator_account_facebook_name" ).val();
            $.post( 
                php.sts_ajaxurl, { action: "facebook_pages", query : query }, response, 'json'
            );
        },
        focus: function() { return false; },
        select: function( event, ui ) {
            $( "#statistinator_account_facebook_id" ).val( ui.item.id );
            $( '.sts-error-message' ).remove();
            $( '.sts-error' ).removeClass('sts-error');
        },
        change: function() { 
            if ( $( "#statistinator_account_facebook_name" ).val() == '' ) {
                $( "#statistinator_account_facebook_id" ).val('');
            }
        },
    });

	// Mailchimp lists autocomplete
    if ( $( "#statistinator_account_mailchimp_id" ).length > 0 ) {
        var data = { action: "mailchimp_lists", };
        populateCall( "#statistinator_account_mailchimp_id", data ); 
    }

	// YouTube lists autocomplete
    if ( $( "#statistinator_account_youtube_id" ).length > 0 ) {
        var data = { action: "youtube_channels", };
        populateCall( "#statistinator_account_youtube_id", data ); 
    }

	// On Id change, set description for PHP
    $( ".set_name" ).change ( function() {
        var desc =  $( this ).find('option:selected').text() ;
        $( this ).parent().find( '.get_name' ).val( desc );
    });
    

    // Validate Form
    $( 'form' ).submit( function() {
        var facebook_id = $( "#statistinator_account_facebook_id" ).val();
        if ( facebook_id == '' ) {
            var msg = "<p class='sts-error-message'>Please select your Page from the dropdown that appears while you type</p>";
            $( '#statistinator_account_facebook_name' ).focus();
            $( '#statistinator_account_facebook_name' ).addClass('sts-error');
            $( '#statistinator_account_facebook_name' ).before( msg );

            return false;
        } 
    });

});

// Analytics properties autocomplete
function populateGViews( overwrite = false ) {

    var account = jQuery( "#statistinator_account_google_account" ).val();
    var property = jQuery( "#statistinator_account_google_property" ).val();
    if ( ! property ) return;

    var data = { action: "analytics_views", account: account, property: property };
    populateCall( "#statistinator_account_google_view", data, overwrite ); 
}

// Analytics properties autocomplete
function populateGProperties( overwrite = false ) {

    var account = jQuery( "#statistinator_account_google_account" ).val();
    if ( ! account ) return;

    var data = { action: "analytics_properties", account: account };
    populateCall( "#statistinator_account_google_property", data, overwrite ); 
}

// Make AJAX call to take user data
function populateCall( id, data, overwrite = false ) {
	jQuery( id ).after( loading );
	jQuery.post(
        php.sts_ajaxurl, data, function( res ) {
			jQuery( ".loading" ).remove();
            populateSelect( id, res , overwrite );
		}
	);
}

// Populate Select element with AJAX data
function populateSelect( id, res, overwrite = false ) {
    var data = JSON.parse( res );
	var emptylist = "<option value=''></option>";

    if ( !overwrite ) {
        var current = jQuery( id ).val();
    }
    jQuery( id ).html( emptylist );
    data.forEach( function( item ) {
        var option = "<option value='" + item.value + "'>" + item.name + "</option>";
        jQuery( id ).append( option );
    }) 
    if ( !overwrite ) {
        jQuery( id ).val( current );
    }
}
