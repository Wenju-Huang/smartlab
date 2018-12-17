/* 
* @Author: cl
* @Date:   2015-10-11 20:44:37
* @Last Modified by:   cl
* @Last Modified time: 2015-10-11 21:52:31
*/

$(function(){
	
    Wind.use('validate', 'ajaxForm', 'artDialog', function () {
        var form = $('form.J_ajaxForms');
        //ie处理placeholder提交问题
        if ($.browser.msie) {
            form.find('[placeholder]').each(function () {
                var input = $(this);
                if (input.val() == input.attr('placeholder')) {
                    input.val('');
                }
            });
        }
        //表单验证开始
        var baseValidate = {
			//是否在获取焦点时验证
			onfocusout:false,
			//是否在敲击键盘时验证
			onkeyup:false,
			//当鼠标掉级时验证
			onclick: false,
            //验证错误
            showErrors: function (errorMap, errorArr) {
				//errorMap {'name':'错误信息'}
				//errorArr [{'message':'错误信息',element:({})}]
				try{
					$(errorArr[0].element).focus();
					art.dialog({
						id:'error',
						icon: 'error',
						lock: true,
						fixed: true,
						background:"#CCCCCC",
						opacity:0,
						content: errorArr[0].message,
						cancelVal: '确定',
						cancel: function(){
							$(errorArr[0].element).focus();
						}
					});
				}catch(err){
				}
            },
            //验证规则
           
            //给未通过验证的元素加效果,闪烁等
            highlight: false,
            //是否在获取焦点时验证
            onfocusout: false,
            //验证通过，提交表单
            submitHandler: function (forms) {
                $(forms).ajaxSubmit({
                    url: form.attr('action'), //按钮上是否自定义提交地址(多按钮情况)
                    dataType: 'json',
                    beforeSubmit: function (arr, $form, options) {
                        
                    },
                    success: function (data, statusText, xhr, $form) {
                        if(data.status){
							resultTip({error:0,msg:data.info});
                          	setInterval(function()
                            {
                              window.location.href=data.url;
                            },1000);
						}else{
							isalert(data.info);
						}
                    }
                });
            }
        };
        var validate = $.extend({},baseValidate,userValidate);
        
        form.validate(validate);
    });
});


function add_group(obj)
{
  var html = $(obj).parent('tr').clone();
    html.find('th').attr('onclick','del_group(this)').html('[-]');
  html.find('select option').removeAttr('selected');
  $(obj).parents('table').eq(0).append(html);

}
function del_group(obj)
{
  
  $(obj).parent('tr').eq(0).remove();
}
