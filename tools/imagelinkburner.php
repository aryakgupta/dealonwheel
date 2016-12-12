<!DOCTYPE html>
<html>
<title>Image link bubner::</title>
<head>
<style>
   table, th, td {border: 1px solid black;}
</style>
</head>
<body>
<h2>Image path burner::</h2><br>
<form action="" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload CSV File" name="submit">
</form>

</body>
</html>


<?php
/*
Auther: Pradeep Gupta
Date: Dec 12 2016
Desc: Fuse sheet to return article name and all related articles
*/

if(isset($_FILES['fileToUpload'])){
    $errors= array();
    $file_name = $_FILES['fileToUpload']['name'];
    $file_size = $_FILES['fileToUpload']['size'];
    $file_tmp = $_FILES['fileToUpload']['tmp_name'];
    $file_type = $_FILES['fileToUpload']['type'];
    $file_ext=strtolower(end(explode('.',$_FILES['fileToUpload']['name'])));

    $expensions= array("csv");

    if(in_array($file_ext,$expensions)=== false){
        $errors[]="extension not allowed, please choose a csv file.";
    }

    if($file_size > 2097152) {
        $errors[]='File size must be excately 2 MB';
    }

    if(empty($errors)==true) {
        //move_uploaded_file($file_tmp,"uploaded/".$file_name);
        echo "File uploaded Successfully ... processing....";
        // csv file rading and processing.....
     $res = doProcessing($file_tmp);
     if ($res){
        // process and display the result::
        $articleNumber = array();
        $articleImage = array();
        foreach($res['article'] as $row){
            $key = $row;
            $tmpkey = explode( '-', trim($key), 2)[0];
            $tmpkey = explode('_', $tmpkey, 2)[0];
            if( !array_key_exists($tmpkey, $articleNumber) ) $articleNumber[$tmpkey] = '';
        }
        foreach($res['img'] as $row){
            $value = $row;
            $tmpvalue = trim($value);
            if( !array_key_exists($tmpvalue, $articleImage) ) $articleImage[] = $tmpvalue;
        }
        // echo '<pre>';
        // print_r($articleNumber);
        ob_end_clean();
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=result.csv');
        header('Pragma: no-cache');
        foreach( $articleImage as $imgname){
            $fileName = split('[/]', $imgname);
            $fileName = $fileName[count($fileName)-1];
            $tmpname = explode( '-', $fileName, 2)[0];
            $tmpname = explode('_', $tmpname, 2)[0];
            if( array_key_exists($tmpname, $articleNumber)){
                $articleNumber[$tmpname] = $articleNumber[$tmpname] ? $articleNumber[$tmpname].','.$imgname : $imgname;
            }
        }
        //print_r($articleNumber);
        //echo '<table><tr><td>Article</td><td>Image</td></tr>';
        foreach($articleNumber as $key => $value){
            //echo "<tr><td>$key</td><td>$value</td></tr>";
            echo $key.','.$value;
            echo "\r\n";
        }
        //echo '</table>';
     }
     
    }else{
        echo "failed to upload file...";
        print_r($errors);
    }
}

function doProcessing($csvfile){
    ini_set('auto_detect_line_endings',TRUE);
    if (($handle = fopen($csvfile, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $num = count($data);
            //echo "<p> $num fields in line $row: <br /></p>\n";
            $row++;
            if($row==1) continue;
            // for ($c=0; $c < $num; $c++) {
            //     if( trim($data[$c]) !='' )
            //         $result[$row][] = $data[$c];
            // }
            if( trim($data[0]) !='' ) $result['article'][] = trim($data[0]);
            if( trim($data[1]) !='' ) $result['img'][] = trim($data[1]);
        }
    fclose($handle);
    // echo '<pre>';
    // print_r($result);
    return $result;
   }else{
        echo '<br> Could not read file..';
        return false;
   }
}

?>
