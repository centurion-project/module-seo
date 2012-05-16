<?php
/**
 * @class Seo_Traits_Form_Model_Interface
 * Trait to implements for Model Form to add automatically fields to manage meta for each business rows
 *
 * @package Seo
 * @author Richard DELOGE, rd@octaveoctave.com
 * @copyright Octave & Octave
 */
class Seo_Traits_Form_Model
        extends Centurion_Traits_Form_Model_Abstract{

    /**
     * Prefix to use to name fields used to edit meta for the current instance
     */
    const FORM_META_FIELDS_PREFIX = 'meta_';

    /**
     * Name of the display group to group all fields to edit meta
     */
    const FORM_META_DISPLAYGROUP_NAME = 'meta_display_group';

    /**
     * List of meta to save into the row instance
     * @var array
     */
    protected $_metaList = array();

    /**
     * Connect this trait on some event of the current Model Form to add fields to edit metas of this rows
     * and intercept form response to save metas
     */
    public function init(){
        Centurion_Signal::factory('post_generate')->connect(
            array( $this, 'postGenerate' ),
            $this->_form
        );

        Centurion_Signal::factory('on_populate_with_instance')->connect(
            array($this, 'populateWithInstance'),
            $this->_form
        );

        Centurion_Signal::factory('post_form_pre_validate')->connect(
            array($this, 'prePopulate'),
            $this->_form
        );

        Centurion_Signal::factory('on_form_validation')->connect(
            array($this, 'prePopulate'),
            $this->_form
        );

        Centurion_Signal::factory('post_save')->connect(
            array($this, 'postSave'),
            $this->_form
        );
    }

    /**
     * Called when the form is builded to add fields to edit meta for the form's instance
     * @param Centurion_Signal_Abstract $signal
     * @param Centurion_Form_Model_Abstract $sender
     */
    public function postGenerate($signal, $sender){
        if(!($this->_form->getModel() instanceof Seo_Traits_Model_DbTable_Interface)){
            return;
        }

        $fieldsList = array();
        foreach($this->_form->getModel()->getDefinedMeta() as $type){
            if(null != $this->_form->getElement(self::FORM_META_FIELDS_PREFIX.$type)){
                continue;
            }

            //Add a element for each meta type
            $this->_form->addElement(
                    'textarea',
                    self::FORM_META_FIELDS_PREFIX.$type,
                    array(
                        'label' => $this->_translate($type)
                    )
                );

            $fieldsList[] = self::FORM_META_FIELDS_PREFIX.$type;
        }

        if(count($fieldsList) > 0){
            //Regroup all meta elements into a same display group
            $this->_form->addDisplayGroup($fieldsList, self::FORM_META_DISPLAYGROUP_NAME);
        }
    }

    /**
     * Called when the current form is instancing by a existent row to load also its meta
     * @param Centurion_Signal_Abstract $signal
     * @param Centurion_Form_Model_Abstract $sender
     */
    public function populateWithInstance($signal, $sender){
        if(!($this->_form->getModel() instanceof Seo_Traits_Model_DbTable_Interface)){
            return;
        }

        $formInstance = $this->_form->getInstance();
        if($formInstance instanceof Seo_Traits_Model_DbTable_Row_Interface){
            //Get meta rowset of the current form instance
            $metaRowset = $formInstance->getMetaRowset();

            foreach($metaRowset as $metaRow){
                //And populate each meta element with the value defined in the meta row
                if(null != ($element = $this->_form->getElement(self::FORM_META_FIELDS_PREFIX.$metaRow->type))){
                    $element->setValue($metaRow->content);
                }
            }
        }
    }

    /**
     * Called when the user post a form to retrieve meta values and save it later
     * @param Centurion_Signal_Abstract $signal
     * @param Centurion_Form_Model_Abstract $sender
     * @param array $values
     */
    public function prePopulate($signal, $sender, $values){
        if(!($this->_form->getModel() instanceof Seo_Traits_Model_DbTable_Interface)){
            return;
        }

        $this->_metaList = array();

        foreach($this->_form->getModel()->getDefinedMeta() as $type){
            if(isset($values[self::FORM_META_FIELDS_PREFIX.$type])){
                $this->_metaList[$type] = $values[self::FORM_META_FIELDS_PREFIX.$type];
            }
        }
    }

    /**
     * Build list of meta entries for the current entry
     * @param Centurion_Signal_Abstract $signal
     * @param Centurion_Form_Model_Abstract $sender
     */
    public function postSave($signal, $sender){
        if(!($this->_form->getModel() instanceof Seo_Traits_Model_DbTable_Interface)){
            return;
        }

        $formInstance = $this->_form->getInstance();
        if($formInstance instanceof Seo_Traits_Model_DbTable_Row_Interface){
            $formInstance->saveMetas($this->_metaList);
        }
    }
}