<?php
class My_uploadify{
	
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////__UPLOAD FOR_PAGES	
	public function multiUploadForPages(){
	
		$CI =& get_instance();
		
		$wm     = (int)$_POST['wm'] ? 1 : 0;
		$width  = (int)$_POST['width'] ? (int)$_POST['width'] : 0;
		$height = (int)$_POST['height'] ? (int)$_POST['height'] : 0;
		
		$images = $_FILES['Filedata'];

		if ($images['error'] == 0){
			$dir = ROOT.'/img/for_page/';
			$op_dir = opendir($dir);
			while ($file = readdir($op_dir)){
				if (is_file($dir.$file)){
					if (basename($dir.$file) == $images['name']){
						echo 0;
						exit;
					}
				}
			}
			
			$name = preg_replace('/\.[a-z]{3,4}$/i', '', $images['name']);
			
			//проверяем существует ли папка FOR_PAGE
			if ( ! is_dir(ROOT.'/img/for_page')){
				mkdir(ROOT.'/img/for_page', 0755);
			}
			//проверяем существует ли папка MINI
			if ( ! is_dir(ROOT.'/img/for_page/mini')){
				mkdir(ROOT.'/img/for_page/mini', 0755);
			}
		
			//для превьюшки в админке
			$CI->my_imagemagic->resize($images['tmp_name'], ROOT.'/img/for_page/mini/'.$name.'.jpg', 100, 70);
			
			
			$new = $images['tmp_name'];
			if ($wm){
				$new = ROOT.'/img/for_page/'.$name.'.jpg';
				$CI->my_imagemagic->wm($images['tmp_name'], $new, ROOT.'/img/wm.png');
			}
			
			if ($width && $height){
				$CI->my_imagemagic->resize_2($new, ROOT.'/img/for_page/'.$name.'.jpg', $width, $height);
			}
			if (!$width && !$height){
				$CI->my_imagemagic->upload($new, ROOT.'/img/for_page/'.$name.'.jpg');
			}else{
				$CI->my_imagemagic->resize($new, ROOT.'/img/for_page/'.$name.'.jpg', max($width, $height));
			}
		}
	}
	
}