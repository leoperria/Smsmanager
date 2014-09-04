/***********************************************************************************************************/    
/**  UTILS                                                                                                **/


function mytime() {
  var x=new Date();
  var r;
  h=x.getHours();
  m=x.getMinutes();
  s=x.getSeconds();
  if(s<=9) s="0"+s;
  if(m<=9) m="0"+m;
  if(h<=9) h="0"+h;
  time=h+":"+m+":"+s;
  if(document.getElementById('rtime')){
    document.getElementById('rtime').innerHTML=time;
  }
  setTimeout("mytime()",1000); 
}
window.onload = mytime;



if (!window.console || Ext.isSafari ){
  window.console={};
  window.console.debug= function(param){
   // alert(param);
  }
  window.console.log= function(param){
   // alert(param);
  }
}

Ext.override(Ext.grid.GridView, {
  scrollTop : function() {
      this.scroller.dom.scrollTop = 0;
      this.scroller.dom.scrollLeft = 0;
  },
  scrollToTop : Ext.emptyFn
});

Ext.namespace("Application");
Ext.namespace("Utils");
Utils.utils = function(){
  var msgCt;
  var myData;
  var colModel;
  var myReader;
  var giorniSettimana;
  var mesi;
 
  function createBox(t, s){
        return ['<div class="msg">',
                '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
                '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc"><h3>', t, '</h3>', s, '</div></div></div>',
                '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
                '</div>'].join('');
    }
  
  return{
  	
    init: function(){

    },
    
    msg : function(title, format){
        if(!msgCt){
            msgCt = Ext.DomHelper.insertFirst(document.body, {id:'msg-div'}, true);
        }
        var s = String.format.apply(String, Array.prototype.slice.call(arguments, 1));
        var m = Ext.DomHelper.append(msgCt, {html:createBox(title, s)}, true);
        m.slideIn('t').pause(1).ghost("t", {remove:true});
    },
    
   dateRenderer:function(value,metadata,record,rowIndex,colIndex,store){
      if(value){
       return value.format('d/m/Y');
      }
    },
    
    imageRender:function(value,metadata,record,rowIndex,colIndex,store){
      if(value!=0){
       return '<div class="thumb"><img src="file/get/id/'+value+'/maxx/50/maxy/50" title="'+record.data.o_descrizione+'"></div>';
      }else{
        return "";
      }
    },
    
    getDayOfWeek: function(dayNum){
    	if (!giorniSettimana){
        giorniSettimana={
        	0:"Domenica",
        	1:"Lunedì",
        	2:"Martedì",
        	3:"Mercoledì",
        	4:"Giovedì",
        	5:"Venerdì",
        	6:"Sabato",
        	7:"Domenica"
        };
    	}
      return giorniSettimana[dayNum];
    },
    
    getMonthLiteral: function(mese){
    	if (!mesi){
        mesi={
        	1:"Gennaio",
          2:"Febbraio",
          3:"Marzo",
          4:"Aprile",
          5:"Maggio",
          6:"Giugno",
          7:"Luglio",
          8:"Agosto",
          9:"Settembre",
          10:"Ottobre",
          11:"Novembre",
          12:"Dicembre"
        };
    	}
      return mesi[mese];
    },
    
    
    daysOfWeekStore: function(){
      var s= new Ext.data.SimpleStore({
        fields: ['id_giorno', 'giorno'],
        data :[
          [1,'Lunedì'],
          [2, 'Martedì'],
          [3, 'Mercoledì'],
          [4,'Giovedì'],
          [5, 'Venerdì'],
          [6, 'Sabato'],
          [7, 'Domenica']
        ]
      });
      return s;
    },
    
    boldRenderer: function (value){
       return "<b>"+value+"</b>";
    },
    
    euroRender:function (v){
      if(v!=''){
        v = (Math.round((v-0)*100))/100;
        v = (v == Math.floor(v)) ? v + ".00" : ((v*10 == Math.floor(v*10)) ? v + "0" : v);
        return "€ "+ v;
      }else{
        return '';
      }
    },
    
    pesoRender:function (v){
      return v+" g";
    },
    
    statoMessaggio:function(value,metadata,record,rowIndex,colIndex,store){
      if(record.data.c_tipo!=0){
        switch(value){
          case 0:
            return '<div class="grid-comunicazione-nuova"> </div>';
          break;
          case 1:
            return '<div class="grid-comunicazione-letta"> </div>';
          break;
        }
      }else{
        return '<div class="grid-comunicazione-globale"> </div>';
      }
    },
    
    tipoComunicazione:function(value,metadata,record,rowIndex,colIndex,store){
        switch(value){
          case 0:
            return 'Generale';
          break;
          case 1:
            return 'Privata';
          break;
        }
    },

    openWindow:function(title,url){
      window.open(url,'', 'width=800,height=600,titlebar=0,resizable=1,location=0,status=0,scrollbars=1,toolbar=0');
    },
    
   nD:function(){
      Ext.Msg.alert("Informazioni","Funzione non ancora disponidibie");
    },
    
    loadSMS:function(){
        var resp;
        Ext.Ajax.request({
          url:'ricariche/remainingsms',
          success:function(response,options){
            resp=Ext.decode(response.responseText);
            var dh = Ext.DomHelper;
            dh.overwrite("infoSMS",['SMS disponibili: '+resp.data.SMS]);
          },
          scope:this
        });
      }
  };
  
}();
Ext.onReady(Utils.utils.init, Utils.utils, true);


 