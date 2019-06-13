<?php


namespace AdminBundle\Extension;


use Symfony\Bundle\FrameworkBundle\Routing\Router;

class AdminExtension extends \Twig_Extension{

  private $router;

  public function __construct(Router $router)
  {
      $this->router = $router;
  }

  /**
   * Define Twig Function
   * {@inheritdoc}
   */
  public function getFunctions()
  {
    return array(   
      new \Twig_SimpleFunction('unset', array($this, 'unsetArrayKey')),
      new \Twig_SimpleFunction('checkUrl', array($this, 'routeExists')),

    );
  }


  /**
   * File page js, css file for auto include to html
   * @param Array $arr
   * @param integer|string $key
   * @return array
   *
   * @author Bienmai 2017/08/09
   */
  function unsetArrayKey($arr, $key)
  {
    unset($arr[$key]);

    return $arr;
  }
  function routeExists($name)
  {      
    return $this->router->getRouteCollection()->get($name);
  }


}
