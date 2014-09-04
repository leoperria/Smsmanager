Ext.namespace("Application.apiGrid");
Application.apiGrid.WinList = function(config){
  this.init(config);
};

Ext.extend(Application.apiGrid.WinList, Ext.util.Observable, {

  win:null,
  gridPanel:null,
  editWin:null,
  rowactions:null,
  
  /************************************** INIT *************************************/
  
  init: function(config){
    this.config=config;
    
    this.buildGridPanel();
    
    this.win = new Ext.Window(
      Ext.apply({
        title: 'Generic List',
        iconCls: 'icon-shield',
        width: 460,
        height: 500,
        layout: 'border',
        plain:true,
        modal:false,
        border:false,
        constrainHeader:true,
        shim:false,
        animCollapse:false,
        buttonAlign:'right',
        closeAction:'hide',
        maximizable:true,
        items:[this.gridPanel],
        buttons:[{
          text:'Chiudi',
          handler:this.hide,
          scope:this
        }]
      },this.config.winConfig)
    );

  },
  
  show: function(){
    this.win.show();
    if(!this.config.stopRefreshOnLoad){
      this.refreshPanel();
    }
  },

  hide: function(){
    Ext.QuickTips.init();
    this.win.close();
  }, 
  
  buildMenu:function(){
    
    return [{
        text:'Aggiungi',
        icon:'css/icons/add.png',
        iconCls:'x-btn-text-icon',
        tooltip:{title:'Aggiungi',text:'Aggiungi'},
        handler: function(){
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
      }]
  },
  
  buildGridPanel: function(){
    
    this.store=this.getStoreConfig();
      
    this.bbar=new Ext.PagingToolbar({
      pageSize:15,
      displayInfo: true,
      displayMsg: '{2} righe trovate. Visualizzate {0} - {1}',
      emptyMsg: "nessun risultato.",
      store:this.store
    });
    
    
    this.gridPanel=new Ext.grid.GridPanel(
      Ext.apply({region:'center',
      store:this.store,
      columns:this.getColumnConfig(),
      tbar:this.buildMenu(),
      bbar:this.bbar
    },this.config.gridConfig));
    
    this.gridPanel.on('rowdblclick',function(){
      if(this.config.rowdoubleclick!==false){
    	  this.edit(this.gridPanel.getSelectionModel().getSelected().id);
      }
    },this);
    
  },
  getStoreConfig:function(){
    return null;
  },
  
  getColumnConfig:function(){
    return null;
  },
  
  refreshPanel:function(){
    
    this.gridPanel.getStore().load({
      params:{
        start:0,
        limit:this.bbar.pageSize      
      }
    });
  },
  
  edit:function(id){
    return null;
  },
  
  deleteRecord:function(id){
    var msg=(this.config.deleteMessage)? this.config.deleteMessage:'Si vuole eliminare il record selezionato dall\'archivio?';
    Ext.Msg.show({
      title:'Eliminazione ?',
      msg:msg,
      buttons: Ext.Msg.YESNO,
      fn:function(btn){
        if (btn=='yes'){
          Ext.Ajax.request({
            url:this.config.deleteUrl,
            params:{
              id:id
            },
            success:function(response,options){
              var result=Ext.decode(response.responseText);
              if (result.success==true){
                this.refreshPanel();
              }else{
		        if (result.errorMessages){
		          var errMsg=result.errorMessages.join("<br/>");
		          Ext.MessageBox.show({
			        title: 'Problema...',
			        msg: errMsg,
			        buttons: Ext.MessageBox.OK,
			        icon: Ext.MessageBox.WARNING
			      });
		        }
              }
            },
            scope:this
          });
        }
      },
      icon: Ext.MessageBox.QUESTION,
      scope:this
    });
  }
});