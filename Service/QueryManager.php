<?php

namespace Wixet\WixetBundle\Service;

class QueryManager
{
    
	private $doctrine;
	
	public function __construct($em)
	{
		$this->doctrine = $em;
	}
	
	public function fullSearch($query,$offset = 0, $limit = 10,$filter = null){
		//Iniciar daemon 
		$s = new \SphinxClient();
		$s->setServer("localhost", 9312);
		$s->setMatchMode(SPH_MATCH_ANY);
		$s->setMaxQueryTime(3);
		
		
		$s->setGroupBy("profile_id",SPH_GROUPBY_ATTR);
		$s->setGroupDistinct("profile_id");
		
		//setlimits(offset,limit, results saved in memory, stop when X results found
		$s->setLimits($offset,$limit,$limit*10,$limit*100); 
		$result = $s->query($query, "wixet");
		
		
		$matches = array();
		$matches['total']= $result['total_found'];
		
		$matchList = array();
		if(isset($result["matches"])){
			$respository = $this->doctrine->getRepository('Wixet\WixetBundle\Entity\UserProfile');
			foreach($result["matches"] as $result){ //$key is the object id in db
					$profile = $respository->find($result["attrs"]["profile_id"]);
					
					$match = array();
					
					$match['name'] = $profile->getFirstName()." ".$profile->getLastName();
					$match['id'] = $profile->getId();
					$match['thumbnail'] = $profile->getId();
					$match['group'] = null;
					$match['city'] = array("name"=> "Palencia", "id"=>1);
					$match['type'] = 'UserProfile';
					$match['interests'] = array();
					
					$highlights = array();
					
					foreach($profile->getExtensions() as $extension){
						$h = $s->buildExcerpts(
							array($extension->getBody()),
							"wixet",
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
