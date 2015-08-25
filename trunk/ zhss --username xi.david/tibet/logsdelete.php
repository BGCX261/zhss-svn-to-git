<?php
define("EW_PAGE_ID", "delete", TRUE); // Page ID
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

// Load Key Parameters
$sKey = "";
$bSingleDelete = TRUE; // Initialize as single delete
$arRecKeys = array();
$nKeySelected = 0; // Initialize selected key count
$sFilter = "";
if (@$_GET["id"] <> "") {
	$logs->id->setQueryStringValue($_GET["id"]);
	if (!is_numeric($logs->id->QueryStringValue)) {
		Page_Terminate($logs->getReturnUrl()); // Prevent sql injection, exit
	}
	$sKey .= $logs->id->QueryStringValue;
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
if ($nKeySelected <= 0) Page_Terminate($logs->getReturnUrl()); // No key specified, exit

// Build filter
foreach ($arRecKeys as $sKey) {
	$sFilter .= "(";

	// Set up key field
	$sKeyFld = $sKey;
	if (!is_numeric($sKeyFld)) {
		Page_Terminate($logs->getReturnUrl()); // Prevent sql injection, exit
	}
	$sFilter .= "`id`=" . ew_AdjustSql($sKeyFld) . " AND ";
	if (substr($sFilter, -5) == " AND ") $sFilter = substr($sFilter, 0, strlen($sFilter)-5) . ") OR ";
}
if (substr($sFilter, -4) == " OR ") $sFilter = substr($sFilter, 0, strlen($sFilter)-4);

// Set up filter (Sql Where Clause) and get Return Sql
// Sql constructor in logs class, logsinfo.php

$logs->CurrentFilter = $sFilter;

// Get action
if (@$_POST["a_delete"] <> "") {
	$logs->CurrentAction = $_POST["a_delete"];
} else {
	$logs->CurrentAction = "I"; // Display record
}
switch ($logs->CurrentAction) {
	case "D": // Delete
		$logs->SendEmail = TRUE; // Send email on delete success
		if (DeleteRows()) { // delete rows
			$_SESSION[EW_SESSION_MESSAGE] = "Delete Successful"; // Set up success message
			Page_Terminate($logs->getReturnUrl()); // Return to caller
		}
}

// Load records for display
$rs = LoadRecordset();
$nTotalRecs = $rs->RecordCount(); // Get record count
if ($nTotalRecs <= 0) { // No record found, exit
	$rs->Close();
	Page_Terminate($logs->getReturnUrl()); // Return to caller
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
<p><span class="phpmaker">Delete from TABLE: logs<br><br><a href="<?php echo $logs->getReturnUrl() ?>">Go Back</a></span></p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<form action="logsdelete.php" method="post">
<p>
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($arRecKeys as $sKey) { ?>
<input type="hidden" name="key_m[]" id="key_m[]" value="<?php echo ew_HtmlEncode($sKey) ?>">
<?php } ?>
<table class="ewTable">
	<tr class="ewTableHeader">
		<td valign="top">id</td>
		<td valign="top">time</td>
		<td valign="top">client</td>
		<td valign="top">group</td>
		<td valign="top">type</td>
		<td valign="top">message</td>
	</tr>
<?php
$nRecCount = 0;
$i = 0;
while (!$rs->EOF) {
	$nRecCount++;

	// Set row class and style
	$logs->CssClass = "ewTableRow";
	$logs->CssStyle = "";

	// Display alternate color for rows
	if ($nRecCount % 2 <> 1) {
		$logs->CssClass = "ewTableAltRow";
	}

	// Get the field contents
	LoadRowValues($rs);

	// Render row value
	$logs->RowType = EW_ROWTYPE_VIEW; // view
	RenderRow();
?>
	<tr<?php echo $logs->DisplayAttributes() ?>>
		<td<?php echo $logs->id->CellAttributes() ?>>
<div<?php echo $logs->id->ViewAttributes() ?>><?php echo $logs->id->ViewValue ?></div>
</td>
		<td<?php echo $logs->time->CellAttributes() ?>>
<div<?php echo $logs->time->ViewAttributes() ?>><?php echo $logs->time->ViewValue ?></div>
</td>
		<td<?php echo $logs->client->CellAttributes() ?>>
<div<?php echo $logs->client->ViewAttributes() ?>><?php echo $logs->client->ViewValue ?></div>
</td>
		<td<?php echo $logs->group->CellAttributes() ?>>
<div<?php echo $logs->group->ViewAttributes() ?>><?php echo $logs->group->ViewValue ?></div>
</td>
		<td<?php echo $logs->type->CellAttributes() ?>>
<div<?php echo $logs->type->ViewAttributes() ?>><?php echo $logs->type->ViewValue ?></div>
</td>
		<td<?php echo $logs->message->CellAttributes() ?>>
<div<?php echo $logs->message->ViewAttributes() ?>><?php echo $logs->message->ViewValue ?></div>
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
	global $conn, $Security, $logs;
	$DeleteRows = TRUE;
	$sWrkFilter = $logs->CurrentFilter;

	// Set up filter (Sql Where Clause) and get Return Sql
	// Sql constructor in logs class, logsinfo.php

	$logs->CurrentFilter = $sWrkFilter;
	$sSql = $logs->SQL();
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
			$DeleteRows = $logs->Row_Deleting($row);
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
			$DeleteRows = $conn->Execute($logs->DeleteSQL($row)); // Delete
			$conn->raiseErrorFn = '';
			if ($DeleteRows === FALSE)
				break;
			if ($sKey <> "") $sKey .= ", ";
			$sKey .= $sThisKey;
		}
	} else {

		// Set up error message
		if ($logs->CancelMessage <> "") {
			$_SESSION[EW_SESSION_MESSAGE] = $logs->CancelMessage;
			$logs->CancelMessage = "";
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
			$logs->Row_Deleted($row);
		}	
	}
	return $DeleteRows;
}
?>
<?php

// Load recordset
function LoadRecordset($offset = -1, $rowcnt = -1) {
	global $conn, $logs;

	// Call Recordset Selecting event
	$logs->Recordset_Selecting($logs->CurrentFilter);

	// Load list page sql
	$sSql = $logs->SelectSQL();
	if ($offset > -1 && $rowcnt > -1) $sSql .= " LIMIT $offset, $rowcnt";

	// Load recordset
	$conn->raiseErrorFn = 'ew_ErrorFn';	
	$rs = $conn->Execute($sSql);
	$conn->raiseErrorFn = '';

	// Call Recordset Selected event
	$logs->Recordset_Selected($rs);
	return $rs;
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

// Page Load event
function Page_Load() {

	//echo "Page Load";
}

// Page Unload event
function Page_Unload() {

	//echo "Page Unload";
}
?>
