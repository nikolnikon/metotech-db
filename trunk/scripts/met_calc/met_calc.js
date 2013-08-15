$(function() {
	var show_msg = $("#calc_button").html();
	var hide_msg = "Скрыть калькулятор";
	var cur_msg = show_msg;
	//$( "#calc_question" ).hide();
	// run the currently selected effect
	function runEffect() {
		var options = {};

		// run the effect
		$( "#effect" ).toggle( "blind", options, 500 );
	};
	
	$( "#calc_button" ).hover(
		function() {
			$(this).css("color", "#ff9b25")
		},
		function() {
			$(this).css("color", "grey")
		}
	);
	
	// set effect from select menu value
	$( "#calc_button" ).click(function() {
		runEffect();
		if (cur_msg == show_msg) {
			cur_msg = hide_msg;
			$(this).css("font-size", "10pt");
			$(this).css("color", "grey");
			// $(this).css("float", "left");
			$("#calc_question").css("float", "none");
			$("#calc_question").show();
		}
		else {
			cur_msg = show_msg;
			$(this).css("font-size", "16pt");
			$(this).css("color", "#grey");
			$(this).css("float", "none");
			$ ( "#calc_question").hide();
		}
		$( "#calc_button" ).html(cur_msg);
		return false;
	});
	
	$("#refs_calc_button").click(function() {
		$( ".calc_button" ).click();
	});
	
	$( "#calc_question_form" ).dialog({
		autoOpen: false,
		height: 650,
		width: 450,
		modal: true,
		title: "Задать вопрос по калькулятору \"Металлы и сплавы\"",
		buttons: {
			"Отправить": function() {
				$("#pismo").submit();
			},
			"Отменить": function() {
				$( this ).dialog( "close" );
			}
		},
		position: {
			my: "left center",
			at: "right center",
			of: "#effect"
		}
	});

$( "#calc_question" ).click(function() {
	$( "#calc_question_form" ).dialog( "open" );
	//return false;
  });
});
