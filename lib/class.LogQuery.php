<?php
class LogQuery extends CmsDbQueryBase
{

    public function execute(){
        if( !is_null($this->_rs) ) return;
        $sql = 'SELECT SQL_CALC_FOUND_ROWS H.* FROM '.CMS_DB_PREFIX.'module_AWS_S3_Uploads H ORDER BY created DESC';
        $db = \cms_utils::get_db();
        $this->_rs = $db->SelectLimit($sql,$this->_limit,$this->_offset);
        if( $db->ErrorMsg() ) throw new \CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
        $this->_totalmatchingrows = $db->GetOne('SELECT FOUND_ROWS()');
    }

    public function &GetObject(){
        $obj = new LogItem;
        $obj->fill_from_array($this->fields);
        return $obj;
    }

}
?>