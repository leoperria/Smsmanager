Ext.namespace("Application.permissions");
Application.permissions.WinPermissions = function(){
  
  this.rowAction=new Ext.ux.grid.RowActions({
      actions:[{
        iconCls:'icon-edit',
        qtip:'Modifica'
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
          if(recorda.data.p_ID!=''){
				    this.deleteRecord(record.id);
          }
				break;
      }
    },this);
    
  this.init({
    winConfig:{title:'Gestione permessi',width:700},
    deleteUrl:'permissions/delete',
    gridConfig:{
        plugins:[this.rowAction],
        view: new Ext.grid.GroupingView({
          forceFit:true,
          startCollapsed:true,
          groupTextTpl: '{text}'
        })
    }
  });
};

Ext.extend(Application.permissions.WinPermissions, Application.apiGrid.WinList, {

  getStoreConfig:function(){
    
    return new Ext.data.GroupingStore({
      proxy: new Ext.data.HttpProxy({url: 'permissions/list'}),
      reader:new Ext.data.JsonReader({
      root:'results',
      totalProperty:'totalCount',
      id:'p_ID'
    },[
        {name:"a_ID",type:"int"},
        {name:"a_name"},
        {name:"a_UIname"},
        {name:'p_ID',type:'int'},
        {name:'p_ID_area',type:'int'},
        {name:'p_sortid',type:'int'},
        {name:'p_name'},
        {name:'p_UIname'},
        {name:'p_type'},
        {name:'p_default_value'},
        {name:'sadasd'}
      ]),
      remoteSort:true,
      remoteGroup:true,
      groupField:'a_UIname'
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
        text:'Aggiungi area',
        icon:'css/icons/add.png',
        iconCls:'x-btn-text-icon',
        tooltip:{title:'Aggiungi',text:'Aggiungi una nuova area di permessi'},
        handler: function(){
          this.editArea('new');
        },
        scope:this
      }]
  },
  
  getColumnConfig:function(){
    
    return [
        { header:'Area', width:40, dataIndex:'a_UIname'},
        { header:'Nome', width:100, dataIndex:'p_name'},
        { header:'UIname', width:100, dataIndex:'p_UIname'},
        { header:'Type', width:100, dataIndex:'p_type',renderer:function(value){return value==0 ? 'Permesso':'Parametro';}},
        { header:'Default value', width:120, dataIndex:'p_default_value'},
       this.rowAction
      ]
  },
  
  edit:function(id){
    this.editWin=new Application.permissions.WinEdit();
    this.editWin.show(id);
    this.editWin.on('updated',function(){this.refreshPanel();},this);
  },
  
  editArea:function(id){
    this.editWin=new Application.permissions.WinEditArea();
    this.editWin.show(id);
    this.editWin.on('updated',function(){this.refreshPanel();},this);
  }
});