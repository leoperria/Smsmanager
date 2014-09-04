Application.permissions.WinEditArea = function(){
  
  this.init({
    winConfig:{
      title:'Modifica/aggiungi un\'area di permessi',
      height:250
    },
    loadUrl:'permissions/getarea',
    saveUrl:'permissions/savearea'
  });
  
};

Ext.extend(Application.permissions.WinEditArea , Application.api.GenericForm, {
  
  getFormItems: function(){
    
    this.nameField=new Ext.form.TextField({
        fieldLabel: 'Nome',
        name:'name'
    });
    
    this.uiNameField=new Ext.form.TextField({
      fieldLabel:'UIName',
      name:'UIname'
    });
    
    return [
	  this.nameField,
	  this.uiNameField
    ]
    
  }
  
});