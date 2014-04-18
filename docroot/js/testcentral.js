function clear_all() {
	$('.result').remove();
}

function test_method() {
	var suite = $(this).closest('.test-suite').attr('data-suite');
	var method = $(this).closest('.test-method').attr('data-method');

	$.ajax({
		type: "POST",
		url: "/test/run",
		data: {
			suite: suite,
			method: method
		},
		success: function(data) {
			var t = moment().format('HH:mm:ss');
			var el_method = $('.test-suite[data-suite="' + suite + '"] .test-method[data-method="' + method + '"]');
			if(data.success === true) {
				$(el_method).after('<div class="result pass">' + t + ' Pass</div>')
			}
			else {
				$(el_method).after('<div class="result fail">' + t + ' Fail: ' + data.info + '</div>')
			}
		}
	});
}

function test_suite() {
	$(this).closest('.test-suite').find('.test-method-link').click();
}

function test_all() {
	$(".test-suite-link").click();
}

$(function() {
	$(".test-suite-link").click(test_suite);
	$(".test-method").click(test_method);
});
