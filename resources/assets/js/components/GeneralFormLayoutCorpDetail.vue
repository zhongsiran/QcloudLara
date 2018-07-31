<template>
    <form class="shadow rounded">
        <div class="form-group">
            <div class="form-row justify-content-center">
                    <label for=""><b>日常监管操作</b></label>
            </div>
            <div class="form-row">
                <div class="col">
                    <a class="btn btn-block btn-primary" data-toggle="collapse" href="#inspection_status_editor" role="button">
                        修改核查记录
                    </a>
                </div>
                <div class="col">
                    <a class="btn btn-block btn-primary" data-toggle="collapse" href="#phone_call_record_editor" role="button">
                        修改电话记录
                    </a>
                </div>
            </div>
        </div>
        <div class="form-group collapse" id="inspection_status_editor">
            <div class="form-row">
                <label for="inspection_status">日常备注</label>
                <textarea class="form-control" v-model="corp.inspection_status" id="inspection_status" rows="2"></textarea>
                <a class="btn btn-primary" href="javascript:;">保存备注</a>
                <a class="btn btn-primary" href="javascript:;">撤销更改</a>

            </div>
            <div class="form-row">
                <a class="btn btn-primary" href="javascript:;">录入正常</a>
                <a class="btn btn-primary" href="javascript:;">快速查无</a>
                <a class="btn btn-primary" href="javascript:;">记录新电话</a>
            </div>
        </div>
        
        <div class="form-group collapse" id="phone_call_record_editor">
            <div class="form-row">
                <label for="inspection_status">电话记录</label>
                <textarea class="form-control" v-model="corp.phone_call_record" id="phone_call_record" rows="2"></textarea>
                <a class="btn btn-primary" href="javascript:;">保存记录</a>
                <a class="btn btn-primary" href="javascript:;">撤销更改</a>
            </div>
        </div>
        <div class="form-row">
            <div class="col">
                <a class="btn btn-block btn-primary" href="javascript:;">上传定位</a>    
            </div>
            <div class="col">
                <a class="btn btn-block btn-primary" href="javascript:;">上传照片</a>    
            </div>
            <div class="col">
                <a class="btn btn-block btn-primary" href="javascript:;">进行导航</a>    
            </div>            
        </div>
    </form>
</template>

<script>
export default {
  // props: ['sp_item', 'corp'],
  data: function () {
    return {
      corp: window.Backend.corp,
  }
},
mounted() {
    console.log('Done Undone Btn Ok')
},
};
</script>