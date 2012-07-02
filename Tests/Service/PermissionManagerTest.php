<?php

namespace Wixet\WixetBundle\Tests\Service;


use Wixet\WixetBundle\Entity\MediaItem;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class PermissionManagerTest extends WebTestCase
{
	
	
	/**
	* @var \Wixet\WixetBundle\Entity\ItemContainer
	*/
	private static $ic;
	
	/**
	* @var \Wixet\WixetBundle\Entity\UserProfile
	*/
	private static $ownerProfile;
	
	/**
	* @var \Wixet\WixetBundle\Entity\UserProfile
	*/
	private static $viewerProfile;
	
	
	/**
	* @var \Wixet\WixetBundle\Entity\ProfileGroup
	*/
	private static $pg;
	
	/**
	* @var \Wixet\WixetBundle\Entity\MediaItem
	*/
	private static $md;
	
	/**
	* @var \Wixet\WixetBundle\Entity\MediaItem
	*/
	private static $md2;
	
	/**
	* @var \Wixet\WixetBundle\Entity\User
	*/
	private static $owner;
	
	/**
	* @var \Wixet\WixetBundle\Entity\User
	*/
	private static $viewer;
	
	
	/**
	 * @var \Wixet\WixetBundle\Service\PermissionManager
	 */
	private static $pm;
	
	/**
	* @var \Wixet\WixetBundle\Service\Fetcher
	*/
	private static $fetcher;
	
	
	/**
	* @var \Doctrine\ORM\EntityManager
	*/
	private static $em;

	public static function setUpBeforeClass()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		self::$pm = $kernel->getContainer()->get('wixet.permission_manager');
		self::$em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
		self::$fetcher = $kernel->getContainer()->get('wixet.fetcher');
		
		//Create OWNER
			$username   = "owner".uniqid();
    		$email      = $username;
    		$password   = $username;
    		$active   = true;
    		$superadmin = false;
    		 
    		$manipulator = $kernel->getContainer()->get('fos_user.util.user_manipulator');
    		
    		$error = 0;

    			$manipulator->create($username, $password, $email, $active, $superadmin);

    		

	    		//Create main group
	    		$group = new \Wixet\WixetBundle\Entity\ProfileGroup();
	    		$group->setName("Amigos");
	    		//
	    		$album = new \Wixet\WixetBundle\Entity\ItemContainer();
	    		$album->setName("Fotos");
	    		
	    		//
	    		$messageCollection = new \Wixet\WixetBundle\Entity\PrivateMessageCollection();
	    		$messageCollection->setName("Recibidos"); 
	    		
	    		//Create main profile
	    		$em = $kernel->getContainer()->get('doctrine')->getEntityManager();
	    		$user = $em->getRepository('Wixet\WixetBundle\Entity\User')->findOneByEmail($email);
	    		$profile = new \Wixet\WixetBundle\Entity\UserProfile();
	    		$profile->setUser($user);
	    		
	    		
	    		$profile->setFirstName("Owner");
	    		$profile->setLastName("");
	    		$profile->setPublic(false);
	    		
	    		$group->setProfile($profile);
	    		$profile->setMainItemContainer($album);
	    		$profile->setMainGroup($group);
	    		$profile->setMainPrivateMessageCollection($messageCollection);
	    		
	    		$album->setProfile($profile);
	    		$album->setPublic(false);
	    		$messageCollection->setProfile($profile);
	    		

	    		
	    		$em->persist($profile);
	    		$em->persist($album);
	    		$em->persist($group);
	    		$em->persist($messageCollection);
	    		
	    		$em->flush();
	    		
	    		//Add permissions
	    		$ws = $kernel->getContainer()->get('wixet.permission_manager');
	    		$ws->setItemContainer($album,$album);
	    		$ws->setItemContainer($profile,$album);
	    		$ws->setPermissionProfileItem($profile,$album, array("readGranted"=>true, "readDenied"=>false, "writeGranted"=> true, "writeDenied"=> false));
	    		$ws->setPermissionProfileItem($profile,$profile, array("readGranted"=>true, "readDenied"=>false, "writeGranted"=> true, "writeDenied"=> false));
	    		self::$ic = $album;
	    		self::$pg = $group;
	    		self::$owner = $user;
	    		self::$ownerProfile = $profile;
	    		
	    		
	    //Create VIEWER
	    		$username   = "viewer".uniqid();
    			$email      = $username;
    			$password   = $username;
	    		$active   = true;
	    		$superadmin = false;
	    		 
	    		$manipulator = $kernel->getContainer()->get('fos_user.util.user_manipulator');
	    		
	    		$error = 0;
	    		
	    		$manipulator->create($username, $password, $email, $active, $superadmin);
	    		
	    		
	    		
	    		//Create main group
	    		$group = new \Wixet\WixetBundle\Entity\ProfileGroup();
	    		$group->setName("Amigos");
	    		//
	    		$album = new \Wixet\WixetBundle\Entity\ItemContainer();
	    		$album->setName("Fotos");
	    			    		//
	    		$messageCollection = new \Wixet\WixetBundle\Entity\PrivateMessageCollection();
	    		$messageCollection->setName("Recibidos");
	    		 
	    		//Create main profile
	    		$em = $kernel->getContainer()->get('doctrine')->getEntityManager();
	    		$user = $em->getRepository('Wixet\WixetBundle\Entity\User')->findOneByEmail($email);
	    		$profile = new \Wixet\WixetBundle\Entity\UserProfile();
	    		$profile->setUser($user);
	    		 
	    		 
	    		$profile->setFirstName("Viewer");
	    		$profile->setLastName("");
	    		$profile->setPublic(false);
	    		 
	    		$group->setProfile($profile);
	    		$profile->setMainItemContainer($album);
	    		$profile->setMainGroup($group);
	    		$profile->setMainPrivateMessageCollection($messageCollection);
	    		 
	    		$album->setProfile($profile);
	    		$album->setPublic(false);
	    		$messageCollection->setProfile($profile);
	    		 
	    		
	    		 
	    		$em->persist($profile);
	    		$em->persist($album);
	    		$em->persist($group);
	    		$em->persist($messageCollection);
	    		 
	    		$em->flush();
	    		 
	    		//Add permissions
	    		$ws = $kernel->getContainer()->get('wixet.permission_manager');
	    		$ws->setItemContainer($album,$album);
	    		$ws->setItemContainer($profile,$album);
	    		$ws->setPermissionProfileItem($profile,$album, array("readGranted"=>true, "readDenied"=>false, "writeGranted"=> true, "writeDenied"=> false));
	    		$ws->setPermissionProfileItem($profile,$profile, array("readGranted"=>true, "readDenied"=>false, "writeGranted"=> true, "writeDenied"=> false));
	    		
	    		self::$viewer = $user;
	    		self::$viewerProfile = $profile;
	    		
	    		
	    		//Add media item to album
	    		$media = new \Wixet\WixetBundle\Entity\MediaItem();
	    		$media->setProfile(self::$ownerProfile);
	    		$media->setFileSize(0);
	    		$media->setViews(0);
	    		$media->setDisabled(false);
	    		$media->setPublic(false);
	    		$media->setMimeType(self::$em->getRepository("Wixet\WixetBundle\Entity\MimeType")->find(1));
	    		$em->persist($media);
	    		$em->flush();
	    		
	    		self::$pm->setItemContainer($media, self::$ic);
	    		self::$md = $media;
	    		
	    		
	    		//Add the viewer to the group
	    		self::$pg->addProfile(self::$viewerProfile);
	    		self::$em->flush();

	}
	
	public function tearDown(){
		/*self::$em->remove(self::$md);
		self::$em->remove(self::$album);
		
		
		self::$em->remove(self::$ownerProfile);
		self::$em->remove(self::$ownerUser);
		
		self::$em->remove(self::$viewerProfile);
		self::$em->remove(self::$viewerUser);
		self::$em->flush();*/
		
		//TODO remove all permissions (and final)
	}

	public function testSetItemContainer()
	{
		$media = new \Wixet\WixetBundle\Entity\MediaItem();
		$media->setProfile(self::$ownerProfile);
		$media->setFileSize(0);
		$media->setViews(0);
		$media->setDisabled(false);
		$media->setPublic(false);
		$media->setMimeType(self::$em->getRepository("Wixet\WixetBundle\Entity\MimeType")->find(1));
		
		self::$em->persist($media);		
		self::$em->flush();
		
		self::$pm->setItemContainer($media, self::$ic);
		
		$query = self::$em->createQuery('SELECT i,o FROM Wixet\WixetBundle\Entity\ItemContainerHasItems i JOIN i.objectType o WHERE i.itemContainer = ?1');
		$query->setParameter(1,self::$ic);
		
		$results = $query->getArrayResult();
		
		//Profile, main item container and 2 media item
		$this->assertCount(4, $results);
		
		self::$md2 = $media;
	}
	
	public function testProfileItem()
	{
		//Implicit deny
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		//Explicity deny
		$permission = array("readGranted"=>false, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$md, $permission);
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		//Grant
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> true, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$md, $permission);
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		
		$this->assertNotNull($fetched);
		
		$this->assertEquals($fetched->getId(), self::$md->getId());
		
		//Explicity deny after grant
		$permission = array("readGranted"=>false, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$md, $permission);
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		
	}
	
	public function testProfileContainer()
	{
		//Grant read to album
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> true, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		
		
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNotNull($fetched);
		$this->assertEquals($fetched->getId(), self::$md->getId());
		
		//Refetch
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNotNull($fetched);
		$this->assertEquals($fetched->getId(), self::$md->getId());
		
		//Access to profileItem
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\ItemContainer", self::$ic->getId(), self::$viewerProfile);
		$this->assertNotNull($fetched);
		$this->assertEquals($fetched->getId(), self::$ic->getId());
		
		
		//Deny access to album
		$permission = array("readGranted"=>false, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
	
		//Grant access to album but photo denied
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> true, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		$permission = array("readGranted"=>false, "readDenied"=> true, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		//Grant access to photo but album denied
		$permission = array("readGranted"=>false, "readDenied"=> true, "writeGranted"=> true, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$md, $permission);
		
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);

		$this->assertNull($fetched);
		
		//Access to profileItem
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\ItemContainer", self::$ic->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
	
	}
	
	public function testGroupItemContainer()
	{

		//Implicit deny
		$permission = array("readGranted"=>false, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		self::$pm->setPermission(self::$viewerProfile, self::$md, $permission);
		self::$pm->setPermission(self::$pg, self::$ic, $permission);
		
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		//Grant group / itemContainer
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> true, "writeDenied"=> false);
		self::$pm->setPermission(self::$pg, self::$ic, $permission);
		
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNotNull($fetched);
		$this->assertEquals($fetched->getId(), self::$md->getId());
		
		//Deny group / itemContainer
		$permission = array("readGranted"=>false, "readDenied"=> true, "writeGranted"=> false, "writeDenied"=> true);
		self::$pm->setPermission(self::$pg, self::$ic, $permission);
		
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		//Deny group / grant profile - itemContainer
		$permission = array("readGranted"=>false, "readDenied"=> true, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$pg, self::$ic, $permission);
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		//Grant group / deny profile - itemContainer
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$pg, self::$ic, $permission);
		$permission = array("readGranted"=>false, "readDenied"=> true, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		//Grant group / grant profile - itemContainer
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		self::$pm->setPermission(self::$pg, self::$ic, $permission);
		
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNotNull($fetched);
		$this->assertEquals($fetched->getId(), self::$md->getId());
		
		
		
		////////******** over a item *******//////
		//Deny group / grant profile - item
		$permission = array("readGranted"=>false, "readDenied"=> true, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$pg, self::$md, $permission);
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$md, $permission);
		
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		//Grant group / deny profile - item
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$pg, self::$md, $permission);
		$permission = array("readGranted"=>false, "readDenied"=> true, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$md, $permission);
		
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		//Grant group / grant profile - item
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$md, $permission);
		self::$pm->setPermission(self::$pg, self::$md, $permission);
		
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNotNull($fetched);
		$this->assertEquals($fetched->getId(), self::$md->getId());
		
		
		/************* MIX item and itemContainer ***************/
		//Deny group item / grant profile itemContainer
		$permission = array("readGranted"=>false, "readDenied"=> true, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$pg, self::$md, $permission);
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		//Deny group itemContainer / grant profile item
		$permission = array("readGranted"=>false, "readDenied"=> true, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$pg, self::$ic, $permission);
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$md, $permission);
		
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		
		
		//Grant group item / deny profile itemContainer
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$pg, self::$md, $permission);
		$permission = array("readGranted"=>false, "readDenied"=> true, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		//Grant group itemContainer / deny profile item
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$pg, self::$ic, $permission);
		$permission = array("readGranted"=>false, "readDenied"=> true, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$md, $permission);
		
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		//Deny all
		//Deny profile/itemContainer
		$permission = array("readGranted"=>false, "readDenied"=> true, "writeGranted"=> true, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		//Deny group/mediaItem
		self::$pm->setPermission(self::$pg, self::$md, $permission);
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		//Deny profile/item
		self::$pm->setPermission(self::$viewerProfile, self::$md, $permission);
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		
		
		
		
		//Grant all
		//Grant profile/itemContainer
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> true, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);//Still denied
		
		//Grant group/mediaItem
		self::$pm->setPermission(self::$pg, self::$md, $permission);
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);//Still denied
		
		//Grant group/mediaItem
		self::$pm->setPermission(self::$viewerProfile, self::$md, $permission);
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNotNull($fetched);//All granted, should not be denied
		$this->assertEquals($fetched->getId(), self::$md->getId());
		
		
		
		
		
		
		
	}
	
	function testGetCollection(){

		//Grant all
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> true, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		self::$pm->setPermission(self::$viewerProfile, self::$md, $permission);
		self::$pm->setPermission(self::$viewerProfile, self::$ownerProfile, $permission);
		self::$pm->setPermission(self::$pg, self::$ic, $permission);
		
		
		$fetched = self::$fetcher->getCollection(self::$ic, self::$viewerProfile);
		
		$this->assertEquals($fetched->getSize(), 4);//2 photos, profile and album

		//Refetch
		$fetched = self::$fetcher->getCollection(self::$ic, self::$viewerProfile);
		$this->assertEquals($fetched->getSize(), 4);//2 photos, profile and album
		
		//Access to profileItem
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\ItemContainer", self::$ic->getId(), self::$viewerProfile);
		$this->assertNotNull($fetched);
		$this->assertEquals($fetched->getId(), self::$ic->getId());
		
		
		//Deny access to album
		$permission = array("readGranted"=>false, "readDenied"=> true, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		$fetched = self::$fetcher->get("Wixet\WixetBundle\Entity\MediaItem", self::$md->getId(), self::$viewerProfile);
		$this->assertNull($fetched);
		
		/*********************************************/
		
		
		//Implicit deny
		$permission = array("readGranted"=>false, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		self::$pm->setPermission(self::$viewerProfile, self::$md, $permission);
		self::$pm->setPermission(self::$viewerProfile, self::$md2, $permission);
		self::$pm->setPermission(self::$viewerProfile, self::$ownerProfile, $permission);
		
		self::$pm->setPermission(self::$pg, self::$ic, $permission);
		self::$pm->setPermission(self::$pg, self::$md, $permission);
		self::$pm->setPermission(self::$pg, self::$md2, $permission);
		self::$pm->setPermission(self::$pg, self::$ownerProfile, $permission);
		
		$fetched = self::$fetcher->getCollection(self::$ic, self::$viewerProfile);
		
		$this->assertEquals($fetched->getSize(), 0);
		
		
		//Grant group / itemContainer
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> true, "writeDenied"=> false);
		self::$pm->setPermission(self::$pg, self::$ic, $permission);
		
		$fetched = self::$fetcher->getCollection(self::$ic, self::$viewerProfile);
		$this->assertEquals($fetched->getSize(), 4);//2 photos, profile and album
		
		//Deny group / itemContainer
		$permission = array("readGranted"=>false, "readDenied"=> true, "writeGranted"=> false, "writeDenied"=> true);
		self::$pm->setPermission(self::$pg, self::$ic, $permission);
		
		$fetched = self::$fetcher->getCollection(self::$ic, self::$viewerProfile);
		$this->assertEquals($fetched->getSize(), 0);
		
		//Deny group / grant profile - itemContainer
		$permission = array("readGranted"=>false, "readDenied"=> true, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$pg, self::$ic, $permission);
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		
		$fetched = self::$fetcher->getCollection(self::$ic, self::$viewerProfile);
		$this->assertEquals($fetched->getSize(), 0);

		//Grant group / deny profile - itemContainer
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$pg, self::$ic, $permission);
		$permission = array("readGranted"=>false, "readDenied"=> true, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		
		$fetched = self::$fetcher->getCollection(self::$ic, self::$viewerProfile);
		$this->assertEquals($fetched->getSize(), 0);
		
		//Grant group / grant profile - itemContainer
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		self::$pm->setPermission(self::$pg, self::$ic, $permission);
		
		$fetched = self::$fetcher->getCollection(self::$ic, self::$viewerProfile);
		$this->assertEquals($fetched->getSize(), 4);
		
		//Deny one item to profile
		$permission = array("readGranted"=>true, "readDenied"=> false, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$ic, $permission);
		self::$pm->setPermission(self::$pg, self::$ic, $permission);
		$permission = array("readGranted"=>false, "readDenied"=> true, "writeGranted"=> false, "writeDenied"=> false);
		self::$pm->setPermission(self::$viewerProfile, self::$md, $permission);
		
		$fetched = self::$fetcher->getCollection(self::$ic, self::$viewerProfile);

		$this->assertEquals($fetched->getSize(), 3);
		
		
		
		
		
	}
	
	
}
