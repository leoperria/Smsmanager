var submitted=false;

function actionSubmit(id, act) {
 if (submitted==false){
   var makeSubmit=document.getElementById(id);
   makeSubmit.action=act;
   makeSubmit.submit();
   submitted=true;
 }
}