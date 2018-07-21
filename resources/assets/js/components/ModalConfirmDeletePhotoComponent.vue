<template>
  <div class="modal fade" id="confirmDeletePhoto" tabindex="-1" role="dialog" aria-labelledby="confirmDeletePhotoTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmDeletePhotoTitle">确认</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          请确认是否要删除此照片
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
          <button type="submit" class="btn btn-primary">确认删除</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
    }
</script>