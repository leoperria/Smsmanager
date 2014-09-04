Ext.namespace("Application.dataentry");
Application.dataentry.WinDataEntry = function(){
  
  this.rowAction=new Ext.ux.grid.RowActions({
      actions:[{
        iconCls:'icon-edit',
        qtip:'Modifica'
      },{
        iconCls:'icon-key',
        qtip:'Modifica password'
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
        
        case 'icon-key':
          var editPasswordWin=new Application.users.WinEditPassword();
          editPasswordWin.show(record.id);
        break;
      }
    },this);
    
  this.init({
    winConfig:{title:'Gestione data-entry interni',width:600},
    deleteUrl:'usersadmin/deletedataentry',
    gridConfig:{plugins:[this.rowAction]}
  });
};

Ext.extend(Application.dataentry.WinDataEntry, Application.apiGrid.WinList, {

  getStoreConfig:function(){

    return new Ext.data.JsonStore({
      url: 'usersadmin/listdataentry',
      root:'results',
      totalProperty:'totalCount',
      id:'u_ID',
      fields:[
        {name:'u_ID',type:'int'},
        {name:'u_data_iscrizione',type:'date',dateFormat:'Y-m-d'},
        {name:'u_nome'},
        {name:'u_cognome'},
        {name:'u_user'},
        {name:'u_active',type:'int'}
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
       text:'Aziende',
       icon:'css/icons/chart_organisation.png',
       iconCls:'x-btn-text-icon',
       tooltip:{title:'Aziende',text:'Aziende per le quali attualmente lavora il dataentry'},
       handler:function(){
         if(!this.gridPanel.getSelectionModel().getSelected()){
           Ext.Msg.alert("Attenzione","Selezionare una riga della lista");
           return
         }
         var nc=this.gridPanel.getSelectionModel().getSelected().data.u_nome+' '+this.gridPanel.getSelectionModel().getSelected().data.u_cognome;
         this.organizationList(this.gridPanel.getSelectionModel().getSelected().id,nc);
       },
       scope:this
      },'-',{
          text:'Contatti inseriti',
          icon:'css/icons/vcard.png',
          iconCls:'x-btn-text-icon',
          tooltip:{title:'Contatti inseriti',text:'Contatti inseriti dal Data-entry selezionato'},
          handler:function(){
            if(!this.gridPanel.getSelectionModel().getSelected()){
              Ext.Msg.alert("Attenzione","Selezionare una riga della lista");
              return
            }
            var nc=this.gridPanel.getSelectionModel().getSelected().data.u_nome+' '+this.gridPanel.getSelectionModel().getSelected().data.u_cognome;
            var v=new Application.dataentry.WinListContatti(this.gridPanel.getSelectionModel().getSelected().id);
            v.show(nc);
          },
          scope:this
         }]
  },
  
  getColumnConfig:function(){
    
    return [
        { header:'Data iscrizione', width:100, dataIndex:'u_data_iscrizione',renderer:Utils.utils.dateRenderer},
        { header:'Nome', width:100, dataIndex:'u_nome'},
        { header:'Cognome', width:100, dataIndex:'u_cognome'},
        { header:'Userid', width:100, dataIndex:'u_user'},
        { header:'Attivo',align:'center',width:50, dataIndex:'u_active',renderer:function(v){return  v==1 ? "Si":"No";}},
        this.rowAction
      ]
  },
  
  edit:function(id){
    this.editWin=new Application.dataentry.WinEdit();
    this.editWin.show(id);
    this.editWin.on('updated',function(){this.refreshPanel();},this);
  },
  
  organizationList:function(id,nc){
	var w=new Application.dataentry.Organizations();
	w.show(id,nc);
  }
});