<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    서비스 계약관리 
                    <button type="button" class="pull-right btn btn-primary waves-effect w-md" onclick="location.href='./<?=$page_name?>_write?page=<?=$page?><?=$params?>'">+계약등록</button>                 
                </h1>                   
            </section>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box p-b-0">
                        <div class="row">
                            <form class="form-horizontal group-border-dashed clearfix" name="fsearch" id="fsearch" method="get" action="">
                                <input type="hidden" name="page" value="<?=$page?>">
                                <input type="hidden" name="list_rows" value="<?=$list_rows?>">                                                        
                                <input type="hidden" name="top_code" value="<?=$top_code?>">
                                <input type="hidden" name="left_code" value="<?=$left_code?>">

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">가입일</label>
                                        <div class="col-sm-9">
                                            <div class="input-daterange input-group" id="date-range">
                                                <input type="text" class="form-control datepicker" readonly="readonly" name="sch_s_date" value="<?=$sch_s_date;?>">
                                                <span class="input-group-addon bg-primary b-0 text-white">~</span>
                                                <input type="text" class="form-control datepicker" readonly="readonly" name="sch_e_date" value="<?=$sch_e_date;?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                              
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">검색</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="sch_keyword" value="<?=$sch_keyword;?>" placeholder="회사명, 사업자등록번호, 관리자명, 휴대폰번호">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">서비스명</label>
                                        <div class="col-sm-9 p-t-5">
                                            <?php                                                
                                                foreach( $service_list AS $idx=>$item) {                                                    
                                            ?>
                                            <input type="checkbox" name="sch_service_<?=$idx?>" id="sch_service_<?=$idx?>" value="<?=$item['service_code']?>" <?=(${'sch_service_'.$idx} == $item['service_code']) ? 'checked="checked"' : '' ?>  />
                                            <label class="m-l-5 m-r-10" for="sch_service_<?=$idx?>"><?=$item['service_name']?></label>
                                            <?php
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-sm-offset-5 col-sm-7 m-t-15">
                                            <button type="button" class="btn btn-primary waves-effect waves-light" onclick="location.href='./<?=$page_name?>_list?top_code=<?=$top_code?>&left_code=<?=$left_code?>'">기본설정</button>
                                            <!-- <button type="reset" class="btn btn-primary waves-effect waves-light">기본설정</button> -->
                                            <button type="submit" class="btn btn-inverse waves-effect m-l-5">검색</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <h5 class="header-title m-t-0">
                            <b>목록</b>
                            <!-- <button type="button" class="btn btn-sm btn-primary waves-effect waves-light m-l-5 fright" onclick="tableToExcel('excelTable', '기업회원');">엑셀다운로드</button> -->
                        </h5>
                        <hr class="m-t-0">
                        <div class="table-responsive m-b-0">
                            <table class="table table-bordered text-center" id="excelTable">
                                <thead>
                                    <tr class="active">
                                        <th class="info" style="width: 3%;">NO</th>
                                        <th class="info" >서비스명</th>
                                        <th class="info" >계약 시작일</th>
                                        <th class="info" >계약 종료일</th>
                                        <th class="info"  style="width: 10%;">회사명</th> 
                                        <th class="info" >사업자등록번호</th>
                                        <th class="info" >대표명</th>
                                        <th class="info" >전화번호</th>
                                        <th class="info" >담당자</th>
                                        <th class="info" >휴대폰번호</th>
                                        <th class="info" >이메일</th>                                                                                                         
                                    </tr>                                
                                </thead>
                                <tbody>
                                    <?php 
                                    if( $paging->total_rs > 0 ){ 
                                                
                                        foreach($list AS $key=>$value) {
                                    ?>
                                    <tr>
                                        <td><?=( ( $paging->total_rs - ( $page-1 ) * $list_rows - $key ) );?></td>
                                        <td><?=$value['service_name'];?></td>
                                        <td><?=dateType( $value['start_date'], 8);?></td>
                                        <td><?=dateType( $value['end_date'], 8);?></td>
                                        <td>
                                            <a class="underline" href="./<?=$page_name?>_write?mode=edit&page=<?=$page?><?=$params?>&idx=<?=$value['idx'];?>">
                                                <?=$value['company_name'];?>
                                            </a>
                                        </td>
                                        <td><?=$value['registration_no'];?></td>
                                        <td><?=$value['ceo_name'];?></td>
                                        <td><?=$value['company_tel'];?></td>
                                        <td><?=$value['partner_name'];?></td>
                                        <td><?=$value['partner_phone_no'];?></td>
                                        <td><?=$value['partner_email'];?></td>
                                    </tr>
                                    <?php   
                                        }
                                    } else {
                                    ?>                                
                                        <tr><td colspan="11">데이터가 없습니다</td></tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>                                       
                            </table>
                        </div>
                        <div class="text-center">
                            <div class="pagination">                    
                                <?=$paging->draw(); ?>
                            </div>
                        </div>

                        <?php include_once( $this->getViewPhysicalPath( '/view/inc/select_list_rows.php' )  ); ?>

                    </div>                       
                </div>
            </div><!-- end row --> 

        </div> <!-- // container -->
    </div> <!-- // content -->
</div> <!-- // content-page -->

            
