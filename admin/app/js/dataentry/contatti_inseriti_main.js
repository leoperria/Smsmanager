Application.dataentry.WinListContatti = function(id){
	this.id=id;
	this.init();
};

Ext.extend(Application.dataentry.WinListContatti, Ext.util.Observable, {

  win:null,
  gridPanel:null,
  editWin:null,
  idSelezionato:0,
  orgid:0,
  filtro:0,
  
  
  init: function(){
    
    this.buildGridPanel();
    this.addEvents({'updated':true});
    
    this.win = new Ext.Window({
        title:'Lista contatti inseriti',
        width:900,
        maximizable:false,
        iconCls: 'icon-shield',
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
        items:[this.gridPanel],
        buttons:[{
          text:'Chiudi',
          handler:this.hide,
          scope:this
        }]
      });

  },
  
  show: function(nc){
	this.idSelezionato=0;
	this.orgid=0;
	this.filtro=0;
	this.win.setTitle('Lista dei contatti inseriti dal Data entry: '+nc);
    this.win.show();
    this.refreshPanel();
  },

  hide: function(){
    Ext.QuickTips.init();
    this.win.close();
  }, 
  
  buildGridPanel: function(){
    var organizationsStore=new Ext.data.JsonStore({
        url:'organizations/listcombo',
        root:'results',
        id:'orgid',
        fields:[
          {name:'orgid',type:'int'},
          {name:'rag_soc'}
        ]
      });
    organizationsStore.on('load',function(store,records){
      store.add(new Ext.data.Record({orgid:0,rag_soc:'Tutte le aziende'}));
    },this);
      
    this.comboOrganizations=new Ext.form.ComboBox({
      name:'orgid',
      fieldLabel:'Azienda',
      store:organizationsStore,
      triggerAction:'all',
      forceSelection:true,
      width:150,
      displayField:'rag_soc',
      valueField:'orgid',
      hiddenName:'orgid',
      emptyText:'Selezionare un\'azienda'
    });
    this.comboOrganizations.on('select',function(){
      this.orgid=this.comboOrganizations.getValue();
      this.refreshPanel();
    },this);
      
    var storeFiltraggi=new Ext.data.SimpleStore({
      fields: ['filtro', 'descrizione'],
      data :[
        [0,'Nessun filtro'],
        [1,'Pagati - Pubblicati'],
        [2,'Pagati - Non pubblicati'],
        [3,'Non pagati - Pubblicati'],
        [4,'Non pagati - Non pubblicati']
      ]
    });
    this.comboFiltro=new Ext.form.ComboBox({
      name:'filtro',
      editable:false,
      fieldLabel:'Filtro',
      store:storeFiltraggi,
      mode:'local',
      triggerAction:'all',
      forceSelection:true,
      displayField:'descrizione',
      valueField:'filtro',
      hiddenName:'filtro',
      emptyText:'Filtri'
    });
    this.comboFiltro.on('select',function(){
      this.filtro=this.comboFiltro.getValue();
      this.refreshPanel();
    },this);
    
    this.store=new Ext.data.JsonStore({
      url: 'contatti/listbycriteria',
      baseParams:{
    	id_dataentry:this.id
      },
      root:'results',
      totalProperty:'totalCount',
      id:'c_ID',
      fields:[
	      {name:'c_ID',type:'int'},
	      {name:'c_data_creazione',type:'date',dateFormat:'Y-m-d H:i:s'},
	      {name:'o_rag_soc'},
	      {name:'c_nome'},
	      {name:'c_cognome'},
	      {name:'c_telefono'},
	      {name:'c_indirizzo'},
	      {name:'c_localita'},
	      {name:'c_cap'},
	      {name:'c_ID_provincia'},
	      {name:'c_pagato',type:'int'},
	      {name:'c_pubblicato',type:'int'}
	    ]
    });
      
    this.bbar=new Ext.PagingToolbar({
      pageSize:15,
      displayInfo: true,
      displayMsg: '{2} righe trovate. Visualizzate {0} - {1}',
      emptyMsg: "nessun risultato.",
      store:this.store
    });
    
    function renderIndirizzo(value, metaData, record, rowIndex, colIndex, store){
        return record.data.c_indirizzo+' '+record.data.c_cap+' '+record.data.c_localita+' ('+record.data.c_ID_provincia+')';
    }
    
    this.gridPanel=new Ext.grid.GridPanel({
      region:'center',
      store:this.store,
      columns:[
        { header:'Data inserimento', width:90, dataIndex:'c_data_creazione',renderer:Utils.utils.dateRenderer},
        { header:'Azienda', width:150, dataIndex:'o_rag_soc'},
        { header:'Nome', width:100, dataIndex:'c_nome'},
        { header:'Cognome', width:100, dataIndex:'c_cognome'},
        { header:'Telefono', width:100, dataIndex:'c_telefono'},
        { header:'Indirizzo', width:200,dataIndex:'c_indirizzo',renderer:renderIndirizzo},
        { header:'Pagato',width:70,dataIndex:'c_pagato',align:'center',renderer:function(v){return (v==1)? "X":"";}},
        { header:'Pubblicato',width:70,dataIndex:'c_pubblicato',align:'center',renderer:function(v){return (v==1)? "X":"";}}
      ],
      tbar:[
        this.comboOrganizations,'-',this.comboFiltro,'-','Tutti i filtrati:'
      ,'-',{
        text:'Pubblica',
        icon:'css/icons/status_online.png',
        iconCls:'x-btn-text-icon',
        tooltip:{title:'Pubblica',text:'Marchia gli inserimenti filtrati come "PUBBLICATO"'},
        handler:function(){
          if(this.gridPanel.getSelectionModel().getSelected()){
        	this.idSelezionato=this.gridPanel.getSelectionModel().getSelected().data.c_ID;
          }
          this.modifyRecords(1);
        },
        scope:this
      },'-',{
        text:'Nascondi',
        icon:'css/icons/status_offline.png',
        iconCls:'x-btn-text-icon',
        tooltip:{title:'Nascondi',text:'Marchia gli inserimenti filtrati come "NON PUBBLICATO"'},
        handler:function(){
          if(this.gridPanel.getSelectionModel().getSelected()){
           	this.idSelezionato=this.gridPanel.getSelectionModel().getSelected().data.c_ID;
          }
          this.modifyRecords(2);
        },
        scope:this
      },'-',{
        text:'Pagato',
        icon:'css/icons/money.png',
        iconCls:'x-btn-text-icon',
        tooltip:{title:'Segna come pagato',text:'Marchia gli inserimenti filtrati come "PAGATO"'},
        handler:function(){
          if(this.gridPanel.getSelectionModel().getSelected()){
          	this.idSelezionato=this.gridPanel.getSelectionModel().getSelected().data.c_ID;
          }
          this.modifyRecords(3);
        },
        scope:this
      },'-',{
        text:'Non pagato',
        icon:'css/icons/money_delete.png',
        iconCls:'x-btn-text-icon',
        tooltip:{title:'Segna come non pagato',text:'Marchia gli inserimenti filtrati come "NON PAGATO"'},
        handler:function(){
          if(this.gridPanel.getSelectionModel().getSelected()){
           	this.idSelezionato=this.gridPanel.getSelectionModel().getSelected().data.c_ID;
          }
          this.modifyRecords(4);
        },
        scope:this
      }],
      bbar:this.bbar
    });    
  },

  refreshPanel:function(){
    this.gridPanel.getStore().load({
      params:{
        start:0,
        id_dataentry:this.id,
        filtro:this.filtro,
        orgid:this.orgid,
        limit:this.bbar.pageSize      
      }
    });
  },
  modifyRecords:function(type){
	var actionType;
	if(this.idSelezionato!=0){
		switch(type){
	     case 1:
	    	 actionType={title:"Pubblicazione contatti",msg:'Marchiare il contatto selezionato come "PUBBLICATO"?',url:'contatti/setpubblicato'}
	     break;
	     case 2:
	    	 actionType={title:"Pubblicazione contatti",msg:'Marchiare il contatto selezionato come "NON PUBBLICATO"?',url:'contatti/unsetpubblicato'}
	     break;
	     case 3:
	    	 actionType={title:"Pagamento contatti",msg:'Marchiare il contatto selezionato come "PAGATO"?',url:'contatti/setpagato'}
	     break;
	     case 4:
	    	 actionType={title:"Pagamento contatti",msg:'Marchiare il contatto selezionato come "NON PAGATO"?',url:'contatti/unsetpagato'}
	     break;
	    }
		Ext.Msg.show({
		      title:actionType.title,
		      msg:actionType.msg,
		      buttons: Ext.Msg.YESNO,
		      fn:function(btn){
		        if (btn=='yes'){
		          Ext.Ajax.request({
		            url:actionType.url,
		            params:{
		        	  id_selected:this.idSelezionato,
		        	  id_dataentry:this.id,
		              filtro:this.filtro,
		              orgid:this.orgid,
		            },
		            success:function(response,options){
		              var result=Ext.decode(response.responseText);
		              if (result.success==true){
		                var s=this.gridPanel.getStore();
		                s.reload();
		                Utils.utils.msg(actionType.title,"Operazione avvenuta con successo");
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
		        }else{
		          this.idSelezionato=0;
		          this.gridPanel.getSelectionModel().clearSelections();
		        }
		      },
		      icon: Ext.MessageBox.QUESTION,
		      scope:this
		    }); 
	}else{
		switch(type){
	     case 1:
	    	 actionType={title:"Pubblicazione contatti",msg:'Marchiare i contatti filtrati come "PUBBLICATI"?',url:'contatti/setpubblicato'}
	     break;
	     case 2:
	    	 actionType={title:"Pubblicazione contatti",msg:'Marchiare i contatti filtrati come "NON PUBBLICATI"?',url:'contatti/unsetpubblicato'}
	     break;
	     case 3:
	    	 actionType={title:"Pagamento contatti",msg:'Marchiare i contatti filtrati come "PAGATI"?',url:'contatti/setpagato'}
	     break;
	     case 4:
	    	 actionType={title:"Pagamento contatti",msg:'Marchiare i contatti filtrati come "NON PAGATI"?',url:'contatti/unsetpagato'}
	     break;
	    }
		Ext.Msg.show({
		      title:actionType.title,
		      msg:actionType.msg,
		      buttons: Ext.Msg.YESNO,
		      fn:function(btn){
		        if (btn=='yes'){
		          Ext.Ajax.request({
		            url:actionType.url,
		            params:{
		        	  id_dataentry:this.id,
		              filtro:this.filtro,
		              orgid:this.orgid,
		            },
		            success:function(response,options){
		              var result=Ext.decode(response.responseText);
		              if (result.success==true){
		                var s=this.gridPanel.getStore();
		                s.reload();
		                Utils.utils.msg(actionType.title,"Operazione avvenuta con successo");
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
	}
});