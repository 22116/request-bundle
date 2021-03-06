<?php declare(strict_types=1);

namespace LSBProject\RequestBundle\DependencyInjection;

use LSBProject\RequestBundle\Util\NamingConversion\NamingConversionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class LSBProjectRequestExtension extends Extension
{
    /**
     * @param mixed[]          $configs
     * @param ContainerBuilder $container
     *
     * @return void
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        /** @var Configuration $configuration */
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        if ($config['naming_conversion']) {
            $container
                ->getDefinition(NamingConversionInterface::class)
                ->setDecoratedService($config['naming_conversion']);
        }
    }
}
