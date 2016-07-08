(function( $ ) {
  $.fn.validate = function(form, name, valid, required, min, max, error) {

	if (valid == 'text'){
		if (required == 1) {
			if( $('input[name="' + name + '"]').val().search(/^[A-ZА-ЯЁ][a-zа-яё]+[\-]?[A-ZА-ЯЁа-яёa-z]?[a-zа-яё]+$/) == -1 || $('input[name="' + name + '"]').val().length < min || $('input[name="' + name + '"]').val().length > max){
				$('#form-' + form + '-label-' + name).addClass('form-error')
				$('#form-' + form + '-error-' + name).html('Error: ' + error + '<br>(/^[A-ZА-ЯЁ][a-zа-яё]+[\-]?[A-ZА-ЯЁа-яёa-z]?[a-zа-яё]+$/)<input type="hidden" name="form-' + form + '-disabled" value="1">')
				$('input[name="form-' + form + '-submit"]').prop('disabled', true);
			} else {
				$('#form-' + form + '-label-' + name).removeClass('form-error')
				$('#form-' + form + '-error-' + name).html('')
				if(($('input[name="form-' + form + '-disabled"]').val() != 1) && ($('input[name="resume"]').val() != '')) {
					$('input[name="form-' + form + '-submit"]').prop('disabled', false);
				}
			}
		}
	}
	
	if (valid == 'date'){
		if (required == 1) {
			if( $('input[name="' + name + '"]').val().search(/^[0-1][0-9][\/][0-3][0-9][\/][0-9]{4}$/) == -1 || $('input[name="' + name + '"]').val().length < min || $('input[name="' + name + '"]').val().length > max){
				$('#form-' + form + '-label-' + name).addClass('form-error')
				$('#form-' + form + '-error-' + name).html('Error: ' + error + '<br>(/^[0-1][0-9][\/][0-3][0-9][\/][0-9]{4}$/)<input type="hidden" name="form-' + form + '-disabled" value="1">')
				$('input[name="form-' + form + '-submit"]').prop('disabled', true);
			} else {
				$('#form-' + form + '-label-' + name).removeClass('form-error')
				$('#form-' + form + '-error-' + name).html('')
				if(($('input[name="form-' + form + '-disabled"]').val() != 1) && ($('input[name="resume"]').val() != '')) {
					$('input[name="form-' + form + '-submit"]').prop('disabled', false);
				}
			}
		}
	}

	if (valid == 'int') {
		if (required == 1) {
			if( $('input[name="' + name + '"]').val().search(/^[0-9]+$/) == -1 || $('input[name="' + name + '"]').val() < min || $('input[name="' + name + '"]').val() > max){
				$('#form-' + form + '-label-' + name).addClass('form-error')
				$('#form-' + form + '-error-' + name).html('Error: ' + error + ' &nbsp; (/^[0-9]+$/)<input type="hidden" name="form-' + form + '-disabled" value="1">')
				$('input[name="form-' + form + '-submit"]').prop('disabled', true);
			} else {
				$('#form-' + form + '-label-' + name).removeClass('form-error')
				$('#form-' + form + '-error-' + name).html('')
				if(($('input[name="form-' + form + '-disabled"]').val() != 1) && ($('input[name="resume"]').val() != '')) {
					$('input[name="form-' + form + '-submit"]').prop('disabled', false);
				}
			}
		}
	}

	if (valid == 'file') {
		
		if(($('input[name="form-' + form + '-disabled"]').val() != 1) && ($('input[name="resume"]').val() != '')) {
			$('#form-' + form + '-label-' + name).removeClass('form-error')
			$('#form-' + form + '-error-' + name).html('')
			$('input[name="form-' + form + '-submit"]').prop('disabled', false);
		}else{
			$('input[name="form-' + form + '-submit"]').prop('disabled', true);
			$('#form-' + form + '-label-' + name).addClass('form-error')
		}
	}

  };
})(jQuery);