jQuery(document).ready(function () {

    if (Cookies.get('top-panel-closed') == 1) {
        $('.our-app-wrap').hide();
        $('header .show-app-block').show();
    }
    else {
        $('.our-app-wrap').show();
        $('header .show-app-block').hide();
    }
    $('.our-app-wrap .close').click(function () {
        Cookies.set('top-panel-closed', 1, { path: '/' });
        $('.our-app-wrap').slideUp();
        //$('header .show-app-block').show(1000);
        setTimeout(
      function () {
          $('header .show-app-block').show();

      },
      1000
    );
        return false;
    })

    $('header .show-app-block').click(function () {
        $('.our-app-wrap').slideDown();
        $('header .show-app-block').hide();
        Cookies.set('top-panel-closed', 0, { path: '/' });
        return false;
    });
});