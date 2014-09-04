Ext.namespace("Application.organizations");
Application.organizations.WinOrganizations = function(){
  
  this.rowAction=new Ext.ux.grid.RowActions({
      actions:[{
        iconCls:'icon-edit',
        qtip:'Modifica'
      },{
        iconCls:'icon-delete',
        qtip:'Elimina'
      }],
        widthIntercept:Ext.isSafari ? 4 : 2,
        id:'actions'
    });
    
    this.rowAction.on('action',function(grid, record, action, row, col){
      switch(action) {
        case 'icon-edit':
          console.log(record.id);
          this.edit(record.id);
        break;

		case 'icon-delete':
		  this.deleteRecord(record.id);
		break;
        
      }
    },this);
    
  this.init({
    winConfig:{title:'Gestione Aziende clienti',width:900},
    deleteUrl:'organizations/delete',
    gridConfig:{plugins:[this.rowAction]}
  });
};

Ext.extend(Application.organizations.WinOrganizations, Application.apiGrid.WinList, {

  getStoreConfig:function(){

    return new Ext.data.JsonStore({
      url: 'organizations/list',
      root:'results',
      totalProperty:'totalCount',
      id:'orgid',
      fields:[
        {name:'orgid',type:'int'},
        {name:'data_iscrizione',type:'date',dateFormat:'Y-m-d'},
        {name:'rag_soc'},
        {name:'sms_sender'},
        {name:'codfis'},
        {name:'p_iva'},
        {name:'tel'},
        {name:'balance'}
      ]
    });
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
      },'-',{
      text: 'Lista utenti',
      icon:'css/icons/group.png',
      iconCls:'x-btn-text-icon',
      handler: function(){
          if(!this.gridPanel.getSelectionModel().getSelected()){
            Ext.Msg.alert("Attenzione","Selezionare una riga della lista");
            return
          }
          var win=new Application.organizations.WinUsers(this.gridPanel.getSelectionModel().getSelected().id,this.gridPanel.getSelectionModel().getSelected().data.o_rag_soc);
          win.show();
          //this.hide();
      },
      scope: this
    },'-',{
     text:'Data-entry interni',
     icon:'css/icons/user.png',
     iconCls:'x-btn-text-icon',
     handler: function(){
         if(!this.gridPanel.getSelectionModel().getSelected()){
           Ext.Msg.alert("Attenzione","Selezionare una riga della lista");
           return
         }
         var win=new Application.organizations.InternalResourcesOrganizations();
         win.show(this.gridPanel.getSelectionModel().getSelected().id,this.gridPanel.getSelectionModel().getSelected().data.o_rag_soc);
         //this.hide();
     },
     scope: this
    },'-',{
	  text:'Campagne',
	  icon:'css/icons/folder_page.png',
      iconCls:'x-btn-text-icon',
      handler: function(){
        if(!this.gridPanel.getSelectionModel().getSelected()){
          Ext.Msg.alert("Attenzione","Selezionare una riga della lista");
          return
        }
        var win=new Application.campagne.WinList(this.gridPanel.getSelectionModel().getSelected().id);
        win.show();
        //this.hide();
      },
      scope: this
    },'-',{
      text: 'Costi ricariche',
      icon:'css/icons/money.png',
      iconCls:'x-btn-text-icon',
      handler: function(){
    	if(!this.gridPanel.getSelectionModel().getSelected()){
          Ext.Msg.alert("Attenzione","Selezionare una riga della lista");
          return
        }
        var win=new Application.ricariche.WinImpostazioniRicariche(this.gridPanel.getSelectionModel().getSelected().id);
        win.show();
      },
      scope: this
    },'-',{
	  text:'Ricariche',
	  icon:'css/icons/money_add.png',
      iconCls:'x-btn-text-icon',
      handler: function(){
        if(!this.gridPanel.getSelectionModel().getSelected()){
          Ext.Msg.alert("Attenzione","Selezionare una riga della lista");
          return
        }
        var win=new Application.ricariche.WinRicariche(this.gridPanel.getSelectionModel().getSelected().id);
        win.show();
        //this.hide();
      },
      scope: this
    },'-',{
	  text:'Contatti',
	  icon:'css/icons/vcard.png',
      iconCls:'x-btn-text-icon',
      handler: function(){
        if(!this.gridPanel.getSelectionModel().getSelected()){
          Ext.Msg.alert("Attenzione","Selezionare una riga della lista");
          return
        }
        var win=new Application.organizations.EntryWorkedAll(this.gridPanel.getSelectionModel().getSelected().id,this.gridPanel.getSelectionModel().getSelected().data.o_rag_soc);
        win.show();
        //this.hide();
      },
      scope: this
    },'-',{
      text:'Gruppi',
      icon:'css/icons/folder_user.png',
      iconCls:'x-btn-text-icon',
      handler: function(){
    	if(!this.gridPanel.getSelectionModel().getSelected()){
          Ext.Msg.alert("Attenzione","Selezionare una riga della lista");
          return
        }
        var win=new Application.gruppi.WinGruppi(this.gridPanel.getSelectionModel().getSelected().id);
        win.show();
      },
      scope: this
    },];
  },
  
  getColumnConfig:function(){
    
    return [
        { header:'Data iscrizione', width:100, dataIndex:'data_iscrizione',renderer:Utils.utils.dateRenderer},
        { header:'Ragione sociale', width:250, dataIndex:'rag_soc'},
        { header:'Sender',width:100,dataIndex:'sms_sender'},
      //  { header:'P. iva', width:120, dataIndex:'p_iva'},
//        { header:'Cod. Fis.', width:120, dataIndex:'codfis'},
//        { header:'Telefono', width:100, dataIndex:'tel'},
        { header:'Bilancio',width:70, dataIndex:'balance'},
        this.rowAction
      ]
  },
  
  edit:function(id){
    this.editWin=new Application.organizations.WinEdit();
    this.editWin.show(id);
    this.editWin.on('updated',function(){this.refreshPanel();},this);
  }
});