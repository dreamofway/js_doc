<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    APP 현황
                </h1>                   
            </section>


            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <h5 class="header-title m-t-0">
                            <b>등록된 앱</b>
                            <!-- <button type="button" class="btn btn-sm btn-primary waves-effect waves-light m-l-5 fright" onclick="tableToExcel('excelTable', '기업회원');">엑셀다운로드</button> -->
                        </h5>
                        <hr class="m-t-0">
                        <div class="table-responsive m-b-0">
                            <table class="table table-bordered text-center" id="excelTable">
                                <thead>
                                    <tr class="active">                                        
                                        <th class="info" style="width: 10%;" >등록일</th>
                                        <th class="info"  style="width: 20%;">APP 명</th> 
                                        <th class="info" >OS</th>                                        
                                        <th class="info" >AOS 배포</th>
                                        <th class="info" >IOS 배포</th>                                                                                                                                                 
                                    </tr>                                
                                </thead>
                                <tbody>
                                    <?php 
                                    if( $paging->total_rs > 0 ){ 
                                                
                                        foreach($list AS $key=>$value) {
                                    ?>
                                    <tr>
                                        <td><?=dateType( $value['reg_date'], 8);?></td>                                        
                                        <td>
                                            <a class="underline" href="./app_ver_history_list?top_code=<?=$top_code?>&left_code=<?=$left_code;?>&app_idx=<?=$value['app_idx'];?>">
                                                <?=$value['app_name'];?>
                                            </a>
                                        </td>                                        
                                        <td><?=( $value['aos_release'] == 'Y') ? ' [AOS] ' : ''?><?=( $value['ios_release'] == 'Y') ? ' [IOS] ' : ''?></td>
                                        <td><?=$value['aos_initial_date'];?></td>
                                        <td><?=$value['ios_initial_date'];?></td>                                       
                                    </tr>
                                    <?php   
                                        }
                                    } else {
                                    ?>                                
                                        <tr><td colspan="5">데이터가 없습니다</td></tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>                                       
                            </table>
                        </div>
                        <div class="text-center">
                            <div class="pagination">                    
                                <!-- <?=$paging->draw(); ?> -->
                            </div>
                        </div>

                    </div>                       
                </div>
            </div><!-- end row --> 

        </div> <!-- // container -->
    </div> <!-- // content -->
</div> <!-- // content-page -->

            
