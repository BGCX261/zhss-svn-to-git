<?php
define("EW_PAGE_ID", "view", TRUE); // Page ID
define("EW_TABLE_NAME", 'categories', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "categoriesinfo.php" ?>
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
$categories->Export = @$_GET["export"]; // Get export parameter
$sExport = $categories->Export; // Get export parameter, used in header
$sExportFile = $categories->TableVar; // Get export file, used in header
?>
<?php
if (@$_GET["id"] <> "") {
	$categories->id->setQueryStringValue($_GET["id"]);
} else {
	Page_Terminate("categorieslist.php"); // Return to list page
}

// Get action
if (@$_POST["a_view"] <> "") {
	$categories->CurrentAction = $_POST["a_view"];
} else {
	$categories->CurrentAction = "I"; // Display form
}
switch ($categories->CurrentAction) {
	case "I": // Get a record to display
		if (!LoadRow()) { // Load record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "No records found"; // Set no record message
			Page_Terminate("categorieslist.php"); // Return to list
		}
}

// Set return url
$categories->setReturnUrl("categoriesview.php");

// Render row
$categories->RowType = EW_ROWTYPE_VIEW;
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
<p><span class="phpmaker">View TABLE: categories
<br><br>
<a href="categorieslist.php">Back to List</a>&nbsp;
<a href="categoriesadd.php">Add</a>&nbsp;
<a href="<?php echo $categories->EditUrl() ?>">Edit</a>&nbsp;
<a href="<?php echo $categories->CopyUrl() ?>">Copy</a>&nbsp;
<a href="<?php echo $categories->DeleteUrl() ?>">Delete</a>&nbsp;
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
		<td<?php echo $categories->id->CellAttributes() ?>>
<div<?php echo $categories->id->ViewAttributes() ?>><?php echo $categories->id->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">name</td>
		<td<?php echo $categories->name->CellAttributes() ?>>
<div<?php echo $categories->name->ViewAttributes() ?>><?php echo $categories->name->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">keywords</td>
		<td<?php echo $categories->keywords->CellAttributes() ?>>
<div<?php echo $categories->keywords->ViewAttributes() ?>><?php echo $categories->keywords->ViewValue ?></div>
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
	global $conn, $Security, $categories;
	$sFilter = $categories->SqlKeyFilter();
	if (!is_numeric($categories->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($categories->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$categories->Row_Selecting($sFilter);

	// Load sql based on filter
	$categories->CurrentFilter = $sFilter;
	$sSql = $categories->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$categories->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $categories;
	$categories->id->setDbValue($rs->fields('id'));
	$categories->name->setDbValue($rs->fields('name'));
	$categories->keywords->setDbValue($rs->fields('keywords'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $categories;

	// Call Row Rendering event
	$categories->Row_Rendering();

	// Common render codes for all row types
	// id

	$categories->id->CellCssStyle = "";
	$categories->id->CellCssClass = "";

	// name
	$categories->name->CellCssStyle = "";
	$categories->name->CellCssClass = "";

	// keywords
	$categories->keywords->CellCssStyle = "";
	$categories->keywords->CellCssClass = "";
	if ($categories->RowType == EW_ROWTYPE_VIEW) { // View row

		// id
		$categories->id->ViewValue = $categories->id->CurrentValue;
		$categories->id->CssStyle = "";
		$categories->id->CssClass = "";
		$categories->id->ViewCustomAttributes = "";

		// name
		$categories->name->ViewValue = $categories->name->CurrentValue;
		$categories->name->CssStyle = "";
		$categories->name->CssClass = "";
		$categories->name->ViewCustomAttributes = "";

		// keywords
		$categories->keywords->ViewValue = $categories->keywords->CurrentValue;
		if (!is_null($categories->keywords->ViewValue)) $categories->keywords->ViewValue = str_replace("\n", "<br>", $categories->keywords->ViewValue); 
		$categories->keywords->CssStyle = "";
		$categories->keywords->CssClass = "";
		$categories->keywords->ViewCustomAttributes = "";

		// id
		$categories->id->HrefValue = "";

		// name
		$categories->name->HrefValue = "";

		// keywords
		$categories->keywords->HrefValue = "";
	} elseif ($categories->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($categories->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($categories->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$categories->Row_Rendered();
}
?>
<?php

// Set up Starting Record parameters based on Pager Navigation
function SetUpStartRec() {
	global $nDisplayRecs, $nStartRec, $nTotalRecs, $nPageNo, $categories;
	if ($nDisplayRecs == 0) return;

	// Check for a START parameter
	if (@$_GET[EW_TABLE_START_REC] <> "") {
		$nStartRec = $_GET[EW_TABLE_START_REC];
		$categories->setStartRecordNumber($nStartRec);
	} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
		$nPageNo = $_GET[EW_TABLE_PAGE_NO];
		if (is_numeric($nPageNo)) {
			$nStartRec = ($nPageNo-1)*$nDisplayRecs+1;
			if ($nStartRec <= 0) {
				$nStartRec = 1;
			} elseif ($nStartRec >= intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1) {
				$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1;
			}
			$categories->setStartRecordNumber($nStartRec);
		} else {
			$nStartRec = $categories->getStartRecordNumber();
		}
	} else {
		$nStartRec = $categories->getStartRecordNumber();
	}

	// Check if correct start record counter
	if (!is_numeric($nStartRec) || $nStartRec == "") { // Avoid invalid start record counter
		$nStartRec = 1; // Reset start record counter
		$categories->setStartRecordNumber($nStartRec);
	} elseif (intval($nStartRec) > intval($nTotalRecs)) { // Avoid starting record > total records
		$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to last page first record
		$categories->setStartRecordNumber($nStartRec);
	} elseif (($nStartRec-1) % $nDisplayRecs <> 0) {
		$nStartRec = intval(($nStartRec-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to page boundary
		$categories->setStartRecordNumber($nStartRec);
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
