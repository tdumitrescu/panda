(function($) {

$(document).ready(function() {
  $('.player-link').click(function(e) {
    $.get($(this).attr('href'));
    e.preventDefault();
  });
});

})(jQuery);