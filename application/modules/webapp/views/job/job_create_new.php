<div>
    <legend>Create New Job</legend>
    <form id="job-creation-form" method="post" >
        <input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
        <input type="hidden"  name="page" value="details"/>
        <div class="rows">
            Content coming soon...
        </div>
    </form>
</div>

<script>
    $(document).ready(function(){
        
        //Address lookup
        $('.postcode-lookup').change(function(){
            var postCode = $(this).val();
            if( postCode.length > 0 ){
                $.post("<?php echo base_url("webapp/job/get_addresses_by_postcode"); ?>",{postcodes:postCode},function(result){
                    $("#address-lookup-result").html(result["addresses_list"]);             
                },"json");
            }
        });
        
        //Package manipulations
        $('.addons').change(function(){
            $('.sports-addon').click(function() {
                $('.sports-addon').not(this).prop('checked', false);
            });
            
            $('.movies-addon').click(function() {
                $('.movies-addon').not(this).prop('checked', false);
            });
        });

        $(".job-creation-steps").click(function(){
            var currentpanel = $(this).data("currentpanel");
            panelchange("."+currentpanel)   
            return false;
        }); 
        
        $(".btn-back").click(function(){
            var currentpanel = $(this).data("currentpanel");
            go_back("."+currentpanel)   
            return false;
        }); 
        
        function panelchange(changefrom){
            var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
            var changeto = ".job_creation_panel"+panelnumber;
            $(changefrom).hide( "slide", {direction : 'left'}, 500);
            $(changeto).delay(600).show( "slide", {direction : 'right'},500);   
            return false;   
        }
        
        function go_back( changefrom ){
            var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
            var changeto = ".job_creation_panel"+panelnumber;
            $(changefrom).hide( "slide", {direction : 'right'}, 500);
            $(changeto).delay(600).show( "slide", {direction : 'left'},500);    
            return false;   
        }
        
        //Submit job form
        $( '#create-job-btn' ).click(function( e ){
            e.preventDefault();
            var formData = $('#job-creation-form').serialize();
            
            swal({
                title: 'Confirm new job creation?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function (result) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/job/create_job/'); ?>",
                        method:"POST",
                        data:formData,
                        dataType: 'json',
                        success:function(data){
                            if( data.status == 1 && ( data.job !== '' ) ){
                                
                                var newSiteId = data.job.job.job_id;
                                
                                swal({
                                    type: 'success',
                                    title: data.status_msg,
                                    showConfirmButton: false,
                                    timer: 3000
                                })
                                window.setTimeout(function(){ 
                                    location.href = "<?php echo base_url('webapp/job/profile/'); ?>"+newSiteId;
                                } ,3000);                           
                            }else{
                                swal({
                                    type: 'error',
                                    title: data.status_msg
                                })
                            }       
                        }
                    });
                }
            }).catch(swal.noop)
        });
        
    });
</script>