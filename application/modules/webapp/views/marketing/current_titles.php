<?php
$get = ( ( $this->input->get( 'woj' ) !== false ) && ( $this->input->get( 'woj' ) == "cup" ) ) ? 'yes' : false ;
 ?>

<div class="row marketing-pdf">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">
			<div class="x_content">
				<legend>Marketing PDF</legend>
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 system-reports">
						<form id="generate_pdf" action="<?php echo base_url( 'webapp/marketing/generate_pdf' ) ?>" method="post">

							<div class="input-group form-group container-full">
								<label class="input-group">Select Product Type</label>
								<select id="product_types" name="product_name" class="form-control">
									<option value="">Please select</option>
									<?php if( !empty( $product_types ) ) { foreach( $product_types as $k => $pr_types ) { ?>
										<option data-product_id="<?php echo ( !empty( $pr_types->setting_id ) ) ? $pr_types->setting_id : '' ; ?>" value="<?php echo ( !empty( $pr_types->setting_value ) ) ? $pr_types->setting_value : '' ; ?>"><?php echo ( !empty( $pr_types->setting_value ) ) ? $pr_types->setting_value : '' ; ?></option>
									<?php } } ?>
								</select>
							</div>

							<div class="input-group form-group container-full">
								<label class="input-group">Select Territory</label>
								<select id="territory" name="territory_id" class="form-control">
									<option value="">Please select</option>
									<?php
									if( !empty( $territories ) ) { foreach( $territories as $k => $ter ) { ?>
										<option value="<?php echo ( !empty( $ter->territory_id ) ) ? $ter->territory_id : '' ; ?>">
											<?php echo ( !empty( $ter->country ) ) ? $ter->country : '' ; ?>
										</option>
									<?php
									} } ?>
								</select>
							</div>

							<div class="input-group form-group container-full">
								<label class="input-group">Select Provider</label>
								<?php
								if( !empty( $content_providers ) ){ 
									foreach( $content_providers as $k => $prov ) { ?>
										<div class="single_field" style="float: left; margin: 0 10px; width: calc(33% - 20px);">
											<input name="provider_id[]" id="<?php echo ( !empty( $prov->provider_id ) ) ? "provider_".$prov->provider_id : '' ; ?>" type="checkbox" value="<?php echo ( !empty( $prov->provider_id ) ) ? $prov->provider_id : '' ; ?>" /> 
											<label for="<?php echo ( !empty( $prov->provider_id ) ) ? "provider_".$prov->provider_id : '' ; ?>">&nbsp;<?php echo ( !empty( $prov->provider_name ) ) ? $prov->provider_name : '' ; ?></label>
										</div>
										<?php 
									}
								} ?>
							</div>

							<div class="input-group form-group container-full">
								<div class="row">
									<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
										<?php 
										if( isset( $get ) && strtolower( $get ) == 'yes' ){ ?>
											<button class="btn btn-sm btn-block btn-success" type="submit">Generate</button>
										<?php 
										} ?>
									</div>
								</div>
							</div>
						</form>
					</div>

					<div class="col-lg-offset-2 col-lg-6 col-md-offset-2 col-md-6 col-sm-12 col-xs-12 generated-reports el-hidden">
						<div class="input-group form-group container-full">
							<label class="input-group">Generated Materials</label>
						</div>

						<?php
						if( !empty( $marketing_materials ) ){
							foreach( $report_categories as $category ){
								if( $category->category_id == 1 ){ ?>
								<div class="x_panel tile group-container no-background">
									<h4 class="legend category-caller data-container" data-category_id="<?php echo $category->category_id; ?>">
										<i class="fas fa-caret-down"></i><?php echo ( !empty( $category->category_name ) ) ? ucwords( $category->category_name ) : '' ; ?>
									</h4>
									<div class="row group-content category-container" style="display: none;">
									</div>
								</div>
								<?php
								}
							}
						}	?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$( document ).ready( function(){
	<?php 
	if( !empty( $flash_message ) ){ ?>
		swal({
			title: "<?php echo html_escape( $flash_message ); ?>",
			// showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Ok'
		})
	<?php 
	} ?>
});
</script>