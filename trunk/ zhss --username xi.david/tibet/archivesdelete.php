<?php
define("EW_PAGE_ID", "delete", TRUE); // Page ID
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

// Load Key Parameters
$sKey = "";
$bSingleDelete = TRUE; // Initialize as single delete
$arRecKeys = array();
$nKeySelected = 0; // Initialize selected key count
$sFilter = "";
if (@$_GET["id"] <> "") {
	$archives->id->setQueryStringValue($_GET["id"]);
	if (!is_numeric($archives->id->QueryStringValue)) {
		Page_Terminate($archives->getReturnUrl()); // Prevent sql injection, exit
	}
	$sKey .= $archives->id->QueryStringValue;
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
if ($nKeySelected <= 0) Page_Terminate($archives->getReturnUrl()); // No key specified, exit

// Build filter
foreach ($arRecKeys as $sKey) {
	$sFilter .= "(";

	// Set up key field
	$sKeyFld = $sKey;
	if (!is_numeric($sKeyFld)) {
		Page_Terminate($archives->getReturnUrl()); // Prevent sql injection, exit
	}
	$sFilter .= "`id`=" . ew_AdjustSql($sKeyFld) . " AND ";
	if (substr($sFilter, -5) == " AND ") $sFilter = substr($sFilter, 0, strlen($sFilter)-5) . ") OR ";
}
if (substr($sFilter, -4) == " OR ") $sFilter = substr($sFilter, 0, strlen($sFilter)-4);

// Set up filter (Sql Where Clause) and get Return Sql
// Sql constructor in archives class, archivesinfo.php

$archives->CurrentFilter = $sFilter;

// Get action
if (@$_POST["a_delete"] <> "") {
	$archives->CurrentAction = $_POST["a_delete"];
} else {
	$archives->CurrentAction = "I"; // Display record
}
switch ($archives->CurrentAction) {
	case "D": // Delete
		$archives->SendEmail = TRUE; // Send email on delete success
		if (DeleteRows()) { // delete rows
			$_SESSION[EW_SESSION_MESSAGE] = "Delete Successful"; // Set up success message
			Page_Terminate($archives->getReturnUrl()); // Return to caller
		}
}

// Load records for display
$rs = LoadRecordset();
$nTotalRecs = $rs->RecordCount(); // Get record count
if ($nTotalRecs <= 0) { // No record found, exit
	$rs->Close();
	Page_Terminate($archives->getReturnUrl()); // Return to caller
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
<p><span class="phpmaker">Delete from TABLE: archives<br><br><a href="<?php echo $archives->getReturnUrl() ?>">Go Back</a></span></p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<form action="archivesdelete.php" method="post">
<p>
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($arRecKeys as $sKey) { ?>
<input type="hidden" name="key_m[]" id="key_m[]" value="<?php echo ew_HtmlEncode($sKey) ?>">
<?php } ?>
<table class="ewTable">
	<tr class="ewTableHeader">
		<td valign="top">id</td>
		<td valign="top">url</td>
		<td valign="top">projectname</td>
		<td valign="top">title</td>
		<td valign="top">datetime</td>
	</tr>
<?php
$nRecCount = 0;
$i = 0;
while (!$rs->EOF) {
	$nRecCount++;

	// Set row class and style
	$archives->CssClass = "ewTableRow";
	$archives->CssStyle = "";

	// Display alternate color for rows
	if ($nRecCount % 2 <> 1) {
		$archives->CssClass = "ewTableAltRow";
	}

	// Get the field contents
	LoadRowValues($rs);

	// Render row value
	$archives->RowType = EW_ROWTYPE_VIEW; // view
	RenderRow();
?>
	<tr<?php echo $archives->DisplayAttributes() ?>>
		<td<?php echo $archives->id->CellAttributes() ?>>
<div<?php echo $archives->id->ViewAttributes() ?>><?php echo $archives->id->ViewValue ?></div>
</td>
		<td<?php echo $archives->url->CellAttributes() ?>>
<div<?php echo $archives->url->ViewAttributes() ?>><?php echo $archives->url->ViewValue ?></div>
</td>
		<td<?php echo $archives->projectname->CellAttributes() ?>>
<div<?php echo $archives->projectname->ViewAttributes() ?>><?php echo $archives->projectname->ViewValue ?></div>
</td>
		<td<?php echo $archives->title->CellAttributes() ?>>
<div<?php echo $archives->title->ViewAttributes() ?>><?php echo $archives->title->ViewValue ?></div>
</td>
		<td<?php echo $archives->datetime->CellAttributes() ?>>
<div<?php echo $archives->datetime->ViewAttributes() ?>><?php echo $archives->datetime->ViewValue ?></div>
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
	global $conn, $Security, $archives;
	$DeleteRows = TRUE;
	$sWrkFilter = $archives->CurrentFilter;

	// Set up filter (Sql Where Clause) and get Return Sql
	// Sql constructor in archives class, archivesinfo.php

	$archives->CurrentFilter = $sWrkFilter;
	$sSql = $archives->SQL();
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
			$DeleteRows = $archives->Row_Deleting($row);
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
			$DeleteRows = $conn->Execute($archives->DeleteSQL($row)); // Delete
			$conn->raiseErrorFn = '';
			if ($DeleteRows === FALSE)
				break;
			if ($sKey <> "") $sKey .= ", ";
			$sKey .= $sThisKey;
		}
	} else {

		// Set up error message
		if ($archives->CancelMessage <> "") {
			$_SESSION[EW_SESSION_MESSAGE] = $archives->CancelMessage;
			$archives->CancelMessage = "";
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
			$archives->Row_Deleted($row);
		}	
	}
	return $DeleteRows;
}
?>
<?php

// Load recordset
function LoadRecordset($offset = -1, $rowcnt = -1) {
	global $conn, $archives;

	// Call Recordset Selecting event
	$archives->Recordset_Selecting($archives->CurrentFilter);

	// Load list page sql
	$sSql = $archives->SelectSQL();
	if ($offset > -1 && $rowcnt > -1) $sSql .= " LIMIT $offset, $rowcnt";

	// Load recordset
	$conn->raiseErrorFn = 'ew_ErrorFn';	
	$rs = $conn->Execute($sSql);
	$conn->raiseErrorFn = '';

	// Call Recordset Selected event
	$archives->Recordset_Selected($rs);
	return $rs;
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
	} elseif ($archives->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($archives->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($archives->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$archives->Row_Rendered();
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
