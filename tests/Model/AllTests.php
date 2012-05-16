<?php
if (! defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Translation_Test_AllTests::main');
}
require dirname(__FILE__) . '/../../../../../tests/TestHelper.php';

/**
 * @class Seo_Test_Model_AllTests
 * @package Tests
 * @subpackage Seo
 * @author Richard Déloge, rd@octaveoctave.com
 *
 * To run all tests on model SEO
 */
class Seo_Test_Model_AllTests
        extends PHPUnit_Framework_TestSuite
{
    public static function main ()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite ()
    {
        $suite = new PHPUnit_Framework_TestSuite('Seo Model Suite');
        $suite->addTest(Seo_Test_Model_DbTables_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Seo_Test_Model_AllTests::main') {
    Seo_Test_Model_AllTests::main();
}
