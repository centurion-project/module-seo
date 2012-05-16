<?php
/**
 * @class Seo_Traits_Model_DbTable_Row_Interface
 * Trait to implements for each row instance to build and setup metas
 *
 * @package Seo
 * @author Richard DELOGE, rd@octaveoctave.com
 * @copyright Octave & Octave
 */
class Seo_Traits_Model_DbTable_Row
        extends Centurion_Traits_Model_DbTable_Row_Abstract{

    /**
     * Build the list of params used in request to filter result, or to create or update a row
     * @return array
     */
    protected function _buildParamsFilters(){
        //Get the website adapter if the developer has defined one for this project
        $websiteAdapter = Seo_Model_DbTable_Meta::getWebsiteAdapter();

        //Prepare filters to select only meta for this row
        $_params = array(
            'record_id' => sha1(serialize($this->_row->pk)),
            'model_id'  => Centurion_Db::getSingleton('core/content_type')->getContentTypeIdOf($this->_row)
        );

        //If the row it is a translatable row, select for the current language
        if($this->_row instanceof Translation_Traits_Model_DbTable_Row_Interface
            || !empty($this->_row->{Translation_Traits_Model_DbTable::LANGUAGE_FIELD})){
            $_params['language_id'] = $this->_row->{Translation_Traits_Model_DbTable::LANGUAGE_FIELD};
        }

        //If we are in a multisite context
        if(null !== $websiteAdapter){
            $_params['website_id'] = $websiteAdapter->getWebsiteId();
        }

        return $_params;
    }

    /**
     * Return the rowset of meta defined for this row
     * @return Zend_Db_Table_Rowset_Abstract
     */
    protected function _listMeta(){
        $_params = $this->_buildParamsFilters();

        //Get all metas defined for this row
        return Centurion_Db::getSingleton('seo/meta')
                    ->getCache()
                    ->filter($_params);

    }

    /**
     * Method to create or update a meta for this current row.
     * Developper must pass to this row all params to get or create the row
     * @param array &$metaSpec list of spec of the current model to write automatically meta when there are empty
     * @param array $params to retrieve the row
     * @param string $type of the meta (keywords, descriptions, etc...)
     * @param string $content of the meta
     * @return false|Seo_Model_DbTable_Row_Meta (false if the content is empty, else, return the updated row)
     */
    protected function _saveMetaRow(array &$metaSpec, array $params, $type, $content = null){
        $m_seoContent = Centurion_Db::getSingleton('seo/meta');

        if(!empty($metaSpec[$type])
            && empty($content)){
            //Generate automatically the content of the meta row if it is empty

            $content = array();
            foreach($metaSpec[$type] as $key=>$value){
                $fieldName  = null; //Field of the row to use
                $modifier   = null; //Method to call to alter the field value to generate the meta

                if(!is_numeric($key)
                    && is_callable($value)){
                    //Developper want alter the value before to use it into the meta
                    $fieldName = $key;
                    $modifier = $value;
                }
                else{
                    //no modifier, use the value directly
                    $fieldName = $value;
                }

                if(!empty($this->_row->{$fieldName})){
                    //If the value exist, add it into content
                    if(null == $modifier){
                        $content[] = $this->_row->{$fieldName};
                    }
                    else{
                        $content[] = call_user_func_array($modifier, array($this->_row->{$fieldName}));
                    }
                }
            }

            $content = implode(', ', $content);
        }

        if(null !== $content){
            //Get the row for this meta (or create it if it's not exist)
            $params['type'] = $type;
            list($row,) = $m_seoContent->getOrCreate($params);

            if(null !== $row){
                //Update the row
                $row->content = $content;
                $row->save();

                return $row;
            }
        }

        //The content is empty
        return false;
    }

    /**
     * Return a rowset of meta defined for the current row
     * Warning, this method return an array  of Seo_Model_DbTable_Row_Meta and not a Centurion_Db_Table_Rowset object
     * @return Seo_Model_DbTable_Row_Meta[]
     */
    public function getMetaRowset(){
        $rowSet = array();

        $metas = $this->_listMeta();

        $listOfMetaType = $this->_row->getTable()->getDefinedMeta();

        //And register them
        foreach($metas as $meta){
            $rowSet[] = $meta;

            //Remove the current meta type of the list of required type
            $listOfMetaType = array_diff($listOfMetaType, array($meta->type));
        }

        //Create automatically all missing meta type
        if(count($listOfMetaType) > 0){
            //Get list of fields to use to generate automatically meta fields
            $metaSpec = $this->_row->getTable()->getFieldsToGenerateMeta();

            $params = $this->_buildParamsFilters();

            foreach($listOfMetaType as $type){
                //Create all new row meta
                $_newMetaRow = $this->_saveMetaRow($metaSpec, $params, $type);
                if(false !== $_newMetaRow){
                    $rowSet[] = $_newMetaRow;
                }
            }
        }

        return $rowSet;
    }

    /**
     * Method to save several metas for the current instance.
     * If a meta is not defined in the platform, it will be skipped
     * @param array $metas : list of meta to save
     */
    public function saveMetas(array $metas){
        //Get the list of required meta
        $listOfMetaType = $this->_row->getTable()->getDefinedMeta();

        //Prepare params to save meta
        $params = $this->_buildParamsFilters();

        //Get list of fields to use to generate automatically meta fields
        $metaSpec = $this->_row->getTable()->getFieldsToGenerateMeta();

        //Check if the set of meta is valid
        foreach($metas as $name=>$meta){
            if(!is_string($meta)){
                throw new Exception(sprintf(
                        'Error, the meta %s is not a string',
                        $name
                    )
                );
            }
        }

        foreach($metas as $name=>$meta){
            if(in_array($name, $listOfMetaType)){
                //If the meta is allowed
                $this->_saveMetaRow($metaSpec, $params, $name, $meta);
            }
        }

        Centurion_Db::getSingleton('seo/meta')
            ->getCache()
            ->clean();
    }
}