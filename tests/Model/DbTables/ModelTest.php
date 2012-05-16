<?php
/**
 * Author : Richard Déloge, rd@octaveoctave.com
 *
 * Test for the class Translation_Traits_Common
 */
require_once dirname(__FILE__) . '/../../../../../../tests/TestHelper.php';

/**
 * @class Seo_Test_Model_DbTables_Model
 * @package Tests
 * @subpackage Seo
 * @author Richard Déloge, rd@octaveoctave.com
 *
 */
class Seo_Test_Model_DbTables_ModelTest
        extends PHPUnit_Framework_TestCase{

    /**
     * Test if the method accepts object of instance Seo_Model_Website_Interface
     */
    public function testRegisterGoodWebsiteAdapter(){
        $adapter = new Seotable_Model_WebsiteAdapter();
        //Test for a Seo_Model_Website_Interface object
        Seo_Model_DbTable_Meta::registerWebsiteAdapter($adapter);

        //Test for null value (to unregister adapter)
        Seo_Model_DbTable_Meta::registerWebsiteAdapter(null);
    }

    /**
     * Test if the method throws an exception when the adapter is bad (not an instance of Seo_Model_Website_Interface)
     */
    public function testRegisterBadWebsiteAdapter(){
        $failure = null;
        try{
            //Test with a bad object
            Seo_Model_DbTable_Meta::registerWebsiteAdapter(new stdClass());
            $failure = true;
        }
        catch(Exception $e){
            $failure = false;
        }

        if(false !== $failure){
            $this->fail('Error, the method Seo_Model_DbTable_Meta::registerWebsiteAdapter() must throw an exception'
                .' when the adapter is not a valid object');
        }

        try{
            //Test with a natural type
            Seo_Model_DbTable_Meta::registerWebsiteAdapter(4);
        }
        catch(Exception $e){
            return;
        }

        $this->fail('Error, the method Seo_Model_DbTable_Meta::registerWebsiteAdapter() must throw an exception'
            .' when the adapter is not a valid object');
    }

    /**
     * Test if the model class returns the good website adapter and if the behavior is good
     */
    public function testGetWebsiteAdapter(){
        $adapter = new Seotable_Model_WebsiteAdapter();
        Seo_Model_DbTable_Meta::registerWebsiteAdapter($adapter);
        //Check if the model class return an website adapter
        $this->assertInstanceOf(
                'Seo_Model_Website_Interface',
                Seo_Model_DbTable_Meta::getWebsiteAdapter(),
                'Error, the adapter must be an instance of Seo_Model_Website_Interface'
            );

        $adapter->setWebsiteId(4);
        //Check if the model class return the good website adapter (the registered adapter)
        $this->assertInstanceOf(
                'Seotable_Model_WebsiteAdapter',
                Seo_Model_DbTable_Meta::getWebsiteAdapter(),
                'The returned website adapter has not the same class of the registered adapter'
            );
        //Check if the instance of adapter is the original instance
        $this->assertEquals(
                Seo_Model_DbTable_Meta::getWebsiteAdapter()->getWebsiteId(),
                4,
                'Error, the website adapter is thirsty in the model Seo/meta'
            );

        //Check if we unregister the adapter, the class model return null
        Seo_Model_DbTable_Meta::registerWebsiteAdapter(null);
        $this->assertNull(
                Seo_Model_DbTable_Meta::getWebsiteAdapter(),
                'Error, the website adapter must be null'
            );
    }
}