<? ?>
<style>
    label{
      width: 100px;
      display:inline-block;
      vertical-align: top;
    }
</style>
<? //require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );?>


<div class="wrap">  
  <h2>Recent Press</h2>
    <table id="recent-press-list">
    <tr id="0" class="recent-press-element-header">
    	<th class="d_order">Display Order</th>
    	<th class="dater">Date</th>
    	<th class="d_dater">Custom Date</th>
    	<th class="titler">Title</th>
    	<th class="snippet">Snippet (Summary)</th>
    	<th class="linker">Link</th>
    	<th class="logo_linker">Link to Logo</th>
    	<th class="outlet_name">Outlet Name</th>
    	<th class="update"></th>
    	<th class="add"></th>
    	<th class="remove"></th>
    </tr>
<?  global $wpdb;
    $table_name = $wpdb->prefix . 'hca_recent_press';
    $sql = 'SELECT * FROM '.$table_name.' ORDER BY d_order';
    $results = $wpdb->get_results( $sql , OBJECT );

    foreach ($results as $press) : ?>
    <tr id="<? echo $press->d_order;?>" rel="ID-<? echo $press->id;?>" class="recent-press-element">
    	<td class="d_order"><? echo $press->d_order;?></td>
    	<td class="dater"><input id="<? echo $press->id;?>-dater" value="<? echo $press->dater;?>" /></td>
    	<td class="d_dater"><input id="<? echo $press->id;?>-d_dater" value="<? echo $press->d_dater;?>" /></td>
    	<td class="titler"><input id="<? echo $press->id;?>-titler" value="<? echo $press->titler;?>" /></td>
    	<td class="snippet"><input id="<? echo $press->id;?>-snippet" value="<? echo $press->snippet;?>" /></td>
    	<td class="linker"><input id="<? echo $press->id;?>-linker" value="<? echo $press->linker;?>" /></td>
    	<td class="logo_linker"><input id="<? echo $press->id;?>-logo_linker" value="<? echo $press->logo_linker;?>" /></td>
    	<td class="outlet_name"><input id="<? echo $press->id;?>-outlet_name" value="<? echo $press->outlet_name;?>" /></td>
    	<td class="update"><div class='rp_btn'>Update</div></td>
        <td class="add"><div class='rp_btn'>Add Above</div></td>
        <td class="remove"><div class='rp_btn'>Remove</div></td> 
    </tr>
    <? endforeach; ?> 
    <? //}?>
    <tr class="recent-press-element" id="recent-press-element-placeholder"   
        style="display:none;">  
        <td id="new_d_order" class="d_order"></td>
        <td id="new_dater" class="dater"><input id="<? echo $id;?>-dater" /></td>
        <td id="new_d_dater" class="d_dater"><input id="<? echo $id;?>-d_dater" /></td>
        <td id="new_titler" class="titler"><input id="<? echo $id;?>-titler" /></td>
        <td id="new_snippet" class="snippet"><input id="<? echo $id;?>-snippet" /></td>
        <td id="new_linker" class="linker"><input id="<? echo $id;?>-linker" /></td>
        <td id="new_logo_linker" class="logo_linker"><input id="<? echo $id;?>-logo_linker" /></td>
        <td id="new_outlet_name" class="outlet_name"><input id="<? echo $id;?>-outlet_name" /></td>
        <td class="update"><div class='rp_btn'>Update</div></td>
        <td class="add"><div class='rp_btn'>Add Above</div></td>
        <td class="remove"><div class='rp_btn'>Remove</div></td> 
    </tr> 
    </table>
</div><!--wrap-->
