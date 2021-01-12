<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    정보보호관리  > <?=$page_work?>
                    <button type="button" class="pull-right btn btn-inverse waves-effect w-md m-l-5" onclick="location.href='./<?=$page_name?>_list?page=<?=$page?><?=$params?>'">목록</button> 
                </h1>                
            </section>

            <form class="form-horizontal" role="form" method="post" id="form_write" enctype="multipart/form-data"  action="./<?=$page_name?>_proc" >                
                <input type="hidden" name="mode" value="<?=$mode?>" />
                <input type="hidden" name="security_idx" value="<?=$security_idx?>" />
                <input type="hidden" name="company_idx" id="company_idx" value="<?=$company_idx?>" />
                <input type="hidden" name="page" value="<?=$page?>" />
                <input type="hidden" name="top_code" value="<?=$top_code?>" />
                <input type="hidden" name="left_code" value="<?=$left_code?>" />
                <input type="hidden" name="ref_params" value="<?=$params?>" />
                <input type="hidden" name="file_idx" id="file_idx" value="<?=$file_idx?>" />

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
                                <b>정보보호 등록</b>    
                            </h5>
                            <hr class="m-t-0">
                            <table class="table table-bordered text-left">
                                <tbody> 
                                    <tr>
                                        <th class="info middle-align">작성일</th>
                                        <td colspan="3" >                                            
                                            <div class="form-group">                                            
                                                <div class="col-sm-4">
                                                    <div class="input-daterange input-group" >
                                                        <input type="text" class="form-control datepicker " name="write_date" id="write_date" value="<?=dateType( $write_date, 8 )?>" data-valid="blank" readonly="readonly" >                                                        
                                                    </div>
                                                </div>
                                            </div>
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
                                        <th class="info middle-align">파일업로드</th>
                                        <td colspan="3" >                                            
                                            <div class="form-group">                                                
                                                <div class="upload-btn-wrapper">
                                                    <button type="button" class="btn btn-primary">업로드</button>
                                                    <input type="file" name="contract_file" id="contract_file"  onchange="readFile(this, 'contract_file');">                                                    
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
 
                                </tbody>
                            </table>
                        </div> 

                    </div>

                </div>
            </div>
            <!-- //서비스 정보 --> 

           
           
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
            if( ( $('#contract_file').val() == '' ) && ( $('#file_idx').val() == '' ) ) {
                alert('파일을 선택해주세요.');
                return;
            }

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
            
