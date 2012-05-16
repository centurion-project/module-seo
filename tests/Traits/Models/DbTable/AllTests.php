<?php
if (! defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Translation_Test_AllTests::main');
}
require dirname(__FILE__) . '/../../../../../../../tests/TestHelper.php';

/**
 * @class Seo_Test_Traits_Models_AllTests
 * @package Tests
 * @subpackage Seo
 * @author Richard Déloge, rd@octaveoctave.com
 *
 * To run all tests on trait SEO to save and get meta for a record
 */
class Seo_Test_Traits_Models_DbTable_AllTests
    extends PHPUnit_Framework_TestSuite
{
    public static function main ()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite ()
    {
        $suite = new PHPUnit_Framework_TestSuite('Seo Traits Models DbTable Suite');
        $suite->addTestSuite('Seo_Test_Traits_Models_DbTable_RowGetMetasTest');
        $suite->addTestSuite('Seo_Test_Traits_Models_DbTable_RowSaveMetasTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Seo_Test_Traits_Models_DbTable_AllTests::main') {
    Seo_Test_Traits_Models_DbTable_AllTests::main();
}
