<?php

/**
 * Movimenti
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
class Movimenti extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('movimenti');
        $this->hasColumn('ID', 'integer', 4, array('type' => 'integer', 'length' => 4, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('orgid', 'integer', 4, array('type' => 'integer', 'length' => 4, 'default' => '', 'notnull' => true));
        $this->hasColumn('ID_ricarica', 'integer', 4, array('type' => 'integer', 'length' => 4));
        $this->hasColumn('ID_campagna', 'integer', 4, array('type' => 'integer', 'length' => 4));
        $this->hasColumn('data_movimento', 'timestamp', null, array('type' => 'timestamp', 'notnull' => true));
        $this->hasColumn('qnt', 'integer', 4, array('type' => 'integer', 'length' => 4, 'default' => '', 'notnull' => true));
    }
    
    public function setUp(){
     $this->hasOne('Organizations',array("local"=>"orgid","foreign"=>"orgid"));
     $this->hasOne('Ricariche',array('local'=>'ID_ricarica','foreign'=>'ID'));
    }

}