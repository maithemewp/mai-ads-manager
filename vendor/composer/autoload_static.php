<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit591553eddb72da53eb2fb1c33e920f41
{
    public static $files = array (
        'efd9d646f43178e7ba3f07758c02ce1d' => __DIR__ . '/..' . '/yahnis-elsts/plugin-update-checker/load-v5p2.php',
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit591553eddb72da53eb2fb1c33e920f41::$classMap;

        }, null, ClassLoader::class);
    }
}
