<?php
define("EW_PAGE_ID", "delete", TRUE); // Page ID
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

// Load Key Parameters
$sKey = "";
$bSingleDelete = TRUE; // Initialize as single delete
$arRecKeys = array();
$nKeySelected = 0; // Initialize selected key count
$sFilter = "";
if (@$_GET["id"] <> "") {
	$urls->id->setQueryStringValue($_GET["id"]);
	if (!is_numeric($urls->id->QueryStringValue)) {
		Page_Terminate($urls->getReturnUrl()); // Prevent sql injection, exit
	}
	$sKey .= $urls->id->QueryStringValue;
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
if ($nKeySelected <= 0) Page_Terminate($urls->getReturnUrl()); // No key specified, exit

// Build filter
foreach ($arRecKeys as $sKey) {
	$sFilter .= "(";

	// Set up key field
	$sKeyFld = $sKey;
	if (!is_numeric($sKeyFld)) {
		Page_Terminate($urls->getReturnUrl()); // Prevent sql injection, exit
	}
	$sFilter .= "`id`=" . ew_AdjustSql($sKeyFld) . " AND ";
	if (substr($sFilter, -5) == " AND ") $sFilter = substr($sFilter, 0, strlen($sFilter)-5) . ") OR ";
}
if (substr($sFilter, -4) == " OR ") $sFilter = substr($sFilter, 0, strlen($sFilter)-4);

// Set up filter (Sql Where Clause) and get Return Sql
// Sql constructor in urls class, urlsinfo.php

$urls->CurrentFilter = $sFilter;

// Get action
if (@$_POST["a_delete"] <> "") {
	$urls->CurrentAction = $_POST["a_delete"];
} else {
	$urls->CurrentAction = "I"; // Display record
}
switch ($urls->CurrentAction) {
	case "D": // Delete
		$urls->SendEmail = TRUE; // Send email on delete success
		if (DeleteRows()) { // delete rows
			$_SESSION[EW_SESSION_MESSAGE] = "Delete Successful"; // Set up success message
			Page_Terminate($urls->getReturnUrl()); // Return to caller
		}
}

// Load records for display
$rs = LoadRecordset();
$nTotalRecs = $rs->RecordCount(); // Get record count
if ($nTotalRecs <= 0) { // No record found, exit
	$rs->Close();
	Page_Terminate($urls->getReturnUrl()); // Return to caller
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
<p><span class="phpmaker">Delete from TABLE: urls<br><br><a href="<?php echo $urls->getReturnUrl() ?>">Go Back</a></span></p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<form action="urlsdelete.php" method="post">
<p>
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($arRecKeys as $sKey) { ?>
<input type="hidden" name="key_m[]" id="key_m[]" value="<?php echo ew_HtmlEncode($sKey) ?>">
<?php } ?>
<table class="ewTable">
	<tr class="ewTableHeader">
		<td valign="top">id</td>
		<td valign="top">url</td>
		<td valign="top">digest</td>
		<td valign="top">type</td>
	</tr>
<?php
$nRecCount = 0;
$i = 0;
while (!$rs->EOF) {
	$nRecCount++;

	// Set row class and style
	$urls->CssClass = "ewTableRow";
	$urls->CssStyle = "";

	// Display alternate color for rows
	if ($nRecCount % 2 <> 1) {
		$urls->CssClass = "ewTableAltRow";
	}

	// Get the field contents
	LoadRowValues($rs);

	// Render row value
	$urls->RowType = EW_ROWTYPE_VIEW; // view
	RenderRow();
?>
	<tr<?php echo $urls->DisplayAttributes() ?>>
		<td<?php echo $urls->id->CellAttributes() ?>>
<div<?php echo $urls->id->ViewAttributes() ?>><?php echo $urls->id->ViewValue ?></div>
</td>
		<td<?php echo $urls->url->CellAttributes() ?>>
<div<?php echo $urls->url->ViewAttributes() ?>><?php echo $urls->url->ViewValue ?></div>
</td>
		<td<?php echo $urls->digest->CellAttributes() ?>>
<div<?php echo $urls->digest->ViewAttributes() ?>><?php echo $urls->digest->ViewValue ?></div>
</td>
		<td<?php echo $urls->type->CellAttributes() ?>>
<div<?php echo $urls->type->ViewAttributes() ?>><?php echo $urls->type->ViewValue ?></div>
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
	global $conn, $Security, $urls;
	$DeleteRows = TRUE;
	$sWrkFilter = $urls->CurrentFilter;

	// Set up filter (Sql Where Clause) and get Return Sql
	// Sql constructor in urls class, urlsinfo.php

	$urls->CurrentFilter = $sWrkFilter;
	$sSql = $urls->SQL();
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
			$DeleteRows = $urls->Row_Deleting($row);
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
			$DeleteRows = $conn->Execute($urls->DeleteSQL($row)); // Delete
			$conn->raiseErrorFn = '';
			if ($DeleteRows === FALSE)
				break;
			if ($sKey <> "") $sKey .= ", ";
			$sKey .= $sThisKey;
		}
	} else {

		// Set up error message
		if ($urls->CancelMessage <> "") {
			$_SESSION[EW_SESSION_MESSAGE] = $urls->CancelMessage;
			$urls->CancelMessage = "";
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
			$urls->Row_Deleted($row);
		}	
	}
	return $DeleteRows;
}
?>
<?php

// Load recordset
function LoadRecordset($offset = -1, $rowcnt = -1) {
	global $conn, $urls;

	// Call Recordset Selecting event
	$urls->Recordset_Selecting($urls->CurrentFilter);

	// Load list page sql
	$sSql = $urls->SelectSQL();
	if ($offset > -1 && $rowcnt > -1) $sSql .= " LIMIT $offset, $rowcnt";

	// Load recordset
	$conn->raiseErrorFn = 'ew_ErrorFn';	
	$rs = $conn->Execute($sSql);
	$conn->raiseErrorFn = '';

	// Call Recordset Selected event
	$urls->Recordset_Selected($rs);
	return $rs;
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

// Page Load event
function Page_Load() {

	//echo "Page Load";
}

// Page Unload event
function Page_Unload() {

	//echo "Page Unload";
}
?>
