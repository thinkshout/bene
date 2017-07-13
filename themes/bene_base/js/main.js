(function ($) {
  Drupal.behaviors.tsBENE = {
    attach: function(context, settings) {

      /**
       * UCHI Object
       */
      var _bene = {

        isMobile: function(){
          var mobileMenuDisplay = $('.mobile-menu-toggle').css('display');
          return mobileMenuDisplay != 'none';
        },

        beneMobile: function() {
          if (this.isMobile()) {
            $('.mobile-menu-toggle').once().click(function() {
              $(this).toggleClass('active');
              $('.mobile-nav .menu').fadeToggle(500);
            });
          };
        },






        init: function() {
          this.beneMobile();
        }
      } // end _bene{}

      _bene.init();

      // For any JS you want to reinit after resize.
      $(window).resize(function() {
          clearTimeout(window.resizedFinished);
          window.resizedFinished = setTimeout(function(){
            _bene.beneMobile()
          }, 250);
      });

    } // attach
  }; // tsUCHI
})(jQuery, Drupal, window);
