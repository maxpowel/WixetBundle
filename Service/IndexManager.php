<?php

namespace Wixet\WixetBundle\Service;

class IndexManager
{
	
	public function __construct($indexCommand)
	{
		$this->indexCommand = $indexCommand;
	}
	
	public function rebuild(){
		$output = array();
		$res = 0;
		exec($this->indexCommand,$output, $res);
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
