<table style="width:100%;" class="table table-bordered table-striped table-hover payrollTable text-center">

    <tr class="head" style="font-weight:bold; border-bottom: 2px solid black;">
        <td style="width:10px;">Avatar</td>
        <td style="width:150px;">Name of Employee</td>
        <td style="width:50px;">Position</td>
        <td class="afterThis">Gross Pay</td>
		<?php
		 $charge = Modules::run('hr/payroll/getPayrollCharges', $pc_code);
		 $defaults = Modules::run('hr/payroll/getPayrollDefaults');
		 foreach ($defaults as $d):
			 if ($d->pi_is_default != 2):
				 ?>
		 		<td ><?php echo $d->pi_item_name ?></td>

				 <?php
			 endif;
		 endforeach;
		?>

        <td>Other Deductions</td>
        <td>Total Deductions</td>
        <td>NetPay</td>
        <td></td>
    </tr>
	<?php
	 $salaryTotal = 0;
	 $sssTotal = 0;
	 $phTotal = 0;
	 $pagibigTotal = 0;
	 $tinTotal = 0;
	 $contTotal = 0;
	 $netTotal = 0;
	 $total = 0;
	 $totalOd = 0;
	 $totalNet = 0;
	 $totalGross = 0;
	 $totalNetPay = 0;
	 $addOnTotal = 0;
	 $statTotal = 0;
	 $overAllDeductibleTardy = 0;
	 $netPay = 0;

	 $lrn = 0;
	 foreach ($getPayrollReport as $pr):
		 $lrn++;
		 if ($pr->salary != 0):
			 switch ($paymentSchedule->monthly):
				 case 0:
					 $over = 2;
					 break;
				 case 1:
					 $over = 1;
					 break;
				 case 2:
					 $over = 4;
					 break;
			 endswitch;

			 //calculation of the Basic Pay and the benefits
			 $workdays = $hrdb->getWorkdays($startDate, $endDate, TRUE);

			 switch ($pr->pg_id):
				 case 1:
				 case 2:
					 $daysInAMonth = $hrdb->getWorkdays(date('Y-m', strtotime($startDate)) . '-0' . $firstDay, date('Y-m', strtotime($endDate)) . '-' . $lastDay, TRUE);

					 $salary = $pr->salary;

					 $expectedHours = ($workdays * 8) - 4;
					 $days = Modules::run('hr/hrdbprocess/getPayrollTimes', $pr->user_id, $startDate, $endDate);
					 $days = json_decode($days);

					 $dailySalary = round($pr->salary / $daysInAMonth, 2, PHP_ROUND_HALF_UP);
					 $salaryPerHour = round($dailySalary / 8, 2, PHP_ROUND_HALF_UP);

					 $totalHourTardy = round(($salaryPerHour / 60) * $days->undertime, 2, PHP_ROUND_HALF_UP);
					 //use this if exact deduction
					 //$totalDeductibleTardy = round($pr->salary/$schoolDays/8/60*$undertime, 2);

					 $totalDeductibleTardy = ($pr->pay_type ? 0 : $totalHourTardy);
					 $netpay = ($pr->salary / $over);
					 break;
				 case 4:
					 $daysDetails = Modules::run('hr/payroll/getManHours', $pr->employee_id, $pc_code, 1);
					 $salary = (!empty($daysDetails->pmh_amount)) ? $daysDetails->pmh_amount : 0;
					 $netpay = $salary;
					 break;

			 endswitch;
			 //echo $workdays;

			 $additionalIncome = 0;
			 foreach ($manHoursCat as $pmc):
				 $manHours = Modules::run('hr/payroll/getManHourTypeByCat', $pmc->pmc_id);
				 foreach ($manHours as $pmt):
					 $hourDetails = Modules::run('hr/payroll/getManHours', $pr->employee_id, $pc_code, $pmt->pmt_id);
					 $additionalIncome += (!empty($hourDetails->pmh_amount)) ? $hoursDetails->pmh_amount : 0;
				 endforeach;
			 endforeach;
			 $gross = $salary + $additionalIncome;
			 if ($gross != 0):
				 ?>
			 	<tr id="tr_<?php echo $pr->user_id; ?>">
			 		<td style="text-align: center"><img class="img-circle"  style="width:30px;" src="<?php echo base_url() . 'uploads/' . $pr->avatar ?>" /></td>
			 		<td ><?php echo $pr->firstname . ' ' . $pr->lastname ?></td>
			 		<td><?php echo $pr->position ?></td>
			 		<td id="<?php echo $pr->user_id ?>_td" td_id="td_<?php echo $pr->user_id; ?>_<?php echo $d->pi_item_id ?>" tdvalue="<?php echo $gross ?>" class="afterValue"><?php echo number_format(($gross), 2, '.', ',') ?></td>
					 <?php
					 $totalStat = 0;
					 $addOn = 0;
					 $totalNetPayroll = 0;
					 $items = 1;
					 $item = 1;
					 $deduct = array();
					 foreach ($defaults as $d):
						 $items++;
					 
						 $c = Modules::run('hr/payroll/getPayrollChargesByItem', $d->pi_item_id, $pc_code, $pr->employee_id);
						 if ($d->pi_is_default != 2):
							 if (!empty($c->row())):
								 $amount = round($c->row()->pc_amount, 2);
							 else:
								 if ($d->pi_item_id == 1):
									 $equivalent = Modules::run('hr/payroll/getSSSTableEquivalent', $gross);
									 $amount = $equivalent;
								 else:
									 $amount = ($d != NULL ? $d->pi_default : 0);
								 endif;
								 Modules::run('hr/payroll/setPayrollCharges', $pr->user_id, $d->pi_item_id, $amount, pc_code);
							 endif;
							 ?>
					 		<td id="td_<?php echo $pr->user_id; ?>_<?php echo $d->pi_item_id ?>" class="defaults_<?php echo $pr->user_id; ?> tdValue_<?php echo $items ?> tdDefaults_<?php echo $d->pi_item_id ?>" 
					 			td_value="<?php echo $amount ?>"
					 			td_item_id="<?php echo $d->pi_item_id ?>"
					 			><?php echo $amount ?></td>

							 <?php
						 else:
							 $amount = $c->row()->pc_amount;
						 endif;
						 $totalStat += $amount;
					 endforeach;
					 foreach ($charges as $deductions):
						 $items++;
						 $charge = Modules::run('hr/payroll/getPayrollChargesByItem', $deductions->pc_item_id, $pc_code, $pr->employee_id);
						 $amount = ((!empty($charge->row())) ? $charge->row()->pc_amount : 0);
						 ?>
				 		<td id="td_<?php echo $pr->user_id; ?>_<?php echo $d->pi_item_id ?>" class="tdValue_<?php echo $items ?> tdDefaults_<?php echo $d->pi_item_id ?>"
				 			td_value="<?php echo $amount ?>"><?php echo $amount ?></td>

						 <?php
						 if ($charge != NULL):
							 if ($charge->pi_item_type == 1):
								 $addOn += $amount;
							 else:
								 $totalStat += $amount;
							 endif;
						 endif;
					 endforeach;
					 $netPayLessDeduction = ($netpay + $additionalIncome) - ($totalDeductibleTardy);
					 //print_r($loanList->result());
					?>

			 		<td><?php echo $totalDeductibleTardy; ?></td>
			 		<td><?php echo number_format(($totalDeductibleTardy + $totalStat), 2, '.', ',') ?></td>
			 		<td style="<?php echo (($gross) - ($totalDeductibleTardy + $totalStat) < 50 ? 'background-color:rgb(217, 83, 79); color:white;' : '') ?>;"><?php echo number_format((($gross) - ($totalDeductibleTardy + $totalStat)), 2, '.', ',') ?></td>
			 		<td style="width:25%">
						 <?php $ptrans = Modules::run('hr/payroll/checkTransaction', $pr->employee_id, $pc_code); ?>
			 			<div class="btn-group">
			 				<button onclick="getDateFrom('<?php echo $startDate ?>', '<?php echo $endDate ?>', '<?php echo $pr->user_id ?>')" class="btn btn-xs btn-primary ">View Time Attendance</button>
			 				<button onclick="$('#addCharges').modal('show'),
                                                                         loadPayrollDeduction()
			 						 $('#grossPay').html($('#<?php echo $pr->user_id ?>_td').attr('tdvalue')),
			 						 $('#pc_profile_id').val('<?php echo $pr->employee_id ?>'),
			 						$('#pc_code').val('<?php echo $pc_code ?>'), $('#netPay').html('<?php echo round(($gross) - ($totalDeductibleTardy + $totalStat), 2); ?>')" 
			 						class="btn btn-xs btn-danger " <?php echo ($ptrans->ptrans_status) ? "disabled"  : "";  ?>>
			 					Addition / Deduction
			 				</button>
			 				<button id="approveBtn_<?php echo $pr->user_id ?>" style="display:<?php echo ($ptrans == NULL ? '' : 'none') ?>" onclick="approvePayroll('<?php echo $pr->employee_id ?>', '<?php echo (($gross + $additionalIncome) - ($totalDeductibleTardy + $totalStat)) ?>', '<?php echo $pc_code ?>', '<?php echo $pr->user_id ?>')" class="btn btn-xs btn-warning">Approve</button>
			 					<!--<button onclick="loanPayment('<?php echo $pr->user_id ?>')" class="btn btn-xs btn-warning">Approve</button>-->
			 				<button style="display: <?php echo ($ptrans == NULL ? 'none' : '') ?>" id="releaseBtn_<?php echo $pr->user_id ?>" onclick="releasePayroll('<?php echo $pr->employee_id ?>', '<?php echo (($gross + $additionalIncome) - ($totalDeductibleTardy + $totalStat)) ?>', '<?php echo $pc_code ?>', '<?php echo $pr->user_id ?>')" <?php echo ($ptrans->ptrans_status ? 'disabled' : '') ?> class="btn btn-xs <?php echo ($ptrans->ptrans_status ? 'btn-success' : 'btn-danger') ?>"><?php echo ($ptrans->ptrans_status ? 'Released' : 'Release') ?></button>

			 			</div>
			 		</td> 
			 	</tr>
				 <?php
			 endif;

			 $totalGross += $gross;
			 $totalNetPay += $netpay;
			 $addOnTotal += $addOn;
			 $statTotal += $totalStat;
			 $totalOD += ($totalDeductibleTardy + $totalStat);
			 $totalTardy += $totalDeductibleTardy;
			 unset($addOn);
			 unset($totalStat);
			 unset($expectedPerHourRate);
			 $items = 1;
		 endif;
		 $salary = 0;
		 $additionalIncome = 0;
	 endforeach;  //end of Payroll Report
	?>
	<tr style='border-top-style:double; font-weight: bold;'>
		<td colspan="2">Total</td>
		<td></td>
		<td><?php echo number_format($totalGross, 2, '.', ','); ?></td>
		<?php
		 $charges = Modules::run('hr/payroll/getPayrollCharges', $pc_code);
		 $totalNetPayroll = (($totalGross + $addOnTotal) - ($totalOD));
		 foreach ($defaults as $d):
			 $items++;
			 $amount = ($d != NULL ? $d->pi_default : 0);
			 if ($d->pi_is_default != 2):
				 ?>
		 		<td class="total_<?php echo $items ?>" ></td>

				 <?php
				 $totalStats += $amount;
			 endif;
		 endforeach;
		 foreach ($charges as $deductions):
			 $items++;
			 $chrg = Modules::run('hr/payroll/getPayrollChargesByItem', $deductions->pc_item_id, $pc_code);
			 ?>
	 		<td class="total_<?php echo $items ?>"></td>

			 <?php
		 endforeach;
		?>
		<td><?php echo number_format($totalTardy, 2, '.', ','); ?></td>
		<td><?php echo number_format($totalOD, 2, '.', ','); ?></td>
		<td><?php echo number_format($totalNetPayroll, 2, '.', ','); ?></td>
		<td></td>
	</tr>
</table>
<input type="hidden" id="charges" value="<?php echo $items; ?>" />
<input type="hidden" id="totalNetIncome" />
<script type="text/javascript">

	$(document).ready(function () {
		getTotal();
	});

	function loadPayrollDeduction(account_num, dateFrom, dateTo, owners_id, pc_code, employee_id, netPay)
	{
		var grossPay = $('#' + owners_id + '_td').attr('tdvalue');
		var url = "<?php echo base_url() ?>hr/payroll/loadPayrollDeduction"; // the script where you handle the form input.
		$.ajax({
			type: "POST",
			url: url,
			data: "owners_id=" + owners_id + "&grossPay=" + grossPay + "&partialNet=" + netPay + "&dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&accountNumber=" + account_num + "&pc_code=" + pc_code + "&employee_id=" + employee_id + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
			success: function (data)
			{
				$('#payrollDeductionBody').html(data)
			}
		});

		return false; // avoid to execute the actual submit of the form.
	}

	function getDateFrom(dateFrom, dateTo, owners_id)
	{
		var url = "<?php echo base_url() ?>hr/searchDTRbyDateForPayroll"; // the script where you handle the form input.
		$.ajax({
			type: "POST",
			url: url,
			data: "owners_id=" + owners_id + "&dateFrom=" + dateFrom + "&dateTo=" + dateTo + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
			success: function (data)
			{
				$('#viewDTR').modal('show');
				$('#dtrBody').html(data)

			}
		});

		return false; // avoid to execute the actual submit of the form.
	}

	function doEdit(cid) {
		var val = parseFloat($("#" + cid).html());
		var newVal = 0;
		var writtenValue = 0;
		var trVal = $("#" + cid).attr('tr_value');
		var newTrVal = "";
		if ($("#" + cid).attr('td_value') != 0) {
			$("#" + cid).html("<input type='number' class='no-border' style='width:50px;' id='userinput' value='" + val + "'>");
			$("#userinput").focus().select();
			$("#userinput").keydown(function (e) {
				newVal = parseFloat($(this).val());
				writtenValue = numberWithCommas(newVal.toFixed(2));
				newTrVal = trVal.replace(val, newVal);
				if (e.which == 13) {
					e.preventDefault();
					$("#" + cid).attr('td_value', newVal);
					$("#" + cid).attr('tr_value', newTrVal);
					$("#" + cid).html(writtenValue);
					getTotal();
					$('#notificationAlert').show().fadeOut(5000)
					$('#notificationAlert').html('Successfully Saved!');
				}
			});
			$("#userinput").focusout(function () {
				$("#" + cid).attr('td_value', val);
				$("#" + cid).html(val);

			});


		}

	}

	function saveDefaultDeduction(user_id, pc_code, em_id)
	{
		$('td.defaults_' + user_id).each(function () {
			if ($(this).attr('td_value') != 0) {
				//alert($(this).attr('td_value'));
				$.ajax({
					type: 'POST',
					url: '<?php echo base_url() . 'hr/payroll/addDeduction' ?>',
					//dataType: 'json',
					data: {
						item_id: $(this).attr('td_item_id'),
						amount: $(this).attr('td_value'),
						pc_code: pc_code,
						em_id: em_id,
						csrf_test_name: $.cookie('csrf_cookie_name')
					},
					success: function (response) {

					}

				});
			}
		});
	}

	function loanPayment(user_id, pc_code, em_id)
	{
		var or_num = '';
		var payType = 0;
		var profile_id;
		var accountNumber;
		var loanReferenceNumber;
		var transDate;
		var data = [];
		$('td.loanlist_' + user_id).each(function () {
			if ($(this).attr('td_value') != 0) {
				data.push($(this).attr('tr_value'));
				var amount = $(this).attr('td_value');
				profile_id = $(this).attr('profile_id');
				accountNumber = $(this).attr('acnt_number');
				loanReferenceNumber = $(this).attr('lrn');
				transDate = $(this).attr('transDate');
				var item_id = $(this).attr('payroll_item_id');

				$.ajax({
					type: 'POST',
					url: '<?php echo base_url() . 'coopmanagement/saveTransaction' ?>',
					//dataType: 'json',
					data: {
						items: JSON.stringify(data),
						profile_id: profile_id,
						accountNumber: accountNumber,
						or_num: or_num,
						loanReferenceNumber: loanReferenceNumber,
						transDate: transDate,
						payType: payType,
						cheque: '',
						bank: 0,
						t_remarks: '',
						csrf_test_name: $.cookie('csrf_cookie_name')
					},
					success: function (response) {
						$.ajax({
							type: 'POST',
							url: '<?php echo base_url() . 'hr/payroll/addDeduction' ?>',
							//dataType: 'json',
							data: {
								item_id: item_id,
								amount: amount,
								pc_code: pc_code,
								em_id: em_id,
								csrf_test_name: $.cookie('csrf_cookie_name')
							},
							success: function (response) {

							}

						});


					}

				});
				data = [];
			}
		});

	}


	function releasePayroll(em_id, netPay, pc_code, user_id)
	{
		var url = "<?php echo base_url() . 'hr/payroll/releasePayroll/' ?>"; // the script where you handle the form input.
		$.ajax({
			type: "POST",
			url: url,
			data: {
				em_id: em_id,
				netPay: netPay,
				pc_code: pc_code,
				csrf_test_name: $.cookie('csrf_cookie_name')
			},

			beforeSend: function () {
				//$('#consolidatedPayroll').html('<b class="text-center">Please Wait while Payroll is generating...</b>')
			},
			success: function (data)
			{
				$('#releaseBtn_' + user_id).removeClass('btn-danger');
				$('#releaseBtn_' + user_id).addClass('btn-success');
				$('#releaseBtn_' + user_id).attr('disabled', 'disabled');
				$('#releaseBtn_' + user_id).html('Released');

				$('#notificationAlert').show().fadeOut(5000)
				$('#notificationAlert').html('Successfully Saved!');
			}
		});

		return false; // avoid to execute the actual submit of the form
	}

	function approvePayroll(em_id, netPay, pc_code, user_id)
	{
		var url = "<?php echo base_url() . 'hr/payroll/approvePayroll/' ?>"; // the script where you handle the form input.
		$.ajax({
			type: "POST",
			url: url,
			data: {
				em_id: em_id,
				netPay: netPay,
				pc_code: pc_code,
				csrf_test_name: $.cookie('csrf_cookie_name')
			},

			beforeSend: function () {
				//$('#consolidatedPayroll').html('<b class="text-center">Please Wait while Payroll is generating...</b>')
			},
			success: function (data)
			{
				loanPayment(user_id, pc_code, em_id);
				saveDefaultDeduction(user_id, pc_code, em_id);
				$('#approveBtn_' + user_id).hide();
				$('#releaseBtn_' + user_id).show();

				$('#notificationAlert').show().fadeOut(5000)
				$('#notificationAlert').html('Successfully Approved!');
			}
		});

		return false; // avoid to execute the actual submit of the form
	}

	function getTotal()
	{
		var charges = $('#charges').val();
		var total = 0;
		for (var i = 1; i <= charges; i++)
		{
			$('.tdValue_' + i).each(function () {
				total += parseFloat($(this).attr('td_value'))
			})

			//alert(total);
			$('.total_' + i).text(numberWithCommas(Number(total.toFixed(1)).toFixed(2)))

			total = 0;

		}

	}

	function numberWithCommas(x) {
		if (x == null) {
			x = 0;
		}
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
</script>

