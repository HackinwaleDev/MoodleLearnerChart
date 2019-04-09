$(document).ready(function(){
	
	function postCurrent(url,params){
    var form = $("<form method='post'></form>");
    var input;
    form.attr({"action":url});
    $.each(params,function (key,value) {
        input = $("<input type='hidden'>");
        input.attr({"name":key});
        input.val(value);
        form.append(input);
		//alert(value);
    });	
    $(document.body).append(form);
    form.submit();
}
	
   $("#selectstudent").change(function(){
       var studentid=$(this).children('option:selected').val();
       var studentname=$(this).children('option:selected').attr('label');
       //console.log(studentid);
	   //console.log(studentname);
	   
	   var array={};
	   array["studentid"]=studentid;
	   array["studentname"]=studentname;
	   postCurrent("./index.php",array);
	    /* if (studentid!=""){
			$.post('./index.php',{
			 studentid:studentid,
			 studentname:studentname,
					}, function(data) {
				   var res = JSON.parse(data);
				   
				   //alert(res.msg);
				   alert("hello");
				});	
			location.reload();
		}  */	
   });
   
   
   
});