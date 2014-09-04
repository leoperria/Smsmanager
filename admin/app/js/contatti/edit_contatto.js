Application.contatti.WinEdit = function(orgid){
  this.orgid=orgid;
  this.init({
    winConfig:{
	  height:450,
      title:'Modifica/Aggiungi contatto'
    },
    loadUrl:'contatti/get',
    saveUrl:'contatti/save/orgid/'+this.orgid
  });
  
};

Ext.extend(Application.contatti.WinEdit , Application.api.GenericForm, {
  
  getFormItems: function(){
  	var store=new Ext.data.JsonStore({
        url: 'province/listcombo',
        root: 'results',
        fields: [{name:'ID_provincia'},{name:'provincia'}]
    });
    store.load();
    
    var storeComboSesso=new Ext.data.SimpleStore({
        fields: ['sesso', 'descrizione'],
        data :[
          ['M','Maschio'],
          ['F','Femmina']
        ]
      });
    var storeGruppi=new Ext.data.JsonStore({
      url: 'gruppi/listcombo',
      baseParams:{orgid:this.orgid},
      root: 'results',
      fields: [{name:'ID_gruppo'},{name:'descrizione'}]
    });
    storeGruppi.load();
    this.comboSesso=new Ext.form.ComboBox({
        name:'sesso',
        editable:false,
        fieldLabel:'Sesso',
        store:storeComboSesso,
        mode:'local',
        triggerAction:'all',
        forceSelection:true,
        displayField:'descrizione',
        valueField:'sesso',
        hiddenName:'sesso'
    });
    this.comboGruppi=new Ext.form.ComboBox({
        name:'ID_gruppo',
        editable:true,
        fieldLabel:'Gruppo',
        store:storeGruppi,
        triggerAction:'all',
        forceSelection:false,
        displayField:'descrizione',
        valueField:'ID_gruppo',
        hiddenName:'ID_gruppo'
      });
      
    this.nameField=new Ext.form.TextField({
        fieldLabel: 'Nome',
        name:'nome'
    });
    
    this.cognomeField=new Ext.form.TextField({
      fieldLabel:'Cognome',
      name:'cognome'
    });
    
    this.dataNascitaField=new Ext.form.DateField({
        fieldLabel: 'Data nascita',
        name:'data_nascita'
    });
    
    this.indirizzoField=new Ext.form.TextField({
      fieldLabel:'Indirizzo',
      name:'indirizzo'
    });
    
    this.localitaField=new Ext.form.TextField({
        fieldLabel:'Localita',
        name:'localita'
      });
    
    this.capField=new Ext.form.TextField({
        fieldLabel:'C.A.P.',
        name:'cap'
      });
    this.comboProvince=new Ext.form.ComboBox({
        name:'ID_provincia',
        editable:true,
        fieldLabel:'Provincia',
        store:store,
        triggerAction:'all',
        forceSelection:true,
        displayField:'provincia',
        valueField:'ID_provincia',
        hiddenName:'ID_provincia'
      });
    
    this.dataCreazioneField=new Ext.form.DateField({
        fieldLabel: 'Data creazione',
        name:'data_creazione'
    });
    
    this.telefonoField=new Ext.form.TextField({
        fieldLabel: 'Telefono',
        name:'telefono',
        allowBlank:false
    });
    
    this.emailField=new Ext.form.TextField({
        fieldLabel: 'Email',
        name:'email'
    });
    
    return [
		  this.dataCreazioneField,
		  this.nameField,
		  this.cognomeField,
		  this.dataNascitaField,
		  this.comboSesso,
		  this.comboGruppi,
		  this.indirizzoField,
		  this.localitaField,
		  this.capField,
		  this.comboProvince,
		  this.telefonoField,
		  this.emailField
    ]
    
  },
  
  newRecordInit:function(){
    var d=new Date();
    this.dataCreazioneField.setValue(d.format('d/m/Y'));    
  }
  
});