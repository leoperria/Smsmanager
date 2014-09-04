 <div id="searchbox">
  <form action="<?=url("/node/".$obj->getConfig()->get("SearchModuleNode"))?>">
      <table border="0">
        <tr>
        <td style="vertical-align:top"><input type="text" name="query" value=""></input></td>
        <td><a href="#" onclick="document.forms[0].submit();"><span>Cerca</span></a></td>
        </tr>
      </table>
    <input type="hidden" name="search" value="Cerca"/>
  </form>
 </div>
