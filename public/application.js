(function($) {

$(document).ready(function() {
  $('.player-link').click(function(e) {
    $.get($(this).attr('href'));
    e.preventDefault();
  });

  $('#uploader input').change(validateUploadForm);
  $('#uploader input[type="text"]').on('keyup', validateUploadForm);
  $('#uploader input[type="text"]').on('blur', validateUploadForm);

  $('#talk input[type="text"]').on('keyup', validateTalkForm);
  $('#talk input[type="text"]').on('blur', validateTalkForm);
});

var validateUploadForm = function()
{
  validateForm('uploader', function() {
    return
	  $('#input-title').val() != '' &&
      $('#input-image').val() != '' &&
      $('#input-audio').val() != '';
  });
}

var validateTalkForm = function()
{
  validateForm('talk', function() {
    return $('#input-txtmsg').val() != '';
  });
}

var validateForm = function(formName, validityCheckFunc)
{
  $formSubmit = $('#' + formName + '-submit');
  if (validityCheckFunc())
    $formSubmit.removeAttr('disabled');
  else
    $formSubmit.attr('disabled', 'true');
}

})(jQuery);