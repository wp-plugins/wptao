<?php
include "../../../wp-config.php";
if (!current_user_can('edit_posts')) {
	exit;
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
<form action="" name="post" method="post" id="form-table">
<div id="wptao_tips"></div>
<table class="form-table">
  <tr>
    <th style="width:18%;"> <label for="tbk_link">商品链接*</label>
    </th>
    <td><input type="text" name="tbk[link]" id="tbk_link" value="" size="30" tabindex="30" style="width: 90%;" />
	<p class="description"><input type="hidden" name="tbk[mall]" id="tbk_mall" value="" /><input type="hidden" name="tbk[status]" id="tbk_status" value="1" /><input type="hidden" name="tbk[sellerId]" id="tbk_sellerId" value="" /><input type="button" id="get_item_info" title="获取信息" value="获取信息" /> 支持淘宝网、天猫、京东、苏宁、当当网等自动获取</p></td>
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
    <td><input type="text" name="tbk[image]" id="imageURL" value="" size="30" tabindex="30" style="width: 90%;" /></td>
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
<script type="text/javascript">var wptao_js = <?php echo json_encode(wptao_js_var());?>;</script>
<script type='text/javascript' src='js/jquery.min.js?ver=1.2.6'></script>
<script type='text/javascript' src='js/jquery.page.js?ver=<?php echo WPTAO_V;?>'></script>
</body>
</html>