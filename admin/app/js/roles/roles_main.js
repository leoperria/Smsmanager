Ext.namespace("Application.roles");
Application.roles.WinRoles = function(){
  
  this.rowAction=new Ext.ux.grid.RowActions({
      actions:[{
        iconCls:'icon-edit',
        qtip:'Modifica'
      },{
        iconCls:'icon-lock',
        qtip:'Modifica permessi'
      },{
        iconCls:'icon-delete',
        qtip:'Elimina'
      }]
    });
    
    this.rowAction.on('action',function(grid, record, action, row, col){
      switch(action) {
        case 'icon-edit':
          this.edit(record.id);
        break;

				case 'icon-delete':
				  this.deleteRecord(record.id);
				break;
        
        case 'icon-lock':
          var editPermessiWin=new Application.roles.WinEditPermessi(record.id);
          editPermessiWin.show();
        break;
      }
    },this);
    
  this.init({
    winConfig:{title:'Gestione ruoli',width:400},
    deleteUrl:'roles/delete',
    gridConfig:{plugins:[this.rowAction]}
  });
};

Ext.extend(Application.roles.WinRoles, Application.apiGrid.WinList, {

  getStoreConfig:function(){
    return new Ext.data.JsonStore({
      url: 'roles/list',
      root:'results',
      totalProperty:'totalCount',
      id:'r_ID',
      fields:[
        {name:'r_ID',type:'int'},
        {name:'r_role'},
        {name:'sadasd'}
      ]
    });
  },
  
  getColumnConfig:function(){
    
    return [
        { header:'ID', width:40, dataIndex:'r_ID',css:'background:#efefef;'},
        { header:'Nome ruolo', width:180, dataIndex:'r_role'},
       this.rowAction
      ]
  },
  
  edit:function(id){
    this.editWin=new Application.roles.WinEdit();
    this.editWin.show(id);
    this.editWin.on('updated',function(){this.refreshPanel();},this);
  }
});