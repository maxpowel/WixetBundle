<?php

namespace Wixet\WixetBundle\Service;

use Wixet\WixetBundle\Util\ItemCollection;
use Wixet\WixetBundle\Entity\ItemContainerCacheInfo;

class Fetcher
{
	private $doctrine;
	private $dbal;
	private $itemContainerClass;
	private $itemContainerTypeId;

	public function __construct($em,$config,$dbal)
	{
		$this->doctrine = $em;
		$this->dbal = $dbal;
		//$this->groupClass = $config['classes']['group'];
		//$this->profileClass = $config['classes']['profile'];
		$this->itemContainerClass = $config['classes']['itemContainer'];

		if($this->itemContainerTypeId == null){
			$ot = $this->doctrine->getRepository( 'Wixet\WixetBundle\Entity\ObjectType' )->findOneBy( array( 'name' => $this->itemContainerClass ));
			if($ot == null){
				$ot = new \Wixet\WixetBundle\Entity\ObjectType();
				$ot->setName($this->itemContainerClass);
				$em->persist($ot);
				$em->flush();
			}

			$this->itemContainerTypeId = $ot->getId();
		}

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
		$className = $this->doctrine->getClassMetadata(get_class($item))->name;
		$ot = $this->doctrine->getRepository( 'Wixet\WixetBundle\Entity\ObjectType' )->findOneBy( array( 'name' => $className));
			
			
		$query = $this->doctrine->createQuery('SELECT h FROM Wixet\WixetBundle\Entity\ItemContainerHasItems h JOIN h.itemContainer ic WHERE h.object_id = ?1 AND h.objectType= ?2');
		$query->setParameter(1,$item->getId());
		$query->setParameter(2,$ot);
		return $query->getSingleResult()->getItemContainer();

			
	}

	/* $writable flag when want fetch only if writable */
	public function get($objectType,$objectId,$profile, $writable = false, $count = 0){
		if($count >= 2){
			//throw new \Exception("Loop detected");
			return null;
		}

		$query = $this->doctrine->createQuery('SELECT p FROM Wixet\WixetBundle\Entity\FinalPermission p JOIN p.objectType ot WHERE p.profile = ?1 AND p.object_id = ?2 AND ot.name = ?3');
		$query->setParameter(1,$profile);
		$query->setParameter(2,$objectId);
		$query->setParameter(3,$objectType);

		try{
			$res = $query->getSingleResult();
				
			if($writable){
				if($res->getWriteGranted() > 0 && $res->getWriteDenied() == 0){
					return $this->doctrine->find($objectType, $objectId);
				}
			}else if($res->getReadGranted() > 0 && $res->getReadDenied() == 0){
				return $this->doctrine->find($objectType, $objectId);
			}
		}catch (\Doctrine\ORM\NoResultException $e){
			//Cache fail, create it
			//echo "FALLO CACHE: objecto ".$objectId."\n";
			$this->buildItemProfileCachePermissions($objectType, $objectId, $profile);
			$count = $count + 1;
			return $this->get($objectType, $objectId, $profile, $writable, $count);
		}

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

	public function getCollection($itemContainer,$profile,$objectType = null, $count = 0){
		if($count >= 2){
			throw new \Exception("Loop detected");
		}
		//Check is permissions of the collection is catched
		$query = $this->doctrine->createQuery('SELECT p FROM Wixet\WixetBundle\Entity\ItemContainerCacheInfo p WHERE p.itemContainer = ?1 AND p.profile = ?2');
		$query->setParameter(1,$itemContainer);
		$query->setParameter(2,$profile);
		try{
			$query->getSingleResult();
			return new ItemCollection($this->doctrine, $this->dbal, $profile, $itemContainer, $objectType);
		}catch(\Doctrine\ORM\NoResultException $e){
			//Build permissions for all items of the collection to the profile
			$count = $count + 1;
			$this->buildItemCollectionProfileCachePermissions($itemContainer, $profile);
			return $this->getCollection($itemContainer, $profile, $objectType, $count);

		}

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

	public function searchUserProfile(){
	}
	public function searchGroupProfile(){
	}




	/////////////************** PRIVATE ****************//////////////
	/* Rebuild final permissions for profile/item */
	private function buildItemProfileCachePermissions($objectType, $objectId, $profile){
		//echo "Construyendo para objeto ".$objectId."\n";
		//echo "\nINI: ".$objectType." ".$objectId." ".$profile->getId()."\n";

		//$this->itemContainerTypeId = $this->em





		$query = $this->doctrine->createQuery('SELECT ot FROM Wixet\WixetBundle\Entity\ObjectType ot WHERE ot.name = ?1');
		$query->setParameter(1,$objectType);
		$ot = $query->getSingleResult();
			
		//Remove final permissions for this profile/item
		$query = $this->doctrine->createQuery('DELETE FROM Wixet\WixetBundle\Entity\FinalPermission p WHERE p.profile = ?1 AND p.objectType = ?2 and p.object_id = ?3');
		$query->setParameter(1,$profile);
		$query->setParameter(2,$ot);
		$query->setParameter(3,$objectId);
		$query->execute();

		//Build final permissions

		$profileId = $profile->getId();
		/* Get ownerId */
		$query = $this->doctrine->createQuery('SELECT o FROM '.$objectType.' o WHERE o.id = ?1');
		$query->setParameter(1,$objectId);
		$item = $query->getSingleResult();
		$ownerId = $item->getProfile()->getId();

		//Container id
		$query = $this->doctrine->createQuery('SELECT ot FROM Wixet\WixetBundle\Entity\ItemContainerHasItems ot WHERE ot.objectType = ?1 AND ot.object_id = ?2');
		$query->setParameter(1,$ot);
		$query->setParameter(2,$objectId);
		$itemContainerInfo = $query->getSingleResult();
		$itemContainerId = $itemContainerInfo->getItemContainer()->getId();

		//Permissions profile/item
		$sql = "
		    	
		    	INSERT INTO final_permission
		    	      (object_creation_time, object_type_id, object_id,                      read_granted,                     read_denied,                       write_granted,                      write_denied,                   profile_id,                owner_id, item_container_id )
		    	
		    	select object_creation_time, object_type_id, object_id, sum(read_granted) as read_granted, sum(read_denied) as read_denied, sum(write_granted) as write_granted, sum(write_denied) as write_denied, ".$profileId." as profile_id, ".$ownerId." as owner_id, ".$itemContainerId." as item_container_id 
		        	
		        	FROM
		    	    	(
		    	    	SELECT up.object_type_id, up.object_id, up.read_granted, up.read_denied, up.write_granted, up.write_denied, up.object_creation_time
		    	    	FROM profile_permission up
		    	    	WHERE up.profile_id = ".$profile->getId()." and up.object_id = ".$item->getId()." 
		    	    	
		    	    	UNION ".

		//Permissions profile/album
		    	    	"SELECT ai.object_type_id, ai.object_id, up.read_granted, up.read_denied, up.write_granted, up.write_denied, up.object_creation_time
		    	    	FROM profile_permission up
		    	    	JOIN item_container_has_items ai on (ai.item_container_id = up.object_id)
		    	    	WHERE up.profile_id = ".$profile->getId()." AND ai.object_id = ".$item->getId()." AND up.object_type_id = ".$this->itemContainerTypeId." 
		    	    	
		    	    	UNION ".

		//Permissions group/item
		    	    	"SELECT gp.object_type_id, gp.object_id, gp.read_granted, gp.read_denied, gp.write_granted, gp.write_denied, gp.object_creation_time
		    	    	FROM group_permission gp
		    	    	JOIN profilegroup_userprofile ghp on (gp.group_id = ghp.profilegroup_id)
		    	    	WHERE ghp.userprofile_id = ".$profile->getId()." AND gp.object_id = ".$item->getId()." 
		    	    	
		    	    	UNION ".
		//Permissions for group/album
		    	    	"SELECT ai.object_type_id, ai.object_id, gp.read_granted, gp.read_denied, gp.write_granted, gp.write_denied, gp.object_creation_time
		    	    	FROM group_permission gp
		    	    	JOIN profilegroup_userprofile ghp on (gp.group_id = ghp.profilegroup_id)
		    	    	JOIN item_container_has_items ai on ( gp.object_id = ai.item_container_id)
		    	    	WHERE ghp.userprofile_id = ".$profile->getId()." AND ai.object_id = ".$item->getId()." AND gp.object_type_id = ".$this->itemContainerTypeId." 
		    	    	) mtable
		    	    
		    	    	group by object_type_id, object_id"; 



		$this->dbal->query($sql);


	}


	/* Rebuild final permissions for profile/mediaItem */
	private function buildItemCollectionProfileCachePermissions($itemContainer, $profile){

		//Remove final permissions for this profile/itemContainer
		$query = $this->doctrine->createQuery('DELETE FROM Wixet\WixetBundle\Entity\FinalPermission p WHERE p.profile = ?1 AND p.itemContainer = ?2');
		$query->setParameter(1,$profile);
		$query->setParameter(2,$itemContainer);
		$query->execute();

		//Build final permissions

		$profileId = $profile->getId();
		/* Get ownerId */
		$ownerId = $itemContainer->getProfile()->getId();

		//Container id
		$itemContainerId = $itemContainer->getId();
		//$item = $itemContainer;


		/* Get all items of the profile */
		$sql = "
		 INSERT INTO final_permission
			    	      (object_creation_time, object_type_id, object_id,                      read_granted,                     read_denied,                       write_granted,                      write_denied,                   profile_id,                owner_id, item_container_id )
			    	select object_creation_time, object_type_id, object_id, sum(read_granted) as read_granted, sum(read_denied) as read_denied, sum(write_granted) as write_granted, sum(write_denied) as write_denied, ".$profileId." as profile_id, ".$ownerId." as owner_id, ".$itemContainerId." as item_container_id 
			        	
			        	FROM
			    	    	(        
		         
		 SELECT ichi.object_type_id, ichi.object_id, pp.read_granted, pp.read_denied, pp.write_granted, pp.write_denied, pp.object_creation_time
		 FROM item_container_has_items ichi ".
		/* Get profile permissions for the items of the album */
        "JOIN profile_permission pp on (ichi.object_id = pp.object_id and ichi.object_type_id = pp.object_type_id)
			WHERE ichi.item_container_id = ".$itemContainerId." AND pp.profile_id = ".$profileId." ".

        "UNION ".
		/* Join with groups and get group permissions of the items of the album */
        "SELECT ichi.object_type_id, ichi.object_id, gp.read_granted, gp.read_denied, gp.write_granted, gp.write_denied, gp.object_creation_time
        FROM item_container_has_items ichi
		JOIN group_permission gp on (ichi.object_id = gp.object_id and ichi.object_type_id = gp.object_type_id)
		JOIN profilegroup_userprofile pup on (gp.group_id = pup.profilegroup_id)
		WHERE ichi.item_container_id = ".$itemContainerId." AND pup.userprofile_id = ".$profileId.

		/* Get items with profile permission inerhited from album */
		" UNION
			SELECT ichi.object_type_id, ichi.object_id, pp.read_granted, pp.read_denied, pp.write_granted, pp.write_denied, pp.object_creation_time
FROM item_container_has_items ichi
JOIN profile_permission pp on (ichi.item_container_id = pp.object_id)
WHERE pp.object_type_id = ".$this->itemContainerTypeId." AND pp.object_id = ".$itemContainerId." AND pp.profile_id = ".$profileId.
		/* Get items with group permission inerhited from album */
 " UNION SELECT ichi.object_type_id, ichi.object_id, gp.read_granted, gp.read_denied, gp.write_granted, gp.write_denied, gp.object_creation_time
        FROM item_container_has_items ichi
JOIN group_permission gp on (ichi.item_container_id = gp.object_id)
JOIN profilegroup_userprofile pup on (gp.group_id = pup.profilegroup_id)
WHERE gp.object_type_id = ".$this->itemContainerTypeId." AND gp.object_id = ".$itemContainerId." AND pup.userprofile_id = ".$profileId."
		
) mtable
			    	    
			    	    	group by object_type_id, object_id		";


		$this->dbal->query($sql);


		//Set as catched
		$catched = new ItemContainerCacheInfo();
		$catched->setItemContainer($itemContainer);
		$catched->setProfile($profile);
		$this->doctrine->persist($catched);
		$this->doctrine->flush();

	}


}
