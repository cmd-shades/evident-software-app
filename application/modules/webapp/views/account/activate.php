<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Simply SID Ltd">
        <title><?php echo(!empty($page_title) ? $page_title : APP_NAME . ' - ' . APP_TAG_LINE); ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" media="screen"/>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/font-awesome.min.css') ?>" media="screen">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.settings.css') ?>" media="screen">
        <link rel="shortcut icon" href="<?php echo base_url(); ?>/public/favicon.ico">
        <style>
            
            body{
                background: url(<?php echo base_url('assets/images/backgrounds/ssid_background.jpg'); ?>) no-repeat center center fixed;
                background-size: cover;
            }
            
            a {
                color: #F89C1C;
                text-decoration: none;
            }
        
            .page-form{
                display: block; 
                padding:20px; 
                width:90%; 
                margin:0 auto;
            }
            
            .page-container{
                border:none;
                padding:25px 25px;
                border-radius: 10px;
                -webkit-border-radius: 10px;
                -moz-border-radius: 10px;
                background: rgb(248, 156, 28);
                /*background: #545E60;*/                
                color:#FFFFFF;
                width:100%;
                margin:25px auto;
            }
            
            .page-container .form-control {
                color:#F89C1C;
                line-height: 1.6;
                height:40px;
                border-radius: 0 !important;
                background: #6B6969;
                border: none !important;
                background: rgba(107, 105, 105, 0.6);
                
            }
                                
            .page-container .input-group-addon {
                background-color: #000;
                border: none !important;
                border-radius: 0 !important;
                border-right: none !important;
            }
            
            img.img-responsive{
                margin:0 auto;
            }
            
            .page-tag-line{
                text-align:center;
                font-style:italic;
            }
            
            .page-screen-logo{
                margin-bottom: 25px;
            }
            
            input{
                color:#F89C1C;
            }
            
            .app-store-container{
                padding:25px;               
            }
            
            .app-store-logo{
                /*padding:25px 25px;*/
                border-radius:3px;              
            }
            
            @media (max-width: 991px) {
                .app-store-logo{
                    margin-top:-15px;
                    margin-left:-10px;
                    margin-right:-10px;
                }
            }
            
            @media (min-width: 992px) {
                .app-store-logo{
                    margin-top:345px;
                }
            }
            
        </style>
    </head>
    
    <body class="nav-sm">
        <div class="container body">
            <div class="main_container">            
                <div class="row" style="margin-top:100px;">
                    <div class="col-md-6 col-md-offset-3">
                        <div class="page-screen-logo">
                            <img class="img-responsive" src="<?php echo base_url('assets/images/logos/ssid-logo.png'); ?>" alt="Simply SID Logo" title="Welcome to Simply SID">
                        </div>
                    </div>
                
                    <div class="col-md-6 col-md-offset-3">
                        <div class="page-container">
                            <?php if (!empty($activation_data->message)) { ?>
                                <div class="page-feedback-message text-center">
                                    <h4 style="font-weight:400" ><em class="text-<?php echo ($activation_data->status != 1) ? 'red' : '#fff'; ?>" ><?php echo $activation_data->message; ?></h4>          
                                </div>
                                <br/>
                            <?php } ?>
                            <a href="<?php echo base_url('webapp/user/login'); ?>" class="btn btn-grey btn-block">Web Portal Login</a>
                            <br/>
                            <a href="#" class="btn btn-grey btn-block">Contact Us</a>
                            <br/>
                        </div>                      
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

<!-- Load JQuery here -->
<script src="<?php echo base_url('assets/js/jquery.min.js'); ?>" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        $(".page-feedback-message").delay(6000).fadeOut(1500);      
    });
</script>
