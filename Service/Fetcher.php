<?php

namespace Wixet\WixetBundle\Service;

use Wixet\WixetBundle\Util\ItemCollection;

class Fetcher
{
	private $doctrine;
        private $dbal;
        
	public function __construct($em,$config,$dbal)
	{
		$this->doctrine = $em;
                $this->dbal = $dbal;
	}
	
	public function get($objectType,$objectId,$profile){
		    $scapedObjectType = str_replace('\\', '\\\\', $objectType);
            $sql="select sum(read_granted) as read_granted, sum(read_denied) as read_denied
                  FROM final_permission
                  where profile_id = ".$profile->getId()." AND real_item_id = ".$objectId." AND object_type_id = (select id from object_type where name = '".$scapedObjectType."')";//Or use a join instead subselect
                    
              $stmt = $this->dbal->query($sql);
              $row = $stmt->fetch();
              if($row['read_granted'] > 0 && $row['read_denied'] == 0){
              	
              	return $this->doctrine->find($objectType, $objectId);
              }
        }
        
	public function unsecureGet($objectType,$objectId){
            return $this->doctrine->find($objectType, $objectId);
        }
	
	public function getCollection($album,$profile,$objectType = null){
            
            return new ItemCollection($this->doctrine, $this->dbal, $profile, $album, $objectType);
            /*
            $sql="select sum(read_granted) as read_granted, sum(read_denied) as read_denied
                  FROM final_permission
                  where profile_id = ".$profile->getId()." AND ream_item_id = ".$objectId." AND object_type_id = (select id from object_type where name = ".$objectType.")";//Or use a join instead subselect
                    
             $stmt = $this->dbal->query($sql);
             $row = $stmt->fetch();
             if($row['read_granted'] > 0 && $row['read_denied'] == 0){
                 return $this->doctrine->find($objectType, $objectId);
             }*/
        }
        
	public function unsecureGetCollection(){
            
        }
	
	public function searchUserProfile(){}
	public function searchGroupProfile(){}
	
	
}
