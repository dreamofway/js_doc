<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    메뉴설정
                </h1>                   
            </section>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <h5 class="header-title m-t-0">
                            <b>메뉴 현황</b>
                        </h5>
                        <hr class="m-t-0">
                    
                        <div class="set_menu_container" >
                            <div class="set_menu_box_area">
                                <div class="set_menu_box" >

                                    <div class="set_menu_item" >
                                        <div class="set_menu_root" >
                                            <div class="set_menu_move_box" ><span class="glyphicon glyphicon-resize-vertical" ><span></div>
                                            <div class="set_menu_title" >대메뉴</div>
                                            <div class="set_menu_setting_box"><span class="glyphicon glyphicon-option-horizontal" ><span></div>
                                        </div>
                                        <div class="set_menu_sub_box" >

                                            <div class="set_menu_sub_item" >
                                                <div class="set_menu_move_box" ><span class="glyphicon glyphicon-resize-vertical" ><span></div>
                                                <div class="set_menu_title" >서브메뉴</div>
                                                <div class="set_menu_setting_box"><span class="glyphicon glyphicon-option-horizontal" ><span></div>
                                            </div>

                                            <div class="set_menu_sub_item" >
                                                <div class="set_menu_move_box" ><span class="glyphicon glyphicon-resize-vertical" ><span></div>
                                                <div class="set_menu_title" >서브메뉴</div>
                                                <div class="set_menu_setting_box"><span class="glyphicon glyphicon-option-horizontal" ><span></div>
                                            </div>
                                            <div class="set_menu_sub_item" >
                                                <div class="set_menu_move_box" ><span class="glyphicon glyphicon-resize-vertical" ><span></div>
                                                <div class="set_menu_title" >서브메뉴</div>
                                                <div class="set_menu_setting_box"><span class="glyphicon glyphicon-option-horizontal" ><span></div>
                                            </div>
                                            <div class="set_menu_sub_item" >
                                                <div class="set_menu_move_box" ><span class="glyphicon glyphicon-resize-vertical" ><span></div>
                                                <div class="set_menu_title" >서브메뉴</div>
                                                <div class="set_menu_setting_box"><span class="glyphicon glyphicon-option-horizontal" ><span></div>
                                            </div>
                                            
                                        </div>

                                        <div class="set_menu_sub_add" >                                                
                                            <div class="set_menu_title" ><span class="glyphicon glyphicon-plus" ><span>추가</div>                                                
                                        </div>

                                    </div>

                                </div> <!-- // set_menu_box -->
                            </div>

                            <div class="set_menu_root_add" >                                                
                                <div class="set_menu_title" ><span class="glyphicon glyphicon-plus" ><span>추가</div>                                                
                            </div>
                            
                        </div>
                    
                     
                    </div>                       
                </div>
            </div><!-- end row --> 

        </div> <!-- // container -->
    </div> <!-- // content -->
</div> <!-- // content-page -->
<style>
    .set_menu_container {
        width:100%;
    }
    .set_menu_box_area {
        width:50%;
    }
    .set_menu_box {
        width:100%;
    }
    .set_menu_item {
        width: 100%;
        margin-top: 10px;
        margin-left: 10px;
        padding-bottom: 10px;                
    }
    .set_menu_item:hover {
        box-shadow:0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12);
	    transition: box-shadow 100ms cubic-bezier(0.4, 0.0, 0.2, 1);
    }
    .set_menu_root {
        width: 100%;
        height: 40px;        
        padding-top: 15px;
        
    }
    .set_menu_title {
        display: table-cell;
        width:100%;
        font-weight: bold;
        font-size: 16px;
        padding-left:10px;
    }
    .set_menu_move_box {
        display: table-cell;
        width:8%;
        text-align:center;
        border-right:1px solid #eee;
        cursor:move;
        visibility:hidden;
    }
    .set_menu_setting_box {
        display: table-cell;
        width:4%;
        text-align:center;
        border-left:1px solid #eee;
        padding-left:10px;
        padding-right:10px;
        cursor:pointer;
        visibility:hidden;
    }
    .set_menu_sub_box {
        margin-left:40px;
        /* padding-bottom: 10px; */
    }
    .set_menu_sub_item {        
        height: 40px;
        margin:10px;
        line-height: 2.5;        
    }
    .set_menu_sub_item:hover {        
        box-shadow:0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12);
	    transition: box-shadow 100ms cubic-bezier(0.4, 0.0, 0.2, 1);
    }
    .set_menu_root_add {
        width:50%;
        height: 40px;
        margin:10px;
        line-height: 2.5;        
        cursor:pointer;
    }
    .set_menu_root_add:hover {
        box-shadow:0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12);
	    transition: box-shadow 100ms cubic-bezier(0.4, 0.0, 0.2, 1);
    }
    .set_menu_sub_add {
        height: 40px;
        margin:10px 10px 10px 100px;
        line-height: 2.5;        
        cursor:pointer;
    }
    .set_menu_sub_add:hover {
        box-shadow:0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12);
	    transition: box-shadow 100ms cubic-bezier(0.4, 0.0, 0.2, 1);
    }

    .set_menu_root_add .set_menu_title {
        color: #b9b8b8;
    }

    .set_menu_sub_add .set_menu_title {
        color: #b9b8b8;
    }
</style>

<script>

    $('.set_menu_item').mouseover(function(){        
        $(this).find('.set_menu_root > .set_menu_move_box').css({'visibility':'visible'});
        $(this).find('.set_menu_root > .set_menu_setting_box').css({'visibility':'visible'});
    });

    $('.set_menu_item').mouseout(function(){        
        $(this).find('.set_menu_root > .set_menu_move_box').css({'visibility':'hidden'});
        $(this).find('.set_menu_root > .set_menu_setting_box').css({'visibility':'hidden'});
    });

    $('.set_menu_sub_item').mouseover(function(){        
        $(this).find('.set_menu_move_box').css({'visibility':'visible'});
        $(this).find('.set_menu_setting_box').css({'visibility':'visible'});
    });

    $('.set_menu_sub_item').mouseout(function(){        
        $(this).find('.set_menu_move_box').css({'visibility':'hidden'});
        $(this).find('.set_menu_setting_box').css({'visibility':'hidden'});
    });

</script>
            
