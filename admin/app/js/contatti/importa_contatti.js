Application.contatti.ImportWin = function(){
  this.init();
};
Ext.extend(Application.contatti.ImportWin , Ext.util.Observable, {

   win:null,
   formPanel:null,
   
   init: function(){

     this.addEvents({
      "updated" : true
     });

     var storeGruppi=new Ext.data.JsonStore({
       url: 'gruppi/listcombo',
       root: 'results',
       fields: [{name:'ID_gruppo'},{name:'descrizione'}]
     });
     storeGruppi.load();
     this.comboGruppi=new Ext.form.ComboBox({
       name:'ID_gruppo',
       editable:true,
       fieldLabel:'Gruppo',
       store:storeGruppi,
       triggerAction:'all',
       forceSelection:true,
       displayField:'descrizione',
       valueField:'ID_gruppo',
       hiddenName:'ID_gruppo'
     });

     this.formPanel= new Ext.FormPanel({
	     baseCls: 'x-plain',
	     bodyStyle: 'padding: 10px 10px 0 10px;',
	     labelWidth: 100,
	     defaults: {anchor:'90%',msgTarget:'side'},
	     plugins: [new Ext.ux.OOSubmit()],
	     items:[
        {
          xtype:'textfield',
          name:'separator',
          fieldLabel:'Separatore',
          value:';'
        },
        { xtype: 'textarea',
          fieldLabel: 'Contatti da importare',
          height:200,
          name:'text'
        },this.comboGruppi
       ]
     });
    
     this.win = new Ext.Window({
	      title: 'Importazione contatti',
        iconCls: 'icon-shield',
	      width: 400,
	      height: 330,
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
	        text: 'Importa',
	        handler:this.importText,
	        scope:this
	      },{
	        text: 'Annulla',
	        handler:this.hide,
	        scope:this
	      }]
	  });

   },


   importText:function(){
     if(this.formPanel.getForm().isValid()){
       this.formPanel.getForm().submit({
         url:'contatti/import',
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

   show: function(){
    this.win.show();
   },


   hide: function(){
    this.win.close();
   }

});