<?php
/**
 * @class Seo_Traits_Controller_CRUD_Interface
 * Trait to customize admin form view for SEOable elements to add display group for meta fields into a specific
 * grid main
 *
 * @package Seo
 * @author Richard DELOGE, rd@octaveoctave.com
 * @copyright Octave & Octave
 */
class Seo_Traits_Controller_CRUD
        extends Centurion_Traits_Controller_Abstract{

    public function init(){
        parent::init();

        //To add only this new group at end
        Centurion_Signal::factory('grid_pre_add_button')->connect(
            array($this, 'addMainGroupForMeta'),
            $this->_getForm()
        );
    }

    /**
     * Add the display group for Meta into a specific main
     * @param Centurion_Signal_Abstract $signal
     * @param Centurion_Form_Model_Abstract $sender
     */
    public function addMainGroupForMeta($signal, $sender){
        $form = $this->_getForm();
        if( $form instanceof Seo_Traits_Form_Model_Interface
            && null != $form->getDisplayGroup(Seo_Traits_Form_Model::FORM_META_DISPLAYGROUP_NAME)){

            //Create it
            $this->_controller->view->gridForm()->addMain($this->_getForm(), array(
                'label'         => $this->_controller->view->translate('SEO / Meta@backoffice'),
                'elements'      => array(Seo_Traits_Form_Model::FORM_META_DISPLAYGROUP_NAME)
            ));
        }
    }
}