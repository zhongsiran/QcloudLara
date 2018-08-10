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
                <!-- <a class="btn btn-primary" href="javascript:;">撤销更改</a> -->

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
                <!-- <a class="btn btn-primary" href="javascript:;">撤销更改</a> -->
            </div>
        </div>
        <div class="form-row">
            <div class="col">
                <a class="btn btn-block btn-primary" href="javascript:;" @click="uploadCoordination">上传定位</a>    
            </div>
            <div class="col">
                <a class="btn btn-block btn-primary" href="javascript:;" @click="uploadPhotosForSpecialAction">上传照片</a>    
            </div>
            <div class="col">
                <a class="btn btn-block btn-primary" href="javascript:;" @click="testCorp">进行导航</a>    
            </div>            
        </div>
        <div class="flash-message" id="upload_coordination_alert" style="margin-top:10px;margin-left: 5px;margin-right: 5px;">
        </div>
    </form>
</template>

<script>
export default {
  data: function() {
    return {
      corp: window.Backend.corp
    };
  },
  mounted() {
    console.log("Done Undone Btn Ok");
  },
  methods: {
    testCorp: function() {
      alert(JSON.stringify(this.corp));
    },
    uploadCoordination: function() {
      $("#upload_coordination_alert").html(
        '<p  class="alert alert-info">尝试取得定位</p>'
      );
      let corp = this.corp;
      wx.getLocation({
        type: "gcj02",
        success: function(res) {
          $("#upload_coordination_alert").html(
            '<p  class="alert alert-info">上传定位中</p>'
          );
          corp.latitude = res.latitude;
          corp.longitude = res.longitude;
          axios
            .post(
              "https://www.shilingaic.cn/index.php/api/corps/" +
                corp.registration_num,
              corp
            )
            .then(function(response) {
              $("#upload_coordination_alert").html(
                '<p  class="alert alert-success">成功上传定位</p>'
              );
            })
            .catch(function(response) {
              $("#upload_coordination_alert").html(
                '<p  class="alert alert-success">' + response.data.msg + "</p>"
              );
            });
        },
        cancel: function(res) {
          $("#upload_coordination_alert").html(
            '<p  class="alert alert-danger">用户拒绝获取定位权限</p>'
          );
        }
      });
    },
    uploadPhotosForSpecialAction: function() {
      var localIds = [];
      var serverIds = [];

      wx.chooseImage({
        count: 9, // 默认9
        sizeType: ["original", "compressed"], // 可以指定是原图还是压缩图，默认二者都有
        sourceType: ["album", "camera"], // 可以指定来源是相册还是相机，默认二者都有
        success: function(res) {
          localIds = res.localIds; // 返回选定照片的本地Id列表，localId可以作为img标签的src属性显示图片

          async.map(
            localIds,
            (localId, callback) => {
              wx.uploadImage({
                localId: localId,
                isShowProgressTips: 1,
                success: function(res) {
                  serverIds.push(res.serverId);
                  callback();
                },
                fail: function(res) {
                  callback(res);
                }
              });
            },
            err => {
              if (err) console.error(err.message);
              // 全部上传完毕，得到完整的serverId
              let datapack = new Object();
              datapack.serverIds = serverIds;
              datapack.corp = window.Backend.corp;
              datapack.uploader = user_openid;
              if (window.Backend.sp_item) {
                // 对应专项行动
                datapack.sp_item = window.Backend.sp_item;
              }

              axios
                .post(
                  "https://www.shilingaic.cn/index.php/api/corps_photos/",
                  datapack
                )
                .then(function(res) {
                  // $("#responseimg").attr("src", res.data);
                  $("#response").html(
                    '<p  class="alert alert-info">' + res.data + "</p>"
                  );
                });
              // 对应日常监管
            }
          );
        }
      });
    }
  }
};
</script>