Ext.namespace("Application.users");
Application.users.WinUsers = function(){
  
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
    winConfig:{title:'Gestione utenti',width:800},
    deleteUrl:'users/delete',
    gridConfig:{plugins:[this.rowAction]}
  });
};

Ext.extend(Application.users.WinUsers, Application.apiGrid.WinList, {

  getStoreConfig:function(){

    return new Ext.data.JsonStore({
      url: 'users/list',
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
    this.editWin=new Application.users.WinEdit();
    this.editWin.show(id);
    this.editWin.on('updated',function(){this.refreshPanel();},this);
  }
});