<?php
define("EW_PAGE_ID", "add", TRUE); // Page ID
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

// Load key values from QueryString
$bCopy = TRUE;
if (@$_GET["id"] != "") {
  $posts->id->setQueryStringValue($_GET["id"]);
} else {
  $bCopy = FALSE;
}

// Create form object
$objForm = new cFormObj();

// Process form if post back
if (@$_POST["a_add"] <> "") {
  $posts->CurrentAction = $_POST["a_add"]; // Get form action
  LoadFormValues(); // Load form values
} else { // Not post back
  if ($bCopy) {
    $posts->CurrentAction = "C"; // Copy Record
  } else {
    $posts->CurrentAction = "I"; // Display Blank Record
    LoadDefaultValues(); // Load default values
  }
}

// Perform action based on action code
switch ($posts->CurrentAction) {
  case "I": // Blank record, no action required
		break;
  case "C": // Copy an existing record
   if (!LoadRow()) { // Load record based on key
      $_SESSION[EW_SESSION_MESSAGE] = "No records found"; // No record found
      Page_Terminate($posts->getReturnUrl()); // Clean up and return
    }
		break;
  case "A": // ' Add new record
		$posts->SendEmail = TRUE; // Send email on add success
    if (AddRow()) { // Add successful
      $_SESSION[EW_SESSION_MESSAGE] = "Add New Record Successful"; // Set up success message
      Page_Terminate($posts->KeyUrl($posts->getReturnUrl())); // Clean up and return
    } else {
      RestoreFormValues(); // Add failed, restore form values
    }
}

// Render row based on row type
$posts->RowType = EW_ROWTYPE_ADD;  // Render add type
RenderRow();
?>
<?php include "header.php" ?>
<script type="text/javascript">
<!--
var EW_PAGE_ID = "add"; // Page id

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
		elm = fobj.elements["x" + infix + "_datetime"];
		if (elm && !ew_CheckDate(elm.value)) {
			if (!ew_OnError(elm, "Incorrect date, format = yyyy/mm/dd - datetime"))
				return false; 
		}
		elm = fobj.elements["x" + infix + "_counter"];
		if (elm && !ew_CheckInteger(elm.value)) {
			if (!ew_OnError(elm, "Incorrect integer - counter"))
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
<script type="text/javascript">
<!--
var ew_MultiPagePage = "Page"; // multi-page Page Text
var ew_MultiPageOf = "of"; // multi-page Of Text
var ew_MultiPagePrev = "Prev"; // multi-page Prev Text
var ew_MultiPageNext = "Next"; // multi-page Next Text

//-->
</script>
<script language="JavaScript" type="text/javascript">
<!--

// Write your client script here, no need to add script tags.
// To include another .js script, use:
// ew_ClientScriptInclude("my_javascript.js"); 
//-->

</script>
<p><span class="phpmaker">Add to TABLE: posts<br><br><a href="<?php echo $posts->getReturnUrl() ?>">Go Back</a></span></p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") { // Mesasge in Session, display
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
  $_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
}
?>
<form name="fpostsadd" id="fpostsadd" action="postsadd.php" method="post" onSubmit="return ew_ValidateForm(this);">
<p>
<input type="hidden" name="a_add" id="a_add" value="A">
<table class="ewTable">
  <tr class="ewTableRow">
    <td class="ewTableHeader">url</td>
    <td<?php echo $posts->url->CellAttributes() ?>><span id="cb_x_url">
<input type="text" name="x_url" id="x_url"  size="30" maxlength="255" value="<?php echo $posts->url->EditValue ?>"<?php echo $posts->url->EditAttributes() ?>>
</span></td>
  </tr>
  <tr class="ewTableAltRow">
    <td class="ewTableHeader">datetime</td>
    <td<?php echo $posts->datetime->CellAttributes() ?>><span id="cb_x_datetime">
<input type="text" name="x_datetime" id="x_datetime"  value="<?php echo $posts->datetime->EditValue ?>"<?php echo $posts->datetime->EditAttributes() ?>>
</span></td>
  </tr>
  <tr class="ewTableRow">
    <td class="ewTableHeader">title</td>
    <td<?php echo $posts->title->CellAttributes() ?>><span id="cb_x_title">
<input type="text" name="x_title" id="x_title"  size="30" maxlength="255" value="<?php echo $posts->title->EditValue ?>"<?php echo $posts->title->EditAttributes() ?>>
</span></td>
  </tr>
  <tr class="ewTableAltRow">
    <td class="ewTableHeader">projectname</td>
    <td<?php echo $posts->projectname->CellAttributes() ?>><span id="cb_x_projectname">
<input type="text" name="x_projectname" id="x_projectname"  size="30" maxlength="255" value="<?php echo $posts->projectname->EditValue ?>"<?php echo $posts->projectname->EditAttributes() ?>>
</span></td>
  </tr>
  <tr class="ewTableRow">
    <td class="ewTableHeader">digest</td>
    <td<?php echo $posts->digest->CellAttributes() ?>><span id="cb_x_digest">
<input type="text" name="x_digest" id="x_digest"  size="30" maxlength="32" value="<?php echo $posts->digest->EditValue ?>"<?php echo $posts->digest->EditAttributes() ?>>
</span></td>
  </tr>
  <tr class="ewTableAltRow">
    <td class="ewTableHeader">counter</td>
    <td<?php echo $posts->counter->CellAttributes() ?>><span id="cb_x_counter">
<input type="text" name="x_counter" id="x_counter"  size="30" value="<?php echo $posts->counter->EditValue ?>"<?php echo $posts->counter->EditAttributes() ?>>
</span></td>
  </tr>
  <tr class="ewTableRow">
    <td class="ewTableHeader">content</td>
    <td<?php echo $posts->content->CellAttributes() ?>><span id="cb_x_content">
<textarea name="x_content" id="x_content" cols="35" rows="4"<?php echo $posts->content->EditAttributes() ?>><?php echo $posts->content->EditValue ?></textarea>
</span></td>
  </tr>
</table>
<p>
<input type="submit" name="btnAction" id="btnAction" value="    Add    ">
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

// Load default values
function LoadDefaultValues() {
	global $posts;
}
?>
<?php

// Load form values
function LoadFormValues() {

	// Load from form
	global $objForm, $posts;
	$posts->url->setFormValue($objForm->GetValue("x_url"));
	$posts->datetime->setFormValue($objForm->GetValue("x_datetime"));
	$posts->datetime->CurrentValue = ew_UnFormatDateTime($posts->datetime->CurrentValue, 5);
	$posts->title->setFormValue($objForm->GetValue("x_title"));
	$posts->projectname->setFormValue($objForm->GetValue("x_projectname"));
	$posts->digest->setFormValue($objForm->GetValue("x_digest"));
	$posts->counter->setFormValue($objForm->GetValue("x_counter"));
	$posts->content->setFormValue($objForm->GetValue("x_content"));
}

// Restore form values
function RestoreFormValues() {
	global $posts;
	$posts->url->CurrentValue = $posts->url->FormValue;
	$posts->datetime->CurrentValue = $posts->datetime->FormValue;
	$posts->datetime->CurrentValue = ew_UnFormatDateTime($posts->datetime->CurrentValue, 5);
	$posts->title->CurrentValue = $posts->title->FormValue;
	$posts->projectname->CurrentValue = $posts->projectname->FormValue;
	$posts->digest->CurrentValue = $posts->digest->FormValue;
	$posts->counter->CurrentValue = $posts->counter->FormValue;
	$posts->content->CurrentValue = $posts->content->FormValue;
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
	} elseif ($posts->RowType == EW_ROWTYPE_ADD) { // Add row

		// url
		$posts->url->EditCustomAttributes = "";
		$posts->url->EditValue = ew_HtmlEncode($posts->url->CurrentValue);

		// datetime
		$posts->datetime->EditCustomAttributes = "";
		$posts->datetime->EditValue = ew_HtmlEncode(ew_FormatDateTime($posts->datetime->CurrentValue, 5));

		// title
		$posts->title->EditCustomAttributes = "";
		$posts->title->EditValue = ew_HtmlEncode($posts->title->CurrentValue);

		// projectname
		$posts->projectname->EditCustomAttributes = "";
		$posts->projectname->EditValue = ew_HtmlEncode($posts->projectname->CurrentValue);

		// digest
		$posts->digest->EditCustomAttributes = "";
		$posts->digest->EditValue = ew_HtmlEncode($posts->digest->CurrentValue);

		// counter
		$posts->counter->EditCustomAttributes = "";
		$posts->counter->EditValue = ew_HtmlEncode($posts->counter->CurrentValue);

		// content
		$posts->content->EditCustomAttributes = "";
		$posts->content->EditValue = ew_HtmlEncode($posts->content->CurrentValue);
	} elseif ($posts->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($posts->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$posts->Row_Rendered();
}
?>
<?php

// Add record
function AddRow() {
	global $conn, $Security, $posts;

	// Check for duplicate key
	$bCheckKey = TRUE;
	$sFilter = $posts->SqlKeyFilter();
	if (trim(strval($posts->id->CurrentValue)) == "") {
		$bCheckKey = FALSE;
	} else {
		$sFilter = str_replace("@id@", ew_AdjustSql($posts->id->CurrentValue), $sFilter); // Replace key value
	}
	if (!is_numeric($posts->id->CurrentValue)) {
		$bCheckKey = FALSE;
	}
	if ($bCheckKey) {
		$rsChk = $posts->LoadRs($sFilter);
		if ($rsChk && !$rsChk->EOF) {
			$_SESSION[EW_SESSION_MESSAGE] = "Duplicate value for primary key";
			$rsChk->Close();
			return FALSE;
		}
	}
	$rsnew = array();

	// Field url
	$posts->url->SetDbValueDef($posts->url->CurrentValue, NULL);
	$rsnew['url'] =& $posts->url->DbValue;

	// Field datetime
	$posts->datetime->SetDbValueDef(ew_UnFormatDateTime($posts->datetime->CurrentValue, 5), NULL);
	$rsnew['datetime'] =& $posts->datetime->DbValue;

	// Field title
	$posts->title->SetDbValueDef($posts->title->CurrentValue, NULL);
	$rsnew['title'] =& $posts->title->DbValue;

	// Field projectname
	$posts->projectname->SetDbValueDef($posts->projectname->CurrentValue, NULL);
	$rsnew['projectname'] =& $posts->projectname->DbValue;

	// Field digest
	$posts->digest->SetDbValueDef($posts->digest->CurrentValue, NULL);
	$rsnew['digest'] =& $posts->digest->DbValue;

	// Field counter
	$posts->counter->SetDbValueDef($posts->counter->CurrentValue, NULL);
	$rsnew['counter'] =& $posts->counter->DbValue;

	// Field content
	$posts->content->SetDbValueDef($posts->content->CurrentValue, NULL);
	$rsnew['content'] =& $posts->content->DbValue;

	// Call Row Inserting event
	$bInsertRow = $posts->Row_Inserting($rsnew);
	if ($bInsertRow) {
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$AddRow = $conn->Execute($posts->InsertSQL($rsnew));
		$conn->raiseErrorFn = '';
	} else {
		if ($posts->CancelMessage <> "") {
			$_SESSION[EW_SESSION_MESSAGE] = $posts->CancelMessage;
			$posts->CancelMessage = "";
		} else {
			$_SESSION[EW_SESSION_MESSAGE] = "Insert cancelled";
		}
		$AddRow = FALSE;
	}
	if ($AddRow) {
		$posts->id->setDbValue($conn->Insert_ID());
		$rsnew['id'] =& $posts->id->DbValue;

		// Call Row Inserted event
		$posts->Row_Inserted($rsnew);
	}
	return $AddRow;
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
