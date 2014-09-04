Ext.namespace("Identity");
//informazioni sull'utente attuale
Identity.info= function(){
  var infoUtente=new Array();
  
  return{
  	init:function(){
    
    },
    setInfoUtente : function(arr){
      infoUtente=arr;
    },
    
    getInfoUtente:function(){
      return infoUtente;
    }
    
  }
}();
Ext.onReady(Identity.info.init, Identity.info, true); 