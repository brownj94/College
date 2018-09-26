<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	// variables
	session_start();

	$pagename = "the <br />Student Tech Test Results Page";
	$error = $_GET['error'];
	$getAnotherRoom = $_GET['getAnotherRoom'];
	$case = $_GET['case'];
	include('../Includes/Functions.php');
	include('../Includes/Variables.php');
	
		if($_SESSION['SuperAdmin'] <> 1 && GetRightsLevel(15) < 7 )
		{
			echo"
				<script language='JavaScript'>
					window.location='http://fsudboard2.ferris.edu/tportal/index.php';
				</script>";	
		}
	
	// ********************************* functions start ******************************
	function Redir()
	{
		?>
		<script language=javascript>
			setTimeout("location.href='LeftRight.php'",500);
		</script>
		<?php
	}
	function UpdateResults($query)
	{
		include('../Includes/Variables.php');
		//echo $query;
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
			if($connection)
			{
				$result=odbc_exec($connection,$query);
				if($result)
				{
					//echo "User Updated. <br /> Redirecting back to User Administration.";
					
				}
				else
				{
					//echo "User Not Updated. Review insert statement for problems.";
				}
			}
			else
			{
				echo "connection Failed";
			}
		$connection = null;
	}
	function GenerateMultiChoice($QuestionNumber,$qText,$qImage,$qID,$rChoiceID)
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
		$query2 = " SELECT STTCText, STTCImage, STTCID,STTCCorrect FROM STTChoices_tbl where STTCQID =".$qID;
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
		$result2=odbc_exec($connection,$query2);
		$i=0;
		while(odbc_fetch_row($result2))
		{
			$CText = odbc_result($result2, 1);	
			$CImage = odbc_result($result2, 2);	
			$ChoiceID = odbc_result($result2, 3);
			$Correct = odbc_result($result2, 4);
			if($i==0)
			{
				if ($ChoiceID == $rChoiceID)
				{
					echo "<tr><td></td><td><input type='radio' name='Question".$QuestionNumber."' value='".$ChoiceID."' checked>".$CText;
					if($Correct==1)
					{
						echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
					}
					else
					{
						echo "<img src='..\images\stt\incorrect.jpeg' width='20' height='20' />";
					}
				}
				else
				{	
					echo "<tr><td></td><td><input type='radio' name='Question".$QuestionNumber."' value='".$ChoiceID."'>".$CText;
					if($Correct==1)
					{
						echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
					}		
				}
				if($CImage<>'')
				{
					echo "<br /><img src='".$CImage."'width='200' height='200'>";
				}
				echo "</td>";
				$i=1;
				
			}
			else
			{
				if($ChoiceID == $rChoiceID)
				{
					echo "<td><input type='radio' name='Question".$QuestionNumber."' value='".$ChoiceID."' checked>".$CText;
					if($Correct==1)
					{
						echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
					}
					else
					{
						
						echo "<img src='..\images\stt\incorrect.jpeg' width='20' height='20' />";
					}
				}
				else
				{
					echo "<td><input type='radio' name='Question".$QuestionNumber."' value='".$ChoiceID."'>".$CText;
					if($Correct==1)
					{
						echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
					}
				}
				if($CImage<>'')
				{
					echo "<br /><img src='".$CImage."' width='200' height='200'>";
				}
				echo "</td></tr>";
				$i=0;
			}
		}

	}
	function GenerateOpenEnded($QuestionNumber,$qText,$qImage,$qID,$oResponseText)
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
			echo "<tr><td></td><td colspan='2'><Textarea name='Question".$QuestionNumber."'  rows='5' cols='50'>".$oResponseText."</TextArea></td></tr>";

	}
	function GenerateTrueFalse($QuestionNumber,$qText,$qImage,$qID,$rChoiceID)
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
		$query2 = " SELECT STTCText, STTCImage, STTCID, STTCCorrect FROM STTChoices_tbl where STTCQID =".$qID;
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
		$result2=odbc_exec($connection,$query2);
		$i=0; // Database reads True first then False so on the Zero pass we can assume that it is comparing True	
		while(odbc_fetch_row($result2))
		{
			$CText = odbc_result($result2, 1);	
			$CImage = odbc_result($result2, 2);	
			$ChoiceID = odbc_result($result2, 3);
			$Correct = odbc_result($result2, 4);
			
		
		
			if($i==0)
			{
				echo "<tr><td></td><td><input type='radio' name='Question".$QuestionNumber."' value='".$ChoiceID."' ".($ChoiceID==$rChoiceID ? 'Checked':'').">True";
				if($Correct==1)
				{
					echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
				}
				else
				{
					if($ChoiceID==$rChoiceID)
					{
						echo "<img src='..\images\stt\incorrect.jpeg' width='20' height='20' />";
					}
				}
			}
			else
			{
				echo "<td><input type='radio' name='Question".$QuestionNumber."' value='".$ChoiceID."' ".($ChoiceID==$rChoiceID ? 'Checked':'').">False";
				if($Correct==1)
				{
					echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
				}
				else
				{
					if($ChoiceID==$rChoiceID)
					{
						echo "<img src='..\images\stt\incorrect.jpeg' width='20' height='20' />";
					}
				}
			}
			$i++;
		}

	}
	function GenerateSelectAll($QuestionNumber,$qText,$qImage,$qID,$TIID)
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
		$query2 = " SELECT STTCText, STTCImage, STTCID,STTCCorrect FROM STTChoices_tbl where STTCQID =".$qID;
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
		$result2=odbc_exec($connection,$query2);
		$i=0;
		while(odbc_fetch_row($result2))
		{
			$CText = odbc_result($result2, 1);	
			$CImage = odbc_result($result2, 2);	
			$ChoiceID = odbc_result($result2, 3);
			$Correct = odbc_result($result2, 4);
			
			
			$query3 = "Select STTResponseID from STTResponse_tbl where STTRQID = ".$qID." and STTRCID = ".$ChoiceID." and STTTestInstanceID = ".$TIID;
			$result3=odbc_exec($connection,$query3);
			
			while(odbc_fetch_row($result3))
			{
				$RID = odbc_result($result3, 1);
			}
			if($i==0)
			{
				if($RID <> '')
				{
					echo "<tr><td></td><td><input type='Checkbox' name='Question".$QuestionNumber."[]' value='".$ChoiceID."' checked>".$CText;
					echo ($Correct==1 ? "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />" : "<img src='..\images\stt\incorrect.jpeg' width='20' height='20' />");
				}
				else
				{
					echo "<tr><td></td><td><input type='Checkbox' name='Question".$QuestionNumber."[]' value='".$ChoiceID."'>".$CText;
					echo ($Correct==1 ? "<img src='..\images\stt\Correct.jpeg' width='20' height='20' /><img src='..\images\stt\incorrect.jpeg' width='20' height='20' />" : '');
				}
				if($CImage<>'')
				{
					echo "<br /><img src='".$CImage."'width='200' height='200'>";
				}
				echo "</td>";
				$i=1;
			}
			else
			{
				if($RID <> '')
				{
					echo "<td><input type='Checkbox' name='Question".$QuestionNumber."[]' value='".$ChoiceID."' checked>".$CText;
					echo ($Correct==1 ? "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />" : "<img src='..\images\stt\incorrect.jpeg' width='20' height='20' />");
				}
				else
				{
					echo "<td><input type='Checkbox' name='Question".$QuestionNumber."[]' value='".$ChoiceID."' >".$CText;
					echo ($Correct==1 ? "<img src='..\images\stt\Correct.jpeg' width='20' height='20' /><img src='..\images\stt\incorrect.jpeg' width='20' height='20' />" : '');
				}
				if($CImage<>'')
				{
					echo "<br /><img src='".$CImage."' width='200' height='200'>";
				}
				echo "</td></tr>";
				$i=0;
			}
			$RID=''; // resets the response id field to make sure that it can be used on the next loop with no data.
		}
	
	}
	function GenerateScaled($QuestionNumber,$qText,$qImage,$qID,$rChoiceID)
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
		$query2 = " SELECT STTCText, STTCImage, STTCID, STTCCorrect FROM STTChoices_tbl where STTCQID =".$qID;
		
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
		$result2=odbc_exec($connection,$query2);
		$i=1;
		while(odbc_fetch_row($result2))
		{
			$CText = odbc_result($result2, 1);	
			$CImage = odbc_result($result2, 2);	
			$ChoiceID = odbc_result($result2, 3);
			$Correct = odbc_result($result2, 4);
		
			if($CText=='Strongly Agree')
			{
				$query3 = "SELECT STTCText FROM STTChoices_tbl where STTCQID =".$qID ." and STTCCorrect = 1";
				$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
				$result3=odbc_exec($connection,$query3);
				while(odbc_fetch_row($result3))
				{
					$CorrectText = odbc_result($result3, 1);	
				}
				
				
				echo "<tr><td></td><td colspan='2'><table cellspacing=10><tr><td align='center'>".$CText."<br /><input type='Radio' name='Question".$QuestionNumber."' value='".$ChoiceID."' ".($ChoiceID==$rChoiceID ? 'checked' : '').">";
				if($ChoiceID==$rChoiceID)
				{
					if($Correct==1)
					{
						echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
					}
					else
					{
						if($CorrectText == 'Agree')
						{
							echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
						}
						else
						{
							echo "<img src='..\images\stt\Incorrect.jpeg' width='20' height='20' />";
						}
					}
				}
				else
				{
					if($Correct==1)
					{
						echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
						if($rChoiceID=="")
						{
							echo "<img src='..\images\stt\Incorrect.jpeg' width='20' height='20' />";
						}
					}
				}
				echo "</td>";
			}
			elseif($CText=='Agree')
			{
				echo "<td align='center'>".$CText."<br /><input type='radio' name='Question".$QuestionNumber."' value='".$ChoiceID."' ".($ChoiceID==$rChoiceID ? 'checked' : '').">";
				if($ChoiceID==$rChoiceID)
				{
					if($Correct==1)
					{
						echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
					}
					else
					{
						echo "<img src='..\images\stt\Incorrect.jpeg' width='20' height='20' />";
					}
				}
				else
				{
					if($Correct==1)
					{ 
						echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
						if($rChoiceID=="")
						{
							echo "<img src='..\images\stt\Incorrect.jpeg' width='20' height='20' />";
						}
					}
				}
				echo "</td>";	
			}
			elseif($CText=='Neutral')
			{
				echo "<td align='center'>".$CText."<br /><input type='radio' name='Question".$QuestionNumber."' value='".$ChoiceID."' ".($ChoiceID==$rChoiceID ? 'checked' : '').">";
				if($ChoiceID==$rChoiceID)
				{
					if($Correct==1)
					{
						echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
					}
					else
					{
						echo "<img src='..\images\stt\Incorrect.jpeg' width='20' height='20' />";
					}
				}
				else
				{
					if($Correct==1)
					{ 
						echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
						if($rChoiceID=="")
						{
							echo "<img src='..\images\stt\Incorrect.jpeg' width='20' height='20' />";
						}
					}
				}
				echo "</td>";
			}
			elseif($CText=='Disagree')
			{
				echo "<td align='center'>".$CText."<br /><input type='radio' name='Question".$QuestionNumber."' value='".$ChoiceID."' ".($ChoiceID==$rChoiceID ? 'checked' : '').">";
				if($ChoiceID==$rChoiceID)
				{
					if($Correct==1)
					{
						$_SESSION['QCorrect']++;
						echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
					}
					else
					{
						echo "<img src='..\images\stt\Incorrect.jpeg' width='20' height='20' />";
					}
				}
				else
				{
					if($Correct==1)
					{ 
						echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
						if($rChoiceID=="")
						{
							echo "<img src='..\images\stt\Incorrect.jpeg' width='20' height='20' />";
						}
					}
				}
				echo "</td>";
			}
			elseif($CText=='Strongly Disagree')
			{
				echo "<td align='center'>".$CText."<br /><input type='radio' name='Question".$QuestionNumber."' value='".$ChoiceID."' ".($ChoiceID==$rChoiceID ? 'checked' : '').">";
				if($ChoiceID==$rChoiceID)
				{
					if($Correct==1)
					{	
						echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
					}
					else
					{
						if($CorrectText == 'Disagree')
						{
							echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
						}
						else
						{
							echo "<img src='..\images\stt\Incorrect.jpeg' width='20' height='20' />";
						}
					}
				}
				else
				{
					if($Correct==1)
					{ 
						echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
						if($rChoiceID=="")
						{
							echo "<img src='..\images\stt\Incorrect.jpeg' width='20' height='20' />";
						}
					}
				}
				echo "</td></tr></table></tr>";
			}
			
		}

	}
	function GenerateComparison($QuestionNumber,$qText,$qImage,$qID,$cCompText)
	{
		include('../Includes/Variables.php');
		?>
		<tr>
			<td align='right'>
				<?php echo $QuestionNumber; ?>) Retype the Following:
			</td>
			<td>
				<?php echo $qText ; ?>
			</td>
			<td>
		<?php
			echo $cCompText;
			echo ($qText==$cCompText ? "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />" : "<img src='..\images\stt\Incorrect.jpeg' width='20' height='20' />");
			echo "</td></tr>";

	}
	function GenerateLikeDisalike($QuestionNumber,$qText,$qImage,$qID,$qCompString,$rChoiceID)
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
			$query2 = " SELECT STTCText, STTCImage, STTCID, STTCCorrect FROM STTChoices_tbl where STTCQID =".$qID;
			$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
			$result2=odbc_exec($connection,$query2);
			$i=0;
			while(odbc_fetch_row($result2))
			{
				$CText = odbc_result($result2, 1);	
				$CImage = odbc_result($result2, 2);	
				$ChoiceID = odbc_result($result2, 3);
				$Correct = odbc_result($result2, 4);
				if($i==0)
				{
					if($ChoiceID == $rChoiceID)
					{
						echo "<tr><td></td><td><input type='Radio' name='Question".$QuestionNumber."' Value='".$ChoiceID."' checked>Alike";
						if($Correct==1)
						{
							echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
						}
						else
						{
							echo "<img src='..\images\stt\incorrect.jpeg' width='20' height='20' />";
						}
					}
					else
					{
						echo "<tr><td></td><td><input type='Radio' name='Question".$QuestionNumber."' Value='".$ChoiceID."'>Alike";
						if($Correct==1)
						{
							echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
						}
					}
					
					$i++;
				}
				else
				{	if($ChoiceID == $rChoiceID)
					{
						echo "<br /><input type='Radio' name='Question".$QuestionNumber."' value='".$ChoiceID."' checked>Disalike";
						if($Correct==1)
						{
							echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
						}
						else
						{
							echo "<img src='..\images\stt\incorrect.jpeg' width='20' height='20' />";
						}
					}
					else
					{
						echo "<br /><input type='Radio' name='Question".$QuestionNumber."' value='".$ChoiceID."'>Disalike";
						if($Correct==1)
						{
							echo "<img src='..\images\stt\Correct.jpeg' width='20' height='20' />";
						}
					}
				echo "</td></tr>";
				}
				
			}
	}
	function PopulateTests()
	{
		include('../Includes/Variables.php');
		$conn2 = odbc_connect($connection_string, $sqlRUser, $sqlRpassword);
		$query2 = "Select t.STTTName,u.STTUFname,u.STTULname,i.STTDTE,s.STTScore,i.STTTestInstanceID   from STTTestInstance_tbl i join STTTest_tbl t on t.STTTID = i.STTTID join STTUser_tbl u on u.STTUID = i.STTUID left join STTScore_tbl s on s.STTTestInstanceID = i.STTTestInstanceID where i.STTTestInstanceEnabled = 1";
		//echo "<tr><td>".$query2."</td></tr>";
		$result2=odbc_exec($conn2, $query2) or die(odbc_errormsg());
		while(odbc_fetch_row($result2))
		{
			$tTestName = odbc_result($result2, 1);
			$uFname = odbc_result($result2, 2);
			$uLname = odbc_result($result2, 3);
			$iDate = odbc_result($result2, 4);
			$sScore = odbc_result($result2, 5);
			$iTestID = odbc_result($result2, 6);
		
			echo "<tr><td>".$tTestName."</td><td>".$uFname." ".$uLname."</td><td>".date("Y-m-d",strtotime($iDate))."</td><td>".$sScore."</td><td><a href=STTResults.php?TIID=".$iTestID.">View Test</a></td>";
			
			if($_SESSION['SuperAdmin'] == 1 || GetRightsLevel(15) >= 9 )
			{
				?>
				<td><label class='buttonlink'><input type='checkbox' name='Result_Remove' value='<?php echo $iTestID; ?>' style='display:none;' onchange='document.results.submit();'>Remove</label></td></tr>
				<?php
			}		
		}

			
	}
	
	// ******************************* Functions end ************************************
	
	// Includes
	//include("useradmincheck.inc");
	//include("Includes/PopUpCalendar.js");
	include('Includes/Variables.php');
	
	if($error == "400"){
		$message = "That Username already exists in the database. Please try again.";
	}
	if($_SESSION['authenticated']!="yes"){
?>
<script language="JavaScript">
	window.location='index.php';
</script>
<?php
	}
	else
	{
		$username = $_SESSION['username'];
	}
	
	if(ISSET($_POST['Result_Remove']))
	{
		$query="update STTTestInstance_tbl set STTTestInstanceEnabled = 0 where STTTestInstanceID = ".$_POST['Result_Remove'];
		UpdateResults($query);
	}
	if(ISSET($_POST['Result_Remove_All']))
	{
		$query="update STTTestInstance_tbl set STTTestInstanceEnabled = 0";
		UpdateResults($query);
	}
?>

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
<title>STT - Results</title>
<link rel="stylesheet" type="text/css" href="http://www.ferris.edu/stylesheets/sitepage.css" />
<link rel="stylesheet" type="text/css" href="CSS/style.css" />
</head>
<link rel="shortcut icon" href="http://fsudboard2.ferris.edu/tportal/favicon.ico" />
<body onload="tableResize()">         
<?php include('../Includes/navpanel.inc.php'); ?>
 
<div id="content">
<table id="main_tbl" cellspacing="0" cellpadding="0" align="center">
	<?php include("../Includes/pagebanner.inc.php"); ?>
    <tr height="60">
        <td><?php include("../Includes/welcomelinks.inc.php"); ?></td>
    </tr>
    <tr valign="top">
        <td>
<!--- Content: begin --->
		<table id="submain_tbl" border=0>
                <tr> 
                	<td align="center" width="50%">
                	
						<table border=0>
							<tr>
								<td colspan="2" align="center">
									<?php
									if($_GET['TIID'])
									{
										echo "<table align='center'  border='0' width='800'>";
										$conn2 = odbc_connect($connection_string, $sqlRUser, $sqlRpassword);
										$query2 = "Select t.STTTName, t.STTTDesc, s.STTScore, s.STTSTotalQuestions,s.STTSTotalCorrect,i.STTDTE,u.STTUFname,u.STTULname,u.STTUShortID  from STTTestInstance_tbl i join STTTest_tbl t on t.STTTID = i.STTTID join STTScore_tbl s on s.STTTestInstanceID = i.STTTestInstanceID join STTUser_tbl u on u.STTUID = i.STTUID where i.STTTestInstanceID = ".$_GET['TIID'];
										$result2=odbc_exec($conn2, $query2) or die(odbc_errormsg());
										while(odbc_fetch_row($result2))
										{
											$tTestName = odbc_result($result2, 1);
											$tTestDesc = odbc_result($result2, 2);
											$sScore = odbc_result($result2, 3);
											$sTotalQuestions = odbc_result($result2, 4);
											$sToatlCorrect = odbc_result($result2, 5);
											$iDTE = odbc_result($result2, 6);
											$uFname = odbc_result($result2, 7);
											$uLname = odbc_result($result2, 8);
											$uShortID = odbc_result($result2, 9);
										}
										echo "<tr>
												<td align=right width=120>
													<b>Name:</b>
												</td>
												<td>
													".$uFname." ".$uLname."
												</td>
												<td align=right >
													<b>Test Name:</b> 
												</td>
												<td>
													".$tTestName."
												</td>
											</tr>
											<tr>
												<td align=right valign=top>
													<b>ShortID:</b>
												</td>
												<td valign=top>
													".$uShortID."
												</td>
												<td align=right valign=top>
													<b>Test Desc:</b>
												</td>
												<td width=240>
													". $tTestDesc."
												</td>
											</tr>
											<tr>
												<td align=right >
													<b>Total Questions:</b><br />
													<b>Total Correct:</b><br />
													<b>Overall Score:</b><br />
												</td>
												<td>
													".$sTotalQuestions."<br />
													".$sToatlCorrect."<br />
													".$sScore."<br />																
												</td>
												<td align=right valign=top>
													<b>Date Taken:</b>
												</td>
												<td valign=top>
													".date("Y-m-d",strtotime($iDTE))."
												</td>
											</tr>
											</table>";
											echo "<table align='center'  border='0' width='800'>";
									
										$conn = odbc_connect($connection_string, $sqlRUser, $sqlRpassword);
										$query = "select q.STTQQuestion,q.STTQImage,t.STTQTID,q.STTQID,q.STTQComparisonString,o.STTTIQOSection from STTTestInstanceQuestionOrder_tbl o
													join STTQuestions_tbl q on o.STTTIQOSTTQID = q.STTQID 
													join STTQType_tbl t on t.STTQTID = q.STTQQTID 
													where o.STTTIQOSTTTIID =".$_GET['TIID'];
										//echo $query;
										$result=odbc_exec($conn, $query) or die(odbc_errormsg());
										$QuestionNumber=1;
										
										while(odbc_fetch_row($result))
										{
											
											$qText = odbc_result($result, 1);
											$qImage = odbc_result($result, 2);
											$qType = odbc_result($result, 3);
											$qID = odbc_result($result, 4);
											$qCompString = odbc_result($result, 5);
											$oSectionNum = odbc_result($result, 6);
											
											if($qType==7)
											{
												$query2 = "Select c.STTCompText from STTComparisonResponse_tbl c where c.STTCompTestInstanceID =".$_GET['TIID']." and c.STTCompQID =".$qID;
												$result2=odbc_exec($conn, $query2) or die(odbc_errormsg());
												$cCompText = odbc_result($result2, 1);
											}
											else
											{
												$query2 = "Select r.STTRCID from STTResponse_tbl r where r.STTTestInstanceID =".$_GET['TIID']." and r.STTRQID =".$qID;
												$result2=odbc_exec($conn, $query2) or die(odbc_errormsg());
												$rChoiceID = odbc_result($result2, 1);
											}
											
											
											if($oSectionNum!=$prevSecNum)
											{
											
													$query2 = "select top ".$oSectionNum." s.STTSecName,s.STTSecDesc,s.STTSecHour,s.STTSecMinute,r.STTSRTHours,r.STTSRTMinutes,r.STTSRTSeconds from STTSection_tbl s
																join STTSectionResponseTime_tbl r on s.STTSectionID = r.STTSRTSTTSectionID 
																where STTSecSTTTID = (select STTTID from STTTestInstance_tbl where STTTestInstanceID = ".$_GET['TIID'].") and sttsrtstttiid =".$_GET['TIID'];
													$result2=odbc_exec($conn, $query2) or die(odbc_errormsg());
													while(odbc_fetch_row($result2))
													{
														$sSecName = odbc_result($result2, 1);
														$sSecDesc = odbc_result($result2, 2);
														$sSecHour = odbc_result($result2, 3);
														if($sSecHour==0)
														{ $sSecHour = '00';}
														$sSecMinute = odbc_result($result2, 4);
														$rHours = odbc_result($result2, 5);
														$rMinutes= odbc_result($result2, 6);
														$rSeconds = odbc_result($result2, 7);
													}
													$SecTotal = ($sSecHour * 3600) + ($sSecMinute * 60); 
													$rSecTotal = $SecTotal - (($rHours * 3600) + ($rMinutes * 60) + $rSeconds);
													$hours = floor($rSecTotal / 3600);
													if($hours==0)
													{ $hours = '00';}
													$mins = floor(($rSecTotal - ($hours*3600)) / 60);
													if($mins==0)
													{ $mins = '00';}
													$secs = floor($rSecTotal % 60); 
													if($secs==0)
													{ $secs = '00';}
													echo "<tr bgcolor='#DDDDDD'><td><b>Section #</b>".$oSectionNum." - ".$sSecName."</td><td colspan=2 align=right><b>Time Allowed:  </b>".$sSecHour."h:".$sSecMinute."m:00s</td></tr>";
													echo "<tr bgcolor='#DDDDDD'><td></td><td colspan=2 align=right><b>Time Taken: </b>".$hours."h:".$mins."m:".$secs."s</td></tr>";
													$prevSecNum = $oSectionNum;
												
											}
											
											
											if($qType == 2) // Multiple Choice
											{
												GenerateMultiChoice($QuestionNumber,$qText,$qImage,$qID,$rChoiceID);
												echo "<tr><td colspan='3'></td></tr>";
												$QuestionNumber++;
											}
											elseif($qType == 4) // True/False
											{
												GenerateTrueFalse($QuestionNumber,$qText,$qImage,$qID,$rChoiceID);
												echo "<tr><td colspan='3'></td></tr>";
												$QuestionNumber++;
											}
											elseif($qType == 5) // Select All That Apply
											{
												if($qID!=$PreviousQID)
												{
													GenerateSelectAll($QuestionNumber,$qText,$qImage,$qID,$_GET['TIID']);
													echo "<tr><td colspan='3'></td></tr>";
													$PreviousQID = $qID;
													$QuestionNumber++;
												}
											}
											elseif($qType == 6) // Scaled Questions Agree/Disagree
											{
												GenerateScaled($QuestionNumber,$qText,$qImage,$qID,$rChoiceID);
												echo "<tr><td colspan='3'></td></tr>";
												$QuestionNumber++;
											}
											elseif($qType == 8) // Like/Disalike
											{
												GenerateLikeDisalike($QuestionNumber,$qText,$qImage,$qID,$qCompString,$rChoiceID);
												echo "<tr><td colspan='3'></td></tr>";
												$QuestionNumber++;
											}
											elseif($qType== 7) // Comparison
											{
												GenerateComparison($QuestionNumber,$qText,$qImage,$qID,$cCompText);
												echo "<tr><td colspan='3'></td></tr>";
												$QuestionNumber++;
											}
											
										}	
									
										
										$query3 = "Select q.STTQQuestion,q.STTQImage,t.STTQTID,q.STTQID, o.STTOpenEndedResponse from STTOpenEndedResponse_tbl o join STTQuestions_tbl q on o.STTQID = q.STTQID join STTQType_tbl t on q.STTQQTID = t.STTQTID where o.STTTestInstanceID =".$_GET['TIID']." order by t.STTQType";
										$result3=odbc_exec($conn, $query3) or die(odbc_errormsg());
										
										$x=0;
										while(odbc_fetch_row($result3))
										{
											$qText = odbc_result($result3, 1);
											$qImage = odbc_result($result3, 2);
											$qType = odbc_result($result3, 3);
											$qID = odbc_result($result3, 4);
											$oResponseText = odbc_result($result3, 5);
											if($x==0)
											{
												echo "<tr><td><b>Section: Open Ended</b></td></tr>";
												$x++;
											}
											
											if($qType == 3) // Open Ended
											{
												GenerateOpenEnded($QuestionNumber,$qText,$qImage,$qID,$oResponseText);
											}
										echo "<tr><td colspan='3'></td></tr>";
											$QuestionNumber++;
										}
									}
									else
									{
										?>
										<form name="results" action="STTResults.php" method="post">
											<table width="600">
												<tr bgcolor="#000066">
													<td><font color="white">
														Test Name
													</font></td>
													<td><font color="white">
														Student Name
													</font></td>
													<td><font color="white">
														Date Taken
													</font></td>
													<td><font color="white">
														Score
													</font></td>
													<td><font color="white">
														Link to Test
													</font></td>
													<?php 
														if($_SESSION['SuperAdmin'] == 1 || GetRightsLevel(15) >= 9 )
														{
															echo '<td><font color="white">Remove</font></td>';
														}
													?>
												</tr>
												<?php populateTests(); ?>
											</table>
											<?php 
												if($_SESSION['SuperAdmin'] == 1 || GetRightsLevel(15) >= 9 )
												{
													?>
													Clicking this link will Remove all Tests from this page.
													<label class='buttonlink'>
													<input type='checkbox' name='Result_Remove_All' value='1' style='display:none;' onchange='document.results.submit();'>
														Remove All
													</label>
													<?php
												}
											?>
											
										</form>
										<?php
									}								
							
									?>
									</table>
								</td>
							</tr>	
						</table>
					</td>  
                </tr>
            </table>
        </td>
   	</tr>
    <?php include("../Includes/FSUfooterlinks.inc.php"); ?>
</table>
</div>
</body>
</html>
              