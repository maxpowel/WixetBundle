<?php 

namespace Wixet\WixetBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class xmlCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('index:xml')
            ->setDescription('Get xml for to index')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    	$em = $this->getContainer()->get('doctrine')->getEntityManager();
    	$connection = $em->getConnection();
    	
    	//Print schema
    	$output->writeln('<?xml version="1.0" encoding="utf-8"?>');
    	$output->writeln('<sphinx:docset>');
    	$output->writeln('<sphinx:schema>');
    	$output->writeln('<sphinx:field name="name"/>');
    	$output->writeln('<sphinx:field name="body"/> ');
    	$output->writeln('<sphinx:attr name="profile_id" type="int" bits="16" default="0"/>');
    	$output->writeln('</sphinx:schema>');
    	//End schema
    	
    	$q = $em->createQuery("SELECT p.id, p.first_name, p.last_name FROM Wixet\WixetBundle\Entity\UserProfile p");
    	foreach($q->getArrayResult() as $p){
    		//Document begin
    		$output->writeln('<sphinx:document id="'.$p['id'].'">');
    		$output->write('<name>');
    		$output->write($p['first_name'].' '.$p['last_name']);
    		$output->writeln('</name>');
    		$output->writeln('<body><![CDATA[');
    		//Profile extension data
    		
    		$sql = "SELECT title,body FROM user_profile_extension WHERE profile_id = ".$p['id'];
    		$statement = $connection->query($sql);
    		while( ($data = $statement->fetch())){
    			$output->writeln(htmlentities($data['title']." ".$data['body']));
    		}	
    		//End profile extension data
    		
    		$output->writeln(']]></body>');
			$output->write('<profile_id>');
			$output->write($p['id']);
			$output->writeln('</profile_id>');
			$output->writeln('</sphinx:document>');
			
			//End document
    		
    	}
    	$output->writeln('</sphinx:docset>');	
    	

    }
}