Application.organizations.InternalResourcesOrganizations = function(){
  this.init();
};

Ext.extend(Application.organizations.InternalResourcesOrganizations, Ext.util.Observable, {
  
  win:null,
  internalResourcesOrganizationsStore:null,
  internalResourcesStore:null,
  orgid:0,
  gridInternalResourcesOrganizations:null,
  gridInternalResources:null,

  init: function(){
  
    this.initStores();
    this.buildInternalResourcesOrganizationsGrid();    
    this.buildInternalResourcesGrid();
    
    var lowerButtons={
      height:450,
      layout:'anchor',
      baseCls: 'x-plain',
      border:false,
      width:32,
      items:[{
        height:150,
        border:false,
        baseCls: 'x-plain'
      },{
        xtype:'button',
        width:20,
        height:20,
        text:"<",
        handler:function(){
          if(this.gridInternalResourcesOrganizations.getSelectionModel().getSelected()){
            this.delInternalResourcesOrganizations(this.gridInternalResourcesOrganizations.getSelectionModel().getSelected().id);
          } else {
            Ext.Msg.alert('SmsManager', 'Selezionare un elemento della lista "Dataentry assegnati"')
            return;
          }
        },
        scope:this
      },{
        height:10,
        border:false,
        baseCls: 'x-plain'
      },{
        xtype:'button',
        width:20,
        height:20,
        text:">",
        handler:function(){
          if(this.gridInternalResources.getSelectionModel().getSelected()){
           this.addInternalResourcesOrganizations(this.gridInternalResources.getSelectionModel().getSelected().id);
          } else {
            Ext.Msg.alert('SmsManager', 'Selezionare un elemento della lista "Dataentry disponibili"')
            return;
          }
        },
        scope:this
      }]
    };
    
    
    this.win = new Ext.Window({
      width: 550,
      height:500,
      plain:true,
      modal:true,
      border:false,
      bodyStyle:'padding:5px;',
      buttonAlign:'right',
      closeAction:'hide',
      layout:'table',
      resizable:false,
      layoutConfig: {
        columns: 3
      },
      items:[{
        items:this.gridInternalResources,
        border:false,
        baseCls: 'x-plain'    
      },lowerButtons,{
        items:this.gridInternalResourcesOrganizations,
        border:false,
        baseCls: 'x-plain'    
      }],
      buttons:[{
        text:'Chiudi',
        handler:function(){
         this.hide();
        },
        scope:this
      }]
    });
  },
  
  buildInternalResourcesOrganizationsGrid:function (){
    
    this.gridInternalResourcesOrganizations=new Ext.grid.GridPanel({
      border:true,
      title:'Dataentry assegnati',
      width:240,
      height:400,
      stripeRows:true,
      columns: [
        {header: "Nome", width: 100, sortable:true, dataIndex: 'nome'},
        {header: "Cognome", width: 100, sortable:true, dataIndex: 'cognome'}
      ],
      store: this.internalResourcesOrganizationsStore
    });
    
    this.gridInternalResourcesOrganizations.on('celldblclick',function(grid, rowIndex,colIndex){
      this.delInternalResourcesOrganizations(this.gridInternalResourcesOrganizations.getSelectionModel().getSelected().data.ID);
    },this);
    
  },

  refreshInternalResourcesOrganizations: function(){
    this.internalResourcesOrganizationsStore.load({
      params:{
        orgid:this.orgid
      }
    });
  },

  addInternalResourcesOrganizations: function (ID_user){
    Ext.Ajax.request({
      url:'organizations/enabledataentry',
      params:{
        orgid:this.orgid,
        ID_user:ID_user
      },
      success:function(response,options){
        this.refreshInternalResourcesOrganizations();
        this.refreshInternalResources();
      },
      scope:this
    });
  },

  delInternalResourcesOrganizations: function(ID_user){
    Ext.Ajax.request({
      url:'organizations/disabledataentry',
      params:{
          orgid:this.orgid,
          ID_user:ID_user
        },
        success:function(response,options){
            this.refreshInternalResourcesOrganizations();
            this.refreshInternalResources();
          },
      scope:this
    });
  },

  buildInternalResourcesGrid:function(){
     
    this.gridInternalResources=new Ext.grid.GridPanel({
      border:true,
      title:'Dataentry disponibili',
      width:220,
      height:400,
      stripeRows:true,
      columns: [
        {header: "Nome", width: 100, sortable:true,dataIndex: 'nome'},
        {header: "Cognome", width: 100, sortable:true,dataIndex: 'cognome'}
      ],
      store: this.internalResourcesStore,
      selModel:new Ext.grid.RowSelectionModel({singleSelect:true})
    });
    this.gridInternalResources.on('celldblclick',  function (grid, rowIndex,colIndex){
      this.addInternalResourcesOrganizations(this.gridInternalResources.getSelectionModel().getSelected().id);
    },this);
  },

  refreshInternalResources: function (){
    this.internalResourcesStore.load({
      params:{
        orgid:this.orgid
      }
    });
  },

  show: function(orgid,rag_soc){
    this.orgid=orgid;
    this.refreshInternalResourcesOrganizations();
    this.refreshInternalResources();
    this.win.setTitle('Dataentry interni scelti per il contratto: '+ rag_soc);
    this.win.show();
  },

  hide: function(){
    Ext.QuickTips.init();
    this.win.close();
  },

  initStores: function(){
    this.internalResourcesOrganizationsStore = new Ext.data.JsonStore({
      url: 'organizations/listresources',
      root:'results',
      totalProperty:'totalCount',
      id:'ID',
      fields:[
        {name:'ID',type:'int'},
        {name:'nome'},
        {name:'cognome'},
        {name:'user'}
      ]
    });

    this.internalResourcesStore=new Ext.data.JsonStore({
      url: 'organizations/getresources',
      root:'results',
      totalProperty:'totalCount',
      id:'ID',
      fields:[
        {name:'ID',type:'int'},
        {name:'nome'},
        {name:'cognome'},
        {name:'user'}
      ]
    });
  }
});