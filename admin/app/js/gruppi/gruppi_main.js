Ext.namespace("Application.gruppi");
Application.gruppi.WinGruppi = function(id){
  this.orgid=id;
  this.init({
    winConfig:{title:'Gestione gruppi',width:400},
    deleteUrl:'gruppi/delete/orgid/'+this.orgid,
    rowdoubleclick:false
  });
};

Ext.extend(Application.gruppi.WinGruppi, Application.apiGrid.WinList, {
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
      url: 'gruppi/list',
      baseParams:{
    	orgid:this.orgid
      },
      root:'results',
      totalProperty:'totalCount',
      id:'g_ID_gruppo',
      fields:[
        {name:'g_ID_gruppo',type:'int'},
        {name:'g_orgid',type:'int'},
        {name:'g_descrizione'}
      ]
    });
  },
  
  getColumnConfig:function(){
    return [{ header:'Nome gruppo', width:380, dataIndex:'g_descrizione'}];
  },
  
  edit:function(orgid,id){
	this.editWin=new Application.gruppi.WinEdit(orgid);
    this.editWin.show(id);
    this.editWin.on('updated',function(){this.refreshPanel();},this);
  }
});