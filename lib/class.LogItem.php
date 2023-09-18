<?php
class LogItem
{

 private $_data = array('id'=>null,'name'=>null,'type'=>null,'description'=>null,'file'=>null,'line'=>null,'created'=>null);

 public function __get($key)
 {
    switch( $key ) {
        case 'id':
        case 'name':
        case 'type':
        case 'line':
        case 'file':
        case 'description':
        case 'created':
        return $this->_data[$key];
    }
 }
 public function __set($key,$val)
 {
    switch( $key ) {
        case 'name':
        case 'type':
        case 'file':
        case 'description':
            $this->_data[$key] = trim($val);
        break;
        case 'line':
            $this->_data[$key] = (int) $val;
        break;
    }
 }
 public function save()
 {
    if( !$this->is_valid() ) return FALSE;
    if( $this->id > 0 ) {
        $this->update();
    } else {
        $this->insert();
    }
 }

 public function is_valid()
 {
    if( !$this->name ) return false;
    return TRUE;
 }
 protected function insert()
 {
    $db = \cms_utils::get_db();
    $sql = 'INSERT INTO '.CMS_DB_PREFIX.'module_AWS_S3_Uploads (name,type,file,line,description,created)VALUES (?,?,?,?,?,?)';
    $dbr = $db->Execute($sql,array($this->name,$this->type,$this->file,$this->line,$this->description, date('Y-m-d H:i:s')));
    if( !$dbr ) return FALSE;
    $this->_data['id'] = $db->Insert_ID();
    return TRUE;
 }
 protected function update()
 {
    $db = \cms_utils::get_db();
    $sql = 'UPDATE '.CMS_DB_PREFIX.'module_AWS_S3_Uploads SET name = ?, type = ?, file= ?, line = ?, description = ? WHERE id = ?';
    $dbr = $db->Execute($sql,array($this->name,$this->type,$this->file,$this->line,$this->description,$this->id));
    if( !$dbr ) return FALSE;
        return TRUE;
 }
 public function delete()
 {
    if( !$this->id ) return FALSE;
    $db = \cms_utils::get_db();
    $sql = 'DELETE FROM '.CMS_DB_PREFIX.'module_AWS_S3_Uploads WHERE id = ?';
    $dbr = $db->Execute($sql,array($this->id));
    if( !$dbr ) return FALSE;
    $this->_data['id'] = null;
    return TRUE;
 }
 /** internal */
 public function fill_from_array($row)
 {
    foreach( $row as $key => $val ) {
        if( array_key_exists($key,$this->_data) ) {
            $this->_data[$key] = $val;
        }
    }
 }

 public static function &load_by_id($id)
 {
    $id = (int) $id;
    $db = \cms_utils::get_db();
    $sql = 'SELECT * FROM '.CMS_DB_PREFIX.'module_AWS_S3_Uploads WHERE id = ?';
    $row = $db->GetRow($sql,array($id));
    if( is_array($row) ) {
        $obj = new self();
        $obj->fill_from_array($row);
        return $obj;
    }
 }
}
?>