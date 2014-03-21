$(function() {
	var show_msg = $("#calc_heater_button").html();
	var hide_msg = "Скрыть калькулятор";
	var cur_msg = show_msg;
	// run the currently selected effect
	function runEffect() {
		var options = {};

		// run the effect
		$( "#heater_effect" ).toggle( "blind", options, 500 );
	};
	
	$( "#calc_heater_button" ).hover(
		function() {
			$(this).css("color", "#ff9b25")
		},
		function() {
			$(this).css("color", "grey")
		}
	);
	
	// set effect from select menu value
	$( "#calc_heater_button" ).click(function() {
		runEffect();
		if (cur_msg == show_msg) {
			cur_msg = hide_msg;
			$(this).css("font-size", "10pt");
			$(this).css("color", "grey");
		}
		else {
			cur_msg = show_msg;
			$(this).css("font-size", "16pt");
			$(this).css("color", "#grey");
			$(this).css("float", "none");
		}
		$( "#calc_heater_button" ).html(cur_msg);
		return false;
	});
});
