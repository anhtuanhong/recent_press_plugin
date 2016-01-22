<?php
/*
Plugin Name: HCA Recent Press Plugin
Plugin URI: http://www.homecareassistance.com/
Description: Recent Press Plugin displays recent press clippings to website template.
Version: 1.0.0
Author: Anhtuan Hong
Author URI: http://www.anhtuanhong.me
License: GPL
Copyright: Anhtuan Hong
*/

define( 'HRP_PLUGIN_URL', plugins_url( '/', __FILE__ ) );

global $hrp_db_version;
$hrp_db_version = '1.0';

//http://codex.wordpress.org/Function_Reference/register_activation_hook
//function to be run when the plugin is activated
register_activation_hook( __FILE__, 'HRP_run_when_plugin_activated' );
register_activation_hook( __FILE__, 'hrp_install_data' );

/****************** HCA Recent Post Admin Page (Start) *******/

add_action('admin_init', 'hrp_admin_init_setting');
add_action('admin_menu', 'hrp_admin_generate_menu_link');

function hrp_admin_generate_menu_link() 
{

	add_menu_page('HCA Recent Press', 'HCA Recent Press', 'manage_options', 'hca_recent_press');

	$hrp_global_setup = add_submenu_page('hrp_setup', 'HCA Recent Press', 'Global Config', 'manage_options', 'hca_recent_press', 'hrp_global_setup');
	add_action('admin_print_styles-' .$hrp_global_setup, 'hrp_admin_output_admin_css');
}

function hrp_admin_init_setting() 
{
	wp_register_style('hrp_admin_style', HRP_PLUGIN_URL . 'css/hca_rp_admin.css');
}

function hrp_admin_output_admin_css()
{
	wp_enqueue_style('hrp_admin_style');
}

/****************** HCA Recent Post Admin Page (End) *******/

function hrp_global_setup()
{
	include_once('admin/hca_rp_admin_settings.php');
}

function HRP_run_when_plugin_activated(){
	global $wpdb;
	global $hrp_db_version;

	$table_name = $wpdb->prefix . 'hca_recent_press';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		d_order mediumint(9) NOT NULL,
		dater varchar(255),
		d_dater varchar(255),
		titler varchar(255),
		snippet text,
		linker varchar(255),
		logo_linker varchar(255),
		outlet_name varchar(255),
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'hrp_db_version', $hrp_db_version );


}

function hrp_install_data() {
	global $wpdb;
	
	$d_order = 1; 
	$dater = 'February 10, 2016';
	$d_dater = '';
	$titler = 'First Title';
	$snippet = 'This is your first snippet.';
	$linker = 'http://homecareassistance.com';
	$logo_linker = 'http://logolinker.com';
	$outlet_name = 'Test Outlet';
	
	$table_name = $wpdb->prefix . 'hca_recent_press';
	
	$wpdb->insert( 
		$table_name, 
		array( 
			'id' => '',
			'd_order' => $d_order, 
			'dater' => $dater, 
			'd_dater' => $d_dater,
			'titler' => $titler,
			'snippet' => $snippet,
			'linker' => $linker,
			'logo_linker' => $logo_linker,
			'outlet_name' => $outlet_name
		) 
	);
}

add_action( 'admin_footer', 'recent_press_javascript' ); // Write our JS below here

function recent_press_javascript() 
{ ?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) 
	{
		var elementPlaceholder = $('#recent-press-element-placeholder');

		$(".add").on('click', addRow);
		$('.remove').on('click', removeRow);
		$('.update').on('click', updateRow);

		function addRow()
		{
			var d_order = parseInt($(this).parent('tr').attr('id'));
			var originalRow = $(this);
			$(this).find('.rp_btn').css('background', '#999');

			var data = { 
				'action': 'rp_add',
				'display_order': d_order
			};

			$.post(ajaxurl, data, function(response) {
				console.log(response);
				if(response == 'success'){
					var returner = updateOrder(d_order, 'add');
					if( returner == true)
					{
						var elementRow = $(elementPlaceholder).clone();
						var elementRowID = "tr#"+d_order;
						var insertRowID = "tr#"+(d_order-1);

						$(elementRow).attr('id', d_order);
						$(insertRowID).after(elementRow).show();
						$(elementRowID).show().find('td.d_order').html(d_order);

						//Add Event Listener
						$(elementRowID).find('.update').on('click', updateRow);
						$(elementRowID).find('.add').on('click', addRow);
						$(elementRowID).find('.remove').on('click', removeRow);

						originalRow.find('.rp_btn').css('background', '#ddd');
					}
				}
			});
		}

		function updateRow()
		{
			var elementRow = $(this);
			var d_order = parseInt($(this).parent('tr').attr('id'));
			//alert(d_order);
			var parentRow = 'tr#' + d_order;
			//alert($(parentRow).find('.dater input').val());
			var data = { 
				'action': 'rp_update',
				'display_order': d_order,
				'dater': $(parentRow).find('.dater input').val(),
				'd_dater': $(parentRow).find('.d_dater input').val(),
				'titler': $(parentRow).find('.titler input').val(),
				'snippet': $(parentRow).find('.snippet input').val(),
				'linker': $(parentRow).find('.linker input').val(),
				'logo_linker': $(parentRow).find('.logo_linker input').val(),
				'outlet_name': $(parentRow).find('.outlet_name input').val()
			};

			$.post(ajaxurl, data, function(response)
			{
				console.log(response);
				if(response == 'success')
				{
					//Update Row Callback
					elementRow.addClass('saved').find('.rp_btn').html('Saved!');
					setTimeout(function()
					{
						elementRow.fadeOut('slow', function()
						{
							$(this).removeClass('saved').show().find('.rp_btn').html('Update');
						});
					}, 2000);
				}
			});
		}

		function removeRow()
		{
			if($('#recent-press-list tr').length > 3){
				$(this).css('opacity', 0);
				var d_order = parseInt($(this).parent('tr').attr('id'));
				
				var data = { 
					'action': 'rp_remove',
					'display_order': d_order
				};	

				$.post(ajaxurl, data, function(response) {
					console.log(response);
					if(response == 'success')
					{
						//alert('sucess');
						//Remove Ajax Callback
						var removeID = "tr#" + d_order;
						$(removeID).remove();
						updateOrder(d_order, 'down');
					}
				});
			}else{
				alert('Warning: Cannot remove last row.');
				$(this).css('opacity', 1);
			}
		}
		
		function updateOrder(d_order, direction)
		{
			//alert(d_order);
			$("td.d_order").each(function()
			{
				var currentholder = $(this).parent("tr").attr('id');
				if(currentholder != 'recent-press-element-placeholder')
				{
					var holder = parseInt(currentholder);
					//alert(holder);
					if( holder >= d_order)
					{	
						if(direction == 'down')
						{
							$(this).parent("tr").attr('id', holder-1);
							$(this).html(holder-1);
						}else{
							$(this).parent("tr").attr('id', holder+1);
							$(this).html(holder+1);
						}
					}
				}
			});

			return true;
		}
	});
	</script> <?php
}

// Same handler function...
add_action( 'wp_ajax_rp_add', 'rp_add_callback' );
add_action( 'wp_ajax_rp_remove', 'rp_remove_callback' );
add_action( 'wp_ajax_rp_update', 'rp_update_callback' );

function update_Order($d_order, $direction)
{
	global $wpdb;
	$rp_table = $wpdb->prefix . 'hca_recent_press';
	if($direction == 'add'){
		$sql = "UPDATE ".$rp_table." SET d_order = d_order + 1 WHERE d_order >= " . $d_order;
	}else{
		$sql = "UPDATE ".$rp_table." SET d_order = d_order - 1 WHERE d_order > " . $d_order;
	}
	
	$results = $wpdb->query($sql);

	return $results;

	wp_die();
}

function rp_add_callback() 
{
	global $wpdb;
	$rp_table = $wpdb->prefix . 'hca_recent_press';
	
	$d_order = intval($_POST['display_order']);

	if(update_Order($d_order, 'add') !== false)
	{
		//echo 'HERE';
		//$sql = "INSERT INTO ".$rp_table." ('id', 'd_order', 'dater', 'd_date', 'titler', 'snippet', 'linker', 'logo_linker', 'outlet_name') VALUES (NULL, ".$d_order.", '','','','','','', '')";
		$results = $wpdb->insert( 
			$rp_table, 
			array( 
				'id' => '',
				'd_order' => $d_order, 
				'dater' => '', 
				'd_dater' => '',
				'titler' => '',
				'snippet' => '',
				'linker' => '',
				'logo_linker' => '',
				'outlet_name' => ''
			)
		);

		if($results !== false)
		{
			echo 'success';
		}else{
			echo 'failed';
		}
	}
	wp_die();
}

function rp_remove_callback() 
{	
	global $wpdb;
	$rp_table = $wpdb->prefix . 'hca_recent_press';
	
	$d_order = intval($_POST['display_order']);

	$wpdb->delete( 
			$rp_table, 
			array( 
				'd_order' => $d_order
			)
		);	
	if(update_Order($d_order, 'subtract') !== false)
	{
		echo 'success';
	}else{
		echo 'failed';
	}

	wp_die();
}

function rp_update_callback() 
{
	global $wpdb;
	
	$rp_table = $wpdb->prefix . 'hca_recent_press';
	$d_order = intval($_POST['display_order']);

	$results = $wpdb->update( 
		$rp_table, 
		array( 
			'dater' => stripslashes($_POST['dater']), 
			'd_dater' => stripslashes($_POST->d_dater),
			'titler' => stripslashes($_POST['titler']),
			'snippet' => stripslashes($_POST['snippet']),
			'linker' => stripslashes($_POST['linker']),
			'logo_linker' => stripslashes($_POST['logo_linker']),
			'outlet_name' => stripslashes($_POST['outlet_name'])
		), 
		array( 'd_order' => $d_order )
	);

	if($results !== false)
	{
		echo 'success';
		//var_dump($results);
	}else{
		echo 'failed';
	}

	wp_die();
}

//ADD SHORTCODE
add_shortcode( 'hca_rp', 'display_hca_recent_press' );

function display_hca_recent_press( $atts ){
	global $wpdb;
    $table_name = $wpdb->prefix . 'hca_recent_press';
    
	$attr = shortcode_atts( array(
        'count' => 200
    ), $atts );
    //$attr['count'] = number to display, defaults to 100
	$count = intval($attr['count']);

	$sql = 'SELECT * FROM '.$table_name.' WHERE d_order <= '.$count.' ORDER BY d_order';
	//echo $sql;
    $results = $wpdb->get_results( $sql);

  	//var_dump($results);

    $returner = '<div class="recent_press_container" align="center">';

    foreach($results as $row)
    {
    	if ( $row->d_dater ) 
    	{
	        $date = $row->d_dater;
	    } else {
	        $date = $row->dater;
	    }
            // logo or outlet name
        if ( $row->logo_linker ) {
          $outlet = '<img src="' . $row->logo_linker . '" style="max-width: 150px; margin: 10px; float:right;" />';
        } else {
          $outlet = '<p style="font-size: 18px; font-weight: bold">' . $row->outlet_name . '</p>';
        }
        // print each row
       	 
        $returner .= '<div class="recent_press_article">'. $outlet  
          . '<a href="' . $row->linker . '" target="_blank">' . $row->titler . '</a><br />'
          . '<small>' . $date . '</small><br />'
          . '<p style="margin-top: 5px">' . $row->snippet . '</p>'
          
        . '</div>';
    }

    $returner .= '</div>';

	return $returner;
}


?>