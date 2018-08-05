// import { map } from "async-es";

function WxChooseAndUploadImages() {
    var localIds = []
    var serverIds = []
    var sp_corp_id = window.Backend.sp_item.id

    wx.chooseImage({
        count: 9, // 默认9
        sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
        sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
        success: function (res) {
            localIds = res.localIds // 返回选定照片的本地Id列表，localId可以作为img标签的src属性显示图片

            async.map(localIds, (localId, callback) => {
                wx.uploadImage({
                    localId: localId,
                    isShowProgressTips: 1,
                    success: function (res) {
                        serverIds.push(res.serverId)
                        callback()
                    },
                    fail: function (res) {
                        callback(res)
                    }
                })
            }, err => {
                if (err) console.error(err.message);
                // 全部上传完毕，得到完整的serverId
                axios.post('/index.php/platform/special_action/corps/' + sp_corp_id + '/photos', {
                    server_ids: serverIds
                })
                .then(function (response) {
                    console.log(response['data'])
                    var src = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=" + response['data'][1]['access_token'] + "&media_id=" +response['data'][0]
                    var p = document.getElementById('upload')
                    p.setAttribute('src', src)
                })
            })
        }
    })
}