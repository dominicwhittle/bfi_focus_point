(function($) {

  // var post_id = $('#post_ID').val();
  //
  // var $wrap = $('#thumbnail-head-'+$('#post_ID').val());
  // var $trigger = $('<span>Set Focus</span>');
  // $trigger.appendTo( $wrap );


  // Trigger button
  $('.wknds_bfifc_trigger').click( function(e){
    e.preventDefault();
    var post_id = $(this).data('id');
    focus_lightbox( post_id );
  });


  // Alternative trigger button for ACF
    $('.acf_postbox').on( 'click', 'img.acf-image-image', function(){
      // console.log( 'click!' );
      var post_id = $(this).parent().siblings('.acf-image-value').val();

      if ( post_id ){
        focus_lightbox( post_id );
      }

    }).on( 'hover', 'img.acf-image-image', function(){
      $(this).css('cursor','alias');
    });
    // $acf.on('click', '.acf-image-uploader .acf-button-edit', function(){
    //   e.preventDefault();
    // });




  var focus_lightbox = function( post_id ){
    // Get the post ID from a hidden input
    // var post_id = $('#post_ID').val();


    $('body').css('overflow','hidden'); // prevent the body from scrolling


    // Get Markup via AJAX

    var data = {
      'action'  : 'get_focus_markup',
      'post_id' : post_id
    };
    $.post(ajaxurl, data, function(response) {
      // console.log( response );

      var $markup = $( response );

      $markup.appendTo( 'body' );

      // Close lightbox
      $markup.find('#wknds-focus-close, #wknds-focus-close-alt').click( function(){
        $markup.remove();
        $('body').css('overflow',''); // put back to whatever it was.
      });


      var $loupe = $markup.find('#wknds-focus-point'),
          $img   = $markup.find('#wknds-focus-img');

      $loupe
      .draggable({
        containment: "parent", // Restrain movement inside parent container
      })
      .on( "dragstart", function() {
        $loupe.removeClass('wknds-focus-point--saved');
      })
      .on( "dragstop", function( event, ui ) {
        // Convert our pixel offsets to %
        var l = (ui.position.left / $img.width()).toFixed(5);
        var t = (ui.position.top / $img.height()).toFixed(5);

        // console.log(l,t);

        // data passed via ajax to our functions
        var data = {
      		'action'  : 'update_focus_point',
      		'post_id' : post_id,
          'focus'   : l + ',' + t
      	};
      	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
      	$.post(ajaxurl, data, function(response) {
      		// console.log('Got this from the server: ' + response);

          var offsets = response.split(',');

          // Position our draggable with the saved offsets.
          $loupe
            .css({
              'left' : (offsets[0]*100) + '%',
              'top'  : (offsets[1]*100) + '%'
            })
            .addClass('wknds-focus-point--saved');

      	}); // $loupe

      });

    }); // markup ajax


  }; // lightbox()

})(jQuery);
