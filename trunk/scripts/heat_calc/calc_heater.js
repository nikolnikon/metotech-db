$(function() {
	var loc = window.location.href;
	// всплывающие подсказки
	$('[data-tooltip!=""]').qtip({
		content: {
			attr: 'data-tooltip',
			title: {
				text: function(api) {
					return $(this).attr('name');
				}
			}
		},
		hide: {
			fixed: true,
			delay: 300
		},
		position: {
			my: "bottom left",
			at: "top right"
		},
		style: {
			classes: 'qtip-light qtip-shadow',
		}
	});
	// заполнение combobox "Материал" и "Размещение"
	var clbck = function (response, status, xhr) {
		//console.log("status: " + status);
		//console.log("xhr.status: " + xhr.status);
		//console.log("xhr.statusText: " + xhr.statusText);
		//console.log("response: " + response);
		if (status == "error") {
			$("div#error h1").text("Ошибка сервера");
			$("div#error p").html("Error code: " + xhr.status + "<br> Попробуйте загрузить страницу позже.");
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
	var validator = $("form[name='heater_calc']").validate({
		rules: {
			power: {
				required: true,
				number: true,
				max: 500000
			}
		},
		messages: {
			power: {
				required: "Не задана мощность печи",
				number: "Введите корректное значение мощности",
				max: "Введите значение мощностти в диаппазоне от 1 до 500000 Вт"
			},
			material: {
				required: "Не задан материал нагревателя"
			},
			temp_heater: {
				required: "Не задана температура нагревателя"
			},
			temp_solid: {
				required: "Не задана температура нагреваемого изделия"
			},
			placement: {
				required: "Не заданы тип и размещение нагревателей"
			}
		},
		errorElement: "li"
	});
	
	// обработка ввода мощности
	$("[name = 'power']").change(function() {
		var min_power_1 = 1000; // минимальная мощность при однофазном подключении
		var max_power_1 = 6999; // максимальная мощность при однофазном подключении
		var min_power_3 = 7000; // минимальная мощность при трехфазном подключении
		var max_power_3 = 500000; // максимальная мощность при трехфазном подключении
		var power = $(this).val(); // введенная мощность
		
		if (power >= min_power_1 && power <= max_power_1) {
			$("[name = 'pgrid']").val("1");
			$("[name = 'pgrid']").change();
		}
		else if (power >= min_power_3 && power <= max_power_3) {
			$("[name = 'pgrid']").val("3");
			$("[name = 'pgrid']").change();
		}
	});
	
	// замена запятой на точку
	$("[name='power']").bind('keyup', ',', function(){
		this.value = this.value.replace(',', '.');
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
			// $("input[name='power']").rules("remove", "min max");
			// $("input[name='power']").rules("add", {
				// range: [7000, 500000],
				// messages: {
					// range: jQuery.format("Введите мощность печи от {0} до {1} Ватт") 
				// }
			// });
		}
		else if (pgrid_type == 1) { // если однофазное подключение
			$("p#pgrid_conn").hide("slow");
			$("input[name = 'voltage']").val("220");
			// $("input[name='power']").rules("remove", "min max");
			// $("input[name='power']").rules("add", {
				// range: [1000, 6999],
				// messages: {
					// range: jQuery.format("Введите мощность печи от {0} до {1} Ватт")
				// }
			// });
		}
		
		var v = validator.element("[name='power']");
		if (v) {
			$("[name='power'] li").hide();
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
			var temp_heater_select = $("select[name='temp_heater']");
			temp_heater_select.append(options);
			
			var temps_count = temp_heater_select.children("option").length;
			//console.log("temps_count: " + temps_count);
			if (temps_count >= 2)
				temp_heater_select.prop("selectedIndex", temps_count - 2);
			
			temp_heater_select.change();
		}
	})
	.change();
	
	// обработка снятия/установки флажка "Редактировать температуру нагревателя"
	$("input[name='temp_heater_enabled']").change(function(){
		if ($(this).prop("checked")) // если установили флажок
			$("select[name='temp_heater']").prop("disabled", false);
		else { // если сняли флажок
			$("select[name='temp_heater']").prop("disabled", true);
			temps_count = $("select[name='temp_heater']").children("option").length;
			if (temps_count >= 2)
				$("select[name='temp_heater']").prop("selectedIndex", temps_count - 2);
			else
				$("select[name='temp_heater']").prop("selectedIndex", 0);
			$("select[name='temp_heater']").change(); // инициируем смену значения температуры нагревателя
		}
	});
	
	// обработка выбора температуры печи (нагревателя)
	$("select[name='temp_heater']").change(function() {
		var prev_temp_solid = $("select[name='temp_solid'] option:selected").val();
		$("select[name='temp_solid']").empty();
		var temp_heater = $("select[name = 'temp_heater'] option:selected").val();
		//console.log("temp_heater: " + temp_heater);
		if (temp_heater !== undefined) {
			var temps = $("select[name = 'material'] option:selected").data("stemps");
			//console.log(temps);
			if (temps !== undefined) {
				var arr_temps = temps.split(",");
				var options;
				var index = -1;
				$.each(arr_temps, function(key, val){
					if (parseInt(val) < parseInt(temp_heater)) {
						options += "<option value=\"" + val + "\">";
						options += val;
						options += "</option>\n";
						
						if (parseInt(val) == prev_temp_solid)
							index = key;
					}
				});
				//console.log(options);
				//console.log("index: " + index);
				$("select[name='temp_solid']").append(options);
				if (index >= 0)
					$("select[name='temp_solid']").prop("selectedIndex", index);
				else
					$("select[name='temp_solid']").prop("selectedIndex", 0);
			}	
		}
	})
	.change();
	
	// если пользователь меняет исходные данные, то результаты предыдущего расчета скрываются
	$("form[name='heater_calc'] select, form[name='heater_calc'] input").change(function() {
		hide_result("slow");
	})
	.change();
	
	$("[name='power']").keydown(function(){
		hide_result("slow");
	});
	
	// отправка данных на сервер
	$("form[name='heater_calc']").submit(function() {
		var params = $("form[name='heater_calc'] input[name!='pgrid_conn'], form[name='heater_calc'] select[name!='pgrid_conn']").serialize();
		//console.log(params);
		var options = {
				url: 'calc_heater.php',
				data: params,
				dataType: 'json',
				beforeSerialize: function() {
					$("select[name='temp_heater']").prop("disabled", false);
					$("select[name='pgrid']").prop("disabled", false);
				},
				beforeSubmit: function(arr, $form, options) {
					return $form.valid();
				},
				success: function(result, statusText, xhr, $form) {
					//console.log(result);
					var pgrid = $("select[name='pgrid'] option:selected").val();
					$("input[name='diameter']").val(result.D);
					$("input[name='length']").val(result.L);
					$("input[name='mass']").val(result.M);
					
					$("#result").show("slow");
					$("form[name='heater_calc_res']").show("slow");
					//window.location = loc + "#result";
					$("#diameter").show("slow");
					$("#length").show("slow");
					$("#mass").show("slow");
					if (pgrid == 1) {
						$("span.formInfo #two, span.formInfo #three, span.formInfo #four").hide("slow");
						$("#total_length").hide("slow");
						$("#total_mass").hide("slow");
						$("#total_note").hide("slow");
					}
					else if (pgrid == 3) {
						$("span.formInfo #two, span.formInfo #three, span.formInfo #four").show("slow");
						$("#total_length").show("slow");
						$("#total_mass").show("slow");
						$("input[name='total_length']").val(result.L * 3);
						$("input[name='total_mass']").val((result.M * 3).toFixed(3));
					}
					
					$("select[name='temp_heater']").prop("disabled", ! $("input[name='temp_heater_enabled']").prop("checked"));
					$("select[name='pgrid']").prop("disabled", true);
				}
		};
		$(this).ajaxSubmit(options);
		return false;
	});
});
