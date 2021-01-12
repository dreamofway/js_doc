<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="subject" content="<?=$ogp_title?>" /> 
        <meta name="keywords" content="<?=$meta_keywords?>" /> 
        <meta name="description" content="<?=$meta_description?>" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="<?=$ogp_title?>" />
        <meta property="og:stitle_name" content="<?=$ogp_stitle_name?>" />	 
        <meta property="og:url" content="<?=$ogp_url?>" />
        <meta property="og:image" content="<?=$ogp_image?>" />
        <meta property="og:description"  content="<?=$ogp_description?>" />
        <?php
            if( $favicon_path ){
        ?>
        <link rel="shortcut icon" href="<?=$favicon_path?>" />
        <?php
            }
        ?>
        <title><?=$meta_title?></title>
        
        <!-- App CSS -->
        <link rel="stylesheet" type="text/css" href="<?=$aqua_view_path;?>/public/css/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" href="<?=$aqua_view_path;?>/public/plugins/datatables/dataTables.bootstrap.css">
        <link rel="stylesheet" type="text/css" href="<?=$aqua_view_path;?>/public/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css">
        <link rel="stylesheet" type="text/css" href="<?=$aqua_view_path;?>/public/css/core.css" />
        <link rel="stylesheet" type="text/css" href="<?=$aqua_view_path;?>/public/css/components.css"  />
        <link rel="stylesheet" type="text/css" href="<?=$aqua_view_path;?>/public/css/icons.css"  />
        <link rel="stylesheet" type="text/css" href="<?=$aqua_view_path;?>/public/css/pages.css" />
        <link rel="stylesheet" type="text/css" href="<?=$aqua_view_path;?>/public/css/menu.css" />
        <link rel="stylesheet" type="text/css" href="<?=$aqua_view_path;?>/public/css/responsive.css" />
        <link rel="stylesheet" type="text/css" href="<?=$aqua_view_path;?>/public/css/admin_dev.css" />
        <link rel="stylesheet" type="text/css" href="<?=$aqua_view_path;?>/public/css/_lee.css" />
        
        
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <script src="<?=$aqua_view_path;?>/public/js/jquery.min.js"></script>
        
        
    </head>
    <body class="fixed-left">

        <!-- Top Bar Start -->
        <?php 
            if( $use_top == true ) {

                include_once( $this->getViewPhysicalPath( $top_path ) );
                
            }

            if( $use_left == true ) {
                include_once( $this->getViewPhysicalPath( $left_menu_path )  );
            }
            
        ?>
        <!-- Top Bar End -->        
        <?php
            include_once( $contents_path );
        ?>

        <?php 
            if( $use_footer == true ) {

                include_once( $this->getViewPhysicalPath( $footer_path ) );
            
            }
        ?>

        
        
        <script src="<?=$aqua_view_path;?>/public/js/commfunc.js"></script>
        <script src="<?=$aqua_view_path;?>/public/js/sweetalert.min.js"></script>
        <script src="<?=$aqua_view_path;?>/public/js/modernizr.min.js"></script>
        <script src="<?=$aqua_view_path;?>/public/js/jquery.form.js"></script>
        <script src="<?=$aqua_view_path;?>/public/js/lee.lib.js"></script>
        <script src="<?=$aqua_view_path;?>/public/js/template/jquery.tmpl.min.js"></script>
        <script src="<?=$aqua_view_path;?>/public/js/template/jquery.tmplPlus.min.js"></script>

        <script src="<?=$aqua_view_path;?>/public/js/bootstrap.min.js"></script>
        <script src="<?=$aqua_view_path;?>/public/js/detect.js"></script>
        <script src="<?=$aqua_view_path;?>/public/js/fastclick.js"></script>
        <script src="<?=$aqua_view_path;?>/public/js/jquery.slimscroll.js"></script>
        <script src="<?=$aqua_view_path;?>/public/js/jquery.blockUI.js"></script>
        <script src="<?=$aqua_view_path;?>/public/js/waves.js"></script>
        <script src="<?=$aqua_view_path;?>/public/js/jquery.nicescroll.js"></script>
        <script src="<?=$aqua_view_path;?>/public/js/jquery.scrollTo.min.js"></script>

        <!-- Plugins Js -->        
        <script src="<?=$aqua_view_path;?>/public/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <script src="<?=$aqua_view_path;?>/public/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.kr.js"></script>        
        <script src="<?=$aqua_view_path;?>/public/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="<?=$aqua_view_path;?>/public/plugins/datatables/dataTables.bootstrap.min.js"></script>

        <script type="text/javascript">
            
            $(function () {
                
                $('.datepicker').datepicker({
                    calendarWeeks: false,
                    todayHighlight: true,
                    autoclose: true,
                    toggleActive: true,
                    format: "yyyy-mm-dd",
                    language: "kr",
                    clearBtn: true
                });

                $('#table-pageing').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": false,
                    "ordering": true,
                    "info": false,
                    "autoWidth": false
                });
                
            });

        </script>


    </body>
</html>