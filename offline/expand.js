
function expand_all() {
   var uls = document.getElementsByTagName("ul");
   for (var i = 0; i < uls.length; i++) {
      if ( uls[i].style.display=="none" ) {
        uls[i].style.display="block";
      }
   }

   var all_a = document.getElementsByTagName("a");
   for (var i = 0; i < uls.length; i++) {
      if ( all_a[i].className=="trigger" ) {
        all_a[i].className="trigger open";
      }
   }
   var btn = document.getElementById('btn');
   btn.value = " ВСЕ СВЕРНУТЬ ";
}

function collapse_all() {
   var uls = document.getElementsByTagName("ul");
   for (var i = 0; i < uls.length; i++) {
      if ( uls[i].style.display=="block" ) {
        uls[i].style.display="none";
      }
   }

   var all_a = document.getElementsByTagName("a");
   for (var i = 0; i < uls.length; i++) {
      if ( all_a[i].className=="trigger open" ) {
        all_a[i].className="trigger";
      }
   }
   all_a[0].className="trigger open";
   uls[1].style.display="block"
   
   var btn = document.getElementById('btn');
   btn.value = "РАЗВЕРНУТЬ ВСЕ";  
}

function expand_switch(){
    if (btn.value == " ВСЕ СВЕРНУТЬ ") {
    	window.location.reload();
    	return true;
    	//collapse_all();
    }
    else expand_all();
}
