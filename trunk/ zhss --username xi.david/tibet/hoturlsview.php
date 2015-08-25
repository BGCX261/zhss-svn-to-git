<?php
define("EW_PAGE_ID", "view", TRUE); // Page ID
define("EW_TABLE_NAME", 'hoturls', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "hoturlsinfo.php" ?>
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
$hoturls->Export = @$_GET["export"]; // Get export parameter
$sExport = $hoturls->Export; // Get export parameter, used in header
$sExportFile = $hoturls->TableVar; // Get export file, used in header
?>
<?php
if (@$_GET["id"] <> "") {
	$hoturls->id->setQueryStringValue($_GET["id"]);
} else {
	Page_Terminate("hoturlslist.php"); // Return to list page
}

// Get action
if (@$_POST["a_view"] <> "") {
	$hoturls->CurrentAction = $_POST["a_view"];
} else {
	$hoturls->CurrentAction = "I"; // Display form
}
switch ($hoturls->CurrentAction) {
	case "I": // Get a record to display
		if (!LoadRow()) { // Load record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "No records found"; // Set no record message
			Page_Terminate("hoturlslist.php"); // Return to list
		}
}

// Set return url
$hoturls->setReturnUrl("hoturlsview.php");

// Render row
$hoturls->RowType = EW_ROWTYPE_VIEW;
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
<p><span class="phpmaker">View TABLE: hoturls
<br><br>
<a href="hoturlslist.php">Back to List</a>&nbsp;
<a href="hoturlsadd.php">Add</a>&nbsp;
<a href="<?php echo $hoturls->EditUrl() ?>">Edit</a>&nbsp;
<a href="<?php echo $hoturls->CopyUrl() ?>">Copy</a>&nbsp;
<a href="<?php echo $hoturls->DeleteUrl() ?>">Delete</a>&nbsp;
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
		<td<?php echo $hoturls->id->CellAttributes() ?>>
<div<?php echo $hoturls->id->ViewAttributes() ?>><?php echo $hoturls->id->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">url</td>
		<td<?php echo $hoturls->url->CellAttributes() ?>>
<div<?php echo $hoturls->url->ViewAttributes() ?>><?php echo $hoturls->url->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">projectname</td>
		<td<?php echo $hoturls->projectname->CellAttributes() ?>>
<div<?php echo $hoturls->projectname->ViewAttributes() ?>><?php echo $hoturls->projectname->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">datetime</td>
		<td<?php echo $hoturls->datetime->CellAttributes() ?>>
<div<?php echo $hoturls->datetime->ViewAttributes() ?>><?php echo $hoturls->datetime->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">categories</td>
		<td<?php echo $hoturls->categories->CellAttributes() ?>>
<div<?php echo $hoturls->categories->ViewAttributes() ?>><?php echo $hoturls->categories->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">title</td>
		<td<?php echo $hoturls->title->CellAttributes() ?>>
<div<?php echo $hoturls->title->ViewAttributes() ?>><?php echo $hoturls->title->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">content</td>
		<td<?php echo $hoturls->content->CellAttributes() ?>>
<div<?php echo $hoturls->content->ViewAttributes() ?>><?php echo $hoturls->content->ViewValue ?></div>
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
	global $conn, $Security, $hoturls;
	$sFilter = $hoturls->SqlKeyFilter();
	if (!is_numeric($hoturls->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($hoturls->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$hoturls->Row_Selecting($sFilter);

	// Load sql based on filter
	$hoturls->CurrentFilter = $sFilter;
	$sSql = $hoturls->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$hoturls->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $hoturls;
	$hoturls->id->setDbValue($rs->fields('id'));
	$hoturls->url->setDbValue($rs->fields('url'));
	$hoturls->projectname->setDbValue($rs->fields('projectname'));
	$hoturls->datetime->setDbValue($rs->fields('datetime'));
	$hoturls->categories->setDbValue($rs->fields('categories'));
	$hoturls->title->setDbValue($rs->fields('title'));
	$hoturls->content->setDbValue($rs->fields('content'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $hoturls;

	// Call Row Rendering event
	$hoturls->Row_Rendering();

	// Common render codes for all row types
	// id

	$hoturls->id->CellCssStyle = "";
	$hoturls->id->CellCssClass = "";

	// url
	$hoturls->url->CellCssStyle = "";
	$hoturls->url->CellCssClass = "";

	// projectname
	$hoturls->projectname->CellCssStyle = "";
	$hoturls->projectname->CellCssClass = "";

	// datetime
	$hoturls->datetime->CellCssStyle = "";
	$hoturls->datetime->CellCssClass = "";

	// categories
	$hoturls->categories->CellCssStyle = "";
	$hoturls->categories->CellCssClass = "";

	// title
	$hoturls->title->CellCssStyle = "";
	$hoturls->title->CellCssClass = "";

	// content
	$hoturls->content->CellCssStyle = "";
	$hoturls->content->CellCssClass = "";
	if ($hoturls->RowType == EW_ROWTYPE_VIEW) { // View row

		// id
		$hoturls->id->ViewValue = $hoturls->id->CurrentValue;
		$hoturls->id->CssStyle = "";
		$hoturls->id->CssClass = "";
		$hoturls->id->ViewCustomAttributes = "";

		// url
		$hoturls->url->ViewValue = $hoturls->url->CurrentValue;
		$hoturls->url->CssStyle = "";
		$hoturls->url->CssClass = "";
		$hoturls->url->ViewCustomAttributes = "";

		// projectname
		$hoturls->projectname->ViewValue = $hoturls->projectname->CurrentValue;
		$hoturls->projectname->CssStyle = "";
		$hoturls->projectname->CssClass = "";
		$hoturls->projectname->ViewCustomAttributes = "";

		// datetime
		$hoturls->datetime->ViewValue = $hoturls->datetime->CurrentValue;
		$hoturls->datetime->ViewValue = ew_FormatDateTime($hoturls->datetime->ViewValue, 5);
		$hoturls->datetime->CssStyle = "";
		$hoturls->datetime->CssClass = "";
		$hoturls->datetime->ViewCustomAttributes = "";

		// categories
		$hoturls->categories->ViewValue = $hoturls->categories->CurrentValue;
		$hoturls->categories->CssStyle = "";
		$hoturls->categories->CssClass = "";
		$hoturls->categories->ViewCustomAttributes = "";

		// title
		$hoturls->title->ViewValue = $hoturls->title->CurrentValue;
		$hoturls->title->CssStyle = "";
		$hoturls->title->CssClass = "";
		$hoturls->title->ViewCustomAttributes = "";

		// content
		$hoturls->content->ViewValue = $hoturls->content->CurrentValue;
		if (!is_null($hoturls->content->ViewValue)) $hoturls->content->ViewValue = str_replace("\n", "<br>", $hoturls->content->ViewValue); 
		$hoturls->content->CssStyle = "";
		$hoturls->content->CssClass = "";
		$hoturls->content->ViewCustomAttributes = "";

		// id
		$hoturls->id->HrefValue = "";

		// url
		$hoturls->url->HrefValue = "";

		// projectname
		$hoturls->projectname->HrefValue = "";

		// datetime
		$hoturls->datetime->HrefValue = "";

		// categories
		$hoturls->categories->HrefValue = "";

		// title
		$hoturls->title->HrefValue = "";

		// content
		$hoturls->content->HrefValue = "";
	} elseif ($hoturls->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($hoturls->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($hoturls->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$hoturls->Row_Rendered();
}
?>
<?php

// Set up Starting Record parameters based on Pager Navigation
function SetUpStartRec() {
	global $nDisplayRecs, $nStartRec, $nTotalRecs, $nPageNo, $hoturls;
	if ($nDisplayRecs == 0) return;

	// Check for a START parameter
	if (@$_GET[EW_TABLE_START_REC] <> "") {
		$nStartRec = $_GET[EW_TABLE_START_REC];
		$hoturls->setStartRecordNumber($nStartRec);
	} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
		$nPageNo = $_GET[EW_TABLE_PAGE_NO];
		if (is_numeric($nPageNo)) {
			$nStartRec = ($nPageNo-1)*$nDisplayRecs+1;
			if ($nStartRec <= 0) {
				$nStartRec = 1;
			} elseif ($nStartRec >= intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1) {
				$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1;
			}
			$hoturls->setStartRecordNumber($nStartRec);
		} else {
			$nStartRec = $hoturls->getStartRecordNumber();
		}
	} else {
		$nStartRec = $hoturls->getStartRecordNumber();
	}

	// Check if correct start record counter
	if (!is_numeric($nStartRec) || $nStartRec == "") { // Avoid invalid start record counter
		$nStartRec = 1; // Reset start record counter
		$hoturls->setStartRecordNumber($nStartRec);
	} elseif (intval($nStartRec) > intval($nTotalRecs)) { // Avoid starting record > total records
		$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to last page first record
		$hoturls->setStartRecordNumber($nStartRec);
	} elseif (($nStartRec-1) % $nDisplayRecs <> 0) {
		$nStartRec = intval(($nStartRec-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to page boundary
		$hoturls->setStartRecordNumber($nStartRec);
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
