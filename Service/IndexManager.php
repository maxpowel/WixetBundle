<?php

namespace Wixet\WixetBundle\Service;

class IndexManager
{
	
	public function __construct($indexCommandExtensions, $indexCommandContacts)
	{
		$this->indexCommandExtensions = $indexCommandExtensions;
		$this->indexCommandContacts = $indexCommandContacts;
	}
	
	public function rebuild($index){
		$output = array();
		$res = 0;
		if($index == "extensions")
			exec($this->indexCommandExtensions,$output, $res);
		elseif($index == "contacts")
			exec($this->indexCommandContacts,$output, $res);
		else 
			throw new \Exception("Index must be specified");
		
		if($res != 0){
			exec('whoami',$output);
			throw new \Exception("Index cannot be rebuild. Please be sure that the command provided '".$this->indexCommand."' is right and the user '".$output[0]."' has permissions");
		}
	}
	
	public function __toString()
	{
		return 'Wixet IndexManager Service';
	}
}
