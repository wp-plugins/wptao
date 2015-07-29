<?php
/*
Plugin Name: WordPress淘宝客插件
Author: 水脉烟香
Author URI: http://www.smyx.net/
Plugin URI: http://blogqun.com/wptao.html
Description: 匹配不同的淘宝客主题，实现自动填充商品信息及推广链接(CPS)。
Version: 1.2.1
*/

define('WPTAO_V', '1.2.1');

add_action('admin_menu', 'wptao_add_page');
function wptao_add_page() {
	add_options_page('淘宝客', '淘宝客', 'manage_options', 'wptao', 'wptao_do_page');
} 

add_action('plugin_action_links_' . plugin_basename(__FILE__), 'wptao_plugin_actions');
function wptao_plugin_actions($links) {
    $new_links = array();
    $new_links[] = '<a href="options-general.php?page=wptao">' . __('Settings') . '</a>';
    return array_merge($new_links, $links);
}

add_action('admin_menu', 'wptao_sidebox_info_add');
function wptao_sidebox_info_add() {
	if (function_exists('add_meta_box')) {
		add_meta_box('wp-sidebox-wptao-info', '获取商品信息', 'wptao_sidebox_info', 'post', 'normal', 'high');
		add_meta_box('wp-sidebox-wptao-info', '获取商品信息', 'wptao_sidebox_info', 'page', 'normal', 'high');
	} 
	if (function_exists('get_post_types')) { // 自定义文章类型
		if ($post_types = get_post_types(array('public' => true, '_builtin' => false), 'names', 'and')) {
			foreach($post_types as $type => $object) {
				add_meta_box('wp-sidebox-wptao-info', '获取商品信息', 'wptao_sidebox_info', $type, 'normal', 'high');
			} 
		} 
	} 
}
// 文章页面
function wptao_sidebox_info() {
	$wptao = get_option('wptao');
	$items = $wptao['item'];
?>
<script type="text/javascript">
var wptao_js = <?php echo json_encode(wptao_js_var());?>;
(function(win,doc){ var s = doc.createElement("script"), h = doc.getElementsByTagName("head")[0]; if (!win.alimamatk_show) { s.charset = "gbk"; s.async = true; s.src = "http://a.alimama.cn/tkapi.js"; h.insertBefore(s, h.firstChild); }; var o = { pid: wptao_js.pid,/*推广单元ID，用于区分不同的推广渠道*/ appkey: "",/*通过TOP平台申请的appkey，设置后引导成交会关联appkey*/ unid: ""/*自定义统计字段*/ }; win.alimamatk_onload = win.alimamatk_onload || []; win.alimamatk_onload.push(o); })(window,document);
jQuery(function($) {
    // 商品信息
    $("#wptao_get_item").click(function() {
        var link = $("#wptao_link").val();
        if (!link) {
            alert('商品链接不能留空！');
            return false;
        }
        jQuery.ajax({
            type: "GET",
			url: wptao_js.plugin_url + '/get_items.php?type=sign&link=' + encodeURIComponent(link),
            success: function(data) {
                if (data) {
                    var url = wptao_js.api + '/get_items_detail.php?callback=?';
					$.getJSON(url, {
						u: encodeURIComponent(link),
						from: encodeURIComponent(wptao_js.blog_url),
						sign: data,
						c: 'p',
						v:wptao_js.v,
						<?php echo (trim($items['desc'])) ? '' : 'desc:0';?>
					}, function(data) {
                        if (data.title) {
                            $("#wptao_link").val(data.url);
							$("#wptao_mall").val(data.mall);
							if (data.tips){
								$('#wptao_tips').html(data.tips);
							}
							<?php
							foreach ($items as $i => $item) {
								$item = trim($item);
								if ($item) {
									echo 'if (data.' . $i . ') {';
									echo '$("#' . $item . '").val(data.' . $i . ');';
									echo '}';
								} 
							} 
							?>
                        } else if (data.error) {
                            alert(data.error);
                        }
                    })
                } else {
					alert('请填写插件授权码！');
					return false;
				}
            }
        });
    });
});
</script>
<div id="wptao_tips"></div>
<table class="form-table">
  <tr>
    <th style="width:18%;"> <label for="wptao_link">商品链接*</label>
    </th>
    <td><input type="text" name="wptao_link" id="wptao_link" size="30" tabindex="30" style="width: 90%;" />
	<p class="description"><input type="hidden" name="wptao_mall" id="wptao_mall" /><input type="button" id="wptao_get_item" title="获取信息" value="获取信息" /> 支持淘宝网、天猫、京东、苏宁、当当网等自动获取</p>
	</td>
  </tr>
  <?php do_action('wptao_sidebox_tr', $items);?>
</table>
<?php
} 
// 设置 Setting
function wptao_do_page() {
	if (isset($_POST['wptao_options'])) {
		$authorize_code = trim($_POST['authorize_code']);
		if ($authorize_code) {
			if (substr($authorize_code, -4) == 'WPMU') {
				$authorizecode = substr($authorize_code, 0, -4);
				$is_wpmu = 1;
			} else {
				$authorizecode = $authorize_code;
				$is_wpmu = '';
			} 
			$_POST['wptao']['code'] = array('apikey' => substr($authorizecode, 0, -32), 'secret' => substr($authorizecode, -32), 'wpmu' => $is_wpmu, 'authorize_code' => $authorize_code);
		} 
		update_option("wptao", $_POST['wptao']);
	}
	$wptao = get_option('wptao');
	if (!$wptao) $wptao = array('open' => 1, 'item' => array());
	$plugin_url = plugins_url('wptao');
	if (is_multisite()) {
		$code = get_site_option('wptao_code');
		if ($code && is_array($code) && $code['apikey'] == DOMAIN_CURRENT_SITE) {
			$is_network = true;
			if (!$code['wpmu'] && strpos($_SERVER["HTTP_HOST"], $code['apikey']) === false) {
				$is_network = false;
			}
		}
	}
?>
<script type="text/javascript">
function add_value(i,v){document.getElementById(i).value=v.innerHTML;}
</script>
<div class="wrap">
  <h2>淘宝客<code>v<?php echo WPTAO_V;?></code> <code><a target="_blank" href="http://blogqun.com/wptao.html">官网</a></code> <code><a target="_blank" href="http://shang.qq.com/wpa/qunwpa?idkey=5dd1c3ec6a1faf9dd3586b4d76e0bb32073baa09a55d9f76f433db393f6451a7">QQ群讨论:77434617</a></code></h2>
  <p>说明：本插件必须与您正在使用淘宝客相关的主题/插件配合使用，使用前请根据您的主题/插件填写输入框节点(id)，可以在发布页查看网页源代码获得。</p>
  <form method="post" action="">
	<?php wp_nonce_field('wptao-options');?>
	<h3>基本设置</h3>
	<table class="form-table">
		<?php if (!$is_network) {
			if (!$wptao['code']['authorize_code']) {
				$blogurl = get_bloginfo('url');
				$time = time();
				$getTestCode = 'http://opent.blogqun.com/test/getcode.php?id=170&url=' . urlencode($blogurl) . '&sign=' . md5($blogurl . $time) . '&t=' . $time . '&v=' . WPTAO_V;
				$getTestCode = ' <a target="_blank" href="' . $getTestCode . '">申请测试</a>';
			} 
		?>
        <tr>
          <td width="200" valign="top"><label for="wptao_code">填写插件授权码（<a target="_blank" href="http://blogqun.com/wptao.html">购买</a>）：</label></td>
          <td><input type="text" name="authorize_code" id="wptao_code" size="30" value="<?php echo $wptao['code']['authorize_code'];?>"><?php echo $getTestCode;?>
		  <?php if (is_multisite()) echo '<p class="description">您正在使用WPMU，您可以在 管理网络 -> 设置 -> <a target="_blank" href="' . admin_url('network/settings.php?page=wptao') . '">淘宝客</a> 填写插件授权码。<a href="http://blogqun.com/wptao.html" target="_blank">如何获得授权码</a></p>';?></td>
        </tr>
		<?php } ?>
		<tr>
          <td width="200" valign="top"><label for="wptao_pid">阿里妈妈-淘点金推广单元ID</label></th>
		  <td><input type="text" id="wptao_pid" name="wptao[pid]" size="30" value="<?php echo $wptao['pid'];?>" /> <a target="_blank" href="http://blogqun.com/wptao.html#pid">如何获取？</a></td>
		</tr>
		<tr>
          <td width="200" valign="top"><label for="wptao_unionId">京东-联盟ID</label></th>
		  <td><input type="text" id="wptao_unionId" name="wptao[unionId]" size="30" value="<?php echo $wptao['unionId'];?>" /> <a target="_blank" href="http://ww2.sinaimg.cn/large/62579065gw1eu92xormivj20fl05674l.jpg">查看</a><br />位于【京东联盟】-【<a target="_blank" href="http://media.jd.com/master/account/center">结算中心</a>】</td>
		</tr>
		<tr>
          <td width="200" valign="top"><label for="wptao_webId">京东-网站ID</label></th>
		  <td><input type="text" id="wptao_webId" name="wptao[webId]" size="30" value="<?php echo $wptao['webId'];?>" /> <a target="_blank" href="http://ww1.sinaimg.cn/large/62579065gw1eu92xp7q1wj20ef08ndgv.jpg">查看</a><br />位于【京东联盟】-【<a target="_blank" href="http://media.jd.com/myadv/web">推广管理</a>】</td>
		</tr>
		<tr>
          <td width="200" valign="top"><label for="wptao_jd_token">京东-Access token</label></th>
		  <td><input type="text" id="wptao_jd_token" name="wptao[jd_token]" size="30" value="<?php echo $wptao['jd_token'];?>" /> <a target="_blank" href="http://open.blogqun.com/oauth/jd.php">去获取</a></td>
		</tr>
		<tr>
          <td width="200" valign="top"><label for="wptao_dangdang_from">当当网-联盟ID</label></th>
		  <td><input type="text" id="wptao_dangdang_from" name="wptao[dangdang_from]" size="30" value="<?php echo $wptao['dangdang_from'];?>" /> <a target="_blank" href="http://blogqun.com/wptao.html#dangdang">如何获取？</a></td>
		</tr>
		<tr>
          <td width="200" valign="top"><label for="wptao_suning_userId">苏宁易购-userId</label></th>
		  <td><input type="text" id="wptao_suning_userId" name="wptao[suning_userId]" size="30" value="<?php echo $wptao['suning_userId'];?>" /> <a target="_blank" href="http://blogqun.com/wptao.html#suning">如何获取？</a></td>
		</tr>
		<tr>
          <td width="200" valign="top"><label for="wptao_open">获取商品信息</label></td>
		  <td><label><input type="checkbox" id="wptao_open" name="wptao[open]" value="1" <?php if($wptao['open']) echo "checked "; ?>>添加到撰写新文章/编辑文章 页面</label></td>
		</tr>
		<tr>
          <th scope="row">商品信息:</th>
		  <td>输入框的节点id, 如果没有请留空: <br />比如：<code>&lt;input name="xxx" id="<span style="color:blue">abc</span>" /&gt;</code>，<code>abc</code>即为我们要的节点id</td>
		</tr>
<?php
$options = array('link' => array('商品链接', ''),
	'item_click' => array('商品推广链接（CPS）', ''),
	'shop_name' => array('店铺名称', ''),
	'shop_click' => array('店铺推广链接（CPS）', ''),
	'title' => array('商品标题', '如果对应【文章标题】，可以填写<code><a href="javascript:;" onclick="add_value(\'wptao_title\',this)">titlewrap input</a></code>'),
	'desc' => array('商品描述', '如果对应【文章内容】，可以填写<code><a href="javascript:;" onclick="add_value(\'wptao_desc\',this)">wp-content-editor-container textarea</a></code>'),
	'image' => array('商品图片', ''),
	'price' => array('商品价格', ''),
	'old_price' => array('商品原价', ''),
	);

foreach ($options as $key => $value) {
	echo '<tr><td width="200" valign="top"><label for="wptao_' . $key . '">' . $value[0] . '</label></td><td><label>#<input id="wptao_' . $key . '" name="wptao[item][' . $key . ']" type="text" value="' . $wptao['item'][$key] . '" /></label>';
	echo $value[1] ? '<p class="description">' . $value[1] . '</p>' : '';
	echo '</td></tr>';
} 
?>
	</table>
	<p class="submit">
	  <input type="submit" name="wptao_options" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
  </form>
<p>PS:测试页面：<a target="_blank" href="<?php echo $plugin_url;?>/get_info.php"><?php echo $plugin_url;?>/get_info.php</a></p>
<p>PS:如果您不确定您的淘宝客主题或者插件是否支持，或者您不懂配置节点，<a target="_blank" href="http://blogqun.com/wptao.html#inputid">先看教程</a>，还是不懂可以联系我（收费<code>10</code>RMB）。<br />如果您的输入框没有节点id，可以联系我改造（收费<code>50</code>RMB）。<br />如果您的主题有爆料功能，但是需要添加自动获取商品信息的按钮，可以联系我改造（收费<code>80</code>RMB）。<br />如果您的主题没有爆料功能，可以联系我定制（收费<code>150</code>RMB）<br />联系QQ：<code>3249892</code>，E-mail: <code>smyx@qq.com</code></p>
</div>
<?php
} 
// js var
function wptao_js_var() {
	$wptao = get_option('wptao');
	$var = array('pid' => $wptao['pid'],
		'v' => WPTAO_V,
		'api' => (!$wptao['code']['authorize_code'] || substr($wptao['code']['authorize_code'], -4) == 'TEST') ? 'http://opent.blogqun.com/test/shop' : 'http://open.blogqun.com/shop',
		'blog_url' => get_bloginfo('url'),
		'plugin_url' => plugins_url('wptao'),
		'login' => is_user_logged_in() ? true : false
		);
	return $var;
}
if (!function_exists('key_authcode')) {
	function key_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
		$ckey_length = 4;
		$key = ($key) ? md5($key) : '';
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), - $ckey_length)) : '';

		$cryptkey = $keya . md5($keya . $keyc);
		$key_length = strlen($cryptkey);

		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		} 

		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		} 

		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		} 

		if ($operation == 'DECODE') {
			if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			} 
		} else {
			return $keyc . str_replace('=', '', base64_encode($result));
		} 
	} 
} 
function wptao_getcode() {
	$wptao = get_option('wptao');
	if (is_array($wptao) && $wptao['code']['authorize_code']) {
		return $wptao['code'];
	}
	if (is_multisite()) { // WPMU
		$option = get_site_option('wptao_code');
		if ($option['bought']) {
			return $option;
		} 
	} 
} 
function wptao_ensign($site, $url = '') {
	$keys = wptao_getcode();
	if ($keys['apikey'] && $keys['secret']) {
		if (!empty($keys['bought'])) {
			$code = DOMAIN_CURRENT_SITE;
		} elseif (strpos($keys['apikey'], '.') >= 1) {
			if (strpos($_SERVER["HTTP_HOST"], $keys['apikey']) !== false) {
				$code = $keys['apikey'];
			} else {
				$code = $_SERVER['HTTP_HOST'];
			} 
		} else {
			$code = $keys['apikey'];
		} 
		$wptao_options = get_option('wptao');
		if (!$site && $url) {
			if (strpos($url, '.jd.com')) {
				$site = 'jd';
			} elseif (strpos($url, '.dangdang.com')) {
				$site = 'dangdang';
			} elseif (strpos($url, '.suning.com')) {
				$site = 'suning';
			} 
		} 
		if ($site == 'jd') {
			$op = 'token=' . $wptao_options['jd_token'] . '&unionId=' . $wptao_options['unionId'] . '&webId=' . $wptao_options['webId'];
		} elseif ($site == 'dangdang') {
			$op = 'from=' . $wptao_options['dangdang_from'];
		} elseif ($site == 'suning') {
			$op = 'userId=' . $wptao_options['suning_userId'];
		} else {
			$op = 'pid=' . $wptao_options['pid'];
		} 
		// return $op;
		return $code . '|' . key_authcode($op, 'ENCODE', $keys['secret'], 300);
	} 
} 
// WPMU
function wptao_network_pages() {
	add_submenu_page('settings.php', '淘宝客', '淘宝客', 'manage_options', 'wptao', 'wptao_network_admin');
}
add_action('network_admin_menu', 'wptao_network_pages');
function wptao_network_admin() {
	if (isset($_POST['network_option'])) {
		do_action('wptao_update_network');
		$authorize_code = trim($_POST['authorize_code']);
		if ($authorize_code) {
			if (substr($authorize_code, -4) == 'WPMU') {
				$authorizecode = substr($authorize_code, 0, -4);
				$is_wpmu = 1;
			} else {
				$authorizecode = $authorize_code;
				$is_wpmu = '';
			}
			$apikey = substr($authorizecode, 0, -32);
			$secret = substr($authorizecode, -32);
			$network_option = array('apikey' => $apikey, 'secret' => $secret, 'wpmu' => $is_wpmu, 'authorize_code' => $authorize_code);
			if (strpos($apikey, '.') >= 1) { // 请勿修改，否则插件会出现未知错误
				$network_option['bought'] = 1;
			} else {
				$network_option['bought'] = '';
			}
			update_site_option('wptao_code', $network_option);
		} else {
			update_site_option('wptao_code', array());
		}
	}
	$network_option = get_site_option('wptao_code');
	if ($network_option['apikey'] != DOMAIN_CURRENT_SITE) {
		echo '<div class="updated"><p><strong>请填写正确的插件“根域名/WPMU”授权码。</strong></p></div>';
	}
?>
<div class="wrap">
  <h2>淘宝客设置</h2><br />
  <div class="custom-item-wrapper">
	<h3>插件授权</h3>
	<div class="custom-section">
	  <div class="custom-container">
		<form method="post" action="">
		  <?php wp_nonce_field('network-coption');?>
		  <table class="form-table">
			<tr>
			  <td width="220" valign="top">填写插件“根域名/WPMU”授权码：</span></td>
			  <td><input type="text" name="authorize_code" size="35" value="<?php echo $network_option['authorize_code'];?>" /> <?php echo $code_yes;?></td>
			</tr>
			<tr>
			  <td colspan="2"><input type="submit" name="network_option" class="button-primary" value="<?php _e('Save Changes') ?>" /></td>
			</tr>
		  </table>
		</form>
	   <p><a href="http://blogqun.com/wptao.html" target="_blank">如何获得插件授权码?</a> (请选择根域名或者WPMU)</p>
	  </div>
	</div>
  </div>
</div>
<?php
}