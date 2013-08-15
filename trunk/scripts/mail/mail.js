$(function() {
	// валидаци€ данных на форме
	$("#pismo").validate({
		rules: {
			email: {
				required: true,
				email: true
			}
		},
		messages: {
			name: {
					required: "¬ведите им€"
			},
			email: {
					required: "¬ведите адрес электронной почты",
					email: "¬ведите правильный адрес электронной почты"
			},
			text: {
					required: "¬ведите текст сообщени€"
			},
			captcha: {
					required: "¬ведите проверочный код"
			}
		},
		errorElement: "li"
	});
	
	// captcha
	$("#captcha").realperson({length: 5, includeNumbers: true, regenerate: 'ќбновить'});
	
	// отправка данных на сервер
	$("#pismo").submit(function() {
		var params = $("#pismo").formSerialize();
		console.log('ѕараметры: ' + params);
		var options = {
						url: 'scripts/mail/mail.php',
						type: 'POST',
						data: params,
						dataType: 'json',
						//contentType: 'application/x-www-form-urlencoded; charset=windows-1251',
						beforeSubmit: function(arr, $form, options) {
								return $form.valid();
						},
						success: function(result, statusText, xhr, $form) {
								console.log(result);
								if (result.success == true) {
									$("#pismo div.message").toggleClass("success", true);
									$("#pismo div.message").toggleClass("error", false);
								}
								else {
									$("#pismo div.message").toggleClass("error", true);
									$("#pismo div.message").toggleClass("success", false);
								}
								$("#pismo div.message").text(result.message);
								$("#pismo div.message").show("slow").fadeOut(18000);
								$(".realperson-challenge").click();
						},
						error: function() {
							$("#pismo div.message").toggleClass("error", true);
							$("#pismo div.message").toggleClass("success", false);
							$("#pismo div.message").text(result.message);
							$("#pismo div.message").show("slow").fadeOut(18000);
							$(".realperson-challenge").click();
							
						}
		};
		$(this).ajaxSubmit(options);
		return false;
	});
});