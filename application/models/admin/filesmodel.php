<?php 
/*	crystalline.in.ua
*	настрайка главной папки определяется вручную  define("FM_FOLDER", ROOT.'/img/root');
*/
Class filesmodel extends CI_Model{
	private static $settings = array();
	private $size = array();
	
	public function __construct()
    {
        parent::__construct();

		if (defined(ROOT)) {
			define("ROOT", $_SERVER['DOCUMENT_ROOT']);
		}
		
		define("FM_FOLDER", ROOT.'/img/root');
		if ( ! is_dir(FM_FOLDER)) 
			mkdir(FM_FOLDER, 0777);
		
		if( ! self::$settings){
			self::$settings = $this->settingsModel->getSettings();
		}
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////GET_TREE
	# Формирует ДЕРЕВО КАТАЛОГА
	public function getTree($directory = '', $data = array(), $path = '')
	{
		if ( ! $directory) $directory = FM_FOLDER;
		
		if (is_dir($directory)){
			$data = array(
				'name'=> basename($directory),
				'path'=> ($path ? $path . '/' : '').basename($directory),
				'child'=>array()
			);
		}
		
		$arr = scandir($directory);
		foreach ($arr as $k){
			if ($k == '.' || $k == '..') continue;
			
			$d = $directory . '/' . $k;
			if (is_dir($d) && $k != '_cache_'){
				$data['child'][] = $this->getTree($d, $data, $data['path']);
			}
		}

		return $data;
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////GET IMAGES
	# Выбирвет изабражения из конкретной папки*/
	public function getImage()
	{
		if ( ! isset($_POST['path'])) return;
		
		$path = trim($_POST['path'], '/');
		if ( ! preg_match('/^root/', $path)){echo'нет root';exit;}
		if ( ! is_dir(ROOT.'/img/'.$path)){echo 0;exit;}
		
		$directory = ROOT.'/img/'.$path;
		
		$arr = array();
		$directory = rtrim($directory, '/');
		$dir = opendir($directory);
		while ($file = readdir($dir)){
			if (is_file($directory."/".$file)){
			
				# делаем превью 100\100 (надо переделать!!!!!!!!!!)
				$cache = $directory.'/_cache_';
				if ( ! is_dir($cache)) mkdir($cache, 0777);
				
				if ( ! is_file($cache.'/'.$file)){
					@$this->_resize($directory."/".$file, $cache.'/'.$file , 100, 100);
				}
				
				# размеры из таб. настроек
				if (isset(self::$settings->sizes)) foreach (self::$settings->sizes as $size){
				
					if ( ! $size) continue;
					
					# вставить в название 100x100 ...
					$name = preg_replace('/(.*)(\..+)$/iu', '${1}'.$size.'x'.$size.'${2}', $file);
					
					if ( ! is_file($cache.'/'.$name)){
						@$this->_resize($directory."/".$file, $cache.'/'.$name , $size, 100);
					}
				}
				
				$arr[] = array(
					'name'=> $file,
					'cache'=> '/img/'.$path.'/_cache_/'.$file,
					'path'=> '/img/'.$path.'/'.$file,
					'size'=> round(filesize($directory."/".$file)/1000).'KB'
				);
			}
		}
		closedir($dir);

		return $arr;
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////ADD IMAGE
	public function addImage()
	{	
		if ( ! isset($_POST['path'])) return;
		
		$path = trim($_POST['path'], '/');
		if ( ! preg_match('/^root/', $path)){echo'нет root';exit;}
		
		$path = '/img/'.$path;
		if ( ! is_dir(ROOT.$path)){echo 0;exit;}
		
		$image = $_FILES['image'];
		if ($image['error'] == 0){
			
			$file = $image['name'];

			if (move_uploaded_file($image['tmp_name'], ROOT.$path.'/'.$file)){
				# watermark
				if (isset($_POST['watermark']) && $_POST['watermark']){
					// $this->_add_watermark(ROOT.$path.'/'.$file, ROOT.$path.'/'.$file, 'CRYSTALLINE.IN.UA');
					$this->_add_watermark_image(ROOT.$path.'/'.$file, ROOT.$path.'/'.$file);
				}
				
				# делаем превью 100\100 (надо переделать!!!!!!!!!!)
				$cache = ROOT.$path.'/_cache_';
				if ( ! is_dir($cache)) mkdir($cache, 0777);
				
				if ( ! is_file($cache.'/'.$file)){
					$this->_resize(ROOT.$path.'/'.$file, $cache.'/'.$file , 100, 100);
				}
				
				# размеры из таб. настроек
				if (isset(self::$settings->sizes)) foreach (self::$settings->sizes as $size){
					
					if ( ! $size) continue;
					
					# вставить в название 100x100 ...
					$name = preg_replace('/(.*)(\..+)$/iu', '${1}'.$size.'x'.$size.'${2}', $file);
					
					if ( ! is_file($cache.'/'.$name)){
						$this->_resize(ROOT.$path."/".$file, $cache.'/'.$name , $size, 100);
					}
				}
				
				$data = array(
					'name'=> $file,
					'cache'=> $path.'/_cache_/'.$file,
					'path'=> $path.'/'.$file,
					'size'=> round(filesize(ROOT.$path."/".$file)/1000).'KB'
				);
				echo json_encode($data);
				exit;
			}
			
			echo 0;
			exit;
		}
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DEL IMAGE
	public function delImage()
	{
		if ( ! isset($_POST['path'])) return;

		$image = ROOT.$_POST['path'];
		
		$cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $image);
		@unlink($cache);
		
		if (isset(self::$settings->sizes)) foreach (self::$settings->sizes as $size){	
			$name = preg_replace('/(.*)(\..+)$/iu', '${1}'.$size.'x'.$size.'${2}', $cache);
			@unlink($name);
		}
		
		@unlink($image);
		exit;
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////ADD DIR
	public function addDir()
	{
		if ( ! isset($_POST['path'])) return;
		
		$path = trim($_POST['path'], '/');
		$name = preg_replace('/[^а-яa-z0-9\s]/u','',$_POST['name']);
		$name = $this->_translit($name);

		$name = iconv("UTF-8", "WINDOWS-1251", $name);	//русские символы в windows XP
		
		if(!preg_match('/^root/', $path)){echo'нет root';exit;}
		
		mkdir(ROOT.'/img/'.$path.'/'.$name, 0777);
		
		echo 0;
		exit;
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DEL DIR
	# Удаляет папки и всё что в ней находится
	public function delDir()
	{	
		if ( ! isset($_POST['path'])) return;
		
		$path = trim($_POST['path'], '/');
		if ( ! preg_match('/^root/', $path)){
			echo'нет root';
			exit;
		}
		
		$dir = ROOT.'/img/'.$path;
		if ($dir) $this->_delDir($dir);
		
		echo 0;
		exit;
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////__DEL DIRS
	# Функция (каскадного) удаления папок
	public function _delDir($directory = '')
	{
		$dir = opendir($directory);
		while($file = readdir($dir)){
			if (is_file($directory."/".$file)){
				unlink ($directory."/".$file);
			}elseif(is_dir($directory."/".$file) && ($file != ".") && ($file != "..")){
				$this->_delDir($directory."/".$file);
			}
		}

		closedir($dir);
		rmdir($directory);
		
		return;
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////__RESIZE IMAGE
	public function _resize($urlImg = '', $out = '', $size = 0, $q = 100)
	{
		if ( ! $size || !$urlImg) return;
		
		$info = getimagesize($urlImg);
		if ( ! $info) return;
		

		list($type, $mime) = explode('/', $info['mime']);
		
		$fnCreate = 'imagecreatefrom'.$mime;
		$fnImage = 'image'.$mime;
		
		if ( ! function_exists($fnCreate)) return;
		
		$img = $fnCreate($urlImg);
		if ( ! $img){
			log_message('error', 'Не смог открыть изображение '.$fnCreate.'(): '.$urlImg.'. filesmodel.php '.__LINE__);
			return;
		}
		
		$w = imagesx($img);
		$h = imagesy($img);
		
		$max = max($w, $h);
		
		if ($max > $size){
			if ($w > $h){
				$kf = $w/$size;
				$newW = $size;	
				$newH = ceil($h/$kf);			
			}else{
				$kf = $h/$size; 
				$newW = ceil($w/$kf);
				$newH = $size;
			}

			$n = imagecreatetruecolor($newW, $newH);
			
			imagealphablending($n, false);
			imagesavealpha($n, true);
			
			imagecopyresampled($n, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
			
			$fnImage($n, $out);
			
			imagedestroy($n);
			imagedestroy($img);
		}else{
			# надо для PNG (для JPG оно не мешает)
			imagealphablending($img, false);
			imagesavealpha($img, true);
			
			$fnImage($img, $out);
			
			imagedestroy($img);
		}
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// TRANSLIT
	private function _translit($str)
	{
		$str = preg_replace("/[^а-яa-z\d\s\-\+]/u", '', $str);
		$str = preg_replace("/\s+/",' ',$str);
		$str = trim($str);
		
		$tr = array(
			"А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
			"Д"=>"D","Е"=>"E","Ё"=>"Yo","Ж"=>"J","З"=>"Z","И"=>"I",
			"Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
			"О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
			"У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
			"Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
			"Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
			"в"=>"v","г"=>"g","д"=>"d","е"=>"e","ё"=>"yo","ж"=>"j",
			"з"=>"z","и"=>"i","і"=>"i","й"=>"y","к"=>"k","л"=>"l",
			"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
			"с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
			"ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
			"ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya"," "=>"-"
		);
		
		return strtr($str,$tr);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// WATERMARK TEXT
	private function _add_watermark($img = '', $out = '', $text = '', $font = '')
	{
		
		$font = $font ? $font : ROOT.'/css/fonts/arial.ttf';
		if ( ! file_exists($font)){
			log_message('error', 'Ненайден файл: '.$font.'. Watermark. function_helper.php');
			return;
		}
		
		$fontSize = 100;
		$angle = 0;
		
		# узнаем размеры холста при шрифте 70px
		$box = imagettfbbox($fontSize, $angle, $font, $text);
		$widthString = abs($box[2]) + 80;
		$heightString = abs($box[5]) + 100;
		
		$wm = imagecreatetruecolor($widthString, $heightString);
		$color = imagecolorallocatealpha($wm, 0, 0, 0, 127);
		imagefill($wm, 0, 0, $color);
		imagesavealpha($wm, true);

		# текст
		$x = 40;
		$y = $heightString - 50;
		$c = imagecolorallocatealpha($wm, 0, 0, 0, 100);
		
		# записываем строку на изображение
		imagettftext($wm, $fontSize, $angle, $x, $y, $c, $font, $text);
		
		
		# основное изображение
		$info = @getimagesize($img);
		if ( ! $info){
			log_message('error', 'Ошибка getimagesize(): '.$img.'. Watermark. function_helper.php '.__LINE__);
			return;
		}
		
		list($type, $mime) = explode('/', $info['mime']);
		$fnCreate = 'imagecreatefrom'.$mime;
		$fnImage = 'image'.$mime;
		
		if( ! function_exists($fnCreate)){
			log_message('error', 'Ошибка function not exists: '.$fnCreate.'. Watermark. function_helper.php '.__LINE__);
			return;
		}
		
		$img = $fnCreate($img);
		if ( ! $img){
			log_message('error', 'Ошибка '.$fnCreate.'(): '.$img.'. Watermark. function_helper.php '.__LINE__);
			return;
		}
		
		# для PNG
		imagealphablending($img, true);
		imagesavealpha($img, true);
		
		$width = imagesx($img);
		$height = imagesy($img);

		$kfx = $heightString / $widthString;
		$new_width = $width;
		$new_height = $new_width * $kfx;
		
		imagecopyresampled($img, $wm, 0, $height - $new_height, 0, 0, $new_width, $new_height, $widthString , $heightString);
		$q = $mime == 'jpeg' ? 100 : 0;
		$fnImage($img, $out, $q);
		
		imagedestroy($img);
		imagedestroy($wm);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// WATERMARK IMAGE
	private function _add_watermark_image($img = '', $out = '', $wm = '')
	{
		$wm = $wm ? $wm : ROOT.'/img/i/wm.png';
		if ( ! file_exists($wm)){
			log_message('error', 'Ненайден файл: '.$wm.'. Watermark. function_helper.php');
			return;
		}
		
		# wm изображение
		$infoWm = @getimagesize($wm);
		
		list($type, $mime) = explode('/', $infoWm['mime']);
		$fnCreate = 'imagecreatefrom'.$mime;
		$fnImage = 'image'.$mime;
		
		$wm = $fnCreate($wm);
		
		$widthWm = imagesx($wm);
		$heightWm = imagesy($wm);
		
		
		# основное изображение
		$infoImg = @getimagesize($img);
		
		list($type, $mime) = explode('/', $infoImg['mime']);
		$fnCreate = 'imagecreatefrom'.$mime;
		$fnImage = 'image'.$mime;
		
		$img = $fnCreate($img);
		
		$width = imagesx($img);
		$height = imagesy($img);

		$kfx = $heightWm / $widthWm;
		$new_width = $width;
		$new_height = $new_width * $kfx;
		
		imagecopyresampled($img, $wm, 0, $height - $new_height, 0, 0, $new_width, $new_height, $widthWm , $heightWm);
		$q = $mime == 'jpeg' ? 100 : 0;
		$fnImage($img, $out, $q);
		
		imagedestroy($img);
		imagedestroy($wm);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// WATERMARK ANGLE
	private function _add_watermark_angle($img = '', $out = '', $text = '', $font = '', $r = 128, $g = 128, $b = 128, $alpha = 100)
	{
		$font = $font ? $font : ROOT.'/css/fonts/arial.ttf';
		if ( ! file_exists($font)){
			log_message('error', 'Ненайден файл: '.$font.'. Watermark. function_helper.php '.__LINE__);
			return;
		}
		
		# основное изображение
		$info = @getimagesize($img);
		if ( ! $info){
			log_message('error', 'Ошибка getimagesize(): '.$img.'. Watermark. function_helper.php '.__LINE__);
			return;
		}
		
		list($type, $mime) = explode('/', $info['mime']);
		$fnCreate = 'imagecreatefrom'.$mime;
		$fnImage = 'image'.$mime;
		
		if( ! function_exists($fnCreate)){
			log_message('error', 'Ошибка function not exists: '.$fnCreate.'. Watermark. function_helper.php '.__LINE__);
			return;
		}

		$img = $fnCreate($img);
		if ( ! $img){
			log_message('error', 'Ошибка '.$fnCreate.'(): '.$img.'. Watermark. function_helper.php '.__LINE__);
			return;
		}
		
		# для PNG
		imagealphablending($img, true);
		imagesavealpha($img, true);
		
		# получаем ширину и высоту исходного изображения
		$width = imagesx($img);
		$height = imagesy($img);
		
		# угол поворота текста
		$angle =  -rad2deg(atan2((-$height),($width))); 

		# добавляем пробелы к строке
		$text = "  ".$text."  ";

		$c = imagecolorallocatealpha($img, $r, $g, $b, $alpha);
		$size = ($width+$height) / strlen($text);
		$box  = imagettfbbox ( $size, $angle, $font, $text );
		$x = $width/2 - abs($box[4] - $box[0])/2;
		$y = $height/2 + abs($box[5] - $box[1])/2;

		# записываем строку на изображение
		imagettftext($img, $size ,$angle, $x, $y, $c, $font, $text);
		
		$q = $mime == 'jpeg' ? 100 : 0;
		$fnImage($img, $out, $q);

		imagedestroy($img);
	}
	
}