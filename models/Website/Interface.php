<?php
/**
 * @class Seo_Model_Website_Interface
 * Interface to build a adapter for this module SEO to allows developer to use this module with
 * projects whom needs a management of multisite.
 *
 * @package Seo
 * @author Richard DELOGE, rd@octaveoctave.com
 * @copyright Octave & Octave
 */
interface Seo_Model_Website_Interface{
    /**
     * @abstract
     * Return the id of the current website to allow developers to use this module in a project
     * with a multisite constraint. If the project has no a multisite, developer can not implements a website adapter
     * for this SEO module
     * @return int
     */
    public function getWebsiteId();
}