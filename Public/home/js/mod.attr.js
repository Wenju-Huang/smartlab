/* 
* @Author: cl
* @Date:   2015-10-11 20:29:07
* @Last Modified by:   cl
* @Last Modified time: 2015-10-11 20:29:17
*/

$(function(){

	$('.attr_value .plus').live('click',function(){
		var html = $(this).parents('tr').eq(0).clone();
		html.find('span').removeClass('plus').addClass('minus');
		html.find('span').html("[-]");
		html.find('input').val('');
		html.find('input:eq(0)').attr('name','attr_value_name[]');
		html.find('input:eq(1)').attr('name','attr_value[]');
		$(this).parents('table').eq(0).append(html);
	})

	

	$('.attr_value .minus').live('click',function(){
		$(this).parents('tr').eq(0).remove();
	})
})