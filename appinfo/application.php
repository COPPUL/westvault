<?php

/*
 *  This file is licensed under the MIT License version 3 or
 *  later. See the LICENSE file for details.
 *
 * Copyright 2017 michael.
 */

namespace OCA\WestVault\AppInfo;

use OCA\WestVault\Controller\ConfigController;
use OCA\WestVault\Controller\PageController;
use OCA\WestVault\Hooks\UserHooks;
use OCA\WestVault\Service\Navigation;
use OCA\WestVault\Service\WestVaultConfig;
use OCP\AppFramework\App;
use OCP\IContainer;

class Application extends App {

    public function __construct($appName = 'westvault', array $urlParams = array()) {
        parent::__construct($appName, $urlParams);

        /**
         * IContainer
         */
        $container = $this->getContainer();

        $container->registerService('Config', function($c) {
            return $c->query('ServerContainer')->getConfig();
        });

        // User Session gets information about the logged in user. It must be
        // registered so that the User service can use it.
        $container->registerService('UserSession', function($c) {
            return $c->query('ServerContainer')->getUserSession();
        });

        // currently logged in user, userId can be gotten by calling the
        $container->registerService('User', function($c) {
            return $c->query('UserSession')->getUser();
        });

        $container->registerService('GroupManager', function(IContainer $c) {
            return $c->query('ServerContainer')->getGroupManager();
        });

        $container->registerService('WestVaultConfig', function($c) {
            return new WestVaultConfig($c->query('Config'), $c->query('AppName'));
        });
        
        $container->registerService('WestVaultNavigation', function($c){
            return new Navigation($c->query('OCP\IURLGenerator'));
        });

        $container->registerService('PageController', function($c) {
            return new PageController(
                    $c->query('AppName'), 
                    $c->query('Request'), 
                    $c->query('User'), 
                    $c->query('WestVaultNavigation')
            );
        });
        
        $container->registerService('SwordClient', function($c){
            return new SwordClient(
                $c->query('WestVaultConfig'),
                $c->query($c->query('OCP\IURLGenerator'))
            );
        });
        
        $container->registerService('ConfigController', function($c) {
            return new ConfigController(
                    $c->query('AppName'), 
                    $c->query('Request'), 
                    $c->query('User'), 
                    $c->query('GroupManager'),
                    $c->query('WestVaultConfig'),
                    $c->query('WestVaultNavigation')
            );
        });
        
        $container->registerService('UserHooks', function($c) {
            return new UserHooks(
                    $c->query('ServerContainer')->getUserManager(),
                    $c->query('WestVaultConfig')
            );
        });
    }

}
