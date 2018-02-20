jQuery(function($){

	$('#test').BootSideMenu({
            side: "left", 
            pushBody:false,
            remember:false,
            autoClose:true,
            closeOnClick: false
        });

	$("#limitedVendor").click(function(){ 
		var regex = /[0-9]/;
		var vendor = prompt('Enter the vendor Number : ');
		if(regex.test(vendor))
		{
			$("#limitedVendorNo").val(vendor);
			$("#fromlimitedVendorNo").val($('#fromdate').val());
			$("#tolimitedVendorNo").val($('#todate').val());
			document.forms["limitedVendorform"].submit();
			$("#limitedVendorNo").val('');
		}
		else
		{
			alert("There are no vendor numbers with more than six digits. Try again.");
		}
	});

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

	$("#multiplesection").click(function(){ 
		var sectionNumber = prompt('Enter the section Number : ');
		if(sectionNumber)
		{
			$("#mulsectionNumber").val(sectionNumber);
			$("#mulfromsection").val($('#fromdate').val());
			$("#multosection").val($('#todate').val());
			document.forms["multiplesectionform"].submit();
			$("#mulsectionNumber").val('');
		}
		else
		{
			alert("There are no section numbers with more than four digits. Try again.");
		}
	});

	$("#multiplesectionneg").click(function(){ 
		var sectionNumber = prompt('Enter the section Number : ');
		if(sectionNumber)
		{
			$("#mulsectionNumberneg").val(sectionNumber);
			$("#mulfromsectionneg").val($('#fromdate').val());
			$("#multosectionneg").val($('#todate').val());
			document.forms["multiplesectionformneg"].submit();
			$("#mulsectionNumberneg").val('');
		}
		else
		{
			alert("There are no section numbers with more than four digits. Try again.");
		}
	});

	$("#sectionNegative").click(function(){ 
		var regex = /[0-9]/;
		var sectionNumber = prompt('Enter the section Number : ');
		if(regex.test(sectionNumber))
		{
			$("#sectionNegNumber").val(sectionNumber);
			$("#fromNegsection").val($('#fromdate').val());
			$("#toNegsection").val($('#todate').val());
			document.forms["sectionNegform"].submit();
			$("#sectionNegNumber").val('');
		}
		else
		{
			alert("There are no section numbers with more than four digits. Try again.");
		}
	});

	$("#sectionMvt").click(function(){ 
		var regex = /[0-9]/;
		var sectionNumber = prompt('Enter the section Number : ');
		if(regex.test(sectionNumber))
		{
			$("#sectionMvtNumber").val(sectionNumber);
			$("#fromMvtsection").val($('#fromdate').val());
			$("#toMvtsection").val($('#todate').val());
			document.forms["sectionMvtform"].submit();
			$("#sectionMvtNumber").val('');
		}
		else
		{
			alert("There are no section numbers with more than four digits. Try again.");
		}
	});

	$("#dptMvt").click(function(){ 
		var regex = /[0-9]/;
		var dptNumber = prompt('Enter the department Number : ');
		if(regex.test(dptNumber))
		{
			$("#dptMvtNumber").val(dptNumber);
			$("#fromDptvendor").val($('#fromdate').val());
			$("#toDptvendor").val($('#todate').val());
			document.forms["dptMvtform"].submit();
			$("#dptMvtNumber").val('');
		}
		else
		{
			alert("There are no department numbers with more than two digits. Try again.");
		}
	});

	$("#vendoritemcode").click(function(){ 
		var itemcode = prompt('Enter the item code : ');
		if(itemcode)
		{
			$("#itemcode").val(itemcode);
			$("#fromcode").val($('#fromdate').val());
			$("#tocode").val($('#todate').val());
			document.forms["itemcodeform"].submit();
			$("#itemcode").val('');
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

	$("#vendorMvt").click(function(){ 
		var regex = /[0-9]/;
		var vendorNumber = prompt('Enter the vendor Number : ');
		if(regex.test(vendorNumber))
		{
			$("#vendorMvtNumber").val(vendorNumber);
			$("#fromMvtvendor").val($('#fromdate').val());
			$("#toMvtvendor").val($('#todate').val());
			document.forms["vendorMvtform"].submit();
			$("#vendorMvtNumber").val('');
		}
		else
		{
			alert("There are no vendor numbers with more than six digits. Try again.");
		}
	});

	$("#vendorNegative").click(function(){ 
		var regex = /[0-9]/;
		var vendorNumber = prompt('Enter the vendor Number : ');
		if(regex.test(vendorNumber))
		{
			$("#vendorNegNumber").val(vendorNumber);
			$("#fromNegvendor").val($('#fromdate').val());
			$("#toNegvendor").val($('#todate').val());
			document.forms["vendorNegativeform"].submit();
			$("#vendorNegNumber").val('');
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
			alert("Verify that the numbers are correct.");
		}
	});

	$("#vendorDepartmentNeg").click(function(){ 
		var regex = /[0-9]/;
		var vendorNumber = prompt('Enter the vendor number :')
		var departmentNumber = prompt('Enter the department Number : ');
		if(regex.test(departmentNumber) && regex.test(vendorNumber))
		{
			$("#dvendorNumberNeg").val(vendorNumber); // vendor number
			$("#dptvendorNumberNeg").val(departmentNumber); // department number
			$("#fromvendorDptNeg").val($('#fromdate').val());
			$("#tovendorDptNeg").val($('#todate').val()); 
			document.forms["vendorDepartmentformNeg"].submit();
			$("#dvendorNumberNeg").val(''); // vendor number
			$("#dptvendorNumberNeg").val(''); // department number
		}
		else
		{
			alert("Verify that the numbers are correct.");
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
			alert("Verify that the numbers are correct.");
		}
	});

	$(".vendorOne").html($("#vendor1").text());
	$(".vendorTwo").html($("#vendor2").text());
	$(".vendorEqual").html($("#vendor3").text());

	$("#vendorSectionMvt").click(function(){ 
		var regex = /[0-9]/;
		var vendorNumber = prompt('Enter the vendor number :')
		var sectionNumber = prompt('Enter the section Number : ');
		if(regex.test(sectionNumber) && regex.test(vendorNumber))
		{
			$("#svendorMvtNumber").val(vendorNumber); // vendor number
			$("#sctvendorMvtNumber").val(sectionNumber); // section number 
			$("#fromvendorMvtSection").val($('#fromdate').val());
			$("#tovendorMvtSection").val($('#todate').val());
			document.forms["vendorSectionMvtform"].submit();
			$("#svendorMvtNumber").val(''); // vendor number
			$("#sctvendorMvtNumber").val(''); // section number
		}
		else
		{
			alert("Verify that the numbers are correct.");
		}
	});

	$("#vendorSectionNegative").click(function(){ 
		var regex = /[0-9]/;
		var vendorNumber = prompt('Enter the vendor number :')
		var sectionNumber = prompt('Enter the section Number : ');
		if(regex.test(sectionNumber) && regex.test(vendorNumber))
		{
			$("#svendorNegNumber").val(vendorNumber); // vendor number
			$("#sctvendorNegNumber").val(sectionNumber); // section number 
			$("#fromvendorNegSection").val($('#fromdate').val());
			$("#tovendorNegSection").val($('#todate').val());
			document.forms["vendorSectionNegativeform"].submit();
			$("#svendorNegNumber").val(''); // vendor number
			$("#sctvendorNegNumber").val(''); // section number
		}
		else
		{
			alert("Verify that the numbers are correct.");
		}
	});

	$("#vendorPrice").click(function(){ 
		var regex = /[0-9]/;
		var vendor1 = prompt('Enter the first vendor number :')
		var vendor2 = prompt('Enter the second vendor Number : ');
		if(regex.test(vendor1) && regex.test(vendor2))
		{
			$("#vendor1").val(vendor1); // vendor number
			$("#vendor2").val(vendor2); // section number 
			$("#fromPriceCompare").val($('#fromdate').val());
			$("#toPriceCompare").val($('#todate').val());
			document.forms["priceCompareForm"].submit();
			$("#vendor1").val(''); // vendor number
			$("#vendor2").val(''); // section number
		}
		else
		{
			alert("Verify that the vendor numbers are correct (They must have six digits).");
		}
	});

	$("#sectionPrice").click(function(){ 
		var regex = /[0-9]/;
		var vendor1 = prompt('Enter the first vendor number :');
		var vendor2 = prompt('Enter the second vendor Number : ');
		var section = prompt('Enter the section number :')
		if(regex.test(vendor1) && regex.test(vendor2) && regex.test(section))
		{
			$("#vendor1Section").val(vendor1); // vendor number
			$("#vendor2Section").val(vendor2); // section number 
			$("#sectionCompare").val(section);
			$("#fromSectionCompare").val($('#fromdate').val());
			$("#toSectionCompare").val($('#todate').val());
			document.forms["SectionPriceCompareForm"].submit();
			$("#vendor1Section").val(''); // vendor number
			$("#vendor2Section").val(''); // section number
			$("#sectionCompare").val('');
		}
		else
		{
			alert("Verify that the section numbers are correct (They must have four digits).");
		}
	});

	$("#receivingUPC").click(function(){ 
		var regex = /[0-9]/;
		var upcNumber = prompt('Enter the UPC Number : ');
		if(regex.test(upcNumber))
		{
			$("#upcReceivingNumber").val(upcNumber);
			$("#fromReceivingupc").val($('#fromdate').val());
			$("#toReceivingupc").val($('#todate').val());
			document.forms["upcReceivingform"].submit();
			$("#upcReceivingNumber").val('');
		}
		else
		{
			alert("The UPC number cannot have more than fifteen digits.");
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

	$(document).ready( function() {
		$('.dropdown-toggle').dropdown();
	});


	$('.setpass').click(function(event){
		if($('.newpass').val() == $('.newpass2').val())
		{
			$('.errorDiv').empty();
			$('.newpass').css('border', 'none');
			$('.newpass2').css('border', 'none');
			$('#setpassform').submit();
		}
		else
		{
			$('.errorDiv').append('<p class="bg-danger">Both passwords must match</p>');
			$('.newpass').css('border', '1px solid red');
			$('.newpass2').css('border', '1px solid red');
			event.preventDefault();
		}
	})

	$('#keywordInput').change(function(){
		var keyword = $(this).val();
		$.ajax({
	       url : '/csm/public/home/setKeyword',
	       type : 'POST',
	       data : {key : keyword},
	       dataType : 'html',
	       success : function(data, statut){
	           console.log(data) // On passe code_html à jQuery() qui va nous créer l'arbre DOM !
	       },

	       error : function(resultat, statut, erreur){
	         
	       },

	       complete : function(resultat, statut){

	       }

	    });
	})


});