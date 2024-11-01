jQuery(document).ready(function(){
    var options = [];

    jQuery( '.country_drop a' ).on( 'click', function( event ) {

    var $target = jQuery( event.currentTarget ),
        val = $target.attr( 'data-bs-value' ),
        $inp = $target.find( 'input' ),
        idx;

    if ( ( idx = options.indexOf( val ) ) > -1 ) {
        options.splice( idx, 1 );
        setTimeout( function() { $inp.prop( 'checked', false ) }, 0);
    } else {
        options.push( val );
        setTimeout( function() { $inp.prop( 'checked', true ) }, 0);
    }

    jQuery( event.target ).blur();
    return false;
    });


    jQuery('#banners').owlCarousel({
        loop:true,
        margin:10,
        nav:false,
        dots: false,
       
        autoplay:true,
        autoPlaySpeed: 5000,
        autoplayTimeout:5000,
        autoplayHoverPause:true,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 2
            },
            1000: {
                items: 2
            }
        }
    });

    jQuery('#kinsta_banners').owlCarousel({
        loop:true,
        margin:10,
        nav:false,
        dots: false,
        autoplay:true,
        autoPlaySpeed: 5000,
        autoplayTimeout:5000,
        autoplayHoverPause:true,
        responsive: {
            0: {
                items: 2
            },
            600: {
                items: 3
            },
            1400: {
                items: 6
            }
        }
    });
})