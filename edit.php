<?php
/*
 * (C) 2018. Aleksey Eremin
 * 22.08.19
 *
 * Отображение и загрузка таблицы
 *
 */
require_once "common.php";

// открытая БД $My_Db
global $My_Db, $Uid, $Reg;

printHeadPageEdt("Редактирование границ");

$Op_Id = 0; // в opdocshead изменится
// заголовок с названием оператора и списком агентов
//require_once "opdocshead.php";
// код оператора, про которого показываем - $Op_Id

// установим признак возможности редактирования строки оператора по региону
$canEditOp = true; // isCanEditOp($Op_Id);

$ud = ($canEditOp)? 'уд.': '';

echo <<<_EOF

<table width="100%" border="0">
<tr>
<td width="50%" class="showdocnote"><b>Границы</b></td>
<td width="50%" align="right"><a href="opnotes.php?op_id=$Op_Id" class="gotodocnote">заметки</a>$zs</td>
</tr>
</table>

<table width="100%" class="spis">
<thead><tr>
 <th class='spis' width="6%">дата</th>
 <th class='spis' width="10%">тип</th>
 <th class='spis'>описание</th>
 <th class='spis' width="4%">док.</th>
 <th class="spis" width="18px">$ud</th>
</tr></thead>
<tbody class="hightlight">

_EOF;

$sql = "SELECT id,dat,nam,subj,f.file_name,reg,lck 
        FROM (opdocs as d LEFT JOIN opdocstip as t ON (d.tdoc=t.tdoc))
        LEFT JOIN gmir.opers_files as f ON (d.ifile=f.ifile)
        WHERE op_id=$Op_Id ORDER BY reg,dat,d.tdoc,subj";
$res = queryDb($sql); //
while ($row = fetchAssoc($res)) {
  $ido  = $row['id'];
  $dats = dat2str($row['dat']);
  $docn = $row['nam'];      // название типа документа
  $subj = $row['subj'];
  $fnam = $row['file_name'];
  $re   = $row['reg'];      // регион
  $lck  = $row['lck'];      // признак блокировки данной записи
  $ff = '';
  $fr = '';
  // есть имя документа
  if($fnam) {
    // есть имя документа, его можно открыть
    $ff = "<a href='".FILES_DIR."/$fnam' target='_blank' class='nounderline' title='открыть документ'>
        <img src='images/doc_open.png' alt='открыть документ'></a>";
  }
  // запись "можно редактировать" или нельзя?
  $cledt = '';
  $cledtsel='';
  if($canEditOp  && (!$lck) && isCanEditRec($re)) {
    // если "можно редактировать" запись
    $cledt ='edt';
    $cledtsel='edtsel';
    if($fnam) {
      // есть документ  - можно удалить документ
      $fr = "<a href='opdocssave.php?delDoc=$ido' onclick='return confirm(\"Удалить документ?\")' title='удалить документ'><img src='images/doc_del.png' alt='удалить'></a>";
    } else {
      // нет имени документа - можно добавить документ или удалить запись
      // сделаем класс fileupload ! https://github.com/blueimp/jQuery-File-Upload/wiki/Multiple-File-Upload-Widgets-on-the-same-page
      $ff = "<div class='file-upload'><label title='добавить документ'>" .
            "<input class='fileupload' type='file' name='filename' data-url='opdocssave.php?addDoc=$ido'>" .
            "<span>+</span>" .
            "</label></div>";
      // редактирование - удалить строку
      $fr = "<a href='opdocssave.php?delRec=$ido' onclick='return confirm(\"Удалить запись?\")' title='удалить запись'><img src='images/rec_del.png' alt='удалить запись'></a>";
    }
  } else {
    $fr ="<span class='litlereg' title='регион'>$re</span>";
  }

  echo "<tr class='spis'><td class='$cledt spis' id='D$ido'>$dats</td>";  // D дата
  echo "<td class='$cledtsel spis' id='T$ido'>$docn</td>";                 // Т тип документа
  echo "<td class='$cledt spis' id='S$ido'>$subj</td>";                   // S тема описание
  echo "<td class='spis' align='center'>$ff</td>";
  echo "<td class='spis' align='center'>$fr</td></tr>\n";
}
$res->close();
echo "</tbody></table>\n";

if($canEditOp) {
  $dat = date("Y-m-d");
  // новые записи только по регионам
  echo <<<_EOF
  <form action="opdocssave.php" method="post" enctype="multipart/form-data">
  <input type="hidden" name="MAX_FILE_SIZE" value="30000000">
  <input type="hidden" name="newrecord" value="$Op_Id">
  <input type="hidden" name="f_dat" value="$dat">
  <!-- <input type="hidden" size="20" name="f_subj" value="документ"> -->
  <!-- <td><input type="file" name="filename"></td>  -->
  <input type="submit" value="новая запись" class="info">
  </form>
_EOF;
}

//
$jsonTip = makeTipSelectJson();
// подключим редактирование полей
echo <<<_EOF
<!-- программа  -->
<script type="text/javascript" language="javascript">
$(document).ready(function(){
  // подключим редактирование "в таблице на месте"
  $('td.edt').editable('opdocssave.php', {
    placeholder: '...'
  });
  // подключим редактирование "в таблице на месте"
  $('td.edtsel').editable('opdocssave.php', 
  {
     placeholder: '',
     //data   : " {'E':'Letter E','F':'Letter F','G':'Letter G', 'selected':'F'}",
     data: '$jsonTip',
     type: 'select'
     /* submit : 'OK' */
  });
  // подключим добавление документов
  $('.fileupload').fileupload()
      .bind('fileuploaddone', function(e,data) {window.location.reload(true);});
});
</script>

_EOF;
printEndPage();

// подготовим список типов
function  makeTipSelectJson()
{
  $res = queryDb("SELECT tdoc,nam FROM opdocstip ORDER BY tdoc");
  $arraytips = array();
  while(list($tdoc,$nam)=fetchRow($res)) $arraytips[$tdoc] = $nam;
  $res->close();
  $str = json_encode($arraytips);
  return $str;
}
