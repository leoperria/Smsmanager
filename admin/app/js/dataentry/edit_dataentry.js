Application.dataentry.WinEdit = function(){
  this.init({
    winConfig:{
      title:'Modifica/aggiungi utente'
    },
    loadUrl:'usersadmin/get',
    saveUrl:'usersadmin/save'
  });
  
};

Ext.extend(Application.dataentry.WinEdit , Application.api.GenericForm, {
  
  getFormItems: function(){
  	var store=new Ext.data.JsonStore({
        url: 'usersadmin/loadroledataentry',
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
  
  newRecordInit:function(){
    var d=new Date();
    this.dataIscrizioneField.setValue(d.format('d/m/Y'));    
  }
  
});