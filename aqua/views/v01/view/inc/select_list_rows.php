<div style="height: 35px;">
    <div class="pageNum">
        <div class="pull-right">
            <select class="form-control pull-right" id="change_list_rows" style="width:120px;" >
                <option value="10" <?php if($page_size == 10) echo "selected"?>>10개 보기</option>
                <option value="20" <?php if($page_size == 20) echo "selected"?>>20개 보기</option>
                <option value="50" <?php if($page_size == 50) echo "selected"?>>50개 보기</option>
                <option value="100" <?php if($page_size == 100) echo "selected"?>>100개 보기</option>
            </select>
        </div>
    </div> 
</div>