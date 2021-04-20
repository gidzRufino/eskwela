<script type="text/javascript">
   $(function(){
      $("#bday").tablesorter({debug: true});
   });  
</script>
<div class="row">
    <div class="col-lg-12">
        <h3 style="margin-top: 20px;" class="page-header">List of Students and their Corresponding Birthday</h3>
    </div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<b>Item Status</b>
			</div>
			<div class="panel-body" style="height: 400px;overflow-y:scroll;">
				<table id="bday" class="table table-bordered tablesorter" style="width:100%;">
					<thead class="bg-primary">
						<!-- <th style="text-align:center">#</th> -->
						<th style="text-align:center">Student Name</th>
						<th style="text-align:center">Grade Level</th>
						<th style="text-align:center">Birthdate</th>
					</thead>
					
					<?php 
						$count = 0;
						foreach ($showbday as $bday) {
							$count = $count + 1;
							$name = $bday->lastname.', '.$bday->firstname;
					?>		
					
					<tr>
						<!-- <td style="text-align:center"><?php echo $count ?></td> -->
						<td style="text-align:center"><?php echo $name ?></td>
						<td style="text-align:center"><?php echo $bday->level ?></td>
						<td style="text-align:center"><?php echo $bday->cal_date ?></td>
					</tr>

					<?php }
					?>
				</table>
			</div>
			<div class="panel-footer">
				<span> Press on the header to sort data. Scroll for more details. Number of students: <b style="color: red;"><?php echo $count ?></b></span>
			</div>
		</div>
	</div>
</div>