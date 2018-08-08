<template>
<form class="shadow rounded">
        <div class="form-group">
            <div class="form-row  justify-content-center">
                <label for=""><b>提示：开发中，按键未有实际保存作用，请通过公众号对话方式操作</b></label>
            </div>
            <div class="form-row  justify-content-center">
                <label for=""><b>专项行动操作</b></label>
            </div>
            
            <div class="form-row">
                <label for="inspection_status">专项核查情况</label>
                <textarea class="form-control" v-model="sp_item.inspection_record" id="sp_inspection_record" rows="3"></textarea>
                <a class="btn btn-primary" href="javascript:;" @click = "saveInspectionRecord">保存备注</a>
                <!-- <a class="btn btn-primary" href="javascript:;" @click = "getChineseDate">撤销更改</a> -->
                <a class="btn btn-primary" href="javascript:;" @click="setInspectionNormal">正常</a>
                <a class="btn btn-primary" href="javascript:;" @click="setInspectionNotFound" >查无</a>
            </div>
        </div>

        <div class="form-group">
            <div class="form-row">
                <label for="inspection_status">核查开始时间</label>
                <textarea class="form-control" v-model="sp_item.start_inspect_time" rows="1"></textarea>
                <label for="inspection_status">核查结束时间</label>
                <textarea class="form-control" v-model="sp_item.end_inspect_time" rows="1"></textarea>
                <a class="btn btn-primary" href="javascript:;">保存开始时间</a>
                <a class="btn btn-primary" href="javascript:;">保存结束时间</a>
                <a class="btn btn-primary" href="javascript:;" @click="setInspectionTimeShortcut">快速设置时间并保存</a>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 0">
            <div class="form-row">
                <label for="inspection_status">专项电话记录</label>
                <textarea class="form-control" v-model="sp_item.phone_call_record" id="sp_phone_call_record" rows="2"></textarea>
                <a class="btn btn-primary" href="javascript:;">保存记录</a>
                <a class="btn btn-primary" href="javascript:;">无法接通</a>
                <a class="btn btn-primary" href="javascript:;">空号</a>
                <a class="btn btn-primary" href="javascript:;">停机</a>
                <a class="btn btn-primary" href="javascript:;">撤销更改</a>
            </div>
        </div>
    </form>
</template>

<script>
export default {
  // props: ['sp_item', 'corp'],
  data: function() {
    return {
      sp_item: window.Backend.sp_item,
      corp:  window.Backend.corp
    };
  },
  mounted() {
    console.log("Special action input forms Ok");
  },
  methods: {
    saveInspectionRecord: function() {
      //   alert(this.sp_item.inspection_record)
      let datetime_array = this.getChineseDateArray();
      this.sp_item.start_inspect_time = datetime_array[0];
      this.sp_item.end_inspect_time = datetime_array[2];

      // alert(JSON.stringify(this.sp_item));
    },
    getChineseDateArray: function() {
      let chn_datetime_now = moment().format("YYYY年M月D日H时m分");

      let chn_datetime_15_b = moment()
        .subtract(15, "m")
        .format("YYYY年M月D日H时m分");

      let chn_datetime_15_a = moment()
        .add(15, "m")
        .format("YYYY年M月D日H时m分");

      return [chn_datetime_15_b, chn_datetime_now, chn_datetime_15_a];
    },
    setInspectionNormal: function () {
      this.sp_item.inspection_record = '当事人在' + this.corp.address + '正常经营'
    },
    setInspectionNotFound: function () {
      this.sp_item.inspection_record = '执法人员在' + this.corp.address + '未发现当事人的经营迹象。当事人通过登记地址无法联系。'
    },
    setInspectionTimeShortcut: function() {
      let datetime_array = this.getChineseDateArray();
      this.sp_item.start_inspect_time = datetime_array[0];
      this.sp_item.end_inspect_time = datetime_array[2];
    }
  }
};
</script>