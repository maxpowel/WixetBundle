<?php

namespace Wixet\WixetBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;


class WixetExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {

                $config = $configs[0];
                $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('wixet.xml');
        
        //$container->setParameter('wixet.service.mood', isset($configs['mood']));
        
        $container->setParameter('wixet.config', $config);
        
        /*foreach($config['classes'] as $key => $className){
                        $container->setParameter('wixet.'.$key.'.class', $className);
                }
        */
//        $container->setParameter('wixet.em', $this->get('doctrine.orm.entity_manager'));
    }

    public function getAlias()
    {
        return 'wixet';
    }

    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/doctrine';
    }
}

