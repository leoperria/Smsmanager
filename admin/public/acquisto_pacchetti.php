<?php
  require("../core/bootstrap.php");
  if((int)A::orgid()==0){
  	die();
  }
  $u=Zend_Registry::get('aaac');
  $user=$u->getCurrentUser();
  $db=Zend_Registry::get('db');
  $importi=$db->fetchAll("SELECT * FROM importi_ricariche WHERE orgid=? ORDER BY numero_sms",A::orgid());
  
?>
<html>
<head>
<title>Acquisto pacchetti SMS</title>
<style>
body{
 font-size:12px;
 font-family:'Verdana';
}
a, em{
 color:green;
 text-decoratin:none;
 font-weight:bold;
}
table{
 border-collapse:collapse;
 margin-top:20px;
}
table td{
 height:60px;
}
td div {
 background-image:url(css/images/acquista_pacchetto.jpg);
 background-repeat:no-repeat;
 padding:10px 10px;
 width:200px;
 height:40px;
}
div a{
 color:white;
 font-weight:bold;
 text-decoration:none;
}
div.header{
 background-image:url(css/images/logo_chart.jpg);
 background-repeat:no-repeat;
 height:90px;
}
</style>
</head>
<body>
<div class="header"></div>
<table>
<?php foreach($importi as $i):?>
<tr><td><div><a href="http://www.google.it" target="_blank"><?php echo $i['numero_sms'];?> SMS a &euro; <?php echo $i['importo']?> &raquo;</a></div></td></tr>
<?php endforeach;?>
</table>
<p>Per ulteriori informazioni puoi contattarci al numero <em>0783 XXXYYYY</em> o scriverci a <a href="mailto:info@mistersms.com">info@mistersms.com</a></p>
</body>
</html>