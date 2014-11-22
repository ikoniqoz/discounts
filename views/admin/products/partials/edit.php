
<div class='shop_discounts_popup' style='padding:10px;margin:10px;'>

				<div class="discounts_message_area">

				</div>
				<input type='hidden' name='discount_id' value='<?php echo $discount->id;?>' />
				<fieldset>
						<div class="value">
							<h3>Edit Discount</h3>
						</div>
						<table>
							<tr>
								<td>User Group</td>
								<td>Percentage</td>
							</tr>
							<tr>
								<td>
									<?php if(isset($pyroUserGroups)): ?>
										<?php echo form_dropdown('form_groups',$pyroUserGroups,$discount->group_id); ?>
									<?php endif; ?>
								</td>
								<td>
									<?php echo form_dropdown('discountValues',$percent_list, $discount->value); ?>
								</td>
							</tr>
							<tr>
								<td>
									<div class="value">
										Min QTY<br />
										<?php echo form_input('form_min_qty',$discount->min_qty,'placeholder="Minimum purchase Qty"'); ?>
									</div>
								</td>
								<td></td>
							</tr>
							<tr>

								<td>
									<a class="saveEditDiscount btn orange">Save</a>
								</td>
								<td></td>
							</tr>
						</table>

				</fieldset>

</div>
