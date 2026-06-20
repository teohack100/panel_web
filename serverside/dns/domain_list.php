<?php
require "dns_list.php";
?>
<?php
										    for($row = 1;$row < 101; $row++){
										    	if(!empty($dns_list_array[$row][0])){
											         echo '<option value="'.$dns_list_array[$row][0].'">';
										            echo $dns_list_array[$row][0];
										            echo '</option>';
											    } else {
											        break;
											    }
											}
										?>