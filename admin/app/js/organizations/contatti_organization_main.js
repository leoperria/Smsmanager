Application.organizations.EntryWorkedAll = function(orgid,azienda){
  this.orgid=orgid;
  this.id=0;
  this.azienda=azienda;    
  this.init({winConfig:{title:'Contatti dell\'azienda: '+this.azienda,width:800},deleteUrl:'contatti/delete'});
};

Ext.extend(Application.organizations.EntryWorkedAll, Application.apiGrid.WinList, {

  getStoreConfig:function(){

    return new Ext.data.JsonStore({
      url: 'contatti/listbyorganization',
      baseParams:{
        orgid:this.orgid,
        id:this.id
      },
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
	      {name:'c_pagato'}
	    ]
    });
  },
  
  buildMenu:function(){
    return [{
        text:'Aggiungi',
        icon:'css/icons/add.png',
        iconCls:'x-btn-text-icon',
        tooltip:{title:'Aggiungi',text:'Aggiungi un nuovo contatto'},
        handler:function(){
          this.edit('new');
        },
        scope:this
      },'-',{
        text:'Modifica',
        icon:'css/icons/pencil.png',
        iconCls:'x-btn-text-icon',
        tooltip:{title:'Modifica',text:'Modifica'},
        handler:function(){
          if(!this.gridPanel.getSelectionModel().getSelected()){
            Ext.Msg.alert("Attenzione","Selezionare una riga della lista");
            return
          }
          this.edit(this.gridPanel.getSelectionModel().getSelected().id);
        },
        scope:this
      },'-',{
        text:'Elimina',
        icon:'css/icons/delete.png',
        iconCls:'x-btn-text-icon',
        tooltip:{title:'Elimina',text:'Elimina'},
        handler:function(){
          if(!this.gridPanel.getSelectionModel().getSelected()){
            Ext.Msg.alert("Attenzione","Selezionare una riga della lista");
            return
          }
          this.deleteRecord(this.gridPanel.getSelectionModel().getSelected().id);
        },
        scope:this
      }];
  },
  
  getColumnConfig:function(){
	  function renderIndirizzo(value, metaData, record, rowIndex, colIndex, store){
	        return record.data.c_indirizzo+' '+record.data.c_cap+' '+record.data.c_localita+' ('+record.data.c_ID_provincia+')';
	    }
    return [
        { header:'Data inserimento', width:90, dataIndex:'c_data_creazione',renderer:Utils.utils.dateRenderer},
        { header:'Nome', width:100, dataIndex:'c_nome'},
        { header:'Cognome', width:100, dataIndex:'c_cognome'},
        { header:'Telefono', width:100, dataIndex:'c_telefono'},
        { header:'Indirizzo', width:200,dataIndex:'c_indirizzo',renderer:renderIndirizzo}
      ];
  },
  
  edit:function(id){
	  if(this.gridPanel.getSelectionModel().getSelected()){
	  if(this.gridPanel.getSelectionModel().getSelected().data.c_pagato==1){
		  Ext.Msg.alert("Attenzione","Non si possono modificare i contatti gi&agrave; pagati");
          return
	  }
	}
    this.editWin=new Application.contatti.WinEdit(this.orgid);
    this.editWin.show(id);
    this.editWin.on('updated',function(){this.gridPanel.getStore().reload();},this);
  }
});