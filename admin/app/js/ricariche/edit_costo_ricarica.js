Application.ricariche.WinEditCostoRicarica = function(orgid){
  this.orgid=orgid;
  this.orgidField=new Ext.form.Hidden({name:'orgid',value:this.orgid});
  this.init({
    winConfig:{
      title:'Modifica/aggiungi costo ricarica',
      height:200
    },
    loadUrl:'ricariche/getcostoricarica',
    saveUrl:'ricariche/savecostoricarica'
  });
  
};

Ext.extend(Application.ricariche.WinEditCostoRicarica , Application.api.GenericForm, {
  
  getFormItems: function(){
	
    return [
     this.orgidField,
     new Ext.form.NumberField({
       fieldLabel: 'Numero SMS',
       name:'numero_sms',
       allowBlank:false
     }),
     new Ext.form.TextField({
       name:'importo',
       fieldLabel:'Importo €',
       allowBlank:false
     })
    ];
  }
});