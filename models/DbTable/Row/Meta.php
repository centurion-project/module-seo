<?php
    /**
     * @class Seo_Model_DbTable_Meta
     * Class model to manage meta entries for each business data row in the project
     *
     * @package Seo
     * @author Richard DELOGE, rd@octaveoctave.com
     * @copyright Octave & Octave
     */
class Seo_Model_DbTable_Row_Meta
        extends Centurion_Db_Table_Row_Abstract{

    public function __toString(){
        return implode('-', array($this->id, $this->website_id, $this->model_id, $this->record_id, $this->type));
    }
}