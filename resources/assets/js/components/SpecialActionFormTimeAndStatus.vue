<template>
    <form class="shadow rounded">
        <div class="form-group">
            <div class="form-row  justify-content-center">
                <label for=""><b>专项行动操作</b></label>
            </div>
            <div class="form-row">
                <label for="inspection_status">专项核查情况</label>
                <textarea class="form-control" v-model="sp_item.inspection_record" id="sp_inspection_record" rows="2"></textarea>
                <a class="btn btn-primary" href="javascript:;">保存备注</a>
                <a class="btn btn-primary" href="javascript:;">撤销更改</a>
                <a class="btn btn-primary" href="javascript:;">录入正常</a>
                <a class="btn btn-primary" href="javascript:;">快速查无</a>
                <a class="btn btn-primary" href="javascript:;">记录新电话</a>
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 0">
            <div class="form-row">
                <label for="inspection_status">专项电话记录</label>
                <textarea class="form-control" v-model="sp_item.phone_call_record" id="sp_phone_call_record" rows="2"></textarea>
                <a class="btn btn-primary" href="javascript:;">保存记录</a>
                <a class="btn btn-primary" href="javascript:;">撤销更改</a>
            </div>
        </div>
    </form>
</template>

<script>
export default {
  // props: ['sp_item', 'corp'],
  data: function () {
    return {
      sp_item: window.Backend.sp_item,
  }
},
mounted() {
    console.log('Done Undone Btn Ok')
},
};
</script>