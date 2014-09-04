Application.roles.WinEdit = function(){
  this.init({
    winConfig:{
      title:'Modifica/aggiungi un ruolo',
      height:120
    },
    loadUrl:'roles/get',
    saveUrl:'roles/save'
  });
};

Ext.extend(Application.roles.WinEdit , Application.api.GenericForm, {
  getFormItems: function(){
  	return [new Ext.form.TextField({fieldLabel: 'Nome ruolo',name:'role'})]
  }
});