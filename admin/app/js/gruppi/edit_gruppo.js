Application.gruppi.WinEdit= function(orgid){
  this.orgid=orgid;
  this.init({
    winConfig:{
      title:'Modifica/aggiungi gruppo per i contatti',
      height:200
    },
    loadUrl:'gruppi/get',
    saveUrl:'gruppi/save'
  });
  
};

Ext.extend(Application.gruppi.WinEdit , Application.api.GenericForm, {
  
  getFormItems: function(){
    return [
     new Ext.form.TextField({
       fieldLabel: 'Descrizione',
       name:'descrizione',
       allowBlank:false
     })
    ];
  },
  
  loadForm:function(id){
    this.formPanel.getForm().load({
      url:'gruppi/get',
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
          url:'gruppi/save',
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
  }
});