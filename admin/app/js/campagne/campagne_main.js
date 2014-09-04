Ext.namespace("Application.campagne");
Application.campagne.WinList = function(orgid){
  this.orgid=orgid;
  this.init({
    winConfig:{title:'Campagne',width:950},
    deleteUrl:'campagne/delete'
  });
};

Ext.extend(Application.campagne.WinList, Application.apiGrid.WinList, {
  
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
          if(this.gridPanel.getSelectionModel().getSelected().data.c_inviata==1){
              Ext.Msg.alert("Attenzione","Le campagne gi&agrave; avviate non possono essere modificate");
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
          if(this.gridPanel.getSelectionModel().getSelected().data.c_inviata==1){
              Ext.Msg.alert("Attenzione","Le campagne gi&agrave; avviate non possono essere eliminate");
              return
          }
          this.deleteRecord(this.gridPanel.getSelectionModel().getSelected().id);
        },
        scope:this
      },'-',{
    	text:'Avvia campagna',
        icon:'css/icons/email_go.png',
        iconCls:'x-btn-text-icon',
        tooltip:{title:'Avvia campagna',text:'Invia gli SMS al target selezionato per questa campagna'},
        handler:function(){
          if(!this.gridPanel.getSelectionModel().getSelected()){
            Ext.Msg.alert("Attenzione","Selezionare una riga della lista");
            return
          }
          if(this.gridPanel.getSelectionModel().getSelected().data.c_inviata==1){
              Ext.Msg.alert("Attenzione","Le campagne gi&agrave; avviate non possono essere inviate di nuovo, creare una nuova campagna");
              return
            }
          this.sendSMS(this.gridPanel.getSelectionModel().getSelected().id);
        },
        scope:this
      }];
  },
  getStoreConfig:function(){

    return new Ext.data.JsonStore({
      url: 'campagne/list',
      baseParams:{
    	orgid:this.orgid
      },
      root:'results',
      totalProperty:'totalCount',
      id:'c_ID',
      fields:[
        {name:'c_ID',type:'int'},
        {name:'c_orgid',type:'int'},
        {name:'c_data_campagna',type:'date',dateFormat:'Y-m-d H:i:s'},
        {name:'c_data_invio',type:'date',dateFormat:'Y-m-d H:i:s'},
        {name:'c_nome_campagna'},
        {name:'c_testo'},
        {name:'c_criteri'},
        {name:'c_inviata',type:'int'},
        {name:'n_messaggi',type:'int'},
        {name:'inviati',type:'int'},
        {name:'consegnati',type:'int'}
      ]
    });
  },
  
  getColumnConfig:function(){
	
	function dataInvio(value,metadata,record,rowIndex,colIndex,store){
      if( value > record.data.c_data_campagna ){
       return value.format('d/m/Y');
      }else{
        return "";
      }
    }
	
	function inviatiRender(value,metadata,record,rowIndex,colIndex,store){
      if( value>0 && record.data.c_inviata==1 ){
       /*var percent=(100*(value/record.data.n_messaggi));
       return Math.round(percent) + "%";*/
      return value;
      }else{
       return "";
      }
    }

  	function consegnatiRender(value,metadata,record,rowIndex,colIndex,store){
      if( value>0 && record.data.c_inviata==1 ){
       /*var percent=(100*(value/record.data.n_messaggi));
       return Math.round(percent) + "%";*/
      return value;
      }else{
       return "";
      }
    }

    function percdeliveryRender(value,metadata,record,rowIndex,colIndex,store){
      if( value>0 && record.data.c_inviata==1 ){
       var percent=(100*(value/record.data.n_messaggi));
       return Math.round(percent*10)/10 + "%";
      }else{
       return "";
      }
    }

	
	function criteriRenderer(value,metadata,record,rowIndex,colIndex,store){
	 var testo="";
	 if(value.sesso!=false)testo=testo+"Sesso: "+value.sesso+"<br\>";
	 if(value.ID_provincia!=false)testo=testo+"Provincia: "+value.ID_provincia+"<br\>";
	 if(value.cap!=false)testo=testo+"C.A.P.: "+value.cap+"<br\>";
	 if(value.eta!=false)testo=testo+"Anni: "+value.eta.comparazione+" "+value.eta.value+"<br\>";
	 if(value.ID_gruppo!=false)testo=testo+"Gruppo: "+value.ID_gruppo+"<br\>";
	 return testo;
	}
	
    return [
        { header:'Data creazione', width:90, dataIndex:'c_data_campagna',renderer:Utils.utils.dateRenderer},
        { header:'Data invio', width:80, dataIndex:'c_data_invio',renderer:dataInvio},
        { header:'Nome campagna', width:120, dataIndex:'c_nome_campagna'},
        { header:'Testo', width:180, dataIndex:'c_testo'},
        { header:'Criteri', width:150, dataIndex:'c_criteri',renderer:criteriRenderer},
        { header:'Inviata', width:70, dataIndex:'c_inviata',renderer:function(value){return (value==1)? "X":"";}},
        { header:'N Messaggi', width:70, dataIndex:'n_messaggi'},
        { header:'Inviati', width:50, dataIndex:'inviati',renderer:inviatiRender},
        { header:'Cons.', width:50, dataIndex:'consegnati',renderer:consegnatiRender},
        { header:'% cons.', width:60, dataIndex:'consegnati',renderer:percdeliveryRender}
      ];
  },
  
  edit:function(id){
	if(this.gridPanel.getSelectionModel().getSelected()){
	  if(this.gridPanel.getSelectionModel().getSelected().data.c_data_invio!='' && id!='new'){
		  Ext.Msg.alert("Attenzione","Non si possono modificare le campagne gi&agrave; inviate");
          return
	  }
	}
    this.editWin=new Application.campagne.WinEdit(this.orgid);
    this.editWin.show(id);
    this.editWin.on('updated',function(){this.refreshPanel();},this);
  },
  
  sendSMS:function(id){
	var selezionata=this.gridPanel.getSelectionModel().getSelected();
	var criteri="";
	if(selezionata.data.c_criteri.sesso!=false)criteri=criteri+"sesso: "+selezionata.data.c_criteri.sesso+"<br\>";
	if(selezionata.data.c_criteri.ID_provincia!=false)criteri=criteri+"provincia: "+selezionata.data.c_criteri.ID_provincia+"<br\>";
	if(selezionata.data.c_criteri.cap!=false)criteri=criteri+"C.A.P.: "+selezionata.data.c_criteri.cap+"<br\>";
	if(selezionata.data.c_criteri.eta!=false)criteri=criteri+"et�: "+selezionata.data.c_criteri.eta.comparazione+" "+selezionata.data.c_criteri.eta.value+"<br\>";
	if(selezionata.data.c_criteri.gruppo!=false)criteri=criteri+"gruppo: "+selezionata.data.c_criteri.gruppo+"<br\>";
    Ext.Msg.show({
      title:'INVIA SMS',
      msg:'Avviare la campagna di avvio SMS?<br/>ATTENZIONE, l\'operazione non � reversibile,<br/>assicurarsi di aver impostato al meglio tutte le opzioni<br/>'+
          '<br/>Verranno inviati <b>'+selezionata.data.n_messaggi+'</b> SMS<br/>A tutti i contatti con :<br/>'+criteri+'<br/><br/>Continuare?',
      buttons: Ext.Msg.YESNO,
      width:300,
      height:350,
      fn:function(btn){
        if (btn=='yes'){
          Ext.Ajax.request({
            url:'campagne/invia',
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