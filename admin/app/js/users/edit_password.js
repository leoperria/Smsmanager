Application.users.WinEditPassword = function(){
  var titolo='Modifica password utente';
  this.init({
    winConfig:{
      title:titolo,
      width:350,
      height:200
    },
    saveUrl:'users/setpassword',
    labelWidth:130
  });
  
};

Ext.extend(Application.users.WinEditPassword , Application.api.GenericForm, {
  
  getFormItems: function(){
	this.passwordField=new Ext.form.TextField({
        fieldLabel: 'Password',
        inputType:'password',
        name:'password',
        allowBlank:false
    });
    
    this.passwordConfirmField=new Ext.form.TextField({
        fieldLabel: 'Conferma password',
        inputType:'password',
        name:'confirm_password',
        allowBlank:false
    });
    this.passwordConfirmField.on('blur',function(){
	  if(this.passwordField.getValue()!=this.passwordConfirmField.getValue()){
		  Ext.Msg.show({
	        title:'ERRORE',
	        msg: 'Le password inserite risultano diverse<br/> ',
	        buttons: Ext.Msg.OK,
	        fn:function(btn){
	          this.passwordField.reset();
	          this.passwordConfirmField.reset();
	        },
	        scope:this,
	        icon: Ext.MessageBox.WARNING
	      });
	  }
	},this);
    return [this.passwordField,this.passwordConfirmField]; 
  },
  
  loadForm:function(){
    return null;
  }
  
});