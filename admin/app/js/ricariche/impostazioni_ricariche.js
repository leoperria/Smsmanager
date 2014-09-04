Ext.namespace("Application.ricariche");
Application.ricariche.WinImpostazioniRicariche = function(id){
  this.orgid=id;
  this.init({
    winConfig:{title:'Impostazioni costi ricariche',width:300},
    deleteUrl:'ricariche/deletecosto'    
  });
};

Ext.extend(Application.ricariche.WinImpostazioniRicariche, Application.apiGrid.WinList, {
  
  getStoreConfig:function(){

    return new Ext.data.JsonStore({
      url: 'ricariche/costiricariche',
      baseParams:{
    	orgid:this.orgid
      },
      root:'results',
      totalProperty:'totalCount',
      id:'i_ID',
      fields:[
        {name:'i_ID',type:'int'},
        {name:'i_orgid',type:'int'},
        {name:'i_numero_sms',type:'int'},
        {name:'i_importo',type:'float'}
      ]
    });
  },
  
  getColumnConfig:function(){
    
    return [
        { header:'SMS', width:120, dataIndex:'i_numero_sms'},
        { header:'Importo', width:100, dataIndex:'i_importo',renderer:Utils.utils.euroRender}
      ];
  },
  
  edit:function(id){
	this.editWin=new Application.ricariche.WinEditCostoRicarica(this.orgid);
    this.editWin.show(id);
    this.editWin.on('updated',function(){this.refreshPanel();},this);
  }
});