<?php
define("EW_PAGE_ID", "view", TRUE); // Page ID
define("EW_TABLE_NAME", 'urls', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "urlsinfo.php" ?>
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
$urls->Export = @$_GET["export"]; // Get export parameter
$sExport = $urls->Export; // Get export parameter, used in header
$sExportFile = $urls->TableVar; // Get export file, used in header
?>
<?php
if (@$_GET["id"] <> "") {
	$urls->id->setQueryStringValue($_GET["id"]);
} else {
	Page_Terminate("urlslist.php"); // Return to list page
}

// Get action
if (@$_POST["a_view"] <> "") {
	$urls->CurrentAction = $_POST["a_view"];
} else {
	$urls->CurrentAction = "I"; // Display form
}
switch ($urls->CurrentAction) {
	case "I": // Get a record to display
		if (!LoadRow()) { // Load record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "No records found"; // Set no record message
			Page_Terminate("urlslist.php"); // Return to list
		}
}

// Set return url
$urls->setReturnUrl("urlsview.php");

// Render row
$urls->RowType = EW_ROWTYPE_VIEW;
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
<p><span class="phpmaker">View TABLE: urls
<br><br>
<a href="urlslist.php">Back to List</a>&nbsp;
<a href="urlsadd.php">Add</a>&nbsp;
<a href="<?php echo $urls->EditUrl() ?>">Edit</a>&nbsp;
<a href="<?php echo $urls->CopyUrl() ?>">Copy</a>&nbsp;
<a href="<?php echo $urls->DeleteUrl() ?>">Delete</a>&nbsp;
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
		<td<?php echo $urls->id->CellAttributes() ?>>
<div<?php echo $urls->id->ViewAttributes() ?>><?php echo $urls->id->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">url</td>
		<td<?php echo $urls->url->CellAttributes() ?>>
<div<?php echo $urls->url->ViewAttributes() ?>><?php echo $urls->url->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">digest</td>
		<td<?php echo $urls->digest->CellAttributes() ?>>
<div<?php echo $urls->digest->ViewAttributes() ?>><?php echo $urls->digest->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">type</td>
		<td<?php echo $urls->type->CellAttributes() ?>>
<div<?php echo $urls->type->ViewAttributes() ?>><?php echo $urls->type->ViewValue ?></div>
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
	global $conn, $Security, $urls;
	$sFilter = $urls->SqlKeyFilter();
	if (!is_numeric($urls->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($urls->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$urls->Row_Selecting($sFilter);

	// Load sql based on filter
	$urls->CurrentFilter = $sFilter;
	$sSql = $urls->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$urls->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $urls;
	$urls->id->setDbValue($rs->fields('id'));
	$urls->url->setDbValue($rs->fields('url'));
	$urls->digest->setDbValue($rs->fields('digest'));
	$urls->type->setDbValue($rs->fields('type'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $urls;

	// Call Row Rendering event
	$urls->Row_Rendering();

	// Common render codes for all row types
	// id

	$urls->id->CellCssStyle = "";
	$urls->id->CellCssClass = "";

	// url
	$urls->url->CellCssStyle = "";
	$urls->url->CellCssClass = "";

	// digest
	$urls->digest->CellCssStyle = "";
	$urls->digest->CellCssClass = "";

	// type
	$urls->type->CellCssStyle = "";
	$urls->type->CellCssClass = "";
	if ($urls->RowType == EW_ROWTYPE_VIEW) { // View row

		// id
		$urls->id->ViewValue = $urls->id->CurrentValue;
		$urls->id->CssStyle = "";
		$urls->id->CssClass = "";
		$urls->id->ViewCustomAttributes = "";

		// url
		$urls->url->ViewValue = $urls->url->CurrentValue;
		$urls->url->CssStyle = "";
		$urls->url->CssClass = "";
		$urls->url->ViewCustomAttributes = "";

		// digest
		$urls->digest->ViewValue = $urls->digest->CurrentValue;
		$urls->digest->CssStyle = "";
		$urls->digest->CssClass = "";
		$urls->digest->ViewCustomAttributes = "";

		// type
		$urls->type->ViewValue = $urls->type->CurrentValue;
		$urls->type->CssStyle = "";
		$urls->type->CssClass = "";
		$urls->type->ViewCustomAttributes = "";

		// id
		$urls->id->HrefValue = "";

		// url
		$urls->url->HrefValue = "";

		// digest
		$urls->digest->HrefValue = "";

		// type
		$urls->type->HrefValue = "";
	} elseif ($urls->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($urls->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($urls->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$urls->Row_Rendered();
}
?>
<?php

// Set up Starting Record parameters based on Pager Navigation
function SetUpStartRec() {
	global $nDisplayRecs, $nStartRec, $nTotalRecs, $nPageNo, $urls;
	if ($nDisplayRecs == 0) return;

	// Check for a START parameter
	if (@$_GET[EW_TABLE_START_REC] <> "") {
		$nStartRec = $_GET[EW_TABLE_START_REC];
		$urls->setStartRecordNumber($nStartRec);
	} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
		$nPageNo = $_GET[EW_TABLE_PAGE_NO];
		if (is_numeric($nPageNo)) {
			$nStartRec = ($nPageNo-1)*$nDisplayRecs+1;
			if ($nStartRec <= 0) {
				$nStartRec = 1;
			} elseif ($nStartRec >= intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1) {
				$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1;
			}
			$urls->setStartRecordNumber($nStartRec);
		} else {
			$nStartRec = $urls->getStartRecordNumber();
		}
	} else {
		$nStartRec = $urls->getStartRecordNumber();
	}

	// Check if correct start record counter
	if (!is_numeric($nStartRec) || $nStartRec == "") { // Avoid invalid start record counter
		$nStartRec = 1; // Reset start record counter
		$urls->setStartRecordNumber($nStartRec);
	} elseif (intval($nStartRec) > intval($nTotalRecs)) { // Avoid starting record > total records
		$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to last page first record
		$urls->setStartRecordNumber($nStartRec);
	} elseif (($nStartRec-1) % $nDisplayRecs <> 0) {
		$nStartRec = intval(($nStartRec-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to page boundary
		$urls->setStartRecordNumber($nStartRec);
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
