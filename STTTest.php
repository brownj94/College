<?php 
	session_start();
	include('../Includes/Variables.php');
	include('../Includes/Functions.php');
	if($_GET['ID'])
	{
		$_SESSION['TestID'] = $_GET['ID'];
	}
	$uid = $_SESSION['UID'];
	$pagename = "the <br /> Student Tech Test Page";
	$username = $_SESSION['username'];
	function RedirToResults($TIID)
	{
		?>
		<script language=javascript>
			setTimeout("location.href='STTStudentResults.php?TIID=<?php echo $TIID; ?>'",2000);
		</script>
		<?php
	}
	function SetSectionTimer($CurSec)
	{
		include('../Includes/Variables.php');
		$conn = odbc_connect($connection_string, $sqlRUser, $sqlRpassword);
		$query = "select STTSecHour, STTSecMinute FROM STTSection_tbl WHERE STTSectionID = ".$_SESSION['Sec'.$CurSec.'ID']."";
		$result=odbc_exec($conn, $query) or die(odbc_errormsg());
			
		while(odbc_fetch_row($result))
		{
			$SecHour = odbc_result($result, 1);
			$SecMinute = odbc_result($result, 2);
		}
		$totalTime = (($SecHour * 60) * 60) + $SecMinute * 60;
		echo "<div style='position:fixed; right: 11%; top: 20%;'><b>Time Left:</b></div>";
		echo "<div id='test' style='position:fixed; right: 5%; top: 20%;'></div>";
		?>
		<script>
			
			var Timer;
			var TotalSeconds;
			function CreateTimer(Time) 
			{
				Timer = document.getElementById("test");
				TotalSeconds = Time;
				UpdateTimer();
				window.setTimeout("Tick()", 1000);
			}
			function Tick() 
			{
				if (TotalSeconds <= 0) 
				{
					document.getElementById("Test1").submit();
					return;
				}
				TotalSeconds -= 1;
				UpdateTimer();
				window.setTimeout("Tick()", 1000);
			}
			function UpdateTimer() 
			{	
				var Seconds = TotalSeconds;
				var Days = Math.floor(Seconds / 86400);
				Seconds -= Days * 86400;
				var Hours = Math.floor(Seconds / 3600);
				Seconds -= Hours * (3600);
				var Minutes = Math.floor(Seconds / 60);
				Seconds -= Minutes * (60);
				var TimeStr = ((Days > 0) ? Days + " days " : "") + LeadingZero(Hours) + ":" + LeadingZero(Minutes) + ":" + LeadingZero(Seconds)
				Timer.innerHTML = TimeStr;
				
				$(function () {
				  $('#HourTaken').val(Hours);
				  $('#MinTaken').val(Minutes);
				  $('#SecTaken').val(Seconds);
				});
				//document.getElementById("HourTaken").value=Hours;
				//document.getElementById("MinTaken").value=Minutes;
				//document.getElementById("SecTaken").value=Seconds;
			}
			function LeadingZero(Time) {
			return (Time < 10) ? "0" + Time : + Time;
			}
			CreateTimer("<?php echo $totalTime;?>");
			
		</script>
			<?php
	}
	function disableCutCopyPaste($InputName)
	{
		
	}
	function GetSectionIDs()
	{
		include('../Includes/Variables.php');
		//If a test has been selected then get the ID for the test and place it into the query below
		$conn = odbc_connect($connection_string, $sqlRUser, $sqlRpassword);
		$query = "select STTSectionID,STTSecIsAlikeDisalike FROM STTSection_tbl WHERE STTSecSTTTID = ".$_SESSION['TestID']."";
		$result=odbc_exec($conn, $query) or die(odbc_errormsg());
		$i=1;
		while(odbc_fetch_row($result))
		{
			if($i==1)
			{
				$_SESSION['Sec1ID'] = odbc_result($result, 1);
				$_SESSION['Sec1AD'] = odbc_result($result, 2);
				$_SESSION['Sections']=1;
			}
			Elseif($i==2)
			{
				$_SESSION['Sec2ID'] = odbc_result($result, 1);
				$_SESSION['Sec2AD'] = odbc_result($result, 2);
				$_SESSION['Sections']=2;
			}
			Elseif($i==3)
			{
				$_SESSION['Sec3ID'] = odbc_result($result, 1);
				$_SESSION['Sec3AD'] = odbc_result($result, 2);
				$_SESSION['Sections']=3;
			}
			Elseif($i==4)
			{
				$_SESSION['Sec4ID'] = odbc_result($result, 1);
				$_SESSION['Sec4AD'] = odbc_result($result, 2);
				$_SESSION['Sections']=4;
			}
			Elseif($i==5)
			{
				$_SESSION['Sec5ID'] = odbc_result($result, 1);
				$_SESSION['Sec5AD'] = odbc_result($result, 2);
				$_SESSION['Sections']=5;
			}
			$i++;
		}
	}
	function GenerateMultiChoice($QuestionNumber,$qText,$qImage,$qID)
	{
		include('../Includes/Variables.php');
		?>
		<tr>
			<td align='right'>
				<?php echo $QuestionNumber; ?>) Question:
				
			</td>
			<td colspan='2'>
				<?php echo $qText ; ?>
			</td>
		</tr>
		<?php
		if($qImage<>'')
		{
		?>
			<tr>
				<td valign="top">
					Question Image:
				</td>
				<td colspan="2" align="center">
					<img src='<?php echo $qImage;?>' width='200' height='200'>
				</td>
			</tr>	
		<?php
		}
		$query2 = " SELECT STTCText, STTCImage, STTCID FROM STTChoices_tbl where STTCQID =".$qID;
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
		$result2=odbc_exec($connection,$query2);
		$i=0;
		while(odbc_fetch_row($result2))
		{
			$CText = odbc_result($result2, 1);	
			$CImage = odbc_result($result2, 2);	
			$ChoiceID = odbc_result($result2, 3);
			if($i==0)
			{
				echo "<tr><td></td><td><input type='radio' name='Question".$QuestionNumber."' value='".$ChoiceID."'>".$CText;
				if($CImage<>'')
				{
					echo "<br /><img src='".$CImage."'width='200' height='200'>";
				}
				echo "</td>";
				$i=1;
			}
			else
			{
				echo "<td><input type='radio' name='Question".$QuestionNumber."' value='".$ChoiceID."'>".$CText;
				if($CImage<>'')
				{
					echo "<br /><img src='".$CImage."' width='200' height='200'>";
				}
				echo "</td></tr>";
				$i=0;
			}
		}
		// extra row is for spacing between questions.
		echo "<tr><td><br /></td></tr>";
	}
	function GenerateOpenEnded($QuestionNumber,$qText,$qImage,$qID)
	{
		include('../Includes/Variables.php');
		?>
		<tr>
			<td align='right'>
				<?php echo $QuestionNumber; ?>) Question:
			</td>
			<td colspan='2'>
				<?php echo $qText ; ?>
			</td>
		</tr>
		<?php
		if($qImage<>'')
		{
		?>
			<tr>
				<td valign="top">
					Question Image:
				</td>
				<td  colspan="2" align="center">
					<img src='<?php echo $qImage;?>' width='200' height='200'>
				</td>
			</tr>	
		<?php
		}
			echo "<tr><td></td><td colspan='2'><textarea id='Question".$QuestionNumber."' name='Question".$QuestionNumber."'  rows='5' cols='50'></textarea></td></tr>";
			?>
				<script type="text/javascript">
				$("#TestMain").validate();
				$( "#Question<?php echo $QuestionNumber; ?>" ).rules( "add", {
											textareaComment:true, 
											messages: {
												textareaComment:"<font color='red'>Please only use Letters, Numbers and Basic Punctuation.</font>" 
												}
											});	
				
				</script>
			<?php
		// extra row is for spacing between questions.
		echo "<tr><td><br /></td></tr>";
	}
	function GenerateTrueFalse($QuestionNumber,$qText,$qImage,$qID)
	{
		include('../Includes/Variables.php');
		?>
		<tr>
			<td align='right'>
				<?php echo $QuestionNumber; ?>) Question:
			</td>
			<td colspan='2'>
				<?php echo $qText ; ?>
			</td>
		</tr>
		<?php
		
		if($qImage<>'')
		{
		?>
			<tr>
				<td valign="top">
					Question Image:
				</td>
				<td colspan="2" align="center">
					<img src='<?php echo $qImage;?>' width='200' height='200'>
				</td>
			</tr>	
		<?php
		}
		$query2 = " SELECT STTCText, STTCImage, STTCID FROM STTChoices_tbl where STTCQID =".$qID;
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
		$result2=odbc_exec($connection,$query2);
		$i=0;
		while(odbc_fetch_row($result2))
		{
			$CText = odbc_result($result2, 1);	
			$CImage = odbc_result($result2, 2);	
			$ChoiceID = odbc_result($result2, 3);
			if($i==0)
			{
				echo "<tr><td></td><td><input type='radio' name='Question".$QuestionNumber."' value='".$ChoiceID."'>True";
				$i++;
			}
			else
			{
				echo "<td><input type='radio' name='Question".$QuestionNumber."' value='".$ChoiceID."'>False</td></tr>";
			}
		
		}
		
		// extra row is for spacing between questions.
		echo "<tr><td><br /></td></tr>";	
	}
	function GenerateSelectAll($QuestionNumber,$qText,$qImage,$qID)
	{
		include('../Includes/Variables.php');
		?>
		<tr>
			<td align='right'>
				<?php echo $QuestionNumber; ?>) Question:
			</td>
			<td colspan='2'>
				<?php echo $qText ; ?>
			</td>
		</tr>
		<?php
		if($qImage<>'')
		{
		?>
			<tr>
				<td valign="top">
					Question Image:
				</td>
				<td colspan="2" align="center">
					<img src='<?php echo $qImage;?>' width='200' height='200'>
				</td>
			</tr>	
		<?php
		}
		$query2 = " SELECT STTCText, STTCImage, STTCID FROM STTChoices_tbl where STTCQID =".$qID;
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
		$result2=odbc_exec($connection,$query2);
		$i=0;
		while(odbc_fetch_row($result2))
		{
			$CText = odbc_result($result2, 1);	
			$CImage = odbc_result($result2, 2);	
			$ChoiceID = odbc_result($result2, 3);
			if($i==0)
			{
				echo "<tr><td></td><td><input type='Checkbox' name='Question".$QuestionNumber."[]' value='".$ChoiceID."'>".$CText;
				if($CImage<>'')
				{
					echo "<br /><img src='".$CImage."'width='200' height='200'>";
				}
				echo "</td>";
				$i=1;
			}
			else
			{
				echo "<td><input type='Checkbox' name='Question".$QuestionNumber."[]' value='".$ChoiceID."'>".$CText;
				if($CImage<>'')
				{
					echo "<br /><img src='".$CImage."' width='200' height='200'>";
				}
				echo "</td></tr>";
				$i=0;
			}
		}
		// extra row is for spacing between questions.
		echo "<tr><td><br /></td></tr>";
	}
	function GenerateScaled($QuestionNumber,$qText,$qImage,$qID)
	{
		include('../Includes/Variables.php');
		?>
		<tr>
			<td align='right'>
				<?php echo $QuestionNumber; ?>) Question:
			</td>
			<td colspan='2'>
				<?php echo $qText ; ?>
			</td>
		</tr>
		<?php
		$query2 = " SELECT STTCText, STTCImage, STTCID FROM STTChoices_tbl where STTCQID =".$qID;
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
		$result2=odbc_exec($connection,$query2);
		$i=1;
		while(odbc_fetch_row($result2))
		{
			$CText = odbc_result($result2, 1);	
			$CImage = odbc_result($result2, 2);	
			$ChoiceID = odbc_result($result2, 3);
			if($CText=='Strongly Agree')
			{
				echo "<tr><td></td><td colspan='2'><table cellspacing=10><tr><td align='center'>".$CText."<br /><input type='Radio' name='Question".$QuestionNumber."' value='".$ChoiceID."'></td>";
				
			}
			elseif($CText=='Strongly Disagree')
			{
				echo "<td align='center'>".$CText."<br /><input type='radio' name='Question".$QuestionNumber."' value='".$ChoiceID."'></td></tr></table></tr>";
			}
			else
			{
				echo "<td align='center'>".$CText."<br /><input type='radio' name='Question".$QuestionNumber."' value='".$ChoiceID."'></td>";
				
			}
		}
		// extra row is for spacing between questions.
		echo "<tr><td><br /></td></tr>";
	}
	function GenerateComparison($QuestionNumber,$qText,$qImage,$qID)
	{
		include('../Includes/Variables.php');
		?>
		<tr>
			<td align='right'>
				<?php echo $QuestionNumber; ?>) Retype the Following:
			</td>
			<td colspan='2'>
				<?php echo $qText ; ?>
			
		
		<?php
			echo "<input type='Text' id='Question".$QuestionNumber."' name='Question".$QuestionNumber."' size=50></td></tr>";
			$DisableMe = 'Question'.$QuestionNumber;
			disableCutCopyPaste($DisableMe);
			?>
				<script type="text/javascript">
				$("#TestMain").validate();
				$( "#Question<?php echo $QuestionNumber; ?>" ).rules( "add", {
											textComparison:true, 
											messages: {
												textComparison:"<font color='red'>Please only use Letters, Numbers or Dashes.</font>" 
												}
											});	
				$('#Question<?php echo $QuestionNumber; ?>').bind("cut copy paste",function(e) {
															  e.preventDefault();
														  });
				</script>
			<?php
		// extra row is for spacing between questions.
		echo "<tr><td><br /></td></tr>";
	}
	function GenerateLikeDisalike($QuestionNumber,$qText,$qImage,$qID,$qCompString)
	{
		include('../Includes/Variables.php');
		?>
		<tr>
			<td align='right'>
				<?php echo $QuestionNumber; ?>) Question:
			</td>
			<td colspan='2'>
				<?php echo $qText ;?> &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; <?php echo $qCompString ;?>
			</td>
		</tr>
		<?php
			$query2 = " SELECT STTCText, STTCImage, STTCID FROM STTChoices_tbl where STTCQID =".$qID;
			$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
			$result2=odbc_exec($connection,$query2);
			$i=0;
			while(odbc_fetch_row($result2))
			{
				$CText = odbc_result($result2, 1);	
				$CImage = odbc_result($result2, 2);	
				$ChoiceID = odbc_result($result2, 3);
				if($i==0)
				{
					echo "<tr><td></td><td><input type='Radio' name='Question".$QuestionNumber."' Value='".$ChoiceID."'>Alike";
					$i++;
				}
				else
				{
					echo "<br /><input type='Radio' name='Question".$QuestionNumber."' value='".$ChoiceID."'>Disalike";
				}
				
			}
			
			
		// extra row is for spacing between questions.
		echo "<tr><td><br /></td></tr>";
	}
	function GenerateLikeDisalikeSection($QuestionNumber,$qText,$qImage,$qID,$qCompString)
	{
		include('../Includes/Variables.php');
		?>
		<tr>
			<td align='right'>
				<?php echo $QuestionNumber; ?>)
			</td>
			<td align=center style="border-right:solid medium #cc0033">
				<?php echo $qText ;?>
			</td>
			<td align=center>
				<?php echo $qCompString ;?>
			</td>
			<td align='center'>
			<?php
				$query2 = " SELECT STTCText, STTCImage, STTCID FROM STTChoices_tbl where STTCQID =".$qID;
				
				$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
				$result2=odbc_exec($connection,$query2);
				$i=0;
				while(odbc_fetch_row($result2))
				{
					$CText = odbc_result($result2, 1);	
					$CImage = odbc_result($result2, 2);	
					$ChoiceID = odbc_result($result2, 3);
					if($i==0)
					{
						echo "<input type='Radio' name='Question".$QuestionNumber."' Value='".$ChoiceID."'></td>";
						$i++;
					}
					else
					{
						echo "<td align='center'><input type='Radio' name='Question".$QuestionNumber."' value='".$ChoiceID."'>";
					}
				}
			?>
			</td>
		</tr>
	<?php
	}
	function WriteSectionInfo($SecID)
	{
		include('../Includes/Variables.php');
		$query = "Select STTSecName,STTSecDesc from STTSection_tbl where STTSectionID =".$SecID;
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
		$result=odbc_exec($connection, $query);
		while(odbc_fetch_row($result))
		{
			$SectionName = odbc_result($result, 1);
			$SectionDesc = odbc_result($result, 2);
		}
		echo"<tr><td align='center' colspan='4'><b>Section Description:</b>".$SectionDesc."</td></tr>";
		
	}

	function RecordQuestionOrder($qID,$CurSec)
	{
		include('../Includes/Variables.php');
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
			if($connection)
			{
				$query = "Insert into STTTestInstanceQuestionOrder_tbl (STTTIQOSTTTIID,STTTIQOSTTQID,STTTIQOSection) values (".$_SESSION['TestInstanceID'].",".$qID.",".$CurSec.")";
				$result=odbc_exec($connection,$query);
			}
			else
			{
				echo "connection Failed";
			}
	}
	function viewTest()
	{
		//**** Includes ******
		include('../Includes/Variables.php');
		//**** End Includes *****
		GetSectionIDs();
		
		if(!$_GET['SecChange'])
		{
			$_SESSION['CurrentSection']=1;
		}
		$CurSec = $_SESSION['CurrentSection'];
		if($_SESSION['Sec'.$CurSec.'ID']<>'')
		{
			SetSectionTimer($CurSec);
			$query = "SELECT TOP (Select s.STTSecQCount from STTSection_tbl s where s.STTSectionID =".$_SESSION['Sec'.$CurSec.'ID'].")STTQQuestion, STTQImage,STTQQTID,STTQID,STTQComparisonString FROM STTQuestions_tbl LEFT JOIN STTTQAssoc_tbl q on STTQID =q.TQAssocSTTQID WHERE STTQEnabled =1 and TQAssocSTTSecID =".$_SESSION['Sec'.$CurSec.'ID']." order by NEWID()";
			$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
			$result=odbc_exec($connection, $query);
			echo "<table align='center'  border='0'>";
			WriteSectionInfo($_SESSION['Sec'.$CurSec.'ID']);
			echo "<input type='hidden' name='SecID' id='SecID' value='".$_SESSION['Sec'.$CurSec.'ID']."'/>";
			if($_SESSION['Sec'.$CurSec.'AD']==1)
			{
				echo "<tr><td colspan='3' align='right'></td><td>Alike</td><td>Disalike</td></tr>";
				$QuestionNumber = 1;
				while(odbc_fetch_row($result))
				{
					$qText = odbc_result($result, 1);
					$qImage = odbc_result($result, 2);
					$qType = odbc_result($result, 3);
					$qID = odbc_result($result, 4);
					$qCompString = odbc_result($result,5);
					RecordQuestionOrder($qID,$CurSec);
					echo "<input type='hidden' name='Question".$QuestionNumber."ID' value=".$qID.">";
					echo "<input type='hidden' name='Question".$QuestionNumber."Type' value=".$qType.">";
					GenerateLikeDisalikeSection($QuestionNumber,$qText,$qImage,$qID,$qCompString);
					$QuestionNumber++;
				}
			}
			else
			{
				$QuestionNumber = 1;
				while(odbc_fetch_row($result))
				{
					$qText = odbc_result($result, 1);
					$qImage = odbc_result($result, 2);
					$qType = odbc_result($result, 3);
					$qID = odbc_result($result, 4);
					$qCompString = odbc_result($result,5);
					RecordQuestionOrder($qID,$CurSec);
					echo "<input type='hidden' name='Question".$QuestionNumber."ID' value=".$qID.">";
					echo "<input type='hidden' name='Question".$QuestionNumber."Type' value=".$qType.">";
					if($qType == 2) // Multiple Choice
					{
						GenerateMultiChoice($QuestionNumber,$qText,$qImage,$qID);
						echo "<tr><td colspan='3'><hr /></td></tr>";
					}
					elseif($qType == 3) // Open Ended
					{
						GenerateOpenEnded($QuestionNumber,$qText,$qImage,$qID);
						echo "<tr><td colspan='3'><hr /></td></tr>";
					}
					elseif($qType == 4) // True/False
					{
						GenerateTrueFalse($QuestionNumber,$qText,$qImage,$qID);
						echo "<tr><td colspan='3'><hr /></td></tr>";
					}
					elseif($qType == 5) // Select All That Apply
					{
						GenerateSelectAll($QuestionNumber,$qText,$qImage,$qID);
						echo "<tr><td colspan='3'><hr /></td></tr>";
					}
					elseif($qType == 6) // Scaled Questions Agree/Disagree
					{
						GenerateScaled($QuestionNumber,$qText,$qImage,$qID);
						echo "<tr><td colspan='3'><hr /></td></tr>";
					}
					elseif($qType == 7) // Comparison
					{
						GenerateComparison($QuestionNumber,$qText,$qImage,$qID);
						echo "<tr><td colspan='3'><hr /></td></tr>";
					}
					elseif($qType == 8) // Like/Disalike
					{
						GenerateLikeDisalike($QuestionNumber,$qText,$qImage,$qID,$qCompString);
						echo "<tr><td colspan='3'><hr /></td></tr>";
					}
					$QuestionNumber++;
				}
			}
		}
	echo "</table>";
	}	
							
?>
<script  type="text/JavaScript">
function tableResize()
{
	document.getElementById('main_tbl').style.height = window.innerHeight*".96"+"px";
}
</script>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="http://www.ferris.edu/stylesheets/sitepage.css" />
	<link rel="stylesheet" type="text/css" href="../CSS/style.css" />
    <link rel="shortcut icon" href="http://fsudboard2.ferris.edu/home/favicon.ico" />
    <title>STT - Test</title>

</head>

<body onLoad="tableResize()">  
       

	 
	<?php //include('../Includes/navpanel.inc.php'); ?>
<script src="http://fsudboard2.ferris.edu/tportal/JQuery/jquery-1.9.1.js"></script>
<script src="http://fsudboard2.ferris.edu/tportal/Includes/Validation/lib/jquery.js"></script>
<script src="http://fsudboard2.ferris.edu/tportal/Includes/Validation/dist/jquery.validate.js"></script>
<script src="http://fsudboard2.ferris.edu/tportal/Includes/Validation/dist/additional-methods.js"></script>
<script type="text/javascript">
$.validator.setDefaults({
	submitHandler: function() { 
								document.TestMain.submit(); 
								
								}
});

$().ready(function() {
	// validate signup form on keyup and submit
	// For help with this stuff... http://jqueryvalidation.org/documentation
	$("#TestMain").validate();
});
</script>
<div id="content">
	<table id="main_tbl" cellspacing="0" cellpadding="0" align="center" >
   
	<?php include("../Includes/pagebanner.inc.php"); ?>
		<tr height='60px'>
			<td>
				<?php include("../Includes/welcomelinks.inc.php"); ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php 
				if(isset($_GET['ID']))
				{	
					$query = "SELECT q.TQAssocSTTQID from STTTQAssoc_tbl q join STTSection_tbl s on q.TQAssocSTTSecID = s.STTSectionID join STTTest_tbl t on t.STTTID = s.STTSecSTTTID where T.STTTID =".$_GET['ID'];
					$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
					$result = odbc_exec($connection, $query);
					$rowCount = odbc_num_rows($result);
					
					if($rowCount <> 0 || $rowCount <> '')	
					{
						GetSectionIDs();
						echo"<br />
						<form id='Test1' name='Test1' action='STTTestHelper.php' method='POST' >
							<table align='center'>
									<tr>
										<td>First Name:</td><td><input type='text' name='FName' pattern='[A-Za-z]{2,16}' required/></td>
										<td> Student ID:</td><td><input type='number' name='CWID' min='10000000' max='99999999' required/></td>
									</tr>
									<tr>
										<td>Last Name:</td><td><input type='text' name='LName' pattern='[A-Za-z]{2,24}' required/></td>
										<td> Short ID:</td><td><input type='text' name='ShortID' pattern='^[A-Za-z]{3,8}[0-9]{0,3}' required/></td>
									</tr>
								
							</table>
							<br/>";
							echo" 
							<div align='center'>
								<br/>
								<br/>
								<input type='hidden' name='TestID' value=".$_GET['ID']." />
								
								<input type='submit' class='button' name='Test'  value='submit'/>
							</div>
						</form>";
					}
					else
					{
						echo" <div align='center'>No questions have been associated with this test.</div>";	
						
					}
				}
				elseif($_GET['SecChange'])
				{
					$query = "SELECT q.TQAssocSTTQID from STTTQAssoc_tbl q join STTSection_tbl s on q.TQAssocSTTSecID = s.STTSectionID join STTTest_tbl t on t.STTTID = s.STTSecSTTTID where T.STTTID =".$_SESSION['TestID'];
					$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
					$result = odbc_exec($connection, $query);
					$rowCount = odbc_num_rows($result);
					
					if($rowCount <> 0 || $rowCount <> '')	
					{
						echo"<form class='cmxform' id='TestMain' name='TestMain' action='STTTestHelper.php' method='POST' >";
							
							viewTest();
							echo" 
							<div align='center'>
								<br/>
								<br/>
								<input type='hidden' name='MinTaken' id='MinTaken' value='' />
								<input type='hidden' name='SecTaken' id='SecTaken' value='' />
								<input type='hidden' name='HourTaken' id='HourTaken' value='' />
								<input type='submit' class='button' name='Test' value='submit'/>
							</div>
						</form>";
					}
					else
					{
						echo" <div align='center'>No questions have been associated with this test.</div>";
						//echo $query;
					}
				}
				elseif($_SESSION['CurrentSection']>$_SESSION['Sections'])
				{
					
					echo "<center>Thank You for taking the Test. You will be automatically redirected to your results.<br />";
					RedirToResults($_SESSION['TestInstanceID']);
					//echo " Click <a href='LeftRight.php?TIID=".$_SESSION['TestInstanceID']."'>Here</a> to see your Results.</center>";
									
				
					unset($_SESSION['TestInstanceID']);
					unset($_SESSION['TestID']);
					unset($_SESSION['CurrentSection']);
					unset($_SESSION['Sections']);
					unset($_SESSION['Sec1ID']);
					unset($_SESSION['Sec2ID']);
					unset($_SESSION['Sec3ID']);
					unset($_SESSION['Sec4ID']);
					unset($_SESSION['Sec5ID']);
				}
				else
				{
					include('../Includes/Variables.php');
					$conn = odbc_connect($connection_string, $sqlRUser, $sqlRpassword) or die('Error connecting to mysql');
					$query = "select STTTID, STTTName, STTTDesc FROM STTTest_tbl where STTTEnabled =1";
					$result=odbc_exec($conn, $query);
						
					?>
					<table border='0' cellspacing='0'  align='center'>
						<tr>
							<td align='center'>
								<h5>Test Name</h5>
							</td>
							<td align='center'>
								<h5>Test Description</h5>
							</td>
						</tr>
						<tr>
							<td colspan='3'>
								<hr />
							</td>
						</tr>
					<?php
					
					while(odbc_fetch_row($result))
					{
						$TestID = odbc_result($result, 1);
						$Test = odbc_result($result, 2);
						$Desc = odbc_result($result, 3);
						
						
						
						if($i==1)
						{
							$bgcolor = '#F6CEE3';
							$i=0;
						}
						else
						{
							$bgcolor = 'white';
							$i=1;
						}
						?>
							<tr bgcolor='<?php echo $bgcolor; ?>'>
								<td width='200'  align='center' ><?php echo $Test; ?></td>
								<td width='600'><?php echo $Desc; ?></td>
								<td width='7px' valign='center' align='center'>
								<form action='?ID=<?php echo $TestID; ?>' method='POST'>
									<input type='submit' value='Launch' class='button'/>
								</form>
								</td>
							</tr>
						<?php
					}
					echo "</table>";
					
				}
				
				?>
			</td>
		</tr>
       
	<?php include("../Includes/FSUfooterlinks.inc.php"); ?>
	</table>
   
</div>
</body>
</html>