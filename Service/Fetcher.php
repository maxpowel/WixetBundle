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
	
	
	
	public function getPrevious($item, $ic, $profile){

		$scapedObjectType = str_replace('\\', '\\\\', $this->doctrine->getClassMetadata(get_class($ic))->name);
		
		$sql = "SELECT max(object_id) FROM final_permission JOIN object_type on (object_type.id = final_permission.object_type_id) WHERE object_type.name != '".$scapedObjectType."' AND  profile_id = ".$profile->getId()." AND read_granted = 1 AND read_denied = 0 AND item_container_id = ". $ic->getId(). " AND object_id < ". $item->getId();
		$query = $this->dbal->query($sql);
		$res = $query->fetch();
		$prev = $res[0];
		if($prev == null)
			$prev = $this->getLast($ic, $profile);
		
		return $prev;
	}
	
	public function getNext($item, $ic, $profile){
	
		$scapedObjectType = str_replace('\\', '\\\\', $this->doctrine->getClassMetadata(get_class($ic))->name);
	
		$sql = "SELECT min(object_id) FROM final_permission JOIN object_type on (object_type.id = final_permission.object_type_id) WHERE object_type.name != '".$scapedObjectType."' AND  profile_id = ".$profile->getId()." AND read_granted = 1 AND read_denied = 0 AND item_container_id = ". $ic->getId(). " AND object_id > ". $item->getId();
		$query = $this->dbal->query($sql);
		$res = $query->fetch();
		$prev = $res[0];
		if($prev == null)
			$prev = $this->getFirst($ic, $profile);

		return $prev;
	}
	
	public function getLast($ic, $profile){
		$scapedObjectType = str_replace('\\', '\\\\', $this->doctrine->getClassMetadata(get_class($ic))->name);
		$sql = "SELECT max(object_id) FROM final_permission JOIN object_type on (object_type.id = final_permission.object_type_id) WHERE object_type.name != '".$scapedObjectType."' AND  profile_id = ".$profile->getId()." AND read_granted = 1 AND read_denied = 0 AND item_container_id = ". $ic->getId();
		$query = $this->dbal->query($sql);
		$res = $query->fetch();
		return $res[0];
	}
	
	public function getFirst($ic, $profile){
		$scapedObjectType = str_replace('\\', '\\\\', $this->doctrine->getClassMetadata(get_class($ic))->name);
		$sql = "SELECT min(object_id) FROM final_permission JOIN object_type on (object_type.id = final_permission.object_type_id) WHERE object_type.name != '".$scapedObjectType."' AND  profile_id = ".$profile->getId()." AND read_granted = 1 AND read_denied = 0 AND item_container_id = ". $ic->getId();
		$query = $this->dbal->query($sql);
		$res = $query->fetch();
		
		return $res[0];
	}
	
	public function getItemContainer($item){
		$ot = $this->doctrine->getRepository( 'Wixet\WixetBundle\Entity\ObjectType' )->findOneBy( array( 'name' => get_class($item)));
		 
		 
		$query = $this->doctrine->createQuery('SELECT h FROM Wixet\WixetBundle\Entity\ItemContainerHasItems h JOIN h.itemContainer ic WHERE h.object_id = ?1 AND h.objectType= ?2');
		$query->setParameter(1,$item->getId());
		$query->setParameter(2,$ot);
		return $query->getSingleResult()->getItemContainer();
	
		 
	}
	
	public function get($objectType,$objectId,$profile){
		    $scapedObjectType = str_replace('\\', '\\\\', $objectType);
            $sql="select sum(read_granted) as read_granted, sum(read_denied) as read_denied
                  FROM final_permission
                  where profile_id = ".$profile->getId()." AND object_id = ".$objectId." AND object_type_id = (select id from object_type where name = '".$scapedObjectType."')";//Or use a join instead subselect
                    
              $stmt = $this->dbal->query($sql);
              $row = $stmt->fetch();
              if($row['read_granted'] > 0 && $row['read_denied'] == 0){
              	return $this->doctrine->find($objectType, $objectId);
              }/*else{
              	//Maybe the user is the owner?
              	$item = $this->doctrine->find($objectType, $objectId);
              	if($item->getProfile()->getId() == $profile->getId())
              		return $item;
              }*/
        }
        
        
        /* Get if not denied but granted is not required */
        public function getPassive($objectType,$objectId,$profile){
        	$scapedObjectType = str_replace('\\', '\\\\', $objectType);
        	$sql="select sum(read_granted) as read_granted, sum(read_denied) as read_denied
                          FROM final_permission
                          where profile_id = ".$profile->getId()." AND object_id = ".$objectId." AND object_type_id = (select id from object_type where name = '".$scapedObjectType."')";//Or use a join instead subselect
        
        	$stmt = $this->dbal->query($sql);
        	$row = $stmt->fetch();
        	if($row['read_denied'] == 0){
        		return $this->doctrine->find($objectType, $objectId);
        	}
        }
        
        public function getWritable($objectType,$objectId,$profile){
        	$scapedObjectType = str_replace('\\', '\\\\', $objectType);
        	$sql="select sum(write_granted) as write_granted, sum(write_denied) as write_denied
                                  FROM final_permission
                                  where profile_id = ".$profile->getId()." AND object_id = ".$objectId." AND object_type_id = (select id from object_type where name = '".$scapedObjectType."')";//Or use a join instead subselect
        
        	$stmt = $this->dbal->query($sql);
        	$row = $stmt->fetch();
        	if($row['write_granted'] > 0 && $row['write_denied'] == 0){
        		return $this->doctrine->find($objectType, $objectId);
        	}
        }
        
	public function unsecureGet($objectType,$objectId){
            return $this->doctrine->find($objectType, $objectId);
        }
	
	public function getCollection($itemContainer,$profile,$objectType = null){
            
            return new ItemCollection($this->doctrine, $this->dbal, $profile, $itemContainer, $objectType);
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
