/**
 * 
 */

$(function() {
	var loc = window.location.href;
	// заполнение combobox "Материал" и "Размещение"
	var clbck = function (response, status, xhr) {
		console.log("status: " + status);
		console.log("xhr.status: " + xhr.status);
		console.log("xhr.statusText: " + xhr.statusText);
		console.log("response: " + response);
		if (status == "error") {
			$("div#error h1").text("Ошибка сервера");
			$("div#error p").html("Error code: " + xhr.status + "<br> Попробуйте перезагрузить страницу позже.");
			$("div#error").show("slow").fadeOut(7000);
	  	}
		else if (status == "success") {
			$("select[name = 'material']").change();
		}
	};
	
	function hide_result(is_slow) {
		$("form[name='heater_calc_res'] p").each(function(index) {
			$(this).hide(is_slow);
		});
		$("form[name='heater_calc_res']").hide(is_slow);
		$("#result").hide(is_slow);
	}
	
	hide_result("");
	$("div#error").hide();
	
	$("select[name = 'material']").load("prepare_heater_form.php", {select_name: "material"}, clbck);
	$("select[name = 'placement']").load("prepare_heater_form.php", {select_name: "placement"}, clbck);
	
	// валидация данных на форме
	$("form[name='heater_calc']").validate({
		rules: {
			power: {
				required: true
			}
		},
		messages: {
			power: {
				required: "Введите мощность печи"
			}
		},
		errorElement: "li"
	});
	
	// обработка выбора однонофазного/трехфазного подключения
	$("[name = 'pgrid']").change(function() {
		var pgrid_type = $("select[name = 'pgrid'] option:selected").val();
		if (pgrid_type == 3) { // если трехфазное подключение
			$("p#pgrid_conn").show("slow");
			$("[name = 'pgrid_conn']").change(function() {
				var conn_type = $("input[name = 'pgrid_conn']:checked").val();
				if (conn_type == "STAR") {
					$("input[name = 'voltage']").val("220");
				}
				else {
					$("input[name = 'voltage']").val("380");
				}
			})
			.change();
			$("input[name='power']").rules("remove", "min max");
			$("input[name='power']").rules("add", {
				range: [7000, 50000],
				messages: {
					range: jQuery.format("Введите мощность печи от {0} до {1} Ватт") 
				}
			});
			//$("input[name='power']").valid();
			/*if (! $("input[name='power']").valid()) {
				$("button").attr("disabled", "disabled");
			}
			else {
				$("button").removeAttr("disabled");
			}*/
		}
		else if (pgrid_type == 1) { // если однофазное подключение
			$("p#pgrid_conn").hide("slow");
			$("input[name = 'voltage']").val("220");
			$("input[name='power']").rules("remove", "min max");
			$("input[name='power']").rules("add", {
				range: [1000, 6999],
				messages: {
					range: jQuery.format("Введите мощность печи от {0} до {1} Ватт")
				}
			});
			//$("input[name='power']").valid();
			/*if (! $("input[name='power']").valid()) {
				$("button").attr("disabled", "disabled");
			}
			else {
				$("button").removeAttr("disabled");
			}*/
		}
	})
	.change();
	
	// обработка выбора материала нагревателя
	$("select[name = 'material']").change(function() {
		$("select[name = 'temp_solid']").empty();
		$("select[name = 'temp_heater']").empty();
		
		var arr = ["resistivity", "density"];
		$.each(arr, function(key, par) {
			var value = $("select[name = 'material'] option:selected").data(par);
			if (value === undefined) {
				value = "";
			}
			$("[name = '"+par+"']").val(value);
		});
		
		var temps = $("select[name = 'material'] option:selected").data("htemps");
			if (temps !== undefined) {
			var arr_temps = temps.split(",");
			var options;
			$.each(arr_temps, function(key, val){
				options += "<option value=\"" + val + "\">";
				options += val;
				options += "</option>\n";
			});
			//console.log(options);
			$("select[name='temp_heater']").append(options);
			$("select[name='temp_heater']").change();
		}
	})
	.change();
	
	// обработка выбора температуры печи (нагревателя)
	$("select[name='temp_heater']").change(function() {
		$("select[name='temp_solid']").empty();
		var temp_heater = $("select[name = 'temp_heater'] option:selected").val();
		console.log("temp_heater: "+temp_heater);
		if (temp_heater !== undefined) {
			var temps = $("select[name = 'material'] option:selected").data("stemps");
			console.log(temps);
			if (temps !== undefined) {
				var arr_temps = temps.split(",");
				var options;
				$.each(arr_temps, function(key, val){
					if (parseInt(val) < parseInt(temp_heater)) {
						options += "<option value=\"" + val + "\">";
						options += val;
						options += "</option>\n";
					}
				});
				console.log(options);
				$("select[name='temp_solid']").append(options);
			}	
		}
	})
	.change();
	
	// если пользователь меняет исходные данные, то результаты предыдущего расчета скрываются
	$("form[name='heater_calc'] select, form[name='heater_calc'] input").change(function() {
		hide_result("slow");
	})
	.change();
	
	// отправка данных на сервер
	$("form[name='heater_calc']").submit(function() {
		var params = $("form[name='heater_calc'] input[name!='pgrid_conn'], form[name='heater_calc'] select[name!='pgrid_conn']").serialize();
		console.log(params);
		var options = {
				url: 'calc_heater.php',
				data: params,
				dataType: 'json',
				beforeSubmit: function(arr, $form, options) {
					return $form.valid();
				},
				success: function(result, statusText, xhr, $form) {
					console.log(result);
					var pgrid = $("select[name='pgrid'] option:selected").val();
					$("input[name='diameter']").val(result.D);
					$("input[name='length']").val(result.L);
					$("input[name='mass']").val(result.M);
					
					$("#result").show("slow");
					$("form[name='heater_calc_res']").show("slow");
					window.location = loc + "#result";
					$("#diameter").show("slow");
					$("#length").show("slow");
					$("#mass").show("slow");
					if (pgrid == 1) {
						$("#total_length").hide("slow");
						$("#total_mass").hide("slow");
						$("#total_note").hide("slow");
					}
					else if (pgrid == 3) {
						$("#total_length").show("slow");
						$("#total_mass").show("slow");
						$("#total_note").show("slow");
						$("input[name='total_length']").val(result.L * 3);
						$("input[name='total_mass']").val((result.M * 3).toFixed(1));
					}
				}
		};
		$(this).ajaxSubmit(options);
		
		/*$.getJSON("calc_heater.php", params, function(result, textStatus, jqXHR){
			var pgrid = $("select[name='pgrid'] option:selected").val();
			$("input[name='diameter']").val(result.D);
			$("input[name='length']").val(result.L);
			$("input[name='mass']").val(result.M);
			
			$("#result").show("slow");
			$("form[name='heater_calc_res']").show("slow");
			window.location = loc + "#result";
			$("#diameter").show("slow");
			$("#length").show("slow");
			$("#mass").show("slow");
			if (pgrid == 1) {
				$("#total_length").hide("slow");
				$("#total_mass").hide("slow");
				$("#total_note").hide("slow");
			}
			else if (pgrid == 3) {
				$("#total_length").show("slow");
				$("#total_mass").show("slow");
				$("#total_note").show("slow");
				$("input[name='total_length']").val(result.L * 3);
				$("input[name='total_mass']").val((result.M * 3).toFixed(1));
			}
		});*/
		return false;
	});
});
