<?php

/* CLEAN
--------------------------------------------------------------*/
function clean($str = '', $tag = false, $sql = false)
{
	if ($tag) $str = strip_tags($str);
	$str = trim(preg_replace('/\s+/', ' ', $str));
	if ($sql) $str = mysql_real_escape_string($str);
	
	return $str;
}

/* TRANSLIT
--------------------------------------------------------------*/
function translit($str)
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

/* RUS DATE FORMAT (month)
--------------------------------------------------------------*/
function rus_date_format($datetime)
{
	$months = array(
		'01'=>'Январь',
		'02'=>'Февраль',
		'03'=>'Март',
		'04'=>'Апрель',
		'05'=>'Май',
		'06'=>'Июнь',
		'07'=>'Июль',
		'08'=>'Август',
		'09'=>'Сентябрь',
		'10'=>'Октябрь',
		'11'=>'Ноябрь',
		'12'=>'Декабрь'
	);

	$date = explode('.',  date('Y.m.d', $datetime));
	
	return $date[0].' '.(isset($months[$date[1]]) ? $months[$date[1]] : $date[1] ).' '.$date[2]; 
}
/* DEL DIR
--------------------------------------------------------------*/
function delDir($directory = '')
{	
	$dir = opendir($directory);
	while ($file = readdir($dir)){
		if (is_file($directory."/".$file)){
			unlink ($directory."/".$file);
		}else if (is_dir($directory."/".$file) && ($file != ".") && ($file != "..")){
			delDir($directory."/".$file);
		}
	}

	closedir($dir);
	rmdir($directory);
}