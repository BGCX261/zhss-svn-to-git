<?php
define("EW_PAGE_ID", "view", TRUE); // Page ID
define("EW_TABLE_NAME", 'user', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "userinfo.php" ?>
<?php include "userfn50.php" ?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Always modified
header("Cache-Control: private, no-store, no-cache, must-revalidate"); // HTTP/1.1 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
?>
<?php

// Open connection to the database
$conn = ew_Connect();
?>
<?php

// Common page loading event (in userfn*.php)
Page_Loading();
?>
<?php

// Page load event, used in current page
Page_Load();
?>
<?php
$user->Export = @$_GET["export"]; // Get export parameter
$sExport = $user->Export; // Get export parameter, used in header
$sExportFile = $user->TableVar; // Get export file, used in header
?>
<?php
if (@$_GET["id"] <> "") {
	$user->id->setQueryStringValue($_GET["id"]);
} else {
	Page_Terminate("userlist.php"); // Return to list page
}

// Get action
if (@$_POST["a_view"] <> "") {
	$user->CurrentAction = $_POST["a_view"];
} else {
	$user->CurrentAction = "I"; // Display form
}
switch ($user->CurrentAction) {
	case "I": // Get a record to display
		if (!LoadRow()) { // Load record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "No records found"; // Set no record message
			Page_Terminate("userlist.php"); // Return to list
		}
}

// Set return url
$user->setReturnUrl("userview.php");

// Render row
$user->RowType = EW_ROWTYPE_VIEW;
RenderRow();
?>
<?php include "header.php" ?>
<script type="text/javascript">
<!--
var EW_PAGE_ID = "view"; // Page id

//-->
</script>
<script language="JavaScript" type="text/javascript">
<!--

// Write your client script here, no need to add script tags.
// To include another .js script, use:
// ew_ClientScriptInclude("my_javascript.js"); 
//-->

</script>
<p><span class="phpmaker">View TABLE: user
<br><br>
<a href="userlist.php">Back to List</a>&nbsp;
<a href="useradd.php">Add</a>&nbsp;
<a href="<?php echo $user->EditUrl() ?>">Edit</a>&nbsp;
<a href="<?php echo $user->CopyUrl() ?>">Copy</a>&nbsp;
<a href="<?php echo $user->DeleteUrl() ?>">Delete</a>&nbsp;
</span>
</p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<p>
<form>
<table class="ewTable">
	<tr class="ewTableRow">
		<td class="ewTableHeader">id</td>
		<td<?php echo $user->id->CellAttributes() ?>>
<div<?php echo $user->id->ViewAttributes() ?>><?php echo $user->id->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">user</td>
		<td<?php echo $user->user->CellAttributes() ?>>
<div<?php echo $user->user->ViewAttributes() ?>><?php echo $user->user->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">password</td>
		<td<?php echo $user->password->CellAttributes() ?>>
<div<?php echo $user->password->ViewAttributes() ?>><?php echo $user->password->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">level</td>
		<td<?php echo $user->level->CellAttributes() ?>>
<div<?php echo $user->level->ViewAttributes() ?>><?php echo $user->level->ViewValue ?></div>
</td>
	</tr>
</table>
</form>
<p>
<script language="JavaScript" type="text/javascript">
<!--

// Write your table-specific startup script here
// document.write("page loaded");
//-->

</script>
<?php include "footer.php" ?>
<?php

// If control is passed here, simply terminate the page without redirect
Page_Terminate();

// -----------------------------------------------------------------
//  Subroutine Page_Terminate
//  - called when exit page
//  - clean up connection and objects
//  - if url specified, redirect to url, otherwise end response
function Page_Terminate($url = "") {
	global $conn;

	// Page unload event, used in current page
	Page_Unload();

	// Global page unloaded event (in userfn*.php)
	Page_Unloaded();

	 // Close Connection
	$conn->Close();

	// Go to url if specified
	if ($url <> "") {
		ob_end_clean();
		header("Location: $url");
	}
	exit();
}
?>
<?php

// Load row based on key values
function LoadRow() {
	global $conn, $Security, $user;
	$sFilter = $user->SqlKeyFilter();
	if (!is_numeric($user->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($user->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$user->Row_Selecting($sFilter);

	// Load sql based on filter
	$user->CurrentFilter = $sFilter;
	$sSql = $user->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$user->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $user;
	$user->id->setDbValue($rs->fields('id'));
	$user->user->setDbValue($rs->fields('user'));
	$user->password->setDbValue($rs->fields('password'));
	$user->level->setDbValue($rs->fields('level'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $user;

	// Call Row Rendering event
	$user->Row_Rendering();

	// Common render codes for all row types
	// id

	$user->id->CellCssStyle = "";
	$user->id->CellCssClass = "";

	// user
	$user->user->CellCssStyle = "";
	$user->user->CellCssClass = "";

	// password
	$user->password->CellCssStyle = "";
	$user->password->CellCssClass = "";

	// level
	$user->level->CellCssStyle = "";
	$user->level->CellCssClass = "";
	if ($user->RowType == EW_ROWTYPE_VIEW) { // View row

		// id
		$user->id->ViewValue = $user->id->CurrentValue;
		$user->id->CssStyle = "";
		$user->id->CssClass = "";
		$user->id->ViewCustomAttributes = "";

		// user
		$user->user->ViewValue = $user->user->CurrentValue;
		$user->user->CssStyle = "";
		$user->user->CssClass = "";
		$user->user->ViewCustomAttributes = "";

		// password
		$user->password->ViewValue = $user->password->CurrentValue;
		$user->password->CssStyle = "";
		$user->password->CssClass = "";
		$user->password->ViewCustomAttributes = "";

		// level
		$user->level->ViewValue = $user->level->CurrentValue;
		$user->level->CssStyle = "";
		$user->level->CssClass = "";
		$user->level->ViewCustomAttributes = "";

		// id
		$user->id->HrefValue = "";

		// user
		$user->user->HrefValue = "";

		// password
		$user->password->HrefValue = "";

		// level
		$user->level->HrefValue = "";
	} elseif ($user->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($user->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($user->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$user->Row_Rendered();
}
?>
<?php

// Set up Starting Record parameters based on Pager Navigation
function SetUpStartRec() {
	global $nDisplayRecs, $nStartRec, $nTotalRecs, $nPageNo, $user;
	if ($nDisplayRecs == 0) return;

	// Check for a START parameter
	if (@$_GET[EW_TABLE_START_REC] <> "") {
		$nStartRec = $_GET[EW_TABLE_START_REC];
		$user->setStartRecordNumber($nStartRec);
	} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
		$nPageNo = $_GET[EW_TABLE_PAGE_NO];
		if (is_numeric($nPageNo)) {
			$nStartRec = ($nPageNo-1)*$nDisplayRecs+1;
			if ($nStartRec <= 0) {
				$nStartRec = 1;
			} elseif ($nStartRec >= intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1) {
				$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1;
			}
			$user->setStartRecordNumber($nStartRec);
		} else {
			$nStartRec = $user->getStartRecordNumber();
		}
	} else {
		$nStartRec = $user->getStartRecordNumber();
	}

	// Check if correct start record counter
	if (!is_numeric($nStartRec) || $nStartRec == "") { // Avoid invalid start record counter
		$nStartRec = 1; // Reset start record counter
		$user->setStartRecordNumber($nStartRec);
	} elseif (intval($nStartRec) > intval($nTotalRecs)) { // Avoid starting record > total records
		$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to last page first record
		$user->setStartRecordNumber($nStartRec);
	} elseif (($nStartRec-1) % $nDisplayRecs <> 0) {
		$nStartRec = intval(($nStartRec-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to page boundary
		$user->setStartRecordNumber($nStartRec);
	}
}
?>
<?php

// Page Load event
function Page_Load() {

	//echo "Page Load";
}

// Page Unload event
function Page_Unload() {

	//echo "Page Unload";
}
?>
