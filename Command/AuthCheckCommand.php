<?php 

namespace Wixet\WixetBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AuthCheckCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('auth:checkUser')
            ->setDescription('Auth users to ejabber')
        	->addArgument('username', InputArgument::REQUIRED, 'Username')
        	->addArgument('password', InputArgument::REQUIRED, 'Password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$username = $input->getArgument('username');
    	$password = $input->getArgument("password");
    	
    	$container = $this->getApplication()->getKernel()->getContainer();
    	
    	$encoder_service = $container->get('security.encoder_factory');
    	$em = $container->get('doctrine')->getEntityManager();
    	
   try{
    	$user = $em->getRepository('Wixet\WixetBundle\Entity\User')->findOneByUsername($username);
    	
    	
    	$encoder = $encoder_service->getEncoder($user);
    	$encoded_pass = $encoder->encodePassword($password, $user->getSalt());
    	$output->write($user->getPassword() == $encoded_pass?"true":"false");
    	
   }catch(\Exception $e){
   		$output->write("false");
   }
    	
    	
    }


}