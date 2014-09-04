<?php

class Areas extends Doctrine_Record{
  
  public function setTableDefinition()
    {
        $this->setTableName('areas');
        $this->hasColumn('ID', 'integer', 4, array('type' => 'integer', 'length' => 4, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 64, array('type' => 'string', 'length' => 64, 'notnull' => true));
        $this->hasColumn('UIname', 'string', 255, array('type' => 'string', 'length' => 255, 'notnull' => true));
    }
  
  public function setUp(){
    $this->hasMany('Permissions', array ('local' => 'ID', 'foreign' =>'ID_area',"onDelete"=>"CASCADE"));
  }
}