<?php
define("EW_PAGE_ID", "search", TRUE); // Page ID
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
<?php include "userinfo.php" ?>
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
$Security = new cAdvancedSecurity();
?>
<?php
if (!$Security->IsLoggedIn()) $Security->AutoLogin();
if (!$Security->IsLoggedIn()) {
	$Security->SaveLastUrl();
	Page_Terminate("login.php");
}
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

// Get action
$logs->CurrentAction = @$_POST["a_search"];
switch ($logs->CurrentAction) {
	case "S": // Get Search Criteria

		// Build search string for advanced search, remove blank field
		$sSrchStr = BuildAdvancedSearch();
		if ($sSrchStr <> "") {
			Page_Terminate("logslist.php?" . $sSrchStr); // Go to list page
		}
		break;
	default: // Restore search settings
		LoadAdvancedSearch();
}

// Render row for search
$logs->RowType = EW_ROWTYPE_SEARCH;
RenderRow();
?>
<?php include "header.php" ?>
<script type="text/javascript">
<!--
var EW_PAGE_ID = "search"; // Page id
var EW_SHOW_HIGHLIGHT = "Show highlight"; 
var EW_HIDE_HIGHLIGHT = "Hide highlight";

//-->
</script>
<script type="text/javascript">
<!--

function ew_ValidateForm(fobj) {
	if (fobj.a_confirm && fobj.a_confirm.value == "F")
		return true;
	var i, elm, aelm, infix;
	var rowcnt = (fobj.key_count) ? Number(fobj.key_count.value) : 1;
	for (i=0; i<rowcnt; i++) {
		infix = (fobj.key_count) ? String(i+1) : "";
		elm = fobj.elements["x" + infix + "_id"];
		if (elm && !ew_CheckInteger(elm.value)) {
			if (!ew_OnError(elm, "Incorrect integer - id"))
				return false; 
		}
		elm = fobj.elements["x" + infix + "_time"];
		if (elm && !ew_CheckDate(elm.value)) {
			if (!ew_OnError(elm, "Incorrect date, format = yyyy/mm/dd - time"))
				return false; 
		}
		elm = fobj.elements["x" + infix + "_type"];
		if (elm && !ew_CheckInteger(elm.value)) {
			if (!ew_OnError(elm, "Incorrect integer - type"))
				return false; 
		}
	}
	return true;
}

//-->
</script>
<script type="text/javascript">
<!--

// js for DHtml Editor
//-->

</script>
<script type="text/javascript">
<!--

// js for Popup Calendar
//-->

</script>
<script language="JavaScript" type="text/javascript">
<!--

// Write your client script here, no need to add script tags.
// To include another .js script, use:
// ew_ClientScriptInclude("my_javascript.js"); 
//-->

</script>
<p><span class="phpmaker"><br><br><a href="logslist.php">返回列表</a></span></p>
<form name="flogssearch" id="flogssearch" action="logssrch.php" method="post" onSubmit="return ew_ValidateForm(this);">
<p>
<input type="hidden" name="a_search" id="a_search" value="S">
<table class="ewTable">
	<tr class="ewTableRow">
		<td class="ewTableHeader">编号</td>
		<td<?php echo $logs->id->CellAttributes() ?>><span class="ewSearchOpr">=<input type="hidden" name="z_id" id="z_id" value="="></span></td>
		<td<?php echo $logs->id->CellAttributes() ?>><span class="phpmaker">
<input type="text" name="x_id" id="x_id" title="" value="<?php echo $logs->id->EditValue ?>"<?php echo $logs->id->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">时间</td>
		<td<?php echo $logs->time->CellAttributes() ?>><span class="ewSearchOpr">=<input type="hidden" name="z_time" id="z_time" value="="></span></td>
		<td<?php echo $logs->time->CellAttributes() ?>><span class="phpmaker">
<input type="text" name="x_time" id="x_time" title="" value="<?php echo $logs->time->EditValue ?>"<?php echo $logs->time->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">用户名</td>
		<td<?php echo $logs->client->CellAttributes() ?>><span class="ewSearchOpr">包含<input type="hidden" name="z_client" id="z_client" value="LIKE"></span></td>
		<td<?php echo $logs->client->CellAttributes() ?>><span class="phpmaker">
<input type="text" name="x_client" id="x_client" title="" size="30" maxlength="30" value="<?php echo $logs->client->EditValue ?>"<?php echo $logs->client->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">用户组</td>
		<td<?php echo $logs->group->CellAttributes() ?>><span class="ewSearchOpr">包含<input type="hidden" name="z_group" id="z_group" value="LIKE"></span></td>
		<td<?php echo $logs->group->CellAttributes() ?>><span class="phpmaker">
<input type="text" name="x_group" id="x_group" title="" size="30" maxlength="20" value="<?php echo $logs->group->EditValue ?>"<?php echo $logs->group->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">类型</td>
		<td<?php echo $logs->type->CellAttributes() ?>><span class="ewSearchOpr">=<input type="hidden" name="z_type" id="z_type" value="="></span></td>
		<td<?php echo $logs->type->CellAttributes() ?>><span class="phpmaker">
<input type="text" name="x_type" id="x_type" title="" size="30" value="<?php echo $logs->type->EditValue ?>"<?php echo $logs->type->EditAttributes() ?>>
</span></td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">日志信息</td>
		<td<?php echo $logs->message->CellAttributes() ?>><span class="ewSearchOpr">包含<input type="hidden" name="z_message" id="z_message" value="LIKE"></span></td>
		<td<?php echo $logs->message->CellAttributes() ?>><span class="phpmaker">
<input type="text" name="x_message" id="x_message" title="" size="30" maxlength="100" value="<?php echo $logs->message->EditValue ?>"<?php echo $logs->message->EditAttributes() ?>>
</span></td>
	</tr>
</table>
<p>
<input type="submit" name="Action" id="Action" value="  搜索  ">
<input type="button" name="Reset" id="Reset" value="   重设   " onclick="ew_ClearForm(this.form);">
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

// Build advanced search
function BuildAdvancedSearch() {
	global $logs;
	$sSrchUrl = "";

	// Field id
	BuildSearchUrl($sSrchUrl, $logs->id, @$_POST["x_id"], @$_POST["z_id"], @$_POST["v_id"], @$_POST["y_id"], @$_POST["w_id"]);

	// Field time
	BuildSearchUrl($sSrchUrl, $logs->time, ew_UnFormatDateTime(@$_POST["x_time"],9), @$_POST["z_time"], @$_POST["v_time"], ew_UnFormatDateTime(@$_POST["y_time"],9), @$_POST["w_time"]);

	// Field client
	BuildSearchUrl($sSrchUrl, $logs->client, @$_POST["x_client"], @$_POST["z_client"], @$_POST["v_client"], @$_POST["y_client"], @$_POST["w_client"]);

	// Field group
	BuildSearchUrl($sSrchUrl, $logs->group, @$_POST["x_group"], @$_POST["z_group"], @$_POST["v_group"], @$_POST["y_group"], @$_POST["w_group"]);

	// Field type
	BuildSearchUrl($sSrchUrl, $logs->type, @$_POST["x_type"], @$_POST["z_type"], @$_POST["v_type"], @$_POST["y_type"], @$_POST["w_type"]);

	// Field message
	BuildSearchUrl($sSrchUrl, $logs->message, @$_POST["x_message"], @$_POST["z_message"], @$_POST["v_message"], @$_POST["y_message"], @$_POST["w_message"]);
	return $sSrchUrl;
}

// Function to build search URL
function BuildSearchUrl(&$Url, &$Fld, $FldVal, $FldOpr, $FldCond, $FldVal2, $FldOpr2) {
	$sWrk = "";
	$FldParm = substr($Fld->FldVar, 2);
	$FldVal = ew_StripSlashes($FldVal);
	if (is_array($FldVal)) $FldVal = implode(",", $FldVal);
	$FldVal2 = ew_StripSlashes($FldVal2);
	if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
	$FldOpr = strtoupper(trim($FldOpr));
	if ($FldOpr == "BETWEEN") {
		$IsValidValue = ($Fld->FldDataType <> EW_DATATYPE_NUMBER) ||
			($Fld->FldDataType == EW_DATATYPE_NUMBER && is_numeric($FldVal) && is_numeric($FldVal2));
		if ($FldVal <> "" && $FldVal2 <> "" && $IsValidValue) {
			$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
				"&y_" . $FldParm . "=" . urlencode($FldVal2) .
				"&z_" . $FldParm . "=" . urlencode($FldOpr);
		}
	} elseif ($FldOpr == "IS NULL" || $FldOpr == "IS NOT NULL") {
		$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
			"&z_" . $FldParm . "=" . urlencode($FldOpr);
	} else {
		$IsValidValue = ($Fld->FldDataType <> EW_DATATYPE_NUMBER) ||
			($Fld->FldDataType = EW_DATATYPE_NUMBER && is_numeric($FldVal));
		if ($FldVal <> "" && $IsValidValue && ew_IsValidOpr($FldOpr, $Fld->FldDataType)) {
			$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
				"&z_" . $FldParm . "=" . urlencode($FldOpr);
		}
		$IsValidValue = ($Fld->FldDataType <> EW_DATATYPE_NUMBER) ||
			($Fld->FldDataType = EW_DATATYPE_NUMBER && is_numeric($FldVal2));
		if ($FldVal2 <> "" && $IsValidValue && ew_IsValidOpr($FldOpr2, $Fld->FldDataType)) {
			if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
			$sWrk .= "&y_" . $FldParm . "=" . urlencode($FldVal2) .
				"&w_" . $FldParm . "=" . urlencode($FldOpr2);
		}
	}
	if ($sWrk <> "") {
		if ($Url <> "") $Url .= "&";
		$Url .= $sWrk;
	}
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $logs;

	// Call Row Rendering event
	$logs->Row_Rendering();

	// Common render codes for all row types
	if ($logs->RowType == EW_ROWTYPE_VIEW) { // View row
	} elseif ($logs->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($logs->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($logs->RowType == EW_ROWTYPE_SEARCH) { // Search row

		// id
		$logs->id->EditCustomAttributes = "";
		$logs->id->EditValue = $logs->id->AdvancedSearch->SearchValue;

		// time
		$logs->time->EditCustomAttributes = "";
		$logs->time->EditValue = ew_FormatDateTime($logs->time->AdvancedSearch->SearchValue, 9);

		// client
		$logs->client->EditCustomAttributes = "";
		$logs->client->EditValue = ew_HtmlEncode($logs->client->AdvancedSearch->SearchValue);

		// group
		$logs->group->EditCustomAttributes = "";
		$logs->group->EditValue = ew_HtmlEncode($logs->group->AdvancedSearch->SearchValue);

		// type
		$logs->type->EditCustomAttributes = "";
		$logs->type->EditValue = $logs->type->AdvancedSearch->SearchValue;

		// message
		$logs->message->EditCustomAttributes = "";
		$logs->message->EditValue = ew_HtmlEncode($logs->message->AdvancedSearch->SearchValue);
	}

	// Call Row Rendered event
	$logs->Row_Rendered();
}
?>
<?php

// Load advanced search
function LoadAdvancedSearch() {
	global $logs;
	$logs->id->AdvancedSearch->SearchValue = $logs->getAdvancedSearch("x_id");
	$logs->time->AdvancedSearch->SearchValue = $logs->getAdvancedSearch("x_time");
	$logs->client->AdvancedSearch->SearchValue = $logs->getAdvancedSearch("x_client");
	$logs->group->AdvancedSearch->SearchValue = $logs->getAdvancedSearch("x_group");
	$logs->type->AdvancedSearch->SearchValue = $logs->getAdvancedSearch("x_type");
	$logs->message->AdvancedSearch->SearchValue = $logs->getAdvancedSearch("x_message");
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
