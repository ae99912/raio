	// смотрим, какой уровень проекта выбран
    var select;
    var value;

	// смотрим, какой город или район выбран
    var select_2;
    var value_2;


var phpStr;
var phpObj_1;
var phpObj_2;


Drupal.behaviors.d7ToJsBehavior = {
  attach: function (context, settings) {
    // From PHP
    var phpObj = Drupal.settings.myArray;	// Ассоциативный массив приходит в виде объекта
    var phpStr = JSON.stringify(phpObj);	// Преобразование объекта в строку
    var phpObj_1 = Drupal.settings.point;	// уровень проекта
    var phpObj_2 = Drupal.settings.region;	// ОКТМО или координаты
 }
}

$(document).ready(function() {
	my_module_javascript_function();
});

function my_module_javascript_function()
{
	alert('При запуске страницы выводим карту с уровнем '+phpObj_1+' с местностью '+phpStr);

}

function myfunction_2() {

	// смотрим, какой уровень проекта выбран
    select = document.getElementById("edit-proj-level");
    value = select.value;

	// смотрим, какой город или район выбран
    select_2 = document.getElementsByName("region_select[line3]");
    value_2 = select_2[0].value;

switch(Number(value)) {
	case 2:
			// если совпал старый и новый выбор уровня проекта, а также выбор региона
    	if ((Number(phpObj_1)==2)&&(Number(phpObj_2)==Number(value_2))) { 
	   		alert('Отобразить на карте местность региона '+phpStr+' с ранее выбранными районами и городами');
    	}
    	else
    	{
	   		alert('Отобразить на карте местность региона '+value_2+' с районами (выбор районов и городов ещё не сделан)');
			 	Regions = value_2;
			 	loadRegs();
    	}

	break;

	case 3:
    	
    	if ((Number(phpObj_1)==3)&&(Number(phpObj_2)==Number(value_2))) {
    		alert('Отобразить на карте местность города '+phpStr+' с ранее выбранными координатами');
    	}
	else
	{
	  alert('Отобразить на карте местность города с центральной точкой '+Number(value_2.substr(0, 8))/1000000+'/'+Number(value_2.substr(8, 9))/1000000+' (выбор полигона ещё не сделан)');
	}

	    break;

	case 4:

    	if ((Number(phpObj_1)==4)&&(Number(phpObj_2)==Number(value_2))) { 
			alert('Отобразить на карте местность города '+phpStr+' с ранее выбранными координатами');
    	}
	    else
	    {
			alert('Отобразить на карте местность города с центральной точкой '+Number(value_2.substr(0, 8))/1000000+'/'+Number(value_2.substr(8, 9))/1000000+' (выбор полигона ещё не сделан)');
	    }

	    break;

	  default:
	    alert('Что-то не так');
	    break;

}

}

function myfunction() {
	// смотрим, какой уровень проекта выбран
    select = document.getElementById("edit-proj-level");
    value = select.value;

	switch(Number(value)) {
	  case 1:
    	if (phpObj_1==1) {
			alert('Список регионов'+phpStr);
    	}
	    else
	    {
	    	alert('Вывести карту России без выбранных регионов');
				Regions = "00";
				loadRegs();
	    }
	    break;

/*	  case 2:
	    break;
	  case 3:
    	if (phpObj_1==3) {
			alert('Коордиинаты полигона'+phpStr);
    	}
	    else
	    {
	    	alert('Вывести карту города с полигоном, центр которого по заданным координатам');
	    }
	    break;
	  case 4:
    	if (phpObj_1==4) {
			alert('Коордиинаты полигона'+phpStr);
    	}
	    else
	    {
	    	alert('Вывести карту города с полигоном, центр которого по заданным координатам');
	    }
	    break;
	  default:
	    alert('Что-то не так');
	    break;
	    */
	}




}
