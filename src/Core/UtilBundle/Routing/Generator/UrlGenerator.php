<?php 
namespace UtilBundle\Routing\Generator;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;

class UrlGenerator extends BaseUrlGenerator implements ContainerAwareInterface
{
    private $container;

    protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, array $requiredSchemes = array())
    {
        if(!empty($parameters)) {
            $ignore = array('month', 'year', 'page', 'perPage');
            $publicRoutes = Constant::PUBLIC_ROUTES;
            foreach ($parameters as $key => $value) {
                if (!empty($value) && !is_array($value) && !in_array($key, $ignore) && preg_match('/^\d+$/', $value)) {
                    $parameters[$key] = Common::encodeHex($value);
                }

                if (!empty($value) && in_array($name, $publicRoutes) && $name != 'pdf_cif') {
                    if ($key == 'orderNumber' || $name == 'failed_index') {
                        $parameters[$key] = Common::encryptTripleDes($value, $this->container->getParameter('tripledes_hashphrase'));
                    }
                }
            }
        }
		
        return parent::doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, $requiredSchemes);
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}