<?php	
	/* Pre-Load Site Options - Utilizes Built-in Cache Functions */
	global $bapi_all_options; 
	function bapi_wp_site_options(){
		global $bapi_all_options;
		$bapi_all_options = wp_load_alloptions();
		if(!isset($bapi_all_options['bapi_solutiondata'])){
			$bapi_all_options['bapi_solutiondata'] = '';
		}
		if(!isset($bapi_all_options['bapi_solutiondata_lastmod'])){
			$bapi_all_options['bapi_solutiondata_lastmod'] = 0;
		}
		if(!isset($bapi_all_options['bapi_textdata'])){
			$bapi_all_options['bapi_textdata'] = '';
		}
		if(!isset($bapi_all_options['bapi_textdata_lastmod'])){
			$bapi_all_options['bapi_textdata_lastmod'] = 0;
		}
		if(!isset($bapi_all_options['bapi_keywords_array'])){
			$bapi_all_options['bapi_keywords_array'] = '';
		}
		if(!isset($bapi_all_options['bapi_keywords_lastmod'])){
			$bapi_all_options['bapi_keywords_lastmod'] = 0;
		}
		if(!isset($bapi_all_options['bapi_language'])){
			$bapi_all_options['bapi_language'] = 'en-US';
		}
		if(!isset($bapi_all_options['bapi_baseurl'])){
			$bapi_all_options['bapi_baseurl'] = 'connect.bookt.com';
		}
		//print_r($bapi_all_options); exit();
	}
	
	/* BAPI url handlers */
	function urlHandler_emailtrackingimage() {
		$url = get_relative($_SERVER['REQUEST_URI']);		
		$url = strtolower($url);
		$url = substr($url, 0, 8);
		
		if ($url == "/t/misc/") {
			//header('Content-Type: application/javascript');	
			header('Cache-Control: public');
			//$expires = round((60*10 + $lastupdatetime), 2); // expires every 10 mins
			//$expires = gmdate('D, d M Y H:i:s \G\M\T', $expires);
			//header( 'Expires: ' . $expires );		
			echo "Image Handler";
			exit();
		}
	}
	
	function urlHandler_bapitextdata() {
		global $bapi_all_options; 
		$url = get_relative($_SERVER['REQUEST_URI']);
		if (strtolower($url) != "/bapi.textdata.js")
			return; // not our handler
		
		header('Content-Type: application/javascript');	
		header('Cache-Control: public');

		$expires = round((60*10 + $lastupdatetime), 2); // expires every 10 mins
		$expires = gmdate('D, d M Y H:i:s \G\M\T', $expires);
		header( 'Expires: ' . $expires );
		
		$js = $bapi_all_options['bapi_textdata']; // core data should have been synced prior to this
		echo "/*\r\n";
		echo "	BAPI TextData\r\n";
		echo "	Last updated: " . date('r',$lastupdatetime) . "\r\n";	
		echo "	Language: " . getbapilanguage() . "\r\n";
		echo "*/\r\n\r\n";
		echo "BAPI.textdata = " . $js . ";\r\n";
		exit();
	}
	
	function urlHandler_bapiconfig() {
		$url = get_relative($_SERVER['REQUEST_URI']);
		if (strtolower($url) != "/bapi.config.js")
			return; // not our handler
		
		header('Content-Type: application/javascript');	
		header('Cache-Control: public');
		//header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
				 
		echo "BAPI.config().adults.enabled = false;\r\n";
		echo "BAPI.config().altid.enabled = false;\r\n";
		echo "BAPI.config().amenity.enabled = false;\r\n";				
		echo "BAPI.config().beds.enabled = false;\r\n";
		echo "BAPI.config().category.enabled = false;\r\n";
		echo "BAPI.config().checkin.enabled = false;\r\n";
		echo "BAPI.config().checkout.enabled = false;\r\n";
		echo "BAPI.config().children.enabled = false;\r\n";
		echo "BAPI.config().city.enabled = false;\r\n";
		echo "BAPI.config().dev.enabled = false;\r\n";
		echo "BAPI.config().headline.enabled = false;\r\n";
		echo "BAPI.config().los.enabled = false;\r\n";
		echo "BAPI.config().location.enabled = false;\r\n";
		echo "BAPI.config().rate.enabled = false;\r\n";
		echo "BAPI.config().rooms.enabled = false;\r\n";
		echo "BAPI.config().sleeps.enabled = false;\r\n";
		
		echo "BAPI.config().minlos = 3;\r\n";
		echo "BAPI.config().availcalendarmonths = 6;\r\n";
		echo "BAPI.config().displayavailcalendar = false;\r\n";		
		echo "BAPI.config().hasreviews = false;\r\n";		
		
		echo "BAPI.config().leadsettings.emailrequired = false;\r\n";
		echo "BAPI.config().leadsettings.hasdatesoninquiryform = false;\r\n";
		echo "BAPI.config().leadsettings.lsrequired = false;\r\n";
		echo "BAPI.config().leadsettings.phonerequired = false;\r\n";
		
		exit();
	}
	
	function urlHandler_bapitemplates() {
		$url = get_relative($_SERVER['REQUEST_URI']);
		if (strtolower($url) != "/bapi.templates.js")
			return; // not our handler
		
		header('Content-Type: application/javascript');	
		header('Cache-Control: public');
		//header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
				 
		$c = file_get_contents(BAPISync::getMustacheLocation());
		$j2 = rawurlencode($c); //addslashes($c);		
		
		if (BAPISync::isMustacheOverriden()) {
			echo "// custom bapi template file\r\n";
		} else {
			echo "// baseline bapi template file\r\n";
		}
		
		echo "var t = '" . $j2 . "';\r\n";	
		echo "t = decodeURIComponent(t);\r\n";
		echo "BAPI.templates.set(t);\r\n";	
		exit();
	}
	
	function urlHandler_sitelist() {
		$url = get_relative($_SERVER['REQUEST_URI']);
		if (strtolower($url) != "/bapi.sitelist.js")
			return; // not our handler
		
		header('Content-Type: application/javascript');	
		header('Cache-Control: public');
		$blog_list = get_blog_list( 0, 'all' );
		$i=0;
		echo '{';
		foreach ($blog_list AS $blog) {
			if ($i>0) echo ', ';
			echo $blog['domain'].$blog['path'];
			$i++;
		}
		echo '}';
		exit();
	}
	
	
	/* Converted a url to a physical file path */
	function get_local($url) {
		$urlParts = parse_url($url);
		return realpath($_SERVER['DOCUMENT_ROOT']) . $urlParts['path'];				
	}
	
	function get_relative($url) {
		$urlParts = parse_url($url);
		return $urlParts['path'];		
	}
	
	function get_adminurl($url) {
		$url = get_relative( plugins_url($url, __FILE__) );
		return str_replace("/wp-content/plugins","",$url);	
	}	
	
	/* BAPI Helpers */	
	function getbapiurl() {
		global $bapi_all_options;
		$bapi_baseurl = 'connect.bookt.com';
		//Check if there is a globally defined baseurl constant.  This should be set in wp-config.php like so: define('BAPI_BASEURL', 'connect.bookt.com');
		if(defined(BAPI_BASEURL)){ 
			$bapi_baseurl = BAPI_BASEURL;
		}
		if($bapi_all_options['bapi_baseurl']){
			$bapi_baseurl = get_option('bapi_baseurl');
		}
		if(empty($bapi_baseurl) || $bapi_baseurl=='connect.bookt.com'){
			$bapi_baseurl = 'd2kqqk9digjl80.cloudfront.net';  
			//$bapi_baseurl = 'connect.bookt.com';
		}
		if (stripos($bapi_baseurl, "localhost", 0) === 0) {			
			return "http://" . $bapi_baseurl;
		}
		return "https://" . $bapi_baseurl;
	}

	function getbapilanguage() {
		global $bapi_all_options;
		$language = $bapi_all_options['bapi_language'];	
		if(empty($language)) {
			$language = "en-US";
		}
		return $language;	
	}
	
	function bapi_language_attributes($doctype) {
		return 'lang="'.getbapilanguage().'"';
	}

	function getbapijsurl($apiKey) {
		return getbapiurl() . "/js/bapi.js?apikey=" . $apiKey;
	}

	function getbapiapikey() {
		global $bapi_all_options;
		return $bapi_all_options['api_key'];	
	}
	
	function getbapisolutiondata() {
		$wrapper = array();	
		$wrapper['site'] = getbapicontext();
		$wrapper['textdata'] = getbapitextdata();			
		return $wrapper;
	}	
	
	function getbapicontext() {	
		global $bapi_all_options;
		return json_decode($bapi_all_options['bapi_solutiondata'],TRUE); 		
	}
	
	function getbapitextdata() {
		global $bapi_all_options;
		return json_decode($bapi_all_options['bapi_textdata'],TRUE); 		
	}	
	
	/* Page Helpers */
	function getPageKeyForEntity($entity, $pkid) {
		return $entity . ':' . $pkid;
	}	
	
	function getPageForEntity($entity, $pkid, $parentid) {
		$pagekey = getPageKeyForEntity($entity, $pkid);
		$args = array('meta_key' => 'bapikey', 'meta_value' => $pagekey, 'child_of' => $parentid);
		return get_pages($args);		
	}
	
	/* Common include files needed for BAPI */
	function getconfig() {	
		global $bapi_all_options;	
		//echo 'getconfig';
		//echo get_option('api_key');
		if($bapi_all_options['api_key']){
			$apiKey = $bapi_all_options['api_key'];
			$language = getbapilanguage();			
			
			$secureurl = '';
			if($bapi_all_options['bapi_secureurl']){
				$secureurl = $bapi_all_options['bapi_secureurl'];
			}
			$siteurl = $bapi_all_options['home'];
			if($bapi_all_options['bapi_site_cdn_domain']){
				$siteurl = $bapi_all_options['bapi_site_cdn_domain'];
			}
			$siteurl = str_replace("http://", "", $siteurl);
?>
<script type="text/javascript">
window.Muscula = { settings: { logId: "2d835166-5e05-4073-817c-c7d0bf477ff4", suppressErrors: false, branding: "none" } }; (function () { var m = document.createElement("script"); m.type = "text/javascript"; m.async = true; m.src = (window.location.protocol == "https:" ? "https:" : "http:") + "//musculahq.appspot.com/Muscula2.js"; var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(m, s); window.Muscula.run = function (c) { eval(c); window.Muscula.run = function () { } }; window.Muscula.errors = []; window.onerror = function () { window.Muscula.errors.push(arguments); return window.Muscula.settings.suppressErrors === undefined } })();
</script>
<link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/base/jquery.ui.all.css" rel="stylesheet" />

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-migrate/1.2.1/jquery-migrate.min.js" type="text/javascript"></script>    
<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js" type="text/javascript"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/i18n/jquery-ui-i18n.min.js" type="text/javascript"></script>
<!--[if gt IE 7]>
<script type="text/javascript" src="<?= get_relative(plugins_url('/js/pickadate/source/pickadate.min.js', __FILE__)) ?>" ></script>			
<![endif]-->
<![if !IE]>
<script type="text/javascript" src="<?= get_relative(plugins_url('/js/pickadate/source/pickadate.min.js', __FILE__)) ?>" ></script>			
<![endif]>
<!--[if lte IE 8]>
<script type="text/javascript" src="<?= get_relative(plugins_url('/js/pickadate/source/pickadate.legacy.min.js', __FILE__)) ?>" ></script>			
<![endif]-->
<script type="text/javascript" src="<?= getbapijsurl($apiKey) ?>"></script>
<script type="text/javascript" src="<?= get_relative(plugins_url('/bapi/bapi.ui.js', __FILE__)) ?>" ></script>		
<script type="text/javascript" src="/bapi.textdata.js" ></script>
<script type="text/javascript" src="/bapi.templates.js" ></script>
<!--<script type="text/javascript" src="/bapi.config.js" ></script>-->
<script type="text/javascript">		
	BAPI.UI.loading.setLoadingImgUrl('<?= get_relative(plugins_url("/img/loading.gif", __FILE__)) ?>');
	BAPI.site.url =  '<?= $siteurl ?>';
	<?php if ($secureurl!='') { ?>
	BAPI.site.secureurl = '<?= $secureurl ?>';
	<?php } ?>
	BAPI.init();
	BAPI.UI.jsroot = '<?= plugins_url("/", __FILE__) ?>'
	$(document).ready(function () {
		BAPI.UI.init();
	});    
</script>

<?php			
		}
	}

	/* Slideshow */
	function bapi_get_slideshow($mode='raw'){
		$slide1 = get_option('bapi_slideshow_image1');
		$slide2 = get_option('bapi_slideshow_image2');
		$slide3 = get_option('bapi_slideshow_image3');
		$slide4 = get_option('bapi_slideshow_image4');
		$slide5 = get_option('bapi_slideshow_image5');
		$slide6 = get_option('bapi_slideshow_image6');
		$slide1cap = get_option('bapi_slideshow_caption1');
		$slide2cap = get_option('bapi_slideshow_caption2');
		$slide3cap = get_option('bapi_slideshow_caption3');
		$slide4cap = get_option('bapi_slideshow_caption4');
		$slide5cap = get_option('bapi_slideshow_caption5');
		$slide6cap = get_option('bapi_slideshow_caption6');
		$slideshow = array();
		$i = 0;
		if(strlen($slide1)>0){
			$slideshow[$i] = array("url"=>$slide1,"caption"=>$slide1cap,"thumb"=>plugins_url('thumbs/timthumb.php?src='.urlencode($slide1).'&h=80', __FILE__));
			$i++;
		}
		if(strlen($slide2)>0){
			$slideshow[$i] = array("url"=>$slide2,"caption"=>$slide2cap,"thumb"=>plugins_url('thumbs/timthumb.php?src='.urlencode($slide2).'&h=80', __FILE__));
			$i++;
		}
		if(strlen($slide3)>0){
			$slideshow[$i] = array("url"=>$slide3,"caption"=>$slide3cap,"thumb"=>plugins_url('thumbs/timthumb.php?src='.urlencode($slide3).'&h=80', __FILE__));
			$i++;
		}
		if(strlen($slide4)>0){
			$slideshow[$i] = array("url"=>$slide4,"caption"=>$slide4cap,"thumb"=>plugins_url('thumbs/timthumb.php?src='.urlencode($slide4).'&h=80', __FILE__));
			$i++;
		}
		if(strlen($slide5)>0){
			$slideshow[$i] = array("url"=>$slide5,"caption"=>$slide5cap,"thumb"=>plugins_url('thumbs/timthumb.php?src='.urlencode($slide5).'&h=80', __FILE__));
			$i++;
		}
		if(strlen($slide6)>0){
			$slideshow[$i] = array("url"=>$slide6,"caption"=>$slide6cap,"thumb"=>plugins_url('thumbs/timthumb.php?src='.urlencode($slide6).'&h=80', __FILE__));
			$i++;
		}
		if($mode=='raw'){
			return $slideshow;
		}
		if($mode=='json'){
			$json = json_encode($slideshow);
			?>
			<script>
				var slides_json = '<?= $json ?>';
			</script>	
			<?php
			return true;
		}
		if($mode=='divs'){
			foreach($slideshow as $sl){
				?>
				<div>
					<a href=""><img src="<?= $sl['url'] ?>" title="<?= $sl['caption'] ?>" /></a>
				</div>
				<?php
			}
			return true;
		}
	}
	
	/* CDN Support */
	function home_url_cdn( $path = '', $scheme = null ) {
		return get_home_url_cdn( null, $path, $scheme );
	}

	function get_home_url_cdn( $blog_id = null, $path = '', $scheme = null ) {	
		$cdn_url = get_option('home');
		if(get_option('bapi_site_cdn_domain')&&!(is_admin()||is_super_admin())){
			$cdn_url = get_option('bapi_site_cdn_domain');
		}
		$home_url = str_replace(get_option('home'),$cdn_url,$path);
		//echo $home_url; 
		
		return $home_url;
	}
	
	function add_server_name_meta(){
		$sn = gethostname();
		echo '<meta name="SERVERNAME" content="'.$sn.'" />'."\n";
	}
	
	function bapi_redirect_fix($redirect_url, $requested_url) {
		$cdn_domain = parse_url(get_option('bapi_site_cdn_domain'));
		$redirect = parse_url($redirect_url);
		if($redirect['scheme']!='https') {
			$redirect_url = $redirect['scheme'].'://'.$cdn_domain['host'];
			$redirect_url .= $redirect['path'];
			if ( !empty($redirect['query']) ) {
				$redirect_url .= '?' . $redirect['query'];
			}
			return $redirect_url; 
		}
		return $redirect_url;
	}
	
	function bapi_getmeta(){
		$pid = get_the_ID();
		
		$metak = get_post_meta($pid,'bapi_meta_keywords',true);
		$metak = str_replace('"', "", $metak);
		$metak = str_replace("'", "", $metak);
		
		$metad = get_post_meta($pid,'bapi_meta_description',true);
		$metad = str_replace('"', "", $metad);
		$metad = str_replace("'", "", $metad);
		
		$lastu = (int) get_post_meta($pid,'bapi_last_update',true);
		$lastu = date('r',$lastu);
		
		?><meta name="LASTMOD" content="<?= $lastu ?>" /><?= "\n" ?><meta name="KEYWORDS" content="<?= $metak ?>" /><?= "\n" ?><meta name="DESCRIPTION" content="<?= $metad ?>" /><?= "\n" ?><?php
	}
	
	function bapi_add_entity_meta(){
		global $entityUpdateURL;
		?><meta name="ENTITYURL" content="<?= $entityUpdateURL ?>" /><?= "\n" ?><?php
	}
	function bapi_add_context_meta(){
		global $getContextURL;
		?><meta name="CONTEXTURL" content="<?= $getContextURL ?>" /><?= "\n" ?><?php
	}
	function bapi_add_textdata_meta(){
		global $textDataURL;
		?><meta name="TEXTDATAURL" content="<?= $textDataURL ?>" /><?= "\n" ?><?php
	}
	function bapi_add_seo_meta(){
		global $seoDataURL;
		?><meta name="SEOURL" content="<?= $seoDataURL ?>" /><?= "\n" ?><?php
	}
	
	function getBAPIObj() {
		global $bapi_all_options;
		return new BAPI($bapi_all_options['api_key'], $bapi_all_options['bapi_language'], $bapi_all_options['bapi_baseurl']);
	}		
	
	function disable_kses_content() {
		if(is_admin()||is_super_admin()){
			remove_filter('content_save_pre', 'wp_filter_post_kses');
		}
	}
	
	function custom_upload_mimes ( $existing_mimes=array() ) {
		// add the file extension to the array
		$existing_mimes['ico'] = 'image/x-icon';
		// call the modified list of extensions
		return $existing_mimes;
	}
	
	function display_global_header(){
		global $bapi_all_options;
		echo $bapi_all_options['bapi_global_header'];
	}
	
	function perm($return, $id, $new_title, $new_slug){
		/* if the user is not super admin */
		if (!is_super_admin()) {
			/* if the post var is set this var show when editing post and pages like this /wp-admin/post.php?post=2468&action=edit */
			if(isset($_GET['post']) && $_GET['post'] != ''){
			/* its set we get the post ID */
			$thePostID = $_GET['post'];
			/* we get the meta data array for this post */
			$metaArray = get_post_meta($thePostID);
				/* we check if our custom fields exists */
				if(!empty($metaArray) && array_key_exists('bapi_page_id', $metaArray) || array_key_exists('bapikey', $metaArray) || array_key_exists('bapi_last_update', $metaArray)){
					/* this is not a super admin and the page is a BAPI page we remove the permalink edit button*/
					$ret2 = preg_replace('/<span id="edit-slug-buttons">.*<\/span>/i', '', $return);
					return $ret2;
				}else{
					/* this is a page created by the user , we do nothing */
					return $return;
				}
			}
		}
			/* this is a super admin we do nothing */
			return $return;
	}
	function getSSL(){
		global $wp_query;
		$postid = $wp_query->post->ID;
		$thePostMeta = get_post_meta($postid, 'bapi_page_id', true);
		$SSLscriptBlock = '<div id="SSLcontent"><script pin type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=135640565046fb4bc11011f1400b8da37ea394266002690762020641"></script></div>';
		if($thePostMeta == 'bapi_makebooking'){echo $SSLscriptBlock;}
	}
	
	function bapi_setup_default_pages() {
		$url = get_relative($_SERVER['REQUEST_URI']);
		//echo $url; exit();
		if (strtolower($url) == "/bapi.init")
			return;
		if(!(strpos($_SERVER['REQUEST_URI'],'wp-admin')===false)||!(strpos($_SERVER['REQUEST_URI'],'wp-login')===false)){
			return;
		}
		$menuname = "Main Navigation Menu";
		$menu_id = initmenu($menuname);
		$menu = wp_get_nav_menu_items($menu_id);
		//print_r($menu);
		if(count($menu) == 0){
			//Initialize menu and pages
			$path = '/bapi.init?mode=initial-setup';
			$url = get_site_url().$path;
			//$server_output = file_get_contents($url);
			header("Location: $url");
			exit();
		}
	}
?>
