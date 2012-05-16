<?php
/**
 * Author : Richard Déloge, rd@octaveoctave.com
 */
require_once dirname(__FILE__) . '/../../../../../../tests/TestHelper.php';

/**
 * @class Seo_Test_Traits_Form_ModelTest
 * @package Tests
 * @subpackage Seo
 * @author Richard Déloge, rd@octaveoctave.com
 *
 * Test the behavior of Form to manage SEOtable records
 */
class Seo_Test_Traits_Form_ModelTest
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
     * Test if the trait add fields into the form to manage Meta fields for this record
     */
    public function testPresenceOfSeoMetaFieldsInFormAfterFormGeneration(){
        //Change the local configuration for next tests
        Centurion_Config_Manager::set('seo.meta.types', array('description', 'keywords'));

        $form = new Seotable_Form_Model_First();

        $elements = $form->getElements();
        $this->assertArrayHasKey(
                Seo_Traits_Form_Model::FORM_META_FIELDS_PREFIX.'description',
                $elements,
                'Error, the trait Seo was not add the element "description" for the Form'
            );

        $this->assertArrayHasKey(
                Seo_Traits_Form_Model::FORM_META_FIELDS_PREFIX.'keywords',
                $elements,
                'Error, the trait Seo was not add the element "keywords" for the Form'
            );

        $this->assertInstanceOf(
                'Zend_Form_Element_TextArea',
                $elements[Seo_Traits_Form_Model::FORM_META_FIELDS_PREFIX.'description'],
                'Error, the type of all Meta Fields in the form must be a textarea. It is not the case for "description"'
            );

        $this->assertInstanceOf(
                'Zend_Form_Element_TextArea',
                $elements[Seo_Traits_Form_Model::FORM_META_FIELDS_PREFIX.'keywords'],
                'Error, the type of all Meta Fields in the form must be a textarea. It is not the case for "keywords"'
            );

        $displayGroups = $form->getDisplayGroups();

        $this->assertArrayHasKey(
            Seo_Traits_Form_Model::FORM_META_DISPLAYGROUP_NAME,
            $displayGroups,
            'Error, the trait Seo was not create the display group "meta_display_group" to store Metas Fields'
        );

        $dgElements = $displayGroups[Seo_Traits_Form_Model::FORM_META_DISPLAYGROUP_NAME]->getElements();
        $this->assertEquals(
                2,
                count($dgElements),
                'Error, the displaygroup to store Metas Fields must contains only fields for meta (aka description and keywords)'
            );

        $this->assertArrayHasKey(
                Seo_Traits_Form_Model::FORM_META_FIELDS_PREFIX.'description',
                $dgElements,
                'Error, the trait Seo was not add the element "description" for the Form'
            );

        $this->assertArrayHasKey(
                Seo_Traits_Form_Model::FORM_META_FIELDS_PREFIX.'keywords',
                $dgElements,
                'Error, the trait Seo was not add the element "keywords" for the Form'
            );
    }

    /**
     * Check if the trait keeps the list of meta types available for a model if this model customize this list
     */
    public function testPresenceOfSeoMetaFieldsInFormAfterFormGenerationWithCustomizedModel(){
        //Change the local configuration for next tests
        Centurion_Config_Manager::set('seo.meta.types', array('description', 'keywords'));

        $form = new Seotable_Form_Model_Custo();

        $elements = $form->getElements();

        $this->assertArrayHasKey(
            Seo_Traits_Form_Model::FORM_META_FIELDS_PREFIX.'keywords',
            $elements,
            'Error, the trait Seo was not add the element "keywords" for the Form'
        );

        $this->assertInstanceOf(
            'Zend_Form_Element_TextArea',
            $elements[Seo_Traits_Form_Model::FORM_META_FIELDS_PREFIX.'keywords'],
            'Error, the type of all Meta Fields in the form must be a textarea. It is not the case for "keywords"'
        );

        $displayGroups = $form->getDisplayGroups();

        $this->assertArrayHasKey(
            Seo_Traits_Form_Model::FORM_META_DISPLAYGROUP_NAME,
            $displayGroups,
            'Error, the trait Seo was not create the display group "meta_display_group" to store Metas Fields'
        );

        $dgElements = $displayGroups[Seo_Traits_Form_Model::FORM_META_DISPLAYGROUP_NAME]->getElements();
        $this->assertEquals(
            1,
            count($dgElements),
            'Error, the displaygroup to store Metas Fields must contains only fields for meta (aka keywords)'
        );

        $this->assertArrayHasKey(
            Seo_Traits_Form_Model::FORM_META_FIELDS_PREFIX.'keywords',
            $dgElements,
            'Error, the trait Seo was not add the element "keywords" for the Form'
        );
    }

    /**
     * Check if the trait populate SEO fields with current SEO Values of the current record
     */
    public function testExistentValuesOfSeoMetaFieldsInFormWithObjectInstance(){

        //Change the local configuration for next tests
        Centurion_Config_Manager::set('seo.meta.types', array('description', 'keywords'));

        $form = new Seotable_Form_Model_First();

        //Load instance
        $form->setInstance($form->getModel()->get(array('id' => 1)));

        $values = $form->getValues(true);

        $this->assertArrayHasKey(
                Seo_Traits_Form_Model::FORM_META_FIELDS_PREFIX.'description',
                $values,
                'Error, the trait Seo was not add the element "description" for the Form'
            );

        $this->assertArrayHasKey(
                Seo_Traits_Form_Model::FORM_META_FIELDS_PREFIX.'keywords',
                $values,
                'Error, the trait Seo was not add the element "keywords" for the Form'
            );

        $this->assertEquals(
                'meta description first 1',
                $values[Seo_Traits_Form_Model::FORM_META_FIELDS_PREFIX.'description'],
                'Error, the trait Seo was not loaded the good value for meta for the intance seotable/first_model:id:1'
            );

        $this->assertEquals(
                'meta keywords first 1',
                $values[Seo_Traits_Form_Model::FORM_META_FIELDS_PREFIX.'keywords'],
                'Error, the trait Seo was not loaded the good value for meta for the intance seotable/first_model:id:1'
            );
    }

    /**
     * Test if the trait runs again even if SEO fields are empty
     */
    public function testSavingOfMetaWhenMetaFieldsAreEmpty(){

        //Change the local configuration for next tests
        Centurion_Config_Manager::set('seo.meta.types', array('description', 'keywords'));

        $form = new Seotable_Form_Model_First();

        //Load instance
        $form->setInstance($form->getModel()->get(array('id' => 2)));

        //Simulate editing from user
        $form->isValid($form->getValues(true));
        $form->save();

        //Check result
        $metasRowset = $form->getInstance()->getMetaRowset();

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
    }

    /**
     * Test if the trait runs again even if SEO fields are defined by the webmaster
     */
    public function testUpdatingMetaWithCustomizedValueForNewInstance(){
        //Change the local configuration for next tests
        Centurion_Config_Manager::set('seo.meta.types', array('description', 'keywords'));

        $form = new Seotable_Form_Model_First();

        //Simulate editing from user
        $values = array(
            'title' => 'new title',
            'body'  => 'new body',
            'chapo' => 'new chapo',
            Seo_Traits_Form_Model::FORM_META_FIELDS_PREFIX.'description' => 'new desc',
        );

        Centurion_Signal::factory('post_form_pre_validate')->send($form, array($values));
        $form->isValid($values);
        $form->save();

        //Check result
        $metasRowset = $form->getInstance()->getMetaRowset();

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
            'new desc',
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
            'new chapo',
            $metaRow2->content,
            'Error, the meta row for the keywords has not the good value'
        );
    }

    /**
     * Test if the trait runs again even if SEO fields are defined by the webmaster for an existant row
     */
    public function testUpdateMetaWithCustomizedValueForExistantInstance(){
        //Change the local configuration for next tests
        Centurion_Config_Manager::set('seo.meta.types', array('description', 'keywords'));

        $form = new Seotable_Form_Model_First();

        //Simulate editing from user
        $values = array(
            'title' => 'new title',
            'body'  => 'new body',
            'chapo' => 'new chapo',
            Seo_Traits_Form_Model::FORM_META_FIELDS_PREFIX.'description' => 'new desc',
        );

        $form->setInstance($form->getModel()->get(array('id' => 2)));

        //Simulate editing from user
        $values = $form->getValues(true);
        $values[Seo_Traits_Form_Model::FORM_META_FIELDS_PREFIX.'description'] = 'new desc';

        $form->isValid($values);
        $form->save();

        //Check result
        $metasRowset = $form->getInstance()->getMetaRowset();

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
            'new desc',
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
    }
}