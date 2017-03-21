jQuery(function($){
	$("#section").click(function(){ 
		var regex = /[0-9]/;
		var sectionNumber = prompt('Enter the section Number : ');
		if(regex.test(sectionNumber))
		{
			$("#sectionNumber").val(sectionNumber);
			$("#fromsection").val($('#fromdate').val());
			$("#tosection").val($('#todate').val());
			document.forms["sectionform"].submit();
			$("#sectionNumber").val('');
		}
		else
		{
			alert("There are no section numbers with more than four digits. Try again.");
		}
	});

	$("#vendor").click(function(){ 
		var regex = /[0-9]/;
		var vendorNumber = prompt('Enter the vendor Number : ');
		if(regex.test(vendorNumber))
		{
			$("#vendorNumber").val(vendorNumber);
			$("#fromvendor").val($('#fromdate').val());
			$("#tovendor").val($('#todate').val());
			document.forms["vendorform"].submit();
			$("#vendorNumber").val('');
		}
		else
		{
			alert("There are no vendor numbers with more than six digits. Try again.");
		}
	});

	$("#department").click(function(){ 
		var regex = /[0-9]/;
		var departmentNumber = prompt('Enter the department Number : ');
		if(regex.test(departmentNumber))
		{
			$("#departmentNumber").val(departmentNumber);
			$("#fromdepartment").val($('#fromdate').val());
			$("#todepartment").val($('#todate').val());
			document.forms["departmentform"].submit();
			$("#departmentNumber").val('');
		}
		else
		{
			alert("There are no department numbers with more than two digits. Try again.");
		}
	});

	$("#vendorDepartment").click(function(){ 
		var regex = /[0-9]/;
		var vendorNumber = prompt('Enter the vendor number :')
		var departmentNumber = prompt('Enter the department Number : ');
		if(regex.test(departmentNumber) && regex.test(vendorNumber))
		{
			$("#dvendorNumber").val(vendorNumber); // vendor number
			$("#dptvendorNumber").val(departmentNumber); // department number
			$("#fromvendorDpt").val($('#fromdate').val());
			$("#tovendorDpt").val($('#todate').val()); 
			document.forms["vendorDepartmentform"].submit();
			$("#dvendorNumber").val(''); // vendor number
			$("#dptvendorNumber").val(''); // department number
		}
		else
		{
			alert("Verify that you numbers are correct.");
		}
	});

	$("#vendorSection").click(function(){ 
		var regex = /[0-9]/;
		var vendorNumber = prompt('Enter the vendor number :')
		var sectionNumber = prompt('Enter the section Number : ');
		if(regex.test(sectionNumber) && regex.test(vendorNumber))
		{
			$("#svendorNumber").val(vendorNumber); // vendor number
			$("#sctvendorNumber").val(sectionNumber); // section number 
			$("#fromvendorSection").val($('#fromdate').val());
			$("#tovendorSection").val($('#todate').val());
			document.forms["vendorSectionform"].submit();
			$("#svendorNumber").val(''); // vendor number
			$("#sctvendorNumber").val(''); // section number
		}
		else
		{
			alert("Verify that you numbers are correct.");
		}
	});

	$("#vendorUPC").click(function(){ 
		var regex = /[0-9]/;
		var upcNumber = prompt('Enter the UPC Number : ');
		if(regex.test(upcNumber))
		{
			$("#upcNumber").val(upcNumber);
			$("#fromupc").val($('#fromdate').val());
			$("#toupc").val($('#todate').val());
			document.forms["upcform"].submit();
			$("#upcNumber").val('');
		}
		else
		{
			alert("The UPC number cannot have more than fifteen digits.");
		}
	});

		// Search by UPC number function 
	$('#search_upc').click(function(){
		var upcs = $('#upc').val().split(',');
		$('tr').each(function(){
			condition = 'undefined';
			var j = upcs.length + 1 ;
			for(var i = 0 ; i < upcs.length ; i++)
			{
				if($(this).children(":first").html().indexOf(upcs[i]) != -1)
				{
					console.log(upcs[i]);
					var condition = 'exist';
				}
				if($(this).children(":first").html() == 'UPC' || $(this).children(":first").html() == 'VDRNO')
				{
					var condition = 'upc';
				}
			}
			if(condition == 'undefined' && (condition != 'exist' || condition == 'upc'))
			{
				$(this).hide();
				condition = 'undefined';
			}
		});
	});

	$("#description").click(function(){ 
		var itemDescription = prompt('Enter the item description : ');
		if(itemDescription)
		{
			$("#itemDescription").val(itemDescription);
			$("#fromdescription").val($('#fromdate').val());
			$("#todescription").val($('#todate').val());
			document.forms["descriptionform"].submit();
			$("#itemDescription").val('');
		}
		else
		{
			alert("Please enter a valid description for you item.");
		}
	});

	$("#upcRange").click(function(){ 
		var upcRangeNo1 = prompt('Enter the first UPC number: ');
		var upcRangeNo2 = prompt('Enter the second UPC number: ');
		if(upcRangeNo1 && upcRangeNo2)
		{
			$("#upcRangeNo1").val(upcRangeNo1);
			$("#upcRangeNo2").val(upcRangeNo2);
			$("#fromupcRange").val($('#fromdate').val());
			$("#toupcRange").val($('#todate').val());
			document.forms["upcRangeform"].submit();
			$("#upcRangeNo1").val('');
			$("#upcRangeNo2").val('');
		}
		else
		{
			alert("Please enter a valid range of UPC numbers for your search.");
		}
	});

	$('#back_upc').click(function(){
		$('tr').show();
	});

	$(document).ready(function(){
		var parts = window.location.search.substr(1).split("&");
		var report = parts[0].split('=');
		$('#'+report[1]).addClass('active');
	});

});