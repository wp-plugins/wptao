jQuery(function(a){a("#get_item_info").click(function(){if(!wptao_js.login)return alert("请登录后再操作！"),!1;var b=a("#tbk_link").val();return b?(a("input[type='text'],input[type='hidden'],textarea").val(""),a("#tbk_link").val(b),a("#wptao_tips").html(""),jQuery.ajax({type:"GET",url:wptao_js.plugin_url+"/get_items.php?type=sign&link="+encodeURIComponent(b),success:function(c){if(!c)return alert("请填写插件授权码！"),!1;var d=wptao_js.api+"/get_items_detail.php?callback=?";a.getJSON(d,{u:encodeURIComponent(b),from:encodeURIComponent(wptao_js.blog_url),sign:c,c:"p",v:wptao_js.v},function(b){var c,d;b.title?(a("#tbk_link").val(b.url),b.tips&&a("#wptao_tips").html(b.tips),b.item_click&&a("#tbk_mm_link").val(b.item_click),b.shop_click&&a("#shop_url").val(b.shop_click),b.price&&a("#tbk_price").val(b.price),b.old_price&&a("#tbk_old_price").val(b.old_price),a("#post_title").val(b.title),b.image&&a("#imageURL").val(b.image),a("#tbk_mall").val(b.mall),b.desc&&a("#post_content").val(b.desc),b.shop_name&&a("#shop_name").val(b.shop_name),c=a("#tbk_tag").val(),d="",b.site&&(c.indexOf(b.site)>=0||(d+=b.site+",")),b.baoyou&&(c.indexOf(b.baoyou)>=0||(d+="包邮,")),a("#tbk_tag").val((d+c).replace(/,$/,""))):b.error&&alert(b.error)})}}),void 0):(alert("商品链接不能留空！"),!1)})});