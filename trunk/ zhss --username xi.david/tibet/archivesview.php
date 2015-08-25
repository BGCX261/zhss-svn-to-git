<?php
define("EW_PAGE_ID", "view", TRUE); // Page ID
define("EW_TABLE_NAME", 'archives', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "archivesinfo.php" ?>
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
$archives->Export = @$_GET["export"]; // Get export parameter
$sExport = $archives->Export; // Get export parameter, used in header
$sExportFile = $archives->TableVar; // Get export file, used in header
?>
<?php
if (@$_GET["id"] <> "") {
	$archives->id->setQueryStringValue($_GET["id"]);
} else {
	Page_Terminate("archiveslist.php"); // Return to list page
}

// Get action
if (@$_POST["a_view"] <> "") {
	$archives->CurrentAction = $_POST["a_view"];
} else {
	$archives->CurrentAction = "I"; // Display form
}
switch ($archives->CurrentAction) {
	case "I": // Get a record to display
		if (!LoadRow()) { // Load record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "No records found"; // Set no record message
			Page_Terminate("archiveslist.php"); // Return to list
		}
}

// Set return url
$archives->setReturnUrl("archivesview.php");

// Render row
$archives->RowType = EW_ROWTYPE_VIEW;
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
<p><span class="phpmaker">View TABLE: archives
<br><br>
<a href="archiveslist.php">Back to List</a>&nbsp;
<a href="archivesadd.php">Add</a>&nbsp;
<a href="<?php echo $archives->EditUrl() ?>">Edit</a>&nbsp;
<a href="<?php echo $archives->CopyUrl() ?>">Copy</a>&nbsp;
<a href="<?php echo $archives->DeleteUrl() ?>">Delete</a>&nbsp;
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
		<td<?php echo $archives->id->CellAttributes() ?>>
<div<?php echo $archives->id->ViewAttributes() ?>><?php echo $archives->id->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">url</td>
		<td<?php echo $archives->url->CellAttributes() ?>>
<div<?php echo $archives->url->ViewAttributes() ?>><?php echo $archives->url->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">projectname</td>
		<td<?php echo $archives->projectname->CellAttributes() ?>>
<div<?php echo $archives->projectname->ViewAttributes() ?>><?php echo $archives->projectname->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">title</td>
		<td<?php echo $archives->title->CellAttributes() ?>>
<div<?php echo $archives->title->ViewAttributes() ?>><?php echo $archives->title->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">datetime</td>
		<td<?php echo $archives->datetime->CellAttributes() ?>>
<div<?php echo $archives->datetime->ViewAttributes() ?>><?php echo $archives->datetime->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">content</td>
		<td<?php echo $archives->content->CellAttributes() ?>>
<div<?php echo $archives->content->ViewAttributes() ?>><?php echo $archives->content->ViewValue ?></div>
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
	global $conn, $Security, $archives;
	$sFilter = $archives->SqlKeyFilter();
	if (!is_numeric($archives->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($archives->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$archives->Row_Selecting($sFilter);

	// Load sql based on filter
	$archives->CurrentFilter = $sFilter;
	$sSql = $archives->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$archives->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $archives;
	$archives->id->setDbValue($rs->fields('id'));
	$archives->url->setDbValue($rs->fields('url'));
	$archives->projectname->setDbValue($rs->fields('projectname'));
	$archives->title->setDbValue($rs->fields('title'));
	$archives->datetime->setDbValue($rs->fields('datetime'));
	$archives->content->setDbValue($rs->fields('content'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $archives;

	// Call Row Rendering event
	$archives->Row_Rendering();

	// Common render codes for all row types
	// id

	$archives->id->CellCssStyle = "";
	$archives->id->CellCssClass = "";

	// url
	$archives->url->CellCssStyle = "";
	$archives->url->CellCssClass = "";

	// projectname
	$archives->projectname->CellCssStyle = "";
	$archives->projectname->CellCssClass = "";

	// title
	$archives->title->CellCssStyle = "";
	$archives->title->CellCssClass = "";

	// datetime
	$archives->datetime->CellCssStyle = "";
	$archives->datetime->CellCssClass = "";

	// content
	$archives->content->CellCssStyle = "";
	$archives->content->CellCssClass = "";
	if ($archives->RowType == EW_ROWTYPE_VIEW) { // View row

		// id
		$archives->id->ViewValue = $archives->id->CurrentValue;
		$archives->id->CssStyle = "";
		$archives->id->CssClass = "";
		$archives->id->ViewCustomAttributes = "";

		// url
		$archives->url->ViewValue = $archives->url->CurrentValue;
		$archives->url->CssStyle = "";
		$archives->url->CssClass = "";
		$archives->url->ViewCustomAttributes = "";

		// projectname
		$archives->projectname->ViewValue = $archives->projectname->CurrentValue;
		$archives->projectname->CssStyle = "";
		$archives->projectname->CssClass = "";
		$archives->projectname->ViewCustomAttributes = "";

		// title
		$archives->title->ViewValue = $archives->title->CurrentValue;
		$archives->title->CssStyle = "";
		$archives->title->CssClass = "";
		$archives->title->ViewCustomAttributes = "";

		// datetime
		$archives->datetime->ViewValue = $archives->datetime->CurrentValue;
		$archives->datetime->ViewValue = ew_FormatDateTime($archives->datetime->ViewValue, 5);
		$archives->datetime->CssStyle = "";
		$archives->datetime->CssClass = "";
		$archives->datetime->ViewCustomAttributes = "";

		// content
		$archives->content->ViewValue = $archives->content->CurrentValue;
		if (!is_null($archives->content->ViewValue)) $archives->content->ViewValue = str_replace("\n", "<br>", $archives->content->ViewValue); 
		$archives->content->CssStyle = "";
		$archives->content->CssClass = "";
		$archives->content->ViewCustomAttributes = "";

		// id
		$archives->id->HrefValue = "";

		// url
		$archives->url->HrefValue = "";

		// projectname
		$archives->projectname->HrefValue = "";

		// title
		$archives->title->HrefValue = "";

		// datetime
		$archives->datetime->HrefValue = "";

		// content
		$archives->content->HrefValue = "";
	} elseif ($archives->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($archives->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($archives->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$archives->Row_Rendered();
}
?>
<?php

// Set up Starting Record parameters based on Pager Navigation
function SetUpStartRec() {
	global $nDisplayRecs, $nStartRec, $nTotalRecs, $nPageNo, $archives;
	if ($nDisplayRecs == 0) return;

	// Check for a START parameter
	if (@$_GET[EW_TABLE_START_REC] <> "") {
		$nStartRec = $_GET[EW_TABLE_START_REC];
		$archives->setStartRecordNumber($nStartRec);
	} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
		$nPageNo = $_GET[EW_TABLE_PAGE_NO];
		if (is_numeric($nPageNo)) {
			$nStartRec = ($nPageNo-1)*$nDisplayRecs+1;
			if ($nStartRec <= 0) {
				$nStartRec = 1;
			} elseif ($nStartRec >= intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1) {
				$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1;
			}
			$archives->setStartRecordNumber($nStartRec);
		} else {
			$nStartRec = $archives->getStartRecordNumber();
		}
	} else {
		$nStartRec = $archives->getStartRecordNumber();
	}

	// Check if correct start record counter
	if (!is_numeric($nStartRec) || $nStartRec == "") { // Avoid invalid start record counter
		$nStartRec = 1; // Reset start record counter
		$archives->setStartRecordNumber($nStartRec);
	} elseif (intval($nStartRec) > intval($nTotalRecs)) { // Avoid starting record > total records
		$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to last page first record
		$archives->setStartRecordNumber($nStartRec);
	} elseif (($nStartRec-1) % $nDisplayRecs <> 0) {
		$nStartRec = intval(($nStartRec-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to page boundary
		$archives->setStartRecordNumber($nStartRec);
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
