<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2a3a0fb1a04e3707af5cd51220e27955
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'Lib\\' => 4,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Lib\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Lib',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/App',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'PHPExcel' => 
            array (
                0 => __DIR__ . '/..' . '/phpoffice/phpexcel/Classes',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2a3a0fb1a04e3707af5cd51220e27955::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2a3a0fb1a04e3707af5cd51220e27955::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit2a3a0fb1a04e3707af5cd51220e27955::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
