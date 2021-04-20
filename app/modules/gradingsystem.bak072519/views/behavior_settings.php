<div class="col-lg-12">
    <div class="panel panel-green">
        <div class="panel-heading clearfix">
            <h5>Observed Values and Behavioral Statements</h5>
        </div>
        <div class="panel-body">
            <table class="table table-bordered">
                <tr>
                    <th>CORE VALUES</th>
                    <th >Behavioral Statements</th>
                </tr>
                <?php 
                foreach ($coreValues as $cv):
                    $bStatements = Modules::run('gradingsystem/getListOfValues', $cv->core_id);
                    //count($bStatements->result());
                ?>
                <tr>
                    <td style="vertical-align: middle; width:10%; text-align: center;"><?php echo $cv->core_values; ?></td>
                    <td colspan="2"><table class="table table-bordered">
                        <?php foreach ($bStatements->result() as $bs): 
                            ?>
                            <tr>
                                <td style="vertical-align: middle; width:50%;  text-align: left;">
                                    <?php echo $bs->bh_name; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </table>
                    </td>
                </tr>    
                    
                <?php
                endforeach;
            ?>
                
            </table>
            

        </div>
    </div>
    
</div>