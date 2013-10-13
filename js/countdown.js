var eventstr = "Срок заполнения и утверждения отчетов вышел!!!"; //Приветствие по окончанию отсчета
var countdownid = document.getElementById("countdown"); //ID элемента в который выводится время
var montharray=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");


function CountDowndmn(yr,m,d){
	cdyear=yr;
	cdmonth=m;
	cdday=d;
	var today=new Date();
	var todayy=today.getYear();
	if (todayy < 1000)
	todayy+=1900;
	var todaym=today.getMonth();
	var todayd=today.getDate();
	var todayh=today.getHours();
	var todaymin=today.getMinutes();
	var todaysec=today.getSeconds();
	var todaystring=montharray[todaym]+" "+todayd+", "+todayy+" "+todayh+":"+todaymin+":"+todaysec;
	futurestring=montharray[m-1]+" "+d+", "+yr;
	dd=Date.parse(futurestring)-Date.parse(todaystring);
	dday=Math.floor(dd/(60*60*1000*24)*1);
	dhour=Math.floor((dd%(60*60*1000*24))/(60*60*1000)*1);
	dmin=Math.floor(((dd%(60*60*1000*24))%(60*60*1000))/(60*1000)*1);
	dsec=Math.floor((((dd%(60*60*1000*24))%(60*60*1000))%(60*1000))/1000*1);


	if(dday <= 0 && dhour <= 0 && dmin <= 0 && dsec <= 1){
	countdownid.innerHTML=eventstr;
	return
	}
	else {
	var lastchar = "" + dsec;	lastchar = lastchar.substring(lastchar.length-1,lastchar.length);
	var dsecstr = "секунд";
	if (lastchar=="1") { dsecstr = "секунда"; }
	if ((lastchar=="2")||(lastchar=="3")||(lastchar=="4")) { dsecstr = "секунды"; }

	lastchar = ""+dmin;	lastchar = lastchar.substring(lastchar.length-1,lastchar.length);
	var dminstr    = "минут";
	if (lastchar=="1") { dminstr = "минута"; }
	if ((lastchar=="2")||(lastchar=="3")||(lastchar=="4")) { dminstr = "минуты"; }

	lastchar = ""+dhour;	lastchar = lastchar.substring(lastchar.length-1,lastchar.length);
	var dhourstr   = "часов";
	if (lastchar=="1") { dhourstr = "час"; }
	if ((lastchar=="2")||(lastchar=="3")||(lastchar=="4")) { dhourstr = "часа"; }

	lastchar = ""+dday;	lastchar = lastchar.substring(lastchar.length-1,lastchar.length);
	var ddaystr = "дней";
	if (lastchar=="1") { ddaystr = "день"; }
	if ((lastchar=="2")||(lastchar=="3")||(lastchar=="4")) { ddaystr = "дня"; }

	countdownid.innerHTML=dday+ " " +ddaystr+" "+dhour+" "+dhourstr+" "+dmin+" "+dminstr+" и "+dsec+" "+dsecstr;

}
setTimeout("CountDowndmn(cdyear,cdmonth,cdday)",1000);
}


//определим текущую дату
var nov = new Date();
var month = nov.getMonth(); //текущий месяц  отсчет с 0
var year = 2013;   //менять на след год после 20 декабря предыдущего
var day = nov.getDate();    //текущий день


var d; //плановое время
var m;  //плановый месяц

month = month + 1;

if (month == 3 || month == 6 || month == 9 || month == 12) {
   d = 20;
   if (day >= 1 & day < d) {
     m = month;
   }
   else if (day >= d & day <= 31) {
     m = month + 1;
    d = 25;
   }
} else if (month == 2 || month == 5 || month == 8 || month == 11) {
   d = 25;
   if (day >= 1 & day < d) {
     m = month;
   }
   else if (day >= d & day <= 31) {
     m = month + 1;
     d = 20;
   }
   } else if (month == 1 || month == 4 || month == 7 || month == 10) {
   d = 25;
   if (day >= 1 & day < d) {
     m = month;
   }
   else if (day >= d & day <= 31) {
     m = month + 1;
   }
   }


if (m == 13) {
	m = 1;
}

	CountDowndmn(year,m,d); //Дата отсчета ГОД, МЕСЯЦ, ДЕНЬ



