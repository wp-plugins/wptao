<?php
include "../../../wp-config.php";
if (!is_user_logged_in()) {
	wp_die('<a href=' . wp_login_url('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . '>请点击这里登录后操作。</a>');
} elseif (!current_user_can('edit_posts')) {
	wp_die(__('You do not have sufficient permissions to access this page.'));
} 
?>
<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<title>获取淘宝客信息</title>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
<link rel='stylesheet' id='wptao-style-css'  href='css/css.css?ver=1.0' type='text/css' media='all' />
<body>
<!-- By WordPress淘宝客插件 http://blogqun.com/wptao.html -->
<form action="" name="post" method="post" id="form-table">
<div id="wptao_tips"></div>
<table class="form-table">
  <tr>
    <th style="width:18%;"> <label for="tbk_link">商品链接*</label>
    </th>
    <td><input type="text" name="tbk[link]" id="tbk_link" value="" size="30" tabindex="30" style="width: 90%;" />
	<p class="description"><input type="hidden" name="tbk[mall]" id="tbk_mall" /><input type="button" id="get_item_info" title="获取信息" value="获取信息" /> 支持淘宝/天猫/京东/国美/苏宁/当当/亚马逊/多麦等自动获取</p></td>
  </tr>
  <tr>
    <th style="width:18%;"> <label for="tbk_mm_link">商品推广链接</label>
    </th>
    <td><div id="get_alimama" style="max-width:510px;overflow:hidden;"></div><input type="text" name="tbk[mm_link]" id="tbk_mm_link" value="" size="30" tabindex="30" style="width: 90%;" placeholder="商品推广链接" /></td>
  </tr>
  <tr>
    <th style="width:18%;"> <label for="shop_name">店铺名称</label>
    </th>
    <td><input type="text" name="shop_name" id="shop_name" value="" size="30" tabindex="30" style="width: 90%;" /></td>
  </tr>
  <tr>
    <th style="width:18%;"> <label for="shop_url">店铺推广链接</label>
    </th>
    <td><input type="text" name="shop_url" id="shop_url" value="" size="30" tabindex="30" style="width: 90%;" placeholder="店铺推广链接" /></td>
  </tr>
  <tr>
    <th style="width:18%;"> <label for="post_title">商品标题</label>
    </th>
    <td><input type="text" name="post_title" id="post_title" value="" size="30" tabindex="30" style="width: 90%;" /></td>
  </tr>
  <tr>
    <th style="width:18%;"> <label for="post_content">商品描述</label>
    </th>
    <td><textarea id="post_content" rows="5" name="post_content" style="width: 90%;"></textarea></td>
  </tr>
  <tr>
    <th style="width:10%;"> <label for="imageURL">商品图片</label>
    </th>
    <td><input type="text" name="tbk[image]" id="imageURL" value="" size="30" tabindex="30" style="width: 90%;" /><div id="wptao_preview"></div></td>
  </tr>
  <tr>
    <th style="width:18%;"> <label for="tbk_price">商品价格</label>
    </th>
    <td><input type="text" name="tbk[price]" id="tbk_price" value="" size="30" tabindex="30" style="width: 90%;" /></td>
  </tr>
  <tr>
    <th style="width:18%;"> <label for="tbk_old_price">商品原价</label>
    </th>
    <td><input type="text" name="tbk[old_price]" id="tbk_old_price" value="" size="30" tabindex="30" style="width: 90%;" /></td>
  </tr>
  <tr>
    <th style="width:18%;"> <label for="tbk_tag">标签</label>
    </th>
    <td><input type="text" name="tbk[tag]" id="tbk_tag" value="" size="30" tabindex="30" style="width: 90%;" />
	</td>
  </tr>
</table>
</form>
<script type="text/javascript">
var wptao_data,wptao_js = <?php echo json_encode(wptao_js_var());?>;
(function(win,doc){ var s = doc.createElement("script"), h = doc.getElementsByTagName("head")[0]; if (!win.alimamatk_show) { s.charset = "gbk"; s.async = true; s.src = "http://a.alimama.cn/tkapi.js"; h.insertBefore(s, h.firstChild); }; var o = { pid: wptao_js.pid,/*推广单元ID，用于区分不同的推广渠道*/ appkey: "",/*通过TOP平台申请的appkey，设置后引导成交会关联appkey*/ unid: ""/*自定义统计字段*/ }; win.alimamatk_onload = win.alimamatk_onload || []; win.alimamatk_onload.push(o); })(window,document);
</script>
<script type='text/javascript' src='js/jquery.min.js?ver=1.2.6'></script>
<script type='text/javascript' src='js/jquery.page.js?ver=<?php echo WPTAO_V;?>'></script>
</body>
</html>