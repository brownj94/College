<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	session_start();
	if(!$_SESSION['authenticated'])
	{
?>
		<script language="JavaScript">
			window.location='../index.php';
		</script>
<?php
	}
else
{
	$username = $_SESSION['username'];
}

	
	session_start();
	$pagename = "the <br />Student Tech Test Question  Administration Page";
	$error = $_GET['error'];
	$getAnotherRoom = $_GET['getAnotherRoom'];
	

	include('../Includes/Variables.php');
	include('../Includes/Functions.php');
	
	if($_SESSION['SuperAdmin'] <> 1 && GetRightsLevel(15) <> 9 )
	{
		echo"
			<script language='JavaScript'>
				window.location='http://fsudboard2.ferris.edu/tportal/index.php';
			</script>";	
	}
	

// ***************************** Begin Functions *****************************************

	$EditID = $_GET['EditID'];
	if(!isset($EditID))
	{	
		$EditID = $_POST['EditID'];
	}
	if($EditID=='')
	{
		$EditID = 0;	
	}
	//When a question has been chosen to be edited this query will return all information about the question and choices and 
	//assign the values to variables accordingly
	if(isset($EditID) && !isset($_POST['AddQuestion']))
	{
		$query = "SELECT STTQTID, STTQType, STTQQuestion, STTQComparisonString, STTQImage, STTQEnabled FROM STTQuestions_tbl 
		LEFT JOIN STTChoices_tbl on STTQID = STTCQID LEFT JOIN STTQType_tbl on STTQTID = STTQQTID WHERE STTQID = ".$EditID."";
	
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
		$result=odbc_exec($connection,$query);
			
		//include('../Includes/Variables.php');
		while(odbc_fetch_row($result))
		{
			$QTypeID = odbc_result($result, 1);
			$QType1 = odbc_result($result, 2);
			$Question1 = odbc_result($result, 3);
			$compString = odbc_result($result, 4);
			$QImage = odbc_result($result, 5);
			$QEnabled = odbc_result($result, 6);
		}

		$query = "select count(STTCID) AS TotalID FROM STTChoices_tbl";
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
		$result=odbc_exec($connection,$query);
		//	include('../Includes/Variables.php');
		while(odbc_fetch_row($result))
		{
			$OTotalOfRows = odbc_fetch_row($result, 1);	
				
		}
			
	}
	else
	{
		$query = "SELECT STTQImage FROM STTQuestions_tbl  WHERE STTQID = ".$EditID."";
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
		$result=odbc_exec($connection,$query);
		while(odbc_fetch_row($result))
		{
			$QImage = odbc_result($result, 1);
		}

	}
	if($EditID > 0)
	{
		if(!isset($_POST['AddQuestion']))
		{
?>			<script> 
				
				//This function will set the value of the Update field to 1 if the update button is clicked
				function ChangeUpdateValue()
				{
					document.getElementById("Update").value = 1;
				}
				//when the page is refreshed or the user leaves this page this function will execute to check to see if the user has clicked update or not
				function ConFirm()
				{
		 			confirm("If you don't update this question your changes will not be saved.");
				}
				window.onbeforeunload = function() 
				{
					var Update = document.getElementById("Update").value;
					if(Update == 0 )
					{
						ConFirm(); 
					}
				};		
		</script>
<?php
		} 		
		
		$query="select STTCText, STTCImage, STTCQID, STTCCorrect, STTQQTID FROM STTChoices_tbl 
		LEFT JOIN STTQuestions_tbl on STTCQID = STTQID  where STTCQID = ".$EditID." ";
		
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
		$result=odbc_exec($connection,$query);
		//include('../Includes/Variables.php');
		
		$ChoiceT = array();
		$ChoiceI = array();
		$ChoiceQ = array();
		$ChoiceC = array();
		$QueType = array();
				
		while(odbc_fetch_row($result))
		{		
			$ChText = odbc_result($result, 1);
			$ChImage = odbc_result($result, 2);
			$ChQID = odbc_result($result, 3);
			$ChCorrect = odbc_result($result, 4);
			$Type = odbc_result($result, 5);
					
			array_push($ChoiceT, $ChText);
			array_push($ChoiceI, $ChImage);
			array_push($ChoiceQ, $ChQID);
			array_push($ChoiceC, $ChCorrect);
			array_push($QueType, $Type);
		}
			
		$NumOfChoices = count($ChoiceT);
				
				
		//if the question does not have any images for the choices then do the following
			if($QImage == '' && $ChoiceI[0] == '' && $ChoiceI[1] == '' && $ChoiceI[1] == '' && $ChoiceI[2] == '' && $ChoiceI[3] == '')
			{
				$isempty = 1;	
			}
			else
			{
				$isempty = 0;	
			}
			
		
}
?>

<script type="text/javascript">

//This block of code will hide or show the enabled contents when the show disabled button is clicked
	function hideshow(which)
	{
		if (!document.getElementById)
		return
			if (which.style.display=="block")
			{
				document.getElementById("showHide").innerHTML='<b>Show Disabled</b>';
				which.style.display="none";
			}
			else
			{
			document.getElementById("showHide").innerHTML='<b>Hide Disabled</b>'
			which.style.display="block";
			}
		
	}
</script>


<script>

	/////////////////////First Choice////////////////////////////////
	
var EditID = "<?php echo $EditID; ?>";

if(EditID !='' || EditID !=0)
{		

		Ctext1 = "<?php echo $ChoiceT[0];?>";
		Ctext2 = "<?php echo $ChoiceT[1];?>";
		Ctext3 = "<?php echo $ChoiceT[2];?>";
		Ctext4 = "<?php echo $ChoiceT[3];?>";
		Ctext5 = "<?php echo $ChoiceT[4];?>";
		

	
		Checked1 = "<?php echo $ChoiceC[0];?>";
		Checked2 = "<?php echo $ChoiceC[1];?>";
		Checked3 = "<?php echo $ChoiceC[2];?>";
		Checked4 = "<?php echo $ChoiceC[3];?>";
		Checked5 = "<?php echo $ChoiceC[4];?>";
		
		
		QImage  = "<?php echo $QImage; ?>";
		CImage1 = "<?php echo $ChoiceI[0];?>";
		CImage2 = "<?php echo $ChoiceI[1];?>";
		CImage3 = "<?php echo $ChoiceI[2];?>";
		CImage4 = "<?php echo $ChoiceI[3];?>";
		isempty = "<?php echo $isempty; ?>";



	if(QImage != '')
	{
		
		Q = '<img src="'+QImage+'" height="200px" width="200px"/>';	
	}
	else
	{
		Q = '';	
	}
	//If the first text element in the choice array is not empty then assign the value to "Ca"
	if(Ctext1 !='')
	{
		Ca = Ctext1;
	}
	else
	{
		Ca = "";	
	}
	//if the first image element in the array is not empty then assign that value to la
	if(CImage1 !='')
	{
		
		Ia = '<img src="'+CImage1+'" height="220px" width="220px"/>';
	}
	else
	{
		Ia = '';	
	}
	
	////////End of the first ///////////
	
	//////Start of Second choice////////

	if(Ctext2 !='')
	{
	    Cb = Ctext2;
	}
	else
	{
		Cb = "";	
	}
	if(CImage2 !='')
	{
		Ib = '<img src="'+CImage2+'" height="220px" width="220px"/>';
	}
	else
	{
		Ib = '';	
	}
	//////End of second choice/////////


	/////////Start of third choice//////
	if(Ctext3 !='')
	{
		Cc = Ctext3;
	}
	else
	{
		Cc = "";	
	}
	if(CImage3 !='')
	{
		Ic = '<img src="'+CImage3+'" height="220px" width="220px"/>';
	}
	else
	{
		Ic = '';	
	}
	////////End of third choice///////
	
	
	///////Start of fourth choice/////
	if(Ctext4 !='')
	{
		Cd = Ctext4;
	}
	else
	{
		Cd = "";	
	}
	if(CImage4 !='')
	{
		Id = '<img src="'+CImage4+'" height="220px" width="220px"/>';
	}
	else
	{
		Id = '';	
	}
	
	/////////END OF FOURTH OPTION //////
	

	////////START OF FIFTH CHOICE//////
	
	///////Start of fourth choice/////
	if(Ctext5 !='')
	{
		Ce = Ctext5;
	}
	else
	{
		Ce = "";	
	}
	///////END OF FIFTH CHOICE////////
}
	

</script>


<script type="text/javascript">

function DeleteImages(id)
{
	
	
	document.getElementById(id).innerHTML='';

	if(id == 1)
	{
		id = 0;
	}
	else if(id == 2)
	{
		id = 11;
	}
	else if(id == 3)
	{
		id = 12;	
	}
	else if(id == 4)
	{
		id = 13;	
	}
	else if(id == 5)
	{
		id = 14;	
	}
	
	document.getElementById(id).value = 1;
	
}



function MultipleChoice(type)
{	
	
	if(EditID != '' || EditID !=0)
	{
		
		if(Checked1== 0)
		{
			var Ra = '<td align="right"><input type="radio" name="mc[]" value="a" ></td>';
		}
		else if(Checked1 == 1)
		{
			var Ra =  '<td align="right"><input type="radio" name="mc[]" value="a" Checked></td>';
		}
		if(Checked2== 0)
		{
			 var Rb = '<td align="right"><input type="radio" name="mc[]" value="b" ></td>';
		}
		else if(Checked2 == 1)
		{
			var Rb =  '<td align="right"><input type="radio" name="mc[]" value="b" Checked></td>';
		}
		if(Checked3 == 0)
		{
			 var Rc = '<td align="right"><input type="radio" name="mc[]" value="c" ></td>';
		}
		else if(Checked3 == 1)
		{
			var Rc =  '<td align="right"><input type="radio" name="mc[]" value="c" Checked></td>';
		}
		if(Checked4== 0)
		{
			var Rd = '<td align="right"><input type="radio" name="mc[]"  value="d" ></td>';
		}
		else if(Checked4 == 1)
		{
			var Rd=  '<td align="right"><input type="radio" name="mc[]" value="d" Checked></td>';
		}
		
		if(type == 6)
		{
			if(Checked5 == 0)
			{
				var Re = '<td align="right"><input type="radio" name="mc[]"  value="e" ></td>';
			}
			else if(Checked5 == 1)
			{
				var Re= '<td align="right"><input type="radio" name="mc[]" value="e" Checked></td>';
			}	
		}
		
	}
	
	if(type == 6)
	{
		 document.getElementById("mc").innerHTML= '<table>'+
									'<tr>'+
										'<td></td><td></td>'+
										'<td align="right"><h5>Correct</h5></td>'+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Choice A</h5></td>'+
										'<td><input type="text" size="30" name="ChoiceA" value="Strongly Agree" disabled="disabled"/></td>'
										+Ra+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Choice B</h5></td>'+
										'<td><input type="text" size="30" name="ChoiceB" value="Agree" disabled="disabled"/></td>'
										+Rb+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Choice C</h5></td>'+
										'<td><input type="text" size="30" name="ChoiceC" value="Neutral" disabled="disabled"/></td>'
										+Rc+
										
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Choice D</h5></td>'+
										'<td><input type="text" size="30" name="ChoiceD" value="Disagree" disabled="disabled"/></td>'
										+Rd+
									'</tr>'+
									
									'<tr>'+
										'<td align="right"><h5>Choice E</h5></td>'+
										'<td><input type="text" size="30" name="ChoiceE" value="Strongly Disagree" disabled="disabled"/></td>'
										+Re+
									'</tr>'+
									'</table>';
									
										
	}
	else
	{
		
		
		 document.getElementById("mc").innerHTML= '<table>'+
									'<tr>'+
										'<td></td><td></td>'+
										'<td align="right"><h5>Correct</h5></td>'+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Choice A</h5></td>'+
										'<td><input type="text" size="30" name="ChoiceA" value="'+Ca+'"/></td>'
										+Ra+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Image A</h5></td>'+
										'<td><input type="File" name="Cimage[]"  /></td>'+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Choice B</h5></td>'+
										'<td><input type="text" size="30" name="ChoiceB" value="'+Cb+'"/></td>'
										+Rb+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Image B</h5></td>'+
										'<td><input type="File" name="Cimage[]"/></td>'+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Choice C</h5></td>'+
										'<td><input type="text" size="30" name="ChoiceC" value="'+Cc+'"/></td>'
										+Rc+
										
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Image C</h5></td>'+
										'<td><input type="File" name="Cimage[]" /></td>'+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Choice D</h5></td>'+
										'<td><input type="text" size="30" name="ChoiceD" value="'+Cd+'"/></td>'
										+Rd+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Image D</h5></td>'+
										'<td><input type="File" name="Cimage[]" /></td>'+
									'</tr>'+
									
									'</table>';
	}
	
}
						
function TrueFalse(type)
{
	if(type == 4)
	{
			displayText1 = 'True';
			displayText2 = 'False';
	}
	else if(type == 8)
	{
		displayText1 = 'Like';
		displayText2 = 'Disalike';	
	}
 	if( EditID != 0)
	{
		
		
		if(type == 4)
		{
			if(Checked1 == 1)
			{
				option1 = '<td><input type="radio"  name="TF" value="T" checked /></td>';
				option2 = '<td><input type="radio"  name="TF" value="F"/></td>';
			}
			else if(Checked1 == 0)
			{
				option1 = '<td><input type="radio"  name="TF" value="T"/></td>';
				option2 = '<td><input type="radio"  name="TF" value="F" checked /></td>';	
			}
		}
		else if(type ==8)
		{
			if(Checked1 == 1)
			{
				option1 = '<td><input type="radio"  name="lD" value="Like" checked /></td>';
				option2 = '<td><input type="radio"  name="lD" value="Disalike"/></td>';
			}
			else if(Checked1 == 0)
			{
				option1  = '<td><input type="radio"  name="lD" value="Like"/></td>';
				option2 = '<td><input type="radio"  name="lD" value="Disalike" checked /></td>';	
			}
			
		}
	}
	else
	{
		if(type == 4)
		{
			option1 = '<td><input type="radio"  name="TF" value="T"/></td>';
			option2 = '<td><input type="radio"  name="TF" value="F"/></td>';	
		}
		else if(type == 8)
		{
			option1 = '<td><input type="radio"  name="lD" value="Like"/></td>';
			option2 = '<td><input type="radio"  name="lD" value="Disalike"/></td>';	
			
		}
	}
	
	document.getElementById("mc").innerHTML='<table>'+
									'<tr>'+
 										'<td></td><td></td>'+
										'<td align="right"><h5>Correct</h5></td>'+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>'+displayText1+'</h5></td>'
										+option1+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>'+displayText2+'</h5></td>'
										+option2+
									'</tr>'+
									'</table>';
		
		 
   }
	   
function SelectAllThatApply() 
{
	
	
	if(EditID != '' || EditID != 0)
	{
		
		if(Checked1== 0)
		{
			var Ra = '<td align="right"><input type="checkbox" name="a"></td>';
		}
		else if(Checked1 == 1)
		{
			var Ra =  '<td align="right"><input type="checkbox" name="a"Checked></td>';
		}
		if(Checked2== 0)
		{
			var Rb = '<td align="right"><input type="checkbox" name="b" ></td>';
		}
		else if(Checked2 == 1)
		{
			var Rb =  '<td align="right"><input type="checkbox" name="b" Checked></td>';
		}
		if(Checked3 == 0)
		{
			var Rc = '<td align="right"><input type="checkbox" name="c" ></td>';
		}
		else if(Checked3 == 1)
		{
			var Rc =  '<td align="right"><input type="checkbox" name="c" Checked></td>';
		}
		if(Checked4== 0)
		{
			var Rd = '<td align="right"><input type="checkbox" name="d" ></td>';
		}
		else if(Checked4 == 1)
		{
			var Rd = '<td align="right"><input type="checkbox" name="d" Checked></td>';
		}
	}
	
	document.getElementById("mc").innerHTML= '<table>'+
									'<tr>'+
										'<td></td><td></td>'+
										'<td align="right"><h5>Correct</h5></td>'+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Choice A</h5></td>'+
										'<td><input type="text" size="30" name="ChoiceA" value="'+Ca+'"/></td>'
										+Ra+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Image A</h5></td>'+
										'<td><input type="File" name="Cimage[]"/></td>'+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Choice B</h5></td>'+
										'<td><input type="text" size="30" name="ChoiceB" value="'+Cb+'"/></td>'
										+Rb+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Image B</h5></td>'+
										'<td><input type="File" name="Cimage[]"/></td>'+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Choice C</h5></td>'+
										'<td><input type="text" size="30" name="ChoiceC" value="'+Cc+'"/></td>'
										+Rc+
										
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Image C</h5></td>'+
										'<td><input type="File" name="Cimage[]"/></td>'+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Choice D</h5></td>'+
										'<td><input type="text" size="30" name="ChoiceD" value="'+Cd+'"/></td>'
										+Rd+
									'</tr>'+
									'<tr>'+
										'<td align="right"><h5>Image D</h5></td>'+
										'<td><input type="File" name="Cimage[]" /></td>'+
									'</tr></table>';
}

function ImageTable()
{
	
	
	
	if(QImage == '')
	{
		QImageBox = '<td align="center" style="border: 1px solid black; border-collapse: collapse;" height="220px" width="220px">'+Q+'</td>';
	}
	else
	{
		QImageBox = '<td id="1" align="center" style="border: 1px solid black; border-collapse: collapse;" height="220px" width="220px">'+Q+
					'<img src="../images/red.png" style="position: relative; top: -100%; left: 4%; z-index: 1;" width="20px" height="20px" onClick="DeleteImages(1);"></td>';	
	}
	
		if(CImage1 == '')
		{
			ImageBox1 = '<td align="center" style="border: 1px solid black; border-collapse: collapse;" height="220px" width="220px">'+Ia+'</td>';
		}
		else
		{
		ImageBox1 = '<td id="2" align="center" style="border: 1px solid black; border-collapse: collapse;" height="220px" width="220px">'+Ia+
						'<img src="../images/red.png" style="position: relative; top: -100%; left: 46%; z-index: 1;" width="20px" height="20px"      onClick="DeleteImages(2);"></td>';		
		}
	
	if(CImage2 == '')
	{
		ImageBox2 = '<td align="center" style="border: 1px solid black; border-collapse: collapse;" height="220px" width="220px">'+Ib+'</td>';
	}
	else
	{
		ImageBox2 = '<td id="3" align="center" style="border: 1px solid black; border-collapse: collapse;" height="220px" width="220px">'+Ib+
					'<img src="../images/red.png" style="position: relative; top: -100%; left: 46%; z-index: 1;" width="20px" height="20px" onClick="DeleteImages(3);"></td>';		
	}
	
		if(CImage3 == '')
		{
			ImageBox3 = '<td align="center" style="border: 1px solid black; border-collapse: collapse;" height="220px" width="220px">'+Ic+'</td>';	
		}
		else
		{
			ImageBox3 = '<td id="4" align="center" style="border: 1px solid black; border-collapse: collapse;" height="220px" width="220px">'+Ic+
					'<img src="../images/red.png" style="position: relative; top: -100%; left: 46%; z-index: 1;" width="20px" height="20px" onClick="DeleteImages(4);"></td>';		
		}
	
	if(CImage4 == '')
	{
		ImageBox4 = '<td align="center" style="border: 1px solid black; border-collapse: collapse;" height="220px" width="220px">'+Id+'</td>';
	}
	else
	{
		ImageBox4 = '<td id="5" align="center" style="border: 1px solid black; border-collapse: collapse;" height="220px" width="220px">'+Id+
					'<img src="../images/red.png" style="position: relative; top: -100%; left: 46%; z-index: 1;" width="20px" height="20px" onClick="DeleteImages(5);"></td>';	
	}
	
	
	document.getElementById("Itable").innerHTML = '<table  cellpadding="0px"  height="0">'+
													'<tr>'+
														'<th align="center">Question Image</th>'+
														'<th align="center">A</th>'+
														'<th align="center">B</th>'+
														'<th align="center">C</th>'+
														'<th align="center">D</th>'+
													'</tr>'+
													'<tr>'
														+QImageBox+
														ImageBox1+
														ImageBox2+ 
														ImageBox3+
														ImageBox4+
	
												 	'</tr>'+
												   '</table>';
}


	//this function will execute when no question has been chosen to be updated
	function input(type)
	{
		if(type == 6)
		{
			
			Ra = '<td align="right"><input type="radio" name="mc[]" value="a"></td>';
			Rb = '<td align="right"><input type="radio" name="mc[]" value="b"></td>';
			Rc = '<td align="right"><input type="radio" name="mc[]" value="c"></td>';
			Rd = '<td align="right"><input type="radio" name="mc[]" value="d"></td>';
			Re = '<td align="right"><input type="radio" name="mc[]" value="e"></td>';
				
			MultipleChoice(6);	
		}
		if(type == 2)
		{
			
			Ra = '<td align="right"><input type="radio" name="mc[]" value="a"></td>';
			Rb = '<td align="right"><input type="radio" name="mc[]" value="b"></td>';
			Rc = '<td align="right"><input type="radio" name="mc[]" value="c"></td>';
			Rd = '<td align="right"><input type="radio" name="mc[]" value="d"></td>';
	
			MultipleChoice(2);	
		}
		else if(type == 5)
		{
			Ra = '<td align="right"><input type="checkbox" name="a" ></td>';		
			Rb = '<td align="right"><input type="checkbox" name="b" ></td>';
			Rc = '<td align="right"><input type="checkbox" name="c" ></td>';
			Rd = '<td align="right"><input type="checkbox" name="d" ></td>';
	
			SelectAllThatApply();
		}
	
	}




	function redir()
	{
		setTimeout("location.href='STTQuestions.php'",0);			
	}
    //This is the first javascript function that will
	function ChangeQType(type)
	{	
		
		if(type != 8)
		{
			if("<?php echo $Question1; ?>" == '')
			{
				document.getElementById("s1").innerHTML='<b>Question</b> <input type="text" size="30" value="" name="Question"/></td>';	
			}
			else
			{
				document.getElementById("s1").innerHTML='<b>Question</b> <input type="text" size="30" value="<?php echo $Question1;?>" name="Question"/></td>';	
			}
			
			document.getElementById("s2").innerHTML='';
			if(type == 7)
			{
				document.getElementById("mc").innerHTML='';	
			}
		}
		
		if(type == 8)
		{
			document.getElementById("mc").innerHTML='';
			
			if("<?php echo $Question1;?>" == '')
			{
				document.getElementById("s1").innerHTML='<b>String 1</b> <input type="text" size="30" value="" name="Question"/></td>';
			}
			else
			{
				document.getElementById("s1").innerHTML='<b>String 1</b> <input type="text" size="30" value="<?php echo $Question1; ?>" name="Question"/>';
			}
			if("<?php echo $compString; ?>" == '')
			{
				document.getElementById("s2").innerHTML='<b>String 2</b> <input type="text" size="30" value="" name="string1"/></td>';
			}
			else
			{
				document.getElementById("s2").innerHTML='<b>String 2</b> <input type="text" size="30" value="<?php echo $compString; ?>" name="string1"/></td>';
			}
												
		}
		if(type == 6 || type == 7 || type == 8)
		{
			document.getElementById("staticQImage").innerHTML='';	
		}
		else
		{
			document.getElementById("staticQImage").innerHTML='<b>Question Image</b> <input name="Qimage" type="file" />';	
		}
		//stores the value of the edit id variable
		var Edit = "<?php echo $EditID; ?>";
	
		//if this variable is not equal to zero then a question is being edited
		if( Edit != 0)
		{
			//if the type of question selected is true or false or open ended then dont display an image box
			if(type == 3 || type == 4 || isempty == 1)
			{
				document.getElementById("Itable").innerHTML = '';
				
				if(type == 3)
				{
					document.getElementById("openEndedNote").innerHTML = '* Open Ended questions will not be graded';	
				}
				else if(type == 4)
				{
					//Make sures if the open ended status is was previously shown then it will be removed upon selection of another question type
					document.getElementById("openEndedNote").innerHTML = '';
				}
			}
			else
			{	
				ImageTable();
			}
		}
		//if the type of question selected equals 2 or 6 then 
		if(type == 2 || type == 6)
   		{
			document.getElementById("openEndedNote").innerHTML = '';
			//check to see if a question is being edited. if it is then display the multiple choice form 
			if(Edit != '' && Edit != 0)
			{
				if(type == 2)
				{
					input(2);
				}
				else
				{
					MultipleChoice(6);	
				}
			}
			else if(type == 2)
			{
				input(2);
			}
			else
			{
				input(6);	
			}
		}	
		//if the type of question is three then dont display a form
   		else if(type == 3)
   		{
	    	document.getElementById("mc").innerHTML='';
			
			if(type == 3)
			{
				document.getElementById("openEndedNote").innerHTML = '* Open Ended questions will not be graded';	
			}
   		}
		//if the type of question is 4 or 7 then display the form for the true or false
   		else if(type == 4 || type == 8)
   		{
			//Make sures if the open ended status is was previously shown then it will be removed upon selection of another question type
			document.getElementById("openEndedNote").innerHTML = '';
 			if(type == 4)
			{
				TrueFalse(4);
			}
			else if(type == 8)
			{
				TrueFalse(8);	
			}
		}
		//if the type of question is 5 then
   		else if(type == 5 )
   		{	
			//Make sures if the open ended status is was previously shown then it will be removed upon selection of another question type
			document.getElementById("openEndedNote").innerHTML = '';
			//store the edit variable  
			var Edit = "<?php echo $EditID; ?>";
			
			if(Edit != '' && Edit != 0)
			{
				SelectAllThatApply();
			}
			else
			{	
				input(5);
			}
		}
		
   	}
		
		
	
</script>

			
<?php
if(isset($_POST['AddQuestion']))
{
	//This will get the last 4 choice ID's if a question is being edited  
	if($EditID <>'' && $EditID <> 0)
	{
		$query = "select STTCID from STTChoices_tbl where STTCQID = ".$EditID." ";
	}
	else
	{
		$query = "select top 4 STTCID from STTChoices_tbl order by STTCID desc";
		
	}
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
		$result=odbc_exec($connection,$query);
			
			$ChoiceID = array();
			while(odbc_fetch_row($result))
			{
				$Choice = odbc_result($result, 1);	
				array_push($ChoiceID, $Choice);
			}
				
			
			//If a question is being edited get the choice id for each choice
			if($EditID <>'' && $EditID <> 0)
			{
				$ChoiceID[0] = $ChoiceID[0];
				$ChoiceID[1] = $ChoiceID[1];
				$ChoiceID[2] = $ChoiceID[2];
				$ChoiceID[3] = $ChoiceID[3];
			}
			
			//if there are no records in the database then start the choice file names off from 1
			else if($ChoiceID[0] == '')
			{
				$ChoiceID[0] = 1;
				$ChoiceID[1] = 2;
				$ChoiceID[2] = 3;
				$ChoiceID[3] = 4;
			}
			else
			{
				
				//otherwise add from the last record and on
				$ChoiceID[0] = $ChoiceID[3];
				$ChoiceID[1] = $ChoiceID[2];
				$ChoiceID[2] = $ChoiceID[1] +1;
				$ChoiceID[3] = $ChoiceID[2] +1;
				
				
				$ChoiceID[0] = $ChoiceID[3] + 1;
				$ChoiceID[1] = $ChoiceID[0] + 1;
				$ChoiceID[2] = $ChoiceID[1] + 1;
				$ChoiceID[3] = $ChoiceID[2] + 1;
			
			}				
			
			// If the user is editing an existing question then set the Question Image Name to the current Question ID
			if($EditID <>'' && $EditID <> 0)
			{	
				$QImageName = $EditID;
		
			}
			
			//If the user is adding a new question then get the last question ID 
			else
			{	
				$query = "SELECT TOP 1 STTQID FROM STTQuestions_tbl order by STTQID desc ";
				$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
				$result=odbc_exec($connection,$query);
		
					while(odbc_fetch_row($result))
					{
						$LastQID = odbc_result($result, 1);	
					}
						if($LastQID == '')
						{
							//add 1 to get the new ID for the 
							$QImageName = 1;				
						}
						else
						{
							$QImageName =	 $LastQID + 1;
						}
			}
	
			function findexts ($filename) 
 			{ 
 				$filename = strtolower($filename) ; 
 				$exts = split("[/\\.]", $filename) ; 
 				$n = count($exts)-1; 
 				$exts = $exts[$n]; 
 				return $exts; 
 			} 
	
		
			
			$QImageName = $QImageName."." ;	
			$UploadName = $_FILES['Qimage']['name'];
			$target2 = "../images/STT/Questions/";	
			if($UploadName == '')
			  {
				if($QImage <> '')
				{
					$FinalQName = $QImage;
				}
				else
				{
					$FinalQName = '';	
				}
			  }
			  else
			  {
				
				$FinalQName = $target2 .$QImageName.$UploadName;
			  }
			  	
				if($_POST['0'] == 1)
				{
					
					$FinalQName = '';
					unlink($QImage);
				}
				move_uploaded_file($_FILES['Qimage']['tmp_name'], $FinalQName);
		
			$QType = $_POST['QType'];
			$Question = $_POST['Question'];
			$string1 = $_POST['string1'];
			$QEnabled = $_POST['QEnabled'];	
		
			
			
	/*echo "<script>alert('".$QEnabled."');</script>";
	if($QEnabled == 'on')
	{
	
		$QEnabled = 1;	
	}
	else if($QEnabled == '')
	{
		
		$QEnabled = 0;	
	}*/
		//if a question has been selected to be updated then do the following query
		
	
	
		if($EditID <>'' && $EditID <> 0)
		{
			
			$query = "If Exists(Select * FROM STTQuestions_tbl where STTQID = ".$EditID.")
			UPDATE STTQuestions_tbl Set STTQQuestion = '".$Question."', STTQComparisonString ='".$string1."', 
			STTQEnabled = 1, STTQImage ='".$FinalQName."',STTQQTID=".$QType."
			Where STTQID = ".$EditID."";
			
			if($QType == 3)
			{
				$a = 1;	
			}
		}
		//other wise run the normal insert statement
		else
		{
			if($QType == 3)
			{
				$a = 2;	
			}
			 $query = "INSERT INTO STTQuestions_tbl (STTQQuestion,STTQComparisonString, STTQEnabled, STTQImage, STTQQTID)
			 Values ('".$Question."','".$string1."',1,'".$FinalQName."',".$QType.")";
		}
				include('../Includes/Variables.php');
				$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
				$QAddUpdate=odbc_exec($connection,$query);
					

		
		$connection = '';
		
		//after the basic question information has been inserted into the database get the new 
		//question ID so the choices will be associated with the question 		
		$query = "SELECT TOP 1 STTQID FROM STTQuestions_tbl order by STTQID desc";
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
		$result=odbc_exec($connection,$query);
					
				while(odbc_fetch_row($result))
				{
					$QID = odbc_result($result, 1);
				}
			
				
					$connection = '';		
	
		
			
			$CNames = array();
			for($i=0; $i<=3; $i++)
			{
				
				//original file name
				$OName = $_FILES['Cimage']['name'][$i];
			
				$total = count($_FILES['Cimage']);
				
				//variable with question id and choice id extension
				
				$CImageName = $QID.$ChoiceID[$i];
				
				
				//file destination location
				$target1 = "../images/STT/Choices/";	
				
	
				//if there is no image file upload for this choice then do the following 
				if($OName == '')
				{
					
					//if thhere is a value for the Choice array then do the following
					if($ChoiceI[$i])
					{
					
						//if the element is not null then set the value of the choice photo filed to what the current path name is in the database 
						if($ChoiceI[$i] <> '')
						{
						 	
							$FinalCName = $ChoiceI[$i];
						}
						else
						{
							
							$FinalCName = '';	
						}
						
					}
					else
					{
						$FinalCName = '';	
					}
				}
				
				//if there is a image being uploaded then do the following.
				else
				{ 
					
					$FinalCName = $target1.$CImageName.$OName;
				}
				if($_POST['1'.$i] == 1)
				{
					$FinalCName = '';
					unlink($ChoiceI[$i]);
				}
	
				array_push($CNames, $FinalCName);
				
				move_uploaded_file( $_FILES['Cimage']['tmp_name'][$i], $FinalCName);
				
			
			}	
				$FullPName = $Target1.$_FILES['Cimage']['name'][$i];	
	
						
	if ($QType == 2 || $QType == 5 || $QType == 6)
	{
		
		//assigning the choice values  from the form these variables
		
		$ChoiceA = $_POST['ChoiceA'];
		$ChoiceB = $_POST['ChoiceB'];
		$ChoiceC = $_POST['ChoiceC'];
		$ChoiceD = $_POST['ChoiceD'];
		$ChoiceE = $_POST['ChoiceE'];
		
		
		

		//creates an array for all of the values inserted into each choice text field
		//holds the values for the multiple choice type questions and select all question types
		if($QType == 2 || $QType == 5)
		{
			$ChoiceValue = array($ChoiceA,$ChoiceB,$ChoiceC,$ChoiceD);
			$counter = 4;
			$cVArray = '1';
		}
		else
		{
			$ChoiceValue = array('Strongly Agree','Agree','Neutral','Disagree','Strongly Disagree');
			$counter = 5;
			$cVArray = '2';
		}
		
		
		//creating an array with the abcd values to use as comparisons for the clauses below
		$abc = array('a','b','c','d','e');
		
		//This will grab the value of the multiple choice array for the radio buttons
		$mc = $_POST['mc'][0];		
	
		
			//loops through each element in array 
			for($i=0; $i< $counter; $i++)
			{
				//if the question type is multiple choice
				if($QType == 5)
				{
					// this will check value of each check box if its on then set correct to 1
					if($_POST[$abc[$i]] == 'on')
					{
						$Correct = 1;	
					}
					else
					{
						$Correct = 0;	
					}
				}
				//if the question type is multiple choice then 
				else if($QType == 2 || $QType == 6)
				{
					//set the variable key to the current key name
					$key = key($abc);
					
						
					if($mc == 'a')
					{
						$mc = 0;	
					}
					else if ($mc == 'b')
					{
						$mc = 1;
					}
					else if ($mc == 'c')
					{
						$mc = 2;	
					}
					else if ($mc == 'd')
					{
						$mc = 3;
					}
					else if ($mc == 'e')
					{
						$mc = 4;
					}
					
					
					if($key == $mc)
					{
				
						$Correct = 1;	
					}
					else
					{
						$Correct = 0;	
					}
				}
				
			//If a question has been chosen to edit then use this query to update the choices
			if($EditID <> '' && $EditID <> 0)
			{	
				$query = "IF EXISTS(Select * FROM STTChoices_tbl where STTCQID = ".$EditID.")
				UPDATE  STTChoices_tbl set STTCText='".$ChoiceValue[$i]."', STTCImage = '".$CNames[$i]."', 
				STTCQID=".$EditID.", STTCCorrect=".$Correct." where STTCID = ".$ChoiceID[$i]."";
				$a = 1;
			}
			// othewise use this query to insert new records into the database
			else
			{
				//if a question is being added then set the variable to zero 
				
				$a = 2;
				$query = " INSERT INTO STTChoices_tbl (STTCText,STTCImage,STTCQID,STTCCorrect) 
				Values('".$ChoiceValue[$i]."','".$CNames[$i]."',".$QID.",".$Correct.")";
				
				
			}
				
				include('../Includes/Variables.php');
				$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
				$QAddUpdate=odbc_exec($connection,$query);
				
			next($abc);	
		
		}
		
		unset($EditID);
	
	}
	
		
	

	elseif($QType == 4 || $QType == 8)
	{
			
			
		
		for($i=0; $i< 2; $i++)
		{		
			$TF = $_POST['TF'];
			$lD = $_POST['lD'];
		
			if($lD == 'Like')
			{
				$text = 'Like';	
				$static = 'Disalike';
			}
			else if($lD == 'Disalike')
			{
				$text = 'Disalike';
				$static ='Like';	
			}
			else if($TF == 'T')
			{
				$text = 'True';	
				$static = 'False';	
			}
			elseif($TF == 'F')
			{
				$text = 'False';	
				$static = 'True';	
			}
	
			if($EditID <>'' && $EditID <> 0)
			{	
			 
				if($TF == 'T' || $lD == 'Like' )
				{		
					if($i == 0)
					{
						$query = "IF EXISTS(Select * FROM STTChoices_tbl where STTCQID = ".$EditID.")
						UPDATE  STTChoices_tbl  SET STTCCorrect = 1 where STTCQID =".$EditID." AND STTCText ='".$text."'";
						$a = 1;			
					}
					elseif($i == 1)
					{
						$query = "IF EXISTS(Select * FROM STTChoices_tbl where STTCQID = ".$EditID.")
						UPDATE  STTChoices_tbl  SET STTCCorrect = 0 where STTCQID =".$EditID." AND STTCText ='".$static."'";
						$a = 1;	
					}
					
				}
				else if ($TF == 'F' || $lD == 'Disalike' )
				{
					if($i == 0)
					{
						$query = "IF EXISTS(Select * FROM STTChoices_tbl where STTCQID = ".$EditID.")
						UPDATE  STTChoices_tbl SET STTCCorrect = 1 where STTCQID =".$EditID."  AND STTCText ='".$text."'";
						$a = 1;
					}
					elseif($i == 1)
					{	
						$query = "IF EXISTS(Select * FROM STTChoices_tbl where STTCQID = ".$EditID.")
						UPDATE  STTChoices_tbl SET  STTCCorrect = 0 where STTCQID =".$EditID." AND STTCText ='".$static."'";
						$a = 1;
					}
					
				}
				
			}
			
			else
			{
				if($TF == 'T' || $lD == 'Like')
				{
					$query = "INSERT INTO STTChoices_tbl (STTCText,STTCQID,STTCCorrect,STTCImage) values ('".$text."',".$QID.",1,'')
							INSERT INTO STTChoices_tbl (STTCText,STTCQID,STTCCorrect,STTCImage) values ('".$static."',".$QID.",0,'')";
					$a = 2;	
				}
				else if ($TF =='F' || $lD == 'Disalike')
				{
					$query =	"INSERT INTO STTChoices_tbl (STTCText,STTCQID,STTCCorrect,STTCImage) values ('".$static."',".$QID.",0,'')
								INSERT INTO STTChoices_tbl (STTCText,STTCQID,STTCCorrect,STTCImage) values ('".$text."',".$QID.",1,'')";	
					$a = 2;
				}
			}
			
			$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
			if(!$connection)
			{
				
			}
			
			$QAddUpdate=odbc_exec($connection,$query);
			
		}
	}

		unset($EditID);
	}

	
	
	
	if(isset($_POST['DisableQ']))
	{
		echo "<script>alert('how');</script>";
		//include('../Includes/Variables.php');
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
		
	
		$count = $_POST['count'];	
		
		for($i=0; $i<=$count; $i++)
		{
			if(isset($_POST['question'.$i.''] ))
			{
			
				$Ques = $_POST['question'.$i.''];
				
				
				//Gets the type of question thats being deleted
				/*$query = "Select STTQQTID from STTQuestions_tbl WHERE STTQID = ".$Ques."";
				$TypeSearch=odbc_exec($connection,$query);
				while(odbc_fetch_row($TypeSearch))
				{
					$type = odbc_result($TypeSearch, 1);
				}*/
				
				//Grabs the image name and deletes it from the server
				/*if($type == 2 || $type == 5)
				{
					
					$query = "Select STTCImage from STTChoices_tbl where STTCQID = ".$Ques." ";
					$CIName=odbc_exec($connection,$query);
					while(odbc_fetch_row($CIName))
					{
						$ChoiceImage = odbc_result($CIName, 1);
						unlink($ChoiceImage);
					}
				}*/
				
				
				
				/*$query = "Select STTQImage from STTQuestions_tbl where STTQID = ".$Ques." ";
				$QIName=odbc_exec($connection,$query);
				while(odbc_fetch_row($QIName))
				{
					$QName= odbc_result($QIName, 1);
					unlink($QName);
				}*/
				
				
				/*//deletes all question records from question table selected
				$query = "DELETE FROM STTChoices_tbl where STTCQID = ".$Ques."";
				$DeleteChoices=odbc_exec($connection,$query);*/
				
				//deletes all choices from choices table selected
				/*$query = "DELETE FROM STTQuestions_tbl where STTQID = ".$Ques."";
				$DeleteQuestion=odbc_exec($connection,$query);	
				if($DeleteQuestion)*/
				$query = "UPDATE STTQuestions_tbl SET STTQEnabled = 0 WHERE STTQID = ".$Ques."";
				
				$DisableQuestion=odbc_exec($connection,$query);
				
			}
		}

			$connection = null;
	}
	else if(isset($_POST['EnableQ']))
	{
		include('../Includes/Variables.php');
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
		
		
		$count = $_POST['count'];
		
		for($i=0; $i<=$count; $i++)
		{
			if(isset($_POST['question'.$i.''] ))
			{
				$Ques = $_POST['question'.$i.''];	
			
		
		
		
		
		$query = "UPDATE STTQuestions_tbl SET STTQEnabled = 1 WHERE STTQID = ".$Ques."";
		$EnableQuestion=odbc_exec($connection,$query);	
			}
		}
	}
// ***************************** End Functions ****************************************
?>

<!-- ***************************** Begin JavaScript ************************************* -->

<!-- ***************************** End JavaScript ************************************* -->
<script  type="text/JavaScript">
function resizeFrame(h)
{
	document.getElementById('submain_tbl').style.height = h+"px";
	document.getElementById('iframe_main').style.height = h+"px";
}
function tableResize()
{
	document.getElementById('main_tbl').style.height = window.innerHeight*".96"+"px";
}
</script>

<html>
<head>
<title>STT - Questions</title>
<link rel="stylesheet" type="text/css" href="http://www.ferris.edu/stylesheets/sitepage.css" />
<link rel="stylesheet" type="text/css" href="../CSS/style.css" />


</head>
<link rel="shortcut icon" href="http://fsudboard2.ferris.edu/home/favicon.ico" />
<body onload="tableResize()">         
<?php include("../Includes/navpanel.inc.php"); ?>
 
<div id="status" style="position: absolute; top: 36%; left: 22%;" ></div>
<div id="content">
<table id="main_tbl" cellspacing="0" cellpadding="0" align="center">
	<?php include("../Includes/pagebanner.inc.php"); ?>
    <tr height="60">
        <td><?php include("../Includes/welcomelinks.inc.php"); ?></td>
    </tr>
    <tr>
        <td align="center">
       
			<?php 

				?>
                <table border=0 width="75%">
                    <tr>
                        <td>
                            <form name="frmAddQuestions" method="post"  action="STTQuestions.php" enctype="multipart/form-data">
                                <table border=0 >
                                	<tr>
                                    	<td align="center">
                      					
                                    	<td>
                                  	</tr>
                                    <tr>
                                        <?php if($EditID <> 0){?>
                                        <td colspan="0" align="center"><h1>Editing Question</h1></td>
                                        <?php }else{?>
                                        <td colspan="0" align="center"><h1>Add New Questions</h1></td>
                                        <?php }?>
                                    </tr>
                              
									
										<td size="100%">
                                        <b>Question Type</b>
											<select name="QType" id="QType" onChange='ChangeQType(this.value); tableResize();'>
												<?php 
												if($QType1 <> '')
												{
													echo"<option value='".$QTypeID."'>".$QType1."</option>";
												}
                                                else
											    {
													echo"<option value=''>Select a Question Type</option>";
                                                
												}
													//include('../Includes/Variables.php');
													$conn = odbc_connect($connection_string, $sqlRUser, $sqlRpassword) or die('Error connecting to mysql');
													$query = "SELECT STTQTID,STTQType from STTQType_tbl where STTQTEnabled = 1";
													$result=odbc_exec($conn, $query);
													while(odbc_fetch_row($result))
													{
														$STTQTID = odbc_result($result, 1);
														$STTQType = odbc_result($result, 2);
													
														echo "<option value=".$STTQTID.">".$STTQType."</option>";
													}	
													$conn = null;
												?>
											</select>
										</td>
									</tr>
									<tr>
										<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
										<?php 
												if($Question1 == '')
												{
													echo" <td id='s1'><b>Question</b> <input type='text' size='30' value='' name='Question'/></td> ";
												}
												else
												{
											  		echo" <td id='s1'><b>Question</b> <input type='text' size='30' value='".$Question1."' name='Question'/></td>";
											 	}
										?>
                                        <br/>
                                        <div id='openEndedNote'></div>	
									</tr>
									<tr >
										<td id="s2"></td>
                                    </tr>
                                    <tr>
										<td id="staticQImage"><b>Question Image</b> <input name="Qimage" type="file" /></td>
									</tr>
									<tr id="tr1">
										<td colspan=3 style="border-Top:solid medium #cc0033"></td>
									</tr>
<!-- 									This will change based on what question type the user selects -->
									</table>
                             
								<table> 
                              <?php 
							  if($EditID > 0 && !isset($_POST['AddQuestion']))
							  {
								  ?>
								
								<input type="hidden" name="0" id="0" value="0"/>
                                <input type="hidden" name="10" id="11" value="0"/>
                                <input type="hidden" name="11" id="12" value="0"/>
                                <input type="hidden" name="12" id="13" value="0"/>
                                <input type="hidden" name="13" id="14" value="0"/>
                                <input type="hidden" name="EditID" value="<?php echo $EditID;?>"/>
                                
                               <?php 
							  }
							   ?>
                                <div id="mc"></div><br/>
                               </table>
                             	
                                    <table>
                                    <tr>
                                        <!--<td align="right"><h5>Question Enabled</h5></td>-->
                                        <?php 
											/*if($QEnabled == 0)
											{
												echo" <td><input type='checkbox'  Name='QEnabled'/></td>";
											}
											else
											{
                                        		echo" <td><input type='checkbox'  Name='QEnabled' checked/></td>";
											}*/
										?>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td><input type="hidden" id="Update" value="0"/></td>
                                        <td><input type="submit" class="button" Value="Add/Update" name="AddQuestion" onClick="ChangeUpdateValue()"/></td>
                                        <?php   if($EditID <> 0 && (!isset($_POST['AddQuestion'])))
												{
													 echo "<td><input type='submit' class='button' Value='Cancel' onClick='redir();'/></td>";
												}
												
										?>
                                        <td>
                                    </tr>
                                </table>
                            </form>	
                        </td>
                        <td width="25" style="border-right:solid medium #cc0033">
                        </td>
                        <td width="25">
                        </td>
                        <td>
                        <div style="overflow:scroll;height:600px;overflow:auto">
                            <form name="frmUpdateQuestions" method="post" action="STTQuestions.php">
                                <table Border=0 width="100%"  cellpadding="2" cellspacing="0px">
                                    <tr>
                                        <td colspan="7" align="center"><h1>Edit Questions</h1></td>
                                    </tr>
                                    <tr>
                                   
                                        <td align="center"><h5>Question</h5></td>
                                        <td align="center"><h5>Type</h5></td>
                                       	<td align="center"><h5>Enabled</h5></td>
                                        <td align="center"><h5>Disable</h5></td>
                                    
                                    </tr>
                                  <?php
		
			include('../Includes/Variables.php');

			$connection = odbc_connect($connection_string, $sqlRUser, $sqlRpassword);
				$query = "SELECT STTQType,STTQID,STTQEnabled,STTQQuestion FROM STTQuestions_tbl LEFT JOIN STTQType_tbl on STTQQTID = STTQTID";
				
				$result=odbc_exec($connection,$query);
				$i = 0;
				
				$enabledQIDArray = array();
				$enabledQType = array();
				$enabledQEnabled = array();
				$enabledQuestion = array();
				
				$disabledQIDArray = array();
				$disabledQType = array();
				$disabledQEnabled = array();
				$disabledQuestion = array();
				
				while(odbc_fetch_row($result))
				{
					$Type = odbc_result($result, 1);
					$QID = odbc_result($result, 2);
					$TEnabled = odbc_result($result, 3);
					$Question = odbc_result($result, 4);
					
					if($TEnabled == 1)
					{
						
						array_push($enabledQIDArray, $QID);
						array_push($enabledQType, $Type);
						array_push($enabledQEnabled, $TEnabled);
						array_push($enabledQuestion, $Question);
					}
					elseif($TEnabled == 0)
					{
						array_push($disabledQIDArray, $QID);
						array_push($disabledQType, $Type);
						array_push($disabledQEnabled, $TEnabled);
						array_push($disabledQuestion, $Question);
						
					}
				}		
					$countEnabled = count($enabledQIDArray);
					$countDisabled = count($disabledQIDArray);
					
					
					
					for($i=0; $i< $countEnabled; $i++)
					{
						 echo "<tr>
						 		<td align='center'><a href='?EditID=".$enabledQIDArray[$i]."'>".$enabledQuestion[$i]."</a></td>
						 		<td align='center'>".$enabledQType[$i]."</td>
								<td align='center'>Yes</td>
					     		<td align='center'><input type='checkbox' name='question".$i."' value=".$enabledQIDArray[$i]."></td>
							   </tr>";
						 
					} 
					
                     echo"
					 	<br/>
					 	<tr>
							<td>
							
							<td></td><td></td><td align='right'><input type='submit' class='button' value='Disable'  name='DisableQ'/></td>
							
							</td>
						</tr>
						<tr bgcolor='#E7E7E7' >
							<td>&nbsp;</td>
							<td align='left' width='35%'><div id='showHide' onclick='hideshow(adiv)'><b >Show Disabled<b/></div></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</table>
						
						
						 <table id='adiv' width='100%' style='display: none;'>
						 
									   <tr>
                                   
                                        <td align='center'><h5>&nbsp;&nbsp;&nbsp;&nbsp;</h5></td>
                                        <td align='center'><h5>&nbsp;&nbsp;&nbsp;&nbsp;</h5></td>
                                       	<td align='center'><h5>&nbsp;&nbsp;&nbsp;&nbsp;</h5></td>
                                        <td align='center'><h5>&nbsp;&nbsp;&nbsp;&nbsp;</h5></td>
                                    
                                    </tr>";
						
						
					
					$b = 0;
					while($b< $countDisabled)
					{			
						echo"
							<tr > 
						 			<td></td><td width='36%' align='center'><a href='?EditID=".$disabledQIDArray[$b]."'>".$disabledQuestion[$b]."</a></td>
						 			<td></td><td width='30%' align='center'>".$disabledQType[$b]."</td>
									<td></td><td align='right' width='70%'>No</td>
							    	<td></td><td></td><td></td><td></td><td align='center' width='50%' ><input type='checkbox' name='question".$b."' value=".$disabledQIDArray[$b]."></td>
									
							</tr>";
					$b++;
					}
				 	if($countDisabled <> 0)
					{
								echo "<tr><td></td><td></td><td></td><td></td><td></td>
								<td></td><td></td><td></td><td></td><td></td><td align='right'>
								<input type='submit' class='button' value='Enable'  name='EnableQ'/></td></tr>";
					}
					else
					{
						echo "<tr>
								<td></td><td width='100%' align='center'>There are no disabled questions</td></tr>";	
					}
		
					
					$count = $b + $i;
					
					echo "<tr><td><input type='hidden' name='count' value=".$count."></td></tr>";
				
		?>             </table>   
                               
                        
                         </div>
                     
                        <!-- <div style="position: relative; top: 0px; left: 84%;"><input type="submit" class="button" value="Delete"  name="DeleteQ"/></div>-->
                        </td>
                    </tr>
                    <!--<tr>
                    <td></td><td></td><td></td><td align="right"><input type="submit" class="button" value="Delete"  name="DeleteQ"/></td>
					</tr>-->
                </table>
                 <table><tr><td><div id="Itable"></div></td></tr></table>   
                 <?php 
	
		//this block of code will produce the status of the users request	
		
		//if the query was successful
		if($QAddUpdate)
		{
				echo "<script>
				
				function start(a)
				{
					document.getElementById('status').innerHTML = a;
						
						function good()
						{
							document.getElementById('status').innerHTML='';
							
						}
						setTimeout(good, 2000);
				}
				</script>";
				
				//if the user updated the question
				if($a == 1 )
				{
					$a = '<font color="#009F00">Question Updated Successfuly</font>';
					echo "<script>
							start('".$a."');
						  </script>";
					
				}
				//if the user added a question
				elseif($a == 2)
				{
					$a = '<font color="#009F00">Question Added Successfuly</font>';	
					echo "<script>
							start('".$a."');
						  </script>";
				}
			
		}
		else
		{
			
			//if the question update failed
			if($a == 1 )
			{
				$a = '<font color="#D40000">Question Update Failed</font>';
				echo "<script>
						start('".$a."');
					  </script>";
					
				}
				//if the question add failed
				elseif($a == 2)
				{
					$a = '<font color="#D40000">Question Add Failed</font>';	
					echo "<script>
							start('".$a."');
						  </script>";
				}
		
		}
		
		
				
?>
  </form>
                <?php 
				if(!isset($_POST['AddQuestion']))
				{
					echo 
					 "<script type='text/javascript'>
					
						ChangeQType(".$QTypeID.");
							
										
					</script>";
				 }
				 else
				?>
              
            <?php
           // }
            ?>
     	</td>
   	</tr>
    <?php include("../Includes/FSUfooterlinks.inc.php"); ?>
</table>
</div>
</body>
</html>
