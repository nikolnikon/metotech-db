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
			$("select[name = 'heater_type']").change();
		}
	};
	
	function hide_result(is_slow) {
		$("form[name='heater_calc_res'] p").each(function(index) {
			$(this).hide(is_slow);
		});
		$("form[name='heater_calc_res']").hide(is_slow);
		$("#result").hide(is_slow);
		$("#result_legend").hide(is_slow);
	}
	
	hide_result("");
	$("div#error").hide();
	
	$("select[name = 'material']").load("../../heater_calc/prepare_heater_form.php", {select_name: "material"}, clbck);
	$("select[name = 'placement']").load("../../heater_calc/prepare_heater_form.php", {select_name: "placement"}, clbck);
	
	// валидация данных на форме
	var validator = $("form[name='heater_calc']").validate({
		rules: {
			power: {
				required: true,
				number: true,
				min: 1,
				max: 500000
			}
		},
		messages: {
			power: {
				required: "Не задана мощность печи",
				number: "Введите корректное значение мощности",
				min: "Введите значение мощностти в диаппазоне от 1 до 500000 Вт",
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
		var max_power_1 = 6999; // максимальная мощность при однофазном подключении
		var min_power_3 = 7000; // минимальная мощность при трехфазном подключении
		var max_power_3 = 500000; // максимальная мощность при трехфазном подключении
		var power = $(this).val(); // введенная мощность
		
		if (power <= max_power_1) {
			$("[name = 'pgrid']").val("1");
			$("[name = 'pgrid']").change();
			$("select[name='pgrid']").prop("disabled", false);
		}
		else if (power >= min_power_3 && power <= max_power_3) {
			$("[name = 'pgrid']").val("3");
			$("[name = 'pgrid']").change();
			$("select[name='pgrid']").prop("disabled", true);
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
		}
		else if (pgrid_type == 1) { // если однофазное подключение
			$("p#pgrid_conn").hide("slow");
			$("input[name = 'voltage']").val("220");
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
	
	// обработка выбора круглого/плоского нагревателя
	$("[name = 'heater_type']").change(function() {
		var heater_type = $("select[name = 'heater_type'] option:selected").val();
		var options = $("select[name = 'placement'] option");
		var first_placement_index = -1;
		
		if (heater_type == 2) { // если плоский нагреватель
			$("p#sizes_type").show("slow");
			options.each(function(index){
				if ($(this).data("type") == 1)
					$(this).hide();
				else if ($(this).data("type") == 2) {
					if (first_placement_index < 0)
						first_placement_index = index;
					$(this).show();
				}
			});
			$("input[name='size_relation']").rules("add", {
														range: [5, 15],
														messages: {
														range: jQuery.format("Введите отношение ширины ленты к ее толщине от {0} до {1}")
														}
			});
			$("input[name = 'size_relation']").val("");
		}
		else if (heater_type == 1) { // если круглый нагреватель
			$("p#sizes_type").hide("slow");
			$("p#size_relation").hide();
			$("p#custom_thickness").hide("slow");
			$("p#custom_width").hide("slow");
			$("input[name = 'size_relation']").val("0");
			options.each(function(index){
				if ($(this).data("type") == 2)
					$(this).hide();
				else if ($(this).data("type") == 1) {
					if (first_placement_index < 0)
						first_placement_index = index;
					$(this).show();
				}
			});
			$("input[name='size_relation']").rules("remove", "min max");
		}
		
		$("select[name = 'placement']").prop("selectedIndex", first_placement_index);
		$("select[name = 'placement']").change();
	})
	.change();
	
	// обработка выбора рассчитывать оптимальные размеры ленты или использовать произвольные?
	$("[name = 'sizes_type']").change(function() {
		var sizes_type = $("select[name = 'sizes_type'] option:selected").val();
		//var heater_type = $("select[name = 'heater_type'] option:selected").val();
		
		if (sizes_type == 1) { // если рассчитывать оптимальные размеры
			$("p#custom_thickness").hide();
			$("p#custom_width").hide();
			$("p#size_relation").show("slow");
		}
		else if (sizes_type == 2) { // если использовать размеры, заданные пользователем
			$("p#size_relation").hide();
			$("p#custom_thickness").show("slow");
			$("p#custom_width").show("slow");
		}
	});
	
	// обработка снятия/установки флажка "Редактировать отношение"
	$("input[name='size_relation_enabled']").change(function(){
		if ($(this).prop("checked")) { // если установили флажок
			$("input[name='size_relation']").prop("disabled", false);
			$("input[name='size_relation']").val(10); // устанавливаем среднее арифметическое между 5 и 15
		}
		else { // если сняли флажок
			$("input[name='size_relation']").prop("disabled", true);
			$("input[name='size_relation']").val(""); 
		}
	})
	.change();
	
	$("input[name='standard_sizes_enabled']").change(function(){
		if ($(this).prop("checked")) { // если установили флажок
			$("select[name='standard_sizes']").prop("disabled", false);
		}
		else { // если сняли флажок
			$("select[name='standard_sizes']").prop("disabled", true);
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
	var is_pgrid_disabled = $("select[name='pgrid']").prop("disabled");
	$("form[name='heater_calc']").submit(function() {
		var params = $("form[name='heater_calc'] input[name!='pgrid_conn'], form[name='heater_calc'] select[name!='pgrid_conn'], p#custom_thickness input[name!='custom_thickness'], p#custom_width input[name!='custom_width']").serialize();
		console.log($("form[name='heater_calc'] input[name='custom_thickness']").data("hren"));
		console.log(params);
		
		var options = {
				url: '../../heater_calc/calc_heater.php',
				data: params,
				dataType: 'json',
				beforeSerialize: function() {
					$("select[name='temp_heater']").prop("disabled", false);
					$("select[name='pgrid']").prop("disabled", false);
					$("input[name='size_relation']").prop("disabled", false);
					$("input[name='voltage']").prop("disabled", false);
					var sizes_type = $("select[name = 'sizes_type'] option:selected").val();
					if (size_type == 2) { // если используются пользовательские размеры
						var custom_thickness = $("input[name='custom_thickness']").val();
						var custom_width = $("input[name='custom_width']").val();
						$("input[name='size_relation']").val(custom_width / custom_thickness);
					}
				},
				beforeSubmit: function(arr, $form, options) {
					return $form.valid();
				},
				success: function(result, statusText, xhr, $form) {
					//console.log(result);
					var pgrid = $("select[name='pgrid'] option:selected").val();
					var heater_type = $("select[name = 'heater_type'] option:selected").val();
					
					$("#result").show("slow");
					$("#result_legend").show("slow");
					$("form[name='heater_calc_res']").show("slow");
					if (heater_type == 1) { // если круглый нагреватель
						$("#options").hide();
						$("#standard_sizes").hide();
						$("#thickness").hide();
						$("#width").hide();
						$("input[name='diameter']").val(result.D);
						$("#diameter").show("slow");
						$("input[name='length']").val(result.L);
						$("input[name='mass']").val(result.M);
					}
					else if (heater_type == 2) { // если плоский нагреватель
						$("#diameter").hide();
						var option_cont = $("#options");
						option_cont.children().remove();
						$.each(result, function(key, value){
							if ($.isNumeric(key)) {
								option_cont.append('<br><input type="radio" name="options" id="option_' + key + '" value="' + key + '"><label for="option_' + key + '">' + value.A + 'x' + value.B + '</label>');
							}
						});
						$("[name = 'options']").change(function() {
							var sel_stripe = $("input[name = 'options']:checked").val();
							$("input[name='thickness']").val(result[sel_stripe].A);
							$("input[name='width']").val(result[sel_stripe].B);
							$("input[name='length']").val(result[sel_stripe].L);
							$("input[name='mass']").val(result[sel_stripe].M);
							
							if (pgrid == 3) {
								$("input[name='total_length']").val($("input[name='length']").val() * 3);
								$("input[name='total_mass']").val(($("input[name='mass']").val() * 3).toFixed(3));
							}
						});
						$("#option_0").prop("checked", true);
						$("[name = 'options']").change();
						$("#options").show("slow");
						$("#standard_sizes").show("slow");
						$("#thickness").show("slow");
						$("#width").show("slow");
					}
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
						$("input[name='total_length']").val($("input[name='length']").val() * 3);
						$("input[name='total_mass']").val(($("input[name='mass']").val() * 3).toFixed(3));
					}
					
					$("select[name='temp_heater']").prop("disabled", ! $("input[name='temp_heater_enabled']").prop("checked"));
					$("input[name='size_relation']").prop("disabled", ! $("input[name='size_relation_enabled']").prop("checked"));
					$("input[name='voltage']").prop("disabled", true);
					if (is_pgrid_disabled)
						$("select[name='pgrid']").prop("disabled", true);
				}
		};
		$(this).ajaxSubmit(options);
		return false;
	});
	
	// обработка выбора стандартного размера для ленты
	$("select[name='standard_sizes']").change(function(){
		$("select[name='temp_heater']").prop("disabled", false);
		$("select[name='pgrid']").prop("disabled", false);
		$("input[name='size_relation']").prop("disabled", false);
		$("input[name='voltage']").prop("disabled", false);
		var params = $("form[name='heater_calc'] input[name!='pgrid_conn'], form[name='heater_calc'] select[name!='pgrid_conn'], form[name='heater_calc'] input[name!='size_relation_enabled'], form[name='heater_calc'] input[name!='size_relation']").serialize();
		$("select[name='temp_heater']").prop("disabled", ! $("input[name='temp_heater_enabled']").prop("checked"));
		$("input[name='size_relation']").prop("disabled", ! $("input[name='size_relation_enabled']").prop("checked"));
		$("input[name='voltage']").prop("disabled", true);
		if (is_pgrid_disabled)
			$("select[name='pgrid']").prop("disabled", true);
						
		var a = $("select[name = 'standard_sizes'] option:selected").data("thickness");
		var b = $("select[name = 'standard_sizes'] option:selected").data("width");
		var m = Math.round(b/a);
		params += "&size_relation=";
		params += m;
		$.getJSON('../../heater_calc/calc_heater.php', params, function(result, statusText, xhr){
			// подставить выбранные стандартные толщину и ширину, длину взять из ответа, массу рассчитать
				$("input[name='thickness']").val(a);
				$("input[name='width']").val(b);
				$("input[name='length']").val(result[0].L);
				var dens = $("select[name = 'material'] option:selected").data("density");
				var mass =  dens * Math.pow(10, 3) * a * b * result[0].L * Math.pow(10, -6);
				mass = Math.round(mass);
				$("input[name='mass']").val(mass);
		});
	});
});
