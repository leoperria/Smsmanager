Ext.namespace("Application.ricariche");
Application.ricariche.WinRicariche = function(id){
  this.orgid=id;
  this.init({
    winConfig:{title:'Gestione ricariche',width:600},
    deleteUrl:'ricariche/delete',
    rowdoubleclick:false
  });
};

Ext.extend(Application.ricariche.WinRicariche, Application.apiGrid.WinList, {
  buildMenu:function(){
	return [{
        text:'Aggiungi',
        icon:'css/icons/add.png',
        iconCls:'x-btn-text-icon',
        tooltip:{title:'Aggiungi',text:'Aggiungi'},
        handler: function(){
          this.edit(this.orgid,'new');
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
          this.edit(this.orgid,this.gridPanel.getSelectionModel().getSelected().id);
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
  
  getStoreConfig:function(){

    return new Ext.data.JsonStore({
      url: 'ricariche/list',
      baseParams:{
    	orgid:this.orgid
      },
      root:'results',
      totalProperty:'totalCount',
      id:'r_ID',
      fields:[
        {name:'r_ID',type:'int'},
        {name:'r_data_ricarica',type:'date',dateFormat:'Y-m-d H:i:s'},
        {name:'r_orgid',type:'int'},
        {name:'r_numero_sms',type:'int'},
        {name:'r_importo',type:'float'}
      ]
    });
  },
  
  getColumnConfig:function(){
    
    return [
        { header:'Data ricarica', width:100, dataIndex:'r_data_ricarica',renderer:Utils.utils.dateRenderer},
        { header:'SMS', width:120, dataIndex:'r_numero_sms'},
        { header:'Importo', width:100, dataIndex:'r_importo',renderer:Utils.utils.euroRender}
      ]
  },
  
  edit:function(orgid,id){
	this.editWin=new Application.ricariche.WinEdit(orgid);
    this.editWin.show(id);
    this.editWin.on('updated',function(){this.refreshPanel();},this);
  }
});