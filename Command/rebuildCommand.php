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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	system($this->getContainer()->getParameter('index_command'));
    	$output->writeln("Done");

    }
}