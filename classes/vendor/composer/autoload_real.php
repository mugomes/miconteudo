<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitc8a11cd04e34cdafd826be09d5cc929e
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitc8a11cd04e34cdafd826be09d5cc929e', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitc8a11cd04e34cdafd826be09d5cc929e', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitc8a11cd04e34cdafd826be09d5cc929e::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}