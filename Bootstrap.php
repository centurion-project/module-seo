<?php
/**
 * @class Seo_Bootstrap
 * Bootstrap for the seo module.
 *  - To register only the action helper "SetupMeta"
 *
 * @package Seo
 * @author Richard DELOGE, rd@octaveoctave.com
 * @copyright Octave & Octave
 */
class Seo_Bootstrap
        extends Centurion_Application_Module_Bootstrap{

    protected function _initHelper(){
        $this->bootstrap('FrontController');

        //To allow trait to add meta into the view when there are sended by a SEOtable Row
        Zend_Controller_Action_HelperBroker::addHelper(new Seo_Controller_Action_Helper_SetupMeta());
    }

}
