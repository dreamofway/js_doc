<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    서비스 계약관리  > 계약 정보 <?=$page_work?>
                    <button type="button" class="pull-right btn btn-inverse waves-effect w-md m-l-5" onclick="location.href='./<?=$page_name?>_list?page=<?=$page?><?=$params?>'">목록</button> 
                </h1>                
            </section>

            <form class="form-horizontal" role="form" method="post" id="form_write" enctype="multipart/form-data"  action="./<?=$page_name?>_proc" >                
                <input type="hidden" name="mode" value="<?=$mode?>" />
                <input type="hidden" name="idx" value="<?=$idx?>" />
                <input type="hidden" name="company_idx" id="company_idx" value="<?=$company_idx?>" />
                <input type="hidden" name="page" value="<?=$page?>" />
                <input type="hidden" name="top_code" value="<?=$top_code?>" />
                <input type="hidden" name="left_code" value="<?=$left_code?>" />
                <input type="hidden" name="ref_params" value="<?=$params?>" />
                <input type="hidden" name="file_idx" value="<?=$file_idx?>" />

            <!-- 기업정보 -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        
                        <div class="table-responsive m-b-0">
                            <h5 class="header-title m-b-10">
                                <b>기업정보</b>    
                                <button type="button" class="pull-right btn btn-sm btn-purple waves-effect w-md m-l-5" style="top:-4px;" onclick="changeCompany()">기업선택</button>                
                            </h5>
                            <hr class="m-t-0">
                            <table class="table table-bordered text-left">
                                <tbody>                                    
                                    <tr>
                                        <th class="info middle-align">회사명</th>
                                        <td colspan="3" id="company_name" >
                                            <?=$company_name?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="info middle-align">사업자등록번호</th>
                                        <td colspan="3" id="registration_no" >
                                            <?=$registration_no?>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th class="info middle-align">대표명</th>
                                        <td colspan="3" id="ceo_name" ><?=$ceo_name?></td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">전화번호</th>
                                        <td colspan="3" id="company_tel" ><?=$company_tel?></td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">담당자명</th>
                                        <td colspan="3" id="partner_name" ><?=$partner_name?></td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">담당자 휴대폰 번호</th>
                                        <td colspan="3" id="partner_phone_no" ><?=$partner_phone_no?></td>
                                    </tr>

                          
                                </tbody>
                            </table>
                        </div> 

                    </div>

                </div>
            </div>
            <!-- //기업정보 -->

            <!-- 서비스 정보 -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        
                        <div class="table-responsive m-b-0">
                            <h5 class="header-title m-b-10">
                                <b>계약 정보</b>    
                            </h5>
                            <hr class="m-t-0">
                            <table class="table table-bordered text-left">
                                <tbody> 
                                    <tr>
                                        <th class="info middle-align">서비스</th>
                                        <td colspan="3" >                                            
                                            <?php                                        
                                                if( count( $service_codes ) > 0 ) {
                                            ?>
                                            <select class="form-control" name="service_code" id="service_code" style="width:200px" data-valid="blank">
                                                <option value="" >선택</option>
                                                <?php
                                                    foreach( $service_codes AS $item ) {
                                                ?>
                                                <option value="<?=$item['service_code']?>" <?=($service_code == $item['service_code'] ? 'selected="selected"' : '' )?> ><?=$item['service_name']?></option>
                                                <?php
                                                    }
                                                ?>
                                            </select>
                                            <?php
                                                }
                                            ?>   
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="info middle-align">기간</th>
                                        <td colspan="3" >                                            
                                            <div class="form-group">                                            
                                                <div class="col-sm-4">
                                                    <div class="input-daterange input-group" >
                                                        <input type="text" class="form-control datepicker " name="start_date" id="end_date" value="<?=dateType( $start_date, 6 )?>" data-valid="blank" readonly="readonly" >
                                                        <span class="input-group-addon bg-primary b-0 text-white">~</span>
                                                        <input type="text" class="form-control datepicker " name="end_date" id="end_date" value="<?=dateType($end_date, 6 )?>" data-valid="blank" readonly="readonly" >
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="info middle-align">계약서</th>
                                        <td colspan="3" >                                            
                                            <div class="form-group">                                                
                                                <div class="upload-btn-wrapper">
                                                    <button type="button" class="btn btn-primary">업로드</button>
                                                    <input type="file" name="contract_file" onchange="readFile(this, 'contract_file');">                                                    
                                                    <code class="control-label m-l-10 contract_file"></code>
                                                    
                                                </div>

                                                <?php
                                                    if( isset( $file_origin_name ) == true ) {
                                                ?>
                                                <br/><br/><a href="/file_down.php?key=<?=$file_idx?>" ><?=$file_origin_name?></a>
                                                <?php
                                                    } 
                                                ?>
                                                
                                            </div>
                                        </td>
                                    </tr>

                                    <tr id="service_items_area" style="display:none">
                                        <th class="info middle-align">메뉴선택</th>
                                        <td colspan="3" id="service_choice_area" >                                            
                                            
                                        </td>
                                    </tr>
                                                
                                </tbody>
                            </table>
                        </div> 

                    </div>

                </div>
            </div>
            <!-- //서비스 정보 -->

            <!-- 문서 유형선택  -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        
                        
                        <div class="table-responsive m-b-0">
                            <h5 class="header-title m-b-10">
                                <b>i-masic 문서 선택</b>                    
                            </h5>
                            <hr class="m-t-0">
                            
                            <div>
                                
                                <select class="form-control" name="doc_service_type" id="doc_service_type" style="width:250px;float:left;margin-right:20px" >
                                    <?php
                                        foreach( $doc_type AS $item ) {
                                    ?>
                                    <option value="<?=$item['item_code']?>" ><?=$item['title']?></option>
                                    <?php
                                        }
                                    ?>
                                    
                                </select>
                                
                                <select class="form-control" name="doc_item_code" id="doc_item_code" style="width:250px;float:left;margin-right:20px;display:none" >
                                   
                                </select>

                                <select class="form-control" name="doc_list" id="doc_list" style="width:250px;margin-right:20px;float:left;display:none" >
                                   
                                </select>
                                
                                <button type="button" class="pull-left btn btn-success waves-effect w-md m-l-5" id="btn_doc_add" style="display:none" onclick="addDoc();" >추가</button>

                            </div>
                            <div id="added_doc_area" style="clear:both;padding-top:30px" >
                            
                                <?php
                                    
                                    foreach( $document_usage AS $item ) {
                                ?>
                                <div class="alert alert-info alert-dismissible" role="alert" id="doc_div_<?=$item['doc_idx']?>" >
                                    <input type="hidden" name="doc_usage_idx[]" value="<?=$item['doc_usage_idx']?>">
                                    <input type="hidden" name="doc_idx[]" id="doc_input_<?=$item['doc_idx']?>" value="<?=$item['doc_idx']?>" >
                                    <input type="hidden" name="item_code[]"  value="<?=$item['item_code']?>" >
                                    <input type="hidden" name="doc_type[]"  value="<?=$item['doc_type']?>" >
                                    <input type="hidden" name="doc_title[]"  value="<?=$item['doc_title']?>" >
                                    <input type="hidden" name="doc_table_style_data[]"  value="<?=$item['doc_table_style_data']?>" >
                                    <input type="hidden" name="doc_data[]"  value="<?=$item['doc_data']?>" >
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
                                    <strong><?=$item['doc_title']?></strong>
                                </div>
                                <?php
                                    }                                  
                                ?>

                            </div>
                        </div> 
                        
                    </div>

                </div>
            </div>
            <!-- // 문서 유형선택  -->

           <input type="hidden" id="current_service_items" value="<?=$service_items?>" >
           
            </form>

            <div class="row"> 
                <div class="col-lg-12">
                    <button type="button" class="pull-right btn btn-inverse waves-effect w-md m-l-5" onclick="location.href='./<?=$page_name?>_list?page=<?=$page?><?=$params?>'">목록</button> 
                    <button type="button" class="pull-right btn btn-primary waves-effect w-md m-l-5" onclick="register()">저장</button>
               </div>
            </div>


        </div> <!-- // container -->
    </div> <!-- // content -->
</div> <!-- // content-page -->

<!-- 기업선택 레이어  -->
<div id="company_list_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" style="width: 500px; border: 1px solid;">
        <div class="modal-content p-0 b-0">                                    
            <div class="panel panel-color panel-inverse m-0">
                <div class="panel-heading">
                    <button type="button" class="close m-t-5" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="panel-title">기업 현황</h3>
                </div>
                <div class="panel-body">
                    <div class="row img-contents">
                        <div class="col-lg-12 table-responsive m-b-0">
                            
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="active">
                                        <th class="info" style="width: 40%;">회사명</th>
                                        <th class="info" style="width: 40%;">대표명</th>
                                        <th class="info" style="width: 20%;">사업자등록번호</th>
                                        <th class="info" style="width: 20%;">선택</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if( count( $company_list ) > 0  ){
                                            foreach( $company_list AS $item ) {                                                
                                    ?>
                                    <tr style="cursor: pointer;" >
                                        <td class="text-center"><?=$item['company_name']?></td>
                                        <td class="text-center vertical-align"><?=$item['ceo_name']?></td>
                                        <td class="text-center vertical-align"><?=$item['registration_no']?></td>
                                        <td class="text-center vertical-align">
                                            <button type="button" class="pull-right btn btn-sm btn-purple waves-effect w-md m-l-5" style="top:-4px;" onclick="choiceCompany(this)" data-company="<?=preg_replace( '/\"/', "'" ,json_encode( $item, JSON_UNESCAPED_UNICODE ))?>">선택</button> 
                                        </td>
                                    </tr>
                                    <?php
                                            }
                                        } else {
                                    ?>
                                    <tr style="cursor: pointer;" >
                                        <td colspan="4" class="text-center">
                                            기업 정보가 없습니다.
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>                                    
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- // 기업선택 레이어  -->




<script>

var doc_list_items = [];

    $(function(){

        
        $('#service_code').change(function(){

            if( $(this).val() == '' || $(this).val() == 'SV001' ) {
                $('#service_choice_area').html('');                
                $('#service_items_area').hide();
            } else {
                $('#service_items_area').show();
                getServiceItems( $(this).val() );
            }
           
        });

        

        $('#doc_service_type').change(function(){

            getDocItems( $(this).val() );
            $('#doc_list').hide();
            $('#btn_doc_add').hide();
            
        });
        
        /**
         * 문서 양식 요청 및 option 생성
         */
        $('#doc_item_code').change(function(){

            var doc_type = ($('#doc_service_type').val() == 'SI001') ? 'HACCP' : 'MES';

            jqueryAjaxCall({
                type : "post",
                url : '/doc/getDocFormListJson',
                dataType : "json",
                paramData :{                    
                    doc_type : doc_type                    
                    , item_code : $(this).val()                    
                } ,
                callBack : function( result_data ){
                    
                    
                    var html = '';

                    if( result_data.length > 0 ) {

                        $.each( result_data, function(idx, list_val ){

                            doc_list_items[ list_val['doc_idx'] ] = list_val;
                            
                            html += '<option value="'+ list_val['doc_idx'] +'">'+ list_val['doc_title'] +'</option>'; 
                            
                        });


                        $('#doc_list').html( html );                
                        $('#doc_list').show();
                        $('#btn_doc_add').show();

                    } else {
                        
                        $('#doc_list').html( '' );                
                        $('#doc_list').hide();
                        $('#btn_doc_add').hide();

                    }
                    

                } 
            });
            
        });

        $('#service_code').trigger('change');
        $('#doc_service_type').trigger('change');

    });

    

    /**
     * 문서 유형 생성
     */
    function getDocItems( arg_val ){

        jqueryAjaxCall({
            type : "post",
            url : '/service/service_items_proc',
            dataType : "json",
            paramData :{
                mode : 'get_fns'
                ,service_code : 'SV001'
                ,item_group : arg_val
            } ,
            callBack : function( result_data ){
                
                var html = '<option value="">문서양식 선택</option>';

                $.each( result_data, function(idx, list_val ){
         
                    html += '<option value="'+ list_val['item_code'] +'">'+ list_val['title'] +'</option>'; 
                    
                });

                $('#doc_item_code').html( html );                
                $('#doc_item_code').show();
                $('#btn_doc_add').hide();

            } 
        });

    }

    /**
     * 문서 추가
     */
    function addDoc(){        
        
        var doc_obj = doc_list_items[ $('#doc_list').val() ];
        var html='';
// console.log( doc_obj );
        if( $('#doc_div_' + doc_obj.doc_idx ).length == 0 ){

            html = '<div class="alert alert-info alert-dismissible" role="alert" id="doc_div_'+ doc_obj.doc_idx +'"" >';
            html += '   <input type="hidden" name="doc_usage_idx[]" value="" >';
            html += '   <input type="hidden" name="doc_idx[]" id="doc_input_'+ doc_obj.doc_idx +'" value="'+ doc_obj.doc_idx +'" >';
            html += '   <input type="hidden" name="item_code[]" value="'+ doc_obj.item_code +'" >';
            html += '   <input type="hidden" name="doc_type[]" value="'+ doc_obj.doc_type +'" >';
            html += '   <input type="hidden" name="doc_title[]"  value="'+ doc_obj.doc_title +'" >';
            html += '   <input type="hidden" name="doc_table_style_data[]"  value="'+ doc_obj.doc_table_style_data +'" >';
            html += '   <input type="hidden" name="doc_data[]"  value="'+ doc_obj.doc_data +'" >';
            html += '   <button type="button" class="close" data-dismiss="alert" aria-label="Close" ><span aria-hidden="true">&times;</span></button>';
            html += '   <strong>'+doc_obj.doc_title+'</strong>';
            html += '</div>  ';

            $('#added_doc_area').append( html );

        }
        
    }


    function getServiceItems( arg_service_code ){

        if( arg_service_code !== '' ) {
            // 데이터 요청
            jqueryAjaxCall({
                type : "post",
                url : '/service/service_items_proc',
                dataType : "json",
                paramData :{
                    mode : 'get_items'
                    , service_code : arg_service_code
                } ,
                callBack : makeServiceItemsCheckbox
            });
            
        } 

    }

    function makeServiceItemsCheckbox( arg_data ) {

        if( arg_data.length > 0 ) {
            
            var html = '';
            var valid_val = 'data-valid="check/3"';
            var checked_str = '';
            var current_service_items = $('#current_service_items').val();

            $.each( arg_data, function(idx, list_val ){
                
                if( idx > 0 ) {
                    valid_val = '';
                } 

                if( current_service_items.indexOf( list_val['item_code'] ) > -1 ) {
                    checked_str = 'checked="checked"';
                } else {
                    checked_str = '';
                }

                html += '<input type="checkbox" name="service_items[]" id="service_items_'+list_val['item_code']+'" value="'+ list_val['item_code'] +'" '+ valid_val +' '+ checked_str +' > <label class="m-r-10" for="service_items_'+list_val['item_code']+'">'+list_val['title']+'</label>'; 
                

            });

            $('#service_choice_area').html( html );


        }
        
    }

     /**
     * 저장 버튼 동작
     */
    function register(){

        if( $('#company_idx').val() == '' ) {
            alert('기업을 선택 해주세요.');
            return;
        }

        viewFormValid.alert_type = 'alert';        
        if( viewFormValid.run( 'form_write' ) === true ) {
            // submit
            $('#form_write').submit();
        }

    }

    function changeCompany(){
        $('#company_list_modal').modal();
    }

    function choiceCompany( arg_this ) {       

        var data = JSON.parse( $(arg_this).data('company').replace(/'/g, '"') );

        $('#company_idx').val( data.company_idx );
        $('#company_name').html( data.company_name );
        $('#ceo_name').html( data.ceo_name );
        $('#registration_no').html( data.registration_no );
        $('#company_tel').html( data.company_tel );
        $('#partner_name').html( data.partner_name );
        $('#partner_phone_no').html( data.partner_phone_no );
        
        $('#company_list_modal').modal('hide');
        
    }

    //  업로드 버튼 이벤트 처리
    function readFile(arg_this, arg_input_name) {
        if (arg_this.files && arg_this.files[0]) {

            if(window.FileReader){  // modern browser
				var filename = $(arg_this)[0].files[0].name;
			} 
			else {  // old IE
				var filename = $(arg_this).val().split('/').pop().split('\\').pop();  // 파일명만 추출
			}
            
            $('code.'+arg_input_name ).text(filename);   
        }
    }

    /**
     * 문서 서비스 변경 이벤트
     */
    
   

</script>
<style>    
    .table>tbody>tr>th.info {
        width: 15%;
    }
    /* table input {
        width: 40% !important; display: inline-block !important;
    } */
</style>
<script type="text/javascript" src="<?=$aqua_view_path;?>/public/js/view.form.valid.js"></script>
            
