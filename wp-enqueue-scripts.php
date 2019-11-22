<?php
$wp_starter_js = array();
$wp_starter_use_js = array();
$wp_starter_condition_js = array();



/* ===============================================================
	REGISTER JS
=============================================================== */
function new_js($name='', $path='', $footer=false){
	global $wp_starter_js;

	if( $name != '' AND $path != '' ){
		$wp_starter_js[$name] = $path;

		if( $footer == true ){
			$wp_starter_js[$name.'_footer'] = true;
		}else{
			$wp_starter_js[$name.'_footer'] = false;
		}
	}
}



/* ===============================================================
	USE JS
=============================================================== */
function use_js($name='', $condition='', $validate=''){
	global $wp_starter_js, $wp_starter_use_js, $wp_starter_condition_js;

	if( $name != '' ){
		if( $condition != '' ){ // If have a condition to insert the JS
			$generate_id = uniqid(); // Get a New ID
			$wp_starter_condition_js[$generate_id]['name']      = $name.$generate_id;
			$wp_starter_condition_js[$generate_id]['original']  = $name;
			$wp_starter_condition_js[$generate_id]['condition'] = $condition;
			$wp_starter_condition_js[$generate_id]['validate']  = $validate;
		}else{ // Don't have any conditions
			$generate_id = uniqid(); // Get a New ID
			$wp_starter_use_js[$generate_id]['name']     = $name.$generate_id;
			$wp_starter_use_js[$generate_id]['path']     = $wp_starter_js[$name];
			$wp_starter_use_js[$generate_id]['original'] = $name;
		}
	}
}



/* ===============================================================
	INSERT IN HEAD
=============================================================== */
add_action( 'wp_enqueue_scripts', 'wp_starter_js_scripts' );
function wp_starter_js_scripts(){
	global $wp_starter_js, $wp_starter_use_js;

	/* Register Styles */
	foreach( $wp_starter_use_js as $js ){
		$name     = $js['name'];
		$path     = $js['path'];
		$original = $js['original'];

		if( !strstr($path, 'http://') AND !strstr($path, 'https://') ){ $path = THEMEROOT.'/'.$path; } // Verifying if have http:// or https:// if not, add the template directory url to the path

		if( $wp_starter_js[$original.'_footer'] == false ){
			wp_register_script( $name, $path );
		}else{ // Insert before close the tag body
			wp_register_script( $name, $path, array(), null, true );
		}
	}

	/* Enqueue Styles */
	foreach( $wp_starter_use_js as $js ){
		$name = $js['name'];
		wp_enqueue_script( $name );
	}
}



/* ===============================================================
	INSERT IN HEAD WITH CONDITION
=============================================================== */
add_action('wp', 'use_js_condition');
function use_js_condition(){
	global $wp_starter_condition_js;

	foreach($wp_starter_condition_js as $js){
		$js_name      = $js['name'];
		$js_original  = $js['original'];
		$js_condition = $js['condition'];
		$js_validate  = $js['validate'];

		/* -- IS HOME -- */
		if( $js_condition == 'is_home' ){
			if( is_home() ){
				use_js($js_original);
			}

		/* -- IS FRONT PAGE -- */
		}else if( $js_condition == 'is_front_page' ){
			if( is_front_page() ){
				use_js($js_original);
			}

		/* -- IS SINGLE -- */
		}else if( $js_condition == 'is_single' ){
			if( $js_validate != '' ){
				if( is_single($js_validate) ){
					use_js($js_original);
				}
			}else{
				if( is_single() ){
					use_js($js_original);
				}
			}

		/* -- IS SINGULAR -- */
		}else if( $js_condition == 'is_singular' ){
			if( is_singular($js_validate) ){
				use_js($js_original);
			}

		/* -- IS CATEGORY -- */
		}else if( $js_condition == 'is_category' ){
			if( is_category($js_validate) ){
				use_js($js_original);
			}

		/* -- IS ARCHIVE -- */
		}else if( $js_condition == 'is_archive' ){
			if( is_archive() ){
				use_js($js_original);
			}

		/* -- IS PAGE -- */
		}else if( $js_condition == 'is_page' ){
			if( is_page($js_validate) ){
				use_js($js_original);
			}

		/* -- IS PAGE TEMPLATE -- */
		}else if( $js_condition == 'is_page_template' ){
			if( is_page_template($js_validate) ){
				use_js($js_original);
			}

		/* -- IS SEARCH -- */
		}elseif( $js_condition == 'is_search' ){
			if( is_search() ){
				use_js($js_original);
			}

		/* -- IS ADMIN -- */
		}else if( $js_condition == 'is_admin' ){
			if( is_admin() ){
				use_js($js_original);
			}
		}
	}
}


?>