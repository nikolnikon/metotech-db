$(function() {
	$(".fancy_image").fancybox({
		prevSpeed  : 'slow',
		nextSpeed  : 'slow',
		nextClick  : true,
		type       : 'image',
		wrapCSS    : 'fancybox-custom',
		helpers	: {
			title	: {
				type: 'inside'
			},
			thumbs	: {
				width	: 70,
				height	: 50
			}
		}
	});
});