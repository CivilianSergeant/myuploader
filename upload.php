<?php
session_start();
ini_set('upload_max_filesize', '60M');   
ini_set('max_execution_time', '999');
ini_set('memory_limit', '128M');
ini_set('post_max_size', '60M'); 

if(!empty($_POST)){
	
	$filename = strtolower($_POST['filename']);
	if(!empty($filename)){
		
                $data = base64_decode(str_replace("data:;base64,", "", $_POST['data']));
            
                $fh = fopen($filename,"a+");
                echo fwrite($fh,$data);
                
           
	}

	/*print_r($_FILES);*/
	/*$_SESSION['buffer'] = file_put_contents($_FILES['file1']['name'], $_FILES['file1']['tmp_name']);*/
	/*$uploaded = move_uploaded_file($_FILES['file1']['tmp_name'], $_FILES['file1']['name']);

	header("Location:index.php");
*/

}
?>