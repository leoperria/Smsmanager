Application.ricariche.WinEdit = function(orgid){
  this.orgid=orgid;
  this.init({
    winConfig:{
      title:'Modifica/aggiungi ricarica'
    }
  });
  
};

Ext.extend(Application.ricariche.WinEdit , Application.api.GenericForm, {
  
  getFormItems: function(){
  	var store=new Ext.data.JsonStore({
        url: 'ricariche/listcombo',
        baseParams:{
  		  orgid:this.orgid
  	    },
        root: 'results',
        fields: [
         {name:'ID',type:'int'},
         {name:'importo',type:'float'},
         {name:'numero_sms',type:'int'}
        ]
    });
    store.load();
    
    var costiTpl = new Ext.XTemplate('<tpl for="."><div class="x-combo-list-item">{numero_sms} SMS - euro {importo}</div></tpl>');
    
    this.comboCosti=new Ext.form.ComboBox({
      //name:'ID_role',
      editable:true,
      fieldLabel:'SMS',
      store:store,
      tpl:costiTpl,
      triggerAction:'all',
      displayField:'numero_sms',
      valueField:'numero_sms',
      hiddenName:'numero_sms'
    });
    this.comboCosti.on('select',function(combo,record,index){
      this.importoField.setValue(record.data.importo);
    },this);
    
    this.dataRicaricaField=new Ext.form.DateField({
        fieldLabel: 'Data',
        name:'data_ricarica',
        allowBlank:false
    });
    
    this.importoField=new Ext.form.TextField({
     name:'importo',
     fieldLabel:'Importo',
     allowBlank:false
    });
    
    return [this.dataRicaricaField,this.comboCosti,this.importoField]
    
  },
  
  loadForm:function(id){
    this.formPanel.getForm().load({
      url:'ricariche/get',
      params:{
        id:id,
        orgid:this.orgid
      },
      success:function(){},
      failure: function(form,action){
        if (action.result.errorMessages){
          var errMsg=action.result.errorMessages.join("<br/>");
          Ext.MessageBox.show({
            title: 'Problema...',
            msg: errMsg,
            buttons: Ext.MessageBox.OK,
            icon: Ext.MessageBox.WARNING,
            fn:function(btn){
              if (action.result.closeAfterErrors){
                this.hide();
              }
            },
            scope:this
          });
        }
      },
      scope:this,
      waitMsg:'Caricamento...'
    });
  },
  
  saveForm:function(){
    if(this.formPanel.getForm().isValid()){
        this.formPanel.getForm().submit({
          url:'ricariche/save',
          params:{
            id:this.id,
            orgid:this.orgid
          },
          waitMsg: 'Salvataggio in corso...',
          success: function(form,action){
            if(action.result.message){
              Ext.Msg.alert('Messaggio',action.result.message);
            }
            this.fireEvent('updated');
            this.hide();
          },
          failure: function(form,action){
          if (action.result.errorMessages){
            var errMsg=action.result.errorMessages.join("<br/>");
            Ext.MessageBox.show({
	            title: 'Problema...',
	            msg: errMsg,
	            buttons: Ext.MessageBox.OK,
	            icon: Ext.MessageBox.WARNING,
	            fn:function(btn){
	              if (action.result.closeAfterErrors){
		          	this.hide();
		          }
	            },
	            scope:this
	        });
          }
         
        },
        scope:this
      });
    }
  },

  
  newRecordInit:function(){
    var d=new Date();
    this.dataRicaricaField.setValue(d.format('d/m/Y'));    
  }
  
});