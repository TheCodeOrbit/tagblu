
<!-- pagination -->
<?php if($totalitemcount['totrecords'] > 0 ){?>
                    <div class="pagination">

			<input id="noofpages" name="noofpages" type="hidden" value="<?php echo $totalitemcount['noofpages']; ?>"/>
			<?php $pageNumber=$_POST['pageNumber'];if(($_POST['pageNumber'] !='' || $_POST['pageNumberpre'] !='' || $_POST['pagejump'] !='') && $_POST['orderby'] !='') {
					$val1=$listserchvals['orderby'];
					$val2=$listserchvals['nextorder'];
					$urls = Yii::app()->createAbsoluteUrl($module)."/List/OrderBy/".$val1."/SortOrder/".$val2;
				}else if($_POST['pageNumber'] !=''){
					$url = Yii::app()->createAbsoluteUrl($module)."/List";
				}else{
					$url = Yii::app()->createAbsoluteUrl($module)."/List";
				}
				echo CHtml::beginForm($urls, 'POST',array("name"=>"previous","id"=>"previous")); ?>
				<div class="d-flex justify-content-between align-items-center disabled me-3">
					<input type="hidden" value="<?php if($orderby !='') echo $orderby; else echo $listserchvals['orderby']; ?>" name="orderby" id="orderby" /> 
					<input type="hidden" value="<?php if($sortorder !='') echo $sortorder;  else echo $listserchvals['nextorder']; ?>" name="nextorder" id="nextorder" />
					<?php echo $listserchvals['hidevals'];?>
				<svg width="16" height="9" viewBox="0 0 16 9" fill="none" xmlns="http://www.w3.org/2000/svg" class="left-arrow me-2">
                                <path d="M2 1.5L7.29289 6.79289C7.68342 7.18342 8.31658 7.18342 8.70711 6.79289L14 1.5" stroke-width="3" stroke-linecap="round"/>
                                </svg>
                           <button type="button" class="btn" <?php if($totalitemcount['previousPageExists'] == 'FALSE'){ ?> disabled <?php } ?> name="listViewpreviousPageButton" id="listViewpreviousPageButton" onclick="previous.submit();">
			<p>Prev</p> 

			</button>
				</div>
			<input id="pageNumberpre" name="pageNumberpre" type="hidden" value="<?php echo $totalitemcount['nextPageNumber']; ?>">
			
			<?php echo CHtml::endForm();?>

                        
                                              
                        
		<?php for($i=1;$i<= $totalitemcount['noofpages'];$i++){ ?>
			<?php echo CHtml::beginForm($urls, 'POST',array("name"=>"pagejumpfm".$i,"id"=>"pagejumpfm".$i)); ?>
			<input type="hidden" value="<?php if($orderby !='') echo $orderby; else echo $listserchvals['orderby']; ?>" name="orderby" id="orderby" /> 
			<input type="hidden" value="<?php if($sortorder !='') echo $sortorder;  else echo $listserchvals['nextorder']; ?>" name="nextorder" id="nextorder" /> 
			<?php echo $listserchvals['hidevals'];?>
			
				<input type="hidden" rel="curPageNo:<?php echo $curPageNo;?>" id="pagejump" name="pagejump"  value="<?php echo $i ; ?>" />
				<div class="pagination-btn me-3 <?php echo ($totalitemcount['nextPageNumber']==$i ? 'active' : '');?>" onclick="pagejumpfm<?php echo $i;?>.submit();"> <p><?php echo $i;?></p></div>

			<?php if($i==5 and $totalitemcount['noofpages']>5){?>
					<div class="pagination-btn me-3">
                            <p>...</p>
                        </div>
			<?php echo CHtml::endForm();?> 	
			<?php break;}?>
			<?php echo CHtml::endForm();?> 
		<?php } ?>
	


                       <?php echo CHtml::beginForm($urls, 'POST',array("name"=>"next","id"=>"next")); ?>
				 <div class="d-flex justify-content-between align-items-center me-3">
					<input type="hidden" value="<?php if($orderby !='') echo $orderby; else echo $listserchvals['orderby']; ?>" name="orderby" id="orderby" />
					<input type="hidden" value="<?php if($sortorder !='') echo $sortorder;  else echo $listserchvals['nextorder']; ?>" name="nextorder" id="nextorder" />
					<?php echo $listserchvals['hidevals'];?>
				
			<input id="pageNumber" name="pageNumber" type="hidden" value="<?php echo $totalitemcount['nextPageNumber']; ?>">
				<button type="button" class="btn" <?php if($totalitemcount['nextPageExists'] == 'FALSE'){?> disabled <?php } ?> name="listViewNextPageButton" id="listViewNextPageButton" onclick="next.submit();"> <p>Next</p>                  </button>
                            <svg width="16" height="9" viewBox="0 0 16 9" fill="none" xmlns="http://www.w3.org/2000/svg" class="right-arrow ms-2">
                                <path d="M2 1.5L7.29289 6.79289C7.68342 7.18342 8.31658 7.18342 8.70711 6.79289L14 1.5" stroke-width="3" stroke-linecap="round"/>
                                </svg>
			
				</div>
			<?php echo CHtml::endForm();?>
                    </div>
<?php } ?>
