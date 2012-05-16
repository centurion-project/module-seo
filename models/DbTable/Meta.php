<?php
/**
 * @class Seo_Model_DbTable_Meta
 * Class model to manage meta entries for each business data row in the project
 *
 * @package Seo
 * @author Richard DELOGE, rd@octaveoctave.com
 * @copyright Octave & Octave
 */
class Seo_Model_DbTable_Meta
        extends Centurion_Db_Table_Abstract{

    protected $_name = 'seo_meta';

    protected $_primary = 'id';

    protected $_meta = array(	'verboseName'   => 'seo_meta',
                                'verbosePlural' => 'seo_metas');

    protected $_rowClass = 'Seo_Model_DbTable_Row_Meta';

    /**
     * Get current website adapter for this module
     * @var Seo_Model_Website_Interface|null
     */
    protected static $_websiteAdapter = null;

    protected $_referenceMap = array(
        'model' => array(
            'columns'       => 'model_id',
            'refColumns'    => 'id',
            'refTableClass' => 'Core_Model_DbTable_ContentType',
            'onDelete'      => self::CASCADE
        ),
        'language' => array(
            'columns'       => 'language_id',
            'refColumns'    => 'id',
            'refTableClass' => 'Translation_Model_DbTable_Language',
            'onDelete'      => self::CASCADE
        ),
    );

    /**
     * @static
     * Register into this module the website adapter to use for all requests in the DBMS for this module
     * @param Seo_Model_Website_Interface $adapter
     */
    public static function registerWebsiteAdapter(Seo_Model_Website_Interface $adapter=null){
        self::$_websiteAdapter = $adapter;
    }

    /**
     * @static
     * Return the current website adapter to use for all request in the DBMS for the module
     * @return null|Seo_Model_Website_Interface
     */
    public static function getWebsiteAdapter(){
        return self::$_websiteAdapter;
    }
}