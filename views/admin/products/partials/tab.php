      <?php if(!group_has_role('shop_discounts','admin_manage'))
      {
          $userDisplayMode = 'view';
          echo "<fieldset><h3 style='color:#f00'>You do not have permission to manage discounts.</h3></fieldset>";
      }
      ?>

				<fieldset>

						<h3>
							Discounts are applied by Group to each individual product.
							A group can have multiple discounts, based on the min purchase of this product.
							<a href="admin/shop/product/edit/<?php echo $id;?>#discounts-tab">Set open tab</a>
						</h3>

						<?php if($userDisplayMode == 'edit'):?>
							<a class="sbtn glow modal" href="admin/shop_discounts/discounts/addpop/<?php echo $id;?>">Add discount</a>
						<?php endif;?>

				</fieldset>
				<fieldset>
					<h3>Discounts assigned to this Product</h3>
					<?php if(isset($modules['shop_discounts']['groups'])): ?>
						<?php if(isset($modules['shop_discounts']['discounts'])): ?>
								<table class="shop_discounts_list">
									<tr>
										<th>ID</th>
										<th>Group</th>
										<th>Discount</th>
										<th>Min Qty</th>
										<th></th>
									</tr>
									<?php foreach($modules['shop_discounts']['discounts'] AS $discount): ?>
										<tr discount-id='<?php echo $discount->id; ?>'>
											<td><?php echo $discount->id; ?></td>
											<td><?php echo $modules['shop_discounts']['groups'][$discount->group_id];?></td>
											<td><?php echo $discount->value; ?> % </td>
											<td><?php echo $discount->min_qty; ?> </td>
											<td><span style='float:right'>
												<?php if($userDisplayMode == 'edit'):?>
													<a href="admin/shop_discounts/product/edit/<?php echo $id;?>/<?php echo $discount->id;?>" class='modal editDiscount button blue'>Edit</a>
													<a href="#" class='delDiscount button red delete_button'>Delete</a>
												<?php endif;?>
											</span></td>
										</tr>
									<?php endforeach; ?>
								</table>
						<?php endif; ?>
					<?php endif; ?>

				</fieldset>


<?php if($userDisplayMode == 'edit'):?>

	<script>


	function removeDiscountLine(line)
	{
		line.fadeTo("slow", 0.1);
		setTimeout(function() {
		  	line.delay(3000).remove();
        	mymessage= "Success";
		}, 2000);
	}


	function getDiscountLineString(obj,mygroup_name)
	{
		var str  ="<tr discount-id='"+obj.id+"'>";
		   str += "		<td>"+ obj.id +"</td>";
		   str += "		<td>"+ mygroup_name +"</td>";
		   str += "		<td>"+ obj.value +" %</td>";
		   str += "		<td>"+ obj.min_qty +"</td>";
		   str += "		<td>";
		   str += "			<span style='float:right'>";
		   str += "				<a href='admin/shop_discounts/product/edit/"+obj.product_id+"/"+obj.id+"' class='modal editDiscount button blue'>Edit</a>";
		   str += "				<a href='#' class='delDiscount button red delete_button'>Delete</a></span>";	
		   str += "			</span>";	
		   str += "		</td>";				   		   		   	   		   
		   str += "</tr>";		   
		return str;

	}
	function addDiscount(obj,mygroup_name)
	{
		$('.shop_discounts_popup').html('Discount has been added.');
		var str =  getDiscountLineString(obj,mygroup_name);
		$('table.shop_discounts_list tbody').append(str);

	}
	function replaceLine(obj,mygroup_name)
	{
		var str = getDiscountLineString(obj,mygroup_name);

		$("table.shop_discounts_list tr[discount-id='"+obj.id+"']").replaceWith( str );
	}

	$(function() {


		$(document).on('click', '.saveEditDiscount', function(event) {
		        var group_select = $("select[name='form_groups']");
		        var disc_select = $("select[name='discountValues']");
		        var min_qty_val = $("input[name='form_min_qty']").val();
		        var select_group_name = $("select[name='form_groups'] option:selected").text();
		        var discount_id = $("input[name='discount_id']").val(); 


	            var url = "<?php echo site_url();?>admin/shop_discounts/product/save/<?php echo $id;?>/";
	            var senddata =
	            {
	            	disc_id:discount_id,
	            	product_id:<?php echo $id;?>,
					min_qty:min_qty_val,
					group_id:group_select.val(),
					modifier:0,
					value:disc_select.val()
	            };

	            $.post(url,senddata).done(function(data)
	            {

		        	var mymessage = '';
	                var obj = jQuery.parseJSON(data);

	                if(obj.status == 'success')
	                {
	                	replaceLine(obj,select_group_name)
	                	mymessage = 'Edit successful';
	                }
	                else
	                {
	                	alert("Failed:"+ obj.message);
	                	mymessage = obj.message;
	                }

	                $(".discounts_message_area").html(mymessage);

	            });

				

		        // Prevent Navigation
		        event.preventDefault();

		  });



		//http://stackoverflow.com/questions/16362778/running-jquery-function-on-dynamically-created-content
		//$( ".addDiscount" ).on('click',function() {
		//$(document).on('click', '.addDiscount', function() {

		$(document).on('click', '.addDiscount', function(event) {
		        var mymessage = '';
		        var group_select = $("select[name='form_groups']");
		        var disc_select = $("select[name='discountValues']");
		        var min_qty_val = $("input[name='form_min_qty']").val();
		        var select_group_name = $("select[name='form_groups'] option:selected").text();


	            var url = "<?php echo site_url();?>admin/shop_discounts/discounts/add/<?php echo $id;?>/";
	            var senddata =
	            {
	            	product_id:<?php echo $id;?>,
					min_qty:min_qty_val,
					group_id:group_select.val(),
					modifier:0,
					value:disc_select.val()
	            };

	            $.post(url,senddata).done(function(data)
	            {

	                var obj = jQuery.parseJSON(data);

	                if(obj.status == 'success')
	                {
	                	addDiscount(obj,select_group_name);
	                }
	                else
	                {
	                	alert("Failed:"+ obj.message);
	                }

	            });

				$(".discounts_message_area").html(mymessage);

		        // Prevent Navigation
		        event.preventDefault();

		  });



		//$( ".delDiscount" ).click(function( event ) { //does not work with dynamic
		$(document).on('click', '.delDiscount', function(event) {	//this works much better

				//rtather have the object than parent.parent!
		        var options = $(this).parent().parent().parent();

		        //var options = $('tr[discount-id="'+obj.disc_id+'"]');
    							//$('tr[option-id="'+obj.id+'"]').replaceWith(str);		        

		        //Warn about delete
		        if(confirm("Are you sure you want to remove this Discount ? "))
		        {
		            var url = "<?php echo site_url();?>admin/shop_discounts/discounts/remove/" + options.attr("discount-id");
					var mymessage = '';
		            $.post(url).done(function(data)
		            {

		                var obj = jQuery.parseJSON(data);

		                if(obj.status == 'success')
		                {
		                	removeDiscountLine(options);
		                }
		                else
		                {
		                	 alert("Failed");
		                }

		            });

					$(".discounts_message_area").html(mymessage);
		            $(".discounts_message_area").delay(2000).fadeTo("slow", 0.6).remove();


		        }

		        // Prevent Navigation
		        event.preventDefault();


		  });


		}); //end query
	</script>
<?php endif; ?>