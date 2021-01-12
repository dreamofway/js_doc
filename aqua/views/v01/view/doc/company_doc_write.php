<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    기업 문서 <?=$page_work?>
                    <button type="button" class="pull-right btn btn-inverse waves-effect w-md m-l-5" onclick="location.href='./<?=$page_name?>_list?page=<?=$page?><?=$params?>'">목록</button> 
                    <?php
                        if($mode == 'edit' ) {
                    ?>
                    <button type="button" class="pull-right btn btn-danger waves-effect w-md m-l-5" onclick="delProc()">삭제</button> 
                    <?php
                        }
                    ?>
                </h1>                
            </section>

            <form class="form-horizontal" role="form" method="post" id="form_write" enctype="multipart/form-data"  action="./company_doc_proc" >                
                <input type="hidden" name="mode" id="mode" value="<?=$mode?>" />
                <input type="hidden" name="doc_usage_idx" id="doc_usage_idx" value="<?=$doc_usage_idx?>" />
                <input type="hidden" name="page" value="<?=$page?>" />
                <input type="hidden" name="top_code" value="<?=$top_code?>" />
                <input type="hidden" name="left_code" value="<?=$left_code?>" />
                <input type="hidden" name="ref_params" value="<?=$params?>" />                
                <input type="hidden" name="return_page" value="<?=$page_name?>" />
                <input type="hidden" name="doc_data" id="doc_data" value="<?=$doc_data?>" />                
                <input type="hidden" name="doc_table_style_data" id="doc_table_style_data" value="<?=$doc_table_style_data?>" />                


            <!-- 문서  -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        
                        <div class="table-responsive m-b-0">
                            <h5 class="header-title m-b-10">
                                <b>생성</b> 								
                            </h5>
                            <hr class="m-t-0">
                            <table class="table table-bordered text-left">
                                <tbody>

                                    <tr>
                                        <th class="info middle-align">문서 분류</th>
                                        <td colspan="3" >                                            
                                            
											<?=($doc_type == 'HACCP' ? 'HACCP' : '일반' )?> > <?=$item_title?>

                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">문서명</th>
                                        <td colspan="3" >                                            
                                            <input class="form-control" type="text" id="doc_title" name="doc_title" value="<?=$doc_title?>" style="width:50% !important;" data-valid="blank" />
                                        </td>
                                    </tr>
                                    

                                    <tr>
                                        <th class="info middle-align">사용여부</th>
                                        <td colspan="3">

                                            <select class="form-control" name="use_flag" id="use_flag" style="width:200px" >
                                               
                                                <option value="Y" <?=($use_flag == 'Y' ? 'selected="selected"' : '' )?> >사용</option>
                                                <option value="N" <?=($use_flag == 'N' ? 'selected="selected"' : '' )?> >미사용</option>
                                                
                                            </select>

                                        </td>
                                    </tr>
                                    <?php
                                        if( $mode == 'ins') {
                                    ?>
                                    <tr>
                                        <th class="info middle-align">문서양식변경</th>
                                        <td colspan="3">
                                            <button type="button" class="pull-left btn btn-sm btn-success waves-effect w-md m-l-5 _change_form" style="top:-4px;" onclick="changeFrom(this, 'v')" >세로</button>
                                            <button type="button" class="pull-left btn btn-sm btn-default waves-effect w-md m-l-5 _change_form" style="top:-4px;" onclick="changeFrom(this, 'h')" >가로</button>
                                            <button type="button" class="pull-left btn btn-sm btn-purple waves-effect w-md m-l-5" style="top:-4px;" onclick="changeSetRowCol()" >행렬설정</button>
                                                
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    ?>


                                </tbody>
                            </table>

                            <div id="table_create_area" >
                                
                            </div>

                        </div> 

                    </div>

                </div>
            </div>
            <!-- //문서 -->
          
           
            </form>

            <div class="row"> 
                <div class="col-lg-12">                    
                    <button type="button" class="pull-right btn btn-primary waves-effect w-md m-l-5" onclick="register()">저장</button>
                    <button type="button" class="pull-right btn btn-inverse waves-effect w-md m-l-5" onclick="location.href='./<?=$page_name?>_list?page=<?=$page?><?=$params?>'">목록</button>                     
               </div>
            </div>


        </div> <!-- // container -->
    </div> <!-- // content -->
</div> <!-- // content-page -->


<style>    
    .table>tbody>tr>th.info {
        width: 15%;
    }
    /* table input {
        width: 40% !important; display: inline-block !important;
    } */

    .doc_section {
        margin: 0px auto;
        padding: 1cm;
        width: 23cm;
        min-height: 29.7cm;
        background: #fff;
        box-sizing: border-box;
        border:1px solid
    }

</style>
<script type="text/javascript" src="<?=$aqua_view_path;?>/public/js/view.form.valid.js"></script>
<script type="text/javascript" src="<?=$aqua_view_path;?>/public/js/imasic.doc.js"></script>
<script>
    
    /**
     * 삭제 버튼 동작
     */
    function delProc(){
        if(confirm('현재 게시물을 삭제하시겠습니까?') == true ){
            $('#mode').val('del');
            $('#form_write').submit();
        }
    }

    /**
     * 저장 버튼 동작
     */
    function register(){

        doc.renewDocJson();
        
        $('#form_write').submit();

    }

    var glo_doc_table_width = 800;
    
    function changeSetRowCol() {
        doc.init({
            work : 'new'
            , element : 'table_create_area'
            , class_obj : 'doc'
            , return_element_id : 'doc_data'
            , table_width : glo_doc_table_width
        });
    }

    function changeFrom( arg_this, arg_type ) {

        if( confirm('양식을 변경하시겠습니까?\n현재까지 작성된 데이터는 복구가 불가능합니다.') ) {

            $('._change_form').removeClass('btn-success');
            $('._change_form').removeClass('btn-default');

            $('._change_form').addClass('btn-default');
            $('._change_form').addClass('btn-default');

            if( arg_type == 'v') {
                //# 세로
                $('._change_form').removeClass('btn-default');
                $(arg_this).addClass('btn-success');
                
                glo_doc_table_width = 800;                                
                doc.init({
                    work : 'new'
                    , element : 'table_create_area'
                    , class_obj : 'doc'
                    , return_element_id : 'doc_data'
                    , table_width : 800
                    , init_matrix : {
                        init_rows : 20
                        , init_cols : 10
                    }
                });
                
            } else {
                //# 가로
                
                $('._change_form').removeClass('btn-default');
                $(arg_this).addClass('btn-success');

                glo_doc_table_width = 1200;
                
                doc.init({
                    work : 'new'
                    , element : 'table_create_area'
                    , class_obj : 'doc'
                    , return_element_id : 'doc_data'
                    , table_width : 1200
                    , init_matrix : {
                        init_rows : 10
                        , init_cols : 20
                    }
                });
                
                
            }
            
        }
        
    }


    if( $('#mode').val() == 'ins' ) {

        doc.init({
            work : 'new'
            , element : 'table_create_area'
            , class_obj : 'doc'
            , return_element_id : 'doc_data'
            , return_table_id : 'doc_table_style_data'
            , init_matrix : {
                init_rows : 20
                , init_cols : 10
            }
        });

    } else {
        
        doc.init({
            work : 'form_edit'
            , element : 'table_create_area'
            , class_obj : 'doc'
            , return_element_id : 'doc_data'
            , return_table_id : 'doc_table_style_data'
            , table_data : $('#doc_table_style_data').val()
            , data : $('#doc_data').val()
        });

    }
	

</script>

            
