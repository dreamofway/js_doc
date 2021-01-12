<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    APP 버전 정보  > <?=$page_work?>
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

            <form class="form-horizontal" role="form" method="post" id="form_write" enctype="multipart/form-data"  action="./<?=$page_name?>_proc" >                
                <input type="hidden" name="mode" id="mode" value="<?=$mode?>" />                
                <input type="hidden" name="app_ver_idx" id="app_ver_idx" value="<?=$app_ver_idx?>" />
                <input type="hidden" name="app_idx" id="app_idx" value="<?=$app_idx?>" />
                <input type="hidden" name="page" value="<?=$page?>" />
                <input type="hidden" name="top_code" value="<?=$top_code?>" />
                <input type="hidden" name="left_code" value="<?=$left_code?>" />
                <input type="hidden" name="ref_params" value="<?=$params?>" />
                <input type="hidden" name="return_page" value="<?=$page_name?>" />
                <input type="hidden" name="file_idx" id="file_idx" value="<?=$file_idx?>" />                  

                <input type="hidden" id="init_platform_aos" value="<?=$aos_release?>" />
                <input type="hidden" id="init_platform_ios" value="<?=$ios_release?>" />			    

             <!-- 파일 정보 -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        
                        <div class="table-responsive m-b-0">
                            <h5 class="header-title m-b-10">
                                <b>APK 파일 정보</b>    
                            </h5>
                            <hr class="m-t-0">
                            <table class="table table-bordered text-left">
                                <tbody> 
                                    
                                    <tr>
                                        <th class="info middle-align">APK 파일</th>
                                        <td colspan="3" >                                            
                                            <div class="form-group"> 

                       
                                                <div class="upload-btn-wrapper">
                                                    <button type="button" class="btn btn-primary">업로드</button>
                                                    <input type="file" name="app_apk_file" id="app_apk_file"  onchange="readFile(this, 'app_apk_file');">                                                    
                                                    <code class="control-label m-l-10 app_apk_file"></code>                                                    
                                                </div>

                                                <?php
                                                    if( isset( $file_origin_name ) == true ) {
                                                ?>
                                                <br><br><a href="/file_down.php?key=<?=$file_idx?>" ><?=$file_origin_name?></a>
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
            <!-- //파일 정보 --> 
            
            <!-- 기본정보 -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        
                        <div class="table-responsive m-b-0">
                            <h5 class="header-title m-b-10">
                                <b>버전정보</b>                                    
                            </h5>
                            <hr class="m-t-0">
                            <table class="table table-bordered text-left">
                                <tbody>  
                                    <tr>
                                        <th class="info middle-align">최신 적용 버전</th>
                                        <td colspan="3" >                                            
                                            <?=$recently_ver?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="info middle-align">os</th>
                                        <td colspan="3" >                                            
                                            <select class="form-control" name="os" id="os" style="width:200px"  >
                                                <option value="AOS" <?=($os == 'AOS') ? 'selected="selected"' : '' ?> >AOS</option>
                                                <option value="IOS" <?=($os == 'IOS') ? 'selected="selected"' : '' ?> >IOS</option>
                                            </select> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="info middle-align">버전</th>
                                        <td colspan="3" >                                            
                                            <input type="text" class="form-control" id="version" name="version" value="<?=$version?>" style="width:50% !important;" data-valid="blank" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="info middle-align">버전 설명</th>
                                        <td colspan="3" >                                            
                                            <textarea class="form-control" id="description" name="description" style="width:784px;height:200px;line-height:30px"><?=$description?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="info middle-align">적용여부</th>
                                        <td colspan="3" >

                                        <select class="form-control" name="apply_flag" id="apply_flag" style="width:200px"  >
                                            <option value="N" <?=($apply_flag == 'N') ? 'selected="selected"' : '' ?> >미적용</option>
                                            <option value="Y" <?=($apply_flag == 'Y') ? 'selected="selected"' : '' ?> >적용</option>
                                        </select> 

                                        </td>
                                    </tr>

                          
                                </tbody>
                            </table>
                        </div> 

                    </div>

                </div>
            </div>
            <!-- //기본정보 -->
           
           
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




<script>

    $(function(){
        $('#aos_release').click(function(){
            if( $(this).prop('checked') == true ) {
                $('#AOS_area').show();
            } else {
                $('#AOS_area').hide();
            }
        });

        $('#ios_release').click(function(){
            if( $(this).prop('checked') == true ) {
                $('#IOS_area').show();
            } else {
                $('#IOS_area').hide();
            }
        });
        
        if( $('#init_platform_aos').val() == 'Y' ) {
            $('#aos_release').trigger('click');
        }

        if( $('#init_platform_ios').val() == 'Y' ) {
            $('#ios_release').trigger('click');
        }
       
    });

     /**
     * 저장 버튼 동작
     */
    function register(){

        
        viewFormValid.alert_type = 'add';        
        if( viewFormValid.run( 'form_write' ) === true ) {
            // submit
            
            if( $('#aos_release').prop('checked') == true ) {
                if( $('#aos_package').val() == '' ){
                    jQueryDialog({type : 'alert', text : 'AOS Package 명을 입력해주세요.', callback_fn : function(){ $('#aos_package').focus(); }}); 
                    return;
                }
            }

            if( $('#ios_release').prop('checked') == true ) {
                if( $('#ios_bundle_id').val() == '' ){
                    jQueryDialog({type : 'alert', text : 'IOS Bundle ID를 입력해주세요.', callback_fn : function(){ $('#ios_bundle_id').focus(); }}); 
                    return;
                }
            }
            
            $('#form_write').submit();
        }

    }


    /**
     * 삭제 버튼 동작
     */
    function delProc(){
        if(confirm('현재 게시물을 삭제하시겠습니까?') == true ){
            $('#mode').val('del');
            $('#form_write').submit();
        }
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
            
