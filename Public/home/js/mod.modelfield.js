/* 
* @Author: cl
* @Date:   2015-10-11 20:43:13
* @Last Modified by:   cl
* @Last Modified time: 2015-10-11 20:43:15
*/

$(function(){
  $('[name=show_type]').change(function(){
    if($(this).val()=='5' || $(this).val()=='6' || $(this).val()=='7')
    {
      $('.single input').attr('disabled','disabled');
      $('.single').hide();
      $('.multiple input').removeAttr('disabled');
      $('.multiple').show();
    }
    else
    {
      $('.single input').removeAttr('disabled');
      $('.single').show();
      $('.multiple input').attr('disabled','disabled'); 
      $('.multiple').hide();
    }
  })
  $('[name=show_type]').trigger('change');


  $('.plus').click(function(){
      var html =  $(this).parents('tr').eq(0).clone();
      html.find('input').val('');
      html.find('span').removeClass('plus').addClass('minus').html('[-]');
      $(this).parents('table').eq(0).append(html);
  })
  $('.minus').live('click',function(){
      $(this).parents('tr').eq(0).remove();
  })
})