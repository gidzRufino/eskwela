<div class="row"><?php echo $filePath; ?>
    <div class="col-lg-12">
        <h3 style="margin:10px 0;" class="page-header">Collection Report 
            <div class="btn-group pull-right" role="group" aria-label="">
                <button type="button" class="btn btn-default" onclick="document.location='<?php echo base_url() ?>'">Dashboard</button>
            <button type="button" class="btn btn-default" onclick="document.location='<?php echo base_url('finance/accounts') ?>'">Accounts</button>
            <button type="button" class="btn btn-default" onclick="generateCollectible()">Generate Collectible</button>
            <div class="form-group pull-right">
                <select onclick="getStudentByYear(this.value)" tabindex="-1" id="inputSY" style="width:200px; font-size: 15px;">
                    <option>School Year</option>
                    <?php 
                          foreach ($ro_year as $ro)
                           {   
                              $roYears = $ro->ro_years+1;
                              if($this->session->userdata('school_year')==$ro->ro_years):
                                  $selected = 'Selected';
                              else:
                                  $selected = '';
                              endif;
                          ?>                        
                        <option <?php echo $selected; ?> value="<?php echo $ro->ro_years; ?>"><?php echo $ro->ro_years.' - '.$roYears; ?></option>
                        <?php }?>
                </select>
             </div>
              </div>
        </h3>
    </div>
    <div class="col-lg-12" style="margin-bottom: 10px;">
        <div class="row pull-right">
            <input name="startDate" type="text" data-date-format="yyyy-mm-dd" value="<?php echo ($this->uri->segment(3)==NULL?date('Y-m-d'):$this->uri->segment(3)) ?>" id="startDate" placeholder="Select Start Date" />
            <input  name="endDate" type="text" data-date-format="yyyy-mm-dd" value="<?php echo ($this->uri->segment(4)==NULL?date('Y-m-d'):$this->uri->segment(4)) ?>" id="endDate" placeholder="Select End Date" />
            
            <div class="btn-group pull-right" role="group" aria-label="">
                <button onclick="generateCollection()" type="button" class="btn btn-medium btn-primary">Generate Collection</button>
                <button onclick="printSales($('#reportType').val())" type="button" class="btn btn-medium btn-info">Print</button>
            </div>
            <div class="form-group pull-right">
                <select tabindex="-1" id="reportType" name="reportType"  class="col-lg-12">
                   <option value="0">Report Per Account</option>
                   <option value="1">Report Per Item</option>
                   
               </select>
             </div>
        </div>
    </div>
    <div class="col-lg-2"></div>
    <div id="salesTable" class="col-lg-8">
        <table class="table table-striped">
            <tr>
                <th style="width:10%;">Date</th>
                <th style="width:10%;">OR #</th>
                <th style="width:30%;">Account Name</th>
                <th style="width:30%;">Particulars</th>
                <th style="width:30%;">Teller</th>
                <th style="width: 40%; text-align: right;">Amount</th>
            </tr>
            <tbody id="salesBody">
                    <?php 
                        $total = 0;
                        $overAll = 0;
                        foreach($collection->result() as $c): 
                            if($c->t_type != 3 && $c->t_type!=5):
                                $ctype = $c->acnt_type;
                                $a_id = $c->t_st_id;
                                if ($ctype!=1) {
                                  $overAll += $c->t_amount;
                                  $account_name = strtoupper($c->lastname.', '.$c->firstname); 
                                  $grade_level = $c->level;
                                  $teller = Modules::run('hr/getEmployeeName',$c->t_em_id);
                                  $tellerName = strtoupper(substr($teller->firstname, 0,1).'. '.$teller->lastname);

                                ?>

                                <tr>
                                    <td><?php echo $c->t_date ?></td>
                                    <td><?php echo $c->ref_number ?></td>
                                    <td><?php echo $account_name ?></td>
                                    <td><?php echo $c->item_description ?></td>
                                    <td><?php echo $tellerName ?></td>
                                    <td style="text-align: right;"><?php echo number_format($c->t_amount, 2, '.',',')?></td>
                                </tr>
                        <?php
                                }
                                unset($total);
                            endif;
                        endforeach; 
                        $cancelReceipt = $this->finance_model->getCollection($from, $to, 3, $school_year);
                        foreach($cancelReceipt->result() as $cr): 
                            $ctype = $cr->acnt_type;
                            $a_id = $cr->t_st_id;
                              $account_name = strtoupper($cr->lastname.', '.$cr->firstname); 
                              $grade_level = $cr->level;
                              $teller = Modules::run('hr/getEmployeeName',$cr->t_em_id);
                              $tellerName = strtoupper(substr($teller->firstname, 0,1).'. '.$teller->lastname);
                              
                            ?>

                            <tr class="strikeout">
                                <td><?php echo $cr->t_date ?></td>
                                <td><?php echo $cr->ref_number ?></td>
                                <td><?php echo $account_name ?></td>
                                <td><?php echo $tellerName ?></td>
                                <td style="text-align: right;"><?php echo number_format($cr->t_amount, 2, '.',',')?></td>
                            </tr>
                        <?php
                        endforeach;
                            if($overAll!=0):
                        ?>
                            <tr>
                                <th colspan="5"></th>
                                <th style="text-align: right;"> <?php echo number_format($overAll, 2, '.',',') ?></th>
                            </tr>
                        <?php
                            endif;
                            
                        ?>
                </tbody>
        </table>
    </div>
    
    
</div>

<script type="text/javascript">
    
    $('#startDate').datepicker({
        orientation: 'left'
    });
    $('#endDate').datepicker({
        orientation: 'left'
    });
    
     function generateCollectible()
    {
        var url = document.location='<?php echo base_url('finance/getCollectible/') ?>'+$('#inputSY').val()+'/1';
        
        document.location = url;
    }
    
     function generateCollection()
    {
        var url = '<?php echo base_url() . 'finance/collectionReport/' ?>'+$('#startDate').val()+'/'+$('#endDate').val();
        
        document.location = url;
    }
    
     function printSales(option)
    {
        switch(option)
        {
            case '0':
                var url = '<?php echo base_url() . 'finance/printCollectionReport/' ?>'+$('#startDate').val()+'/'+$('#endDate').val();
        
                window.open(url, '_blank');
            break;
            case '1':
                var url = '<?php echo base_url() . 'finance/printCollectionReportPerTeller/'.base64_encode($this->session->employee_id) ?>/'+$('#startDate').val()+'/'+$('#endDate').val();
        
                window.open(url, '_blank');
            break;
        }
        
    }
 
    function getSales()
    {
         $.ajax({
               type: 'GET',
               url: '<?php echo base_url() . 'canteen/getSales/' ?>'+$('#startDate').val()+'/'+$('#endDate').val(),
               data: 'csrf_test_name='+$.cookie('csrf_cookie_name'),
               success: function (response) {
                   //    alert(response);
                   $('#salesBody').html(response);
               }
           });
    }
    

</script>    