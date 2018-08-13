<template>
  <div class="modal fade" :id="this.confirmDeletePhotoId" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" :id="this.confirmTitle" >确认</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          请确认是否要删除此照片
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
          <a href="javascript:;" class="btn btn-primary" @click="confirmedDelete">确认删除</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["photoId"],
  data: function() {
    return {
      confirmDeletePhotoId: "confirmDeletePhoto" + this.photoId,
      confirmTitle: "confirmTitle" + this.photoId
    };
  },
  mounted() {
    console.log(this.confirmDeletePhotoId + "|" + this.confirmTitle);
  },
  methods: {
    confirmedDelete: function() {
      axios
        .delete("https://www.shilingaic.cn/index.php/corp_photos/" + this.photoId
        )
        .then(
          response => {
            location.reload();
          },
          error => {
            // error callback
          }
        );
    }
  }
};
</script>