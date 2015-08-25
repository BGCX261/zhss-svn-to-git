<?php
define("EW_PAGE_ID", "list", TRUE); // Page ID
define("EW_TABLE_NAME", 'posts', TRUE);
?>
<?php
$view = @$_GET['view'];
if( $view == '' ) {
	$view = @$_COOKIE['view'];
}
if( $view == '' ) {
	$view = 'normal';
}
$sSrchAdvanced="";

if(isset($_GET['adv'])&&($_GET['adv']=='on')){
	if(isset($_GET['sSrchAdv'])){
		$sSrchAdvanced = rawurldecode($_GET['sSrchAdv']);
	}
}
setcookie('view',$view);
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "postsinfo.php" ?>
<?php include "categoriesinfo.php" ?>
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
//echo $Security->getCurrentUserLevel();exit;
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
$action = @$_GET['action'];

if( $action != '' ) {
	$ids = @$_GET['ids'];
	if( $action == 'delete' ) {
		$idstring = implode('","',$ids);
		$sql = 'DELETE FROM `posts` WHERE `id` IN ("' . $idstring . '")';
		$conn->Execute($sql);
	}
	else if( $action == 'archive' ) {
		$idstring = implode('","',$ids);
		$sql = 'SELECT * FROM `posts` WHERE `id` IN ("' . $idstring . '")';
		$rs = $conn->Execute($sql);
		while(!$rs->EOF) {
			$title = $rs->fields('title');
			$url = $rs->fields('url');
			$projectname = $rs->fields('projectname');
			$content = $rs->fields('content');
			if( checkArchive($url) ) {
				$hsql = 'INSERT INTO `archives`(`url`,`projectname`,`title`,`content`,`datetime`) VALUES("' . mysql_escape_string($url) . '","'.mysql_escape_string($projectname).'","' . mysql_escape_string($title) . '","' . mysql_escape_string($content) . '",NOW())';
				$conn->Execute($hsql);
			}
			$rs->MoveNext();
		}
		$rs->Close();
	}
	Page_Terminate("postslist.php");
}
function checkArchive($url) {
	global $conn;
	$sql = 'SELECT COUNT(`url`) FROM `archives` WHERE `url`="' . $url . '"';
	$cnt = 0;
	if ($rs = $conn->Execute($sql)) {
		if (!$rs->EOF) $cnt = $rs->fields[0];
		$rs->Close();
	}
	return($cnt==0);
}
// Page load event, used in current page
Page_Load();
?>
<?php
$posts->Export = @$_GET["export"]; // Get export parameter
$sExport = $posts->Export; // Get export parameter, used in header
$sExportFile = $posts->TableVar; // Get export file, used in header
?>
<?php

// Paging variables
$nStartRec = 0; // Start record index
$nStopRec = 0; // Stop record index
$nTotalRecs = 0; // Total number of records
$nDisplayRecs = 20;
$nRecRange = 10;
$nRecCount = 0; // Record count

// Search filters
//$sSrchAdvanced = ""; // Advanced search filter
$sSrchBasic = ""; // Basic search filter
$sSrchWhere = ""; // Search where clause
$sFilter = "";

// Master/Detail
$sDbMasterFilter = ""; // Master filter
$sDbDetailFilter = ""; // Detail filter
$sSqlMaster = ""; // Sql for master record

// Handle reset command
ResetCmd();
$posts->CurrentFilter = "";
// Get basic search criteria

$sSrchBasic = BasicSearchWhere($sSrchAdvanced);


if ($sSrchBasic <> "") {
	if ($sSrchWhere <> "") $sSrchWhere .= " AND ";
	$sSrchWhere .= "(" . $sSrchBasic . ")";
}
// Build search criteria
//echo $sSrchAdvanced;
// Save search criteria
if ($sSrchWhere <> "") {
	if ($sSrchBasic == "") ResetBasicSearchParms();
	$posts->setSearchWhere($sSrchWhere); // Save to Session
	$nStartRec = 1; // Reset start record counter
	$posts->setStartRecordNumber($nStartRec);
} else {
	RestoreSearchParms();
}

// Build filter
$sFilter = "";
if ($sDbDetailFilter <> "") {
	if ($sFilter <> "") $sFilter .= " AND ";
	$sFilter .= "(" . $sDbDetailFilter . ")";
}
if ($sSrchWhere <> "") {
	if ($sFilter <> "") $sFilter .= " AND ";
	$sFilter .= "(" . BasicSearchWhere() . ")";
}

// Set up filter in Session
$posts->setSessionWhere($sSrchWhere);
$posts->CurrentFilter = "";

// Set Up Sorting Order
SetUpSortOrder();

// Set Return Url
$posts->setReturnUrl("postslist.php");

?>
<?php include "header.php" ?>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<!-- left column (begin) -->
		<td valign="top" class="ewMenuColumn">
<?php if ($posts->Export == "") { ?>
<script type="text/javascript">
<!--
var EW_PAGE_ID = "list"; // Page id
var firstrowoffset = 1; // First data row start at
var lastrowoffset = 0; // Last data row end at
var EW_LIST_TABLE_NAME = 'ewlistmain'; // Table name for list page
var rowclass = 'ewTableRow'; // Row class
var rowaltclass = 'ewTableAltRow'; // Row alternate class
var rowmoverclass = 'ewTableHighlightRow'; // Row mouse over class
var rowselectedclass = 'ewTableSelectRow'; // Row selected class
var roweditclass = 'ewTableEditRow'; // Row edit class

//-->
</script>
<?php } ?>
<?php
if(isset($_GET['adv'])&&($_GET['adv']=='on')){
// Load recordset
$bExportAll = (defined("EW_EXPORT_ALL") && $posts->Export <> "");
$bSelectLimit = ($posts->Export == "" && $posts->SelectLimit);
if (!$bSelectLimit) $rs = LoadRecordset();
$nTotalRecs = ($bSelectLimit) ? $posts->SelectRecordCount() : $rs->RecordCount();
$nStartRec = 1;
if ($nDisplayRecs <= 0) $nDisplayRecs = $nTotalRecs; // Display all records
if (!$bExportAll) SetUpStartRec(); // Set up start record position
if ($bSelectLimit) $rs = LoadRecordset($nStartRec-1, $nDisplayRecs);
}else
{
// Load recordset
$bExportAll = (defined("EW_EXPORT_ALL") && $posts->Export <> "");
$bSelectLimit = ($posts->Export == "" && $posts->SelectLimit);
if (!$bSelectLimit) $rs = LoadRecordset();
$nTotalRecs = ($bSelectLimit) ? $posts->SelectRecordCount() : $rs->RecordCount();
$nStartRec = 1;
if ($nDisplayRecs <= 0) $nDisplayRecs = $nTotalRecs; // Display all records
if (!$bExportAll) SetUpStartRec(); // Set up start record position
if ($bSelectLimit) $rs = LoadRecordset($nStartRec-1, $nDisplayRecs);
}

?>

<?php if ($posts->Export == "") { ?>
<?php if ($Security->IsLoggedIn()) { ?>
<form name="fpostslistsrch" id="fpostslistsrch" action="postslist.php" >
<div class="menuhead">实时检索</div>
<?php
	$sAdvance = "";
	if($sSrchAdvanced <> "")
	{
		$sWhere = "(".$posts->getSearchWhere()." AND id IN(SELECT id FROM `posts` WHERE (".$sSrchAdvanced."))";
	}else
	{
		$sWhere = $posts->getSearchWhere();
	}
	if ($posts->getSearchWhere() <> ''){
		$sAdvance = " id IN (SELECT id FROM `posts` WHERE ".$sWhere.")";
	}
	if($sAdvance <> $sSrchAdvanced){
		
	}
	//echo $sAdvance;
	echo '<input type="hidden" name="sSrchAdv" value="'.rawurlencode($sAdvance).'" />';
?>	

<table class="ewBasicSearch">
	<tr>
		<td width="60">
			<label for="<?php echo EW_TABLE_BASIC_SEARCH ?>">关键词:</label>
		</td>
		<td>
			<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" size="10" class="searchInput" value="<?php echo $posts->getBasicSearchKeyword() ?>"></p>
		</td>
	</tr>
	<tr>
		<td>
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_START_TIME ?>">起始时间:</label>
		</td>
		<td>
			<input type="text" value="<?php echo $posts->getBasicSearchStartTime() ?>" class="dateinput searchInput" onkeypress="return false"size="10" id="<?php echo EW_TABLE_BASIC_SEARCH_START_TIME ?>" name="<?php echo EW_TABLE_BASIC_SEARCH_START_TIME ?>"/></p>
		</td>
	</tr>
	<tr>
		<td>
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_END_TIME ?>">结束时间:</label>
		</td>
		<td>
			<input type="text" value="<?php echo $posts->getBasicSearchEndTime() ?>" class="dateinput searchInput"onkeypress="return false" size="10" id="<?php echo EW_TABLE_BASIC_SEARCH_END_TIME ?>" name="<?php echo EW_TABLE_BASIC_SEARCH_END_TIME ?>"/></p>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="" <?php if ($user->getBasicSearchType() == "") { ?>checked="checked"<?php } ?>>匹配以上<b>单个</b>关键词</label>
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>AND"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>AND" value="AND" <?php if ($user->getBasicSearchType() == "AND") { ?>checked="checked"<?php } ?>>包含以上<b>全部</b>的关键词</label>
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>OR"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>OR" value="OR" <?php if ($user->getBasicSearchType() == "OR") { ?>checked="checked"<?php } ?>>包含以上<b>任意一个</b>关键词</label>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="checkbox" name="adv" >在结果中搜索&nbsp;</br>
			<input type="Submit" value="搜索 (*)">&nbsp;
			<a href="postslistsorted.php?cmd=reset">进入排序搜索</a>
<?php
	if( isset($view) && $view == 'classic' ) {
		echo '<input type="hidden" name="view" value="classic" />';
	}
	else {
		echo '<input type="hidden" name="view" value="normal" />';
	}
?>
		</td>
	</tr>
</table>
<script type="text/javascript">
Calendar.setup({
	inputField     :    "<?php echo EW_TABLE_BASIC_SEARCH_START_TIME ?>",
	ifFormat       :    "%Y-%m-%d %H:%M ", 
	showsTime      :    true,
	onUpdate       :    null
});
Calendar.setup({
	inputField     :    "<?php echo EW_TABLE_BASIC_SEARCH_END_TIME ?>",
	ifFormat       :    "%Y-%m-%d %H:%M ",
	showsTime      :    true,
	onUpdate       :    null
});
</script>
</form>
<?php
$csql = 'SELECT * FROM `posts` WHERE `projectname`<>"" GROUP BY `projectname`';
$crs = $conn->Execute($csql);
if( $crs->_numOfRows > 0 ) {
?>
<div class="menuhead">网站列表</div>
<table class="ewBasicSearch">
	<tr>
		<td>
			<ul class="t_t">
<?php
while( !$crs->EOF && $crs->fields('projectname') != '' ) {
	echo '<li>';
	echo '<a href="postslist.php?projectname=' . rawurlencode($crs->fields('projectname')) . '">' . $crs->fields('projectname') . '</a>';
	echo '</li>';
	$crs->MoveNext();
}
?>
			</ul>
		</td>
	</tr>
</table>
<?php
}
?>
<?php } ?>
<?php } ?>
		</td>
		<!-- left column (end) -->
		<!-- right column (begin) -->
		<td valign="top" class="ewContentColumn">
<?php
	$getArray = array();
	foreach( $_GET as $key => $value ) {
		if( $key != 'view' ) {
			array_push($getArray,$key . '=' . rawurlencode($value));
		}
	}
	$get = implode('&',$getArray);
	$url = 'postslist.php';
	if( $get != '' ) {
		$classicUrl = $url . '?view=classic&' . $get;
		$normalUrl = $url . '?view=normal&' . $get;
	}
	else {
		$classicUrl = $url . '?view=classic';
		$normalUrl = $url . '?view=normal';
	}
?>
<p class="ptitle"><b>中和集成监控系统</b><span><a href="<?php echo $classicUrl ?>">传统视图</a><a href="<?php echo $normalUrl ?>">默认视图</a></span></p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<?php  if ($nTotalRecs > 0) { ?>
<form method="post" name="fpostslist" id="fpostslist" action="postslist.php">
<script type="text/javascript">
	function deleteSelected() {
		if( confirm('确实要删除选中项目吗？') ) {
			document.getElementById('action').value = 'delete';
			document.getElementById('fpostslist').submit();
		}
		document.getElementById('action').value = '';
		return false;
	}
	function archiveSelected() {
		if( confirm('确实要归档选中项目吗？') ) {
			document.getElementById('action').value = 'archive';
			document.getElementById('fpostslist').submit();
		}
		document.getElementById('action').value = '';
		return false;
	}
	function checkAll(){
		var cbx = document.forms["fpostslist"].elements["ids[]"];
		var chk = document.forms["fpostslist"].elements["chkAll"];
		for(var i = 0;i < cbx.length;i ++){
				cbx[i].checked = chk.checked;
		}
	}	
</script>
	<?php
	if( isset($view) && $view == 'classic' ) {
		$keywords = trim($posts->getBasicSearchKeyword());
		if( $posts->getBasicSearchType() == 'OR' || $posts->getBasicSearchType() == 'AND' ) {
			$keywords = explode(' ',$keywords);
		}
	?>
		<table id="ewlistmain" class="ewTable">
		<tr>
<?php if ($Security->CanDelete()||$Security->CanArchive()) { ?>
<td><input type="hidden" name="action" id="action" value=""/></td>
<?php } ?>
</tr>
		<?php
		if (defined("EW_EXPORT_ALL") && $posts->Export <> "") {
			$nStopRec = $nTotalRecs;
		} else {
			$nStopRec = $nStartRec + $nDisplayRecs - 1; // Set the last record to display
		}
		$nRecCount = $nStartRec - 1;
		if (!$rs->EOF) {
			$rs->MoveFirst();
			if (!$posts->SelectLimit) $rs->Move($nStartRec - 1); // Move to first record directly
		}
		$RowCnt = 0;
		while (!$rs->EOF && $nRecCount < $nStopRec) {
			$nRecCount++;
			if (intval($nRecCount) >= intval($nStartRec)) {
				$RowCnt++;
			LoadRowValues($rs); // Load row values
			$posts->RowType = EW_ROWTYPE_VIEW; // Render view
			RenderRow();
			$url_info = parse_url($posts->url->ViewValue);
		?>
			<!-- Table body -->
			<tr>
				<td>
					<div><input type="checkbox" name="ids[]" value="<?php echo $posts->id->ViewValue ?>"/><a href="<?php echo $posts->url->ViewValue ?>" target="_blank"><?php echo $posts->title->ViewValue ?></a><?php echo countword( $posts->content->ViewValue,$keywords) ?></div>
				</td>
			</tr>
			<tr>
				<td>
					<div style="color:#000">  <?php echo highlight(substr($posts->content->ViewValue,0,800),$keywords) ?>   </div>
				</td>
			</tr>
			<tr>
				<td>
					<span style="color:green"><?php echo (isset($url_info['host'])?$url_info['host']:'') . (isset($url_info['path'])?$url_info['path']:'') . '&nbsp;&nbsp;' . $posts->datetime->ViewValue ?></span>
					 - <a href="<?php echo $viewpath . '/' . (isset($url_info['host'])?$url_info['host']:'') . '/' . md5(strtolower($posts->url->ViewValue)) ?>.html" target="_blank">快照</a>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<br>
				</td>
			</tr>
		<?php
			}
			$rs->MoveNext();
		}
		?>
		</table>
	<?php
	}
	else {
	?>
	<?php
		$keywords = trim($posts->getBasicSearchKeyword());
		if( $posts->getBasicSearchType() == 'OR' || $posts->getBasicSearchType() == 'AND' ) {
			$keywords = explode(' ',$keywords);
		}
	?>	
		<table id="ewlistmain" class="ewTable">
			<?php
			$OptionCnt = 0;
			if ($Security->IsLoggedIn()) {
				$OptionCnt++; // view
			}
			if ($Security->IsLoggedIn()) {
				$OptionCnt++; // edit
			}
			if ($Security->IsLoggedIn()) {
				$OptionCnt++; // copy
			}
			if ($Security->IsLoggedIn()) {
				$OptionCnt++; // delete
			}
			?>
			<!-- Table header -->
			<tr class="ewTableHeader">
<?php if ($Security->CanDelete()||$Security->CanArchive()) { ?>
<td width="20"><input type="hidden" name="action" id="action" value=""/></td>
<?php } ?>
				<td valign="top">
		<?php if ($posts->Export <> "") { ?>
		id
		<?php } else { ?>
			<a href="postslist.php?order=<?php echo urlencode('id') ?>&ordertype=<?php echo $posts->id->ReverseSort() ?>">编号<?php if ($posts->id->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($posts->id->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
		<?php } ?>
				</td>
				<td valign="top">
		<?php if ($posts->Export <> "") { ?>
		来源
		<?php } else { ?>
			<a href="postslist.php?order=<?php echo urlencode('url') ?>&ordertype=<?php echo $posts->url->ReverseSort() ?>">站点名称&nbsp;(*)<?php if ($posts->url->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($posts->url->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
		<?php } ?>
				</td>
				<td valign="top">
		<?php if ($posts->Export <> "") { ?>
		时间
		<?php } else { ?>
			<a href="postslist.php?order=<?php echo urlencode('datetime') ?>&ordertype=<?php echo $posts->datetime->ReverseSort() ?>">时间<?php if ($posts->datetime->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($posts->datetime->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
		<?php } ?>
				</td>
				<td valign="top">
		<?php if ($posts->Export <> "" ) { ?>
		标题
		<?php } else { ?>
			<a href="postslist.php?order=<?php echo urlencode('title') ?>&ordertype=<?php echo $posts->title->ReverseSort() ?>">网页标题&nbsp;(*)<?php if ($posts->title->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($posts->title->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
		<?php } ?>
				</td>
		<?php if ($posts->Export == "") { ?>
		<?php
			if ($Security->CanView()) {
		?>
		<td nowrap>&nbsp;</td>
		<?php } ?>
		<?php if ($Security->CanEdit()) { ?>
		<td nowrap>&nbsp;</td>
		<?php } ?>
		<?php } ?>
			</tr>
		<?php
		if (defined("EW_EXPORT_ALL") && $posts->Export <> "") {
			$nStopRec = $nTotalRecs;
		} else {
			$nStopRec = $nStartRec + $nDisplayRecs - 1; // Set the last record to display
		}
		$nRecCount = $nStartRec - 1;
		if (!$rs->EOF) {
			$rs->MoveFirst();
			if (!$posts->SelectLimit) $rs->Move($nStartRec - 1); // Move to first record directly
		}
		$RowCnt = 0;
		if( $posts->getBasicSearchKeyword() == '' ) {
			$keyaction = '';
		}
		else {
			$keyaction = EW_TABLE_BASIC_SEARCH . '=' . rawurlencode($posts->getBasicSearchKeyword()) . '&' . EW_TABLE_BASIC_SEARCH_TYPE . '=' . $posts->getBasicSearchType();
		}
		while (!$rs->EOF && $nRecCount < $nStopRec) {
			$nRecCount++;
			if (intval($nRecCount) >= intval($nStartRec)) {
				$RowCnt++;

			// Init row class and style
			$posts->CssClass = "ewTableRow";
			$posts->CssStyle = "";

			// Init row event
			$posts->RowClientEvents = "onmouseover='ew_MouseOver(this);' onmouseout='ew_MouseOut(this);' onclick='ew_Click(this);'";

			// Display alternate color for rows
			if ($RowCnt % 2 == 0) {
				$posts->CssClass = "ewTableAltRow";
			}
			LoadRowValues($rs); // Load row values
			$posts->RowType = EW_ROWTYPE_VIEW; // Render view
			RenderRow();
		?>
			<!-- Table body -->
			<tr<?php echo $posts->DisplayAttributes() ?>>
<?php if ($Security->CanDelete()||$Security->CanArchive()) { ?>
<td nowrap><input type="checkbox" name="ids[]" value="<?php echo $posts->id->ViewValue ?>"/></td>
<?php } ?>
				<!-- id -->
				<td<?php echo $posts->id->CellAttributes() ?>>
					<div<?php echo $posts->id->ViewAttributes() ?>><?php echo $posts->id->ViewValue ?></div>
				</td>
				<!-- url -->
				<td<?php echo $posts->projectname->CellAttributes() ?>>
					<div<?php echo $posts->projectname->ViewAttributes() ?> class="url"><a href="<?php echo $posts->url->ViewValue ?>" target="_blank"><?php echo $posts->projectname->ViewValue ?></a></div>
				</td>
				<!-- datetime -->
				<td<?php echo $posts->datetime->CellAttributes() ?>>
					<div<?php echo $posts->datetime->ViewAttributes() ?>><?php echo $posts->datetime->ViewValue ?></div>
				</td>
				<!-- title -->
				<td<?php echo $posts->title->CellAttributes() ?>>
					<div<?php echo $posts->title->ViewAttributes() ?>><?php echo $posts->title->ViewValue ?><?php echo countword( $posts->content->ViewValue,$keywords) ?></div>
				</td>
				<?php if ($Security->CanView()) { ?>
				<td nowrap><span class="phpmaker">
				<a href="<?php echo $posts->ViewUrl($keyaction) ?>">快照</a>
				</span></td>
				<?php } ?>
				<?php if ($Security->CanEdit()) { ?>
				<td nowrap><span class="phpmaker">
				<a href="<?php echo $posts->EditUrl() ?>">编辑</a>
				</span></td>
				<?php } ?>
			</tr>
		<?php
			}
			$rs->MoveNext();
		}
		?>
		</table>
	<?php
	}
	?>
<?php if ($posts->Export == "") { ?>
<table>
	<tr><td><span class="phpmaker">
<?php
	echo '<input type="checkbox" onclick="checkAll()" name="chkAll" />全选';
?>	

<?php if ($Security->CanDelete()) { ?>
<?php
	echo '<input type="button" value="删除选中" onclick="deleteSelected()" />';
?>		
<?php } ?>
<?php if ($Security->CanArchive()) { ?>
<?php
	echo '<input type="button" value="归档选中" onclick="archiveSelected()" />';
?>		
<?php } ?>
	</span></td></tr>
</table>
<?php } ?>
</form>
<?php
}
?>
<?php

// Close recordset and connection
if ($rs) $rs->Close();
?>
<?php if ($posts->Export == "") { ?>
<form action="postslist.php" name="ewpagerform" id="ewpagerform">
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td nowrap>
<?php if (!isset($Pager)) $Pager = new cPrevNextPager($nStartRec, $nDisplayRecs, $nTotalRecs) ?>
<?php if ($Pager->RecordCount > 0) { ?>
	<table border="0" cellspacing="0" cellpadding="0"><tr><td><span class="phpmaker">第</span></td>
<!--first page button-->
	<?php if ($Pager->FirstButton->Enabled) { ?>
	<td><a href="postslist.php?start=<?php echo $Pager->FirstButton->Start ?>"><img src="images/first.gif" alt="First" width="16" height="16" border="0"></a></td>
	<?php } else { ?>
	<td><img src="images/firstdisab.gif" alt="First" width="16" height="16" border="0"></td>
	<?php } ?>
<!--previous page button-->
	<?php if ($Pager->PrevButton->Enabled) { ?>
	<td><a href="postslist.php?start=<?php echo $Pager->PrevButton->Start ?>"><img src="images/prev.gif" alt="Previous" width="16" height="16" border="0"></a></td>
	<?php } else { ?>
	<td><img src="images/prevdisab.gif" alt="Previous" width="16" height="16" border="0"></td>
	<?php } ?>
<!--current page number-->
	<td><input type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" id="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $Pager->CurrentPage ?>" size="4"></td>
<!--next page button-->
	<?php if ($Pager->NextButton->Enabled) { ?>
	<td><a href="postslist.php?start=<?php echo $Pager->NextButton->Start ?>"><img src="images/next.gif" alt="Next" width="16" height="16" border="0"></a></td>	
	<?php } else { ?>
	<td><img src="images/nextdisab.gif" alt="Next" width="16" height="16" border="0"></td>
	<?php } ?>
<!--last page button-->
	<?php if ($Pager->LastButton->Enabled) { ?>
	<td><a href="postslist.php?start=<?php echo $Pager->LastButton->Start ?>"><img src="images/last.gif" alt="Last" width="16" height="16" border="0"></a></td>	
	<?php } else { ?>
	<td><img src="images/lastdisab.gif" alt="Last" width="16" height="16" border="0"></td>
	<?php } ?>
	<td><span class="phpmaker">页 在 <?php echo $Pager->PageCount ?> 中</span></td>
	</tr></table>
	<span class="phpmaker">第 <?php echo $Pager->FromIndex ?> 条 到 第 <?php echo $Pager->ToIndex ?> 条记录,总共 <?php echo $Pager->RecordCount ?> 条记录</span>
<?php } else { ?>
	<?php if ($sSrchWhere == "0=101") { ?>
	<span class="phpmaker">请输入搜索标准</span>
	<?php } else { ?>
	<span class="phpmaker">没有发现记录</span>
	<?php } ?>
<?php } ?>
		</td>
	</tr>
</table>
</form>
<?php } ?>

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

// Return Basic Search sql
function BasicSearchSQL($Keyword) {
	$sKeyword = ew_AdjustSql($Keyword);
	$sql = "";
//	$sql .= "`url` LIKE '%" . $sKeyword . "%' OR ";
//	$sql .= "`title` LIKE '%" . $sKeyword . "%' OR ";
//	$sql .= "`digest` LIKE '%" . $sKeyword . "%' OR ";
	$sql .= "`content` LIKE '%" . $sKeyword . "%'";
	if (substr($sql, -4) == " OR ") $sql = substr($sql, 0, strlen($sql)-4);
	return $sql;
}
function parseTime($starttime,$endtime) {
	$timestamp = 0;
	$startSql = $endSql = '';
	if( $starttime != '' ) {
		$startSql = 'UNIX_TIMESTAMP(`datetime`)>=UNIX_TIMESTAMP("' . trim($starttime) . '")';
	}
	if( $endtime != '' ) {
		$endSql = 'UNIX_TIMESTAMP(`datetime`)<=UNIX_TIMESTAMP("' . trim($endtime) . '")';
	}
	$sql = $startSql;
	if( $endSql != null ) {
		if( $sql != '' ) $sql .= ' AND ';
		$sql = '(' . $sql . $endSql . ')';
	}
	return $sql;
}
function parseCategory($searchscope) {
	$sql = '';
	if( count($searchscope) > 0 ) {
		$scope = implode('","',$searchscope);
		$sql = '(`projectname` in ("' . $scope . '"))';
	}
	return $sql;
}
function parseProjectName($projectname) {
	$sql = '';
	if( isset($projectname) && !empty($projectname) ) {
		$sql = '(`projectname`="' . $projectname . '")';
	}
	return $sql;
}
// Return Basic Search Where based on search keyword and type
function BasicSearchWhere($sSrchAdv="") {
	global $Security, $posts;
	$sSearchStr = "";
	$sSearchKeyword = ew_StripSlashes(@$_GET[EW_TABLE_BASIC_SEARCH]);
	$sSearchType = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	$sSearchStartTime = @$_GET[EW_TABLE_BASIC_SEARCH_START_TIME];
	$sSearchEndTime = @$_GET[EW_TABLE_BASIC_SEARCH_END_TIME];
	$sSearchProjectName = @$_GET[EW_TABLE_BASIC_SEARCH_PROJECTNAME];
	if ($sSearchKeyword <> "") {
		$sSearch = trim($sSearchKeyword);
		if ($sSearchType <> "") {
			while (strpos($sSearch, "  ") !== FALSE)
				$sSearch = str_replace("  ", " ", $sSearch);
			$arKeyword = explode(" ", trim($sSearch));
			foreach ($arKeyword as $sKeyword) {
				if ($sSearchStr <> "") $sSearchStr .= " " . $sSearchType . " ";
				$sSearchStr .= "(" . BasicSearchSQL($sKeyword) . ")";
			}
		} else {
			$sSearchStr = BasicSearchSQL($sSearch);
		}
		$sSearchStrTime = parseTime($sSearchStartTime,$sSearchEndTime);
		if( $sSearchStrTime <> "" ) {
			if( $sSearchStr <> "" ) {
				$sSearchStr = '(' . $sSearchStr . ') AND ';
			}
			$sSearchStr .= $sSearchStrTime;
		}
		$posts->setBasicSearchKeyword($sSearchKeyword);
		$posts->setBasicSearchType($sSearchType);
		$posts->setBasicSearchStartTime($sSearchStartTime);
		$posts->setBasicSearchEndTime($sSearchEndTime);
	}
	$sSearchStrProjectName = parseProjectName($sSearchProjectName);
	if( $sSearchStrProjectName <> "" ) {
		if( $sSearchStr <> "" ) {
			$sSearchStr = '(' . $sSearchStr . ') AND ';
		}
		$sSearchStr .= $sSearchStrProjectName;
	}
	
	$posts->setBasicSearchProjectName($sSearchProjectName);
	if($sSrchAdv <> ""){
		if( $sSearchStr <> "" ) {
			$sSearchStr = '(' . $sSearchStr . ') AND ';
		}
		$sSearchStr .= $sSrchAdv;
	}	
	return $sSearchStr;
}

// Clear all search parameters
function ResetSearchParms() {

	// Clear search where
	global $posts;
	$sSrchWhere = "";
	$posts->setSearchWhere($sSrchWhere);

	// Clear basic search parameters
	ResetBasicSearchParms();
}

// Clear all basic search parameters
function ResetBasicSearchParms() {

	// Clear basic search parameters
	global $posts;
	$posts->setBasicSearchKeyword("");
	$posts->setBasicSearchType("");
}

// Restore all search parameters
function RestoreSearchParms() {
	global $sSrchWhere, $posts;
	$sSrchWhere = $posts->getSearchWhere();
}

// Set up Sort parameters based on Sort Links clicked
function SetUpSortOrder() {
	global $posts;

	// Check for an Order parameter
	if (@$_GET["order"] <> "") {
		$posts->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
		$posts->CurrentOrderType = @$_GET["ordertype"];

		// Field id
		$posts->UpdateSort($posts->id);

		// Field url
		$posts->UpdateSort($posts->url);
		
		// Field projectname
		$posts->UpdateSort($posts->projectname);

		// Field datetime
		$posts->UpdateSort($posts->datetime);

		// Field title
		$posts->UpdateSort($posts->title);

		// Field digest
		$posts->UpdateSort($posts->digest);

		// Field counter
		$posts->UpdateSort($posts->counter);
		$posts->setStartRecordNumber(1); // Reset start position
	}
	$sOrderBy = $posts->getSessionOrderBy(); // Get order by from Session
	if ($sOrderBy == "") {
		if ($posts->SqlOrderBy() <> "") {
			$sOrderBy = $posts->SqlOrderBy();
			$posts->setSessionOrderBy($sOrderBy);
		}
	}
}

// Reset command based on querystring parameter cmd=
// - RESET: reset search parameters
// - RESETALL: reset search & master/detail parameters
// - RESETSORT: reset sort parameters
function ResetCmd() {
	global $sDbMasterFilter, $sDbDetailFilter, $nStartRec, $sOrderBy;
	global $posts;

	// Get reset cmd
	if (@$_GET["cmd"] <> "") {
		$sCmd = $_GET["cmd"];

		// Reset search criteria
		if (strtolower($sCmd) == "reset" || strtolower($sCmd) == "resetall") {
			ResetSearchParms();
		}

		// Reset Sort Criteria
		if (strtolower($sCmd) == "resetsort") {
			$sOrderBy = "";
			$posts->setSessionOrderBy($sOrderBy);
			$posts->id->setSort("");
			$posts->url->setSort("");
			$posts->projectname->setSort("");
			$posts->datetime->setSort("");
			$posts->title->setSort("");
			$posts->digest->setSort("");
			$posts->counter->setSort("");
		}

		// Reset start position
		$nStartRec = 1;
		$posts->setStartRecordNumber($nStartRec);
	}
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
	echo $sSql;
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
	$posts->projectname->setDbValue($rs->fields('projectname'));
	$posts->datetime->setDbValue($rs->fields('datetime'));
	$posts->title->setDbValue($rs->fields('title'));
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
	
	// projectname
	$posts->projectname->CellCssStyle = "";
	$posts->projectname->CellCssClass = "";

	// datetime
	$posts->datetime->CellCssStyle = "";
	$posts->datetime->CellCssClass = "";

	// title
	$posts->title->CellCssStyle = "";
	$posts->title->CellCssClass = "";

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
		
		// projectname
		$posts->projectname->ViewValue = $posts->projectname->CurrentValue;
		$posts->projectname->CssStyle = "";
		$posts->projectname->CssClass = "";
		$posts->projectname->ViewCustomAttributes = "";

		// datetime
		$posts->datetime->ViewValue = $posts->datetime->CurrentValue;
		$posts->datetime->ViewValue = ew_FormatDateTime($posts->datetime->ViewValue, 9);
		$posts->datetime->CssStyle = "";
		$posts->datetime->CssClass = "";
		$posts->datetime->ViewCustomAttributes = "";

		// title
		$posts->title->ViewValue = $posts->title->CurrentValue;
		$posts->title->CssStyle = "";
		$posts->title->CssClass = "";
		$posts->title->ViewCustomAttributes = "";

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

// Page Load event
function Page_Load() {

	//echo "Page Load";
}

// Page Unload event
function Page_Unload() {

	//echo "Page Unload";
}
function LoadCategoryRecordset($offset = -1, $rowcnt = -1) {
	global $conn, $categories;

	// Call Recordset Selecting event
	$categories->Recordset_Selecting($categories->CurrentFilter);

	// Load list page sql
	$sSql = $categories->SelectSQL();
	if ($offset > -1 && $rowcnt > -1) $sSql .= " LIMIT $offset, $rowcnt";

	// Load recordset
	$conn->raiseErrorFn = 'ew_ErrorFn';
	$rs = $conn->Execute($sSql);
	$conn->raiseErrorFn = '';

	// Call Recordset Selected event
	$categories->Recordset_Selected($rs);
	return $rs;
}
function LoadCategoryRowValues(&$rs) {
	global $categories;
	$categories->id->setDbValue($rs->fields('id'));
	$categories->name->setDbValue($rs->fields('name'));
	$categories->keywords->setDbValue($rs->fields('keywords'));
}
function RenderCategoryRow() {
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
		$categories->keywords->CssStyle = "";
		$categories->keywords->CssClass = "";
		$categories->keywords->ViewCustomAttributes = "";

		// id
		$categories->id->HrefValue = "";

		// name
		$categories->name->HrefValue = "";
	} elseif ($categories->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($categories->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($categories->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$categories->Row_Rendered();
}
function highlight($str,$key) {
	if( is_array($key) ){
		foreach($key as $k ) {
			$str = highlight($str,trim($k));
		}
		return $str;
	}
	else if ( is_string($key) ) {
		if( $key == '' ) return $str;
		//return(str_ireplace($key,'<span style="color:#f00;font-weight:bold;text-decoration:underline">'.$key.'</span>',$str));
		return(preg_replace("/($key)/isU","<span style=\"color:#f00;font-weight:bold;text-decoration:underline\">$1</span>",$str));
	}
	return $str;
}
function countword($str,$key) {
	$count = count_word($str,$key);
	if( $count == 0 ){
		return "";
	}
	else  {
		return "<span style=\"color:#ff0000;font-weight:bold;text-decoration:underline\">($count)</span>";
	}
	return "";
}
function count_word($str,$key) {
	$count = 0;
	if( is_array($key) ){
		foreach($key as $k ) {
			$count = $count + substr_count($str, $k);
		}
		return $count;
	}
	else if ( is_string($key) ) {
		if( $key == '' ) return 0;
		//return(str_ireplace($key,'<span style="color:#f00;font-weight:bold;text-decoration:underline">'.$key.'</span>',$str));
		return(substr_count($str, $key));
	}
	return $count;
}
?>
