<?php
/* Ini class untuk
 */
namespace Aplikasi\Kitab; //echo __NAMESPACE__;
class PetaXml
{
#==================================================================================================================
#-----------------------------------------------------------------------------------------------------------------
	public function dataBulan($tahun)
	{
		$pusing = array('01','02','03','04','05','06',
		'07','08','09','10','11','12');
		$w1 = 'http://www.e-solat.gov.my/web/waktusolat.php';
		$w2 = 'http://www.e-solat.gov.my/web/muatturun.php';

		return array($pusing,$w1,$w2);
	}
#-----------------------------------------------------------------------------------------------------------------
	function __construct($tahun,$zone)
	{
		# 1. dapatkan tatasusunan dan pusing
		list($pusing,$w1,$w2) = $this->dataBulan($tahun);
		foreach ($pusing as $bln):
			//$setahun = $w1 . '?zone=JHR04&state=JOHOR&year=2017&jenis=year&bulan=' . $bln . '&LG=BM';
			$setahun = $w2 . '?zone=' . $zone
				.'&state=JOHOR&jenis=year&lang=my&year='
				. $tahun . '&bulan=' . $bln . '';
			$htmlContent = file_get_contents($setahun);
			list($tajuk,$dataJadual) = $this->korekData($tahun,$bln,$htmlContent);
		endforeach;

		$dataCantum = array_merge($tajuk, $dataJadual);
		$this->debug($tajuk,$dataJadual,$dataCantum);
		//$fail = new \Aplikasi\Kitab\FailXml();
		//$fail->buatFailXml($dataCantum, $tahun, $bln);
	}
#-----------------------------------------------------------------------------------------------------------------
	public function debug($tajuk,$dataJadual,$dataCantum)
	{
		echo '<pre>';
		//echo 'tajuk->'; print_r($tajuk);
		echo '<hr>data->'; print_r($dataJadual);
		//echo '<pre>dataCantum->'; print_r($dataCantum);
	}
#-----------------------------------------------------------------------------------------------------------------
	public function aturData($tahun)
	{
		$lepas = $tahun - 1;
		$depan = $tahun + 1;
		$senaraiKey = array(0,1,2,3,4,5,6,7,8,9);
		$senaraiTajuk = array('Tarikh','Hari','Imsak','Subuh','Syuruk','Zohor','Asar','Maghrib','Isyak');
		$buangData = array('Januari','Februari','Mac','April','Mei','Jun',
			'Julai','Ogos','September','Oktober', 'November', 'Disember',
			'Disember ' . $lepas,'Januari ' . $depan);
		$i = $j = $isi = 0;

		return array($senaraiKey,$senaraiTajuk,$buangData,$i,$j,$isi);
	}
#-----------------------------------------------------------------------------------------------------------------
	public function korekData($tahun,$bln,$htmlContent)
	{
		$DOM = new \DOMDocument();
		@$DOM->loadHTML($htmlContent);

		//$Header = $DOM->getElementsByTagName('td');
		$Detail = $DOM->getElementsByTagName('font');

		# Get row data/detail table without header name as key
		list($senaraiKey,$senaraiTajuk,$buangData,$i,$j,$isi) = $this->aturData($tahun);
	
		foreach($Detail as $kunci => $sNodeDetail) 
		{
			if ( in_array($kunci,$senaraiKey) ):
				$tajuk['tajuk' . $isi++] = trim(
					preg_replace('/\s\s+/', ' ', 
					$sNodeDetail->textContent));
			else:
				//echo '<hr>' . $j . '[' . $i++ . ']='
				//. trim($sNodeDetail->textContent);
				if ( !in_array(trim($sNodeDetail->textContent),$buangData) )
				{
					$dataJadual[trim($j.$bln)][$senaraiTajuk[$i]] = 
						($senaraiTajuk[$i] == 'Tarikh') ? # tambah tahun pada tarikh
						$this->tukarNamaBulan($sNodeDetail->textContent, $tahun) : 
						$this->tukarJam($sNodeDetail->textContent);
					$i++;
				}
			endif;
			$i = ($i == 9) ? 0 : $i++;
			$j = $i % count($senaraiTajuk) == 0 ? $j + 1 : $j;
		}

		return array($tajuk,$dataJadual);
	}
#-----------------------------------------------------------------------------------------------------------------
	public function tukarNamaBulan($tarikh,$tahun)
	{
		$buangData = array('Januari','Februari','Mac',
		'April','Mei','Jun','Julai','Ogos','September',
		'Oktober', 'November', 'Disember');
		$gantiData = array('Jan','Feb','Mac','Apr','Mei','Jun',
		'Jul','Ogo','Sep','Okt','Nov','Dis');

		$bln = str_replace($buangData, $gantiData, trim($tarikh) );
		//echo $bln . ' ' . $tahun . '<hr>';
		return $bln . ' ' . $tahun;
	}
#-----------------------------------------------------------------------------------------------------------------
	function tukarJam($data)
	{
		//$var = myGetType($data);
		$data = trim($data);
		//$data = ($var == 'numeric') ? ubahJam($data) : $data;
		//echo "$data = $var <hr>";
		return $data;
	}
#-----------------------------------------------------------------------------------------------------------------
	function myGetType($var)
	{
		if (is_array($var)) return "array";
		if (is_bool($var)) return "boolean";
		if (is_float($var)) return "float";
		if (is_int($var)) return "integer";
		if (is_null($var)) return "NULL";
		if (is_numeric($var)) return "numeric";
		if (is_object($var)) return "object";
		if (is_resource($var)) return "resource";
		if (is_string($var)) return "string";
		return "unknown type";
	}
#-----------------------------------------------------------------------------------------------------------------
	function ubahJam($data)
	{
		$d = strlen($data);
		$data = ($d == 4 ) ? '0' . $data : $data;
		//echo "$data = $d" . '<hr>';
		$data = str_replace('.', ':', $data );
		return $data;
	}
#-----------------------------------------------------------------------------------------------------------------
#-----------------------------------------------------------------------------------------------------------------
#==================================================================================================================
}