<?php
class Adminlibrary
{
	public function translit($str)
	{
		$str = preg_replace("/[^а-яa-z\d\s\-]/u",'',$str);
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
	
	public function delDir($directory = ''){
		
		$dir = opendir($directory);
		while($file = readdir($dir)){
			if (is_file($directory."/".$file)){
				unlink ($directory."/".$file);
			}elseif(is_dir($directory."/".$file) && ($file != ".") && ($file != "..")){
				$this->delDir($directory."/".$file);
			}
		}

		closedir($dir);
		rmdir($directory);
		
		return;
	}
	
	public function getDir($directory = ''){
		if(!$directory || !is_dir($directory)){return array();}
		
		$arr = array();
		$directory = rtrim($directory, '/');
		$dir = opendir($directory);
		while($file = readdir($dir)){
			if (is_file($directory."/".$file)){
				$arr[] = $file;
			}
		}
		closedir($dir);
		
		return $arr;
	}

}
?>