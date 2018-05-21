function add_events(lines){
	var inputs = document.getElementsByTagName("input");
	var checkboxes = [];
for(var i = 0; i < inputs.length; i++) {
    if(inputs[i].type == "checkbox") {
        checkboxes.push(inputs[i]); 
    }  
}

for (var i=0; i<checkboxes.length;i++){
	checkboxes[i].addEventListener('click', function(e){
		var fValue = e.srcElement.value;
		console.log(e);
		var chkState = e.srcElement.checked;
		if (chkState) { document.getElementById('category').value += fValue; };
	});
}

document.getElementById('cat').addEventListener("change",function(e){
  

	var v = document.getElementById('cat').value;
		console.log('look for ' + v);
	console.log('search');
   
       for(var j = 0; j < checkboxes.length; j++) {
		//console.log('checkbox ' + checkboxes[j].value);
        checkboxes[j].parentNode.style.display =  (checkboxes[j].value.indexOf(v)>-1) ? 'inline' : 'none'; 
      
} 
      

},false);
}


function main(){

function callAjax(url, callback){
    var xmlhttp;
    // compatible with IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function(){
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
            callback(xmlhttp.responseText);
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

callAjax('/CATEGORIES.TXT', function(data){
	var lines = data.split('\n');
	//console.log(lines); 
	add_events(lines);
});



}



main();
