<?php
define("EW_PAGE_ID", "view", TRUE); // Page ID
define("EW_TABLE_NAME", 'logs', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "logsinfo.php" ?>
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
$logs->Export = @$_GET["export"]; // Get export parameter
$sExport = $logs->Export; // Get export parameter, used in header
$sExportFile = $logs->TableVar; // Get export file, used in header
?>
<?php
if (@$_GET["id"] <> "") {
	$logs->id->setQueryStringValue($_GET["id"]);
} else {
	Page_Terminate("logslist.php"); // Return to list page
}

// Get action
if (@$_POST["a_view"] <> "") {
	$logs->CurrentAction = $_POST["a_view"];
} else {
	$logs->CurrentAction = "I"; // Display form
}
switch ($logs->CurrentAction) {
	case "I": // Get a record to display
		if (!LoadRow()) { // Load record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "No records found"; // Set no record message
			Page_Terminate("logslist.php"); // Return to list
		}
}

// Set return url
$logs->setReturnUrl("logsview.php");

// Render row
$logs->RowType = EW_ROWTYPE_VIEW;
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
<p><span class="phpmaker">View TABLE: logs
<br><br>
<a href="logslist.php">Back to List</a>&nbsp;
<a href="logsadd.php">Add</a>&nbsp;
<a href="<?php echo $logs->EditUrl() ?>">Edit</a>&nbsp;
<a href="<?php echo $logs->CopyUrl() ?>">Copy</a>&nbsp;
<a href="<?php echo $logs->DeleteUrl() ?>">Delete</a>&nbsp;
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
		<td<?php echo $logs->id->CellAttributes() ?>>
<div<?php echo $logs->id->ViewAttributes() ?>><?php echo $logs->id->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">time</td>
		<td<?php echo $logs->time->CellAttributes() ?>>
<div<?php echo $logs->time->ViewAttributes() ?>><?php echo $logs->time->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">client</td>
		<td<?php echo $logs->client->CellAttributes() ?>>
<div<?php echo $logs->client->ViewAttributes() ?>><?php echo $logs->client->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">group</td>
		<td<?php echo $logs->group->CellAttributes() ?>>
<div<?php echo $logs->group->ViewAttributes() ?>><?php echo $logs->group->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">type</td>
		<td<?php echo $logs->type->CellAttributes() ?>>
<div<?php echo $logs->type->ViewAttributes() ?>><?php echo $logs->type->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">message</td>
		<td<?php echo $logs->message->CellAttributes() ?>>
<div<?php echo $logs->message->ViewAttributes() ?>><?php echo $logs->message->ViewValue ?></div>
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
	global $conn, $Security, $logs;
	$sFilter = $logs->SqlKeyFilter();
	if (!is_numeric($logs->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($logs->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$logs->Row_Selecting($sFilter);

	// Load sql based on filter
	$logs->CurrentFilter = $sFilter;
	$sSql = $logs->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$logs->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $logs;
	$logs->id->setDbValue($rs->fields('id'));
	$logs->time->setDbValue($rs->fields('time'));
	$logs->client->setDbValue($rs->fields('client'));
	$logs->group->setDbValue($rs->fields('group'));
	$logs->type->setDbValue($rs->fields('type'));
	$logs->message->setDbValue($rs->fields('message'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $logs;

	// Call Row Rendering event
	$logs->Row_Rendering();

	// Common render codes for all row types
	// id

	$logs->id->CellCssStyle = "";
	$logs->id->CellCssClass = "";

	// time
	$logs->time->CellCssStyle = "";
	$logs->time->CellCssClass = "";

	// client
	$logs->client->CellCssStyle = "";
	$logs->client->CellCssClass = "";

	// group
	$logs->group->CellCssStyle = "";
	$logs->group->CellCssClass = "";

	// type
	$logs->type->CellCssStyle = "";
	$logs->type->CellCssClass = "";

	// message
	$logs->message->CellCssStyle = "";
	$logs->message->CellCssClass = "";
	if ($logs->RowType == EW_ROWTYPE_VIEW) { // View row

		// id
		$logs->id->ViewValue = $logs->id->CurrentValue;
		$logs->id->CssStyle = "";
		$logs->id->CssClass = "";
		$logs->id->ViewCustomAttributes = "";

		// time
		$logs->time->ViewValue = $logs->time->CurrentValue;
		$logs->time->ViewValue = ew_FormatDateTime($logs->time->ViewValue, 5);
		$logs->time->CssStyle = "";
		$logs->time->CssClass = "";
		$logs->time->ViewCustomAttributes = "";

		// client
		$logs->client->ViewValue = $logs->client->CurrentValue;
		$logs->client->CssStyle = "";
		$logs->client->CssClass = "";
		$logs->client->ViewCustomAttributes = "";

		// group
		$logs->group->ViewValue = $logs->group->CurrentValue;
		$logs->group->CssStyle = "";
		$logs->group->CssClass = "";
		$logs->group->ViewCustomAttributes = "";

		// type
		$logs->type->ViewValue = $logs->type->CurrentValue;
		$logs->type->CssStyle = "";
		$logs->type->CssClass = "";
		$logs->type->ViewCustomAttributes = "";

		// message
		$logs->message->ViewValue = $logs->message->CurrentValue;
		$logs->message->CssStyle = "";
		$logs->message->CssClass = "";
		$logs->message->ViewCustomAttributes = "";

		// id
		$logs->id->HrefValue = "";

		// time
		$logs->time->HrefValue = "";

		// client
		$logs->client->HrefValue = "";

		// group
		$logs->group->HrefValue = "";

		// type
		$logs->type->HrefValue = "";

		// message
		$logs->message->HrefValue = "";
	} elseif ($logs->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($logs->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($logs->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$logs->Row_Rendered();
}
?>
<?php

// Set up Starting Record parameters based on Pager Navigation
function SetUpStartRec() {
	global $nDisplayRecs, $nStartRec, $nTotalRecs, $nPageNo, $logs;
	if ($nDisplayRecs == 0) return;

	// Check for a START parameter
	if (@$_GET[EW_TABLE_START_REC] <> "") {
		$nStartRec = $_GET[EW_TABLE_START_REC];
		$logs->setStartRecordNumber($nStartRec);
	} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
		$nPageNo = $_GET[EW_TABLE_PAGE_NO];
		if (is_numeric($nPageNo)) {
			$nStartRec = ($nPageNo-1)*$nDisplayRecs+1;
			if ($nStartRec <= 0) {
				$nStartRec = 1;
			} elseif ($nStartRec >= intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1) {
				$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1;
			}
			$logs->setStartRecordNumber($nStartRec);
		} else {
			$nStartRec = $logs->getStartRecordNumber();
		}
	} else {
		$nStartRec = $logs->getStartRecordNumber();
	}

	// Check if correct start record counter
	if (!is_numeric($nStartRec) || $nStartRec == "") { // Avoid invalid start record counter
		$nStartRec = 1; // Reset start record counter
		$logs->setStartRecordNumber($nStartRec);
	} elseif (intval($nStartRec) > intval($nTotalRecs)) { // Avoid starting record > total records
		$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to last page first record
		$logs->setStartRecordNumber($nStartRec);
	} elseif (($nStartRec-1) % $nDisplayRecs <> 0) {
		$nStartRec = intval(($nStartRec-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to page boundary
		$logs->setStartRecordNumber($nStartRec);
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
