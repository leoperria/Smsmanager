Ext.namespace("Application.contatti");
Application.contatti.WinContatti = function(){
  this.init({
    winConfig:{title:'Gestione contatti',width:650},
    deleteUrl:'contatti/delete'
  });
};

Ext.extend(Application.contatti.WinContatti, Application.apiGrid.WinList, {

  getStoreConfig:function(){

    return new Ext.data.JsonStore({
      url: 'contatti/list',
      root:'results',
      totalProperty:'totalCount',
      id:'c_ID',
      fields:[
        {name:'c_ID',type:'int'},
        {name:'c_data_creazione',type:'date',dateFormat:'Y-m-d H:i:s'},
        {name:'c_nome'},
        {name:'c_cognome'},
        {name:'c_telefono'},
        {name:'c_indirizzo'},
        {name:'c_localita'},
        {name:'c_cap'},
        {name:'c_ID_provincia'},
        {name:'c_pagato'},
        {name:'g_descrizione'}
      ]
    });
  },
  
  getColumnConfig:function(){
    function renderIndirizzo(value, metaData, record, rowIndex, colIndex, store){
     return record.data.c_indirizzo+' '+record.data.c_cap+' '+record.data.c_localita+' ('+record.data.c_ID_provincia+')';
    }
    return [
        { header:'Gruppo', width:90, dataIndex:'g_descrizione'},
        { header:'Nome', width:160, dataIndex:'c_nome'},
        { header:'Cognome', width:100, dataIndex:'c_cognome'},
        { header:'Telefono', width:100, dataIndex:'c_telefono'}
      ]
  },
  
  edit:function(id){
	if(this.gridPanel.getSelectionModel().getSelected()){
	  if(this.gridPanel.getSelectionModel().getSelected().data.c_pagato==1){
		  Ext.Msg.alert("Attenzione","Non si possono modificare i contatti gi&agrave; pagati");
          return
	  }
	}
    this.editWin=new Application.contatti.WinEdit(0);
    this.editWin.show(id);
    this.editWin.on('updated',function(){this.refreshPanel();},this);
  }
});