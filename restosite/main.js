$(document).ready(function(){
    //main slider
    $('.slick').slick({
        dots: true,
        centerMode: true,
        centerPadding: '0px',
        slidesToShow: 1,
        autoplay: true,
        autoplaySpeed: 2000,
    });


    //fixed navigation on scroll
    function fixNav() {
        if ($('.main-nav').length) {

            let $obj = $('.main-nav');
            let top = $obj.offset().top - parseFloat($obj.css('marginTop').replace(/auto/, 0));

            $(window).scroll(function (event) {
                var y = $(this).scrollTop();

                if (y >= top) {
                    $obj.addClass('fixed');
                } else {
                    $obj.removeClass('fixed');
                }
            });
        }
    }
    fixNav();


    //open mobile menu
    $('.mobile-menu-icon').on('click', function () {
        $('body').toggleClass('opened');
        $('.mobile-header').toggleClass('opened');
    })

    $(document).click(function(event) {
        if (!$(event.target).closest(".mobile-menu-icon, .mobile-menu").length) {
            $('body').removeClass('opened');
            $(".mobile-header").removeClass('opened');
        }
    });
});

