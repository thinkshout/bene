(function (Drupal, $) {
    Drupal.behaviors.tsLvhn = {
        attach: function(context, settings) {

            _sttr = {
                setMediaEmbedDisplay: function() {
                    $('.media--view-mode-embedded-half').parents('figure').addClass('embedded-half');
                    console.log($('.media--view-mode-embedded-half').parents('figure'));
                }
            };

            _sttr.setMediaEmbedDisplay();
        }
    }
})(Drupal, jQuery);