<?php
/**
 * Author : Richard Déloge, rd@octaveoctave.com
 */
require_once dirname(__FILE__) . '/../../../../../../../tests/TestHelper.php';

/**
 * @class Seo_Test_Traits_Models_DbTable_RowTest
 * @package Tests
 * @subpackage Seo
 * @author Richard Déloge, rd@octaveoctave.com
 *
 * To test the behavior of the trait SEO when we want save meta of a record
 */
class Seo_Test_Traits_Models_DbTable_RowSaveMetasTest
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
     * Get param to get rows for meta
     * @param Seo_Traits_Model_DbTable_Row_Interface $row
     * @return array
     */
    protected function _getFilterForPk(Seo_Traits_Model_DbTable_Row_Interface $row){
        //Get the website adapter if the developer has defined one for this project
        $websiteAdapter = Seo_Model_DbTable_Meta::getWebsiteAdapter();

        //Prepare filters to select only meta for this row
        $_params = array(
            'record_id' => sha1(serialize($row->pk)),
            'model_id'  => Centurion_Db::getSingleton('core/content_type')->getContentTypeIdOf($row)
        );

        //If the row it is a translatable row, select for the current language
        if($row instanceof Translation_Traits_Model_DbTable_Row_Interface){
            $_params['language_id'] = $row->{Translation_Traits_Model_DbTable::LANGUAGE_FIELD};
        }

        //If we are in a multisite context
        if(null !== $websiteAdapter){
            $_params['website_id'] = $websiteAdapter->getWebsiteId();
        }

        return $_params;
    }

    /**
     * Test normal behavior of the method saveMetas of each seotable row.
     */
    public function testNormalBehaviorOfSaveMetasWithoutWebsiteAndLanguage(){
        Seo_Model_DbTable_Meta::registerWebsiteAdapter(null);
        //Change the local configuration for next tests
        Centurion_Config_Manager::set('seo.meta.types', array('description', 'keywords'));

        //Get the row id:2 for the "FirstModel"
        $firstModel = Centurion_Db::getSingleton('seotable/first_model');
        $fMRow1 = $firstModel->get(array('id' => 2));
        $fMRow1->saveMetas(array(
                    'description'   => 'meta description first 2',
                    'keywords'      => 'meta keywords first 2'
                )
            );

        //Test if the result is good
        $metaModel = Centurion_Db::getSingleton('seo/meta');
        $metaRowset = $metaModel->select(true)
                                ->filter($this->_getFilterForPk($fMRow1))
                                ->order('type')
                                ->fetchAll();

        //Get generated rowset by saveMetas() and check it
        $this->assertEquals(
                2,
                count($metaRowset),
                'Error, the previous save must create two new rows'
            );

        $metaRow1 = $metaRowset->current();
        $metaRowset->next();
        $metaRow2 = $metaRowset->current();

        //The first row must be the row for description (because we have ordered the select on the type)
        $this->assertEquals(
            'description',
            $metaRow1->type,
            'Error, the first meta row must be the row to store the description'
        );

        $this->assertEquals(
            'meta description first 2',
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
            'meta keywords first 2',
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

        //edit meta of current row
        $fMRow1->saveMetas(array(
                'description'   => 'meta description first 2 v2',
                'keywords'      => 'meta keywords first 2 v2'
            )
        );

        //Check if the result is good
        $metaRowset = $metaModel->select(true)
            ->filter($this->_getFilterForPk($fMRow1))
            ->order('type')
            ->fetchAll();

        //Get generated rowset by saveMetas() and check it
        $this->assertEquals(
            2,
            count($metaRowset),
            'Error, the previous save must create two new rows'
        );

        $metaRow1 = $metaRowset->current();
        $metaRowset->next();
        $metaRow2 = $metaRowset->current();

        //The first row must be the row for description (because we have ordered the select on the type)
        $this->assertEquals(
            'description',
            $metaRow1->type,
            'Error, the first meta row must be the row to store the description'
        );

        $this->assertEquals(
            'meta description first 2 v2',
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
            'meta keywords first 2 v2',
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
     * To test the trait SEO when we want save not valid meta for a record.
     * The trait must throws some exceptions
     */
    public function testBehaviorOfSaveMetasWithInvalidData(){
        Seo_Model_DbTable_Meta::registerWebsiteAdapter(null);
        //Change the local configuration for next tests
        Centurion_Config_Manager::set('seo.meta.types', array('description', 'keywords'));

        //Get the row id:2 for the "SecondModel"
        $secondModel = Centurion_Db::getSingleton('seotable/second_model');
        $sMRow1 = $secondModel->get(array('id' => 1));

        try{
            $sMRow1->saveMetas(array(
                    'description'   => 'meta description second 1',
                    'keywords'      => new stdClass()
                )
            );
        }
        catch(Exception $e){
            //Test if the trait seo has not saved anything
            $metaModel = Centurion_Db::getSingleton('seo/meta');
            $metaRowset = $metaModel->select(true)
                                    ->filter($this->_getFilterForPk($sMRow1))
                                    ->fetchAll();

            $this->assertEquals(
                0,
                count($metaRowset),
                'Error, when the metaSet is invalid, the trait Seo must do nothing'
            );

            return;
        }

        $this->fail('Error, the method saveMetas must throw an exception when metas are not valid');
    }

    /**
     * Test the behavior of the trait when we want save unknown Meta. The trait must ignore them
     */
    public function testBehaviorOfSaveMetasWithUnknownMetaType(){
        Seo_Model_DbTable_Meta::registerWebsiteAdapter(null);
        //Change the local configuration for next tests
        Centurion_Config_Manager::set('seo.meta.types', array('description', 'keywords'));

        //Get the row id:1 for the "SecondModel"
        $secondModel = Centurion_Db::getSingleton('seotable/second_model');
        $sMRow1 = $secondModel->get(array('id' => 1));

        $sMRow1->saveMetas(array(
                    'description'   => 'meta description second 1',
                )
            );

        //Test if the result is good
        $metaModel = Centurion_Db::getSingleton('seo/meta');
        $metaRowset = $metaModel->select(true)
            ->filter($this->_getFilterForPk($sMRow1))
            ->order('type')
            ->fetchAll();

        //Get generated rowset by saveMetas() and check it
        $this->assertEquals(
            1,
            count($metaRowset),
            'Error, the previous save must create only a new row '
                .'(because, the meta for keywords is not written automatically, is not defined in secondModel)'
        );

        $metaRow1 = $metaRowset->current();

        //The first row must be the row for description (because we have ordered the select on the type)
        $this->assertEquals(
            'description',
            $metaRow1->type,
            'Error, the first meta row must be the row to store the description'
        );

        $this->assertEquals(
            'meta description second 1',
            $metaRow1->content,
            'Error, the meta row for the description has not the good value'
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
     * Test normal behavior of the method saveMetas of each seotable row.
     */
    public function testNormalBehaviorOfSaveMetasWithLanguage(){
        Seo_Model_DbTable_Meta::registerWebsiteAdapter(null);
        $this->_switchLocale('fr');

        //Change the local configuration for next tests
        Centurion_Config_Manager::set('seo.meta.types', array('description', 'keywords'));

        //Get the row id:1 for the "FirstModel"
        $translatableModel = Centurion_Db::getSingleton('seotable/translatable_model');
        $tMRow1 = $translatableModel->get(array('id' => 1));
        $tMRow1->saveMetas(array(
                'description'   => 'meta description trans 1 FR',
                'keywords'      => 'meta keywords trans 1 FR'
            )
        );

        //Test if the result is good
        $metaModel = Centurion_Db::getSingleton('seo/meta');
        $metaRowset = $metaModel->select(true)
            ->filter($this->_getFilterForPk($tMRow1))
            ->order('type')
            ->fetchAll();

        //Get generated rowset by saveMetas() and check it
        $this->assertEquals(
            2,
            count($metaRowset),
            'Error, the previous save must create two new rows'
        );

        $metaRow1 = $metaRowset->current();
        $metaRowset->next();
        $metaRow2 = $metaRowset->current();

        //The first row must be the row for description (because we have ordered the select on the type)
        $this->assertEquals(
            'description',
            $metaRow1->type,
            'Error, the first meta row must be the row to store the description'
        );

        $this->assertEquals(
            'meta description trans 1 FR',
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
            'meta keywords trans 1 FR',
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

        //Do same operation in EN
        $this->_switchLocale('en');
        $tMRow1En = $translatableModel->get(array('id' => 1));

        $tMRow1En->saveMetas(array(
                'description'   => 'meta description trans 1 EN',
            )
        );

        //Test if the result is good
        $metaModel = Centurion_Db::getSingleton('seo/meta');
        $metaRowset = $metaModel->select(true)
            ->filter($this->_getFilterForPk($tMRow1En))
            ->order('type')
            ->fetchAll();

        //Get generated rowset by saveMetas() and check it
        $this->assertEquals(
            1,
            count($metaRowset),
            'Error, the previous save must create only one rows'
        );

        $metaRow1 = $metaRowset->current();

        //The first row must be the row for description (because we have ordered the select on the type)
        $this->assertEquals(
            'description',
            $metaRow1->type,
            'Error, the first meta row must be the row to store the description'
        );

        $this->assertEquals(
            'meta description trans 1 EN',
            $metaRow1->content,
            'Error, the meta row for the description has not the good value'
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

        //Check if meta for fr version stay unchanged
        //Test if the result is good
        $metaModel = Centurion_Db::getSingleton('seo/meta');
        $metaRowset = $metaModel->select(true)
            ->filter($this->_getFilterForPk($tMRow1))
            ->order('type')
            ->fetchAll();

        //Get generated rowset by saveMetas() and check it
        $this->assertEquals(
            2,
            count($metaRowset),
            'Error, the previous save must create two new rows'
        );

        $metaRow1 = $metaRowset->current();
        $metaRowset->next();
        $metaRow2 = $metaRowset->current();

        //The first row must be the row for description (because we have ordered the select on the type)
        $this->assertEquals(
            'description',
            $metaRow1->type,
            'Error, the first meta row must be the row to store the description'
        );

        $this->assertEquals(
            'meta description trans 1 FR',
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
            'meta keywords trans 1 FR',
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
    }

    /**
     * Check the behavior of the method saveMetas in the multisite context
     */
    public function testNormalBehaviorOfSaveMetasForWebsite(){
        $adapter = new Seotable_Model_WebsiteAdapter();
        //Test for a Seo_Model_Website_Interface object
        Seo_Model_DbTable_Meta::registerWebsiteAdapter($adapter);
        $adapter->setWebsiteId(2);

        //Change the local configuration for next tests
        Centurion_Config_Manager::set('seo.meta.types', array('description', 'keywords'));

        //Get the row id:2 for the "FirstModel"
        $firstModel = Centurion_Db::getSingleton('seotable/first_model');
        $fMRow2 = $firstModel->get(array('id' => 2));

        $fMRow2->saveMetas(array(
                'description'   => 'meta description first 2 website 2',
            )
        );

        //Test if the result is good
        $metaModel = Centurion_Db::getSingleton('seo/meta');
        $metaRowset = $metaModel->select(true)
            ->filter($this->_getFilterForPk($fMRow2))
            ->order('type')
            ->fetchAll();

        //Get generated rowset by saveMetas() and check it
        $this->assertEquals(
            1,
            count($metaRowset),
            'Error, the previous save must create one new row '
        );

        $metaRow1 = $metaRowset->current();

        //The first row must be the row for description (because we have ordered the select on the type)
        $this->assertEquals(
            'description',
            $metaRow1->type,
            'Error, the first meta row must be the row to store the description'
        );

        $this->assertEquals(
            'meta description first 2 website 2',
            $metaRow1->content,
            'Error, the meta row for the description has not the good value'
        );

        //In this test, there are not website or language, so, fields of these rows must be at null
        $this->assertEquals(
            2,
            $metaRow1->website_id,
            'Error, all meta rows in this test must has theirs fields "website_id" at null because there are no website'
        );

        $this->assertNull(
            $metaRow1->language_id,
            'Error, all meta rows in this test must has theirs fields "language_id" at null because secondModel is not '
                .' translatable'
        );

        //Edit meta for another website
        $adapter->setWebsiteId(3);
        $fMRow2->saveMetas(array(
                'description'   => 'meta description first 2 website 3',
            )
        );

        //Test if the result is good
        $metaRowset = $metaModel->select(true)
            ->filter($this->_getFilterForPk($fMRow2))
            ->order('type')
            ->fetchAll();

        //Get generated rowset by saveMetas() and check it
        $this->assertEquals(
            1,
            count($metaRowset),
            'Error, the previous save must create one new row '
        );

        $metaRow1 = $metaRowset->current();

        //The first row must be the row for description (because we have ordered the select on the type)
        $this->assertEquals(
            'description',
            $metaRow1->type,
            'Error, the first meta row must be the row to store the description'
        );

        $this->assertEquals(
            'meta description first 2 website 3',
            $metaRow1->content,
            'Error, the meta row for the description has not the good value'
        );

        //In this test, there are not website or language, so, fields of these rows must be at null
        $this->assertEquals(
            3,
            $metaRow1->website_id,
            'Error, all meta rows in this test must has theirs fields "website_id" at null because there are no website'
        );

        $this->assertNull(
            $metaRow1->language_id,
            'Error, all meta rows in this test must has theirs fields "language_id" at null because secondModel is not '
                .' translatable'
        );

        //Check if previous meta for website:2 stay unchanged
        $adapter->setWebsiteId(2);
        $metaRowset = $metaModel->select(true)
            ->filter($this->_getFilterForPk($fMRow2))
            ->order('type')
            ->fetchAll();

        //Get generated rowset by saveMetas() and check it
        $this->assertEquals(
            1,
            count($metaRowset),
            'Error, the previous save must create one new row '
        );

        $metaRow1 = $metaRowset->current();

        //The first row must be the row for description (because we have ordered the select on the type)
        $this->assertEquals(
            'description',
            $metaRow1->type,
            'Error, the first meta row must be the row to store the description'
        );

        $this->assertEquals(
            'meta description first 2 website 2',
            $metaRow1->content,
            'Error, the meta row for the description has not the good value'
        );

        //In this test, there are not website or language, so, fields of these rows must be at null
        $this->assertEquals(
            2,
            $metaRow1->website_id,
            'Error, all meta rows in this test must has theirs fields "website_id" at null because there are no website'
        );

        $this->assertNull(
            $metaRow1->language_id,
            'Error, all meta rows in this test must has theirs fields "language_id" at null because secondModel is not '
                .' translatable'
        );
    }
}
