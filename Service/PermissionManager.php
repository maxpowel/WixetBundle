<?php

namespace Wixet\WixetBundle\Service;

use Wixet\WixetBundle\Entity\GroupPermission;
use Wixet\WixetBundle\Entity\ProfilePermission;
use Wixet\WixetBundle\Entity\ObjectType;

class PermissionManager
{
    	
	private $groupClass;
	private $profileClass;
	private $itemContainerClass;
	private $doctrine;
	private $useCache;
	private $doctrineCache;
	private $dbal;
	private $itemContainerTypeId;
	
	public function __construct($em,$config,$dbal)
	{
		$this->dbal = $dbal;
		$this->doctrine = $em;
		$this->groupClass = $config['classes']['group'];
		$this->profileClass = $config['classes']['profile'];
		$this->itemContainerClass = $config['classes']['itemContainer'];
		
		//$this->itemContainerTypeId = $this->em
		$ot = $em->getRepository( 'Wixet\WixetBundle\Entity\ObjectType' )->findOneBy( array( 'name' => $this->itemContainerClass ));
		if($ot == null){
			$ot = new \Wixet\WixetBundle\Entity\ObjectType();
			$ot->setName($this->itemContainerClass);
			$em->persist($ot);
			$em->flush();
		}
		
		$this->itemContainerTypeId = $ot->getId();
	
		//TODO
		/*if(isset($config['cache'])){
		$this->useCache = true;
		//foreach($config['cache']['connection'] as $connection){
		//	$this->useCache->addServer($connection['host'], $connection['port']);
		//}
		$this->useCacheTime = $config['cache']['expirationTime'];
		$this->doctrineCache = new \Doctrine\Common\Cache\ApcCache();
		//$this->doctrineCache->setManageCacheIds(true);
		//
		$config = new \Doctrine\ORM\Configuration();
		$config->setQueryCacheImpl($this->doctrineCache);
		$config->setResultCacheImpl($this->doctrineCache);
		$config->setMetadataCacheImpl($this->doctrineCache);
			
		//$query->useResultCache(true);
		//$query->setResultCacheLifetime(3600);
			
	
		}else $this->useCache == null;*/
		}
		
    public function addProfileToGroup($profile, $group){
    	$group->addProfile($profile);
    	$this->doctrine->flush();
    	$this->rebuildFinalPermissionsToProfile($profile, $group->getProfile());
    }
    
    public function removeProfileFromGroup($profile, $group){
		$group->removeProfile($profile);
    	$this->doctrine->flush();
    	$this->rebuildFinalPermissionsToProfile($profile, $group->getProfile());
    }
    
    public function setItemContainer($item, $itemContainer){
    	
    	$ot = $this->doctrine->getRepository( 'Wixet\WixetBundle\Entity\ObjectType' )->findOneBy( array( 'name' => get_class($item)));
    	if($ot == null){
    		$ot = new \Wixet\WixetBundle\Entity\ObjectType();
    		$ot->setName(get_class($item));
    		$this->doctrine->persist($ot);
    		$this->doctrine->flush();
    	}
    	
    	$icont = $this->doctrine->getRepository( 'Wixet\WixetBundle\Entity\ItemContainerHasItems' )->findOneBy( array( 'objectType' => $ot->getName(), 'object_id' => $item->getId()));
    	if($icont == null){
    		//No album assigned
    		
    		$icont = new \Wixet\WixetBundle\Entity\ItemContainerHasItems();
    		$icont->setObjectType($ot);
    		$icont->setObjectId($item->getId());
    		$icont->setItemContainer($itemContainer);
    		
    		$this->doctrine->persist($icont);
    		$this->doctrine->flush();
    	}else{
    		$icont->setItemContainer($itemContainer);
    		$this->doctrine->flush();
    		
    	}
    	
    	
    	$this->rebuildFinalPermissionsToItem($item);
    	
    }
    
    public function setPermissionProfileItem($profile, $item, $permission){
    	
    	
    	$this->setProfilePermission($profile, $item, $permission);
		$this->rebuildFinalPermissionsToProfileItem($profile, $item);
    }
    
    
    public function setPermissionProfileItemContainer($profile, $itemContainer, $permission){
    	$this->setProfilePermission($profile, $itemContainer, $permission);
    	
    	//TODO use sql instead a foreach
    	foreach($itemContainer->getItems() as $item){
    		$this->rebuildFinalPermissionsToProfileItem($profile, $item);
    	}
    }
    
    public function setPermissionGroupItem($group, $item, $permission){
    	$this->setGroupPermission($group, $item, $permission);
    	//TODO use sql instead a foreach
    	foreach($group->getProfiles() as $profile){
    		$this->rebuildFinalPermissionsToProfileItem($profile, $item);
    	} 
    }
    
    public function setPermissionGroupItemContainer($group, $itemContainer, $permission){
    	
    	$this->setGroupPermission($group, $itemContainer, $permission);
    	
    	$profiles = $group->getProfiles();
    	
    	//TODO use sql instead a foreach
    	foreach($itemContainer->getItems() as $item){
    		foreach($profiles as $profile){
    			$this->rebuildFinalPermissionsToProfileItem($profile, $item);
    		}
    	}
    }
    
    private function rebuildFinalPermissionsToProfile($profile,  $owner){
    	//Remove all final permissions
    	$sql = "DELETE FROM final_permission WHERE profile_id = ".$profile->getId()." AND owner_id = ". $owner->getId();
    	$this->dbal->query($sql);
    	
    	//Build final permissions
    	
    	//Permissions user/item
    	$sql = "select object_creation_time, object_type_id, object_id, sum(read_granted) as read_granted, sum(read_denied) as read_denied, sum(write_granted) as write_granted, sum(write_denied) as write_denied
    	
    	FROM
	    	(
	    	SELECT up.object_type_id, up.object_id, up.read_granted, up.read_denied, up.write_granted, up.write_denied, up.object_creation_time
	    	FROM profile_permission up
	    	WHERE up.profile_id = ".$profile->getId()." and up.owner_id = ".$owner->getId()." and up.object_type_id != ".$this->itemContainerTypeId."
	    	
	    	UNION ".
	    	
	    	//Permissions user/album
	    	"SELECT ai.object_type_id, ai.object_id, up.read_granted, up.read_denied, up.write_granted, up.write_denied, up.object_creation_time
	    	FROM profile_permission up
	    	JOIN item_container_has_items ai on (ai.item_container_id = up.object_id)
	    	WHERE up.profile_id = ".$profile->getId()." AND up.owner_id = ".$owner->getId()." AND up.object_type_id = ".$this->itemContainerTypeId."
	    	
	    	UNION ".
	    	
			//Permissions group/item
	    	"SELECT gp.object_type_id, gp.object_id, gp.read_granted, gp.read_denied, gp.write_granted, gp.write_denied, gp.object_creation_time
	    	FROM group_permission gp
	    	JOIN profilegroup_userprofile ghp on (gp.group_id = ghp.profilegroup_id)
	    	WHERE ghp.userprofile_id = ".$profile->getId()." AND gp.owner_id = ".$owner->getId()." AND object_type_id != ".$this->itemContainerTypeId." 
	    	
	    	UNION ".
	    	//Permissions for group/album
	    	"SELECT ai.object_type_id, ai.object_id, gp.read_granted, gp.read_denied, gp.write_granted, gp.write_denied, gp.object_creation_time
	    	FROM group_permission gp
	    	JOIN profilegroup_userprofile ghp on (gp.group_id = ghp.profilegroup_id)
	    	JOIN item_container_has_items ai on ( gp.object_id = ai.item_container_id)
	    	WHERE ghp.userprofile_id = ".$profile->getId()." AND gp.owner_id = ".$owner->getId()." AND gp.object_type_id = ".$this->itemContainerTypeId." 
	    	) mtable
	    
	    	group by object_type_id, object_id, object_creation_time"; 
    	
    	$statement = $this->dbal->query($sql);
    	
    	
    	$profileId = $profile->getId();
    	$ownerId = $owner->getId();
    	while( ($finalPermission = $statement->fetch())){
    		$sql = "SELECT item_container_id FROM item_container_has_items WHERE object_type_id = ".$finalPermission['object_type_id']." AND object_id =".$finalPermission['object_id'];
    		//$finalPermission['write_denied'].",".$finalPermission['read_granted']." ,".$finalPermission['read_denied'].")";
    		$st = $this->dbal->query($sql);
    		$itemContainerInfo = $st->fetch();
    		
    		
    		$itemContainerId = $itemContainerInfo['item_container_id'];
    		$sql = "INSERT INTO final_permission (profile_id, object_type_id, object_id, owner_id, item_container_id, object_creation_time, write_granted, write_denied, read_granted, read_denied)".
    		       "values (".$profileId.", ".$finalPermission['object_type_id'].", ".$finalPermission['object_id'].", ".$ownerId." , $itemContainerId, '".$finalPermission['object_creation_time']."', ".$finalPermission['write_granted'].",".
    		       $finalPermission['write_denied'].",".$finalPermission['read_granted']." ,".$finalPermission['read_denied'].")";

    		$this->dbal->query($sql);
    	}
    	
    	
    }
    
    private function rebuildFinalPermissionsToItem($item){

    	//Get object type id
    	$sql = "SELECT id FROM object_type WHERE name = '".str_replace("\\", "\\\\", get_class($item))."'";
    	$st = $this->dbal->query($sql);
    	$objectTypeId = $st->fetch();
    	$objectTypeId = $objectTypeId['id'];
    	
    	//Remove all final permissions
    	$sql = "DELETE FROM final_permission WHERE object_type_id = ".$objectTypeId." AND object_id = ". $item->getId();
		$this->dbal->query($sql);
    	 
    	//Build final permissions
    	 
    	$sql = "select profile_id, owner_id, object_creation_time, object_type_id, object_id, sum(read_granted) as read_granted, sum(read_denied) as read_denied, sum(write_granted) as write_granted, sum(write_denied) as write_denied
    	    	
    	    	FROM
    		    	(
    		    	SELECT up.object_type_id, up.object_id, up.read_granted, up.read_denied, up.write_granted, up.write_denied, up.object_creation_time, up.profile_id, up.owner_id
    		    	FROM profile_permission up
    		    	WHERE up.object_id = ".$item->getId()." and up.object_type_id = ".$objectTypeId."
    		    	
    		    	UNION ".
    	
    	//Permissions user/album
    		    	"SELECT ai.object_type_id, ai.object_id, up.read_granted, up.read_denied, up.write_granted, up.write_denied, up.object_creation_time, up.profile_id, up.owner_id
    		    	FROM profile_permission up
    		    	JOIN item_container_has_items ai on (ai.item_container_id = up.object_id)
    		    	WHERE ai.object_type_id = ".$objectTypeId." AND ai.object_id = ".$item->getId()." AND up.object_type_id = ".$this->itemContainerTypeId."
    		    	
    		    	UNION ".
    	
    	//Permissions group/item
    		    	"SELECT gp.object_type_id, gp.object_id, gp.read_granted, gp.read_denied, gp.write_granted, gp.write_denied, gp.object_creation_time, ghp.userprofile_id as profile_id, gp.owner_id
    		    	FROM group_permission gp
    		    	JOIN profilegroup_userprofile ghp on (gp.group_id = ghp.profilegroup_id)
    		    	WHERE gp.object_type_id = ".$objectTypeId." AND gp.object_id = ".$item->getId()."
    		    	
    		    	UNION ".
    	//Permissions for group/album
    		    	"SELECT ai.object_type_id, ai.object_id, gp.read_granted, gp.read_denied, gp.write_granted, gp.write_denied, gp.object_creation_time, ghp.userprofile_id as profile_id, gp.owner_id
    		    	FROM group_permission gp
    		    	JOIN profilegroup_userprofile ghp on (gp.group_id = ghp.profilegroup_id)
    		    	JOIN item_container_has_items ai on ( gp.object_id = ai.item_container_id)
    		    	WHERE ai.object_type_id = ".$objectTypeId." AND ai.object_id = ".$item->getId()." AND gp.object_type_id = ".$this->itemContainerTypeId." 
    		    	) mtable
    		    
    		    	group by object_type_id, object_id, object_creation_time, profile_id, owner_id"; 
    	 
    	$statement = $this->dbal->query($sql);
    	 
    	 
    	while( ($finalPermission = $statement->fetch())){
    		$sql = "SELECT item_container_id FROM item_container_has_items WHERE object_type_id = ".$finalPermission['object_type_id']." AND object_id =".$finalPermission['object_id'];
    		//$finalPermission['write_denied'].",".$finalPermission['read_granted']." ,".$finalPermission['read_denied'].")";
    		$st = $this->dbal->query($sql);
    		$itemContainerInfo = $st->fetch();
    	
    	
    		$itemContainerId = $itemContainerInfo['item_container_id'];
    		$sql = "INSERT INTO final_permission (profile_id, object_type_id, object_id, owner_id, item_container_id, object_creation_time, write_granted, write_denied, read_granted, read_denied)".
    	    		       "values (".$finalPermission['profile_id'].", ".$finalPermission['object_type_id'].", ".$finalPermission['object_id'].", ".$finalPermission['owner_id']." , $itemContainerId, '".$finalPermission['object_creation_time']."', ".$finalPermission['write_granted'].",".
    		$finalPermission['write_denied'].",".$finalPermission['read_granted']." ,".$finalPermission['read_denied'].")";
    	
    		$this->dbal->query($sql);
    	}
    }
    
    private function rebuildFinalPermissionsToProfileItem($profile, $item){
    	//Remove final permissions
    	
    	//Get object type id
    	$sql = "SELECT id FROM object_type WHERE name = '".str_replace("\\", "\\\\", get_class($item))."'";
    	$st = $this->dbal->query($sql);
    	$objectTypeId = $st->fetch();
    	$sql = "DELETE FROM final_permission WHERE profile_id = ".$profile->getId()." AND object_type_id = ". $objectTypeId['id'] ." AND object_id = ".$item->getId();

    	$this->dbal->query($sql);
    	
    	//Build final permissions
    	
    	
    	
    	//Permissions profile/item
    	$sql = "select object_creation_time, object_type_id, object_id, sum(read_granted) as read_granted, sum(read_denied) as read_denied, sum(write_granted) as write_granted, sum(write_denied) as write_denied
    	
    	FROM
	    	(
	    	SELECT up.object_type_id, up.object_id, up.read_granted, up.read_denied, up.write_granted, up.write_denied, up.object_creation_time
	    	FROM profile_permission up
	    	WHERE up.profile_id = ".$profile->getId()." and up.object_id = ".$item->getId()." 
	    	
	    	UNION ".
	    	
	    	//Permissions user/album
	    	"SELECT ai.object_type_id, ai.object_id, up.read_granted, up.read_denied, up.write_granted, up.write_denied, up.object_creation_time
	    	FROM profile_permission up
	    	JOIN item_container_has_items ai on (ai.item_container_id = up.object_id)
	    	WHERE up.profile_id = ".$profile->getId()." AND up.object_id = ".$item->getId()." AND up.object_type_id = ".$this->itemContainerTypeId."
	    	
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
	    	WHERE ghp.userprofile_id = ".$profile->getId()." AND gp.object_id = ".$item->getId()." AND gp.object_type_id = ".$this->itemContainerTypeId." 
	    	) mtable
	    
	    	group by object_type_id, object_id"; 
    	
    	$statement = $this->dbal->query($sql);
    	
    	
    	$profileId = $profile->getId();
    	$ownerId = $item->getProfile()->getId();
    	
    	//Container id
    	$sql = "SELECT item_container_id FROM item_container_has_items WHERE object_type_id = ".$objectTypeId['id']." AND object_id =".$item->getId();
    	//$finalPermission['write_denied'].",".$finalPermission['read_granted']." ,".$finalPermission['read_denied'].")";
    	$st = $this->dbal->query($sql);
    	$itemContainerInfo = $st->fetch();
    	
    	
    	$itemContainerId = $itemContainerInfo['item_container_id'];
    	
    	while( ($finalPermission = $statement->fetch())){
    		$sql = "INSERT INTO final_permission (profile_id, object_type_id, object_id, owner_id, item_container_id, object_creation_time, write_granted, write_denied, read_granted, read_denied)".
    		       "values (".$profileId.", ".$finalPermission['object_type_id'].", ".$finalPermission['object_id'].", ".$ownerId." , $itemContainerId, '".$finalPermission['object_creation_time']."', ".$finalPermission['write_granted'].",".
    		       $finalPermission['write_denied'].",".$finalPermission['read_granted']." ,".$finalPermission['read_denied'].")";

    		$this->dbal->query($sql);
    	}
    	
    	
    }
    
    private function setProfilePermission($profile, $item, $permission){
    	//Check if permssion exists. Insert or update or remove
    	$ot = $this->doctrine->getRepository( 'Wixet\WixetBundle\Entity\ObjectType' )->findOneBy( array( 'name' => get_class($item)));
    	if($ot == null){
    		$ot = new \Wixet\WixetBundle\Entity\ObjectType();
    		$ot->setName(get_class($item));
    		$this->doctrine->persist($ot);
    		$this->doctrine->flush();
    	}
    
    	$query = $this->doctrine->createQuery('SELECT p FROM Wixet\WixetBundle\Entity\ProfilePermission p WHERE p.object_id = ?1 AND p.objectType = ?2 AND p.profile = ?3');
    	$query->setParameter(1, $item->getId());
    	$query->setParameter(2, $ot);
    	$query->setParameter(3, $profile);
    	$profilePermission = null;
    	try{
    		$profilePermission = $query->getSingleResult();
    		$profilePermission->setReadGranted($permission['readGranted']);
    		$profilePermission->setReadDenied($permission['readDenied']);
    		$profilePermission->setWriteGranted($permission['writeGranted']);
    		$profilePermission->setWriteDenied($permission['writeDenied']);
    		$this->doctrine->flush();
    	}catch(\Doctrine\ORM\NoResultException $e){
    		//Permission does not exists for this item/user
    		$profilePermission = new \Wixet\WixetBundle\Entity\ProfilePermission();
    		$profilePermission->setProfile($profile);
    		$profilePermission->setOwner($item->getProfile());
    		$profilePermission->setObjectType($ot);
    		$profilePermission->setObjectId($item->getId());

    		$profilePermission->setReadGranted($permission['readGranted']);
    		$profilePermission->setWriteGranted($permission['writeGranted']);
    		$profilePermission->setReadDenied($permission['readDenied']);
    		$profilePermission->setWriteDenied($permission['writeDenied']);
    		 
    		$profilePermission->setObjectCreationTime($item->getCreated());
    		 
    		$this->doctrine->persist($profilePermission);
    		$this->doctrine->flush();
    	}
    }
    
    private function setGroupPermission($group, $item, $permission){
    	//Check if permssion exists. Insert or update or remove
    	$ot = $this->doctrine->getRepository( 'Wixet\WixetBundle\Entity\ObjectType' )->findOneBy( array( 'name' => get_class($item)));
    	if($ot == null){
    		$ot = new \Wixet\WixetBundle\Entity\ObjectType();
    		$ot->setName(get_class($item));
    		$this->doctrine->persist($ot);
    		$this->doctrine->flush();
    	}
    
    	$query = $this->doctrine->createQuery('SELECT p FROM Wixet\WixetBundle\Entity\GroupPermission p WHERE p.object_id = ?1 AND p.objectType = ?2 AND p.group = ?3');
    	$query->setParameter(1, $item->getId());
    	$query->setParameter(2, $ot);
    	$query->setParameter(3, $group);
    	$groupPermission = null;
    	try{
    		$groupPermission = $query->getSingleResult();
    		$groupPermission->setReadGranted($permission['readGranted']);
    		$groupPermission->setReadDenied($permission['readDenied']);
    		$groupPermission->setWriteGranted($permission['writeGranted']);
    		$groupPermission->setWriteDenied($permission['writeDenied']);
    		$this->doctrine->flush();
    	}catch(\Doctrine\ORM\NoResultException $e){
    		//Permission does not exists for this item/user
    		$groupPermission = new \Wixet\WixetBundle\Entity\GroupPermission();
    		$groupPermission->setProfile($group);
    		$groupPermission->setOwner($item->getProfile());
    		$groupPermission->setObjectType($ot);
    		$groupPermission->setObjectId($item->getId());
    		 
    		$groupPermission->setReadGranted(1);
    		$groupPermission->setWriteGranted(1);
    		$groupPermission->setReadDenied(1);
    		$groupPermission->setWriteDenied(1);
    		 
    		$groupPermission->setObjectCreationTime($item->getCreated());
    		 
    		$this->doctrine->persist($groupPermission);
    		$this->doctrine->flush();
    	}
    }
    
    public function removePermission($permission){
    	
    	
    	//Get original item
    	$query = $this->doctrine->createQuery('SELECT p FROM '. $permission->getObjectType()->getName() .' p WHERE p.id = ?1');
    	$query->setParameter(1,$permission->getObjectId());
    	$item = $query->getSingleResult();
    	
    	//Remove permission
    	$this->doctrine->remove($permission);
    	$this->doctrine->flush();
    	
    	if($permission instanceof \Wixet\WixetBundle\Entity\ProfilePermission && $item instanceof \Wixet\WixetBundle\Entity\ItemCollection){
    		//Removing profile/album
    		//TODO use sql instead a foreach
    		foreach($itemContainer->getItems() as $item){
    			$this->rebuildFinalPermissionsToProfileItem($permission->getProfile(), $item);
    		}
    	}elseif($permission instanceof \Wixet\WixetBundle\Entity\ProfilePermission && !($item instanceof \Wixet\WixetBundle\Entity\ItemCollection)){
    		//Removing profile/item
    		$this->rebuildFinalPermissionsToProfileItem($permission->getProfile(), $item);
    	}elseif($permission instanceof \Wixet\WixetBundle\Entity\GroupPermission && $item instanceof \Wixet\WixetBundle\Entity\ItemCollection){
    		//Removing group/album
    		$group = $permission->getGroup();
	    	$profiles = $group->getProfiles();
	    	
	    	//TODO use sql instead a foreach
	    	foreach($item->getItems() as $item){
	    		foreach($profiles as $profile){
	    			$this->rebuildFinalPermissionsToProfileItem($profile, $item);
	    		}
	    	}
    	}elseif($permission instanceof \Wixet\WixetBundle\Entity\GroupPermission && !($item instanceof \Wixet\WixetBundle\Entity\ItemCollection)){
  			//Removing group/item
    		//TODO use sql instead a foreach
    		$group = $permission->getGroup();
	    	foreach($group->getProfiles() as $profile){
	    		$this->rebuildFinalPermissionsToProfileItem($profile, $item);
	    	} 
    	}
    	
    }
}
