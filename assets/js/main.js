
;(function($){

	$(document).ready(function(){
		$('#wpila_other_email').change(function(){
			if($(this.checked)){
				$('.wpila-other').removeClass('disabled-div');
			} 
		});
	});
})(jQuery); 