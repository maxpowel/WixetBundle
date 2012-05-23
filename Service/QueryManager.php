<?php

namespace Wixet\WixetBundle\Service;

class QueryManager
{
    
	private $doctrine;
	
	public function __construct($em, $security)
	{
		$this->doctrine = $em;
		$this->security = $security;
	}
	
	public function fullSearch($query,$offset = 0, $limit = 10,$filter = null){
		$extensionIndex = "extensions";
		$viewer = $this->security->getToken()->getUser()->getProfile();
		$group = $viewer->getMainGroup();
		$connection = $this->doctrine->getConnection();
		//Iniciar daemon 
		$s = new \SphinxClient();
		$s->setServer("localhost", 9312);
		$s->setMatchMode(SPH_MATCH_ANY);
		$s->setMaxQueryTime(3);
		
		
		//$s->setGroupBy("profile_id",SPH_GROUPBY_ATTR);
		//$s->setGroupDistinct("profile_id");
		//Exclude the viewer profile
		$s->setFilter('profile_id',array($viewer->getId()), true);
		
		//setlimits(offset,limit, results saved in memory, stop when X results found
		$s->setLimits($offset,$limit,$limit*10,$limit*100); 
		$result = $s->query($query, $extensionIndex);
		
		//print_r($result);
		$matches = array();
		$matches['total']= $result['total_found'];
		
		$matchList = array();
		if(isset($result["matches"])){
			$respository = $this->doctrine->getRepository('Wixet\WixetBundle\Entity\UserProfile');
			foreach($result["matches"] as $id=>$result){ //$key is the object id in db
					//$profile = $respository->find($result["attrs"]["profile_id"]);
					$profile = $respository->find($id);
					
					$match = array();
					
					$match['name'] = $profile->getFirstName()." ".$profile->getLastName();
					$match['id'] = $profile->getId();
					$match['thumbnail'] = $profile->getId();
					
					//Check if user is in the main group
					$sql = "SELECT count(userprofile_id) as exist from profilegroup_userprofile WHERE profilegroup_id = ". $group->getId() ." AND userprofile_id = ".$profile->getId();
					$statement = $connection->query($sql);
					$res = $statement->fetch();
					if($res['exist'] > 0)
						$match['group'] = array("name" => $group->getName(), "id" => $group->getId());
					
					
					
					//City
					$city = $profile->getCity();
					if($city)
						$match['city'] = array("name"=> $city->getName(), "id"=>$city->getId());
					
					
					$match['type'] = 'UserProfile';
					$match['interests'] = array();
					
					$highlights = array();
					
					foreach($profile->getExtensions() as $extension){
						$h = $s->buildExcerpts(
							array($extension->getBody()),
							$extensionIndex,
							$query,
							array("before_match"=>"[b]",
								  "after_match"=>"[/b]"
								  )
						);
						//Javascript will replace [b] for <strong>
						//Do not use raw html to avoid xss
						
						$highlights[] = array(
							"title" => $extension->getTitle(), 
							"body" => $h[0]
						);
					}
					
					$match['highlights'] = $highlights;
					$matchList[] = $match;
			}
		}
		
		
		$matches['results'] = $matchList;
		
		return $matches;
	}
	
	public function __toString()
	{
		return 'Wixet QueryManager Service';
	}
}
