<?php 
namespace UtilBundle\Routing;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Bundle\FrameworkBundle\Routing\Router as BaseRouter;

class Router extends BaseRouter implements ContainerAwareInterface
{
    private $container;

    public function __construct(ContainerInterface $container, $resource, array $options = array(), RequestContext $context = null)
    {
        parent::__construct($container, $resource, $options, $context);
        $this->setContainer($container);
    }

    public function getGenerator()
    {
        $generator = parent::getGenerator();
        $generator->setContainer($this->container);
        return $generator;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}