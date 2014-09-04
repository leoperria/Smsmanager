Application.permissions.WinEdit = function(){
  
  this.init({
    winConfig:{
      title:'Modifica/aggiungi un permesso',
      height:250
    },
    loadUrl:'permissions/get',
    saveUrl:'permissions/save'
  });
  
};

Ext.extend(Application.permissions.WinEdit , Application.api.GenericForm, {
  
  getFormItems: function(){
  	
    var s= new Ext.data.SimpleStore({
      fields: ['type', 'tipo'],
      data :[
        [0,'Permesso'],
        [1,'Parametro']
      ]
    });
    
    var areasStore=new Ext.data.JsonStore({
        url: 'permissions/loadareas',
        root: 'results',
        fields: [{name:'ID',type:'int'},{name:'UIname'}]
    });
    areasStore.load();
      
    this.comboAreas=new Ext.form.ComboBox({
      name:'ID_area',
      editable:false,
      fieldLabel:'Area',
      store:areasStore,
      triggerAction:'all',
      forceSelection:true,
      displayField:'UIname',
      valueField:'ID',
      hiddenName:'ID_area'
    });
    
    this.nameField=new Ext.form.TextField({
        fieldLabel: 'Nome',
        name:'name'
    });
    
    this.uiNameField=new Ext.form.TextField({
      fieldLabel:'UIName',
      name:'UIname'
    });
    
    this.comboType=new Ext.form.ComboBox({
      name:'type',
      editable:false,
      fieldLabel:'Tipo',
      store:s,
      mode:'local',
      triggerAction:'all',
      forceSelection:true,
      displayField:'tipo',
      valueField:'type',
      hiddenName:'type'
    });
    
    this.dfValueField=new Ext.form.TextField({
      fieldLabel:'Valore default',
      name:'default_value'
    });
    
    this.sortidField=new Ext.form.TextField({
      fieldLabel:'Sortid',
      name:'sortid'
    });
    
    return [
    this.comboAreas,
	  this.nameField,
	  this.uiNameField,
	  this.comboType,
	  this.dfValueField,
    this.sortidField
    ]
    
  }
  
});