<?php
/**
 * Copyright (c) 2019. Aleksey Eremin
 * 18.08.2019, 16:53
 *
 */
/*
 * Выдача координат, подобно
 * https://nominatim.openstreetmap.org/search.php
 *
 */

require_once "common.php";

printHeadPage("Данные ГЕО");

// дополнительно подсчитаем кол-во документов
$sql ="SELECT oktmo, query,short
       FROM raio_oktmo";
echo "</tbody></table>\n";
echo <<<_EOF
<table width="100%" class="spis" id="myTable">
<thead><tr>
<th class="spis">1</th>
<th width="8%" class="spis">2</th>
<th width="6%" class="spis nosort" >3</th>
</tr></thead>
<tbody class="hightlight">

_EOF;

$res = queryDb($sql);
$cnt = 0;
while($row = fetchAssoc($res)) {
  $cnt++;
  $okt = $row['oktmo'];     // ОКТМО
  $que = $row['query'];   // запрос
  $shr = $row['short'];    // краткое наименование

  echo "<tr id=\"O$okt\">";
  echo "<td >$okt</td>";
  echo "<td >$shr</td>";
  echo "<td class='spis'><a href='opdocs.php?op_id=$okt'>$que</a></td>";
  echo "</tr>\n";
}
$res->close();
echo "</tbody></table>\n";
// подключим javascript для таблицы
echo '<script type="text/javascript" language="javascript" src="myjs/jindex.js"></script>';

printEndPage();
