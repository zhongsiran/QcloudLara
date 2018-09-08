<template>
  <form class="shadow rounded" id="special-action-time-and-status-form">
    <div class="form-group">
      <div class="form-row  justify-content-center">
        <label for=""><b>专项行动操作</b></label>
      </div>
    </div>

    <div class="form-group">
      <div class="form-row">
        <label for="inspection_status">核查开始时间</label>
        <textarea class="form-control" v-model="sp_item.start_inspect_time" rows="1"></textarea>
        <label for="inspection_status">核查结束时间</label>
        <textarea class="form-control" v-model="sp_item.end_inspect_time" rows="1"></textarea>
        <div class="container-fluid special-form">
          <span class="float-left">

            <a class="btn btn-primary" href="javascript:;" @click="setInspectionTimeShortcut">快速设置时间</a>
          </span>
          <span class="float-right">
            <a class="btn btn-primary" href="javascript:;" @click="saveSpecialItem('time')">保存</a>
          </span>
        </div>
      </div>
    </div>
    <div class="flash-message saveStatusTime">
    </div>

    <div class="form-group">
      <div class="form-row">
        <label for="inspection_status">专项核查情况</label>
        <textarea class="form-control" v-model="sp_item.inspection_record" id="sp_inspection_record" rows="3"></textarea>
        <!-- <a class="btn btn-primary" href="javascript:;" @click = "getChineseDate">撤销更改</a> -->
        <div class="container-fluid special-form">
          <span class="float-left">
            <a class="btn btn-primary" href="javascript:;" @click="setInspectionNormal">正常</a>
            <a class="btn btn-primary" href="javascript:;" @click="setInspectionNotFound" >查无</a>
            <a class="btn btn-primary" href="javascript:;" @click="setInspectionFake" >虚构</a>
          </span>
          <span class="float-right">
            <a class="btn btn-primary" href="javascript:;" @click="saveSpecialItem('inspect')">保存</a>
          </span>
        </div>

      </div>
    </div>
    <div class="flash-message saveStatusInspect">
    </div>

    <div class="form-group" style="margin-bottom: 0">
      <div class="form-row">
        <label for="inspection_status">专项电话记录</label>
        <textarea class="form-control" v-model="sp_item.phone_call_record" id="sp_phone_call_record" rows="2"></textarea>
        <div class="container-fluid special-form">
          <span class="float-left">

            <a class="btn btn-primary" href="javascript:;" @click="setPhoneNotAvailble">无法接通</a>
            <a class="btn btn-primary" href="javascript:;" @click="setPhoneNotExist">空号</a>
            <a class="btn btn-primary" href="javascript:;" @click="setPhoneDisable">停机</a>
            <a class="btn btn-primary" href="javascript:;" @click="setPhoneNoConnection">与之无关</a>
            <a class="btn btn-primary" href="javascript:;" @click="setPhoneShutdown">关机</a>
            <a class="btn btn-primary" href="javascript:;" @click="setPhoneClose">不做了</a>
          </span>
          <span class="float-right">
            <a class="btn btn-primary" href="javascript:;" @click="saveSpecialItem('phone')">保存</a>
          </span>
        </div>
          <!-- <a class="btn btn-primary" href="javascript:;">撤销更改</a> -->
        </div>
      </div>
      <div class="flash-message saveStatusPhone" style="margin-top:10px;padding-bottom:1px">
      </div>
    </form>
  </template>

  <script>
  export default {
// props: ['sp_item', 'corp'],
data: function() {
  return {
    sp_item: window.Backend.sp_item,
    corp: window.Backend.corp
  };
},
mounted() {
  console.log("Special action input forms Ok");
},
methods: {
  saveSpecialItem: function(info_type) {
    $(".flash-message").html("");
    var notice_target;
    switch (info_type) {
      case "time":
      notice_target = $(".saveStatusTime");
      break;
      case "inspect":
      notice_target = $(".saveStatusInspect");
      break;
      case "phone":
      notice_target = $(".saveStatusPhone");
      break;
      default:
      break;
    }
    notice_target.html('<p  class="alert alert-info">正在保存</p>');

    axios
    .put(
      "https://www.shilingaic.cn/index.php/api/special_action/" +
      this.sp_item.id,
      this.sp_item
      )
    .then(response => {
      notice_target.html(
        '<p  class="alert alert-success">' + response.data.msg + "</p>"
        );
    })
    .catch(error => {
      console.log(error);
    });
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
  setInspectionNormal: function() {
    this.sp_item.inspection_record =
    "当事人在" + this.corp.address + "正常经营";
  },
  setInspectionNotFound: function() {
    this.sp_item.inspection_record =
    "在" +
    this.corp.address +
    "未发现当事人的经营迹象。当事人通过登记地址无法联系。";
  },
  setInspectionFake: function() {
    this.sp_item.inspection_record =
    "在相关地址附近均无法找到当事人的登记地址" +
    this.corp.address +
    "。当事人通过登记地址无法联系。";
  },
  setInspectionTimeShortcut: function() {
    let datetime_array = this.getChineseDateArray();
    this.sp_item.start_inspect_time = datetime_array[0];
    this.sp_item.end_inspect_time = datetime_array[2];
  },
  setPhoneNotAvailble: function() {
    let datetime_array = this.getChineseDateArray();
    this.sp_item.phone_call_record =
    "执法人员于" +
    datetime_array[1] +
    "拨打当事人的登记电话，该电话无人接听";
  },
  setPhoneNotExist: function() {
    let datetime_array = this.getChineseDateArray();
    this.sp_item.phone_call_record =
    "执法人员于" + datetime_array[1] + "拨打当事人的登记电话，该电话为空号";
  },
  setPhoneDisable: function() {
    let datetime_array = this.getChineseDateArray();
    this.sp_item.phone_call_record =
    "执法人员于" +
    datetime_array[1] +
    "拨打当事人的登记电话，该电话已经停机";
  },
  setPhoneNoConnection: function() {
    let datetime_array = this.getChineseDateArray();
    this.sp_item.phone_call_record =
    "执法人员于" +
    datetime_array[1] +
    "拨打当事人的登记电话，该电话接听人员表示与当事人无关系，不清楚当事人的情况";
  },
  setPhoneShutdown: function() {
    let datetime_array = this.getChineseDateArray();
    this.sp_item.phone_call_record =
    "执法人员于" +
    datetime_array[1] +
    "拨打当事人的登记电话，该电话已经关机";
  },
  setPhoneClose: function() {
    let datetime_array = this.getChineseDateArray();
    this.sp_item.phone_call_record =
    "执法人员于" +
    datetime_array[1] +
    "拨打当事人的登记电话，接听人员表示该公司已经倒闭不再经营";
  }
}
};
</script>