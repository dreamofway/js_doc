<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    APP 정보  > <?=$page_work?>
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
                <input type="hidden" name="app_idx" id="app_idx" value="<?=$app_idx?>" />
                <input type="hidden" name="page" value="<?=$page?>" />
                <input type="hidden" name="top_code" value="<?=$top_code?>" />
                <input type="hidden" name="left_code" value="<?=$left_code?>" />
                <input type="hidden" name="ref_params" value="<?=$params?>" />
                <input type="hidden" name="return_page" value="<?=$page_name?>" />
                <input type="hidden" name="file_idx" id="file_idx" value="<?=$file_idx?>" />                  

                <input type="hidden" id="init_platform_aos" value="<?=$aos_release?>" />
                <input type="hidden" id="init_platform_ios" value="<?=$ios_release?>" />			    

             <!-- 이미지 정보 -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        
                        <div class="table-responsive m-b-0">
                            <h5 class="header-title m-b-10">
                                <b>아이콘 정보</b>    
                            </h5>
                            <hr class="m-t-0">
                            <table class="table table-bordered text-left">
                                <tbody> 
                                    
                                    <tr>
                                        <th class="info middle-align">APP 아이콘</th>
                                        <td colspan="3" >                                            
                                            <div class="form-group"> 

                                                
                                                
                                                <?php
                                                    if( isset( $file_origin_name ) == true ) {
                                                ?>

                                                <img src="<?=$file_path?>/<?=$file_server_name?>" style="height:170px" />
                                                <br>
                                                <button type="button" class="btn btn-sm btn-purple waves-effect w-md m-l-15" style="margin-left:25px" onclick="location.href='/file_down.php?key=<?=$file_idx?>'">다운로드</button>
                                                <br/>
                                                <br/>
                                                <?php
                                                    } 
                                                ?>
                                                

                                                <div class="upload-btn-wrapper">
                                                    <button type="button" class="btn btn-primary">업로드</button>
                                                    <input type="file" name="app_icon_file" id="app_icon_file"  onchange="readFile(this, 'app_icon_file');">                                                    
                                                    <code class="control-label m-l-10 app_icon_file"></code>
                                                    
                                                </div>

                                                
                                            </div>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div> 

                    </div>

                </div>
            </div>
            <!-- //이미지 정보 --> 
            
            <!-- 기본정보 -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        
                        <div class="table-responsive m-b-0">
                            <h5 class="header-title m-b-10">
                                <b>기본정보</b>                                    
                            </h5>
                            <hr class="m-t-0">
                            <table class="table table-bordered text-left">
                                <tbody>  

                                    <tr>
                                        <th class="info middle-align">APP 명</th>
                                        <td colspan="3" >                                            
                                            <input type="text" class="form-control" id="app_name" name="app_name" value="<?=$app_name?>" style="width:50% !important;" data-valid="blank" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="info middle-align">앱 설명</th>
                                        <td colspan="3" >                                            
                                            <textarea class="form-control" id="description" name="description" style="width:784px;height:200px;line-height:30px"><?=$description?></textarea>
                                        </td>
                                    </tr>
									<tr>
                                        <th class="info middle-align">FCM KEY</th>
                                        <td colspan="3" >                                            
                                            <input type="text" class="form-control" id="fcm_access_key" name="fcm_access_key" value="<?=$fcm_access_key?>" style="width:100% !important;" />
                                        </td>
                                    </tr>
									
                                    <tr>
                                        <th class="info middle-align">플랫폼</th>
                                        <td colspan="3" >

                                            <span>
                                                <input type="checkbox"  name="aos_release" id="aos_release" value="Y" />
                                                <label for="aos_release" >AOS</label>
                                            </span>

                                            <span style="margin-left:10px" >
                                                <input type="checkbox"  name="ios_release" id="ios_release" value="Y" />
                                                <label for="ios_release" >IOS</label>
                                            </span>

                                        </td>
                                    </tr>

                          
                                </tbody>
                            </table>
                        </div> 

                    </div>

                </div>
            </div>
            <!-- //기본정보 -->


            <!-- AOS 정보 -->
            <div class="row" id="AOS_area" style="display:none" >
                <div class="col-lg-12">
                    <div class="card-box">
                        
                        <div class="table-responsive m-b-0">
                            <h5 class="header-title m-b-10">
                                <b>AOS 정보</b>    
                            </h5>
                            <hr class="m-t-0">
                            <table class="table table-bordered text-left">
                                <tbody>  

                                    <tr>
                                        <th class="info middle-align">AOS Package명</th>
                                        <td colspan="3" >                                            
                                            <input type="text" class="form-control" id="aos_package" name="aos_package" value="<?=$aos_package?>" style="width:50% !important;"  />
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">AOS Key Hash</th>
                                        <td colspan="3" >                                            
                                            <input type="text" class="form-control" id="aos_key_hash" name="aos_key_hash" value="<?=$aos_key_hash?>" style="width:50% !important;"  />
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">AOS Market URL</th>
                                        <td colspan="3" >                                            
                                            <input type="text" class="form-control" id="aos_market_url" name="aos_market_url" value="<?=$aos_market_url?>" style="width:80% !important;"  />
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">Android 출시일</th>
                                        <td colspan="3">                                            
                                            <div class="form-group">                                            
                                                <div class="col-sm-4">
                                                    <div class="input-daterange input-group">
                                                        <input type="text" class="form-control datepicker " name="aos_initial_date" id="aos_initial_date" value="<?=$aos_initial_date?>" >  
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                          
                                </tbody>
                            </table>
                        </div> 

                    </div>

                </div>
            </div>
            <!-- //AOS 정보 -->


            <!-- IOS 정보 -->
            <div class="row" id="IOS_area" style="display:none" >
                <div class="col-lg-12">
                    <div class="card-box">
                        
                        <div class="table-responsive m-b-0">
                            <h5 class="header-title m-b-10">
                                <b>IOS 정보</b>    
                            </h5>
                            <hr class="m-t-0">
                            <table class="table table-bordered text-left">
                                <tbody>  

                                    <tr>
                                        <th class="info middle-align">IOS Bundle ID</th>
                                        <td colspan="3" >                                            
                                            <input type="text" class="form-control" id="ios_bundle_id" name="ios_bundle_id" value="<?=$ios_bundle_id?>" style="width:50% !important;"  />
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">iPhone AppStore ID</th>
                                        <td colspan="3" >                                            
                                            <input type="text" class="form-control" id="iphone_appstore_id" name="iphone_appstore_id" value="<?=$iphone_appstore_id?>" style="width:50% !important;"  />
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th class="info middle-align">iPhone Market URL</th>
                                        <td colspan="3" >                                            
                                            <input type="text" class="form-control" id="iphone_market_url" name="iphone_market_url" value="<?=$iphone_market_url?>" style="width:80% !important;"  />
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th class="info middle-align">iPad AppStore ID</th>
                                        <td colspan="3" >                                            
                                            <input type="text" class="form-control" id="ipad_appstore_id" name="ipad_appstore_id" value="<?=$ipad_appstore_id?>" style="width:80% !important;"  />
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">iPad Market URL</th>
                                        <td colspan="3" >                                            
                                            <input type="text" class="form-control" id="ipad_market_url" name="ipad_market_url" value="<?=$ipad_market_url?>" style="width:80% !important;"  />
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">IOS 출시일</th>
                                        <td colspan="3">                                            
                                            <div class="form-group">                                            
                                                <div class="col-sm-4">
                                                    <div class="input-daterange input-group">
                                                        <input type="text" class="form-control datepicker " name="ios_initial_date" id="ios_initial_date" value="<?=$ios_initial_date?>" >  
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                          
                                </tbody>
                            </table>
                        </div> 

                    </div>

                </div>
            </div>
            <!-- //IOS 정보 -->
           
           
           
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
            
