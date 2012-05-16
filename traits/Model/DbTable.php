<?php
/**
 * @class Seo_Traits_Model_DbTable
 * Interface to implements for each business model whome use this module
 *  to define fields to use to write automatically metas
 *
 * @package Seo
 * @author Richard DELOGE, rd@octaveoctave.com
 * @copyright Octave & Octave
 */
class Seo_Traits_Model_DbTable
        extends Centurion_Traits_Model_DbTable_Abstract{

    /**
     * Return the list of defined type meta for this plateform (like keywords, description, etc..).
     * These types are defined in the configuration at entry "seo.meta.types[]"
     *
     * Each model can overload this method to customize meta types available for these rows
     * @return string[]
     */
    public function getDefinedMeta(){
        //Get the list of meta to generate for this project
        return Centurion_Config_Manager::get('seo.meta.types', array());
    }
}