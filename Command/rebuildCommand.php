<?php 

namespace Wixet\WixetBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class rebuildCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('index:rebuild')
            ->setDescription('Rebuild index')
        	->addArgument('index', InputArgument::REQUIRED, 'Type: extensions or contacts')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	if($input->getArgument('index') == "extensions")
    		system($this->getContainer()->getParameter('index_command_extensions'));
    	elseif($input->getArgument('index') == "contacts")
    		system($this->getContainer()->getParameter('index_command_contacts'));
    	$output->writeln("Done");

    }
}