<?php
/**
 * @class Seo_Traits_Model_DbTable_Interface
 * Interface to implements for each business model whome use this module
 *  to define fields to use to write automatically metas
 *
 * @package Seo
 * @author Richard DELOGE, rd@octaveoctave.com
 * @copyright Octave & Octave
 */
interface Seo_Traits_Model_DbTable_Interface{
    const META_DESCRIPTION      = 'description';
    const META_KEYWORDS         = 'keywords';
    const META_CONTENT_TYPE     = 'Content-Type';
    const META_AUTHOR           = 'Author';
    const META_COPYRIGHT        = 'Copyright';
    const META_ROBOTS           = 'Robots';

    /**
     * Return the list of fields to use to generate a meta field.
     * Must return an array to list each field foreach meta type (keywords, description)
     * @abstract
     * @return array
     */
    public function getFieldsToGenerateMeta();
}