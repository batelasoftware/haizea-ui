/**
 * Projet Name : Dynamic Form Processing with PHP
 * URL: http://techstream.org/Web-Development/PHP/Dynamic-Form-Processing-with-PHP
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2013, Tech Stream
 * http://techstream.org
 */

 function dateToLocalISO(date) {
    const off    = date.getTimezoneOffset()
    const absoff = Math.abs(off)
    return (new Date(date.getTime() - off*60*1000).toISOString().substr(0,23) +
            (off > 0 ? '-' : '+') +
            Math.floor(absoff / 60).toFixed(0).padStart(2,'0') + ':' +
            (absoff % 60).toString().padStart(2,'0'))
 }

 function exportCSVDataOtro (chdata)
 {
    var max_rows  = 0 ;
    for (var i = 0; i < chdata.length; i++) {
      if (Math.max(chdata[i]["data"].length) >= max_rows) {
         max_rows = Math.max(chdata[i]["data"].length);
      }
    }

    var csv="";
    for (var i   = 0; i < chdata.length; i++) {
      csv+= "Fechas;" + chdata[i]["label"].toUpperCase()+";";
    }

    csv += "\n";

    for (var i = 0; i < max_rows; i++) {
      for (var j = 0; j < chdata.length; j++) {
        if (i < chdata[j]["data"].length) {
            csv+= chdata[j]["data"][i].x +";" + chdata[j]["data"][i].y +";";
        }
        else {
          csv+= "" +";" + "b" +";";
        }
      }
      csv += "\n";
    }

    var hiddenElement = document.createElement('a');
    hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
    hiddenElement.target = '_blank';

    //provide the name for the CSV file to be downloaded
    let csvDate = new Date();
    hiddenElement.download = 'Datos_'+csvDate.toISOString()+'.csv';
    hiddenElement.click();
 }

 /**
 */
 function exportCSVData (bx_values,bx_dates,haizea_names)
 {

   console.log("Function Export Data");
   console.log (bx_values);
   console.log (bx_dates);
   var max_rows  = 0 ;
   for (var i = 0; i < bx_values.length; i++) {
     if (Math.max(bx_values[i].length) >= max_rows) {
       max_rows = Math.max(bx_values[i].length);
     }
   }

   for (var i = 0; i < bx_values.length; i++) {
     while (Math.max(bx_values[i].length) < max_rows){
       bx_values[i].push ("");
       bx_dates[i].push ("");
     }
   }
   var csv = '';
   for (var i = 0; i < haizea_names.length; i++) {
     csv+= "Fechas;" + haizea_names[i] +";";
   }
   csv += "\n";

   for (var i = 0; i < bx_values.length; i++) {
     bx_values[i] = bx_values[i].reverse();
     bx_dates[i]  = bx_dates[i].reverse();
   }
   bx_values = transpose(bx_values);
   bx_dates= transpose(bx_dates);

   for (var i = 0; i < bx_values.length; i++) {
     var values = bx_values[i];
     var dates = bx_dates[i];

     for (var j = 0 ; j < values.length;j++) {
       csv += dates[j] + ";"+ values[j] +";";
     }
     csv += "\n";
   }

   var hiddenElement = document.createElement('a');
   hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
   hiddenElement.target = '_blank';

   //provide the name for the CSV file to be downloaded
   let csvDate = new Date();
   hiddenElement.download = 'Datos_'+csvDate.toISOString()+'.csv';
   hiddenElement.click();

 }

 function updateChart(){
   if(document.getElementById("start_date").value == "-" ) {
     var ts_end       = Math.round(new Date().getTime() /1000);
     var ts_start     = ts_end -3600;
   }
   else{
     var dateStart = document.getElementById("start_date").value;
     var dateEnd   = document.getElementById("end_date").value;
     console.log(dateStart);
     console.log(dateEnd);

     $('#lineChart').load('winddata.html?datestart='+dateStart+'&dateend='+dateEnd);
     // var dateArrayStart =  dateStart.split('/');
     // var dateTimeStart  = dateArrayStart[2].split(' ');
     // var ts_start       = (new Date(dateArrayStart[0], dateArrayStart[1]-1, dateTimeStart[0]).getTime()/1000)+3600*dateTimeStart[1].substring(0, 2)+3600;
     // console.log(ts_start);
     // var dateArrayEnd  =  dateEnd.split('/');
     // var dateTimeEnd   = dateArrayEnd[2].split(' ');
     // var ts_end        = (new Date(dateArrayEnd[0],dateArrayEnd[1]-1, dateTimeEnd[0]).getTime()/1000)+3600*dateTimeEnd[1].substring(0, 2)+3600;
     // console.log(ts_end);
   }
   // const arrayCodeName = getSelectedCheckboxValues('value');
   //
   // if(ts_end>ts_start){
   //   var queryCodeName = 'CODE="'+arrayCodeName[0]+'"';
   //   for(var i = 1; i<arrayCodeName.length; i++){
   //   queryCodeName = queryCodeName +'_CODE="'+arrayCodeName[i]+'"';
   //   }
   //   $('#queryNewData').load('winddata.html?datestart='+ts_start+'&dateend='+ts_end+'&nameCode='+queryCodeName);
   // }
   // else{
   //   alert("Wrong Data Window. End Date End Must be greater than Start Date!!");
   // }
 }

function addRowFormEquipos(tableID) {
 	var table = document.getElementById(tableID);
 	var rowCount = table.rows.length;
 	if(rowCount < 50){							// limit the user from creating fields more than your limits
 		var row = table.insertRow(rowCount);
 		var colCount = table.rows[0].cells.length;
 		for(var i=0; i<colCount; i++) {
       var newcell = row.insertCell(i);
       if (rowCount == 1)
         newcell.innerHTML = createTableRowEquipos (i);
       else {
         //newcell.innerHTML = table.rows[1].cells[i].innerHTML;
         newcell.innerHTML = createTableRowEquipos (i);
       }
 		}
 	}else{
 		 alert("Maximum Passenge per ticket is 5.");

 	}
}
function createTableRowEquipos (idx) {
   if (idx == 0) {
     return  "<a class='edit' title='Edit' data-toggle='tooltip'><i class='material-icons'>&#xE254;</i></a>" +
             "<a class='delete' title='Delete' data-toggle='tooltip'><i class='material-icons'>&#xE872;</i></a>" ;
   }
   else {
     if (idx == 3){
       return "<input class='insert-field' style='width:100%' type='text'  name='BX_device_confs[]' value='0' readonly>";
     }
     else if (idx == 4){
       return "<select  name='BX_device_confs[]' style='width: 100%'>"+
             "<option value=0>False</option>"+
             "<option value=1>True</option> </select>"
     }
     else {
        return "<input class='insert-field' style='width:100%' type='text'  name='BX_device_confs[]' value=''>";
     }

   }
 }


  function addRowFormSerie(tableID) {
  	var table = document.getElementById(tableID);
  	var rowCount = table.rows.length;
  	if(rowCount < 50){							// limit the user from creating fields more than your limits
  		var row = table.insertRow(rowCount);
  		var colCount = table.rows[0].cells.length;
  		for(var i=0; i<colCount; i++) {
        var newcell = row.insertCell(i);
        if (rowCount == 1)
          newcell.innerHTML = createTableRowSerie (i);
        else {
          //newcell.innerHTML = table.rows[1].cells[i].innerHTML;
          newcell.innerHTML = createTableRowSerie (i);
        }
  		}
  	}else{
  		 alert("Maximum Passenger per ticket is 5.");
  	}
  }

 function createTableRowSerie (idx) {
    if (idx == 0) {
      return  "<a class='edit' title='Edit' data-toggle='tooltip'><i class='material-icons'>&#xE254;</i></a>" +
              "<a class='delete' title='Delete' data-toggle='tooltip'><i class='material-icons'>&#xE872;</i></a>" ;
    }
    else {
      return "<input class='insert-field' style='width:100%' type='text'  name='BX_serial_conf[]' value='0'>";
    }
  }
