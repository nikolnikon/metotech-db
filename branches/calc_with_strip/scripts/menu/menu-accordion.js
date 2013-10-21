$(function() {
	var metall_numbers = {
		"nihrom": 0,
		"fehral": 1,
		"nihrom-izol": 2,
		"titan": 3,
		"volfram": 4,
		"molibden": 5,
		"kobalt": 6,
		"termopary": 7,
		"nikel": 8,
		"monel": 9,
		"konstantan": 10,
		"splavy": 11,
		"tvsplavy": 12,
		"nergstal": 13,
		"garsplavy": 14
	};
	var metall = $("body").data("metall");
	var active_metall_number = metall_numbers[metall];
	if (active_metall_number === undefined)
		active_metall_number = false;
	
	$( "#accordion" ).accordion({
		collapsible: true,
		heightStyle: "content",
		active: active_metall_number
	});
	var active = $( "#accordion" ).accordion( "option", "active" );
	if (active !== false) {
		$("[name = hidden]").eq(active).css("display", "block");
	}
	$("[name = v]").each(function(index, element) {
		$(element).css("display", "none");
	});
});
