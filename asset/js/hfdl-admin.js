jQuery(function($){
	'use strict';

	let itemMax = $('#item_max').val();
	let itemCount = $('#items_content tr').length;
	let cont = $("#items_content");
	const lastItem = $("#items_content tr").last();

	$(".add-item").click(function () {
		add_items();
	});

	$(".del-item").click(function () {
		$("#items_content tr").each(function (i, elm) {
			if ($(".item_select", this).prop('checked')) {
				$(this).remove();
			}
		});
		if( 0 == $("#items_content tr").length ) {
			itemCount=0;
			itemMax =0;
			add_items();
		}else {
			$("#item_count").attr('value', $("#items_content tr").length);
		}
	});

	function add_items() {
		let tempCount = $('#items_content tr').length;
		let clone;
		if( 0 == tempCount) {
			clone = lastItem;
		} else{
			clone = $("#items_content tr").last().clone();
		}
		if (tempCount > itemCount) {
			itemCount = tempCount;
		} else {
			itemCount = itemCount + 1;
		}
		itemMax++;
		$("#item_count").attr('value', tempCount + 1);
		$("#item_max").attr('value', itemMax);
		clone.find('.registered').removeClass().addClass('registered item-'+itemMax);
		clone.find('.item_date').val('').attr('name','item_data-'+itemMax+'[date]');
		clone.find('.item_time').val('').attr('name','item_data-'+itemMax+'[time]');
		clone.find('.item-date-check').prop('checked',false).attr('name','item_data-'+itemMax+'[date-check]');
		cont.prepend(clone).find('[name="date-'+itemMax+'"]').focus();
	}

	/**
	 * チェックボックスの全選択処理
	 *
	 * @link https://agohack.com/checkbox-all-selected/
	 */
	$('.all_item_select').on('click',function(){
		$('#items_content .item_select').prop('checked',this.checked);
	});

	$("#items_content .item_select").on('click', function() {
		if ($('#items_content .item_select:checked').length == $('#items_content .item_select').length) {
		  // 全てのチェックボックスにチェックが入っていたら、「全選択」 = checked
			$('.all_item_select').prop('checked', true);
		} else {
		  // 1つでもチェックが入っていたら、「全選択」 = checked
			$('.all_item_select').prop('checked', false);
		}
	})
})