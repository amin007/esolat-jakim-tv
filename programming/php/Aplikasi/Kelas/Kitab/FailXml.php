<?php
/* Ini class untuk
 */
namespace Aplikasi\Kitab; //echo __NAMESPACE__;
class FailXml
{
#==================================================================================================================
#------------------------------------------------------------------------------------------------------------------
	public function buatFailXml($dataCantum, $tahun, $bln)
	{
		# creating object of SimpleXMLElement
		$xml_user_info = new \SimpleXMLElement("<?xml version=\"1.0\"?><waktu_solat></waktu_solat>");

		# function call to convert array to xml
		$this->arrayToXml($dataCantum,$xml_user_info);

		# saving generated xml file
		$folder = 'sumber/fail/xml/';
		$namafail = $folder . 'waktu_solat_' . $tahun . '.xml';
		//$namafail = $folder . 'waktu_solat_bulan_' . $bln . '.xml';
		$xml_file = $xml_user_info->asXML($namafail);
		//$xml_file = $xml_user_info->asXML();

		# success and error message based on xml creation
		if($xml_file)
		{
			echo 'XML file ' . $namafail . ' have been generated successfully. '
			. '<a target="_blank" href="' . $namafail . '">Click here</a>';
			//echo $xml_file;
		}
		else
		{
			echo 'XML file generation error.';
		}//*/
	}
#------------------------------------------------------------------------------------------------------------------
	public function arrayToXml($array, &$xml_user_info) 
	{
		# buat pecahan tatasusunan
		foreach($array as $key => $value)
		{
			if(is_array($value)) 
			{
				if(!is_numeric($key))
				{
					$subnode = $xml_user_info->addChild("$key");
					$this->arrayToXml($value, $subnode);
				}
				else
				{
					//$subnode = $xml_user_info->addChild("item$key");
					$subnode = $xml_user_info->addChild("item");
					$this->arrayToXml($value, $subnode);
				}
			}
			else
			{
				$xml_user_info->addChild("$key",htmlspecialchars("$value"));
			}
		}
	}
#------------------------------------------------------------------------------------------------------------------
#------------------------------------------------------------------------------------------------------------------
#------------------------------------------------------------------------------------------------------------------
#==================================================================================================================
}