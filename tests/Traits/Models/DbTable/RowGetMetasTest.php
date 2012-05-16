<?php
/**
 * Author : Richard Déloge, rd@octaveoctave.com
 */
require_once dirname(__FILE__) . '/../../../../../../../tests/TestHelper.php';

/**
 * @class Seo_Test_Traits_Models_DbTable_RowGetMetasTest
 * @package Tests
 * @subpackage Seo
 * @author Richard Déloge, rd@octaveoctave.com
 *
 * Test the behavior of the trait SEO when we want get metas of a record, for a basic record, a translatable record
 * or meta for a specific website
 */
class Seo_Test_Traits_Models_DbTable_RowGetMetasTest
        extends Seo_Test_Traits_Common_Abstract{

    /**
     * To initialize the DB of test
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet(){
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/../../_dataSet/commonTest.xml'
        );
    }

    /**
     * Test the behavior of the trait for a basic record with an existant metaset (meta already exist in the DBMS, the
     * trait must not write them automatically)
     */
    public function testBehaviorGetExistantMeta(){
        Seo_Model_DbTable_Meta::registerWebsiteAdapter(null);
        //Change the local configuration for next tests
        Centurion_Config_Manager::set('seo.meta.types', array('description', 'keywords'));

        //Get the row id:2 for the "FirstModel"
        $firstModel = Centurion_Db::getSingleton('seotable/first_model');
        $fMRow1 = $firstModel->get(array('id' => 1));

        $metasRowset = $fMRow1->getMetaRowset();
        $this->assertTrue(
            is_array($metasRowset),
            'The method getMetaRowset must return an array and not an Centurion_Db_Table_Rowset_Abstract object'
        );

        $this->assertEquals(
            2,
            count($metasRowset),
            'There are already two metas for the first row'
        );

        $metaRow1 = $metasRowset[0];
        $metaRow2 = $metasRowset[1];

        //The first row must be the row for description (because we have ordered the select on the type)
        $this->assertEquals(
            'description',
            $metaRow1->type,
            'Error, the first meta row must be the row to store the description'
        );

        $this->assertEquals(
            'meta description first 1',
            $metaRow1->content,
            'Error, the meta row for the description has not the good value'
        );

        //The second row must be the row for keywords (because we have ordered the select on the type)
        $this->assertEquals(
            'keywords',
            $metaRow2->type,
            'Error, the first meta row must be the row to store the keywords'
        );

        $this->assertEquals(
            'meta keywords first 1',
            $metaRow2->content,
            'Error, the meta row for the keywords has not the good value'
        );

        //In this test, there are not website or language, so, fields of these rows must be at null
        $this->assertNull(
            $metaRow1->website_id,
            'Error, all meta rows in this test must has theirs fields "website_id" at null because there are no website'
        );

        $this->assertNull(
            $metaRow1->language_id,
            'Error, all meta rows in this test must has theirs fields "language_id" at null because secondModel is not '
                .' translatable'
        );
    }

    /**
     * Test the behavior of the trait SEO to get non-existant metas for a record. Also, the trait must write
     * automatically meta from the record's table configuration
     */
    public function testBehaviorGetMetasWithAutoGeneration(){
        Seo_Model_DbTable_Meta::registerWebsiteAdapter(null);
        //Change the local configuration for next tests
        Centurion_Config_Manager::set('seo.meta.types', array('description', 'keywords'));

        //Get the row id:2 for the "FirstModel"
        $firstModel = Centurion_Db::getSingleton('seotable/first_model');
        $fMRow1 = $firstModel->get(array('id' => 2));

        $metasRowset = $fMRow1->getMetaRowset();
        $this->assertTrue(
            is_array($metasRowset),
            'The method getMetaRowset must return an array and not an Centurion_Db_Table_Rowset_Abstract object'
        );

        $this->assertEquals(
            2,
            count($metasRowset),
            'The trait seo must build automatically two meta rows for this row'
        );

        $metaRow1 = $metasRowset[0];
        $metaRow2 = $metasRowset[1];

        //The first row must be the row for description (because we have ordered the select on the type)
        $this->assertEquals(
            'description',
            $metaRow1->type,
            'Error, the first meta row must be the row to store the description'
        );

        $this->assertEquals(
            'first 2, CONTENU DE FIRST 2',
            $metaRow1->content,
            'Error, the meta row for the description has not the good value'
        );

        //The second row must be the row for keywords (because we have ordered the select on the type)
        $this->assertEquals(
            'keywords',
            $metaRow2->type,
            'Error, the first meta row must be the row to store the keywords'
        );

        $this->assertEquals(
            'ma contri first 2',
            $metaRow2->content,
            'Error, the meta row for the keywords has not the good value'
        );

        //In this test, there are not website or language, so, fields of these rows must be at null
        $this->assertNull(
            $metaRow1->website_id,
            'Error, all meta rows in this test must has theirs fields "website_id" at null because there are no website'
        );

        $this->assertNull(
            $metaRow1->language_id,
            'Error, all meta rows in this test must has theirs fields "language_id" at null because secondModel is not '
                .' translatable'
        );
    }

    /**
     * Test if the trait return translated meta for the translatable record
     */
    public function testBehaviorGetMetasWithLanguage(){
        Seo_Model_DbTable_Meta::registerWebsiteAdapter(null);
        $this->_switchLocale('en');

        //Change the local configuration for next tests
        Centurion_Config_Manager::set('seo.meta.types', array('description', 'keywords'));

        //Get the row id:1 for the "translatable_model"
        $translatableModel = Centurion_Db::getSingleton('seotable/translatable_model');
        $tMRow1En = $translatableModel->get(array('id' => 1));

        $metasRowset = $tMRow1En->getMetaRowset();
        $this->assertTrue(
            is_array($metasRowset),
            'The method getMetaRowset must return an array and not an Centurion_Db_Table_Rowset_Abstract object'
        );

        $this->assertEquals(
            2,
            count($metasRowset),
            'The trait seo must build automatically two meta rows for this row'
        );

        $metaRow1 = $metasRowset[0];
        $metaRow2 = $metasRowset[1];

        //The first row must be the row for description (because we have ordered the select on the type)
        $this->assertEquals(
            'description',
            $metaRow1->type,
            'Error, the first meta row must be the row to store the description'
        );

        $this->assertEquals(
            'contenu translatable 1 en',
            $metaRow1->content,
            'Error, the meta row for the description has not the good value'
        );

        //The second row must be the row for keywords (because we have ordered the select on the type)
        $this->assertEquals(
            'keywords',
            $metaRow2->type,
            'Error, the first meta row must be the row to store the keywords'
        );

        $this->assertEquals(
            'translatable 1 en',
            $metaRow2->content,
            'Error, the meta row for the keywords has not the good value'
        );

        //In this test, there are not website or language, so, fields of these rows must be at null
        $this->assertNull(
            $metaRow1->website_id,
            'Error, all meta rows in this test must has theirs fields "website_id" at null because there are no website'
        );

        $this->assertEquals(
            2,
            $metaRow1->language_id,
            'Error, all meta rows in this test must has theirs fields "language_id" at 2'
        );


        //Switch to FR
        $this->_switchLocale('fr');

        $tMRow1Fr = $translatableModel->get(array('id' => 1));

        $metasRowset = $tMRow1Fr->getMetaRowset();
        $this->assertTrue(
            is_array($metasRowset),
            'The method getMetaRowset must return an array and not an Centurion_Db_Table_Rowset_Abstract object'
        );

        $this->assertEquals(
            2,
            count($metasRowset),
            'The trait seo must build automatically two meta rows for this row'
        );

        $metaRow1 = $metasRowset[0];
        $metaRow2 = $metasRowset[1];

        //The first row must be the row for description (because we have ordered the select on the type)
        $this->assertEquals(
            'description',
            $metaRow1->type,
            'Error, the first meta row must be the row to store the description'
        );

        $this->assertEquals(
            'contenu translatable 1',
            $metaRow1->content,
            'Error, the meta row for the description has not the good value'
        );

        //The second row must be the row for keywords (because we have ordered the select on the type)
        $this->assertEquals(
            'keywords',
            $metaRow2->type,
            'Error, the first meta row must be the row to store the keywords'
        );

        $this->assertEquals(
            'translatable 1',
            $metaRow2->content,
            'Error, the meta row for the keywords has not the good value'
        );

        //In this test, there are not website or language, so, fields of these rows must be at null
        $this->assertNull(
            $metaRow1->website_id,
            'Error, all meta rows in this test must has theirs fields "website_id" at null because there are no website'
        );

        $this->assertEquals(
            1,
            $metaRow1->language_id,
            'Error, all meta rows in this test must has theirs fields "language_id" at 1'
        );

        //Check if the version EN stay unchanged
        $this->_switchLocale('en');
        $tMRow1En = $translatableModel->get(array('id' => 1));

        $metasRowset = $tMRow1En->getMetaRowset();
        $this->assertTrue(
            is_array($metasRowset),
            'The method getMetaRowset must return an array and not an Centurion_Db_Table_Rowset_Abstract object'
        );

        $this->assertEquals(
            2,
            count($metasRowset),
            'The trait seo must build automatically two meta rows for this row'
        );

        $metaRow1 = $metasRowset[0];
        $metaRow2 = $metasRowset[1];

        //The first row must be the row for description (because we have ordered the select on the type)
        $this->assertEquals(
            'description',
            $metaRow1->type,
            'Error, the first meta row must be the row to store the description'
        );

        $this->assertEquals(
            'contenu translatable 1 en',
            $metaRow1->content,
            'Error, the meta row for the description has not the good value'
        );

        //The second row must be the row for keywords (because we have ordered the select on the type)
        $this->assertEquals(
            'keywords',
            $metaRow2->type,
            'Error, the first meta row must be the row to store the keywords'
        );

        $this->assertEquals(
            'translatable 1 en',
            $metaRow2->content,
            'Error, the meta row for the keywords has not the good value'
        );

        //In this test, there are not website or language, so, fields of these rows must be at null
        $this->assertNull(
            $metaRow1->website_id,
            'Error, all meta rows in this test must has theirs fields "website_id" at null because there are no website'
        );

        $this->assertEquals(
            2,
            $metaRow1->language_id,
            'Error, all meta rows in this test must has theirs fields "language_id" at 2'
        );
    }

    /**
     * Check if the trait returns the meta for the current website for this record (and not meta for another website
     * for this same record)
     */
    public function testBehaviorGetMetasWithWebsite(){
        $adapter = new Seotable_Model_WebsiteAdapter();
        //Test for a Seo_Model_Website_Interface object
        Seo_Model_DbTable_Meta::registerWebsiteAdapter($adapter);
        $adapter->setWebsiteId(2);

        //Change the local configuration for next tests
        Centurion_Config_Manager::set('seo.meta.types', array('description', 'keywords'));

        //Get the row id:2 for the "FirstModel"
        $firstModel = Centurion_Db::getSingleton('seotable/first_model');
        $fMRow1 = $firstModel->get(array('id' => 2));

        $fMRow1->saveMetas(array(
                'description'=> 'description ws 2',
                'keywords'   => 'keywords ws 2',
            )
        );

        $metasRowset = $fMRow1->getMetaRowset();
        $this->assertTrue(
            is_array($metasRowset),
            'The method getMetaRowset must return an array and not an Centurion_Db_Table_Rowset_Abstract object'
        );

        $this->assertEquals(
            2,
            count($metasRowset),
            'The trait seo must build automatically two meta rows for this row'
        );

        $metaRow1 = $metasRowset[0];
        $metaRow2 = $metasRowset[1];

        //The first row must be the row for description (because we have ordered the select on the type)
        $this->assertEquals(
            'description',
            $metaRow1->type,
            'Error, the first meta row must be the row to store the description'
        );

        $this->assertEquals(
            'description ws 2',
            $metaRow1->content,
            'Error, the meta row for the description has not the good value'
        );

        //The second row must be the row for keywords (because we have ordered the select on the type)
        $this->assertEquals(
            'keywords',
            $metaRow2->type,
            'Error, the first meta row must be the row to store the keywords'
        );

        $this->assertEquals(
            'keywords ws 2',
            $metaRow2->content,
            'Error, the meta row for the keywords has not the good value'
        );

        //In this test, there are not website or language, so, fields of these rows must be at null
        $this->assertEquals(
            2,
            $metaRow1->website_id,
            'Error, all meta rows in this test must has theirs fields "website_id" at 2'
        );

        $this->assertNull(
            $metaRow1->language_id,
            'Error, all meta rows in this test must has theirs fields "language_id" at null because secondModel is not '
                .' translatable'
        );


        //Change website to website id:3
        $adapter->setWebsiteId(3);

        $fMRow1->saveMetas(array(
                'description'=> 'description ws 3',
                'keywords'   => 'keywords ws 3',
            )
        );

        $metasRowset = $fMRow1->getMetaRowset();
        $this->assertTrue(
            is_array($metasRowset),
            'The method getMetaRowset must return an array and not an Centurion_Db_Table_Rowset_Abstract object'
        );

        $this->assertEquals(
            2,
            count($metasRowset),
            'The trait seo must build automatically two meta rows for this row'
        );

        $metaRow1 = $metasRowset[0];
        $metaRow2 = $metasRowset[1];

        //The first row must be the row for description (because we have ordered the select on the type)
        $this->assertEquals(
            'description',
            $metaRow1->type,
            'Error, the first meta row must be the row to store the description'
        );

        $this->assertEquals(
            'description ws 3',
            $metaRow1->content,
            'Error, the meta row for the description has not the good value'
        );

        //The second row must be the row for keywords (because we have ordered the select on the type)
        $this->assertEquals(
            'keywords',
            $metaRow2->type,
            'Error, the first meta row must be the row to store the keywords'
        );

        $this->assertEquals(
            'keywords ws 3',
            $metaRow2->content,
            'Error, the meta row for the keywords has not the good value'
        );

        //In this test, there are not website or language, so, fields of these rows must be at null
        $this->assertEquals(
            3,
            $metaRow1->website_id,
            'Error, all meta rows in this test must has theirs fields "website_id" at 3'
        );

        $this->assertNull(
            $metaRow1->language_id,
            'Error, all meta rows in this test must has theirs fields "language_id" at null because secondModel is not '
                .' translatable'
        );

        //Check if the version for ws2 stay unchanged
        $adapter->setWebsiteId(2);
        $metasRowset = $fMRow1->getMetaRowset();
        $this->assertTrue(
            is_array($metasRowset),
            'The method getMetaRowset must return an array and not an Centurion_Db_Table_Rowset_Abstract object'
        );

        $this->assertEquals(
            2,
            count($metasRowset),
            'The trait seo must build automatically two meta rows for this row'
        );

        $metaRow1 = $metasRowset[0];
        $metaRow2 = $metasRowset[1];

        //The first row must be the row for description (because we have ordered the select on the type)
        $this->assertEquals(
            'description',
            $metaRow1->type,
            'Error, the first meta row must be the row to store the description'
        );

        $this->assertEquals(
            'description ws 2',
            $metaRow1->content,
            'Error, the meta row for the description has not the good value'
        );

        //The second row must be the row for keywords (because we have ordered the select on the type)
        $this->assertEquals(
            'keywords',
            $metaRow2->type,
            'Error, the first meta row must be the row to store the keywords'
        );

        $this->assertEquals(
            'keywords ws 2',
            $metaRow2->content,
            'Error, the meta row for the keywords has not the good value'
        );

        //In this test, there are not website or language, so, fields of these rows must be at null
        $this->assertEquals(
            2,
            $metaRow1->website_id,
            'Error, all meta rows in this test must has theirs fields "website_id" at 2'
        );

        $this->assertNull(
            $metaRow1->language_id,
            'Error, all meta rows in this test must has theirs fields "language_id" at null because secondModel is not '
                .' translatable'
        );
    }
}