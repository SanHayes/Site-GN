
$(function(){
	//建仓止盈加
	$('.profit-right').on('click',function () {
		var $numValue = $(this).prev().val();
		$numValue = parseInt($numValue);
		if ($numValue < max_yk) {
			$(this).prev().val(parseInt($numValue) + parseInt(step));
		}
	});

	//建仓止盈减
	$('.profit-left').on('click',function () {
		var $numValue = $(this).next().val();
		$numValue = parseInt($numValue);
		if ($numValue > min_yk) {
			$(this).next().val(parseInt($numValue) - parseInt(step));
		} else {
			$(this).next().val(min_yk);
		}
	});

	//建仓止损加
	$('.loss-right').on('click',function () {
		var $numValue = $(this).prev().val();
		$numValue = parseInt($numValue);
		if ($numValue < max_yk) {
			$(this).prev().val(parseInt($numValue) + parseInt(step));
		}
	});

	//建仓止损减
	$('.loss-left').on('click',function () {
		var $numValue = $(this).next().val();
		$numValue = parseInt($numValue);
		if ($numValue > min_yk) {
			$(this).next().val(parseInt($numValue) - parseInt(step));
		} else {
			$(this).next().val(min_yk);
		}
	});
});