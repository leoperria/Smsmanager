Application.organizations.WinEdit = function(){
  this.init({
    winConfig:{
      title:'Modifica/aggiungi clienti'
    },
    loadUrl:'organizations/get',
    saveUrl:'organizations/save'
  });
  
};

Ext.extend(Application.organizations.WinEdit , Application.api.GenericForm, {
  
  getFormItems: function(){
    
    this.dataIscrizioneField=new Ext.form.DateField({
        fieldLabel: 'Data iscrizione',
        name:'data_iscrizione',
        allowBlank:false
    });
    
    this.ragSocField=new Ext.form.TextField({
        fieldLabel: 'Ragione sociale',
        name:'rag_soc',
        allowBlank:false
    });
    
    this.sms_senderField=new Ext.form.TextField({
        fieldLabel: 'SMS sender',
        name:'sms_sender',
        maxLength:12,
        allowBlank:false
    });
    
    this.codFisField=new Ext.form.TextField({
      fieldLabel:'Codice fiscale',
      name:'codfis'
    });
    
    this.pIvaField=new Ext.form.TextField({
      fieldLabel:'P. IVA',
      name:'p_iva'
    });

    this.telField=new Ext.form.TextField({
      fieldLabel:'Telefono',
      name:'tel'
    });
    
    return [
		  this.dataIscrizioneField,
		  this.ragSocField,
		  this.sms_senderField,
		  this.codFisField,
		  this.pIvaField,
		  this.telField
    ]
    
  },
  
  newRecordInit:function(){
    var d=new Date();
    this.dataIscrizioneField.setValue(d.format('d/m/Y'));    
  }
  
});