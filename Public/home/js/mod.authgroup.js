/* 
* @Author: cl
* @Date:   2015-10-11 20:31:19
* @Last Modified by:   cl
* @Last Modified time: 2015-10-11 20:31:34
*/

$(function(){


  $('[type=checkbox]').click(function() {
 
      var value = $(this).val();
      if(value == 0)
        return ;

      if($(this).attr('checked')=='checked')
      { 
        $('[pid='+value+']').attr('checked',true);
        $('.'+value).attr('checked',true);
      }
      else
      {
         $('[pid='+value+']').attr('checked',false);
         $('.'+value).attr('checked',false);
      }


     
      // 子级别选中一个父级就要被选中
      var cid =  $(this).attr('class'); 
      $('.'+cid+':checked').length?$('#node'+cid).attr('checked',true):$('#node'+cid).attr('checked',false);

      var id  =  $(this).attr('pid');
      $('[pid='+id+']:checked').length?$('#node'+id).attr('checked',true):$('#node'+id).attr('checked',false);

  });


})