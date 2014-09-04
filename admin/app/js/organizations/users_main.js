Application.organizations.WinUsers = function(orgid,rag_soc){
  this.orgid=orgid;
  this.rag_soc=rag_soc;
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
          this.edit(record.id);
        break;

		case 'icon-delete':
		  this.deleteRecord(record.id);
		break;
        
        case 'icon-key':
          var editPasswordWin=new Application.organizations.WinEditPassword();
          editPasswordWin.show(record.id);
        break;
      }
    },this);
    
  this.init({
    winConfig:{title:'Gestione utenti : '+this.rag_soc,width:800},
    deleteUrl:'usersadmin/delete',
    gridConfig:{plugins:[this.rowAction]}
  });
};

Ext.extend(Application.organizations.WinUsers, Application.apiGrid.WinList, {

  getStoreConfig:function(){

    return new Ext.data.JsonStore({
      url: 'usersadmin/list',
      baseParams:{
        orgid:this.orgid
      },
      root:'results',
      totalProperty:'totalCount',
      id:'u_ID',
      fields:[
        {name:'u_ID',type:'int'},
        {name:'u_data_iscrizione',type:'date',dateFormat:'Y-m-d'},
        {name:'u_nome'},
        {name:'u_cognome'},
        {name:'u_user'},
        {name:'u_active',type:'int'},
        {name:'r_role'}
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
        text: 'Contatti Inseriti',
        icon:'css/icons/vcard.png',
        iconCls:'x-btn-text-icon',
        handler: function(){
          if(!this.gridPanel.getSelectionModel().getSelected()){
            Ext.Msg.alert("Attenzione","Selezionare una riga della lista");
            return
          }
          var recordSelezionato=this.gridPanel.getSelectionModel().getSelected();
          var utente=recordSelezionato.data.u_nome+' '+recordSelezionato.data.u_cognome;
          var win=new Application.organizations.EntryWorked(this.orgid,recordSelezionato.id,utente,this.rag_soc);
          win.show();
          //this.hide();
        },
        scope: this
     }]
  },
  
  getColumnConfig:function(){
    
    return [
        { header:'Data iscrizione', width:100, dataIndex:'u_data_iscrizione',renderer:Utils.utils.dateRenderer},
        { header:'Nome', width:120, dataIndex:'u_nome'},
        { header:'Cognome', width:100, dataIndex:'u_cognome'},
        { header:'Userid', width:100, dataIndex:'u_user'},
        { header:'Ruolo', width:80, dataIndex:'r_role'},
        { header:'Attivo',align:'center',width:50, dataIndex:'u_active',renderer:function(v){return  v==1 ? "Si":"No";}},
        this.rowAction
      ]
  },
  
  edit:function(id){
    this.editWin=new Application.organizations.WinUserEdit();
    this.editWin.show(id,this.orgid);
    this.editWin.on('updated',function(){this.refreshPanel();},this);
  }
});