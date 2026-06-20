<?php 
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../includes/functions.php';

chkSession();
if($user_id_2 == 1 || $reseller_id_2 == 1){
	
}else{
	header("Location: /myaccount");	
}

if($user_id_2 == 1){
	$sql = "Select * from vouchers WHERE is_used=1";
}else{
	$sql = "Select * from vouchers WHERE reseller_id = '".$user_id_2."' AND is_used=1";	
}	
	$filename = "excel-".date("d-m-Y--h-i-s");
	$result = mysql_query($sql) or die("Couldn't execute query:<br>" . mysql_error(). "<br>" . mysql_errno()); 
	$file_ending = "xls";
	//header info for browser
	header("Content-Type: application/xls");    
	header("Content-Disposition: attachment; filename=$filename.xls");  
	header("Pragma: no-cache"); 
	header("Expires: 0");
	/*******Start of Formatting for Excel*******/   
	//define separator (defines columns in excel & tabs in word)
	$sep = "\t"; //tabbed character
	//start of printing column names as names of MySQL fields
	for ($i = 0; $i < mysql_num_fields($result); $i++) {
	echo mysql_field_name($result,$i) . "\t";
	}
	print("\n");    
	//end of printing column names  
	//start while loop to get data
		while($row = mysql_fetch_row($result))
		{
			$schema_insert = "";
			for($j=0; $j<mysql_num_fields($result);$j++)
			{
				if(!isset($row[$j]))
					$schema_insert .= "NULL".$sep;
				elseif ($row[$j] != "")
					$schema_insert .= "$row[$j]".$sep;
				else
					$schema_insert .= "".$sep;
			}
			$schema_insert = str_replace($sep."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			print(trim($schema_insert));
			print "\n";
		} 
?>
