
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	// variables
	session_start();

	$arrSecID;
	$arrSecName;
	$pagename = "the <br />Student Tech Test Section/Question Association Page";
	$error = $_GET['error'];
	$getAnotherRoom = $_GET['getAnotherRoom'];
	$case = $_GET['case'];
	include('../Includes/Functions.php');
	include('../Includes/Variables.php');
	
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
	if($_SESSION['SuperAdmin'] <> 1 && GetRightsLevel(15) <> 9)
	{
		echo"
			<script language='JavaScript'>
				window.location='http://fsudboard2.ferris.edu/tportal/index.php';
			</script>";	
	}
	// functions
	function Redir($site)
	{
		if($site==1)
		{
			?>
			<script language=javascript>
				setTimeout("location.href='../Administration/STTAdmin.php'",1000);
			</script>
			<?php
		}
		else
		{
			?>
			<script language=javascript>
				setTimeout("location.href='STTTestQuestionAssociation.php'",1000);
			</script>
			<?php
		}
	}
	
	function CheckTestInUse($TestID)
	{
		include('../Includes/Variables.php');
		$conn2 = odbc_connect($connection_string, $sqlRUser, $sqlRpassword) or die('Error connecting to mysql');
		$query2 = "select Top 1 stttid from STTTestInstance_tbl where STTTID = $TestID";
		$result2=odbc_exec($conn2, $query2);
		if(odbc_fetch_row($result2))
		{
			$Temp=1;
		}
		else
		{
			$Temp=0;
		}
		return $Temp;
	}
	function DeleteAssociatedQuestions($query)
	{
		include('../Includes/Variables.php');
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
			if($connection)
			{
				$result=odbc_exec($connection,$query);
				if($result)
				{
					Return 1;
				}
				else
				{
					Return 2;
				}
			}
			else
			{
				echo "connection Failed";
			}
		$connection = null;
	}
	function AssociateQuestions($query)
	{
		include('../Includes/Variables.php');
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
			if($connection)
			{
				
				$result=odbc_exec($connection,$query);
				if($result)
				{
					Return 1;
				}
				else
				{
					Return 2;
				}
			}
			else
			{
				echo "connection Failed";
			}
		$connection = null;
	}
	function PopulateTests()
	{
		include('../Includes/Variables.php');
		$conn = odbc_connect($connection_string, $sqlRUser, $sqlRpassword) or die('Error connecting to mysql');
		$query = "select STTTID, STTTName,STTTDESC FROM STTTest_tbl where STTTEnabled=1";
		$result=odbc_exec($conn, $query);
		$i=0;
		while(odbc_fetch_row($result))
		{
			$TestID = odbc_result($result, 1);
			$Test = odbc_result($result, 2);
			$TestDesc = odbc_result($result, 3);
		
			$InUse=CheckTestInUse($TestID);
			
			if($InUse==1)
			{
				echo "<tr bgcolor=".$bgcolor."><td valign='top' >".$Test."</td><td valign='top' >".$TestDesc."</td><td>Locked</td></tr>";
			}	
			else
			{
				echo "<tr bgcolor=".$bgcolor."><td valign='top' ><a href='?EditID=".$TestID."'>".$Test."</a></td><td valign='top' >".$TestDesc."</td><td></td></tr>";
			}
			
		}	
		$conn = null;
		
	}
	function GetSectionCount($TestID)
	{
		include('../Includes/Variables.php');
		$conn = odbc_connect($connection_string, $sqlRUser, $sqlRpassword) or die('Error connecting to mysql');
		$query = "select Count(STTSectionID) FROM STTSection_tbl where STTSecSTTTID =" .$TestID;
		$result=odbc_exec($conn, $query);
		while(odbc_fetch_row($result))
		{
			$Sections = odbc_result($result, 1);
		}	
		$conn = null;
		return $Sections;
	}
	
	function GetSectionInfo($TestID)
	{
		Global $arrSecID,$arrSecName;
		include('../Includes/Variables.php');
		$conn = odbc_connect($connection_string, $sqlRUser, $sqlRpassword) or die('Error connecting to mysql');
		$query = "select STTSectionID, STTSecName FROM STTSection_tbl where STTSecSTTTID =" .$TestID;
		$result=odbc_exec($conn, $query);
		$i=0;
		
		while(odbc_fetch_row($result))
		{
			$SectionID = odbc_result($result, 1);
			$SectionName = odbc_result($result, 2);
			$arrSecID[$i]=$SectionID;
			$arrSecName[$i]=$SectionName;
				
			$i++;
		}	
		$conn = null;
		return $SectionInfo;
	}
	function PopulateQuestions()
	{
		GetSectionInfo($_GET['EditID']);
		Global $arrSecID,$arrSecName;
		$Sections = GetSectionCount($_GET['EditID']);
		// array that holds section id and name. 
		//echo $arrSecID[0];
		//echo $arrSecName[0];
		include('../Includes/Variables.php');
		$connection = odbc_connect($connection_string, $sqlRUser, $sqlRpassword);
		$connection2 = odbc_connect($connection_string, $sqlRUser, $sqlRpassword);
		if($connection)
		{
			$query = "SELECT q.STTQID,t.STTQType,q.STTQQuestion FROM STTQuestions_tbl q join STTQType_tbl t on q.STTQQTID=t.STTQTID where STTQEnabled=1 order by q.STTQQTID";
			$result=odbc_exec($connection,$query);
			$i = 0;
			$x = 0;
			while(odbc_fetch_row($result))
			{
				$QID = odbc_result($result, 1);
				$Type = odbc_result($result, 2);
				$Question = odbc_result($result, 3);
				$AssocSectionID = '';
				if($x==1)
				{
					$bgcolor = '#F6CEE3';
					$x=0;
				}
				else
				{
					$bgcolor = 'white';
					$x=1;
				}
				?>
					<tr bgcolor='<?php echo $bgcolor;?>'>
						<td align='Left'>
							<?php echo $Question; ?>
						</td>
						<td align='center'>
							<?php echo $Type; ?>
						</td>
				<?php
				for($y=0; $y<$Sections; $y++)
				{
					$query2 = "SELECT q.TQAssocSTTSecID from STTTQAssoc_tbl q where q.TQAssocSTTQID = ".$QID." and q.TQAssocSTTSecID = ".$arrSecID[$y];
					// Trouble shooting echo 
					// echo "$y = ". $y." - query = ". $query2. "<br />";
					$result2=odbc_exec($connection2,$query2);
					while(odbc_fetch_row($result2))
					{
						$AssocSectionID = odbc_result($result2, 1);	
					}
				
				
					
					
						
								
						if($y+1 == 1)
						{
							echo "<td align='center'><input type='checkbox' name='Section1Q[]' value='".$QID."' ".($arrSecID[0] == $AssocSectionID ? 'Checked':'') ."></td>";
						}
						if($y+1 == 2)
						{
							echo "<td align='center'><input type='checkbox' name='Section2Q[]' value='".$QID."' ".($arrSecID[1] == $AssocSectionID ? 'Checked':'') ."></td>";
						}
						if($y+1 == 3)
						{
							echo "<td align='center'><input type='checkbox' name='Section3Q[]' value='".$QID."' ".($arrSecID[2] == $AssocSectionID ? 'Checked':'') ."></td>";
						}
						if($y+1 == 4)
						{
							echo "<td align='center'><input type='checkbox' name='Section4Q[]' value='".$QID."' ".($arrSecID[3] == $AssocSectionID ? 'Checked':'') ."></td>";
						}
						if($y+1 == 5)
						{
							echo "<td align='center'><input type='checkbox' name='Section5Q[]' value='".$QID."' ".($arrSecID[4] == $AssocSectionID ? 'Checked':'') ."></td>";
						}
				}		
					?>
					</tr>
					<?php
					$i++;
			}
		}
		else
		{
			echo "connection Failed";
		}
		$connection = null;
	}
	// ../Includes
	//include("useradmincheck.inc");
	//include("../Includes/PopUpCalendar.js");
	include('../Includes/Variables.php');

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
<title>STT - Question Association</title>
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
				<?php
				
				If($_POST['AddQuestions'])
				{
					$Section1Q = $_POST['Section1Q'];
					$Section2Q = $_POST['Section2Q'];
					$Section3Q = $_POST['Section3Q'];
					$Section4Q = $_POST['Section4Q'];
					$Section5Q = $_POST['Section5Q'];
					// Section 1 questions to associate
					
					$connection = odbc_connect($connection_string, $sqlRUser, $sqlRpassword);
					if($connection)
					{
						$query = "SELECT STTSectionID from STTSection_tbl where STTSecSTTTID =".$_POST['TestID'];
						
						$result=odbc_exec($connection,$query);
						$x=1;
						$err=0;
						while(odbc_fetch_row($result))
						{
							$SectionID = odbc_result($result, 1);
							
								// Empty Section before Saving new association with it
								$DelQuery = "Delete from STTTQAssoc_tbl where TQASSOCSTTSecID = " .$SectionID;
								DeleteAssociatedQuestions($DelQuery);
								// new association is here
								$N = Count(${Section.$x.Q});
								$query='Insert Into STTTQAssoc_tbl (TQASSOCSTTSecID,TQAssocSTTQID) VALUES ('.$SectionID.','.${Section.$x.Q}[0].')';
								// Trouble shooting echo
								//echo $x. '-' .$query .'<br />';
								if(!empty(${Section.$x.Q})) 
								{
									if(AssociateQuestions($query) == 2)
									{
										$err+=1;
									}
									for($i=1; $i < $N; $i++)
									{
										$query='Insert Into STTTQAssoc_tbl (TQASSOCSTTSecID,TQAssocSTTQID) VALUES ('.$SectionID.','.${Section.$x.Q}[$i].')';
										// Trouble shooting echo
										//echo $x. '-' .$query .'<br />';
										if(AssociateQuestions($query) == 2)
										{
											$err+=1;
										}
									}
								}
							
							$x++;
						}
						if($err==0)
						{
							Echo "Section(s) updated. <br /> Redirecting back to Test Administration.";
							Redir(1);
						}
						else
						{
							Echo "There was an error Associating the Questions to the Section. <br /> If the problem persists Please contact AC.";
						}
					}
					
					
				}
				ElseIf($_GET['EditID'])
				{
					$TestID = $_GET['EditID'];
					GetSectionInfo($_GET['EditID']);
					Global $arrSecName;
					$Sections = GetSectionCount($TestID);
					$Colspan=$Sections + 2;
					?>
					<form method='post' action='STTTestQuestionAssociation.php'>
						<table>
							<tr>
								<td align="center"><h5>Question</h5></td>
								<td align="center"><h5>Type</h5></td>
								<?php
								if($Sections >= 1)
								{
									echo '<td align="center"><h5>'.$arrSecName[0].'</h5></td>';
								}
								if($Sections >= 2)
								{
									echo '<td align="center"><h5>'.$arrSecName[1].'</h5></td>';
								}
								if($Sections >= 3)
								{
									echo '<td align="center"><h5>'.$arrSecName[2].'</h5></td>';
								}
								if($Sections >= 4)
								{
									echo '<td align="center"><h5>'.$arrSecName[3].'</h5></td>';
								}
								if($Sections == 5)
								{
									echo '<td align="center"><h5>'.$arrSecName[4].'</h5></td>';
								}
								?>
							</tr>
							<?php PopulateQuestions(); ?>
							<tr>
								<?php
								if($Sections >=1)
								{
									echo "<td align='right' colspan='".$Colspan."'>";
									echo "<input type='hidden' name='TestID' value='".$TestID."' />";
									echo "<input type='Submit' name='AddQuestions' value='Add Questions' Class='button' />";
									echo "</td>";
									
								}
								?>
							</tr>
						</table>
					</form>
					<?php
				}
				else
				{
					?>
					<h5>Please Select the Test You Would Like to add/remove Questions to/from.</h5>
					<br />
					<table width='700'>
						<tr bgcolor='#DDDDDD'>
							<td width='120' align='center'>
								<h5>Test Name</h5>
							</td>
							<td align='center'>
								<h5>Test Desc</h5>
							</td>
							<td align='center'>
								<h5>Test Status</h5>
							</td>
						</tr>
					
					<?php
					$i = 0;
					PopulateTests();
					?>
					</table>
					<?php
				}
				?>
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