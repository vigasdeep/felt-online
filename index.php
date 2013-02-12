<?php
include('functions.php');
exec('rm data.txt');
exec('rm combined.txt');
exec('rm *.graph');
exec('rm *.ps');
exec('rm *.png');
#database details
#include "config.php";

#errors 
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

#server setting
$uploadfile = "/home/bongo/public_html/felt/felt-online/";

#home pc setting
#$uploadfile = "/var/www/felt/"; 

#work if file has been posted for upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
	{
	$url = !empty($_POST['stadFileURL']) ? $_POST['stadFileURL'] : '' ;
	if($url != '') {
		
		$stadfilename  = basename($url);
		downloadFile($url,$uploadfile);
	}
	else{
	      	$stadfilename  = $_FILES['stadfilex']['name']; 
		
		$type     = $_FILES['stadfilex']['type'];
		$tmp_name = $_FILES['stadfilex']['tmp_name'];
		$error    = $_FILES['stadfilex']['error'];
		$size     = $_FILES['stadfilex']['size'];
		
		#checks if file uploaded or not
		switch ($error) {
			case UPLOAD_ERR_OK:
				$response = 'There is no error, the file uploaded with success.';
				move_uploaded_file($_FILES['stadfilex']['tmp_name'], $uploadfile.$stadfilename);
				break;
			case UPLOAD_ERR_INI_SIZE:
				$response = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$response = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
				break;
			case UPLOAD_ERR_PARTIAL:
				$response = 'The uploaded file was only partially uploaded.';
				break;
			case UPLOAD_ERR_NO_FILE:
				$response = 'No file was uploaded.';
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$response = 'Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.';
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$response = 'Failed to write file to disk. Introduced in PHP 5.1.0.';
				break;
			case UPLOAD_ERR_EXTENSION:
				$response = 'File upload stopped by extension. Introduced in PHP 5.2.0.';
				break;
			default:
				$response = 'Unknown error';
				break;
		}
	}
		exec('rm data.txt');
		exec('rm combined.txt');
		exec('rm *.png');
		exec('rm *.ps');
		$res = exec('felt -graphics graph.graph '. $stadfilename. ' > data.txt');
		exec('env /usr/local/bin/gnuplot demo.dem');
		exec('env /usr/local/bin/gnuplot demox.dem');	
		exec('convert -rotate 90 post.ps post.png');
		exec('convert -rotate 90 postx.ps postx.png');
		exec('cp '.$stadfilename.' combined.txt');
		$comb_data = file_get_contents('data.txt');
		$handle = fopen('combined.txt', 'a');
		fwrite($handle , $comb_data);
		#file removed for next time ;-)
		unlink($stadfilename);
		echo '<h1><center><a href = "data.txt">Single Result here </a></h1>';
		echo '<h1><center><a href = "combined.txt"> Combined Result here </a><br>';
		echo '<img src = "post.png">';
		echo '<img src = "postx.png">';
}		

#will work if data is not posted
else {
?>	
		<form action="" enctype="multipart/form-data" method="post">
		<center>
		<h1> FELT File Input </h1><br> <br /><input name="stadfilex" type="file" /><br />
			<h2> OR, upload by URL </h2> 
		<input type="text" name="stadFileURL" size="80"/> <br />
		<input type="submit" value="Submit" />
		<center />
		</form>
		<a href = "https://hsrai.wordpress.com/2013/01/01/felt/" ><h2> Refrence documents </h2></a>
<?php 
}
?>	
