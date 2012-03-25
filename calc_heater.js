/**
 * 
 */

$(function() {
	var loc = window.location.href;
	// заполнение combobox "Материал" и "Размещение"
	var clbck = function (response, status, xhr) {
		if (status == "error") {
		    var msg = "Sorry but there was an error: ";
		    alert(msg + xhr.status + " " + xhr.statusText);
	  	}
		else if (status == "success") {
			$("select[name = 'material']").change();
		}
	};
	
	$("form[name='heater_calc_res'] p").each(function(index) {
		$(this).hide();
	});
	
	$("select[name = 'material']").load("prepare_heater_form.php", {select_name: "material"}, clbck);
	$("select[name = 'placement']").load("prepare_heater_form.php", {select_name: "placement"}, clbck);
	
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
	})
	.change();
	
	// обработка выбора материала нагревателя
	$("select[name = 'material']").change(function() {
		$("select[name = 'temp_solid']").empty();
		$("select[name = 'temp_heater']").empty();
		
		var arr = ["max_temp", "resistivity", "density"];
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
		$("form[name='heater_calc_res'] p").each(function(index) {
			$(this).hide("slow");
		});
	})
	.change();
	
	// отправка данных на сервер
	$("form[name='heater_calc']").submit(function() {
		//$("form[name = heater_calc_res']").empty();
		window.location = loc + "#result";
		var params = $("form[name='heater_calc'] input[name!='pgrid_conn'], form[name='heater_calc'] select[name!='pgrid_conn']").serialize();
		console.log(params);
		$.getJSON("calc_heater.php", params, function(result, textStatus, jqXHR){
			var pgrid = $("select[name='pgrid'] option:selected").val();
			$("input[name='diameter']").val(result.D);
			$("input[name='length']").val(result.L);
			$("input[name='mass']").val(result.M);
			
			$("#diameter").show("slow");
			$("#length").show("slow");
			$("#mass").show("slow");
			if (pgrid == 1) {
				$("#total_length").hide("slow");
				$("#total_mass").hide("slow");
			}
			else if (pgrid == 3) {
				$("#total_length").show("slow");
				$("#total_mass").show("slow");
				$("input[name='total_length']").val(result.L * 3);
				$("input[name='total_mass']").val((result.M * 3).toFixed(1));
			}
			console.log(window.location.pathname);
			console.log(window.location.href);
			console.log(tmp);
		});
		return false;
	});
});