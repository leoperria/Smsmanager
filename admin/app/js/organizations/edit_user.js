Application.organizations.WinUserEdit = function(){
  this.init({
    winConfig:{
      title:'Modifica/aggiungi utente'
    },
    loadUrl:'usersadmin/get',
    saveUrl:'usersadmin/save'
  });
  
};

Ext.extend(Application.organizations.WinUserEdit , Application.api.GenericForm, {
  
  getFormItems: function(){
  	var store=new Ext.data.JsonStore({
        url: 'usersadmin/loadroles',
        root: 'results',
        fields: [{name:'ID',type:'int'},{name:'role'}]
    });
    store.load();
      
    this.comboRoles=new Ext.form.ComboBox({
      name:'ID_role',
      editable:false,
      fieldLabel:'Ruolo',
      store:store,
      triggerAction:'all',
      forceSelection:true,
      displayField:'role',
      valueField:'ID',
      hiddenName:'ID_role'
    });
    
    this.nameField=new Ext.form.TextField({
        fieldLabel: 'Nome',
        name:'nome'
    });
    
    this.cognomeField=new Ext.form.TextField({
      fieldLabel:'Cognome',
      name:'cognome'
    });
    
    this.nicknameField=new Ext.form.TextField({
      fieldLabel:'Userid',
      name:'user',
      allowBlank:false
    });
    
    this.dataIscrizioneField=new Ext.form.DateField({
        fieldLabel: 'Data iscrizione',
        name:'data_iscrizione',
        allowBlank:false
    });
    
    this.activeField=new Ext.form.Checkbox({
      fieldLabel:'Utente attivo',
      name:'active'
    });
    
    return [
		  this.dataIscrizioneField,
		  this.nameField,
		  this.cognomeField,
		  this.nicknameField,
		  this.comboRoles,
		  this.activeField
    ]
    
  },
  
  show:function(id,orgid){
    this.orgid=orgid; 
    this.id=id;
    if(this.id!='new'){
      this.loadForm(this.id);
    }else{
      this.newRecordInit();
    }
    this.win.show();  
  },
  
  saveForm:function(){
    if(this.formPanel.getForm().isValid()){
        this.formPanel.getForm().submit({
          url:'usersadmin/save',
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
    this.dataIscrizioneField.setValue(d.format('d/m/Y'));    
  }
  
});