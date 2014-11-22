
<div class='shop_discounts_popup' style='padding:10px;margin:10px;'>

				<div class="discounts_message_area">

				</div>
				<fieldset>
						<div class="value">
							<h3>Create New Discount</h3>
						</div>
						<table>
							<tr>
								<td>User Group</td>
								<td>Percentage</td>
							</tr>
							<tr>
								<td>
									<?php if(isset($pyroUserGroups)): ?>
										<?php echo form_dropdown('form_groups',$pyroUserGroups); ?>
									<?php endif; ?>
								</td>
								<td>
									<select name="discountValues">
										<?php for($i=0;$i<101;$i++):?>
												<option value="<?php echo $i;?>"><?php echo $i; ?></option>
										<?php endfor;?>
									</select>
								</td>
							</tr>
							<tr>
								<td>
									<div class="value">
										Min QTY<br />
										<?php echo form_input('form_min_qty',NULL,'placeholder="Minimum purchase Qty"'); ?>
									</div>
								</td>
								<td></td>
							</tr>
							<tr>

								<td>
									<a class="addDiscount btn orange">Add</a>
								</td>
								<td></td>
							</tr>
						</table>

				</fieldset>

</div>
