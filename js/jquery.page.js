/* v1.2.2 http://blogqun.com/wptao.html */
jQuery(function(a){a("#get_item_info").click(function(){if(!wptao_js.login)return alert("请登录后再操作！"),!1;var b=a("#tbk_link").val();return b?(a("input[type='text'],input[type='hidden'],textarea").val(""),a("#tbk_link").val(b),a("#wptao_tips").html(""),wptao_data||(wptao_data={title:"post_title",url:"tbk_link",item_click:"tbk_mm_link",shop_click:"shop_url",price:"tbk_price",old_price:"tbk_old_price",image:"imageURL",desc:"post_content",shop_name:"shop_name",postfee:"tbk_postfee",tags:"tbk_tag",id:"tbk_id",mall:"tbk_mall",site:"tbk_site"}),jQuery.ajax({type:"GET",url:wptao_js.plugin_url+"/get_items.php?type=sign&link="+encodeURIComponent(b),success:function(c){if(!c)return alert("请填写插件授权码！"),!1;var d=wptao_js.api+"/get_items_detail.php?callback=?";a.getJSON(d,{u:encodeURIComponent(b),from:encodeURIComponent(wptao_js.blog_url),sign:c,c:"p",cps:wptao_data.item_click||wptao_data.shop_click?1:0,desc:wptao_data.desc?1:0,v:wptao_js.v},function(b){if(b.title){b.tips&&a("#wptao_tips").html(b.tips);for(var c in wptao_data)b[c]&&a("#"+wptao_data[c]).val(b[c]);a("#tax_input option").length>0&&(a("#tax_input option[value='']").attr("selected",!0),a("#tax_input option").each(function(){return a(this).text().indexOf(b.site)>=0?(a(this).attr("selected",!0),!1):void 0}))}else b.error&&alert(b.error)})}}),void 0):(alert("商品链接不能留空！"),!1)})});