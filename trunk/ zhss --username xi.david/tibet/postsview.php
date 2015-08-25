<?php
define("EW_PAGE_ID", "view", TRUE); // Page ID
define("EW_TABLE_NAME", 'posts', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "postsinfo.php" ?>
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
$posts->Export = @$_GET["export"]; // Get export parameter
$sExport = $posts->Export; // Get export parameter, used in header
$sExportFile = $posts->TableVar; // Get export file, used in header
?>
<?php
if (@$_GET["id"] <> "") {
	$posts->id->setQueryStringValue($_GET["id"]);
} else {
	Page_Terminate("postslist.php"); // Return to list page
}

// Get action
if (@$_POST["a_view"] <> "") {
	$posts->CurrentAction = $_POST["a_view"];
} else {
	$posts->CurrentAction = "I"; // Display form
}
switch ($posts->CurrentAction) {
	case "I": // Get a record to display
		if (!LoadRow()) { // Load record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "No records found"; // Set no record message
			Page_Terminate("postslist.php"); // Return to list
		}
}

// Set return url
$posts->setReturnUrl("postsview.php");

// Render row
$posts->RowType = EW_ROWTYPE_VIEW;
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
<p><span class="phpmaker">View TABLE: posts
<br><br>
<a href="postslist.php">Back to List</a>&nbsp;
<a href="postsadd.php">Add</a>&nbsp;
<a href="<?php echo $posts->EditUrl() ?>">Edit</a>&nbsp;
<a href="<?php echo $posts->CopyUrl() ?>">Copy</a>&nbsp;
<a href="<?php echo $posts->DeleteUrl() ?>">Delete</a>&nbsp;
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
		<td<?php echo $posts->id->CellAttributes() ?>>
<div<?php echo $posts->id->ViewAttributes() ?>><?php echo $posts->id->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">url</td>
		<td<?php echo $posts->url->CellAttributes() ?>>
<div<?php echo $posts->url->ViewAttributes() ?>><?php echo $posts->url->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">datetime</td>
		<td<?php echo $posts->datetime->CellAttributes() ?>>
<div<?php echo $posts->datetime->ViewAttributes() ?>><?php echo $posts->datetime->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">title</td>
		<td<?php echo $posts->title->CellAttributes() ?>>
<div<?php echo $posts->title->ViewAttributes() ?>><?php echo $posts->title->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">projectname</td>
		<td<?php echo $posts->projectname->CellAttributes() ?>>
<div<?php echo $posts->projectname->ViewAttributes() ?>><?php echo $posts->projectname->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">digest</td>
		<td<?php echo $posts->digest->CellAttributes() ?>>
<div<?php echo $posts->digest->ViewAttributes() ?>><?php echo $posts->digest->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">counter</td>
		<td<?php echo $posts->counter->CellAttributes() ?>>
<div<?php echo $posts->counter->ViewAttributes() ?>><?php echo $posts->counter->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">content</td>
		<td<?php echo $posts->content->CellAttributes() ?>>
<div<?php echo $posts->content->ViewAttributes() ?>><?php echo $posts->content->ViewValue ?></div>
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
	global $conn, $Security, $posts;
	$sFilter = $posts->SqlKeyFilter();
	if (!is_numeric($posts->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($posts->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$posts->Row_Selecting($sFilter);

	// Load sql based on filter
	$posts->CurrentFilter = $sFilter;
	$sSql = $posts->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$posts->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $posts;
	$posts->id->setDbValue($rs->fields('id'));
	$posts->url->setDbValue($rs->fields('url'));
	$posts->datetime->setDbValue($rs->fields('datetime'));
	$posts->title->setDbValue($rs->fields('title'));
	$posts->projectname->setDbValue($rs->fields('projectname'));
	$posts->digest->setDbValue($rs->fields('digest'));
	$posts->counter->setDbValue($rs->fields('counter'));
	$posts->content->setDbValue($rs->fields('content'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $posts;

	// Call Row Rendering event
	$posts->Row_Rendering();

	// Common render codes for all row types
	// id

	$posts->id->CellCssStyle = "";
	$posts->id->CellCssClass = "";

	// url
	$posts->url->CellCssStyle = "";
	$posts->url->CellCssClass = "";

	// datetime
	$posts->datetime->CellCssStyle = "";
	$posts->datetime->CellCssClass = "";

	// title
	$posts->title->CellCssStyle = "";
	$posts->title->CellCssClass = "";

	// projectname
	$posts->projectname->CellCssStyle = "";
	$posts->projectname->CellCssClass = "";

	// digest
	$posts->digest->CellCssStyle = "";
	$posts->digest->CellCssClass = "";

	// counter
	$posts->counter->CellCssStyle = "";
	$posts->counter->CellCssClass = "";

	// content
	$posts->content->CellCssStyle = "";
	$posts->content->CellCssClass = "";
	if ($posts->RowType == EW_ROWTYPE_VIEW) { // View row

		// id
		$posts->id->ViewValue = $posts->id->CurrentValue;
		$posts->id->CssStyle = "";
		$posts->id->CssClass = "";
		$posts->id->ViewCustomAttributes = "";

		// url
		$posts->url->ViewValue = $posts->url->CurrentValue;
		$posts->url->CssStyle = "";
		$posts->url->CssClass = "";
		$posts->url->ViewCustomAttributes = "";

		// datetime
		$posts->datetime->ViewValue = $posts->datetime->CurrentValue;
		$posts->datetime->ViewValue = ew_FormatDateTime($posts->datetime->ViewValue, 5);
		$posts->datetime->CssStyle = "";
		$posts->datetime->CssClass = "";
		$posts->datetime->ViewCustomAttributes = "";

		// title
		$posts->title->ViewValue = $posts->title->CurrentValue;
		$posts->title->CssStyle = "";
		$posts->title->CssClass = "";
		$posts->title->ViewCustomAttributes = "";

		// projectname
		$posts->projectname->ViewValue = $posts->projectname->CurrentValue;
		$posts->projectname->CssStyle = "";
		$posts->projectname->CssClass = "";
		$posts->projectname->ViewCustomAttributes = "";

		// digest
		$posts->digest->ViewValue = $posts->digest->CurrentValue;
		$posts->digest->CssStyle = "";
		$posts->digest->CssClass = "";
		$posts->digest->ViewCustomAttributes = "";

		// counter
		$posts->counter->ViewValue = $posts->counter->CurrentValue;
		$posts->counter->CssStyle = "";
		$posts->counter->CssClass = "";
		$posts->counter->ViewCustomAttributes = "";

		// content
		$posts->content->ViewValue = $posts->content->CurrentValue;
		if (!is_null($posts->content->ViewValue)) $posts->content->ViewValue = str_replace("\n", "<br>", $posts->content->ViewValue); 
		$posts->content->CssStyle = "";
		$posts->content->CssClass = "";
		$posts->content->ViewCustomAttributes = "";

		// id
		$posts->id->HrefValue = "";

		// url
		$posts->url->HrefValue = "";

		// datetime
		$posts->datetime->HrefValue = "";

		// title
		$posts->title->HrefValue = "";

		// projectname
		$posts->projectname->HrefValue = "";

		// digest
		$posts->digest->HrefValue = "";

		// counter
		$posts->counter->HrefValue = "";

		// content
		$posts->content->HrefValue = "";
	} elseif ($posts->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($posts->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($posts->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$posts->Row_Rendered();
}
?>
<?php

// Set up Starting Record parameters based on Pager Navigation
function SetUpStartRec() {
	global $nDisplayRecs, $nStartRec, $nTotalRecs, $nPageNo, $posts;
	if ($nDisplayRecs == 0) return;

	// Check for a START parameter
	if (@$_GET[EW_TABLE_START_REC] <> "") {
		$nStartRec = $_GET[EW_TABLE_START_REC];
		$posts->setStartRecordNumber($nStartRec);
	} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
		$nPageNo = $_GET[EW_TABLE_PAGE_NO];
		if (is_numeric($nPageNo)) {
			$nStartRec = ($nPageNo-1)*$nDisplayRecs+1;
			if ($nStartRec <= 0) {
				$nStartRec = 1;
			} elseif ($nStartRec >= intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1) {
				$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1;
			}
			$posts->setStartRecordNumber($nStartRec);
		} else {
			$nStartRec = $posts->getStartRecordNumber();
		}
	} else {
		$nStartRec = $posts->getStartRecordNumber();
	}

	// Check if correct start record counter
	if (!is_numeric($nStartRec) || $nStartRec == "") { // Avoid invalid start record counter
		$nStartRec = 1; // Reset start record counter
		$posts->setStartRecordNumber($nStartRec);
	} elseif (intval($nStartRec) > intval($nTotalRecs)) { // Avoid starting record > total records
		$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to last page first record
		$posts->setStartRecordNumber($nStartRec);
	} elseif (($nStartRec-1) % $nDisplayRecs <> 0) {
		$nStartRec = intval(($nStartRec-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to page boundary
		$posts->setStartRecordNumber($nStartRec);
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
