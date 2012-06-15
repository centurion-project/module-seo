<?php
/**
 * @class Seo_Controller_Action_Helper_SetupMeta
 * Helper to add to the view all meta sended by a row
 *
 * @package Seo
 * @author Richard DELOGE, rd@octaveoctave.com
 * @copyright Octave & Octave
 */
class Seo_Controller_Action_Helper_SetupMeta
        extends Zend_Controller_Action_Helper_Abstract{

    /**
     * List of meta to add into the view at next postDispatch
     * @var Seo_Model_DbTable_Row_Meta[]
     */
    protected $_metaRowset = array();

    /**
     *
     */
    public function init(){
        parent::init();

        //To keep the defined main object to populate metas in the view from objects'metas
        Centurion_Signal::factory('on_defining_main_object')->connect(
            array($this, 'setupMetaForRows'),
            $this->getActionController()
        );
    }

    /**
     * Populate meta to the view
     */
    public function postDispatch(){
        //Copy locally the rowset ...
        $metaRowset = $this->_metaRowset;

        //... and clean the stack to not repopulate at each subview or forward
        $this->_metaRowset = array();

        $view = $this->getActionController()->view;
        foreach ($metaRowset as $metaRow){
            $view->headMeta($metaRow->content, $metaRow->type);
        }

        parent::postDispatch();
    }

    /**
     * To keep the defined main object to populate metas in the view from objects'metas
     *
     * @param Centurion_Signal_OnDefiningMainObject $signal
     * @param Centurion_Controller_Action $sender
     * @param mixed $object
     */
    public function setupMetaForRows($signal, $sender, $object){
        if ($object instanceof Seo_Traits_Model_DbTable_Row_Interface){
            foreach ($object->getMetaRowset() as $metaRow){
                $this->_metaRowset[] = $metaRow;
            }
        }
    }
}
