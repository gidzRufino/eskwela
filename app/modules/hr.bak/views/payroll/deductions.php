<div class="col-lg-6">
    <div class="control-group defaultDeductions" >
		<?php
		 $items = Modules::run('hr/payroll/getPayrollItems');
		 foreach ($items as $i):

			 $charges = Modules::run('hr/payroll/getPayrollChargesByItem', $i->pi_item_id, $pc_code, $employee_id);
			 if (!empty($charges->row())):
				 $amount = $charges->row()->pc_amount;
			 else:
				 if ($i->pi_item_id == 1):
					 $equivalent = Modules::run('hr/payroll/getSSSTableEquivalent', $grossPay);
					 $amount = ($equivalent != '' ? $equivalent : 0);
				 elseif ($i->pi_item_id == 2):
					 $amount = round($netPay * .01375, 2);
				 else:
					 $amount = ($i != NULL ? $i->pi_default : 0);
				 endif;
			 endif;
			 ?>
	 		<label class="control-label" for="firstRow"><?php echo $i->pi_item_name ?></label>
	 		<input oninput="calculateGross()" name="<?php echo preg_replace('/\s+/', '_', $i->pi_item_name); ?>" itemid="<?php echo $i->pi_item_id ?>" isDefault="<?php echo $i->pi_is_default ?>" td_id="td_<?php echo $user_id ?>_<?php echo $i->pi_item_id ?>" class="form-control text-center" type="text" value="<?php echo (!empty($amount)) ? $amount : '0'; ?>" id="<?php echo preg_replace('/\s+/', '_', $i->pi_item_name); ?>" />
	 		<input type="hidden" name="<?php echo preg_replace('/\s+/', '_', $i->pi_item_name); ?>_id" id="<?php echo preg_replace('/\s+/', '_', $i->pi_item_name); ?>_id" value="<?php echo $i->pi_item_id; ?>">
			 <?php
		 endforeach;
		?>
    </div>
</div>
<div class="col-lg-6">
    <div class="control-group">
		<?php
		 $personalLoanBalance = 0;
		 $loans = Modules::run('coopmanagement/loans/getLoanTypes');
		 foreach ($loans as $l):
			 $charges = Modules::run('hr/payroll/getPayrollChargesByItem', $l->clt_payroll_link, $pc_code, $employee_id);
			 if (!empty($charges->row())):
				 $personalLoanBalance = $charges->row()->pc_amount;
			 else:
				 $individualLoanList = Modules::run('coopmanagement/loans/getLoanList', $startDate, $endDate, $user_id, $l->clt_id, '0');
				 $personalLoanBalance = Modules::run('coopmanagement/loans/getPersonalLoanDue', $individualLoanList->row()->ld_account_num, $individualLoanList->row()->ld_ref_number, $startDate, $endDate, $l->clt_id, '0');
			 endif;
			 ?>
	 		<label class="control-label" for="inputDate"><?php echo $l->clt_type ?></label>
	 		<input oninput="calculateGross()" name="<?php echo preg_replace('/\s+/', '_', $l->clt_type); ?>" class="form-control text-center" type="text" value="<?php echo $personalLoanBalance ?>" id="<?php echo preg_replace('/\s+/', '_', $l->clt_type); ?>" />
	 		<input type="hidden" name="<?php echo preg_replace('/\s+/', '_', $l->clt_type); ?>_id" id="<?php echo preg_replace('/\s+/', '_', $l->clt_type); ?>_id" value="<?php echo $l->clt_payroll_link; ?>">
			 <?php
		 endforeach;
		?>
    </div>
</div>


<script type="text/javascript">
	function calculateGross() {
		var
				sss = parseFloat($('#SSS').val()),
				philHealth = parseFloat($("#PhilHealth").val()),
				pagIbig = parseFloat($("#Pag-ibig").val()),
				tax = parseFloat($("#Tax").val()),
				sssLoan = parseFloat($("#SSS_Loan").val()),
				pagIbigLoan = parseFloat($("#Pag-ibig_Loan").val()),
				shareLoan = parseFloat($("#Share_Loan").val()),
				riceLoan = parseFloat($("#Rice_Loan").val()),
				emergencyLoan = parseFloat($("#Emergency_Loan").val()),
				cashAdvance = parseFloat($("#Cash_Advance_Loan").val()),
				pettyCash = parseFloat($("#Petty_Cash_Loan").val()),
				grossPay = parseFloat($("#grossPay").html()),
				deduction = sss + philHealth + pagIbig + tax + sssLoan + pagIbigLoan + shareLoan + riceLoan + emergencyLoan + cashAdvance + pettyCash,
				netPay = grossPay - deduction;
		$("#netPay").html(netPay.toFixed(2));
	}
	function addDeduction()
	{
		var data = $("#updateDeductionForm").serialize();
		var url = '<?php echo base_url('hr/payroll/updateDeduction/'); ?>';
		$.ajax({
			type: 'POST',
			url: url,
			data: data + '&csrf_test_name=' + $.cookie('csrf_cookie_name'),
			success: function (data, result, xhr) {
				generatePayroll(<?php echo $pc_code; ?>);
			}
		});
	}

	function sanctions() {
	}
</script>    