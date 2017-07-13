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

        messageDismiss: function() {
          $('.messages').once().click(function(){
            $(this).fadeOut();
          })
        },

        showHideTabs: function(){
          $('.user-logged-in .block-local-tasks-block').once().prepend('<div class="show-hide"><span></span></div>');

          $('.block-local-tasks-block .show-hide').once().click(function() {
            $(this).parent().toggleClass('active');
          });
        },

        init: function() {
          this.beneMobile();
          this.messageDismiss();
          this.showHideTabs();
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
