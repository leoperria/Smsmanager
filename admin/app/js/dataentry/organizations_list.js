Application.dataentry.Organizations = function(){
  this.init();
};

Ext.extend(Application.dataentry.Organizations, Ext.util.Observable, {
  
  win:null,
  assignedOrganizationsStore:null,
  organizationsStore:null,
  id:0,
  gridAssignedOrganizations:null,
  gridOrganizations:null,

  init: function(){
  
    this.initStores();
    this.buildAssignedOrganizationsGrid();    
    this.buildOrganizationsGrid();
    
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
          if(this.gridAssignedOrganizations.getSelectionModel().getSelected()){
            this.delAssignedOrganizations(this.gridAssignedOrganizations.getSelectionModel().getSelected().id);
          } else {
            Ext.Msg.alert('SmsManager', 'Selezionare un elemento della lista "Aziende assegnate"')
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
          if(this.gridOrganizations.getSelectionModel().getSelected()){
           this.addAssignedOrganizations(this.gridOrganizations.getSelectionModel().getSelected().id);
          } else {
            Ext.Msg.alert('SmsManager', 'Selezionare un elemento della lista "Aziende disponibili"')
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
        items:this.gridOrganizations,
        border:false,
        baseCls: 'x-plain'    
      },lowerButtons,{
        items:this.gridAssignedOrganizations,
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
  
  buildAssignedOrganizationsGrid:function (){
    
    this.gridAssignedOrganizations=new Ext.grid.GridPanel({
      border:true,
      title:'Aziende assegnate',
      width:240,
      height:400,
      stripeRows:true,
      columns: [
        {header: "Azienda", width: 200, sortable:true, dataIndex: 'o_rag_soc'}
      ],
      store: this.assignedOrganizationsStore
    });
    
    this.gridAssignedOrganizations.on('celldblclick',function(grid, rowIndex,colIndex){
      this.delAssignedOrganizations(this.gridAssignedOrganizations.getSelectionModel().getSelected().id);
    },this);
    
  },

  refreshAssignedOrganizations: function(){
    this.assignedOrganizationsStore.load({
      params:{
        id:this.id
      }
    });
  },
 

  addAssignedOrganizations: function (orgid){
    Ext.Ajax.request({
      url:'organizations/enabledataentry',
      params:{
        orgid:orgid,
        ID_user:this.id
      },
      success:function(response,options){
        this.refreshAssignedOrganizations();
        this.refreshOrganizations();
      },
      scope:this
    });
  },

  delAssignedOrganizations: function(orgid){
    Ext.Ajax.request({
      url:'organizations/disabledataentry',
      params:{
          orgid:orgid,
          ID_user:this.id
        },
        success:function(response,options){
        	this.refreshAssignedOrganizations();
            this.refreshOrganizations();
          },
      scope:this
    });
  },

  buildOrganizationsGrid:function(){
     
    this.gridOrganizations=new Ext.grid.GridPanel({
      border:true,
      title:'Aziende assegnabili',
      width:220,
      height:400,
      stripeRows:true,
      columns: [
        {header: "Azienda", width: 200, sortable:true,dataIndex: 'o_rag_soc'}
      ],
      store: this.organizationsStore,
      selModel:new Ext.grid.RowSelectionModel({singleSelect:true})
    });
    this.gridOrganizations.on('celldblclick',  function (grid, rowIndex,colIndex){
      this.addAssignedOrganizations(this.gridOrganizations.getSelectionModel().getSelected().id);
    },this);
  },

  refreshOrganizations: function (){
    this.organizationsStore.load({
      params:{
        id:this.id
      }
    });
  },

  show: function(id,dataentry){
    this.id=id;
    this.refreshAssignedOrganizations();
    this.refreshOrganizations();
    this.win.setTitle('Aziende assegnate al data-entry '+ dataentry);
    this.win.show();
  },

  hide: function(){
    Ext.QuickTips.init();
    this.win.close();
  },

  initStores: function(){
    this.assignedOrganizationsStore = new Ext.data.JsonStore({
      url: 'organizations/listbydataentry',
      root:'results',
      totalProperty:'totalCount',
      id:'o_orgid',
      fields:[
        {name:'o_orgid',type:'int'},
        {name:'o_rag_soc'}
      ]
    });

    this.organizationsStore=new Ext.data.JsonStore({
      url: 'organizations/listautdataentry',
      root:'results',
      totalProperty:'totalCount',
      id:'o_orgid',
      fields:[
        {name:'o_orgid',type:'int'},
        {name:'o_rag_soc'}
      ]
    });
  }
});