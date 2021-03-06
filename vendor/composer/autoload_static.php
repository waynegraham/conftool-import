<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit49c53b5f0f27285db8ada3932ffae725
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\SimpleCache\\' => 16,
            'PhpOffice\\PhpSpreadsheet\\' => 25,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\SimpleCache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/simple-cache/src',
        ),
        'PhpOffice\\PhpSpreadsheet\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoffice/phpspreadsheet/src/PhpSpreadsheet',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit49c53b5f0f27285db8ada3932ffae725::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit49c53b5f0f27285db8ada3932ffae725::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
