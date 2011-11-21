(function($) {

$(document).ready(function() {
  $('.player-link').click(function(e) {
    $.get($(this).attr('href'));
    e.preventDefault();
  });

  $('#uploader input').change(checkFormValidity);
  $('#uploader input[type="text"]').on('keyup', checkFormValidity);
  $('#uploader input[type="text"]').on('blur', checkFormValidity);
});

var checkFormValidity = function()
{
  var valid =
    $('#input-title').val() != '' &&
    $('#input-image').val() != '' &&
    $('#input-audio').val() != '';

  if (valid)
    $('#uploader-submit').removeAttr('disabled');
  else
    $('#uploader-submit').attr('disabled', 'true');
}

})(jQuery);