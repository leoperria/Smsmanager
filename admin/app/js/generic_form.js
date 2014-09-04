Ext.namespace("Application.api");
/**
 * Configurazione della form.
 * il config deve contenere il winConfig, nel winConfig è possibile stabilire i label dei tasti utilizzando
 * le diciture: firstButtonText ( di default 'Salva') e secondButtonText (di default 'Chiudi')
 * 
 * La form accetta oltre ai soliti parametri di formPanel che sovrascrivono gli attuali anche un parametro labelWidth
 */
Application.api.GenericForm = function(config){
   this.init(config);
};
Ext.extend(Application.api.GenericForm , Ext.util.Observable, {

  win:null,
  id:null,
  formPanel:null,
  
  init: function(config){
    
    this.config=config;
    
    this.buildForm();
    
    this.addEvents({
      "updated" : true
    });    
    
    this.win = new Ext.Window(
	    Ext.apply({
	      title: 'Generic Form',
        iconCls: 'icon-shield',
	      width: 400,
	      height: 300,
	      plain:true,
	      modal:true,
	      border:false,
	      constrainHeader:true,
	      shim:false,
	      animCollapse:false,
	      buttonAlign:'right',
	      maximizable:false,
	      items:[this.formPanel],
	      buttons: [{
	        text: (typeof this.config.winConfig.firstButtonText!='undefined')? this.config.winConfig.firstButtonText : 'Salva',
	        handler:this.saveForm,
	        scope:this
	      },{
	        text: (typeof this.config.winConfig.secondButtonText!='undefined')? this.config.winConfig.secondButtonText : 'Chiudi',
	        handler:this.hide,
	        scope:this
	      }]
	    },this.config.winConfig)
    );
    
  },
 
  show: function(id){
    this.id=id;
    if(this.id!='new'){
      this.loadForm(this.id);
    }else{
      this.newRecordInit();
    }
    this.win.show();
  },
  
  newRecordInit:function(){    
  },
  
  loadForm:function(id){
    this.formPanel.getForm().load({
      url:this.config.loadUrl,
      params:{
        id:id
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
          url:this.config.saveUrl,
          params:{
            id:this.id
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

  hide: function(){
    Ext.QuickTips.init();
    this.win.close();
  },
  
  getFormItems: function(){ //STUB
    return null;
  },
  
  buildForm:function(){
    
    this.formPanel= new Ext.FormPanel({
	    baseCls: 'x-plain', 
	    bodyStyle: 'padding: 10px 10px 0 10px;',
	    labelWidth: (typeof this.config.formPanel!='undefined')? this.config.formPanel.labelWidth : 100,
	    defaults: {anchor:'90%',msgTarget:'side'},
	    plugins: [new Ext.ux.OOSubmit()],
	    items:this.getFormItems()
    });
  }
  
});