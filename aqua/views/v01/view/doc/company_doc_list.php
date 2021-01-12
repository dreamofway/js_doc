<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    기업 문서 관리
                    <!-- <button type="button" class="pull-right btn btn-primary waves-effect w-md" onclick="location.href='./<?=$page_name?>_write?page=<?=$page?><?=$params?>'">+등록</button>                  -->
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
                               
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">상태</label>
                                        <div class="col-sm-3">
                                            <div class="input-daterange input-group" id="date-range">
                                                <select class="form-control" name="sch_use_flag" style="width:200px" >                                                
                                                    <option value="" <?=($sch_use_flag == '' ? 'selected="selected"' : '' )?> >전체</option>
                                                    <option value="Y" <?=($sch_use_flag == 'Y' ? 'selected="selected"' : '' )?> >사용</option>
                                                    <option value="N" <?=($sch_use_flag == 'N' ? 'selected="selected"' : '' )?> >미사용</option>
                                                    
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                

                                <div class="col-sm-7">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">등록일</label>
                                        <div class="col-sm-4">
                                            <div class="input-daterange input-group" id="date-range">
                                                <input type="text" class="form-control datepicker" readonly="readonly" name="sch_s_date" value="<?=$sch_s_date;?>">
                                                <span class="input-group-addon bg-primary b-0 text-white">~</span>
                                                <input type="text" class="form-control datepicker" readonly="readonly" name="sch_e_date" value="<?=$sch_e_date;?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">문서</label>
                                        <div class="col-sm-4">
                                            <div class="input-daterange input-group" id="date-range">
                                                <select class="form-control" name="sch_item_code" style="width:200px" >     

                                                    <option value="" <?=($sch_item_code == '' ? 'selected="selected"' : '' )?> >전체</option>  
                                                                                         
                                                    <?php
                                                        foreach( $doc_items AS $key=>$val ) {
                                                    ?>
                                                    <option value="<?=$val['item_code']?>" <?=($sch_item_code == $val['item_code'] ? 'selected="selected"' : '' )?> ><?=$val['title']?></option> 
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-7">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">검색</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="sch_keyword" value="<?=$sch_keyword;?>" placeholder="문서명, 기업명">
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
                                        <th class="info" style="width: 20%;" >문서</th>                                        
                                        <th class="info" style="width: 40%;" >제목</th>
                                        <th class="info" >기업명</th>
                                        <th class="info" >사용상태</th>                                                                                                     
                                    </tr>                                
                                </thead>
                                <tbody>
                                    <?php 
                                    if( $paging->total_rs > 0 ){ 
                                                
                                        foreach($list AS $key=>$value) {
                                    ?>
                                    <tr>
									<!--
                                        <td><?=( ( $paging->total_rs - ( $page-1 ) * $list_rows - $key ) );?></td>
									-->
                                        <td><?=$value['doc_usage_idx'];?></td>                                        
                                        <td><?=$value['item_title'];?></td>                                        
                                        <td>
											<a class="underline" href="./<?=$page_name?>_write?mode=edit&page=<?=$page?><?=$params?>&doc_usage_idx=<?=$value['doc_usage_idx'];?>">
                                                <?=$value['doc_title'];?>
											</a>
										</td>                                        
                                        <td><?=$value['company_name'];?></td>
                                        <td><?=($value['use_flag'] == 'Y') ? '사용' : '미사용' ;?></td>
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

            
