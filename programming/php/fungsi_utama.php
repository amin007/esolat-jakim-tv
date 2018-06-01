<?php
//namespace Aplikasi\Kitab; //echo __NAMESPACE__; 
function dpt_url()
{
	$url = isset($_GET['url']) ? $_GET['url'] : null;
	$url = rtrim($url, '/');
	$url = filter_var($url, FILTER_SANITIZE_URL);
	$url = explode('/', $url);

	return $url;
}
/* 2. masukkan semua fail class dari folder Aplikasi/Class
** URL : http://www.php-fig.org/psr/psr-4/examples/
** Contoh pelaksanaan projek khusus.
**
** @param string $class nama class yang sebenar tanpa namespace.
** @return void
**/
spl_autoload_register(function ($namaClass)
{
	# buat pecahan tatasusunan $namaClass
	$class = explode('\\', $namaClass); //print_r($class) . '<br>';
	# semak kewujudan class
	//echo '<hr>nama class:' . $class[count($class)-1] . ' | ';
	$cariFail = GetMatchingFiles(GetContents('Aplikasi/Kelas'),$class[count($class)-1] . '.php');
	# jika fail wujud, masukkan 
	foreach($cariFail as $kitabApa)
	{	//echo '$kitabApa->' . $kitabApa . '<br>';
		if (file_exists($kitabApa)) require $kitabApa;
		//else echo 'tidak jumpa daa<br>';
	}//*/
});

# lisfile2 - mula
function GetMatchingFiles($files, $search) 
{
	if($files==false):
		return false;
	else:
		# Split to name and filetype
		if(strpos($search,".")) 
		{
			$baseexp = substr($search,0,strpos($search,"."));
			$typeexp = substr($search,strpos($search,".")+1,strlen($search));
		} 
		else 
		{ 
			$baseexp = $search;
			$typeexp = "";
		} 

		# Escape all regexp Characters 
		$baseexp = preg_quote($baseexp); 
		$typeexp = preg_quote($typeexp); 

		# Allow ? and *
		$baseexp = str_replace(array("\*","\?"), array(".*","."), $baseexp);
		$typeexp = str_replace(array("\*","\?"), array(".*","."), $typeexp);

		# Search for Matches
		$i = 0;
		$matches = null; # $matches adalah array()
		foreach($files as $file) 
		{
			$filename = basename($file);
				  
			if(strpos($filename,".")) 
			{
				$base = substr($filename,0,strpos($filename,"."));
				$type = substr($filename,strpos($filename,".")+1,strlen($filename));
			} 
			else 
			{ 
				$base = $filename;
				$type = "";
			}

			if(preg_match("/^".$baseexp."$/i",$base) && preg_match("/^".$typeexp."$/i",$type))  
			{
				$matches[$i]=$file;
				$i++;
			}
		}

		return $matches;
	endif;
}

# Returns all Files contained in given dir, including subdirs
function GetContents($dir,$files=array()) 
{
	//if(!($res=opendir($dir))): exit("folder $dir tidak wujud!!!");
	if(!($res=@opendir($dir))): exit(\Aplikasi\Kelas\Kitab\Peta::folderPaparTidakWujud());
	else:
		while(($file=readdir($res))==TRUE) 
		if($file!="." && $file!="..")
			if(is_dir("$dir/$file")) 
				$files=GetContents("$dir/$file",$files);
			else array_push($files,"$dir/$file");

		closedir($res);
		return $files;
	endif;
}
# listfile2 - tamat