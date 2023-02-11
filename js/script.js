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
 		 alert("Maximum Passenger per ticket is 5.");

 	}
}
function createTableRowEquipos (idx) {
   if (idx == 0) {
     return  "<a class='edit' title='Edit' data-toggle='tooltip'><i class='material-icons'>&#xE254;</i></a>" +
             "<a class='delete' title='Delete' data-toggle='tooltip'><i class='material-icons'>&#xE872;</i></a>" ;
   }
   else {
     return "<input class='insert-field' style='width:100%' type='text'  name='BX_ast[]' value=''>";
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
      return "<input class='insert-field' style='width:100%' type='text'  name='BX_ast[]' value=''>";
    }
  }
