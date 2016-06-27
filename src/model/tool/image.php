<?php
class ModelToolImage extends Model {
	public function resize($filename, $width, $height, $local=0) {
		if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
			return;
		} 
		
		$info = pathinfo($filename);
		$extension = $info['extension'];

		$old_image = $filename;
		$new_image = 'cache/' . utf8_substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;
		
		if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
			$path = '';
			
			$directories = explode('/', dirname(str_replace('../', '', $new_image)));
			
			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;
				
				if (!file_exists(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}		
			}
			
			$image = new Image(DIR_IMAGE . $old_image);
			$image->resize($width, $height);
			$image->save(DIR_IMAGE . $new_image);
		}

		/**	
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			return HTTPS_IMAGE . $new_image;
		} else {
			return HTTP_IMAGE . $new_image;
		}
		*/
		//use local directory only for management
		if ( $local == 0 ) {
			return LOCAL_IMAGE . $new_image;	
		} else {
			return DIR_IMAGE . $new_image;
		}
	}

	public function resize_product_image($filename, $width, $height) {
        error_log("resize orig filename:" . $filename);

		if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
            error_log("can't find image " . DIR_IMAGE . $filename);
			return null;
		} 
		
		$info = pathinfo($filename);
		//$extension = $info['extension'];
		$extension = "jpg";
		
		$old_image = $filename;
		$new_image = 'thumb/' . utf8_substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;
		
		//if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
        $path = '';
			
        $directories = explode('/', dirname(str_replace('../', '', $new_image)));
		
        foreach ($directories as $directory) {
            $path = $path . '/' . $directory;
			
            if (!file_exists(DIR_IMAGE . $path)) {
                @mkdir(DIR_IMAGE . $path, 0777);
            }		
        }
		
        $image = new Image(DIR_IMAGE . $old_image);
        $image->resize_no_fill($width, $height, true);
        $image_info = $image->getInfo();
        $new_image = 'thumb/' . utf8_substr($filename, 0, strrpos($filename, '.')) . '-' . $image_info['width'] . 'x' . $image_info['height'] . '.' . $extension;
        $image->save(DIR_IMAGE . $new_image);
        
        $md5_tag = md5_file(DIR_IMAGE . $new_image);
        $new_md5_image = 'thumb/' . utf8_substr($filename, 0, strrpos($filename, '.')) . '-' . $image_info['width'] . 'x' . $image_info['height'] . '-' . $md5_tag . '.' . $extension;

        rename(DIR_IMAGE . $new_image, DIR_IMAGE . $new_md5_image );

        error_log("new_image=" . $new_md5_image);
        
        return $new_md5_image;

	}

    public function resize_card_image($filename, $width, $height) {
        error_log("resize orig filename:" . $filename);

		if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
            error_log("can't find image " . DIR_IMAGE . $filename);
			return null;
		} 
		
		$info = pathinfo($filename);
		//$extension = $info['extension'];
		$extension = "jpg";
		
		$old_image = $filename;
		$new_image = 'thumb/' . utf8_substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;
		
		//if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
        $path = '';
			
        $directories = explode('/', dirname(str_replace('../', '', $new_image)));
		
        foreach ($directories as $directory) {
            $path = $path . '/' . $directory;
			
            if (!file_exists(DIR_IMAGE . $path)) {
                @mkdir(DIR_IMAGE . $path, 0777);
            }		
        }
		
        $image = new Image(DIR_IMAGE . $old_image);
        $image->resize_no_fill($width, $height, true);
        $image_info = $image->getInfo();
        $new_image = 'thumb/' . utf8_substr($filename, 0, strrpos($filename, '.')) . '-' . $image_info['width'] . 'x' . $image_info['height'] . '.' . $extension;
        $image->save(DIR_IMAGE . $new_image, 70);
        
        $md5_tag = md5_file(DIR_IMAGE . $new_image);
        $new_md5_image = 'thumb/' . utf8_substr($filename, 0, strrpos($filename, '.')) . '-' . $image_info['width'] . 'x' . $image_info['height'] . '-' . $md5_tag . '.' . $extension;

        rename(DIR_IMAGE . $new_image, DIR_IMAGE . $new_md5_image );

        error_log("new_image=" . $new_md5_image);
        
        return $new_md5_image;
        
    }
}


?>
