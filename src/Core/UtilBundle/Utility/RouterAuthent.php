<?php

namespace UtilBundle\Utility;

use UtilBundle\Utility\Constant;
/**
 * Utility Authentication
 * author: Luyen Nguyen
 */
class RouterAuthent
{        
    /**
     * Check role login for user
     */
    public static function checkRoute($container)
    {
        $request = $container->get('request');
        $_route  = $request->attributes->get('_route');
        // $roles = $container->get('security.context')->getToken()->getUser()->getRoles();
        // Get all access routers        
        // $_routers = Constant::getRoutersOfRole($roles(0));
        // Set default value login
        // TODO update all types of user
        $_routers = Constant::getRoutersOfRole('Admin');
        if (in_array($_route, $_routers)) {
            return true;
        }
        return false;
    }        
}