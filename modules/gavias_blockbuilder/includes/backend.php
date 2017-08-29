<?php
use Drupal\gavias_blockbuilder\includes\core\gavias_sc;

function gavias_single_field( $field, $meta ){
	$output = '';
	if( isset( $field['type'] ) ){ ?>
		<div class="single-field clearfix">
			<div class="field-title">
				<?php if( key_exists('title', $field) ){?> 
					<span class="label-field"><?php print $field['title']; ?> </span>
				<?php } ?>
			</div>
			<div class="field-content">
			<?php 
				$bb_field = new gavias_bb_fields();
				$field_function = 'render_field_'. $field['type'];
				print $bb_field->$field_function($field, $meta);
			?>	
			</div>
		</div>
		<?php
	}
}

function gavias_admin_element( $item_std, $item = false, $column_id = false, $row_id = false) {
	$element_type 		= $item ? 'name="element-type[]"' : '';
	$element_parent	= $item ? 'name="element-parent[]"' : '';
	$element_row_parent = $item ? 'name="element-row-parent[]"' : '';
	$label = ( $item && key_exists('fields', $item) && key_exists('title', $item['fields']) ) ? $item['fields']['title'] : '';
	ob_start();
  	include GAVIAS_BLOCKBUILDER_PATH . '/templates/backend/admin-element.php';
  	$content = ob_get_clean();
	print $content;				
}

function gavias_admin_row( $item_std, $row_std, $column_std, $row = false, $row_id = false ) {
	$name_row_id = $row ? 'name="gbb-row-id[]"' : '';
	ob_start();
  	include GAVIAS_BLOCKBUILDER_PATH . '/templates/backend/admin-row.php';
  	$content = ob_get_clean();
	print $content;		
}

function gavias_admin_column( $item_std, $column_std, $column = false, $column_id = false,  $row_id = false ) {
	$column_size = (isset($column['attr']['size']) && $column['attr']['size']) ? $column['attr']['size'] : '4';
	$column_type = (isset($column['attr']['type']) && $column['attr']['type']) ? $column['attr']['type'] : '';
	$name_column_id = $column ? 'name="gbb-column-id[]"' : '';
	ob_start();
  	include GAVIAS_BLOCKBUILDER_PATH . '/templates/backend/admin-column.php';
  	$content = ob_get_clean();
	print $content;		
}

function gavias_blockbuilder_admin($pid) {
	$pbd_single = gavias_blockbuilder_load($pid);
	if(!$pbd_single){
		drupal_set_message('Not found gavias block builder !');
		return false;
	}
	$gsc = new gavias_sc();
	$gsc->gsc_load_shortcodes(true);
	$gbb_rows_opts = $gsc->row_opts(); 
	$gbb_columns_opts = $gsc->column_opts(); 
	$gbb_els_ops = $gsc->gsc_shortcodes_forms();
	$gbb_els_params = $pbd_single->params;
	$gbb_els = base64_decode($gbb_els_params);
	$gbb_els = json_decode($gbb_els, true);
	//print"<pre>";print_r($gbb_els);die();
	$gbb_title = $pbd_single->title;
	$gbb_id = $pid;
	//print"<pre>";print_r($gbb_els);die();
	if( is_array( $gbb_els ) && ! key_exists( 'attr', $gbb_els[0] ) ){
		$gbb_els_new = array(
			'attr'	=> $gbb_rows_opts,
			'items'	=> $gbb_els
		);
		$gbb_els = array( $gbb_els_new );
	}
	$gbb_rows_count = is_array( $gbb_els ) ? count( $gbb_els ) : 0;
	ob_start();
  	include GAVIAS_BLOCKBUILDER_PATH . '/templates/backend/admin-builder.php';
  	$content = ob_get_clean();
	print $content;
}


// function gavias_blockbuilder_edit(){
//   require_once GAVIAS_BLOCKBUILDER_PATH . '/includes/utilities.php';
//   require_once GAVIAS_BLOCKBUILDER_PATH .'/includes/backend.php';
//   drupal_add_library('media', 'media_browser');
//   drupal_add_library('media', 'media_browser_settings');
//   drupal_add_library('system', 'ui.draggable');
//   drupal_add_library('system', 'ui.dropable');
//   drupal_add_library('system', 'ui.sortable');
//   drupal_add_library('system', 'ui.dialog');
//   drupal_add_css(GAVIAS_BLOCKBUILDER_PATH.'/vendor/font-awesome/css/font-awesome.min.css'); 
//   drupal_add_css(GAVIAS_BLOCKBUILDER_PATH.'/vendor/notify/styles/metro/notify-metro.css');
//   drupal_add_js(GAVIAS_BLOCKBUILDER_PATH . '/vendor/notify/notify.min.js');
//   drupal_add_js(GAVIAS_BLOCKBUILDER_PATH . '/vendor/notify/styles/metro/notify-metro.js');
//   drupal_add_css(GAVIAS_BLOCKBUILDER_PATH.'/assets/css/admin.css');
//   $general_sc = gavias_blockbluider_general_shortcode();
//   if(isset($_GET['destination']) && $_GET['destination']){
//     $url_redirect = $_GET['destination'];
//   }else{
//     $url_redirect = 'admin/gavias_blockbuilder/'.arg(2).'/edit';
//   }
//   $url_redirect = url($url_redirect);
//   $js = 'var gbb_url_redirect = "'.$url_redirect.'"; var $general_shortcodes = '.(json_encode($general_sc)).';';
//   drupal_add_js($js, 'inline');
//   drupal_add_js(GAVIAS_BLOCKBUILDER_PATH . '/assets/js/admin.js');
//   ob_start();
//   include drupal_get_path('module', 'gavias_blockbuilder') . '/templates/backend/form.php';
//   $content = ob_get_clean();
//   return $content;
// }