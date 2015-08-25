<?php
define("EW_PAGE_ID", "delete", TRUE); // Page ID
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

// Load Key Parameters
$sKey = "";
$bSingleDelete = TRUE; // Initialize as single delete
$arRecKeys = array();
$nKeySelected = 0; // Initialize selected key count
$sFilter = "";
if (@$_GET["id"] <> "") {
	$posts->id->setQueryStringValue($_GET["id"]);
	if (!is_numeric($posts->id->QueryStringValue)) {
		Page_Terminate($posts->getReturnUrl()); // Prevent sql injection, exit
	}
	$sKey .= $posts->id->QueryStringValue;
} else {
	$bSingleDelete = FALSE;
}
if ($bSingleDelete) {
	$nKeySelected = 1; // Set up key selected count
	$arRecKeys[0] = $sKey;
} else {
	if (isset($_POST["key_m"])) { // Key in form
		$nKeySelected = count($_POST["key_m"]); // Set up key selected count
		$arRecKeys = ew_StripSlashes($_POST["key_m"]);
	}
}
if ($nKeySelected <= 0) Page_Terminate($posts->getReturnUrl()); // No key specified, exit

// Build filter
foreach ($arRecKeys as $sKey) {
	$sFilter .= "(";

	// Set up key field
	$sKeyFld = $sKey;
	if (!is_numeric($sKeyFld)) {
		Page_Terminate($posts->getReturnUrl()); // Prevent sql injection, exit
	}
	$sFilter .= "`id`=" . ew_AdjustSql($sKeyFld) . " AND ";
	if (substr($sFilter, -5) == " AND ") $sFilter = substr($sFilter, 0, strlen($sFilter)-5) . ") OR ";
}
if (substr($sFilter, -4) == " OR ") $sFilter = substr($sFilter, 0, strlen($sFilter)-4);

// Set up filter (Sql Where Clause) and get Return Sql
// Sql constructor in posts class, postsinfo.php

$posts->CurrentFilter = $sFilter;

// Get action
if (@$_POST["a_delete"] <> "") {
	$posts->CurrentAction = $_POST["a_delete"];
} else {
	$posts->CurrentAction = "I"; // Display record
}
switch ($posts->CurrentAction) {
	case "D": // Delete
		$posts->SendEmail = TRUE; // Send email on delete success
		if (DeleteRows()) { // delete rows
			$_SESSION[EW_SESSION_MESSAGE] = "Delete Successful"; // Set up success message
			Page_Terminate($posts->getReturnUrl()); // Return to caller
		}
}

// Load records for display
$rs = LoadRecordset();
$nTotalRecs = $rs->RecordCount(); // Get record count
if ($nTotalRecs <= 0) { // No record found, exit
	$rs->Close();
	Page_Terminate($posts->getReturnUrl()); // Return to caller
}
?>
<?php include "header.php" ?>
<script type="text/javascript">
<!--
var EW_PAGE_ID = "delete"; // Page id

//-->
</script>
<script language="JavaScript" type="text/javascript">
<!--

// Write your client script here, no need to add script tags.
// To include another .js script, use:
// ew_ClientScriptInclude("my_javascript.js"); 
//-->

</script>
<p><span class="phpmaker">Delete from TABLE: posts<br><br><a href="<?php echo $posts->getReturnUrl() ?>">Go Back</a></span></p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<form action="postsdelete.php" method="post">
<p>
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($arRecKeys as $sKey) { ?>
<input type="hidden" name="key_m[]" id="key_m[]" value="<?php echo ew_HtmlEncode($sKey) ?>">
<?php } ?>
<table class="ewTable">
	<tr class="ewTableHeader">
		<td valign="top">id</td>
		<td valign="top">url</td>
		<td valign="top">datetime</td>
		<td valign="top">title</td>
		<td valign="top">projectname</td>
		<td valign="top">digest</td>
		<td valign="top">counter</td>
	</tr>
<?php
$nRecCount = 0;
$i = 0;
while (!$rs->EOF) {
	$nRecCount++;

	// Set row class and style
	$posts->CssClass = "ewTableRow";
	$posts->CssStyle = "";

	// Display alternate color for rows
	if ($nRecCount % 2 <> 1) {
		$posts->CssClass = "ewTableAltRow";
	}

	// Get the field contents
	LoadRowValues($rs);

	// Render row value
	$posts->RowType = EW_ROWTYPE_VIEW; // view
	RenderRow();
?>
	<tr<?php echo $posts->DisplayAttributes() ?>>
		<td<?php echo $posts->id->CellAttributes() ?>>
<div<?php echo $posts->id->ViewAttributes() ?>><?php echo $posts->id->ViewValue ?></div>
</td>
		<td<?php echo $posts->url->CellAttributes() ?>>
<div<?php echo $posts->url->ViewAttributes() ?>><?php echo $posts->url->ViewValue ?></div>
</td>
		<td<?php echo $posts->datetime->CellAttributes() ?>>
<div<?php echo $posts->datetime->ViewAttributes() ?>><?php echo $posts->datetime->ViewValue ?></div>
</td>
		<td<?php echo $posts->title->CellAttributes() ?>>
<div<?php echo $posts->title->ViewAttributes() ?>><?php echo $posts->title->ViewValue ?></div>
</td>
		<td<?php echo $posts->projectname->CellAttributes() ?>>
<div<?php echo $posts->projectname->ViewAttributes() ?>><?php echo $posts->projectname->ViewValue ?></div>
</td>
		<td<?php echo $posts->digest->CellAttributes() ?>>
<div<?php echo $posts->digest->ViewAttributes() ?>><?php echo $posts->digest->ViewValue ?></div>
</td>
		<td<?php echo $posts->counter->CellAttributes() ?>>
<div<?php echo $posts->counter->ViewAttributes() ?>><?php echo $posts->counter->ViewValue ?></div>
</td>
	</tr>
<?php
	$rs->MoveNext();
}
$rs->Close();
?>
</table>
<p>
<input type="submit" name="Action" id="Action" value="Confirm Delete">
</form>
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

// ------------------------------------------------
//  Function DeleteRows
//  - Delete Records based on current filter
function DeleteRows() {
	global $conn, $Security, $posts;
	$DeleteRows = TRUE;
	$sWrkFilter = $posts->CurrentFilter;

	// Set up filter (Sql Where Clause) and get Return Sql
	// Sql constructor in posts class, postsinfo.php

	$posts->CurrentFilter = $sWrkFilter;
	$sSql = $posts->SQL();
	$conn->raiseErrorFn = 'ew_ErrorFn';
	$rs = $conn->Execute($sSql);
	$conn->raiseErrorFn = '';
	if ($rs === FALSE) {
		return FALSE;
	} elseif ($rs->EOF) {
		$_SESSION[EW_SESSION_MESSAGE] = "No records found"; // No record found
		$rs->Close();
		return FALSE;
	}
	$conn->BeginTrans();

	// Clone old rows
	$rsold = ($rs) ? $rs->GetRows() : array();
	if ($rs) $rs->Close();

	// Call row deleting event
	if ($DeleteRows) {
		foreach ($rsold as $row) {
			$DeleteRows = $posts->Row_Deleting($row);
			if (!$DeleteRows) break;
		}
	}
	if ($DeleteRows) {
		$sKey = "";
		foreach ($rsold as $row) {
			$sThisKey = "";
			if ($sThisKey <> "") $sThisKey .= EW_COMPOSITE_KEY_SEPARATOR;
			$sThisKey .= $row['id'];
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$DeleteRows = $conn->Execute($posts->DeleteSQL($row)); // Delete
			$conn->raiseErrorFn = '';
			if ($DeleteRows === FALSE)
				break;
			if ($sKey <> "") $sKey .= ", ";
			$sKey .= $sThisKey;
		}
	} else {

		// Set up error message
		if ($posts->CancelMessage <> "") {
			$_SESSION[EW_SESSION_MESSAGE] = $posts->CancelMessage;
			$posts->CancelMessage = "";
		} else {
			$_SESSION[EW_SESSION_MESSAGE] = "Delete cancelled";
		}
	}
	if ($DeleteRows) {
		$conn->CommitTrans(); // Commit the changes
	} else {
		$conn->RollbackTrans(); // Rollback changes
	}

	// Call recordset deleted event
	if ($DeleteRows) {
		foreach ($rsold as $row) {
			$posts->Row_Deleted($row);
		}	
	}
	return $DeleteRows;
}
?>
<?php

// Load recordset
function LoadRecordset($offset = -1, $rowcnt = -1) {
	global $conn, $posts;

	// Call Recordset Selecting event
	$posts->Recordset_Selecting($posts->CurrentFilter);

	// Load list page sql
	$sSql = $posts->SelectSQL();
	if ($offset > -1 && $rowcnt > -1) $sSql .= " LIMIT $offset, $rowcnt";

	// Load recordset
	$conn->raiseErrorFn = 'ew_ErrorFn';	
	$rs = $conn->Execute($sSql);
	$conn->raiseErrorFn = '';

	// Call Recordset Selected event
	$posts->Recordset_Selected($rs);
	return $rs;
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
	} elseif ($posts->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($posts->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($posts->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$posts->Row_Rendered();
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
