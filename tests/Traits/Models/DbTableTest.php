<?php
/**
 * Author : Richard Déloge, rd@octaveoctave.com
 */
require_once dirname(__FILE__) . '/../../../../../../tests/TestHelper.php';

/**
 * @class Seo_Test_Traits_Model_DbTableTest
 * @package Tests
 * @subpackage Seo
 * @author Richard Déloge, rd@octaveoctave.com
 *
 * Test the behavior of the trait SEO for DbTable models
 */
class Seo_Test_Traits_Models_DbTableTest
        extends Seo_Test_Traits_Common_Abstract{

    /**
     * To initialize the DB of test
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet(){
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/../_dataSet/commonTest.xml'
        );
    }

    /**
     * Test the behavior of the method getDefinedMeta for a basic model (method not overwritten by the model)
     * from the configuration
     */
    public function testNormalBehaviorOfGetDefinedMeta(){
        //Change the local configuration for next tests
        Centurion_Config_Manager::set('seo.meta.types', array('description', 'keywords'));

        $firstModel = Centurion_Db::getSingleton('seotable/first_model');
        //Test if the list is good
        $this->assertEquals(
                array('description', 'keywords'),
                $firstModel->getDefinedMeta(),
                'Error, the list of meta types is not the list defined in the configuration'
            );

        //We change the configuration
        Centurion_Config_Manager::set('seo.meta.types', array('keywords'));
        $this->assertEquals(
                array('keywords'),
                $firstModel->getDefinedMeta(),
                'Error, the new list of meta types is not the new list defined in the configuration'
            );

        //Check if the new list is always valid for a new model
        $this->assertEquals(
                array('keywords'),
                Centurion_Db::getSingleton('seotable/second_model')->getDefinedMeta(),
                'Error, the new list of meta types is not the new list defined in the configuration for the new model'
            );

        Centurion_Config_Manager::set('seo.meta.types', null);
        $this->assertEmpty(
                $firstModel->getDefinedMeta(),
                'Error, when there are no defined meta, the method must return null'
            );
    }

    /**
     * Test the behavior of the method getDefinedMeta for a model where this method was overwritten by a developer)
     */
    public function testCustomizedBehaviorOfGetDefinedMeta(){
        //Change the local configuration for next tests
        Centurion_Config_Manager::set('seo.meta.types', array('description', 'keywords'));

        $firstModel = Centurion_Db::getSingleton('seotable/custo_model');
        //Test if the list is good
        $this->assertEquals(
                array('keywords'),
                $firstModel->getDefinedMeta(),
                'Error, the list of meta types is the list defined in the configuration, but, this model must customize'
                    .' this list and return only "keywords"'
            );

        Centurion_Config_Manager::set('seo.meta.types', null);
        //Test if the list is good
        $this->assertEquals(
            array('keywords'),
            $firstModel->getDefinedMeta(),
            'Error, this model must return the customized list of meta types, even when the defined list in the'
                .' configuration is empty'
        );
    }
}