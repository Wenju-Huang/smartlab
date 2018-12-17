/* 
* @Author: cl
* @Date:   2015-10-11 20:40:53
* @Last Modified by:   cl
* @Last Modified time: 2015-10-11 20:40:55
*/

/* 登陆表单获取焦点变色 */
    	$(".login-form").on("focus", "input", function(){
            $(this).closest('.item').addClass('focus');
        }).on("blur","input",function(){
            $(this).closest('.item').removeClass('focus');
        });
        $('[name=password]').focus(function(){
             $.ajax({

                url:ajaxShowCodeUrl,
                dataType:'json',
                type:'post',
                success:function(res)
                {
                    if(res==1)
                        $('.showcode').show();
                    else
                        $('.showcode').hide();
                }
            })
        })
       
        //表单提交
        $(document)	

        .ajaxStart(function(){
        	$("button:submit").addClass("log-in").attr("disabled", true);
        })
        .ajaxStop(function(){
        	$("button:submit").removeClass("log-in").attr("disabled", false);
         });

    	$("form").submit(function(){
    		var self = $(this);
    		$.post(self.attr("action"), self.serialize(), success, "json");
    		return false;

    		function success(data){
    			if(data.status){
    				window.location.href = data.url;
    			} else {
    				if(data.show_code==1)
    					$('.showcode').show();
    				self.find(".check-tips").text(data.info);
    				//刷新验证码
    				$(".reloadverify").click();
    			}
    		}
    	});

		$(function(){
			//初始化选中用户名输入框
			$("#itemBox").find("input[name=username]").focus();
			//刷新验证码
			var verifyimg = $(".verifyimg").attr("src");
            $(".reloadverify").click(function(){
                if( verifyimg.indexOf('?')>0){
                    $(".verifyimg").attr("src", verifyimg+'&random='+Math.random());
                }else{
                    $(".verifyimg").attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());
                }
            });

            //判断浏览器是否支持 placeholder属性  
                function isPlaceholder(){  
                    var input = document.createElement('input');  
                    return 'placeholder' in input;  
                }  

                   
                if(!isPlaceholder())
                    {  
                        $("input").not("input[type='password']").each(//把input绑定事件 排除password框  
                            function(){  
                               var me = $(this);
 
                                var ph = me.attr('placeholder');
                         
                                if( ph && !me.val() )
                                {
                                    me.val(ph).css('color', '#aaa').css('line-height', me.css('height'));
                                }
                         
                                me.on('focus', function()
                                {
                                    if( me.val() === ph)
                                    {
                                        me.val(null).css('color', '');
                                    }
                         
                                }).on('blur', function()
                                {
                                    if( !me.val() )
                                    {
                                        me.val(ph).css('color', '#aaa').css('line-height', me.css('height'));
                                    }
                                });
                        }); 

                        //对password框的特殊处理1.创建一个text框 2获取焦点和失去焦点的时候切换  
                        var pwdField    = $("input[type=password]");  
                        var pwdVal      = pwdField.attr('placeholder');  
                        pwdField.after('<input id="pwdPlaceholder" type="text" value='+pwdVal+' autocomplete="off" />');  
                        var pwdPlaceholder = $('#pwdPlaceholder');  
                        pwdPlaceholder.show().css('color','#aaa');  
                        pwdField.hide();  
                          
                        pwdPlaceholder.focus(function(){  
                            pwdPlaceholder.hide();  
                            pwdField.show();  
                            pwdField.focus();  
                        });  
                          
                        pwdField.blur(function(){  
                            if(pwdField.val() == '') {  
                                pwdPlaceholder.show().css('color','#aaa');  
                                pwdField.hide();  
                            }  
                        });  
                          
        
                  
            }  



          
		});