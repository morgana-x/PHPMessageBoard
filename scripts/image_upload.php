<?php
	function check_image_upload()
    {
        //print_r($_FILES);
        if (!isset($_FILES["file"]))
        {
            //echo("No uploaded file!");
            return NULL;
        }
        $uploadFile = $_FILES["file"];
        if ($uploadFile["size"] > UPLOADS_MAXFILESIZE) { // 1000kb
            echo("Uploaded attachment is too large! Max file size: " . UPLOADS_MAXFILESIZE);
            unset($_FILES["file"]);
            return NULL;
        }

        $userFolder = substr(hash('crc32', getIp()),0,5);
        if (!is_dir(UPLOADS_FOLDER . "/" . $userFolder))
            mkdir(UPLOADS_FOLDER . "/" .$userFolder);
        
		$imgExt = strtolower(pathinfo($uploadFile["name"],PATHINFO_EXTENSION));
		if(!in_array($imgExt, UPLOADS_ALLOWEDEXTENSIONS)) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            unset($_FILES["file"]);
            return NULL;
        }
        $imgDest =  $userFolder ."/". strval(time()) . "." . $imgExt;
        if (file_exists( UPLOADS_FOLDER . "/" . $imgDest))
        {
            echo("Error uploading file! File already exists??!?!?!");
            unset($_FILES["file"]);
            return NULL;
        }

     
    
        $realImgDest = UPLOADS_FOLDER . "/" . $imgDest;
        if (!move_uploaded_file($uploadFile["tmp_name"],$realImgDest))
        {
            echo("Error uploading file!");
            unset($_FILES["file"]);
            return NULL;
        }
        unset($_FILES["file"]);
        $imgInfo = getimagesize($realImgDest);
        $width = @$imgInfo [0];
        $height = @$imgInfo [1];
        $type = @$imgInfo [2];
        if ( !in_array ( $type, UPLOADS_ALLOWEDFORMATS ))
        {
            echo("Uploaded attachment is not an allowed image format!");
            unlink($realImgDest);
            return NULL;
        }
        if ($width > UPLOADS_MAX_WIDTH)
        {
            echo("Uploaded attachment is above max width of " . UPLOADS_MAX_WIDTH . " pixels!");
            unlink($realImgDest);
            return NULL;
        }
        if ($height > UPLOADS_MAX_HEIGHT)
        {
            echo("Uploaded attachment is above max height of " . UPLOADS_MAX_HEIGHT . " pixels!");
            unlink($realImgDest);
            return NULL;
        }
		$resizeHTML = "";//'style="max-width: 80%; max-height: 1000px;" ';
		if ($width > UPLOADS_MAX_WIDTH_RENDER)
			$resizeHTML .= "width=" . strval(UPLOADS_MAX_WIDTH_RENDER) . " ";
		if ($height > UPLOADS_MAX_HEIGHT_RENDER)
			$resizeHTML .= "height=" . strval(UPLOADS_MAX_HEIGHT_RENDER);
		
        //echo("uploaded file to " . $imgDest );
        return array(str_replace(__DIR__, "", UPLOADS_FOLDER_RELATIVE . "/". $imgDest), $resizeHTML);
    }
?>