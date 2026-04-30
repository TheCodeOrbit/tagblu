<div class="leftsiede-summary col-sm-2 collapse in" id="leftsiede-summary"><!-- Left side starts -->
		<!-- Large button group -->
		<div class="quicklinkdiv" id="Leftcontainer"><!-- left side links -->
	<?php $ModuleName=$ActionList['ModuleName'];
		$ActionName=$ActionList['ActionName'];
	//echo "<br>ModuleName=$ModuleName and ActionName=$ActionName";
	$ActionUrl=Yii::app()->createAbsoluteUrl($ModuleName)."/";
	///RelatedList/SourceModule/Customer/SourceRecord/248
	if($_REQUEST['SourceModule']!="")
	{
		$RelatedSourceModule=$_REQUEST['SourceModule'];
		$RelatedSourceRecord=$_REQUEST['SourceRecord'];
		$ListUrl="{$ActionUrl}RelatedList/SourceModule/{$RelatedSourceModule}/SourceRecord/{$RelatedSourceRecord}/List";
	}
	else
	$ListUrl="{$ActionUrl}List";
	//print"<pre>";	
	//print_r();
	?>
		<svg id="svglink" height="100%" width="205" viewBox="0 0 150 100" preserveAspectRatio="none" shape-rendering="geometricPrecision">
			<path d="M0,0 h130 l20,50 l-20,50 h-130z" fill="#337AB7" />
		</svg>	
			<div id="svgcontent" class="cursorPointer" onclick="window.location = '<?php echo $ListUrl; ?>'"><?php echo $ModuleLabel;?> List</div>
		  <!--<p class="quicklinkdivP-selected" onclick="window.location = '<?php echo $ListUrl; ?>'"><?php echo $ModuleLabel;?> List</p>
		 <!-- <p onclick="">Dashboard</p>
		  <p onclick="">Recently Modified</p>
		  <p onclick="">Tag Cloud</p> -->
		</div><!-- left side links ends -->
	</div><!-- Left side ends -->
