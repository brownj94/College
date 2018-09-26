<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	session_start();
	
	$pagename = "the <br />Student Tech Test Question Type Administration Page";
	$error = $_GET['error'];
	$getAnotherRoom = $_GET['getAnotherRoom'];
	
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
// ***************************** Begin Functions ****************************************
	function Redir()
	{
				?>
				<script language=javascript>
					setTimeout("location.href='STTQuestionType.php'",2000);
				</script>
				<?php
	}	
	function DeleteQuestionType($query)
	{
		include('../Includes/Variables.php');
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
			if($connection)
			{
				$result=odbc_exec($connection,$query);
				if($result)
				{
					echo "Question Type Deleted. <br /> Redirecting back to Question Type Administration.";
					Redir();
				}
				else
				{
					echo "Question Type Not Deleted. Review insert statement for problems.";
					echo $query;
				}
			}
			else
			{
				echo "connection Failed";
			}
		$connection = null;
	}
	function UpdateQuestionType($query)
	{
		include('../Includes/Variables.php');
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
			if($connection)
			{
				$result=odbc_exec($connection,$query);
				if($result)
				{
					echo "Question Type Updated. <br /> Redirecting back to Question Type Administration.";
					Redir();
				}
				else
				{
					echo "Question Type Not Updated. Review insert statement for problems.";
					echo $query;
				}
			}
			else
			{
				echo "connection Failed";
			}
		$connection = null;
	}
	
	function AddQuestionType($query)
	{
		include('../Includes/Variables.php');
		$connection = odbc_connect($connection_string, $sqlRWUser, $sqlRWpassword);
			if($connection)
			{
				$result=odbc_exec($connection,$query);
				if($result)
				{
					echo "Question Type Added. <br /> Redirecting back to Question Type Administration.";
					Redir();
				}
				else
				{
					echo "Question Type Not Added. Review insert statement for problems.";
					echo $query;
				}
			}
			else
			{
				echo "connection Failed";
			}
		$connection = null;
	}
	
	function PopulateQuestionTypes()
		{
			include('../Includes/Variables.php');

			$connection = odbc_connect($connection_string, $sqlRUser, $sqlRpassword);
			if($connection)
			{
				$query = "SELECT STTQTID, STTQType,STTQTEnabled from STTQType_tbl order by STTQType";
				
				$result=odbc_exec($connection,$query);
				while(odbc_fetch_row($result))
				{
					$QTID = odbc_result($result, 1);
					$QT = odbc_result($result, 2);
					$QTEnabled = odbc_result($result, 3);
					if($QTEnabled == 1)
					{ $QTEnabled = 'Yes'; }
					else
					{ $QTEnabled = 'No'; }
					Echo "<tr><td align='center'><a href='STTQuestionType.php?EditID=".$QTID."'>".$QTID."</a></td><td>".$QT."</td><td align='center'>".$QTEnabled."</td><td align='right'><input type='checkbox' name='DelID[]' value='".$QTID."'/></td></tr>"; 
				
				}
			}
			else
			{
				echo "connection Failed";
			}
			$connection = null;
		}
// ***************************** End Functions ****************************************

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
<title>Launch Page - Tech Support Portal</title>
<link rel="stylesheet" type="text/css" href="http://www.ferris.edu/stylesheets/sitepage.css" />
<link rel="stylesheet" type="text/css" href="../CSS/style.css" />


</head>
<link rel="shortcut icon" href="http://fsudboard2.ferris.edu/home/favicon.ico" />
<body onload="tableResize()">         
<?php include("../Includes/navpanel.inc.php"); ?>
 
<div id="content">
<table id="main_tbl" cellspacing="0" cellpadding="0" align="center">
	<?php include("../Includes/pagebanner.inc.php"); ?>
    <tr height="60">
        <td><?php include("../Includes/welcomelinks.inc.php"); ?></td>
    </tr>
    <tr>
        <td align="center">
			<?php 
// ***************************** Begin QuestionType Add Section ****************************************

			if ($_POST['AddQuestionType'])
			{
				$QT = $_POST['QT'];
				$QTEnabled = $_POST['QTEnabled'];
				if($QTEnabled == 'on')
				{
					$QTEnabled = 'true';
				}
				Else
				{
					$QTEnabled = 'false';
				}
				$query = "Insert INTO STTQType_tbl (STTQType,STTQTEnabled) values ('".$QT."','".$QTEnabled."')";
				AddQuestionType($query);
// ***************************** End QuestionType Add Section ****************************************
			}
			Elseif (($_POST['UpdateQuestionType']) or ( $_POST['UpdateQuestionType2']) or ($_GET['EditID']))
			{
// ***************************** Begin QuestionType Update Section ****************************************
				$EditID = $_GET['EditID'];
				$DelID = $_POST['DelID'];
				
				$connection = odbc_connect($connection_string, $sqlRUser, $sqlRpassword);
				If($connection)
				{
					$query = "SELECT STTQTID,STTQType,STTQTEnabled from STTQType_tbl where STTQTID = '".$EditID."' order by desc";
					$result=odbc_exec($connection,$query);
					while(odbc_fetch_row($result))
					{
						$QTID = odbc_result($result, 1);
						$QT = odbc_result($result, 2);
						$QTEnabled = odbc_result($result, 3);
					}	
				}
				Else
				{
					echo "connection Failed";
				}
				$connection = null;
				If(($_GET['EditID']) or ($_POST['DelID']))
				{	
			
					if ($EditID != '')
					{
						?>
						<form name="frmUpdateQuestionType2" method="post" action="STTQuestionType.php">
							<table border=0 cellspacing="5">
								<tr>
									<td align="right"></td>
									<td><input type="hidden" name="QTID" value="<?php echo $EditID; ?>" readonly/></td>
								</tr>
								<tr>
									<td align="right">Question Type</td>
									<td><input type="text" name="QT" value="<?php echo $QT; ?>"/></td>
								</tr>
								<tr>
									<td align="right">Question Type Enabled</td>
                                    <?php
									if($QTEnabled == '1')
									{
										echo '<td><input type="checkbox" Name="QTEnabled" checked/></td>';
									}
									else
									{
										echo '<td><input type="checkbox" Name="QTEnabled" /></td>';
									}
									?>
								</tr>
								<tr>
									<td></td>
									<td><input type="submit" class="button" Value="Update QuestionType" name="UpdateQuestionType2"/></td>
								</tr>
							</table>
						</form>
						<?php
					}
					Else
					{
						if(empty($DelID)) 
						{
							echo("You didn't select any QuestionTypes.");
						} 
						Else
						{
							$N = count($DelID);
							$query='Delete from STTQType_tbl where STTQTID ='.$DelID[0];
							for($i=1; $i < $N; $i++)
							{
								$query .= " OR STTQTID=" .$DelID[$i];
							}
							DeleteQuestionType($query);
							
						}
						
						
						
						
					}
				}
				Elseif ($_POST['UpdateQuestionType2'])
				{
				$QTEnabled = $_POST['QTEnabled'];
				If($QTEnabled == 'on')
				{
					$QTEnabled = 'true';
				}
				Else
				{
					$QTEnabled = 'false';
				}
					$query = "Update STTQType_tbl SET STTQType='".$_POST["QT"]."', STTQTEnabled='".$QTEnabled."' WHERE STTQTID=".$_POST["QTID"];
					UpdateQuestionType($query);
				}	
	// ***************************** End QuestionType Update Section ****************************************	
			}
			Else
			{
// ***************************** Begin QuestionType Admin Landing Section ****************************************
				?>
                <table>
                    <tr>
                        <td>
                            <form name="frmAddQuestionType" method="post" action="STTQuestionType.php">
                                <table border=0>
                                    <tr>
                                        <td colspan="2" align="center"><h1>Add New Question Type</h1></td>
                                    </tr>
                                 	<tr>
										<td align="right"><h5>Question Type</h5></td>
										<td><input type="text" name="QT"/></td>
									</tr>
                                    <tr>
                                        <td align="right"><h5>Question Type Enabled</h5></td>
                                        <td><input type="checkbox" Name="QTEnabled"/></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td><input type="submit" class="button" Value="Add QuestionType" name="AddQuestionType"/></td>
                                    </tr>
                                </table>
                            </form>	
                        </td>
                        <td width="25" style="border-right:solid medium #cc0033">
                        </td>
                        <td width="25">
                        </td>
                        <td>
                            <form name="frmUpdateQuestionType" method="post" action="STTQuestionType.php">
                                <table Border=0 width="400">
                                    <tr>
                                        <td colspan="4" align="center"><h1>Edit QuestionType</h1></td>
                                    </tr>
                                    <tr>
                                        <td><h5>ID</h5></td>
                                        <td><h5>Question Type</h5></td>
                                        <td><h5>Enabled</h5></td>	
                                        <td align="right"><h5>Delete</h5></td>
                                    </tr>
                    
                                    <?php PopulateQuestionTypes(); ?>
                    
                                    <tr>
                                        <td colspan="7" align='right'><input type="submit" class="button" Value="Delete" name="UpdateQuestionType"/></td>
                                    </tr>
                                </table>
                            </form>
                        </td>
                    </tr>
                </table>
            <?php
            }
            ?>
     	</td>
   	</tr>
    <?php include("../Includes/FSUfooterlinks.inc.php"); ?>
</table>
</div>
</body>
</html>
