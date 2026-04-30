<div id="ApproveDrill" class="">
            <!-- Add Drilling Data Screen -->
            <div class="body-menu d-flex">
                <p class="body-heading">Approve Drilling Data</p>
                <div class="d-flex position-absolute start-50 translate-middle-x">
                    <div class="input-outline flex jc-spbt ai-cntr m-r-10 w-150px">
                        <input type="date"> <!-- current date by default should come here -->
                    </div>
                    <select class="form-select w-150px" aria-label="select example">
                        <option selected="true" disabled>Select Days</option>
                        <option value="1">From Day 1 to Day 15</option>
                        <option value="2">From Day 16 to Day 31</option>
                    </select>                
                </div>
            </div>
            
            <div class="body-container">
                <div class="body-outline">
                    <table>
                        <tr>
                            <th class="bg-white b-prim">
                                <div class="custom-checkbox">
                                    <!-- below input checkbox id needs to be unique -->
                                    <input type="checkbox" id="checkbox1" name="checkbox" class="checkbox checkbox--prim"/>
                                    <label for="checkbox1" class="sm">Checkbox 5</label>
                                </div>
                            </th>
				<?php //print_r($RecordList);	
			    		$col_span=count($ColumnList)+1;
					foreach ($ColumnList as $key=> $Column): ?>
					<th><?php echo $Column;?></th>
				<?php endforeach;?>	
                            <!--<th>Shift</th>
                            <th>Date</th>
                            <th>Drilling material</th>
                            <th>Drill Machine No.</th>
                            <th>Working Area</th>
                            <th>No of Holes Drilled</th>
                            <th>Burden(m)</th>
                            <th>Hole Depth(m)</th>
                            <th>Spacing(m)</th>
                            <th>Type of Drilling</th>
                            <th>Bench height(m)</th>-->
                        </tr>
			<?php //print_r($RecordList);
						
						$addUrl="{$ActionUrl}Create";
						if(count($RecordList)>0):
						foreach ($RecordList as $Record): 
						$dt= str_replace("/","-",$Record['date']);
						$currdt = date('Y-m-d',strtotime($dt)); 
						$recdate = date('Y-m-d', strtotime('+1 day', strtotime($currdt)));?>
					<tr>
				<?php if(!in_array($Record['date'],$arrDate))
					{
						$arrDate[]=$Record['date'];?>
							<td>
                                <div class="custom-checkbox">
                                    <input type="checkbox" id="checkbox2" name="checkbox" class="checkbox checkbox--prim"/>
                                    <label for="checkbox2" class="sm">Checkbox 5</label>
                                </div>
                            </td>
				<?php } ?>
				<?php foreach ($ColumnList as $key=> $Column):?>
					<td><?php echo strip_tags($Record[$key]);?></td>
				<?php endforeach;?>		

					</tr>	
					<?php endforeach;?>
				<?php endif;?>
                        <!--<tr>
                            <td>
                                <div class="custom-checkbox">
                                    <input type="checkbox" id="checkbox2" name="checkbox" class="checkbox checkbox--prim"/>
                                    <label for="checkbox2" class="sm">Checkbox 5</label>
                                </div>
                            </td>
                            <td>A</td>
                            <td class="w-120px">16-Oct-2021</td>
                            <td>Coal</td>
                            <td>CG4DM5376</td>
                            <td>Salhi Pit</td>
                            <td>889</td>
                            <td>8.00</td>
                            <td>8.00</td>
                            <td>8.00</td>
                            <td>Normal</td>
                            <td>8.00</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="custom-checkbox">
                                    <input type="checkbox" id="checkbox3" name="checkbox" class="checkbox checkbox--prim"/>
                                    <label for="checkbox3" class="sm">Checkbox 5</label>
                                </div>
                            </td>
                            <td>A</td>
                            <td class="w-120px">16-Oct-2021</td>
                            <td>Coal</td>
                            <td>CG4DM5376</td>
                            <td>Salhi Pit</td>
                            <td>889</td>
                            <td>8.00</td>
                            <td>8.00</td>
                            <td>8.00</td>
                            <td>Normal</td>
                            <td>8.00</td>
                        </tr>-->
                    </table>
                </div>
                <div class="body-footer d-flex justify-content-between align-items-center mx-2rem">
                    <!-- pagination -->
                    <div class="pagination">
                        <div class="d-flex justify-content-between align-items-center disabled me-3">
                            <svg width="16" height="9" viewBox="0 0 16 9" fill="none" xmlns="http://www.w3.org/2000/svg" class="left-arrow me-2">
                                <path d="M2 1.5L7.29289 6.79289C7.68342 7.18342 8.31658 7.18342 8.70711 6.79289L14 1.5" stroke-width="3" stroke-linecap="round"/>
                                </svg>
                            <p>Prev</p>                   
                        </div>
                        <div class="pagination-btn me-3 active">
                            <p>1</p>
                        </div>
                        <div class="pagination-btn me-3">
                            <p>2</p>
                        </div>
                        <div class="pagination-btn me-3">
                            <p>3</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center me-3">
                            <p>Next</p>                 
                            <svg width="16" height="9" viewBox="0 0 16 9" fill="none" xmlns="http://www.w3.org/2000/svg" class="right-arrow ms-2">
                                <path d="M2 1.5L7.29289 6.79289C7.68342 7.18342 8.31658 7.18342 8.70711 6.79289L14 1.5" stroke-width="3" stroke-linecap="round"/>
                                </svg>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary input-save me-4" data-bs-toggle="modal" data-bs-target="#exampleModal">discard</button>
                        <button type="button" class="btn btn-primary input-save" data-bs-toggle="modal" data-bs-target="#exampleModal">approve</button>
                    </div>
                </div>
            </div>
        </div>
